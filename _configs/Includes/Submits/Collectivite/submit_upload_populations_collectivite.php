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

                            $path_population_collectivite = $path_imports.'POPULATIONS_COLLECTIVITES/TEMP_FILES/';

                            if(!file_exists($path_population_collectivite)) {
                                mkdir($path_population_collectivite,0777, TRUE);
                            }

                            if(!file_exists(DIR.'EXPORTS/')) {
                                mkdir(DIR.'EXPORTS/',0777, true);
                            }
                            if(!file_exists(DIR.'EXPORTS/POPULATIONS_COLLECTIVITES/RAPPORTS_ANALYSE/')) {
                                mkdir(DIR.'EXPORTS/POPULATIONS_COLLECTIVITES/RAPPORTS_ANALYSE/',0777, true);
                            }
                            $path_rapport = DIR.'EXPORTS/POPULATIONS_COLLECTIVITES/RAPPORTS_ANALYSE/'.$code_collectivite.'/';
                            if(!file_exists($path_rapport)) {
                                mkdir($path_rapport,0777, true);
                            }

                            $filetemp = trim($_FILES["fichier_population_collectivite_input"]["tmp_name"]);
                            $filename = trim($_FILES["fichier_population_collectivite_input"]["name"]);
                            $filetype = trim($_FILES["fichier_population_collectivite_input"]["type"]);
                            $filesize = number_format(trim($_FILES["fichier_population_collectivite_input"]["size"]) / 1024, 0, ',', ' '). ' Ko';
                            $fileerror = $_FILES["fichier_population_collectivite_input"]["error"];
                            $extension = strrchr($filename, '.');
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
                                        $fichier_rpt = 'rapport_traitement_fichier_'.$code_collectivite.'_'.date('dmYHis',time()).'.txt';
                                        $fichier_rapport = fopen($path_rapport . $fichier_rpt, "w") or die("Unable to open file!");
                                        $message_erreur = "DATE DEBUT DE TRAITEMENT: " . date('d/m/Y H:i:s', time()) . "\n";
                                        $message_erreur .= "FICHIER : " . $filename . "\n\n";
                                        fwrite($fichier_rapport, $message_erreur);
                                        /***
                                         *
                                         */
                                        for($i = 0 ; $i<=$sc;$i++){
                                            $nbreColonne = count($spreadSheetAry[$i]);
                                            if($i==0){
                                                if(strtoupper(trim($nbre_column)) != 'Q' && strtoupper(trim($nbre_column)) != 'L'){
                                                    echo '<p class="alert alert-danger" align="center"><b> LE NOMBRE DE COLONNES DE CE FICHIER NE CORESPOND PAS AU FORMAT DEFINI. PRIERE VERIFIER LES INFORMATIONS RENSEIGNEES.</b></p>';
                                                    exit();
                                                }
                                            }else{
                                                $ligneDeclaration = $spreadSheetAry[$i];
                                                $ld = $ligneDeclaration;
                                                $l = $i+1;
                                                if(strtoupper(trim($nbre_column)) == 'Q') {
                                                    $type_beneficiaire = strtoupper(trim($ld[0]));
                                                    $numero_secu_payeur = strtoupper(str_replace(',', '', trim($ld[1])));
                                                    $numero_entreprise_payeur = strtoupper(str_replace(',', '', trim($ld[2])));
                                                    $numero_matricule_payeur = strtoupper(str_replace(',', '', trim($ld[3])));
                                                    $nom_payeur = trim($ld[4]);
                                                    $prenoms_payeur = trim($ld[5]);
                                                    $date_naissance_payeur = trim($ld[6]);
                                                    $numero_secu_benef = strtoupper(str_replace(',', '', trim($ld[7])));
                                                    $numero_entreprise_benef = strtoupper(str_replace(',', '', trim($ld[8])));
                                                    $numero_matricule_benef = strtoupper(str_replace(',', '', trim($ld[9])));
                                                    $nom_benef = trim($ld[10]);
                                                    $prenoms_benef = trim($ld[11]);
                                                    $date_naissance_benef = trim($ld[12]);
                                                    $civile = strtoupper(trim($ld[13]));
                                                    $sexe = strtoupper(trim($ld[14]));
                                                    $lieu_naissance = trim($ld[15]);
                                                    $lieu_residence = trim($ld[16]);
                                                }
                                                elseif(strtoupper(trim($nbre_column)) == 'L'){
                                                    /*
                                                    *** FORMAT ISSU DE ECNPS
                                                     *
                                                     *
                                                    $nom_payeur = trim($ld[0]);
                                                    $prenoms_payeur = trim($ld[1]);
                                                    $numero_entreprise_payeur = strtoupper(str_replace(',', '', trim($ld[2])));
                                                    $numero_matricule_payeur = strtoupper(str_replace(',', '', trim($ld[2])));
                                                    $numero_secu_payeur = strtoupper(str_replace(',', '', trim($ld[3])));
                                                    $date_naissance_payeur = trim($ld[4]);


                                                    $nom_benef = trim($ld[5]);
                                                    $prenoms_benef = trim($ld[6]);
                                                    $numero_entreprise_benef = strtoupper(str_replace(',', '', trim($ld[7])));
                                                    $numero_matricule_benef = strtoupper(str_replace(',', '', trim($ld[7])));

                                                    $type_beneficiaire = strtoupper(trim($ld[8]));
                                                    $date_naissance_benef = trim($ld[9]);
                                                    $numero_secu_benef = strtoupper(str_replace(',', '', trim($ld[10])));

                                                    $sexe = strtoupper(trim($ld[11]))=='H'?"M":"F";

                                                    $civile = $sexe=='M'?"M":"MME";
                                                    $lieu_naissance = null;
                                                    $lieu_residence = null;*/


                                                    $numero_entreprise_payeur = strtoupper(str_replace(',', '', trim($ld[0])));
                                                    $numero_matricule_payeur = strtoupper(str_replace(',', '', trim($ld[0])));
                                                    $numero_secu_payeur = strtoupper(str_replace(',', '', trim($ld[1])));
                                                    $nom_payeur = trim($ld[2]);
                                                    $prenoms_payeur = trim($ld[3]);
                                                    $date_naissance_payeur = strtoupper(date('Y-m-d', strtotime(str_replace('/', '-', trim($ld[4])))));


                                                    $numero_entreprise_benef = strtoupper(str_replace(',', '', trim($ld[5])));
                                                    $numero_matricule_benef = strtoupper(str_replace(',', '', trim($ld[5])));
                                                    $numero_secu_benef = strtoupper(str_replace(',', '', trim($ld[6])));
                                                    $type_beneficiaire = strtoupper(trim($ld[7]));
                                                    $nom_benef = trim($ld[8]);
                                                    $prenoms_benef = trim($ld[9]);
                                                    $date_naissance_benef = strtoupper(date('Y-m-d', strtotime(str_replace('/', '-', trim($ld[10])))));

                                                    if(strtoupper(trim($ld[11]))== "H" || strtoupper(trim($ld[11]))=="M"){$sexe = "M";}else{$sexe = "F";}
                                                    //$sexe = strtoupper(trim($ld[11]))=="H"||"M"?"M":"F";

                                                    $civile = $sexe=='M'?"M":"MME";
                                                    $lieu_naissance = null;
                                                    $lieu_residence = null;

                                                    if(empty($numero_matricule_payeur) && !empty($numero_secu_payeur)){
                                                        $numero_matricule_payeur = $numero_secu_payeur;
                                                    }
                                                }
                                                else{
                                                    exit();
                                                }
                                                if(empty($type_beneficiaire) && empty($numero_secu_payeur) && empty($numero_entreprise_payeur) && empty($numero_matricule_payeur) && empty($nom_payeur) && empty($prenoms_payeur) && empty($date_naissance_payeur)
                                                    && empty($numero_secu_benef) && empty($numero_entreprise_benef) && empty($numero_matricule_benef) && empty($nom_benef) && empty($prenoms_benef)
                                                    && empty($date_naissance_benef) && empty($civile) && empty($sexe) && empty($lieu_naissance) && empty($lieu_residence)
                                                ){
                                                    $total_traite--;
                                                }
                                                else{
                                                    $verification_ligne = $COLLECTIVITES->verification_informations_population($l, $code_collectivite, $type_beneficiaire, $numero_secu_payeur, $numero_entreprise_payeur, $numero_matricule_payeur, $nom_payeur, $prenoms_payeur, $date_naissance_payeur, $numero_secu_benef, $numero_entreprise_benef, $numero_matricule_benef, $nom_benef, $prenoms_benef, $date_naissance_benef, $civile, $sexe, $lieu_naissance, $lieu_naissance);
                                                    if($verification_ligne['status']==true){
                                                        if (!empty($numero_secu_benef)) {
                                                            $trouver_numero_secu_benef = $ASSURES->trouver($numero_secu_benef);
                                                            if (!isset($trouver_numero_secu_benef['NUM_SECU'])) {
                                                                $verification_ligne['message'] = $verification_ligne['message']."Ligne N°".$l.": LE NUMERO SECU DU BENEFICIAIRE EST ERRONE.\n";
                                                                $verification_ligne['erreur']=$verification_ligne['erreur']+1;
                                                            }else{
                                                                if(in_array($numero_secu_benef,$all_num_secu)){
                                                                    $verification_ligne['message'] = $verification_ligne['message']."Ligne N°".$l.": LE NUMERO SECU DU BENEFICIAIRE A DEJA ETE ATTRIBUE A UNE AUTRE PERSONNE DE VOTRE FICHIER.\n";
                                                                    //$verification_ligne['erreur']=$verification_ligne['erreur']+1;
                                                                }else{
                                                                    array_push($all_num_secu,$numero_secu_benef);
                                                                }
                                                            }
                                                        }

                                                        if (!empty($numero_secu_payeur)) {
                                                            $trouver_numero_secu_payeur = $ASSURES->trouver($numero_secu_payeur);
                                                            if (!isset($trouver_numero_secu_payeur['NUM_SECU'])) {
                                                                $verification_ligne['message'] = $verification_ligne['message']."Ligne N°".$l.": LE NUMERO SECU DU PAYEUR EST ERRONE.\n";
                                                                $verification_ligne['erreur'] = $verification_ligne['erreur']+1;
                                                            }
                                                        }

                                                        if($verification_ligne['erreur']!=0){
                                                            //$message_erreur = $message_erreur.''.$verification_ligne['message'];
                                                            fwrite($fichier_rapport, $verification_ligne['message']);
                                                            $erreur++;
                                                        }
                                                    }
                                                }
                                            }
                                        }


                                        if($erreur==0){
                                            if(!file_exists($path_population_collectivite)) {
                                                mkdir($path_population_collectivite,0777, TRUE);
                                            }
                                            if(move_uploaded_file($filetemp,$path_population_collectivite.$filename)) {
                                                echo '<script>$("#submit_upload_population_collectivite_btn").hide();</script>';
                                                echo '<p class="alert alert-success" align="center" id="p_success_result"><b> L\'ANALYSE DU FICHIER S\'EST TERMINEE AVEC SUCCES.</b> <br> ';
                                                echo '<p align="center"><button type="submit" id="btn_file_data_file" class="btn btn-sm btn-primary">SAUVEGARDER LES DONNEES</button> <button type="reset" id="btn_annuler_data_file" class="btn btn-sm btn-secondary">ANNULER</button></p>';
                                                $message_success = "L'ANALYSE DU FICHIER S'EST TERMINEE AVEC SUCCES.";
                                                fwrite($fichier_rapport, $message_success);
                                            }else {
                                                echo '<p align="center" class="text-danger">Une erreur est survenue lors du chargement du fichier</p>';
                                                $message_success = "UNE ERREUR EST SURVENUE LORS DU CHARGEMENT DU FICHIER.";
                                                fwrite($fichier_rapport, $message_success);
                                            }
                                        }else{

                                            //$message_erreur = $message_erreur."\nTOTAL LIGNES ANALYSEES: ".$total_traite;
                                            //fwrite($fichier_rapport, "\nTOTAL LIGNES ANALYSEES: ".$total_traite);
                                            //file_put_contents($path_rapport.$fichier_rpt, $message_erreur);
                                            //echo "TOTAL LIGNES ANALYSEES: ".$total_traite;

                                            //echo '<p class="alert alert-danger" align="center">L\'ANALYSE DU FICHIER EST TERMINE. CLIQUEZ SUR LE LIEN CI-DESSOUS POUR VOIR LES ERREURS TROUVEES. <br><a target="_blank" href="'.URL.'EXPORTS/POPULATIONS_COLLECTIVITES/RAPPORTS_ANALYSE/'.$code_collectivite.'/'.$fichier_rpt.'"> RAPPORT D\'ERREURS</a> <br> ';
                                        }
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

