<?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        //print_r($_POST);

        require_once '../../../Classes/UTILISATEURS.php';
        require_once '../../../Classes/RECRUTEMENT.php';
        $RECRUTEMENT = new RECRUTEMENT();


        $id_compte = $_POST["id_compte_et_modif_4"];
        //$code_centre = $_POST['code_centre_modif_input'];
        //$date_embauche = date('Y-m-d', strtotime($_POST['date_embauche_modif_input']));
        //$date_embauche = $_POST['date_embauche_input'];

        /*$verifier_tb_info_affectation = $RECRUTEMENT->trouver_infos_affectation($id_compte);
        if($verifier_tb_info_affectation){
            $editer_affectation = $RECRUTEMENT->modifier_affectation($id_compte);
            if($editer_affectation['status'] === "success"){
                $json = array(
                    'status' => "success",
                    'message' =>  $editer_affectation["message"],
                    'id_agac' =>  $id_compte,
                );
            }
        }*/
        $json = array(
            'status' => "success",
            'message' =>  "Les Informations ont été enregistrées avec succès...Veuillez trouver ci-dessous le résumé de vos informations saisies.",
            'id_agac' =>  $id_compte,
        );

    } else {
        echo json_encode(['status' => 'error', 'message' => 'Méthode non autorisée']);
    }

    echo json_encode($json);