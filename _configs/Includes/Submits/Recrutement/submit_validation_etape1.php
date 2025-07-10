<?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        require_once '../../../Classes/UTILISATEURS.php';
        require_once '../../../Classes/RECRUTEMENT.php';
        $RECRUTEMENT = new RECRUTEMENT();

        $nombreEnfants = (int) $_POST['nombre_enfant_agac_input'];
        $nom = strtoupper($_POST['nom_agac_input']);
        $prenoms = strtoupper($_POST['prenoms_agac_input']);
        $telephone = $_POST["numero_telephone_input"];
        $date_naissance = date('Y-m-d', strtotime($_POST['date_naissance_agac_input']));
        $lieu_naissance = strtoupper($_POST['lieu_naiss_agac_input']);
        $nationalite = $_POST['nationalite_agac_input'];
        $sexe = $_POST['sexe_agac_input'];
        $num_secu_agac = $_POST['num_secu_agac_input'];
        $adresse_email = $_POST['adresse_email_agac_input'];
        $type_piece = $_POST['type_piece_agac_input'];
        $numero_piece = $_POST['numero_piece_agac_input'];
        $situation_matrimoniale = $_POST['situation_matrimoniale_agac_input'];
        $civilite = $_POST['civilite_agac_input'];
        $lieu_residence = $_POST['lieu_residence_agac_input'];
        $code_banque = $_POST['code_banque_input'];
        $nom_banque = $_POST['nom_banque_input'];
        $code_guichet = $_POST['code_guichet_input'];
        $numero_compte = $_POST['numero_compte_input'];
        $cle_rib = $_POST['cle_rib_input'];
        $numero_cnps = $_POST['numero_cnps_input'];
        $enfants = [];

        // Boucle pour récupérer les informations de chaque enfant
        for ($i = 1; $i <= $nombreEnfants; $i++) {
            $enfants[] = [
                'nom' => strtoupper($_POST["nom_enfant_$i"]),
                'prenoms' => strtoupper($_POST["prenoms_enfant_$i"]),
                'date_naissance' => strtoupper($_POST["date_naissance_enfant_$i"]),
                'lieu_naissance' => strtoupper($_POST["lieu_naissance_enfant_$i"]),
                'sexe' => strtoupper($_POST["sexe_enfant_$i"]),
                'num_secu_enfant' => strtoupper($_POST["numero_secu_enfant_$i"]),
                'id_enfant' => $_POST["id_enfant_$i"]
            ];
        }

        // Vérifier si les infos biographiques existent déjà
        $verifier_tb_info_bio = $RECRUTEMENT->trouver_infos_biographique($_SESSION['id_compte']);
        if ($verifier_tb_info_bio) {
            $json = [
                'status' => 'false',
                'message'=> "Une erreur est survenue lors de l'enregistrement. Veuillez contacter l'Administrateur"
            ];
        } else {
            // Vérification des numéros de sécurité sociale
            foreach ($enfants as $tab_enfant) {
                $num_secu_enfant = $tab_enfant['num_secu_enfant'];
                if (!empty($num_secu_enfant) && $num_secu_enfant === $num_secu_agac) {
                    $json = [
                        'status' => 'false',
                        'message' => "Les numéros secu renseignés sont identiques."
                    ];
                    echo json_encode($json);
                    return;  // Stop execution if duplicate found
                }
            }

            // Insérer les informations biographiques
            $editer_agac = $RECRUTEMENT->editer_infos_biometrique($_SESSION['id_compte'], $nom, $prenoms, $date_naissance, $lieu_naissance, $nationalite, $sexe, $telephone, $num_secu_agac, $adresse_email, $type_piece, $numero_piece, $situation_matrimoniale, $nombreEnfants, $civilite, $lieu_residence,$nom_banque,$code_banque,$code_guichet,$numero_compte,$cle_rib,$numero_cnps);

            if ($editer_agac['status'] === "success") {
                $trouver_enfants = $RECRUTEMENT->trouver_infos_enfants($_SESSION['id_compte']);
                if($trouver_enfants){
                    $fermer_all_enfants = $RECRUTEMENT->fermer_all_infos_enfant($_SESSION['id_compte']);
                }
                // Insérer les informations des enfants
                $editer_agac_enfant = $RECRUTEMENT->editer_agac_enfant($_SESSION['id_compte'], $enfants);
                $json = [
                    'status' => 'success',
                    'message' => $editer_agac_enfant["message"]
                ];
            } else {
                $json = [
                    'status' => 'false',
                    'message' => "Une erreur est survenue lors de l'enregistrement."
                ];
            }
        }
    } else {
        $json = ['status' => 'error', 'message' => 'Méthode non autorisée'];
    }

    echo json_encode($json);
