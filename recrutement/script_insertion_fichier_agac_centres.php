<?php
    include "../_configs/Classes/UTILISATEURS.php";
    include "../_configs/Includes/Titles.php";

    //$file = URL . "IMPORTS/RECRUTEMENT_AGAC/base_centres_phase_6.csv";
    //$file = URL . "IMPORTS/RECRUTEMENT_AGAC/VAGUE_1_CENTRES.csv";
    $file = URL . "IMPORTS/RECRUTEMENT_AGAC/VAGUE_1_2_3_CENTRES.csv";
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

        $nombre_ligne = file($file);
        $nb_total_agac = count($nombre_ligne) - 1; // -1 car $ligne commence à 1

        $json = []; // Initialisation du tableau de retour

        while (($data = fgetcsv($handle, 0, ";")) !== false) {

            if (count($data) < 8) {
                $json[$ligne] = array(
                    'status' => false,
                    'message' => "Ligne $ligne : nombre de colonnes incorrect."
                );
                $echec++;
                $ligne++;
                continue;
            }

            $nom = trim($data[0]);
            $prenoms = trim($data[1]);
            $localite = trim($data[2]);
            $structure_sanitaire = trim($data[3]);
            $numero_telephone = substr(str_replace(' ', '', trim($data[4])), 0, 10);
            $centre_coordination_rattache = trim($data[5]);
            $nom_prenoms_coordinateurs = trim($data[6]);
            $numero_telephone_coordinateurs = substr(str_replace(' ', '', trim($data[7])), 0, 10);;


            $import_agac = $RECRUTEMENT->editer_fichier_agac_centre(
                $nom,
                $prenoms,
                $localite,
                $structure_sanitaire,
                $numero_telephone,
                $centre_coordination_rattache,
                $nom_prenoms_coordinateurs,
                $numero_telephone_coordinateurs
            );

            // Enregistrement du résultat
            $json[$ligne] = array(
                'status' => $import_agac['status'],
                'message' => $import_agac['message']
            );

            // Comptabilisation des succès et des échecs
            if ($import_agac['status']) {
                $success++;
            } else {
                $echec++;
            }

            $ligne++;
        }
        fclose($handle); // Fermeture du fichier après lecture
    }

    // Affichage des résultats d'importation
    echo json_encode(array(
        'total' => $nb_total_agac,
        'success' => $success,
        'echec' => $echec,
        'details' => $json
    ));

?>
