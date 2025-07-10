<?php
    require_once '../../../Classes/UTILISATEURS.php';
    require_once '../../../Classes/RECRUTEMENT.php';

    // Activer l'affichage des erreurs pour le débogage
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    $RECRUTEMENT = new RECRUTEMENT();

    // Récupérer les données POST envoyées depuis le frontend
    $id_compte = isset($_SESSION['id_compte']) ? $_SESSION['id_compte'] : '';
    $nombreEnfants = isset($_POST['nombre_enfant']) ? intval($_POST['nombre_enfant']) : 0;

    // Vérifier que les paramètres sont présents et valides
    if (!empty($id_compte) && $nombreEnfants > 0) {
        try {
            // Récupérer les informations des enfants depuis la base de données
            $enfants = $RECRUTEMENT->trouver_enfants($id_compte, $nombreEnfants);
            $sexes = $RECRUTEMENT->lister_sexe();
            $json = [];

            foreach ($enfants as $enfant) {
                $trouver_enfant_sexe = $RECRUTEMENT->trouver_libelle_sexe($enfant["SEXE"]);

                // Vérification des données
                $libelle_sexe = isset($trouver_enfant_sexe["LIBELLE"]) ? $trouver_enfant_sexe["LIBELLE"] : 'N/A';
                $date_naissance = !empty($enfant['DATE_NAISSANCE']) ? date('Y-m-d', strtotime($enfant['DATE_NAISSANCE'])) : 'Date invalide';

                // Déterminer le sexe alternatif
                $code_autre_sexe = ($enfant["SEXE"] == "F") ? "M" : "F";
                $libelle_autre_sexe = ($code_autre_sexe == "F") ? "FEMININ" : "MASCULIN";

                // Ajout au tableau JSON
                $json[] = [
                    'nom' => $enfant["NOM"],
                    'prenoms' => $enfant["PRENOMS"],
                    'sexe_enfant' => $enfant["SEXE"],
                    'date_naissance' => $date_naissance,
                    'lieu_naissance' => $enfant["LIEU_NAISSANCE"],
                    'numero_secu' => $enfant["NUMERO_SECU"],
                    'code_sexe' => $enfant["SEXE"],
                    'libelle_sexe' => $libelle_sexe,
                    'code_autre_sexe' => $code_autre_sexe,
                    'libelle_autre_sexe' => $libelle_autre_sexe
                ];
            }

            echo json_encode($json);
        } catch (Exception $e) {
            // En cas d'erreur, renvoyer un message d'erreur JSON
            echo json_encode(array('status' => "false", 'error' => 'Erreur lors de la récupération des données : ' . $e->getMessage()));
        }
    } else {
        // Si les paramètres sont manquants ou invalides
        echo json_encode(array('status' => false, 'error' => 'Paramètres manquants ou invalides.'));
    }
?>
