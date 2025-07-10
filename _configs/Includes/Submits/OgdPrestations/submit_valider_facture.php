<?php
require_once '../../../Classes/UTILISATEURS.php';
if(isset($_SESSION['ECMU_USER_ID']) || !empty($_SESSION['ECMU_USER_ID'])) {
    $UTILISATEURS = new UTILISATEURS();
    $user = $UTILISATEURS->trouver($_SESSION['ECMU_USER_ID'], NULL, NULL);
    if (!empty($user['ID_UTILISATEUR'])) {
        require_once '../../../Classes/FACTURES.php';
        $FACTURES = new FACTURES();

        $type_verification = trim($_POST['type_verification']);
        $numero_facture = trim($_POST['numero_facture']);

        $facture = $FACTURES->trouver_facture(null,$numero_facture);

        if(!empty($facture['FEUILLE'])) {
            if($type_verification=='DECA'){
                $verification_facture = $FACTURES->validation_verification_facture_deca($numero_facture,$user['ID_UTILISATEUR']);
				/* if($validation_verification_facture['status']===true){
                    $verification_facture = $FACTURES->insert_verification_facture($numero_facture,$user['ID_UTILISATEUR']);
                    $verification_facture['message']=$validation_verification_facture['message'];
                } */
            }else{
                $verification_facture = $FACTURES->liquidation_facture('B',$numero_facture,$user['ID_UTILISATEUR']);
            }
            $json = $verification_facture;
        }else{
            $json = array(
                'status' => false,
                'message' => "CETTE FEUILLE EST ERRONNEE."
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



