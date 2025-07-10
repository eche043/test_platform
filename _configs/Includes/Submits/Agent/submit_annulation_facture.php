<?php
use GuzzleHttp\Pool;
use GuzzleHttp\Client;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\RequestException;

require_once '../../../Classes/UTILISATEURS.php';
require '../../../../vendor/autoload.php';

if(isset($_SESSION['ECMU_USER_ID'])){
    $session_user = $_SESSION['ECMU_USER_ID'];
    if(!empty($session_user)){
        $UTILISATEURS = new UTILISATEURS();
        $utilisateur_existe = $UTILISATEURS->trouver($session_user,null,null);

        if(!empty($utilisateur_existe['ID_UTILISATEUR'])){
            $trouver_ets = $UTILISATEURS->trouver_ets_utilisateur($session_user);
            $motif_annulation = trim($_POST['motif_annulation']);
            $num_facture = trim($_POST['num_facture']);
            $date_statut = date('Y-m-d',time());
            require_once '../../../Classes/FACTURES.php';
            $FACTURES = new FACTURES();
            if($utilisateur_existe['ACTIF'] != 1){
                $json = array(
                    'status' => false,
                    'message' => 'VOTRE IDENTIFIANT A ETE DESACTIVE. VOUS NE POUVEZ PAS EFFECTUER CETTE ACTION. VEUILLEZ CONTACTER VOTRE ADMINISTRATEUR SYSTEME.'
                );
            }else{
                 $facture = $FACTURES->trouver($num_facture);
                if(!empty($facture['FEUILLE'])) {
                    $client = new Client([
                        'timeout' => 60,
                        'verify' => false
                    ]);

                    $headers = [
                        //'Authorization' => 'Bearer 1|pC8jCa84ZSYMjg4fwKWluPKzIWOFRMuSB81dX9ci316eec2b',
                        'Authorization' => 'Bearer 79|VdcGNkIF1YKKeqZeUuYN5TgjYqgOZdlGdQnDb6Fj0dc9a4b6',
                        'accept' => 'application/json',
                        'Content-Type' => 'application/json'
                    ];
                    $method = 'PUT';
                    $url = 'https://10.10.4.85/api/prestations/factures/'.$num_facture;
                    $rejets[] = array(
                        "code_rejet" => "",
                        "code_acte" => ""
                    );
                    $body = json_encode([
                        "code_statut"=> 'A',
                        "date_statut"=> $date_statut,
                        "code_etablissement"=> $facture['ETABLISSEMENT'],
                        "code_organisme"=> $facture['NUM_OGD'],
                        "motif_annulation"=> $motif_annulation,
                        "rejets"=> $rejets
                    ]);

                    $request = new Request($method, $url, $headers, $body);
                    try{
                        $res = $client->send($request);

                        $reponse = json_decode($res->getBody());
                        if($reponse->success===true){
                            $annulation_facture = $FACTURES->annuler_facture($num_facture,$motif_annulation,$session_user);

                            if($annulation_facture['status'] === true){
                                $json = array(
                                    'status' => $annulation_facture['status'],
                                    'message' => $annulation_facture['message']
                                );
                            }else{
                                $json = $annulation_facture;
                            }
                        }
                        else{
                            $json = array(
                                'status' => false,
                                'message' => $reponse['success']
                            );
                        }
                    }
                    catch (\Throwable $e){
                        var_dump(array(json_encode($e->getMessage()),$e->getLine()));
                        $response = $e->getResponse();
                        $json = array('status' => false, 'message'=>$response->getBody()->getContents());
                    }
                }
                else {
                    $json = array(
                        'status' => false,
                        'message' => 'LE N° DE FACTURE SAISI EST INCORRECT. VEUILLEZ CONTACTER VOTRE ADMINISTRATEUR SYSTEME.'
                    );
                }
            }
        }else{
            $json = array(
                'status' => false,
                'message' => 'VOTRE IDENTIFIANT EST INCORRECT. VOUS NE POUVEZ PAS EFFECTUER CETTE ACTION. VEUILLEZ CONTACTER VOTRE ADMINISTRATEUR SYSTEME.'
            );
        }
    }else{
        $json = array(
            'status' => false,
            'message' => 'VOTRE IDENTIFIANT EST INCORRECT. VOUS NE POUVEZ PAS EFFECTUER CETTE ACTION. VEUILLEZ CONTACTER VOTRE ADMINISTRATEUR SYSTEME.'
        );
    }
}else{
    $json = array(
        'status' => false,
        'message' => 'VOTRE IDENTIFIANT EST INCORRECT. VOUS NE POUVEZ PAS EFFECTUER CETTE ACTION. VEUILLEZ CONTACTER VOTRE ADMINISTRATEUR SYSTEME.'
    );
}

echo json_encode($json);
?>

