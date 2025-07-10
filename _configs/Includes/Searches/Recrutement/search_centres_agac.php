<?php
    require_once '../../../Classes/UTILISATEURS.php';
    require_once '../../../Classes/RECRUTEMENT.php';
    $RECRUTEMENT = new RECRUTEMENT();
    $id_compte = $_POST['id_compte'];
    $telephone = $RECRUTEMENT->trouver_telephone_compte($id_compte);
    if($telephone){
        $trouver_infos_centres = $RECRUTEMENT->trouver_centre_agac($telephone['NUMERO_TELEPHONE']);
        $json = array(
            'status' => 'success',
            'localite' => $trouver_infos_centres['LOCALITE'],
            'centre' => $trouver_infos_centres['STRUCTURE_SANITAIRE'],
        );
    }else{
        $json = array(
            'status' => 'false',
            'message' => 'Erreur lors de la récuperation du numéro de télpéphone',
        );
    }
    echo json_encode($json);