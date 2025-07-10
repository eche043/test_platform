<?php

use GuzzleHttp\Pool;
use GuzzleHttp\Client;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\RequestException;

require '../../../../vendor/autoload.php';

function get_token() {

    $curl = curl_init('https://api.test.cicmu.dev.veone.net/identity/v1/rest/auth/tokens');

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.test.cicmu.dev.veone.net/identity/v1/rest/auth/tokens',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_SSL_VERIFYPEER => false,
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
            'Content-Type: application/json'
        ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    return $response;
}

/*function get_token_guzzle(){
    $client = new Client([
        'timeout' => 60,
        'verify' => false
    ]);
    $headers = [
        'Content-Type' => 'application/json',
        'User-Agent' => 'PostmanRuntime/7.36.0'
    ];
    $body = '{
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
}';
    $request = new Request('POST', 'https://api.test.cicmu.dev.veone.net/identity/v1/rest/auth/tokens', $headers, $body);
    try{
        $res = $client->sendAsync($request)->wait();
        return $res->getBody();
    }catch (\Exception $e){echo $e->getMessage();}




}*/

function get_token_file(){
    $body = '{
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
}';
    $opts = array(
        'http'=>array(
            'method'=>"POST",
            'header'=>  "Content-Type: application/json\r\n".
                        "X-Application-Id: 8b52b960-0392-4ba7-94fd-aab0a66c8bf5\r\n",
            'content' => $body,
        )
    );

    $context = stream_context_create($opts);

// Open the file using the HTTP headers set above
    $file = file_get_contents('https://api.test.cicmu.dev.veone.net/identity/v1/rest/auth/tokens', false, $context);

    return $file;
}

function creation_facture($token, $num_secu) {
    $header = array(
        'Content-Type: application/json',
        'Authorization: '.$token
    );
    $parametres = array(
        "patient" => array(
            "socialSecurityNumber" => $num_secu
        )
    );

    $url = 'https://api.test.cicmu.dev.veone.net/adjudicator/v1/rest/care-sheets';
    if(isset($token) && isset($num_secu)) {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($parametres),
            CURLOPT_HTTPHEADER => $header
        ));
        $response = json_decode(curl_exec($curl));
        curl_close($curl);
        return $response;


    }
    elseif(!isset($token)) {
        return array(
            'success' => false,
            'message' => "Le token est obligatoire"
        );
    }
    else {
        return array(
            'success' => false,
            'message' => "Le numéro sécu est obligatoire"
        );
    }
}

function creation_facture_file($token, $num_secu){

    $body = '{
        "patient": {
            "socialSecurityNumber":'.$num_secu.'
        }
    }';

    $opts = array(
        'http'=>array(
            'method'=>  "POST",
            'header'=>  "Content-Type: application/json\r\n".
                        "X-Application-Id: 8b52b960-0392-4ba7-94fd-aab0a66c8bf5\r\n".
                        "Authorization: $token \r\n",
            'content' => $body,
        )
    );

    $url = 'https://api.test.cicmu.dev.veone.net/adjudicator/v1/rest/care-sheets';
    $context = stream_context_create($opts);
    if(file_get_contents($url, false, $context)){
        return json_decode(file_get_contents($url, false, $context));
    }else{
        return array('success'=>false,'message'=>'echec file_get_contents') ;
    }

}

function maj_facture_file($token, $id_caresheet, $code_complementaire, $nom_complementaire, $code_ps, $nom_ps, $affections){

    $liste_affections = "[".trim(implode(',',$affections))."]";

    $body = '{
         "complementaryInsurer": {
            "code": '.$code_complementaire.',
            "name": '.$nom_complementaire.'
         },
         "careProfessional": {
            "code": '.$code_ps.',
            "name": '.$nom_ps.'
         },
         "affectionCodes":'.trim($liste_affections).'
         }';

    $opts = array(
        'http'=>array(
            'method'=>  "PUT",
            'header'=>  "Content-Type: application/json\r\n".
                        "X-Application-Id: 8b52b960-0392-4ba7-94fd-aab0a66c8bf5\r\n".
                        "Authorization: $token \r\n",
            'content' => $body,
        )
    );
    $url = 'https://api.test.cicmu.dev.veone.net/adjudicator/v1/rest/care-sheets/'.$id_caresheet;
    $context = stream_context_create($opts);
    $file = file_get_contents($url, false, $context);
    return $file ;
}

/*function maj_facture_guzzle($token, $id_caresheet, $code_complementaire, $nom_complementaire, $code_ps, $nom_ps, $affections){
    $liste_affections = '['.$affections.']';
    $client = new Client([
        'timeout' => 60,
        'verify' => false
    ]);
    $headers = [
        'Authorization' => $token,
        'Content-Type' => 'application/json'
    ];

    $body = '{
  "complementaryInsurer": {
    "code": "'.$code_complementaire.'",
    "name": "'.$nom_complementaire.'"
  },
  "careProfessional": {
    "code": "'.$code_ps.'",
    "name": "'.$nom_ps.'"
  },
  "affectionCodes": '.$liste_affections.'
}';

$request = new Request('PUT', 'https://api.test.cicmu.dev.veone.net/adjudicator/v1/rest/care-sheets/'.$id_caresheet, $headers, $body);
    try{
        $res = $client->sendAsync($request)->wait();
        return $res->getBody();
    }catch (\Exception $e){echo $e->getMessage();}

}*/
function maj_facture($token, $id_caresheet, $code_complementaire, $nom_complementaire, $code_ps, $nom_ps, $affections){
    $liste_affections = '['.$affections.']';
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.test.cicmu.dev.veone.net/adjudicator/v1/rest/care-sheets/'.$id_caresheet,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'PUT',
        CURLOPT_POSTFIELDS =>'{
 "complementaryInsurer": {
    "code": "'.$code_complementaire.'",
    "name": "'.$nom_complementaire.'"
  },
  "careProfessional": {
    "code": "'.$code_ps.'",
    "name": "'.$nom_ps.'"
  },
  "affectionCodes": '.$liste_affections.'
}',
        CURLOPT_HTTPHEADER => array(
            'Authorization: '.$token,
            'Content-Type: application/json'
        ),
    ));

    $response = json_decode(curl_exec($curl));

    curl_close($curl);
    return  $response;
}

