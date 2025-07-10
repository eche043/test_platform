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
                if(empty($_POST['code_collectivite'])){
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

                        $type_beneficiaire = $_POST['type_individu'];
                        $numero_secu_payeur = $_POST['numero_secu_payeur'];
                        $numero_matricule_payeur = $_POST['num_matricule_ogd_payeur'];
                        $numero_entreprise_payeur = $_POST['num_matricule_entreprise_payeur'];
                        $nom_payeur = $_POST['nom_payeur'];
                        $prenoms_payeur = $_POST['prenom_payeur'];
                        if(!empty(trim($_POST['date_naissance_payeur']))) {
                            $date_naissance_payeur = strtoupper(date('Y-m-d', strtotime(str_replace('/', '-', trim($_POST['date_naissance_payeur'])))));
                        }else{
                            $date_naissance_payeur = null;
                        }
                        if($type_beneficiaire=='T'){
                            $numero_secu_benef = $numero_secu_payeur;
                            $numero_matricule_benef = $numero_matricule_payeur;
                            $numero_entreprise_benef = $numero_entreprise_payeur;
                            $nom_benef = $nom_payeur;
                            $prenoms_benef = $prenoms_payeur;
                            $date_naissance_benef = $date_naissance_payeur;
                        }else{
                            $numero_secu_benef = $_POST['numero_secu_beneficiaire'];
                            $numero_matricule_benef = $_POST['num_matricule_ogd_beneficiaire'];
                            $numero_entreprise_benef = $_POST['num_matricule_entreprise_beneficiaire'];
                            $nom_benef = $_POST['nom_beneficiaire'];
                            $prenoms_benef = $_POST['prenom_beneficiaire'];
                            if(!empty(trim($_POST['date_naissance_beneficiaire']))) {
                                $date_naissance_benef = null;
                            }else{
                                $date_naissance_benef = strtoupper(date('Y-m-d',strtotime(str_replace('/','-',trim($_POST['date_naissance_beneficiaire'])))));
                            }

                        }


                        $sexe = $_POST['genre'];
                        $civile = $_POST['civilite'];
                        $lieu_naissance = $_POST['lieu_naissance'];
                        $lieu_residence = $_POST['lieu_resideance'];
                        $id_population = $_POST['id_population'];
						if(isset($_POST['telephone_1'])){$telephone_1 = trim($_POST['telephone_1']);}else{$telephone_1 = null;}
						if(isset($_POST['telephone_2'])){$telephone_2 = trim($_POST['telephone_2']);}else{$telephone_2 = null;}
						if(isset($_POST['email'])){$email = trim($_POST['email']);}else{$email = null;}
                        //$telephone_1 = trim($_POST['telephone_1']);
                        //$telephone_2 = trim($_POST['telephone_2']);
                        //$email = trim($_POST['email']);

                        $up = 0;
                        $ins = 0;
                        $erreur = 0;

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
                        if($erreur!=0){
                            $json = array(
                                'status' => false,
                                'message' => $json['message']
                            );
                        }else{
                            $ajout_population = $COLLECTIVITES->ajouter_nouvelle_population($id_population, $trouver_ogd_collectivite['CODE_OGD_COTISATIONS'], $code_collectivite, $type_beneficiaire, $numero_secu_payeur, $numero_entreprise_payeur, $numero_matricule_payeur, $nom_payeur, $prenoms_payeur, $date_naissance_payeur, $numero_secu_benef, $numero_entreprise_benef, $numero_matricule_benef, $nom_benef, $prenoms_benef, $date_naissance_benef, $civile, $sexe, $lieu_naissance, $lieu_residence, $telephone_1, $telephone_2, $email, 1,$utilisateur_existe['ID_UTILISATEUR']);
                            if($ajout_population['status']==true){
                                $ajout_mvt = $COLLECTIVITES->ajouter_mouvement_affiliation_population($id_population, null, $trouver_ogd_collectivite['CODE_OGD_COTISATIONS'], $code_collectivite, 1, $utilisateur_existe['ID_UTILISATEUR']);
                                $json = array(
                                    'status' => true,
                                    'message' => 'L\'ENREGISTREMENT A ETE EFFECTUE AVEC SUCCES.'
                                );
                            }
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
