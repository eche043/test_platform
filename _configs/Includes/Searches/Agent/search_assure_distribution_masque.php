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
                $num_secu = trim($_POST['num_secu']);
                if(!empty($num_secu)) {
                    require_once '../../../Classes/ASSURES.php';
                    require_once '../../../Classes/DISTRIBUTIONMASQUES.php';
                    $ASSURES = new ASSURES();
                    $DISTRIBUTIONMASQUES = new DISTRIBUTIONMASQUES();
                    $once_severd_assure = $DISTRIBUTIONMASQUES->trouver_premier_retrait($num_secu);
                    if(isset($once_severd_assure['NUMERO_TELEPHONE']) && !empty($once_severd_assure['NUMERO_TELEPHONE'])){
                        $numero_telephone = $once_severd_assure['NUMERO_TELEPHONE'];
                    }else{
                        $numero_telephone = NULL;
                    }
                    $assure = $ASSURES->trouver($num_secu);
                    if(!empty($assure['NUM_SECU'])) {
						if($assure['ACTIVE']==0){
                            $json = array(
                                'status' => false,
                                'message' => "CET ASSURE N'EST PAS CONCERNE PAR CETTE DISTRIBUTION."
                            );
                        }else {
							$json = array(
								'status' => true,
								'num_secu' => $assure['NUM_SECU'],
								'nom' => $assure['NOM'],
								'prenom' => $assure['PRENOM'],
								'date_naissance' => date('d/m/Y',strtotime($assure['DATE_NAISSANCE'])),
								'csp' => $assure['CATEGORIE_PROFESSIONNELLE'],
								'civilite' => $assure['CIVILITE'],
								'sexe' => $assure['SEXE'],
								'payeur_num_secu' => $assure['PAYEUR_NUM_SECU'],
								'lieu_naissance' => $assure['NAISSANCE_NOM_ACHEMINEMENT'],
								'lieu_residence' => $assure['ADRESSE_NOM_ACHEMINEMENT'],
								'numero_telephone' => $numero_telephone
							);
						}
                    }else {
                        $json = array(
                            'status' => false,
                            'message' => "LE N° SECU SAISI EST INCORRECT."
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
            'message' => 'VOTRE IDENTIFIANT EST INCORRECT. VOUS NE POUVEZ PAS EFFECTUER CETTE ACTION. VEUILLEZ CONTACTER VOTRE ADMINISTRATEUR SYSTEME.'
        );
    }
}else{
    $json = array(
        'status' => false,
        'message' => 'VOTRE IDENTIFIANT EST INCORRECT. VOUS NE POUVEZ PAS EFFECTUER CETTE ACTION. VEUILLEZ CONTACTER VOTRE ADMINISTRATEUR SYSTEME.'
    );
}

echo json_encode($json);
?>