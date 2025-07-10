<?php
    require_once '../../../Classes/UTILISATEURS.php';
    require_once '../../../Classes/RECRUTEMENT.php';
    $RECRUTEMENT = new RECRUTEMENT();
    //$code_otp = $_POST['code_otp'];
    $id_compte =  $_SESSION['id_compte'] ;
    $date_fin = date('Y-m-d H:i:s');
    //$connection = $RECRUTEMENT->connection_otp($id_compte,$code_otp);

    $maj_statut_agac = $RECRUTEMENT->update_statut_compte_agac("1",$id_compte);
    if($maj_statut_agac){
        $json = array(
            'status' => "success",
            'id_agac' => $id_compte,
            'message' => "Connection en Cours...",
        );
    }

    /*if($connection){
        $maj_date_creation = $RECRUTEMENT->editer_code_otp($connection['ID_COMPTE'],$code_otp,null,$date_fin);
        if($maj_date_creation['status'] == "success"){
            $maj_statut_agac = $RECRUTEMENT->update_statut_compte_agac("1",$id_compte);
            if($maj_date_creation){
                $json = array(
                    'status' => "success",
                    'id_agac' => $id_compte,
                    'message' => "Connection en Cours...",
                );
            }
        }else{
            $json = array(
                'status' => "false",
                'message' => "Erreur rencontrée lors de la mise a jour de la date fin du code OTP",
            );
        }

    }else{
        $json = array(
            'status' => "false",
            'message' => "Le CODE RENSEIGNÉ EST INCORRECT.MERCI DE VERIFIER",
        );
    }*/


    echo json_encode($json);
