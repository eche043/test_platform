<?php
/**
 * Created by PhpStorm.
 * User: mohamed.kone
 * Date: 06/02/2020
 * Time: 17:03
 */
include "../../Classes/UTILISATEURS.php";
session_destroy();
$json = array(
    'status' => 'success'
);
echo json_encode($json);

?>