<?php

require_once '../../../Classes/UTILISATEURS.php';
if(isset($_SESSION['ECMU_USER_ID']) || !empty($_SESSION['ECMU_USER_ID'])) {
    $UTILISATEURS = new UTILISATEURS();
    $user = $UTILISATEURS->trouver($_SESSION['ECMU_USER_ID'], NULL, NULL);
    if (!empty($user['ID_UTILISATEUR'])) {

        $date = date('Y-m-d H:i:s',time());
        if(isset($_POST['num_ep'])  && isset($_POST['type_ep'])){
            $num_ep = $_POST['num_ep'];
            $type_ep= $_POST['type_ep'];
            if($_POST['type_hosp']==""){$type_hosp =NULL;}else{$type_hosp = $_POST['type_hosp'];}

            if($type_ep=="EXP"){

                if($_POST['motif1']==""){$motif1=NULL;}else{$motif1= $_POST['motif1'];}
                if($_POST['code_acte1']==""){$code_acte1= NULL;}else{$code_acte1= $_POST['code_acte1'];}
                if(!isset($_POST['etat1'])){
                    $etat1 =NULL;
                }else{
                    $etat1 = $_POST['etat1'];
                }

                if($_POST['motif2']==""){$motif2= NULL;}else{$motif2= $_POST['motif2'];}
                if($_POST['code_acte2']==""){$code_acte2=NULL;}else{$code_acte2= $_POST['code_acte2'];}
                if(!isset($_POST['etat2'])){
                    $etat2 =NULL;
                }else{
                    $etat2 = $_POST['etat2'];

                }

                if($_POST['motif3']==""){$motif3= NULL;}else{$motif3= $_POST['motif3'];}
                if($_POST['code_acte3']==""){$code_acte3= NULL;}else{$code_acte3= $_POST['code_acte3'];}
                if(!isset($_POST['etat3'])){
                    $etat3 =NULL;
                }else{
                    $etat3 = $_POST['etat3'];
                }

                $texte_mail = '<p>Nous venons par la présente vous informer que la demande EP N° <b>'.$num_ep.'</b> a été traitée.<br>';
            }else{
                $texte_mail = '<p>Nous venons par la présente vous informer que la demande EP N° <b>'.$num_ep.'</b> a été validée.';
            }
        }

        if(!empty($num_ep)) {
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
                    'status' => 'failed',
                    'message'=> 'LE N° DE L\'ENTENTE PREALABLE SEMBLE NE PAS ETRE CORRECTE.<br />PRIERE DE CONTACTER L\'ADMINISTRATEUR'
                );
            }else {
                $demandeur = $UTILISATEURS->trouver($entente['USER_REG'],NULL,NULL);
                if(count($demandeur) == 0) {
                    $json = array(
                        'status' => 'failed',
                        'message' => 'LE DEMANDEUR DE L\'ENTENTE PREALABLE SEMBLE NE PAS EXISTER DANS LE SYSTEME.<br />PRIERE DE CONTACTER L\'ADMINISTRATEUR.'
                    );
                }else {
                    $assure = $ASSURES->trouver($entente['NUM_SECU']);

                    if(count($assure) == 0) {
                        $json = array(
                            'status' => 'failed',
                            'message' => 'L\'ASSURE DE L\'ENTENTE PREALABLE SEMBLE NE PAS EXISTER DANS LE SYSTEME.<br />PRIERE DE CONTACTER L\'ADMINISTRATEUR'
                        );
                    }else {
                        $message = '                        
                            <html>
                            <head><meta charset="=\'UTF-8\'/"></head>
                            <body>
                                <p align="center"><img src="'.IMAGES.'logo-cnam.jpg" width="100" alt="Logo CNAM" /></p>
                                <p>Monsieur / Madame</p>
                                '.$texte_mail.'Nous vous invitons à vous connecter à votre interface pour poursuivre la prise en charge de l\'assuré</p>
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

                        $mail->Subject = 'ACCEPTATION ENTENTE PREALABLE N° '.$num_ep;
                        $mail->Body    = $message;
                        $mail->AltBody = '';

                        if(!$mail->send()) {
                            $json = array(
                                'status' => false,
                                'message' => $mail->ErrorInfo
                            );
                        } else {
                            $id_useur = $user['ID_UTILISATEUR'];
                            if($type_ep=="EXP"){
                                if(!empty($code_acte1)){
                                    if($etat1==1){$date_validation=date('Y-m-d',strtotime($date));}else{$date_validation=NULL;}
                                    $up1 = $ENTENTESPREALABLES->validation_entente_prealable_type_exp_par_acte($num_ep,$motif1,$etat1,$code_acte1,$date_validation,$user['ID_UTILISATEUR']);

                                }

                                if(!empty($code_acte2)){
                                    if($etat2==1){$date_validation=date('Y-m-d',strtotime($date));}else{$date_validation=NULL;}
                                    $up2 = $ENTENTESPREALABLES->validation_entente_prealable_type_exp_par_acte($num_ep,$motif2,$etat2,$code_acte2,$date_validation,$id_useur);
                                }

                                if(!empty($code_acte3)){
                                    if($etat3==1){$date_validation=date('Y-m-d',strtotime($date));}else{$date_validation=NULL;}
                                    $up3 = $ENTENTESPREALABLES->validation_entente_prealable_type_exp_par_acte($num_ep,$motif3,$etat3,$code_acte3,$date_validation,$id_useur);
                                }

                                $json = array(
                                    'status' => true
                                );

                            }else if($type_ep=="HOS"){
                                $up = $ENTENTESPREALABLES->validation_entente_prealable_type_hosp($num_ep,1,$id_useur);

                                $json = array(
                                    'status' => true
                                );
                            }
                        }
                    }
                }
            }

        }else{
            $json = array(
                'status' => 'echec_ep_inexistante',
                'message' => " CET UTILISATEUR N\'EXISTE PAS."
            );
        }


    }else{
        $json = array(
            'status' => 'echec_demandeur_inexistant',
            'message' => " CET UTILISATEUR N\'EXISTE PAS."
        );
    }
}else{
    $json = array(
        'status' => 'echec_demandeur_inexistant',
        'message' => " CET UTILISATEUR N\'EST PAS DEFINI."
    );
}
echo json_encode($json);



