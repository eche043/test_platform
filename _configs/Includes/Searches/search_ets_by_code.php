<?php
$code_ets = trim($_POST['code_ets']);
if(!empty($code_ets)) {
    require_once '../../Classes/UTILISATEURS.php';
    require_once '../../Classes/COORDINATIONS.php';
    $COORDINATIONS = new COORDINATIONS();

    $ets = $COORDINATIONS->trouver_ets_valide($code_ets);
    if(empty($ets['CODE'])) {
        $json = array(
            'status' => 'failed',
            'message' => 'LE CODE ETABLISSEMENT '.$code_ets.' N\'EST PAS VALIDE.'
        );
    }else {
        $json = $ets;
    }
}
echo json_encode($json, NULL);