<?php
use GuzzleHttp\Pool;
use GuzzleHttp\Client;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\RequestException;

require_once '../../../Classes/UTILISATEURS.php';
require '../../../../vendor/autoload.php';

if(isset($_SESSION['ECMU_USER_ID']) || !empty($_SESSION['ECMU_USER_ID'])) {
    $UTILISATEURS = new UTILISATEURS();
    $user = $UTILISATEURS->trouver($_SESSION['ECMU_USER_ID'], NULL, NULL);
    if (!empty($user['ID_UTILISATEUR'])) {

        if(isset($_POST['num_ep']) && !empty($_POST['num_ep'])) {
            $num_ep = $_POST['num_ep'];
            $total = $_POST['total'];
            $code_actes = $_POST['code_acte'];
            $motif_refus = $_POST['motif_refus'];

                require_once "../../../Classes/ENTENTESPREALABLES.php";
                require_once "../../../Classes/ASSURES.php";
                require_once "../../../Classes/ETABLISSEMENTSSANTE.php";

                require("../../../../vendor/phpmailer/phpmailer/src/PHPMailer.php");
                require("../../../../vendor/phpmailer/phpmailer/src/SMTP.php");

                $ENTENTESPREALABLES = new ENTENTESPREALABLES();
                $ETABLISSEMENTSSANTE = new ETABLISSEMENTSSANTE();
                $ASSURES = new ASSURES();

            $client = new Client([
                'timeout' => 60,
                'verify' => false
            ]);
            $headers = [
                'Authorization' => 'Bearer 715|WTX2uVR850ZKYtc3auj9oCNK1UXPemWcqHisR1C6e4f44cbb',
                'accept' => 'application/json',
                'Content-Type' => 'application/json'
            ];

            $body_actes = array();

            foreach($code_actes as $actes){
                $acte = explode('_',$actes);
                $motif = "";
                $m = "";
                //if($motif_refus[$acte[1]]){$motif = $motif_refus[$acte[1]];}
                foreach($motif_refus as $motifs){
                    $motif = explode('|',$motifs);
                    if($motif[0]===$acte[1]){
                        $m = $motif[1];
                    }
                }
                $body_actes[] = array(
                    "code" =>$acte[1],
                    "code_statut"=>$acte[0],
                    "motif_rejet"=>$m
                );
            }

            $body = json_encode([
                "actes" =>$body_actes
            ]);

            $request = new Request('PATCH', 'https://10.10.4.85:3128/api/prestations/ententes-prealables/'.$num_ep, $headers, $body);
            try{
                $res = $client->sendAsync($request)->wait();
                $reponse = json_decode($res->getBody());

                if(isset($reponse->numero)){
                    $json = array(
                        'status' => true,
                        'numero' => $reponse->numero,
                        'entente_prealable' => $reponse,
                    );
                }
                else{
                    $json = array(
                        'status' => $reponse['success'],
                        'message' => $reponse['message'],
                        'numero'=>null
                    );
                }
            }catch (\Exception $e){
                //$json = ;
                $json = array(
                    'status' => false,
                    'message' => $e->getMessage()
                );
            }


        }
        else{
            $json = array(
                'status' => false,
                'message' => " LE NUMERO DE l'ENTENTE PREALABLE N'EST PAS DEFINIE. PRIERE VERIFIER LES PARAMETRES."
            );
        }


    }
    else{
        $json = array(
            'status' => false,
            'message' => " CET UTILISATEUR N\'EXISTE PAS. PRIERE VERIFIER VOS PARAMETRES."
        );
    }
}
else{
    $json = array(
        'status' => false,
        'message' => " LA SESSION DE CET UTILISATEUR A ETE ARRETEE. PRIERE VOUS RECONNECTER AFIN DE CONTINUER L'OPERATION."
    );
}
echo json_encode($json);



