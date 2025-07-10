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
                        'message' => 'VOTRE COLLECTIVITE N\'A PAS ETE DEFINIE. VOUS NE POUVEZ PAS EFFECTUER CETTE ACTION. VEUILLEZ CONTACTER VOTRE ADMINISTRATEUR SYSTEME.'
                    );
                }else{
                    require_once '../../../Classes/ASSURES.php';
                    require_once '../../../Classes/COLLECTIVITES.php';
                    $ASSURES = new ASSURES();
                    $COLLECTIVITES = new COLLECTIVITES();

                    $code_collectivite = $_POST["code_collectivite"];

                    $trouver_ogd_collectivite = $COLLECTIVITES->trouver($code_collectivite);
                    if(isset($trouver_ogd_collectivite['CODE_OGD_COTISATIONS']) && !empty($trouver_ogd_collectivite['CODE_OGD_COTISATIONS'])) {

                        $mois = str_pad(trim($_POST["mois_decl_cotisation"]),2,'0',STR_PAD_LEFT);
                        $annee = trim($_POST["annee_decl_cotisation"]);
                        $trouver_populations = $COLLECTIVITES->trouver_population_pour_declarations($mois,$annee,$code_collectivite);
                        $nb_pop  = count($trouver_populations);
                        $ligne = 0;
                        $succes = 0;
                        $erreur = 0;
                        $populations = '';
                        if($nb_pop!=0){
                            $ajouter_log_cotisation = $COLLECTIVITES->ajouter_declaration_cotisation_collectivite($trouver_ogd_collectivite['CODE_OGD_COTISATIONS'], $code_collectivite, $nb_pop, $annee,$mois, $utilisateur_existe['ID_UTILISATEUR']);
                            if($ajouter_log_cotisation['status']==true){
                                $last_declaration = $COLLECTIVITES->last_declaration();
                                if(!empty($last_declaration['LAST_DECLARATION'])) {
                                    foreach ($trouver_populations as $pop) {
                                        $trouver_cotisation = $COLLECTIVITES->trouver_cotisation(trim($mois), trim($annee), trim($pop['ID']), $last_declaration['LAST_DECLARATION']);
                                        if (isset($trouver_cotisation['ID_POPULATION'])) {
                                            $erreur++;
                                        } else {
                                            $ajouter_declarations = $COLLECTIVITES->ajouter_declaration_cotisation($pop['ID'], $last_declaration['LAST_DECLARATION'], $annee, $mois, $code_collectivite, $trouver_ogd_collectivite['CODE_OGD_COTISATIONS'], $pop['BENEFICIAIRE_NUM_SECU'], $pop['PAYEUR_NUM_SECU'], $utilisateur_existe['ID_UTILISATEUR']);
                                            if ($ajouter_declarations['status'] == true) {
                                                $succes++;
                                            }
                                        }
                                        $ligne++;
                                    }
                                    if ($ligne == $nb_pop) {
                                        if ($succes != 0) {

                                            $json = array(
                                                'status' => true,
                                                'message' => $succes . ' DECLARATIONS ONT ETE ENREGISTREES AVEC SUCCES.<br>'
                                            );
                                        } else {
                                            $json = array(
                                                'status' => false,
                                                'message' => 'DES DECLARATIONS ONT DEJA ETE EFFECTUEES POUR CES PERSONNES POUR LE MOIS DE <b>' . $_POST["mois_decl_cotisation"] . '</b>.'
                                            );
                                        }
                                    }
                                }
                            }else{
                                $json = array(
                                    'status' => false,
                                    'message' => 'LA DECLARATION DES COTISATIONS A ECHOUE.'
                                );
                            }
                        }else{
                            $json = array(
                                'status' => false,
                                'message' => 'AUCUNE POPULATION N\'A ETE TROUVEE. PRIERE VERIFIER VOS DECLARATIONS DE POPULATIONS'
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
