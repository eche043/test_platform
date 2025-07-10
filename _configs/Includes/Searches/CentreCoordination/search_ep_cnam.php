<?php
require_once '../../../Classes/UTILISATEURS.php';
if(isset($_SESSION['ECMU_USER_ID'])){
    $session_user = $_SESSION['ECMU_USER_ID'];
    require_once '../../../Classes/FACTURES.php';
    $FACTURES = new FACTURES();

    $num_ep_cnam = $_POST['num_ep_cnam'];
    $num_secu = $_POST['num_secu'];
    $num_facture = $_POST['num_facture'];
    if(!empty($_POST['date_soins'])) {
        $date_soins = strtoupper(date('Y-m-d',strtotime(str_replace('/','-',$_POST['date_soins']))));
    }else {
        $date_soins = NULL;
    }
    $verification_ep = $FACTURES->verifier_entente_prealable($num_ep_cnam,$num_secu,$date_soins);
    if(isset($verification_ep['status']) && $verification_ep['status']==true ){
        if($verification_ep['type_facture']=='HOS') {
            $maj_ep_facture_hosp = $FACTURES->maj_type_facture($num_facture, $verification_ep['type_facture'], $verification_ep['fs_initiale'], $num_ep_cnam,$session_user);
            $json = $verification_ep;
        }else{
            $json = array(
                'status' => true,
                'actes' => $verification_ep['actes']
            );
        }
    }else{
        $json = array(
            'status' => false,
            'message' => $verification_ep['message']
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