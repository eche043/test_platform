<?php
//session_start();
require_once "../../Classes/UTILISATEURS.php";
session_destroy();
echo json_encode(array('status' => true));
?>
