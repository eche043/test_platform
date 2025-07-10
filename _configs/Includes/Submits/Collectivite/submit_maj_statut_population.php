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
                if(!($_POST['code_collectivite'])){
                    $json = array(
                        'status' => false,
                        'message' => 'VOTRE COLLECTIVITE N\'A PAS ETE DEFINIE. VOUS NE POUVEZ PAS EFFECTUER CETTE ACTION. VEUILLEZ CONTACTER VOTRE ADMINISTRATEUR SYSTEME.'
                    );
                }else{
                    require_once '../../../Classes/ASSURES.php';
                    require_once '../../../Classes/COLLECTIVITES.php';
                    $ASSURES = new ASSURES();
                    $COLLECTIVITES = new COLLECTIVITES();
                    $code_collectivite = $_POST['code_collectivite'];
                    $trouver_ogd_collectivite = $COLLECTIVITES->trouver($code_collectivite);
                    if(isset($trouver_ogd_collectivite['CODE_OGD_COTISATIONS']) && !empty($trouver_ogd_collectivite['CODE_OGD_COTISATIONS'])) {

                        $populations = $_POST['populations'];
                        $statut = $_POST['statut'];
                        $id_populations = explode(',',str_replace('[','',str_replace(']','',str_replace('"','',json_encode($populations)))));

                        $nb_id_populations = count($id_populations);
                        $ligne = 0;

                        for ($i = 0; $i < $nb_id_populations; $i++) {
                            $maj_statut = $COLLECTIVITES->maj_statut_population($id_populations[$i], $statut, $utilisateur_existe['ID_UTILISATEUR']);
                            if($maj_statut['status'] == true) {
                                $ajout_mvt = $COLLECTIVITES->ajouter_mouvement_affiliation_population($id_populations[$i], null, $trouver_ogd_collectivite['CODE_OGD_COTISATIONS'], $code_collectivite, $statut, $utilisateur_existe['ID_UTILISATEUR']);
                                $ligne++;
                            }
                        }
                        if($ligne==$nb_id_populations){
                            $json = array(
                                'status' => true,
                                'message' => 'L\'ENREGISTREMENT A ETE EFFECTUE AVEC SUCCES.'
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
