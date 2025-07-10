<?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        //print_r($_POST);

        require_once '../../../Classes/UTILISATEURS.php';
        require_once '../../../Classes/RECRUTEMENT.php';
        $RECRUTEMENT = new RECRUTEMENT();

        $nom_pere = strtoupper($_POST['nom_pere_modif_input']);
        $prenoms_pere = strtoupper($_POST['prenoms_pere_modif_input']);
        $nom_mere = strtoupper($_POST['nom_mere_modif_input']);
        $prenoms_mere = strtoupper($_POST["prenoms_mere_modif_input"]);
        if($_POST['date_naissance_conjoint_modif_input']){$date_naissance = date('Y-m-d', strtotime($_POST['date_naissance_conjoint_modif_input']));}else{$date_naissance=null;}
        $nom_conjoint = strtoupper($_POST['nom_conjoint_modif_input']);
        $prenoms_conjoint = strtoupper($_POST['prenoms_conjoint_modif_input']);
        $profession_conjoint = $_POST['profession_conjoint_modif_input'];
        $num_secu_conjoint = $_POST['num_secu_conjoint_modif_input'];
        $nom_personne_urgence = strtoupper($_POST['nom_personne_urgence_modif_input']);
        $telephone_personne_urgence = strtoupper($_POST['telephone_personne_urgence_modif_input']);
        if(date('Y-m-d', strtotime($_POST['date_naissance_pere_modif_input']))){$date_naissance_pere = date('Y-m-d', strtotime($_POST['date_naissance_pere_modif_input']));}else{$date_naissance_pere=null;}
        if(date('Y-m-d', strtotime($_POST['date_naissance_mere_modif_input']))){$date_naissance_mere = date('Y-m-d', strtotime($_POST['date_naissance_mere_modif_input']));}else{$date_naissance_mere=null;}
        $nom_personne_urgence2 = strtoupper($_POST['nom_personne_urgence_deux_modif_input']);
        $telephone_personne_urgence2 = strtoupper($_POST['telephone_personne_urgence_deux_modif_input']);
        $nom_personne_urgence3 = strtoupper($_POST['nom_personne_urgence_trois_modif_input']);
        $telephone_personne_urgence3 = strtoupper($_POST['telephone_personne_urgence_trois_modif_input']);

        $id_compte = $_SESSION['id_compte'];
        $verifier_tb_info_famille = $RECRUTEMENT->trouver_infos_famille($_SESSION['id_compte']);
        if($verifier_tb_info_famille){
           $modifier_infos_famille = $RECRUTEMENT->modifier_info_famille($nom_pere,$prenoms_pere,$nom_mere,$prenoms_mere,$date_naissance,$nom_conjoint,$prenoms_conjoint,$profession_conjoint,$num_secu_conjoint,$nom_personne_urgence,$telephone_personne_urgence,$date_naissance_pere,$date_naissance_mere,$nom_personne_urgence2,$telephone_personne_urgence2,$nom_personne_urgence3,$telephone_personne_urgence3,$id_compte);

            if($modifier_infos_famille['status'] === 'success'){
                $json = array(
                    'status' => 'success',
                    'message' => 'information modifiées avec succès.'
                );
            }
        }
        else{
            $editer_agac_famille = $RECRUTEMENT->editer_infos_famille($id_compte,$nom_pere,$prenoms_pere,$nom_mere,$prenoms_mere,$date_naissance,$nom_conjoint,$prenoms_conjoint,$profession_conjoint,$num_secu_conjoint,$nom_personne_urgence,$telephone_personne_urgence,$date_naissance_pere,$date_naissance_mere,$nom_personne_urgence2,$telephone_personne_urgence2,$nom_personne_urgence3,$telephone_personne_urgence3);
            if($editer_agac_famille['status'] === "success"){
                $json = array(
                    'status' => "success",
                    'message' =>  $editer_agac_famille["message"]
                );
            }
        }


    } else {
        echo json_encode(['status' => 'error', 'message' => 'Méthode non autorisée']);
    }

    echo json_encode($json);