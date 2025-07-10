<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
require_once '../../../Classes/UTILISATEURS.php';
$spreadSheetAry = 0;
if(isset($_SESSION['ECMU_USER_ID'])){
    $session_user = $_SESSION['ECMU_USER_ID'];
    if(!empty($session_user)){
        $UTILISATEURS = new UTILISATEURS();
        $utilisateur_existe = $UTILISATEURS->trouver($session_user,null,null);
        if(!empty($utilisateur_existe['ID_UTILISATEUR'])){
            $trouver_ets = $UTILISATEURS->trouver_ets_utilisateur($session_user);
            if($utilisateur_existe['ACTIF'] != 1){
                $json = array(
                    'status' => false,
                    'message' => 'VOTRE IDENTIFIANT A ETE DESACTIVE. VOUS NE POUVEZ PAS EFFECTUER CETTE ACTION. VEUILLEZ CONTACTER VOTRE ADMINISTRATEUR SYSTEME.'
                );
            }else{
                require_once '../../../Classes/ASSURES.php';
                require_once '../../../Classes/COLLECTIVITES.php';
                require_once '../../../Classes/FICHIERS.php';
                $ASSURES = new ASSURES();
                $COLLECTIVITES = new COLLECTIVITES();
                $FICHIERS = new FICHIERS();
                if($_POST['code_collectivite_input']){
                    $code_collectivite = $_POST['code_collectivite_input'];
                    
                    $trouver_ogd_collectivite = $COLLECTIVITES->trouver($code_collectivite);
                    if(isset($trouver_ogd_collectivite['CODE_OGD_COTISATIONS']) && !empty($trouver_ogd_collectivite['CODE_OGD_COTISATIONS'])) {

                        if(isset($_POST['submit_upload_population_collectivite_btn'])) {

                            $path_imports = DIR.'IMPORTS/';

                            if(!file_exists($path_imports)) {
                                mkdir($path_imports,0777, true);
                            }

                            $path_population_collectivite = $path_imports.'COLLECTIVITES/'.$code_collectivite.'/TEMP_FILES/';

                            if(!file_exists($path_population_collectivite)) {
                                mkdir($path_population_collectivite,0777, TRUE);
                            }

                            if(!file_exists($path_imports.'COLLECTIVITES/'.$code_collectivite.'/PROCESSING_FILES/')) {
                                mkdir($path_imports.'COLLECTIVITES/'.$code_collectivite.'/PROCESSING_FILES/',0777, TRUE);
                            }

                            if(!file_exists(DIR.'EXPORTS/')) {
                                mkdir(DIR.'EXPORTS/',0777, true);
                            }
                            if(!file_exists(DIR.'EXPORTS/POPULATIONS_COLLECTIVITES/RAPPORTS_ANALYSE/')) {
                                mkdir(DIR.'EXPORTS/POPULATIONS_COLLECTIVITES/RAPPORTS_ANALYSE/',0777, true);
                            }
                            $path_rapport = DIR.'EXPORTS/POPULATIONS_COLLECTIVITES/RAPPORTS_ANALYSE/'.$code_collectivite.'/TEMP_FILES/';
                            if(!file_exists($path_rapport)) {
                                mkdir($path_rapport,0777, true);
                            }

                            $filetemp = trim($_FILES["fichier_population_collectivite_input"]["tmp_name"]);
                            $filename = trim($_FILES["fichier_population_collectivite_input"]["name"]);
                            $filetype = trim($_FILES["fichier_population_collectivite_input"]["type"]);
                            $filesize = number_format(trim($_FILES["fichier_population_collectivite_input"]["size"]) / 1024, 0, ',', ' '). ' Ko';
                            $fileerror = $_FILES["fichier_population_collectivite_input"]["error"];
                            $extension = strrchr($filename, '.');
                            $filerpt  = str_replace($extension, '', $filename);
                            $mode = 'r';
                            $handle = fopen($filetemp, $mode);
                            if(!empty($filename)){
                                $trouver_fichier = $FICHIERS->trouver_fichier($filename,'OGDAFFPOP');
                                if(!isset($trouver_fichier['ID_FICHIER'])){
                                    require '../../../../vendor/autoload.php';
                                    $allowedFileType = [
                                        'application/vnd.ms-excel',
                                        'text/xls',
                                        'text/xlsx',
                                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                                    ];

                                    if (in_array($filetype, $allowedFileType)) {
                                        if($extension == '.xlsx' || $extension == '.XLSX'){
                                            $Reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
                                        }elseif($extension == '.xls' || $extension == '.XLS'){
                                            $Reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
                                        }
                                        $spreadSheet = $Reader->load($filetemp);
                                        $excelSheet = $spreadSheet->getActiveSheet();
                                        $spreadSheetAry = $excelSheet->toArray();
                                        $nbre_column = $excelSheet->getHighestDataColumn();
                                        $sheetCount = count($spreadSheetAry);
                                        $sc = $sheetCount-1;
                                        $erreur =0 ;
                                        $total_traite = $sc;
                                        $message_erreur ='' ;
                                        $all_num_secu = array();
                                        /***
                                        *
                                         * */
                                        //$fichier_rpt = 'rapport_traitement_fichier_'.$code_collectivite.'_'.date('dmYHis',time()).'.txt';
                                        $fichier_rpt = $filerpt.'.txt';
                                        $fichier_rapport = fopen($path_rapport . $fichier_rpt, "w") or die("Unable to open file!");
                                        $message_erreur = "DATE DEBUT DE CHARGEMENT: " . date('d/m/Y H:i:s', time()) . "\n";
                                        $message_erreur .= "FICHIER : " . $filename . "\n";
                                        $message_erreur .= "TOTAL A TRAITER : " . $total_traite . "\n\n";
                                        fwrite($fichier_rapport, $message_erreur);
                                        /***
                                         *
                                         */


                                        if(move_uploaded_file($filetemp,$path_population_collectivite.$filename)) {
                                            echo '<script>$("#submit_upload_population_collectivite_btn").hide();</script>';
                                            echo '<p class="alert alert-success" align="center" id="p_success_result"><b> LE FICHIER A BIEN ÉTÉ CHARGÉ.</b> <br> ';
                                            echo '<script>setTimeout(function () {  location.reload();},2000);</script>';
                                        }else {
                                            echo '<p align="center" class="text-danger">UNE ERREUR EST SURVENUE LORS DU CHARGEMENT DU FICHIER.</p>';
                                            /*$message_success = "UNE ERREUR EST SURVENUE LORS DU CHARGEMENT DU FICHIER.";
                                            fwrite($fichier_rapport, $message_success);*/
                                        }
                                       /* if($erreur==0){
                                            if(!file_exists($path_population_collectivite)) {
                                                mkdir($path_population_collectivite,0777, TRUE);
                                            }

                                        }else{

                                            //$message_erreur = $message_erreur."\nTOTAL LIGNES ANALYSEES: ".$total_traite;
                                            //fwrite($fichier_rapport, "\nTOTAL LIGNES ANALYSEES: ".$total_traite);
                                            //file_put_contents($path_rapport.$fichier_rpt, $message_erreur);
                                            //echo "TOTAL LIGNES ANALYSEES: ".$total_traite;

                                            //echo '<p class="alert alert-danger" align="center">L\'ANALYSE DU FICHIER EST TERMINE. CLIQUEZ SUR LE LIEN CI-DESSOUS POUR VOIR LES ERREURS TROUVEES. <br><a target="_blank" href="'.URL.'EXPORTS/POPULATIONS_COLLECTIVITES/RAPPORTS_ANALYSE/'.$code_collectivite.'/'.$fichier_rpt.'"> RAPPORT D\'ERREURS</a> <br> ';
                                        }*/
                                    }
                                }else{
                                    echo '<p class="alert alert-danger" align="center"><b> CE FICHIER A DEJA ETE TRAITE. PRIERE VERIFIER LES INFORMATIONS.</b></p>';
                                }
                            }
                            else{
                                echo '<p class="alert alert-danger" align="center"><b> LE FICHIER EST VIDE. PRIERE VERIFIER LES INFORMATIONS.</b></p>';
                            }

                        }
                        else{
                            echo '<p class="alert alert-danger" align="center"><b> AUCUN FICHIER CHARGE. PRIERE VERIFIER LES INFORMATIONS.</b></p>';
                        }
                    }
                    else{
                        echo '<p class="alert alert-danger" align="center"><b> LE CODE OGD N\'EST PAS DEFINE . VOUS NE POUVEZ PAS EFFECTUER CETTE ACTION. VEUILLEZ CONTACTER VOTRE ADMINISTRATEUR SYSTEME.</b></p>';
                    }
                }
                else{
                    echo '<p class="alert alert-danger" align="center"><b> LA COLLECTIVITE N\'EST PAS DEFINE . VOUS NE POUVEZ PAS EFFECTUER CETTE ACTION. VEUILLEZ CONTACTER VOTRE ADMINISTRATEUR SYSTEME.</b></p>';
                }
            }
        }else{
                echo '<p class="alert alert-danger" align="center"><b> VOTRE IDENTIFIANT EST INCORRECT. VOUS NE POUVEZ PAS EFFECTUER CETTE ACTION. VEUILLEZ CONTACTER VOTRE ADMINISTRATEUR SYSTEME.</b></p>';
        }
    }else{
        echo '<p class="alert alert-danger" align="center"><b> VOTRE SESSION EST INACTIVE. PRIERE VOUS RECONNECTER POUR POURSUIVRE CETTE ACTION.</b></p>';
    }
}else{
    echo '<p class="alert alert-danger" align="center"><b> VOTRE SESSION EST INACTIVE. PRIERE VOUS RECONNECTER POUR POURSUIVRE CETTE ACTION.</b></p>';
}
?>

