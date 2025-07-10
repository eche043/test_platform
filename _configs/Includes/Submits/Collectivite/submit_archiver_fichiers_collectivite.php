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
                if(!$_POST['code_collectivite']){
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
                    $code_collectivite = $_POST['code_collectivite'];
                    $trouver_ogd_collectivite = $COLLECTIVITES->trouver($code_collectivite);
                    if(isset($trouver_ogd_collectivite['CODE_OGD_COTISATIONS']) && !empty($trouver_ogd_collectivite['CODE_OGD_COTISATIONS'])) {
                        $dossier = DIR. 'IMPORTS/COLLECTIVITES/';
                        if (file_exists($dossier)) {
                            $fichiers = array_diff(scandir($dossier.$code_collectivite.'/TEMP_FILES/'), array(".", ".."));
                            if (!empty($fichiers)) {
                                foreach ($fichiers as $values => $filename) {
                                    rename($dossier.$code_collectivite.'/TEMP_FILES/'.$filename,$dossier . $code_collectivite.'/'.$filename);

                                    $dossier_rapport = DIR.'EXPORTS/POPULATIONS_COLLECTIVITES/RAPPORTS_ANALYSE/'.$code_collectivite;
                                    ///TEMP_FILES/'
                                    if (file_exists($dossier_rapport)) {
                                        $fichiers_rap = array_diff(scandir($dossier_rapport.'/TEMP_FILES/'), array(".", ".."));
                                        if (!empty($fichiers_rap)) {
                                            foreach ($fichiers_rap as $values => $filename_rpt) {
                                                $extension = strrchr($filename_rpt, '.');
                                                $filerpt  = str_replace($extension, '', $filename_rpt);
                                                $new_fichier_rpt = $filerpt.date('dmYHis', time()).'.txt';
                                                rename($dossier_rapport.'/TEMP_FILES/'.$filename_rpt,$dossier_rapport.'/'.$new_fichier_rpt);
                                                $json = array(
                                                    'status' => true,
                                                    'message' => 'TRAITEMENT EFFECTUE AVEC SUCCES'
                                                );
                                            }
                                        }
                                    }else{
                                        $json = array(
                                            'status' => false,
                                            'message' => 'ERREUR LORS DU CHARGEMENT DU FICHIER. PRIERE VERIFIER LE FICHIER.'
                                        );
                                    }
                                }
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
