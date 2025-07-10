<?php
    require_once '../../../Classes/UTILISATEURS.php';
    require_once '../../../Classes/RECRUTEMENT.php';
    $RECRUTEMENT = new RECRUTEMENT();
    $secu = $_POST["num_secu"];
    $assure = $RECRUTEMENT->trouver_numero_secu($secu);
    //echo $assure['DATE_NAISSANCE'];
    //echo date('Y-m-d',strtotime($assure['DATE_NAISSANCE']));
    if($assure){
        $libelle_sexe = $RECRUTEMENT->trouver_sexe($assure['SEXE']);
    }
    if(!$assure){
        $json = ['status' => "false"];
    }else{
        $json = [
            'status' => "success",
            'nom' => $assure['NOM'],
            'prenoms' => $assure['PRENOM'],
            'date_naissance' => date('Y-m-d',strtotime($assure['DATE_NAISSANCE'])),
            'lieu_naissance' => $assure['NAISSANCE_NOM_LIEU_DIT'],
            'code_sexe' => $assure['SEXE'],
            'libelle_sexe' => $libelle_sexe['LIBELLE'],
        ];
    }

    echo json_encode($json);
