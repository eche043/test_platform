<?php

if(isset($_POST['num_secu']) && isset($_POST['email']) && isset($_POST['num_telephone'])){
    $num_secu = htmlentities(trim($_POST['num_secu']));
    $email = htmlentities(trim($_POST['email']));
    $email = htmlentities(trim($_POST['email']));
    $num_telephone = htmlentities(trim($_POST['num_telephone']));
    require_once '../../Classes/UTILISATEURS.php';

    $UTILISATEURS = new UTILISATEURS();

    $creation = $UTILISATEURS->creer($num_secu, $email,$num_telephone);
    if($creation['status']== true){

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
                        <h3>'.$salutation.$creation['prenom'].'</h3>
                        <p>Nous venons par la présente vous informer de la création de votre compte sur la plateforme d\'échange collaborative pour la Couverture Maladie Universelle.
                        <br />Vos identifiants sont les suivants:
                        <br /><b>Nom utilisateur: '.$num_secu.'</b>
                        <br /><b>Mot de passe: '.$creation['mot_de_passe'].'</b><br /><br />
                        <br />Prière de cliquer sur le lien suivant pour vous connecter à votre compte.
                        <br /><a target="_blank" href="'.URL.'">'.URL.'</a>
                        </p>
                        <b>Cordialement Support CNAM</b>
                    </body>
                    </html>
                 ';

        $mail =new PHPMailer\PHPMailer\PHPMailer();
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

        $mail->Subject = 'Création de compte ECMU';
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
                'message' =>$creation['message']
            );
        }
    }else{
        $json =array(
            'status' => false,
            'message' => $creation['message']
        );
    }
}else{
    $json =array(
        'status' => false,
        'message' => 'VEUILLEZ RENSEIGNER TOUS LES CHAMPS SVP.'
    );
}
echo json_encode($json);
