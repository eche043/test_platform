<?php
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
$spreadSheetAry = 0;
header('Content-Type: application/json');
$nom_script = strtoupper(basename($_SERVER['PHP_SELF']));
$repertoire = str_replace('.PHP','',str_replace('.php','',$nom_script));

$root_path = "/var/www/html/";
$share_path = "/mnt/nfsshare/";
$url = "https://recette-ecmu.ipscnam.ci/";

$dossier =  $root_path.'IMPORTS/COLLECTIVITES/';
if (file_exists($dossier)) {
    $collectivites = array_diff(scandir($dossier), array(".", ".."));
    $nb_count_annees = count($collectivites);
    $mode = 'r';
    $ii = 0;
    $nombre_dossier = 0;
    $dossier_empty = 0;
    foreach ($collectivites as $values => $collectivite) {
        if(!file_exists($dossier . $collectivite.'/PROCESSING_FILES/')) {
            mkdir($dossier . $collectivite.'/PROCESSING_FILES/',0777, TRUE);
        }
        $fichiers_temp = $dossier . $collectivite.'/TEMP_FILES/';
        $fichiers = array_diff(scandir($fichiers_temp), array(".", ".."));
        if (!empty($fichiers)) {
            foreach ($fichiers as $values => $filename) {
                $filetemp = $fichiers_temp . $filename;
                $ligne = 0;
                $nb = 0;
                $extension = strtolower(strrchr($filename, '.'));
                $filerpt  = str_replace($extension, '', $filename);


                if (strtolower($extension) == '.xlsx' || $extension == '.XLSX' || $extension == '.xls' || $extension == '.XLS') {
                    require $root_path . 'vendor/autoload.php';
                    require $root_path . '_configs/Classes/BDD.php';
                    require_once $root_path . '_configs/Classes/COLLECTIVITES.php';
                    require_once $root_path . '_configs/Classes/ASSURES.php';

                    $COLLECTIVITES = new COLLECTIVITES();
                    $ASSURES = new ASSURES();

                    $spreadSheetAry = 0;
                    $allowedFileType = [
                        'application/vnd.ms-excel',
                        'text/xls',
                        'text/xlsx',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                    ];
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

                    $code_collectivite = $collectivite;

                    $path_population_collectivite = $root_path.'EXPORTS/POPULATIONS_COLLECTIVITES/RAPPORTS_ANALYSE/'.$code_collectivite;
                    $path_rapport_export = $path_population_collectivite.'/TEMP_FILES/';
                    if (file_exists($path_rapport_export)) {
                        $fichiers_export = array_diff(scandir($path_rapport_export), array(".", ".."));
                        if (!empty($fichiers_export)) {
                            if(count($fichiers_export)==1){
                                $fichier_rpt = $fichiers_export[2];
                                chmod($path_rapport_export . $fichier_rpt, 0777);
                                $file = escapeshellarg($path_rapport_export.$fichier_rpt);
                                $l_c = `tail -n 1 $file`;

                                if (substr($l_c, 0, 7) === "Ligne N") {
                                    $handle = 1;
                                    $ligne_loop = substr($l_c, 9,(strpos($l_c, ':')-10));
                                    $fichier_rapport = fopen($path_rapport_export . $fichier_rpt, "a") or die("Unable to open file!");
                                }elseif (substr($l_c, 0, 24) === "TOTAL LIGNES ANALYSEES: ") {
                                    $handle = 0;
                                    $ligne_loop = 0;
                                } else {
                                    $fichier_rapport = fopen($path_rapport_export . $fichier_rpt, "a") or die("Unable to open file!");
                                    $handle = 1;
                                    $ligne_loop = 0;
                                }
                            }
                            else{
                                $handle = 0;
                            }
                        }
                        else{
                            $handle = 0;
                        }
                    }
                    else{
                        $handle = 0;
                    }
                    if($handle === 1){
                        for($i = 0 ; $i<=$sc;$i++){
                            $nbreColonne = count($spreadSheetAry[$i]);
                            if($i==0){
                                if(strtoupper(trim($nbre_column)) != 'Q' && strtoupper(trim($nbre_column)) != 'L'){
                                    echo '<p class="alert alert-danger" align="center"><b> LE NOMBRE DE COLONNES DE CE FICHIER NE CORESPOND PAS AU FORMAT DEFINI. PRIERE VERIFIER LES INFORMATIONS RENSEIGNEES.</b></p>';
                                    exit();
                                }
                            }
                            else{
                                $ligneDeclaration = $spreadSheetAry[$i];
                                $ld = $ligneDeclaration;
                                $l = $i+1;
                                if ($i > $ligne_loop) {
                                    if (strtoupper(trim($nbre_column)) == 'Q') {
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
                                    elseif (strtoupper(trim($nbre_column)) == 'L') {
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

                                        if (strtoupper(trim($ld[11])) == "H" || strtoupper(trim($ld[11])) == "M") {
                                            $sexe = "M";
                                        } else {
                                            $sexe = "F";
                                        }
                                        //$sexe = strtoupper(trim($ld[11]))=="H"||"M"?"M":"F";

                                        $civile = $sexe == 'M' ? "M" : "MME";
                                        $lieu_naissance = null;
                                        $lieu_residence = null;

                                        if (empty($numero_matricule_payeur) && !empty($numero_secu_payeur)) {
                                            $numero_matricule_payeur = $numero_secu_payeur;
                                        }
                                    }
                                    else {
                                        exit();
                                    }
                                    if (empty($type_beneficiaire) && empty($numero_secu_payeur) && empty($numero_entreprise_payeur) && empty($numero_matricule_payeur) && empty($nom_payeur) && empty($prenoms_payeur) && empty($date_naissance_payeur)
                                        && empty($numero_secu_benef) && empty($numero_entreprise_benef) && empty($numero_matricule_benef) && empty($nom_benef) && empty($prenoms_benef)
                                        && empty($date_naissance_benef) && empty($civile) && empty($sexe) && empty($lieu_naissance) && empty($lieu_residence)
                                    ) {
                                        $total_traite--;
                                    } else {
                                        $verification_ligne = $COLLECTIVITES->verification_informations_population($l, $code_collectivite, $type_beneficiaire, $numero_secu_payeur, $numero_entreprise_payeur, $numero_matricule_payeur, $nom_payeur, $prenoms_payeur, $date_naissance_payeur, $numero_secu_benef, $numero_entreprise_benef, $numero_matricule_benef, $nom_benef, $prenoms_benef, $date_naissance_benef, $civile, $sexe, $lieu_naissance, $lieu_naissance);
                                        if ($verification_ligne['status'] == true) {
                                            if (!empty($numero_secu_benef)) {
                                                $trouver_numero_secu_benef = $ASSURES->trouver($numero_secu_benef);
                                                if (!isset($trouver_numero_secu_benef['NUM_SECU'])) {
                                                    $verification_ligne['message'] = $verification_ligne['message'] . "Ligne N°" . $l . ": LE NUMERO SECU DU BENEFICIAIRE EST ERRONE.\n";
                                                    $verification_ligne['erreur'] = $verification_ligne['erreur'] + 1;
                                                } else {
                                                    if (in_array($numero_secu_benef, $all_num_secu)) {
                                                        $verification_ligne['message'] = $verification_ligne['message'] . "Ligne N°" . $l . ": LE NUMERO SECU DU BENEFICIAIRE A DEJA ETE ATTRIBUE A UNE AUTRE PERSONNE DE VOTRE FICHIER.\n";
                                                        //$verification_ligne['erreur']=$verification_ligne['erreur']+1;
                                                    } else {
                                                        array_push($all_num_secu, $numero_secu_benef);
                                                    }
                                                }
                                            }

                                            if (!empty($numero_secu_payeur)) {
                                                $trouver_numero_secu_payeur = $ASSURES->trouver($numero_secu_payeur);
                                                if (!isset($trouver_numero_secu_payeur['NUM_SECU'])) {
                                                    $verification_ligne['message'] = $verification_ligne['message'] . "Ligne N°" . $l . ": LE NUMERO SECU DU PAYEUR EST ERRONE.\n";
                                                    $verification_ligne['erreur'] = $verification_ligne['erreur'] + 1;
                                                }
                                            }

                                            if ($verification_ligne['erreur'] != 0) {
                                                fwrite($fichier_rapport, $verification_ligne['message']);
                                                $erreur++;
                                            }
                                        }
                                    }
                                }
                            }
                        }

                        if($erreur==0){
                            $message_success = "L'ANALYSE DU FICHIER S'EST TERMINEE AVEC SUCCES.";
                            fwrite($fichier_rapport, $message_success);
                            if(!file_exists($path_population_collectivite)) {
                                mkdir($path_population_collectivite,0777, TRUE);
                            }
                            $new_fichier_rpt = $filerpt.date('dmYHis', time()).'.txt';
                            if(rename($path_rapport_export . $fichier_rpt,$path_population_collectivite.'/'.$new_fichier_rpt)) {
                                if(rename($filetemp,$dossier.$collectivite.'/PROCESSING_FILES/'.$filename)) {
                                    $fichier_rapport = fopen($root_path.'EXPORTS/POPULATIONS_COLLECTIVITES/'.$code_collectivite.'/'. $fichier_rpt, "w") or die("Unable to open file!");
                                    $message_erreur = "DATE DEBUT DE TRAITEMENT: " . date('d/m/Y H:i:s', time()) . "\n";
                                    $message_erreur .= "FICHIER : " . $filename . "\n";
                                    $message_erreur .= "TOTAL A TRAITER : " . $total_traite . "\n\n";
                                    fwrite($fichier_rapport, $message_erreur);
                                    echo "L'ANALYSE DU FICHIER S'EST TERMINEE AVEC SUCCES.";
                                }
                                else{
                                    echo '<p align="center" class="text-danger">Une erreur est survenue lors du chargement du fichier</p>';
                                    $message_success = "UNE ERREUR EST SURVENUE LORS DU CHARGEMENT DU FICHIER DANS LE DOSSIER PROCESSING.";
                                    fwrite($fichier_rapport, $message_success);
                                }
                            }
                            else {
                                echo '<p align="center" class="text-danger">Une erreur est survenue lors du chargement du fichier</p>';
                                $message_success = "UNE ERREUR EST SURVENUE LORS DU CHARGEMENT DU FICHIER.";
                                fwrite($fichier_rapport, $message_success);
                            }
                        }
                        else{
                            //rename($filetemp,$dossier . $collectivite.'/'.$filename);
                            fwrite($fichier_rapport, "\nTOTAL LIGNES ANALYSEES: ".$total_traite);
                            echo '<p class="alert alert-danger" align="center">L\'ANALYSE DU FICHIER '.$filename.' EST TERMINE. <br> ';
                        }
                    }
                    else{
                        echo false;
                    }
                }
                else {
                    echo false;
                }
            }
        }
        else {
            echo false;
        }
    }
}
else{
    echo false;
}

?>