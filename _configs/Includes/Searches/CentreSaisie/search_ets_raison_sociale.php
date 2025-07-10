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
                $json[] = array(
                    'status' => false,
                    'message' => 'VOTRE IDENTIFIANT A ETE DESACTIVE. VOUS NE POUVEZ PAS EFFECTUER CETTE ACTION. VEUILLEZ CONTACTER VOTRE ADMINISTRATEUR SYSTEME.'
                );
            }else{
                $raison_sociale = trim($_GET['raison_sociale']);
                if(!empty($raison_sociale)) {
                    require_once '../../../Classes/ETABLISSEMENTSSANTE.php';
                    $ETABLISSEMENTSSANTE = new ETABLISSEMENTSSANTE();
                    $liste_ets = $ETABLISSEMENTSSANTE->trouver_ets_raison_sociale($raison_sociale);

                    if(count($liste_ets)==0){
                        $json[] = array(
                            'status' => false,
                            'message' => 'AUCUN ETABLISSEMENT NE CORRESPOND A CETTE RECHERCHE.'
                        );
                    }else{
                        foreach($liste_ets as $ets) {
                            $json[] = array(
                                'status' => true,
                                'message' => count($liste_ets) . ' RESULTATS TROUVES.',
                                'code' => $ets["CODE_ETS"],
                                'value' => $ets['RAISON_SOCIALE'],
                                'label' => $ets['RAISON_SOCIALE']
                            );
                        }
                    }
                }else{
                    $json[] = array(
                        'status' => false,
                        'message' => 'LES INFORMATIONS SAISIES SONT INCORRECTS. PRIERE ENTRER DES DONNEES VALIDES.'
                    );
                }
            }
        }else{
            $json[] = array(
                'status' => false,
                'message' => 'VOTRE IDENTIFIANT EST INCORRECT. VOUS NE POUVEZ PAS EFFECTUER CETTE ACTION. VEUILLEZ CONTACTER VOTRE ADMINISTRATEUR SYSTEME.'
            );
        }
    }else{
        $json[] = array(
            'status' => false,
            'message' => 'VOTRE IDENTIFIANT EST INCORRECT. VOUS NE POUVEZ PAS EFFECTUER CETTE ACTION. VEUILLEZ CONTACTER VOTRE ADMINISTRATEUR SYSTEME.'
        );
    }
}else{
    $json[] = array(
        'status' => false,
        'message' => 'VOTRE IDENTIFIANT EST INCORRECT. VOUS NE POUVEZ PAS EFFECTUER CETTE ACTION. VEUILLEZ CONTACTER VOTRE ADMINISTRATEUR SYSTEME.'
    );
}

echo json_encode($json);
flush();
?>


