<?php
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
$spreadSheetAry = 0;
require_once '../../../Classes/UTILISATEURS.php';
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
                if(!$_POST['code_collectivite_input']){
                    $json = array(
                        'status' => false,
                        'message' => 'VOTRE COLLECTIVITE N\'A PAS ETE DEFINIE. VOUS NE POUVEZ PAS EFFECTUER CETTE ACTION. VEUILLEZ CONTACTER VOTRE ADMINISTRATEUR SYSTEME.'
                    );
                }else{
                    require_once '../../../Classes/ASSURES.php';
                    require_once '../../../Classes/COLLECTIVITES.php';
                    require_once '../../../Classes/FICHIERS.php';
                    $ASSURES = new ASSURES();
                    $COLLECTIVITES = new COLLECTIVITES();
                    $FICHIERS = new FICHIERS();
                    $code_collectivite = $_POST['code_collectivite_input'];
                    $trouver_ogd_collectivite = $COLLECTIVITES->trouver($code_collectivite);
                    if(isset($trouver_ogd_collectivite['CODE_OGD_COTISATIONS']) && !empty($trouver_ogd_collectivite['CODE_OGD_COTISATIONS'])) {
                        $fichier = DIR. 'IMPORTS/POPULATIONS_COLLECTIVITES/TEMP_FILES/'. $_POST["fichier"];
                        if (file_exists($fichier)) {
                            $extension = strrchr($fichier,'.');
                            $mode = 'r';
                            $handle = fopen($fichier, $mode);
                            if (!empty($fichier)) {
                                require '../../../../vendor/autoload.php';
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
                                $spreadSheet = $Reader->load($fichier);
                                $excelSheet = $spreadSheet->getActiveSheet();
                                $spreadSheetAry = $excelSheet->toArray();
                                $nbre_column = $excelSheet->getHighestDataColumn();
                                $sheetCount = count($spreadSheetAry);

                                $sc = $sheetCount - 1;
                                $erreur = 0;
                                $succes = 0;
                                $up = 0;
                                $ins = 0;
                                $total_traite = $sc;
                                $trouver_last_fichier = $FICHIERS->trouver_last_fichier();
                                $new_fichier_id = $trouver_last_fichier['LAST_ID']+1;

                                for ($i = 0; $i <= $sc; $i++) {
                                    if ($i >= 1) {
                                        $ligneDeclaration = $spreadSheetAry[$i];
                                        $ld = $ligneDeclaration;
                                        $l = $i + 1;
                                        if(strtoupper(trim($nbre_column)) == 'Q') {
                                            $type_beneficiaire = strtoupper(trim($ld[0]));
                                            $numero_secu_payeur = strtoupper(str_replace(',','',trim($ld[1])));
                                            $numero_entreprise_payeur = strtoupper(str_replace(',','',trim($ld[2])));
                                            $numero_matricule_payeur = strtoupper(str_replace(',','',trim($ld[3])));
                                            $nom_payeur = strtoupper(trim($ld[4]));
                                            $prenoms_payeur = strtoupper(trim($ld[5]));
                                            $date_naiss_payeur = trim($ld[6]);
                                            $numero_secu_benef = strtoupper(str_replace(',','',trim($ld[7])));
                                            $numero_entreprise_benef = strtoupper(str_replace(',','',trim($ld[8])));
                                            $numero_matricule_benef = strtoupper(str_replace(',','',trim($ld[9])));
                                            $nom_benef = strtoupper(trim($ld[10]));
                                            $prenoms_benef = strtoupper(trim($ld[11]));
                                            $date_naiss_benef = trim($ld[12]);
                                            $civile = strtoupper(trim($ld[13]));
                                            $sexe = strtoupper(trim($ld[14]));
                                            $lieu_naissance = strtoupper(trim($ld[15]));
                                            $lieu_residence = strtoupper(trim($ld[16]));
                                        }
                                        elseif(strtoupper(trim($nbre_column)) == 'L'){
                                            $numero_entreprise_payeur = strtoupper(str_replace(',', '', trim($ld[0])));
                                            $numero_matricule_payeur = strtoupper(str_replace(',', '', trim($ld[0])));
                                            $numero_secu_payeur = strtoupper(str_replace(',', '', trim($ld[1])));
                                            $nom_payeur = trim($ld[2]);
                                            $prenoms_payeur = trim($ld[3]);
                                            $date_naiss_payeur = trim($ld[4]);


                                            $numero_entreprise_benef = strtoupper(str_replace(',', '', trim($ld[5])));
                                            $numero_matricule_benef = strtoupper(str_replace(',', '', trim($ld[5])));
                                            $numero_secu_benef = strtoupper(str_replace(',', '', trim($ld[6])));
                                            $type_beneficiaire = strtoupper(trim($ld[7]));
                                            $nom_benef = trim($ld[8]);
                                            $prenoms_benef = trim($ld[9]);
                                            $date_naiss_benef = trim($ld[10]);

                                            $sexe = strtoupper(trim($ld[11]))=='H'?"M":"F";

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

                                        if(empty($type_beneficiaire) && empty($numero_secu_payeur) && empty($numero_entreprise_payeur) && empty($numero_matricule_payeur) && empty($nom_payeur) && empty($prenoms_payeur) && empty($date_naiss_payeur)
                                            && empty($numero_secu_benef) && empty($numero_entreprise_benef) && empty($numero_matricule_benef) && empty($nom_benef) && empty($prenoms_benef)
                                            && empty($date_naiss_benef) && empty($civile) && empty($sexe) && empty($lieu_naissance) && empty($lieu_residence)
                                        ){
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
                                                //$date_naissance_payeur = strtoupper(date('Y-m-d',strtotime(str_replace('/','-',trim($dn_pay[1].'/'.$dn_pay[0].'/'.$dn_pay[2])))));
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
                                                //$date_naissance_benef = strtoupper(date('Y-m-d',strtotime(str_replace('/','-',trim($dn_benef[1].'/'.$dn_benef[0].'/'.$dn_benef[2])))));
                                            }

                                            if (!empty($numero_secu_benef)) {
                                                $trouver_numero_secu_benef = $ASSURES->trouver($numero_secu_benef);
                                                if (!isset($trouver_numero_secu_benef['NUM_SECU'])) {
                                                    $json = array(
                                                        'status' => false,
                                                        'message' => 'LE NUMERO SECU DU BENEFICIAIRE EST ERRONE'
                                                    );
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
                                                    $erreur++;
                                                }
                                            }

                                            if ($erreur == 0) {
                                                $ajout_population = $COLLECTIVITES->ajouter_nouvelle_population(null, $trouver_ogd_collectivite['CODE_OGD_COTISATIONS'], $code_collectivite, $type_beneficiaire, $numero_secu_payeur, $numero_entreprise_payeur, $numero_matricule_payeur, $nom_payeur, $prenoms_payeur, $date_naissance_payeur, $numero_secu_benef, $numero_entreprise_benef, $numero_matricule_benef, $nom_benef, $prenoms_benef, $date_naissance_benef, $civile, $sexe, $lieu_naissance, $lieu_residence, NULL, NULL, NULL,1,$utilisateur_existe['ID_UTILISATEUR']);
                                                if($ajout_population['status']==true){
                                                    $ajout_mvt = $COLLECTIVITES->ajouter_mouvement_affiliation_population($ajout_population['message']['last_id'], $new_fichier_id, $trouver_ogd_collectivite['CODE_OGD_COTISATIONS'], $code_collectivite,1,$utilisateur_existe['ID_UTILISATEUR']);
                                                    $succes++;
                                                    $up = $up + $ajout_population['message']['maj'];
                                                    $ins =$ins + $ajout_population['message']['insert'];
                                                }else{
                                                    $erreur++;
                                                }
                                            }
                                        }
                                    }
                                }
                                if($succes!=0 || $erreur!=0){
                                    $historique = $FICHIERS->inserer_historique_fichier(null, 'IMP', 'OGDAFFPOP', 1, date('d-M-y',time()),$trouver_ogd_collectivite['CODE_OGD_COTISATIONS'],1, $succes, $_POST["fichier"], $utilisateur_existe['ID_UTILISATEUR']);
                                    if($historique['status']==true){
                                        $json = array(
                                            'status' => true,
                                            'message' => 'TRAITEMENT DU FICHIER TERMINE.<br> TOTAL: <b>'.$total_traite.'</b><br> SUCCES: <b>'.$succes.'</b> ==> <b>'.$ins.'</b> AJOUT(S) ET <b>'.$up.'</b> MISE(S) A JOUR<br> ECHECS: <b>'.$erreur.'</b>'
                                        );
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
                            } else {
                                $json = array(
                                    'status' => false,
                                    'message' => 'LE FICHIER EST VIDE. PRIERE VERIFIER LES INFORMATIONS'
                                );
                            }
                        }else{
                            $json = array(
                                'status' => false,
                                'message' => 'ERREUR LORS DU CHARGEMENT DU FICHIER. PRIERE VERIFIER LE FICHIER.'
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
        }else{
            $json = array(
                'status' => false,
                'message' => 'VOTRE IDENTIFIANT EST INCORRECT. VOUS NE POUVEZ PAS EFFECTUER CETTE ACTION. VEUILLEZ CONTACTER VOTRE ADMINISTRATEUR SYSTEME.'
            );
        }
    }else{
        $json = array(
            'status' => false,
            'message' => 'VOTRE SESSION EST INACTIVE. PRIERE VOUS RECONNECTER POUR POURSUIVRE CETTE ACTION.'
        );
    }
}else{
    $json = array(
        'status' => false,
        'message' => 'VOTRE SESSION EST INACTIVE. PRIERE VOUS RECONNECTER POUR POURSUIVRE CETTE ACTION.'
    );
}
echo json_encode($json);
?>
