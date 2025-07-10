<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-with");

$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://api.cicmu.cnam.dev.veone.net/identity/v1/rest/auth/tokens',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS =>'{
    "identity": {
        "methods": [
            "password"
        ],
        "password": {
            "user": {
                "username": "dognimin.koulibali@ipscnam.ci",
                "password": "Cnam@2023"
            }
        }
    }
}',
    CURLOPT_HTTPHEADER => array(
        'Accept: application/json',
        'X-Application-Id: 8b52b960-0392-4ba7-94fd-aab0a66c8bf5',
        'Content-Type: application/json'
    ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;