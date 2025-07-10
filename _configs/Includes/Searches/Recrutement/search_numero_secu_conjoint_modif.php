<?php
    require_once '../../../Classes/UTILISATEURS.php';
    require_once '../../../Classes/RECRUTEMENT.php';
    $RECRUTEMENT = new RECRUTEMENT();
    $secu = $_POST["num_secu"];
    $assure = $RECRUTEMENT->trouver_numero_secu($secu);
    //$libelle_sexe = $RECRUTEMENT->trouver_sexe($assure['SEXE']);
    //$civilite = $RECRUTEMENT->trouver_civilite($assure['CIVILITE']);
    if(!$assure){
        $json = [
            'status' => "false",
            'message' => "Le numéro de sécurité sociale n'existe pas",
            ];
    }else{
        $json = [
            'status' => "success",
            'nom' => $assure['NOM'],
            'prenoms' => $assure['PRENOM'],
            'date_naissance' => date('d-m-Y',strtotime($assure['DATE_NAISSANCE'])),
            'lieu_naissance' => $assure['NAISSANCE_NOM_LIEU_DIT'],
            'code_sexe' => $assure['SEXE'],
        ];
    }

    echo json_encode($json);
