<?php
$num_fs_initiale = $_POST['num_fs'];
$num_secu = $_POST['num_secu'];
if(empty($_POST['date_soins'])) {
    $date_soins = null;
}else {
    $date_soins = strtoupper(date('Y-m-d',strtotime(str_replace('/','-',$_POST['date_soins']))));
}

if(!empty($num_fs_initiale) && !empty($num_secu) && !empty($date_soins)) {
    include "../../../Classes/UTILISATEURS.php";
    include "../../../Classes/FACTURES.php";
    $FACTURES = new FACTURES();
    $json = $FACTURES->verifier_num_fs_initiale($num_fs_initiale,$num_secu,$date_soins);
    echo json_encode($json);
}