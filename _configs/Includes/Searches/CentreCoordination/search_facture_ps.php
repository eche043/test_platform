<?php
if(!empty(count($_POST))) {
    $code_ps = $_POST['code_ps'];
    $nom_ps = NULL;
    $code_ets = $_POST['code_ets'];
    if(empty($_POST['date_soins'])) {
        $date_soins = null;
    }else {
        $date_soins = strtoupper(date('Y-m-d',strtotime(str_replace('/','-',$_POST['date_soins']))));
    }
}else {
    $code_ps = NULL;
    $nom_ps = strtoupper($_GET['nom_ps']);
    $code_ets = $_GET['code_ets'];
    if(empty($_GET['date_soins'])) {
        $date_soins = null;
    }else {
        $date_soins = strtoupper(date('Y-m-d',strtotime(str_replace('/','-',$_GET['date_soins']))));
    }
}



if(!empty($code_ets) && !empty($date_soins)) {
    include "../../../Classes/UTILISATEURS.php";
    include "../../../Classes/FACTURES.php";
    $FACTURES = new FACTURES();
    $json = $FACTURES->verifier_facture_ps($code_ps,$nom_ps,$code_ets,$date_soins);
    echo json_encode($json);
    flush();
}
?>