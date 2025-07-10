<?php
/*use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;*/
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
            if($utilisateur_existe['ACTIF'] != 1){
                $json = array(
                    'status' => false,
                    'message' => 'VOTRE IDENTIFIANT A ETE DESACTIVE. VOUS NE POUVEZ PAS EFFECTUER CETTE ACTION. VEUILLEZ CONTACTER VOTRE ADMINISTRATEUR SYSTEME.'
                );
            }else{

                require_once '../../../Classes/ASSURES.php';
                require_once '../../../Classes/ENTENTESPREALABLES.php';
                require_once '../../../Classes/ETABLISSEMENTSSANTE.php';

                require("../../../../vendor/phpmailer/phpmailer/src/PHPMailer.php");
                require("../../../../vendor/phpmailer/phpmailer/src/SMTP.php");

                $ASSURES = new ASSURES();
                $ENTENTESPREALABLES = new ENTENTESPREALABLES();
                $ETABLISSEMENTSSANTE = new ETABLISSEMENTSSANTE();

                $code_ets = trim($_POST['code_ets']);
                $num_secu = $_POST['num_secu'];
                $motif = trim($_POST['motif']);
                //$numFsInit = trim($_POST['num_fs_init']);
                //$typeEp = trim($_POST['type_ep']);

                if(!empty($code_ets) && !empty($motif) && !empty($num_secu)) {
                    $etablissement = $ETABLISSEMENTSSANTE->trouver($code_ets);
                    $trouver_assure = $ASSURES->trouver($num_secu);
                    if(isset($trouver_assure['NUM_SECU']) && !empty($trouver_assure['NUM_SECU'])){
                        //$lastId = $ENTENTESPREALABLES->trouver_lastId_entente_prealable();
                        //$new_numero_entente_prealable = $lastId['LASTID'] + 1;
                        /*if($typeEp == "HOS"){
                            if(empty(trim($_POST['type_hosp']))){
                                $typeHosp = NULL;
                            }else{
                                $typeHosp = trim($_POST['type_hosp']);
                            }
                            $demande_entente = $ENTENTESPREALABLES->trouver_entente_hospitalisation_valide($typeEp,$typeHosp,$numFsInit,$num_secu);
                            if(!isset($demande_entente['ID'])){
                                $reponse = $ENTENTESPREALABLES->ajouter_nouvelle_demande_entente($typeEp,$typeHosp,$numFsInit,null,$num_secu,$code_ets,$new_numero_entente_prealable,$motif,$trouver_assure['CODE_OGD_PRESTATIONS_PROV'],0,$utilisateur_existe['ID_UTILISATEUR']);
                            }else {
                                $json = array(
                                    'status' => false,
                                    'message' => "UNE DEMANDE A DEJA ETE TRANSMISE".$trouver_assure['CODE_OGD_PRESTATIONS_PROV']
                                );
								$reponse = $json;
                            }
                        }
                        else if($typeEp=="EXP"){
                            $succes = 0;
                            $echec = 0;
                            $demande_entente = $ENTENTESPREALABLES->trouver_entente_biologie_valide($typeEp,$numFsInit,$num_secu);
                            if(!isset($demande_entente['ID'])) {
                                for ($i = 1; $i <= 3; $i++) {
                                    $acte = 'acte_'.$i;
                                    $code_acte = trim($_POST[$acte]);
                                    if(!empty($code_acte)) {
                                        $insertion = $ENTENTESPREALABLES->ajouter_nouvelle_demande_entente($typeEp, NULL, $numFsInit,$code_acte,$num_secu,$code_ets,$new_numero_entente_prealable,$motif,$trouver_assure['CODE_OGD_PRESTATIONS_PROV'],0,  $utilisateur_existe['ID_UTILISATEUR']);
                                        if($insertion['status'] == true) {
                                            $succes++;
                                        }else {
                                            $echec++;
                                        }
                                    }
                                }
                                if($succes != 0 && $echec == 0) {
                                    $json = array(
                                        'status' => true
                                    );
									$reponse = $json;
                                }else {
                                    $json = array(
                                        'status' => false
                                    );
									$reponse = $json;
                                }
                            }else {
                                $json = array(
                                    'status' => false,
                                    'message' => "UNE DEMANDE A DEJA ETE TRANSMISE"
                                );
								$reponse = $json;
                            }
                        }*/
                        $body_actes = array();
                        for ($i = 1; $i <= 3; $i++) {
                            $acte = 'acte_'.$i;
                            $code_acte = trim($_POST[$acte]);
                            if($code_acte){
                                $body_actes[] = array(
                                   "code" =>$code_acte,
                                    "motif"=>$motif
                                );
                            }
                        }

                        $client = new Client([
                            'timeout' => 60,
                            'verify' => false
                        ]);
                        $headers = [
                            'Authorization' => 'Bearer 715|WTX2uVR850ZKYtc3auj9oCNK1UXPemWcqHisR1C6e4f44cbb',
                            'accept' => 'application/json',
                            'Content-Type' => 'application/json',
                        ];

                        $body = json_encode([
                            "code_etablissement" => "$code_ets",
                            "numero_secu" => "$num_secu",
                            "actes" =>$body_actes
                        ]);
                        $request = new Request('POST', 'https://api.ipscnam.ci:3128/api/prestations/ententes-prealables', $headers, $body);

                        try{
                            $res = $client->sendAsync($request)->wait();
                            $return = json_decode($res->getBody());

                            if(isset($return->numero)){
                                $json = array(
                                    'status' => true,
                                    'num_ep' => $return->numero
                                );
                            }
                            else{
                                $json = array(
                                    'status' => $return['success'],
                                    'message' => "VOTRE DEMANDE A ECHOUE. PRIERE CONTACTER LE SUPPORT.",
                                    'numero'=>null
                                );
                            }
                        }catch (\Exception $e){
                            //$json = ;
                            $json = array(
                                'status' => false,
                                'status2' => $e->getMessage(),
                                'message' => "UNE ERREUR EST SURVENUE LORS DE VOTRE DEMANDE. PRIERE CONTACTER LE SUPPORT."
                            );
                        }
                        //var_dump(array($body,$reponse ));exit();
                        /*if($reponse['status'] == true) {
                            $d = $UTILISATEURS->trouver_utilisateur_ogd($trouver_assure['CODE_OGD_PRESTATIONS_PROV']);
                            $message = '		
                                 <html>
                                <head><meta charset="=\'UTF-8\'/"></head>
                                <body>
                                    <p align="center"><img src="'.IMAGES.'logo_cnam.png" width="100" alt="Logo CNAM" /></p>
                                    <p>Monsieur / Madame</p>
                                    <p>Nous venons par la présente vous informer qu\'une demande d\'entente préalable: <b>EP N° '.$reponse['numero'].'</b> a été émise depuis le centre: <b>'.$etablissement['RAISON_SOCIALE'].'</b>.<br />Nous vous invitons à vous connecter à votre interface pour traiter la demande</p>
                                    <p>Pour tout complément d\'information,  prière de contacter l\'agent <b>'.$utilisateur_existe['NOM'].' '.$utilisateur_existe['PRENOM'].'</b> à l\'adresse: <b>'.$utilisateur_existe['EMAIL'].'</b>.</p><br />
                                    <p>Cordialement</p>
                                   
                                </body>
                                </html>
                             ';
                            $mail = new PHPMailer\PHPMailer\PHPMailer();
                            //$mail = new PHPMailer(true);
                            $mail->CharSet = 'UTF-8';
                            $mail->isSMTP();                                      // Set mailer to use SMTP
                            $mail->Host = '172.18.3.13';  // Specify main and backup SMTP servers
                            //$mail->SMTPAuth = true;                               // Enable SMTP authentication
                            $mail->Username = '';                 // SMTP username
                            $mail->Password = '';                           // SMTP password
                            //$mail->SMTPSecure = 'TLS';                            // Enable TLS encryption, `ssl` also accepted
                            $mail->Port = 25;                                   // TCP port to connect to

                            $mail->setFrom('support@ipscnam.ci', 'IPSCNAM-CMU');
                            foreach ($d as $demandeur) {
                                $mail->addAddress($demandeur['EMAIL'], $demandeur['NOM'].' '.$demandeur['PRENOM']);     // Add a recipient
                            }
                            $mail->addReplyTo('support@ipscnam.ci', 'IPSCNAM-CMU');
                            //$mail->addCC('');
                            //$mail->addBCC('');

                            //$mail->addAttachment('');         // Add attachments
                            $mail->isHTML(true);                                  // Set email format to HTML

                            $mail->Subject = 'DEMANDE ENTENTE PREALABLE N° '.$reponse['numero'];
                            $mail->Body    = $message;
                            $mail->AltBody = '';

                            if(!$mail->send()) {
                                $json = array(
                                    'status' => false,
                                    'message' => $mail->ErrorInfo
                                );
                            } else {
                                $json = array(
                                    'status' => true,
                                    'num_ep' => $reponse['numero']
                                );
                            }

                        }*/

                    }else{
                        $json = array(
                            'status' => false,
                            'message' => 'CET ASSURE NE SE TROUVE PAS DANS LE SYSTEME. PRIERE CONTACTER LE SUPPORT.'
                        );
                    }
                }else{
                    $json = array(
                        'status' => false,
                        'message' => 'LES DONNEES SAISIES SONT INCORRECTES.'
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
