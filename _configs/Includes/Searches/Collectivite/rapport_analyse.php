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
                echo '<p class="alert alert-danger" align="center">VOTRE IDENTIFIANT A ETE DESACTIVE. VOUS NE POUVEZ PAS EFFECTUER CETTE ACTION. VEUILLEZ CONTACTER VOTRE ADMINISTRATEUR SYSTEME.</p>';
            }else{
                if(empty($_POST["code_collectivite"])){
                    echo '<p class="alert alert-danger" align="center">VOTRE COLLECTIVITE N\'A PAS ETE DEFINIE. VOUS NE POUVEZ PAS EFFECTUER CETTE ACTION. VEUILLEZ CONTACTER VOTRE ADMINISTRATEUR SYSTEME.</p>';
                }else{
                    require_once '../../../Classes/ASSURES.php';
                    require_once '../../../Classes/COLLECTIVITES.php';
                    $ASSURES = new ASSURES();
                    $COLLECTIVITES = new COLLECTIVITES();

                    $code_collectivite = $_POST["code_collectivite"];
                    $trouver_ogd_collectivite = $COLLECTIVITES->trouver($code_collectivite);
                    $code_collectivite = $_POST["code_collectivite"];
                    $dossier = DIR.'EXPORTS/POPULATIONS_COLLECTIVITES/RAPPORTS_ANALYSE/'.$code_collectivite.'/TEMP_FILES/';
                    $lien = URL.'EXPORTS/POPULATIONS_COLLECTIVITES/RAPPORTS_ANALYSE/'.$code_collectivite.'/TEMP_FILES/';
                    if (file_exists($dossier)) {
                        $fichiers = array_diff(scandir($dossier), array(".", ".."));
                        if (!empty($fichiers)) {
                            $filename = $fichiers[2];
                            echo $lien.$filename;
                        }
                        else{

                        }
                    }
                    else{

                    }
                }
            }
        }else{
            echo '<p class="alert alert-danger" align="center">VOTRE IDENTIFIANT EST INCORRECT. VOUS NE POUVEZ PAS EFFECTUER CETTE ACTION. VEUILLEZ CONTACTER VOTRE ADMINISTRATEUR SYSTEME.</p>';
        }
    }else{
        echo '<p class="alert alert-danger" align="center">VOTRE SESSION EST INACTIVE. PRIERE VOUS RECONNECTER POUR POURSUIVRE CETTE ACTION.</p>';
    }
}else{
    echo '<p class="alert alert-danger" align="center">VOTRE SESSION EST INACTIVE. PRIERE VOUS RECONNECTER POUR POURSUIVRE CETTE ACTION.</p>';
}

?>
