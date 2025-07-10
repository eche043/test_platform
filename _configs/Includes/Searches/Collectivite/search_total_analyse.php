<?php

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
                if(empty($_POST["code_collectivite"])){
                    $json = array(
                        'status' => false,
                        'message' => 'LE CODE COLLECTIVITE EST ERRONNE. VOUS NE POUVEZ PAS EFFECTUER CETTE ACTION. VEUILLEZ CONTACTER VOTRE ADMINISTRATEUR SYSTEME.'
                    );
                }else{
                    require_once '../../../Classes/ASSURES.php';
                    require_once '../../../Classes/COLLECTIVITES.php';
                    $ASSURES = new ASSURES();
                    $COLLECTIVITES = new COLLECTIVITES();

                    $code_collectivite = $_POST["code_collectivite"];
                    $dossier = DIR.'EXPORTS/POPULATIONS_COLLECTIVITES/RAPPORTS_ANALYSE/'.$code_collectivite.'/TEMP_FILES/';
                    if (file_exists($dossier)) {
                        $fichiers = array_diff(scandir($dossier), array(".", ".."));
                        if (!empty($fichiers)) {
                            $filename = $fichiers[2];

                            $file = escapeshellarg($dossier.$filename);
                            $l_c = `tail -n 1 $file`;

                            if (substr($l_c, 0, 7) === "Ligne N") {
                                $total_analyse = (int)substr($l_c, 9,(strpos($l_c, ':')-10));
                            }elseif (substr($l_c, 0, 24) === "TOTAL LIGNES ANALYSEES: ") {
                                $total_analyse = (int)substr($l_c, 24);
                            } else {
                                $total_analyse = 0;
                            }
                            $json = array(
                                'status' => true,
                                'message'=>'total trouve',
                                'total_analyse' => $total_analyse
                            );
                        }
                        else{
                            $processing_folder = DIR.'IMPORTS/COLLECTIVITES/'.$code_collectivite.'/PROCESSING_FILES/';
                            $path_process_export = DIR.'EXPORTS/POPULATIONS_COLLECTIVITES/'.$code_collectivite.'/';
                            $total_fichiers = 0;
                            if (file_exists($processing_folder)) {
                                $fichiers_processing = array_diff(scandir($processing_folder), array(".", ".."));
                                if (!empty($fichiers_processing)) {
                                    foreach ($fichiers_processing as $values => $filenamep) {
                                        $f = $filenamep;
                                        $total_fichiers++;
                                    }
                                    if ($total_fichiers === 1) {
                                        $extension = strrchr($f, '.');
                                        $filerpt = str_replace($extension, '', $f);
                                        $lines = file($path_process_export . $filerpt . '.txt');

                                        $file = escapeshellarg($path_process_export . $filerpt . '.txt');
                                        $l_c = `tail -n 1 $file`;

                                        if (substr($lines[2], 0, 18) === "TOTAL A TRAITER : ") {
                                            $total_a_analyser = (int)substr($lines[2], 18);
                                        } else {
                                            $total_a_analyser = 0;
                                        }

                                        if (substr($l_c, 0, 10) === "Ligne N") {
                                            $total_analyse = (int)substr($l_c, 10);
                                        } elseif (substr($l_c, 0, 23) === "TOTAL LIGNES TRAITEES: ") {
                                            $total_analyse = (int)substr($l_c, 23);
                                        } else {
                                            $total_analyse = 0;
                                        }
                                        $json = array(
                                            'status' => true,
                                            'message'=>'TOTAL TROUVE '.$l_c,
                                            'total_analyse' => $total_analyse
                                        );
                                    } else {
                                        $json = array(
                                            'status' => true,
                                            'message'=>'TOTAL FICHIER NON DEFINI',
                                            'total_analyse' => 0
                                        );
                                    }
                                } else {
                                    $json = array('status' => true,'message'=>'FICHIER NON TROUVE');
                                }
                            }
                            else{
                                $json = array('status' => false,'message'=>'FICHIER NON TROUVE');
                            }
                        }
                    }
                    else{
                        $json = array('status' => false,'message'=>'REPERTOIRE NON DEFINI');
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

