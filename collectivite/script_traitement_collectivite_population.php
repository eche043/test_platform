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

require $root_path . 'vendor/autoload.php';
require $root_path . '_configs/Classes/BDD.php';
require_once $root_path . '_configs/Classes/COLLECTIVITES.php';
require_once $root_path . '_configs/Classes/ASSURES.php';
require_once $root_path . '_configs/Classes/FICHIERS.php';

$COLLECTIVITES = new COLLECTIVITES();
$ASSURES = new ASSURES();
$FICHIERS = new FICHIERS();

$dossier =  $root_path.'IMPORTS/COLLECTIVITES/';
if (file_exists($dossier)) {
    $collectivites = array_diff(scandir($dossier), array(".", ".."));
    $nb_count_annees = count($collectivites);
    $mode = 'r';
    $ii = 0;
    $nombre_dossier = 0;
    $dossier_empty = 0;
    foreach ($collectivites as $values => $collectivite) {
        $trouver_ogd_collectivite = $COLLECTIVITES->trouver($collectivite);
        if(isset($trouver_ogd_collectivite['CODE_OGD_COTISATIONS']) && !empty($trouver_ogd_collectivite['CODE_OGD_COTISATIONS'])) {
            $fichiers_temp = $dossier . $collectivite.'/PROCESSING_FILES/';
            $fichiers = array_diff(scandir($fichiers_temp), array(".", ".."));
            if (!empty($fichiers)) {
                foreach ($fichiers as $values => $filename) {
                    $filetemp = $fichiers_temp . $filename;
                    $ligne = 0;
                    $nb = 0;
                    $extension = strtolower(strrchr($filename, '.'));
                    $filerpt  = str_replace($extension, '', $filename);


                    if (strtolower($extension) == '.xlsx' || $extension == '.XLSX' || $extension == '.xls' || $extension == '.XLS') {

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
                        $succes = 0;
                        $total_traite = $sc;
                        $message_erreur ='' ;
                        $all_num_secu = array();

                        $code_collectivite = $collectivite;

                        $path_population_collectivite = $root_path.'EXPORTS/POPULATIONS_COLLECTIVITES/'.$code_collectivite;
                        $path_rapport_export = $path_population_collectivite.'/'.$filerpt.'.txt';
                        if (file_exists($path_rapport_export)) {
                            chmod($path_rapport_export, 0777);
                            $file = escapeshellarg($path_rapport_export);
                            $l_c = `tail -n 1 $file`;
                            if (substr($l_c, 0, 7) === "Ligne N") {
                                $handle = 1;
                                $ligne_loop = substr($l_c, 9,(strpos($l_c, ':')-10));
                            }elseif (substr($l_c, 0, 23) === "TOTAL LIGNES TRAITEES: ") {
                                $handle = 0;
                                $ligne_loop = 0;
                            } else {
                                $fichier_rapport = fopen($path_rapport_export, "a") or die("Unable to open file!");
                                $handle = 1;
                                $ligne_loop = 0;
                            }
                            /*$fichiers_export = array_diff(scandir($path_rapport_export), array(".", ".."));
                            if (!empty($fichiers_export)) {
                                if(count($fichiers_export)==1){
                                    $fichier_rpt = $fichiers_export[2];

                                }
                                else{
                                    $handle = 0;
                                }
                            }
                            else{
                                $handle = 0;
                            }*/
                        }
                        else{
                            echo 'test23';
                            $handle = 0;
                        }

                        if($handle === 1){
                            for($i = $ligne_loop ; $i<=$sc;$i++){
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
                                        }
                                        else{
                                            if(empty($date_naiss_payeur)){
                                                $date_naissance_payeur = null;
                                            }else{
                                                $dn_pay = explode('/',$date_naiss_payeur);
                                                if($dn_pay[0]<='9'){
                                                    $jn_pay = str_pad($dn_pay[0],2,'0',STR_PAD_LEFT);
                                                }else{
                                                    $jn_pay = $dn_pay[0];
                                                }
                                                if($dn_pay[1]<='9'){
                                                    $mn_pay = str_pad($dn_pay[1],2,'0',STR_PAD_LEFT);
                                                }else{
                                                    $mn_pay = $dn_pay[1];
                                                }
                                                $date_naissance_payeur = strtoupper(date('Y-m-d',strtotime(str_replace('/','-',trim($mn_pay.'/'.$jn_pay.'/'.$dn_pay[2])))));
                                            }

                                            if(empty($date_naiss_benef)){
                                                $date_naissance_benef = null;
                                            }else{
                                                $dn_benef = explode('/',$date_naiss_benef);
                                                if($dn_benef[0]<='9'){
                                                    $jn_ben = str_pad($dn_benef[0],2,'0',STR_PAD_LEFT);
                                                }else{
                                                    $jn_ben = $dn_benef[0];
                                                }
                                                if($dn_benef[1]<='9'){
                                                    $mn_ben = str_pad($dn_benef[1],2,'0',STR_PAD_LEFT);
                                                }else{
                                                    $mn_ben = $dn_benef[1];
                                                }
                                                $date_naissance_benef = strtoupper(date('Y-m-d',strtotime(str_replace('/','-',trim($mn_ben.'/'.$jn_ben.'/'.$dn_benef[2])))));
                                            }

                                            if (!empty($numero_secu_benef)) {
                                                $trouver_numero_secu_benef = $ASSURES->trouver($numero_secu_benef);
                                                if (!isset($trouver_numero_secu_benef['NUM_SECU'])) {
                                                    $json[] = array(
                                                        'status' => false,
                                                        'message' => 'LE NUMERO SECU DU BENEFICIAIRE EST ERRONE'
                                                    );
                                                    fwrite($fichier_rapport, "Ligne N $i: LE NUMERO SECU DU BENEFICIAIRE EST ERRONE \n");
                                                    $erreur++;
                                                }
                                            }
                                            if (!empty($numero_secu_payeur)) {
                                                $trouver_numero_secu_payeur = $ASSURES->trouver($numero_secu_payeur);
                                                if (!isset($trouver_numero_secu_payeur['NUM_SECU'])) {
                                                    $json = array(
                                                        'status' => false,
                                                        'message' => 'LE NUMERO SECU DU PAYEUR EST ERRONE'
                                                    );
                                                    fwrite($fichier_rapport, "Ligne N $i: LE NUMERO SECU DU PAYEUR EST ERRONE \n");
                                                    $erreur++;
                                                }
                                            }

                                            if ($erreur == 0) {
                                                $ajout_population = $COLLECTIVITES->ajouter_nouvelle_population(null, null, $code_collectivite, $type_beneficiaire, $numero_secu_payeur, $numero_entreprise_payeur, $numero_matricule_payeur, $nom_payeur, $prenoms_payeur, $date_naissance_payeur, $numero_secu_benef, $numero_entreprise_benef, $numero_matricule_benef, $nom_benef, $prenoms_benef, $date_naissance_benef, $civile, $sexe, $lieu_naissance, $lieu_residence, NULL, NULL);
                                                if($ajout_population['status']==true){
                                                    $ajout_mvt = $COLLECTIVITES->ajouter_mouvement_affiliation_population($ajout_population['message']['last_id'], null, null, $code_collectivite,1,null);
                                                    $succes++;
                                                    fwrite($fichier_rapport, "Ligne N $i: SUCCES ENREGISTREMENT \n");
                                                }else{
                                                    $erreur++;
                                                    fwrite($fichier_rapport, "Ligne N $i: ECHEC ENREGISTREMENT \n");
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            if($succes!=0 || $erreur!=0){
                                $total_traite = $succes+$erreur;
                                $historique = $FICHIERS->inserer_historique_fichier(null, 'IMP', 'OGDAFFPOP', 1, date('Y-m-d',time()), $trouver_ogd_collectivite['CODE_OGD_COTISATIONS'],1, $succes, $filename, null);
                                if($historique['status']==true){
                                    $new_fichier_rpt = $filerpt.date('dmYHis', time()).'.txt';
                                    rename($path_rapport_export,$path_population_collectivite.'/'.$new_fichier_rpt);
                                    rename($filetemp,$dossier.$collectivite.'/'.$filename);
                                    fwrite($fichier_rapport, "\nTOTAL LIGNES TRAITEES: ".$total_traite);
                                }else{
                                    $json = array(
                                        'status' => false,
                                        'message' => $historique['message']
                                    );
                                }
                            }else{
                                $json = array(
                                    'status' => false,
                                    'message' => 'ERREUR LORS DU TRAITEMENT DU FICHIER. PRIERE VERIFIER LE FICHIER CHARGE.'
                                );
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
                $json = array(
                    'status' => false,
                    'message' => ''
                );
            }
        }else{
            $json = array(
                'status' => false,
                'message' => 'L\'OGD DE VOTRE COLLECTIVITE N\'A PAS ETE DEFINI. VEUILLEZ CONTACTER VOTRE ADMINISTRATEUR SYSTEME.'
            );
        }
    }
}
else{
    $json = array(
        'status' => false,
        'message' => ''
    );
}
echo json_encode($json);
?>