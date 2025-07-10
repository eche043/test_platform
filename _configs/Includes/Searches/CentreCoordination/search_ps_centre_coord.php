<?php

$code_ps = $_POST['code_ps'];

include "../../../Classes/UTILISATEURS.php";
include "../../../Classes/COORDINATIONS.php";
$COORDINATIONS = new COORDINATIONS();

$ps = $COORDINATIONS->lister_ps($code_ps);

if(empty($ps['nom_ps']) && empty($ps['prenom_ps']))
{
    $json = array(
        'status' => false,
        'message' => 'LE CODE DU PS SAISI EST INCORRECT.'
    );
}else
{
    $json = array(
        'status' => true,
        'nom' => $ps['nom_ps'],
        'prenom' => $ps['prenom_ps']
    );
}


echo json_encode($json);
