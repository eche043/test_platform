<?php
    include "../_configs/Classes/UTILISATEURS.php";
    include "../_configs/Includes/Titles.php"; 
    //$file = URL . "IMPORTS/RECRUTEMENT_AGAC/base_compte_test.csv";
    //$file = URL . "IMPORTS/RECRUTEMENT_AGAC/base_compte_phase_5.csv";
    //$file = URL . "IMPORTS/RECRUTEMENT_AGAC/base_compte_phase_6-1.csv";
    //$file = URL . "IMPORTS/RECRUTEMENT_AGAC/Base_test_RH.csv";
    //$file = URL . "IMPORTS/RECRUTEMENT_AGAC/VAGUE_1_2_3_comptes.csv";
    //$file = URL . "IMPORTS/RECRUTEMENT_AGAC/VAGUE_2_comptes.csv";
    //$file = URL . "IMPORTS/RECRUTEMENT_AGAC/VAGUE_3_comptes.csv";
    $file = URL . "IMPORTS/RECRUTEMENT_AGAC/VAGUE_1_2_3_comptes.csv";
    $filename = basename($file);
    $extension = strrchr($file, '.');

    if ($extension == ".csv") {
        $mode = 'r';
        $handle = fopen($file, $mode);

        if ($handle === false) {
            die("Erreur lors de l'ouverture du fichier.");
        }

        require_once '../_configs/Classes/RECRUTEMENT.php';
        $RECRUTEMENT = new RECRUTEMENT();
        /*$a = $RECRUTEMENT->trouver_telephone_agac("0777942862");
        var_dump($a);*/
        $ligne = 1;
        $success = 0;
        $echec = 0;
        $json = []; // Initialisation du tableau JSON

        $nombre_ligne = file($file);
        $nb_total_agac = count($nombre_ligne) - $ligne;

        while ($data = fgetcsv($handle, 0, ";")) {
            $nom = trim($data[0]);
            $prenoms = trim($data[1]);
            $statut = "0";
            $telephone = substr(str_replace(' ', '', trim($data[2])), 0, 10);
            //var_dump($telephone);
            $import_agac = $RECRUTEMENT->editer_fichier_agac_comptes($telephone, $nom, $prenoms, $statut);
            if ($import_agac && is_array($import_agac)) {
                $json[$ligne] = array(
                    'status' => $import_agac['status'] ?? 'error',
                    'message' => $import_agac['message'] ?? 'Erreur lors de l\'importation.'
                );
                $success++;
            } else {
                $json[$ligne] = array(
                    'status' => 'error',
                    'message' => 'Échec de l\'importation pour la ligne ' . $ligne
                );
                $echec++;
            }
            $ligne++;
        }

        fclose($handle);
        echo json_encode($json);
    } else {
        echo "Le fichier n'est pas au format CSV.";
    }
