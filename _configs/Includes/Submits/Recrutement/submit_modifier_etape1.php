<?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        require_once '../../../Classes/UTILISATEURS.php';
        require_once '../../../Classes/RECRUTEMENT.php';
        require_once '../../../Classes/ASSURES.php';
        $RECRUTEMENT = new RECRUTEMENT();
        $ASSURES = new ASSURES();

        $nombreEnfants = $_POST['nombre_enfant_agac_modif_input'];
        $nom = strtoupper(htmlspecialchars($_POST['nom_agac_modif_input']));
        $prenoms = strtoupper(htmlspecialchars($_POST['prenoms_agac_modif_input']));
        $telephone = htmlspecialchars($_POST["numero_telephone_modif_input"]);
        $date_naissance = date('Y-m-d', strtotime($_POST['date_naissance_agac_modif_input']));
        $lieu_naissance = strtoupper(htmlspecialchars($_POST['lieu_naiss_agac_modif_input']));
        $nationalite = htmlspecialchars($_POST['nationalite_agac_modif_input']);
        $sexe = htmlspecialchars($_POST['sexe_agac_modif_input']);
        $num_secu_agac = htmlspecialchars($_POST['num_secu_agac_modif_input']);
        $adresse_email = htmlspecialchars($_POST['adresse_email_agac_modif_input']);
        $type_piece = htmlspecialchars($_POST['type_piece_agac_modif_input']);
        $numero_piece = htmlspecialchars($_POST['numero_piece_agac_modif_input']);
        $situation_matrimoniale = htmlspecialchars($_POST['situation_matrimoniale_agac_modif_input']);
        $id_compte = htmlspecialchars($_POST["id_compte_modif_input"]);
        $code_banque = $_POST['code_banque_modif_input'];
        $nom_banque = $_POST['nom_banque_modif_input'];
        $code_guichet = $_POST['code_guichet_modif_input'];
        $numero_compte = $_POST['numero_compte_modif_input'];
        $cle_rib = $_POST['cle_rib_modif_input'];
        $numero_cnps = $_POST['numero_cnps_modif_input'];
        if($_POST['tb_id_enfants']){
            $tb_id_enfant = explode(",",$_POST['tb_id_enfants']);
        }else{
            $tb_id_enfant = array();
        }

       // var_dump($tb_id_enfant);exit();

        $verifier_tb_info_bio = $RECRUTEMENT->trouver_infos_biographique($_SESSION['id_compte']);
        if ($verifier_tb_info_bio) {
            if(count($tb_id_enfant) == $nombreEnfants){
                if(isset($num_secu_agac) && !empty($num_secu_agac)){
                    $verif_assure = $ASSURES->trouver($num_secu_agac);
                    //if($verif_assure){
                        $modifier_infos_biographique_agac = $RECRUTEMENT->modifier_info_biographique($nom, $prenoms, $date_naissance, $lieu_naissance, $nationalite, $sexe, $telephone, $num_secu_agac, $adresse_email, $type_piece, $numero_piece, $situation_matrimoniale, $nombreEnfants,$code_banque,$nom_banque,$code_guichet,$numero_compte,$cle_rib,$id_compte);
                        if ($modifier_infos_biographique_agac['status'] === 'success') {
                            $status_enfants = [];
                            if ($nombreEnfants > 0) {
                                $trouver_enfants = $RECRUTEMENT->trouver_infos_enfants($_SESSION['id_compte']);
                                if($trouver_enfants){
                                    $fermer_all_enfants = $RECRUTEMENT->fermer_all_infos_enfant($_SESSION['id_compte']);
                                }
                                for ($i = 1; $i <= count($tb_id_enfant); $i++) {
                                    if (isset($_POST["nom_enfant_modif_$i"], $_POST["prenoms_enfant_modif_$i"], $_POST["date_naissance_enfant_modif_$i"], $_POST["lieu_naissance_enfant_modif_$i"], $_POST["sexe_enfant_modif_$i"], $_POST["numero_secu_enfant_modif_$i"])) {

                                        $nom_enfant = strtoupper(htmlspecialchars($_POST["nom_enfant_modif_$i"]));
                                        $prenoms_enfant = strtoupper(htmlspecialchars($_POST["prenoms_enfant_modif_$i"]));
                                        $date_naissance_enfant = date('Y-m-d', strtotime($_POST["date_naissance_enfant_modif_$i"]));
                                        $lieu_naissance_enfant = strtoupper(htmlspecialchars($_POST["lieu_naissance_enfant_modif_$i"]));
                                        $sexe_enfant = strtoupper(htmlspecialchars($_POST["sexe_enfant_modif_$i"]));
                                        $num_secu_enfant = htmlspecialchars($_POST["numero_secu_enfant_modif_$i"]);
                                        $id_enfant = $_POST["id_enfant_modif_$i"];


                                        //$update_enfant = $RECRUTEMENT->modifier_enfant($nom_enfant, $prenoms_enfant, $date_naissance_enfant, $lieu_naissance_enfant, $sexe_enfant, $num_secu_enfant, $id_compte, $id_enfant);
                                        $insert_enfant = $RECRUTEMENT->inserer_agac_enfant($nom_enfant, $prenoms_enfant, $date_naissance_enfant, $lieu_naissance_enfant, $sexe_enfant, $num_secu_enfant, $id_compte, $id_enfant);
                                        $status_enfants[] = [
                                            'nom' => $nom_enfant,
                                            'status' => $insert_enfant['status'],
                                        ];
                                    }
                                }
                            }

                            $json = [
                                'status' => 'success',
                                'message' => 'Informations modifiées avec succès.',
                                'details' => $status_enfants
                            ];
                        } else {
                            $json = [
                                'status' => 'error',
                                'message' => 'Échec de la modification des informations biographiques.'
                            ];
                        }
                  /*  }else{
                        $json = [
                            'status' => 'false',
                            'message' => "Le numéro secu renseigné n'existe pas."
                        ];
                    }*/
                }else{
                    $modifier_infos_biographique_agac = $RECRUTEMENT->modifier_info_biographique($nom, $prenoms, $date_naissance, $lieu_naissance, $nationalite, $sexe, $telephone, $num_secu_agac, $adresse_email, $type_piece, $numero_piece, $situation_matrimoniale, $nombreEnfants,$code_banque,$nom_banque,$code_guichet,$numero_compte,$cle_rib,$numero_cnps,$id_compte);
                    if ($modifier_infos_biographique_agac['status'] === 'success') {
                        $status_enfants = [];
                        if ($nombreEnfants > 0) {
                            $trouver_enfants = $RECRUTEMENT->trouver_infos_enfants($_SESSION['id_compte']);
                            if($trouver_enfants){
                                $fermer_all_enfants = $RECRUTEMENT->fermer_all_infos_enfant($_SESSION['id_compte']);
                            }
                            for ($i = 1; $i <= count($tb_id_enfant); $i++) {
                                if (isset($_POST["nom_enfant_modif_$i"], $_POST["prenoms_enfant_modif_$i"], $_POST["date_naissance_enfant_modif_$i"], $_POST["lieu_naissance_enfant_modif_$i"], $_POST["sexe_enfant_modif_$i"], $_POST["numero_secu_enfant_modif_$i"])) {

                                    $nom_enfant = strtoupper(htmlspecialchars($_POST["nom_enfant_modif_$i"]));
                                    $prenoms_enfant = strtoupper(htmlspecialchars($_POST["prenoms_enfant_modif_$i"]));
                                    $date_naissance_enfant = date('Y-m-d', strtotime($_POST["date_naissance_enfant_modif_$i"]));
                                    $lieu_naissance_enfant = strtoupper(htmlspecialchars($_POST["lieu_naissance_enfant_modif_$i"]));
                                    $sexe_enfant = strtoupper(htmlspecialchars($_POST["sexe_enfant_modif_$i"]));
                                    $num_secu_enfant = htmlspecialchars($_POST["numero_secu_enfant_modif_$i"]);
                                    $id_enfant = $_POST["id_enfant_modif_$i"];


                                    //$update_enfant = $RECRUTEMENT->modifier_enfant($nom_enfant, $prenoms_enfant, $date_naissance_enfant, $lieu_naissance_enfant, $sexe_enfant, $num_secu_enfant, $id_compte, $id_enfant);
                                    $insert_enfant = $RECRUTEMENT->inserer_agac_enfant($nom_enfant, $prenoms_enfant, $date_naissance_enfant, $lieu_naissance_enfant, $sexe_enfant, $num_secu_enfant, $id_compte, $id_enfant);
                                    $status_enfants[] = [
                                        'nom' => $nom_enfant,
                                        'status' => $insert_enfant['status'],
                                    ];
                                }
                            }
                        }

                        $json = [
                            'status' => 'success',
                            'message' => 'Informations modifiées avec succès.',
                            'details' => $status_enfants
                        ];
                    } else {
                        $json = [
                            'status' => 'error',
                            'message' => 'Échec de la modification des informations biographiques.'
                        ];
                    }
                }

            }else{
                $json = [
                    'status' => 'error',
                    'message' => 'Le nombre d\'enfants ne correspond pas.'
                ];
            }
        } else {
            $json = ['status' => 'error', 'message' => 'Aucune information biographique trouvée.'];
        }

        echo json_encode($json);

    } else {
        echo json_encode(['status' => 'error', 'message' => 'Méthode non autorisée']);
    }

?>
