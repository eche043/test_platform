<?php
/**
 * Created by PhpStorm.
 * User: mohamed.kone
 * Date: 28/04/2021
 * Time: 14:21
 */

function envoi_sms($type_envoi,$numero,$message){

    $url = URL."webservices/envoi-sms.php";
    //$url = "https://recette-ecmu.ipscnam.ci/webservices/envoi-sms.php";
    $parametres = array(
        'type_envoi' => $type_envoi,
        'numero' => $numero,
        'message' => $message
    );

    $options = array(
        'http' => array(
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($parametres),
        )
    );

    $context  = stream_context_create($options);

    $retour = json_decode(file_get_contents($url, false, $context));

    return $retour;
}
