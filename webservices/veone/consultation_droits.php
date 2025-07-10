<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-with");

/*
 *
*/

include "functions.php";
$get_token = token();
if($get_token['success']) {
    $token = $get_token['token'];
    $numero_secu = '3841443513209';
    $edtion = creation_facture($token, $numero_secu);
    $json = $edtion;
}else {
    $json = $get_token;
}
echo json_encode($json);
