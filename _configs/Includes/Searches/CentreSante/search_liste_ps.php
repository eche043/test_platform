<?php
 $code_ets = trim($_POST['code_ets']);
if(!empty($code_ets)) {
    require_once '../../../Classes/UTILISATEURS.php';
    require_once '../../../Classes/PROFESSIONNELSANTE.php';
    $PROFESSIONNELSANTE = new PROFESSIONNELSANTE();
    $json = array();
    $professionnels = $PROFESSIONNELSANTE->lister_ets_ps($code_ets);
    foreach ($professionnels as $professionnel) {
        $json[$professionnel['CODE_PS']][] = $professionnel['NOM'].' '.$professionnel['PRENOM'];
    }

}
echo json_encode($json);