function adjudication($token, $id_caresheet, $careItems){

    $curl = curl_init();
    $body ='{
 "careItems": [
    '.$careItems.'
]}';
    //https://api.test.cicmu.dev.veone.net/adjudicator/v1/rest/care-sheets/65942586f5d32540db0a2161/adjudicate
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.test.cicmu.dev.veone.net/adjudicator/v1/rest/care-sheets/'.$id_caresheet.'/adjudicate',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>$body,
        CURLOPT_HTTPHEADER => array(
            'Authorization: '.$token,
            'Content-Type: application/json'
        ),
    ));

    $response = json_decode(curl_exec($curl));

    curl_close($curl);
    return  $response;
}
function adjudication_mws($token, $id_caresheet, $careItems){

$curl = curl_init();
    $body ='{ "id_caresheet":"'.$id_caresheet.'",
 "careItems": [
    '.$careItems.'
]}';
curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://test-mws.ipscnam.ci/cicmu/send/adjudication.php',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS =>$body,
    CURLOPT_HTTPHEADER => array(
        'Authorization: '.$token,
        'Content-Type: application/json'
    ),
));

$response = json_decode(curl_exec($curl));

curl_close($curl);
    return $response;

}
function adjudication_guzzle($token, $id_caresheet, $careItems){

    /*$curl = curl_init();
    $body ='{
 "careItems": [
    '.$careItems.'
]}';
    //https://api.test.cicmu.dev.veone.net/adjudicator/v1/rest/care-sheets/65942586f5d32540db0a2161/adjudicate
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.test.cicmu.dev.veone.net/adjudicator/v1/rest/care-sheets/'.$id_caresheet.'/adjudicate',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>$body,
        CURLOPT_HTTPHEADER => array(
            'Authorization: '.$token,
            'Content-Type: application/json'
        ),
    ));

    $response = json_decode(curl_exec($curl));

    curl_close($curl);
    return  $response;*/

    $body ='{
 "careItems": [
    '.$careItems.'
]}';
    $client = new Client([
        'timeout' => 60,
        'verify' => false
    ]);
    $headers = [
        'Authorization' => $token,
        'Content-Type' => 'application/json'
    ];


    $request = new Request('POST', 'https://api.test.cicmu.dev.veone.net/adjudicator/v1/rest/care-sheets/'.$id_caresheet.'/adjudicate', $headers, $body);
    try{
        $res = $client->sendAsync($request)->wait();
        return $res->getBody();
    }catch (\Exception $e){echo $e->getMessage();}
}

function trouver_transaction_cicmu($token, $type_police){
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.test.cicmu.dev.veone.net/adjudicator/v1/rest/transactions?insurancePolicyType='.$type_police.'&details=null',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Authorization: '.$token,
            'Content-Type: application/json'
        ),
    ));

    $response = json_decode(curl_exec($curl));

    curl_close($curl);
    return $response;
}

function consultation_waf_guzzle($type_envoi, $num_secu, $user){
    $client =  new Client([
        'timeout' => 60,
        'verify' => false
    ]);
    $headers = [
        'Cookie' => 'PHPSESSID=4jsqrja190fft7rstev57n029r'
    ];
    $options = [
        'multipart' => [
            [
                'name' => 'type_envoi',
                'contents' => $type_envoi
            ],
            [
                'name' => 'num_secu',
                'contents' => $num_secu
            ],
            [
                'name' => 'user',
                'contents' => $user
            ]
        ]];
    $request = new Request('POST', 'https://ecnam-test.ipscnam.ci/webservices/cmu/prestations/consultation_droits.php', $headers);
    $res = $client->sendAsync($request, $options)->wait();
    return $res->getBody();
}
function consultation_waf_file($type_envoi, $num_secu, $user){

    $postdata = http_build_query(
        array(
            'type_envoi' => $type_envoi,
            'num_secu' => $num_secu,
            'user' => $user
        )
    );

    $opts = array('http' =>
        array(
            'method'  => 'POST',
            'header'  => 'Content-Type: application/x-www-form-urlencoded',
            'content' => $postdata
        )
    );

    $context  = stream_context_create($opts);

    /*$result = file_get_contents('http://example.com/submit.php', false, $context);
    $body = '{
            "type_envoi": '.$type_envoi.',
            "num_secu":'.$num_secu.',
            "user":'.$user.',
         }';

    $opts = array(
        'http'=>array(
            'method'=>  "POST",
            'header'=>  "Content-Type: application/json\r\n".
                "X-Application-Id: 8b52b960-0392-4ba7-94fd-aab0a66c8bf5\r\n",
            'content' => $body,
        )
    );*/
    $url = 'https://ecnam-test.ipscnam.ci/webservices/cmu/prestations/consultation_droits.php';
    $context = stream_context_create($opts);
    $file = file_get_contents($url, false, $context);
    return $file ;
}

