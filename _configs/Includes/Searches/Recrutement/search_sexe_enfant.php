<?php
    require_once '../../../Classes/UTILISATEURS.php';
    require_once '../../../Classes/RECRUTEMENT.php';
    $RECRUTEMENT = new RECRUTEMENT();
    $sexes = $RECRUTEMENT->lister_sexe_enfant();
    if ($sexes) {
        foreach ($sexes as $sexe) {
            $json[] = [
                'id' => $sexe['CODE'],
                'libelle' => $sexe['LIBELLE']
            ];
        }
    }
    echo json_encode($json);
