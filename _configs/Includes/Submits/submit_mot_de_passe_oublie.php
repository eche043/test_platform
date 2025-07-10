<?php
/**
 * Created by PhpStorm.
 * User: mohamed.kone
 * Date: 05/02/2020
 * Time: 10:03
 */

$email = trim($_POST['email']);

if(empty($email)){
    $json = array(
        'status' => true,
        'message' => 'L\'<strong>Adresse Email</strong> n\'existe pas<strong>'
    );
}else{
    require_once '../../Classes/UTILISATEURS.php';
    $UTILISATEUR = new UTILISATEURS();
    $mot_de_passe = $UTILISATEUR->reinitialiser_mot_de_passe($email);
    if($mot_de_passe['status'] == true){

        require("../../../vendor/phpmailer/phpmailer/src/PHPMailer.php");
        require("../../../vendor/phpmailer/phpmailer/src/SMTP.php");

        if(date('A',time()) == 'AM'){
            $salutation = 'Bonjour ';
        }else{
            $salutation = 'Bonsoir ';
        }
        $message = '                        
                     <!DOCTYPE html>
                     <html>
                    <head><meta charset="=\'UTF-8\'/"></head>
                    <body>
                        <p align="center"><img width="80" alt="Logo CNAM" src="'.IMAGES.'logo_cnam.png" /></p>
                        <h3>'.$salutation.$mot_de_passe['prenom'].'</h3>
                        <p>Nous venons par la présente vous informer de la modification de votre mot de passe.
                        <br />Votre nouveau mot de passe est le suivant:
                        <br /><b>Mot de passe: '.$mot_de_passe['mot_de_passe'].'</b><br /><br />
                        <br />Prière de cliquer sur le lien suivant pour vous connecter à votre compte.
                        <br /><a target="_blank" href="'.URL.'">'.URL.'</a>
                        </p>
                        <b>Cordialement Support CNAM</b>
                    </body>
                    </html>';

        $mail = new PHPMailer\PHPMailer\PHPMailer();
        $mail->CharSet = 'UTF-8';
        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = '172.18.3.13';  // Specify main and backup SMTP servers
        //$mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = '';                 // SMTP username
        $mail->Password = '';                           // SMTP password
        //$mail->SMTPSecure = 'TLS';                            // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 25;                                   // TCP port to connect to

        $mail->setFrom('support@ipscnam.ci', 'IPSCNAM-CMU');
        $mail->addAddress($email);     // Add a recipient
        $mail->addReplyTo('support@ipscnam.ci', 'IPSCNAM-CMU');
        //$mail->addCC('');
        //$mail->addBCC('');

        //$mail->addAttachment('');         // Add attachments
        $mail->isHTML(true);                                  // Set email format to HTML

        $mail->Subject = 'Réinitialisation de compte ECMU';
        $mail->Body    = $message;
        $mail->AltBody = '';

        if(!$mail->send()) {
            $json = array(
                'status' => 'failed',
                'message' => $mail->ErrorInfo
            );
        } else {
            $json = array(
                'status' => true,
                'message' =>$mot_de_passe['message']
            );
        }
    }else{
        $json =array(
            'status' => false,
            'message' => 'UNE ERREUR EST SURVENUE LORS DE LA MODIFICATION DE VOTRE MOT DE PASSE. VEUILLEZ REESSAYER; SI L\'ERREUR PERSISTE, PRIERE NOUS CONTACTER VIA L\'ADRESSE EMAIL: support@ipscnam.ci'
        );
    }
}
echo json_encode($json);