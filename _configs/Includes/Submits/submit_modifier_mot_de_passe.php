<?php
require_once '../../Classes/UTILISATEURS.php';

if(isset($_SESSION['ECMU_USER_ID']) || !empty($_SESSION['ECMU_USER_ID'])) {
    $UTILISATEURS = new UTILISATEURS();
    $user = $UTILISATEURS->trouver($_SESSION['ECMU_USER_ID'],NULL,NULL);
//    var_dump($user);
    if(empty($user['ID_UTILISATEUR'])) {
        session_destroy();
        echo '<script>window.location.href="'.URL.'connexion.php"</script>';
    }else {
        $modules = array_diff(explode(';',stream_get_contents($user['PROFIL'],-1)),array(""));
        $nb_modules = count($modules);
        if($nb_modules == 0) {
            session_destroy();
            echo '<script>window.location.href="'.URL.'connexion.php"</script>';
        }else{

            $user = trim($user['ID_UTILISATEUR']);
            $ancien_mot_de_passe = sha1($_POST['ancien_mot_de_passe']);
            $mot_de_passe = $_POST['mot_de_passe'];
            $mot_de_passe2 = $_POST['mot_de_passe2'];


            require("../../../vendor/phpmailer/phpmailer/src/PHPMailer.php");
            require("../../../vendor/phpmailer/phpmailer/src/SMTP.php");

            if( !empty($ancien_mot_de_passe) && !empty($mot_de_passe) && !empty($mot_de_passe2)) {
                $administrateur =  $UTILISATEURS->trouver($user,null,null);
                if($ancien_mot_de_passe == $administrateur['MOT_DE_PASSE']){

                    $email = $administrateur['EMAIL'];
                    $nom = $administrateur['NOM'];
                    $prenom = $administrateur['PRENOM'];

                    $message = '
                            <!DOCTYPE html>
                             <html>
                            <head><meta charset="=\'UTF-8\'/"></head>
                            <body>
                                <p align="center"><img width="80" alt="Logo CNAM" src="'.IMAGES.'logo_cnam.png" /></p>
                                <h3>Bonjour '.$nom.' '.$prenom.'</h3>
                                <p>Vous venez de proceder à la modification du mot de passe de votre compte sur la <b>plateforme ECMU</b>.
                                <br /><b>Ce n’était pas vous ?</b>Veuillez contacter immédiatement l\'administrateur <br /><br />
                                <br />Prière de cliquer sur le lien suivant pour vous connecter à votre compte.
                                <br /><a target="_blank" href="'.URL.'">'.URL.'</a>
                                </p>
                                <b>Cordialement IPSCNAM</b>
                            </body>
                            </html>
                        ';
                    $mail = new PHPMailer\PHPMailer\PHPMailer();
                    $mail->CharSet = 'UTF-8';
                    $mail->isSMTP();                                      // Set mailer to use SMTP
                    $mail->Host = '172.18.3.13';  // Specify main and backup SMTP servers
                    //$mail->SMTPAuth = true;                               // Enable SMTP authentication
                    $mail->Username = '';                 // SMTP username
                    $mail->Password = '';                           // SMTP password
                    //$mail->SMTPSecure = 'TLS';                            // Enable TLS encryption, `ssl` also accepted
                    $mail->Port = 25;

                    $mail->setFrom('support@ipscnam.com', 'IPSCNAM-CMU');
                    $mail->addAddress($email);     // Add a recipient
                    $mail->addReplyTo('support@ipscnam.com', 'IPSCNAM-CMU');
                    $mail->addCC('');
                    $mail->addBCC('');

//                    $mail->addAttachment('');         // Add attachments
                    $mail->isHTML(true);                                  // Set email format to HTML


                    $mail->Subject = 'Modification du mot de passe de compte ECMU';
                    $mail->Body    = $message;
                    $mail->AltBody = '';
                    if(!$mail->send()) {
                        $json = array(
                            'status' => false,
                            'message' => 'Le message ne peut être envoyé.=>'.$mail->ErrorInfo
                        );
                    } else {
                        $a = $UTILISATEURS->reinitialisation_mot_de_passe($user,$mot_de_passe,$user);
                        $json = array(
                            'status' => $a['status'],
                            'message' => $a['message']
                        );
                    }
                }else{
                    $json = array(
                        'status' => false,
                        'message' => '<b>ERREUR: L\'ancien mot de passe entré est erronné.</b>'
                    );
                }

            }else{
                $json = array(
                    'status' => false,
                    'message' => '<b>ERREUR: Veuillez vérifier les informtions et contacter l\'administrateur.</b>'
                );
            }
            echo json_encode($json);


        }
    }

}else{
    session_destroy();
    echo '<script>window.location.href="'.URL.'connexion.php"</script>';
}
?>
