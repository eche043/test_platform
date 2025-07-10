<?php
    include "../_configs/Classes/UTILISATEURS.php";
    include "../_configs/Includes/Titles.php";

    //$file = URL . "IMPORTS/RECRUTEMENT_AGAC/base_compte_test.csv";
    $file = URL . "IMPORTS/RECRUTEMENT_AGAC/base_300AGAC-.csv";
    $filename = basename($file);
    $extension = strrchr($file, '.');
    if ($extension == ".csv") {

        $mode = 'r';
        $handle = fopen($file, $mode);

        require_once '../_configs/Classes/RECRUTEMENT.php';
        $RECRUTEMENT = new RECRUTEMENT();

        $ligne = 1;
        $success = 0;
        $echec = 0;

        $nombre_ligne=file($file);
        $nb_total_agac =  count($nombre_ligne) - $ligne;

        while ($data = fgetcsv($handle, 0, ";")) {
                $nom[$ligne] = trim($data[0]);
                $prenoms[$ligne] = trim($data[1]);
                $localite[$ligne] = trim($data[2]);
                $centre[$ligne] = trim($data[3]);
                 $telephone[$ligne] = substr(str_replace(' ', '', trim($data[4])), 0, 10);

                //$import_agac = $RECRUTEMENT->editer_fichier_agac($telephone[$ligne],$nom[$ligne],$prenoms[$ligne],'0');
                $import_agac = $RECRUTEMENT->editer_fichier_agac_centre($nom[$ligne],$prenoms[$ligne],$localite[$ligne],$centre[$ligne],$telephone[$ligne]);

                $json[$ligne] = array(
                    'status' => $import_agac[$ligne]['status'],
                    'message' => $import_agac[$ligne]['message']
                );
        }
    }

?>
