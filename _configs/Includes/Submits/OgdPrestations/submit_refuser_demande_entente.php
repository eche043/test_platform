<?php

require_once '../../../Classes/UTILISATEURS.php';
if(isset($_SESSION['ECMU_USER_ID']) || !empty($_SESSION['ECMU_USER_ID'])) {
    $UTILISATEURS = new UTILISATEURS();
    $user = $UTILISATEURS->trouver($_SESSION['ECMU_USER_ID'], NULL, NULL);
    if (!empty($user['ID_UTILISATEUR'])) {

        if(isset($_POST['num_ep']) && !empty($_POST['num_ep'])) {
            $num_ep = $_POST['num_ep'];
            if(!empty($_POST['motif_refus'])){
                $motif_refus = $_POST['motif_refus'];

                require_once "../../../Classes/ENTENTESPREALABLES.php";
                require_once "../../../Classes/ASSURES.php";
                require_once "../../../Classes/ETABLISSEMENTSSANTE.php";

                require("../../../../vendor/phpmailer/phpmailer/src/PHPMailer.php");
                require("../../../../vendor/phpmailer/phpmailer/src/SMTP.php");

                $ENTENTESPREALABLES = new ENTENTESPREALABLES();
                $ETABLISSEMENTSSANTE = new ETABLISSEMENTSSANTE();
                $ASSURES = new ASSURES();

                $entente = $ENTENTESPREALABLES->trouver_entente_prealable($num_ep);
                if(count($entente) == 0) {
                    $json = array(
                        'status' => false,
                        'message'=> 'LE N° DE L\'ENTENTE PREALABLE SEMBLE NE PAS ETRE CORRECTE.<br />PRIERE DE CONTACTER L\'ADMINISTRATEUR'
                    );
                }else {
                    $demandeur = $UTILISATEURS->trouver($entente['USER_REG'],NULL,NULL);
                    if(count($demandeur) == 0) {
                        $json = array(
                            'status' => false,
                            'message' => 'LE DEMANDEUR DE L\'ENTENTE PREALABLE SEMBLE NE PAS EXISTER DANS LE SYSTEME.<br />PRIERE DE CONTACTER L\'ADMINISTRATEUR.'
                        );
                    }else {
                        $assure = $ASSURES->trouver($entente['NUM_SECU']);

                        if(count($assure) == 0) {
                            $json = array(
                                'status' => false,
                                'message' => 'L\'ASSURE DE L\'ENTENTE PREALABLE SEMBLE NE PAS EXISTER DANS LE SYSTEME.<br />PRIERE DE CONTACTER L\'ADMINISTRATEUR'
                            );
                        }else {

                        $message = '
                            
                        <html>
                        <head><meta charset="=\'UTF-8\'/"></head>
                        <body>
                            <p align="center"><img src="'.IMAGES.'logo-cnam.jpg" width="100" alt="Logo CNAM" /></p>
                            <p>Monsieur / Madame</p>
                            <p>Nous venons par la présente vous informer que la demande EP N° <b>'.$num_ep.'</b> a été refusée.<br />.</p>
                             <p>Le motif du rejet est :"'.$motif_refus.'"</p>
                            <p>Pour tout complément d\'information,  prière de contacter l\'agent <b>'.$user['NOM'].' '.$user['PRENOM'].'</b> à l\'adresse: <b>'.$user['EMAIL'].'</b>.</p><br />
                            <p>Cordialement</p>
                           
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
                            $mail->Port = 25;                                   // TCP port to connect to

                            $mail->setFrom('extranet@ipscnam.com', 'ECMU ENTENTE PREALABLE');
                            $mail->addAddress($demandeur['EMAIL']);     // Add a recipient
                            $mail->addReplyTo('extranet@ipscnam.com', 'ECMU MANAGER');
                            $mail->addCC('extranet@ipscnam.com', 'ECMU MANAGER');
                            //$mail->addBCC('');

                            //$mail->addAttachment('');         // Add attachments
                            $mail->isHTML(true);                                  // Set email format to HTML

                            $mail->Subject = 'REFUS ENTENTE PREALABLE N° '.$num_ep;
                            $mail->Body    = $message;
                            $mail->AltBody = '';

                            if(!$mail->send()) {
                                $json = array(
                                    'status' => false,
                                    'message' => $mail->ErrorInfo
                                );
                            } else {
                                $id_useur = $user['ID_UTILISATEUR'];
                                $ep = $ENTENTESPREALABLES->refus_entente_prealable($num_ep, $motif_refus,2,$id_useur);

                                $json = array(
                                    'status' => true
                                );

                            }
                        }
                    }
                }
            }else{
                $json = array(
                    'status' => false,
                    'message' => "LE MOTIF N'A PAS ETE DEFINI."
                );
            }

        }else{
            $json = array(
                'status' => false,
                'message' => " LE NUMERO DE l'ENTENTE PREALABLE N'EST PAS DEFINIE. PRIERE VERIFIER LES PARAMETRES."
            );
        }


    }else{
        $json = array(
            'status' => false,
            'message' => " CET UTILISATEUR N\'EXISTE PAS. PRIERE VERIFIER VOS PARAMETRES."
        );
    }
}else{
    $json = array(
        'status' => false,
        'message' => " LA SESSION DE CET UTILISATEUR A ETE ARRETEE. PRIERE VOUS RECONNECTER AFIN DE CONTINUER L'OPERATION."
    );
}
echo json_encode($json);



