<?php
$code_affection = strtoupper($_POST['code_affection']);
if(!empty($code_affection)) {
    require_once '../../../Classes/UTILISATEURS.php';
    require_once '../../../Classes/FACTURES.php';
    $FACTURES = new FACTURES();
    $affection = $FACTURES->trouver_affection($code_affection,NULL);
    if(!empty($affection['CODE'])) {
        $json = array(
            'status' => true,
            'code' => $affection['CODE']
        );
    }else {
        $json = array(
            'status' => false,
            'message' => 'LE CODE AFFECTION: '.$code_affection.' EST INCORRECT.'
        );
    }
    echo json_encode($json);
}