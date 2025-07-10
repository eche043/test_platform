<?php
    require_once '../../../Classes/UTILISATEURS.php';
    require_once '../../../Classes/RECRUTEMENT.php';
    $RECRUTEMENT = new RECRUTEMENT();

    $telephone_agac = $_POST['telephone'];
    $trouver_agac = $RECRUTEMENT->trouver_agac($telephone_agac);

    if($trouver_agac){
        $_SESSION['id_compte'] = $trouver_agac['ID_COMPTE'];
        
        //$code_otp = rand(1000, 9999);
        //$date_debut = date('Y-m-d H:i:s');
        //$message = "CHER(E) AGENT D'ACCUEIL CMU, VOTRE CODE DE CONNECTION EST LE SUVANT: {$code_otp}.";
        //$update_fin_otp = $RECRUTEMENT->update_date_fin_otp($date_debut,$trouver_agac['ID_COMPTE']);
        //if($update_fin_otp["status"] == "success"){
            //$envoi_sms_code_otp = $RECRUTEMENT->envoi_sms("auth","I",$telephone_agac,$message);

            //$save_otp =$RECRUTEMENT->editer_code_otp($trouver_agac['ID_COMPTE'],$code_otp,$date_debut,null);
           /* if($save_otp){
                $_SESSION['id_compte'] = $trouver_agac['ID_COMPTE'];
                $json = array(
                    'status' => "success",
                    'message' => "Veuillez confirmer la connection avec le mot de passe que nous venons de vous envoyer par SMS",
                );
            }*/
        /*}else{
            $json = array(
                'status' => 'false',
                'message' => "Une erreur est survenu lors de la vérification.",
            );
        }*/

        $json = array(
            'status' => "success",
            'message' => "Connexion en cours...",
        );

    }else{
        $json = array(
            'status' => 'false',
            'message' => "Le numéro de téléphone renseigné est inconnu de nos fichiers.",
        );
    }

    echo json_encode($json);