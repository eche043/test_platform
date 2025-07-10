<?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        //print_r($_POST);

        require_once '../../../Classes/UTILISATEURS.php';
        require_once '../../../Classes/RECRUTEMENT.php';
        $RECRUTEMENT = new RECRUTEMENT();

        $datas = array(
            'document_base64'=> $_POST['document'],
            'extension'=>$RECRUTEMENT->clean_data($_POST['extension']),
            'size'=>$_POST['file_size'],
            'nom_document'=>$RECRUTEMENT->clean_data($_POST['nom_document']),
            'id_compte' => $RECRUTEMENT->clean_data($_POST['id_compte']),
            'file_type' => $RECRUTEMENT->clean_data($_POST['file_type']),

        );
        $mine = $datas['size'];

        $id_compte = $datas['id_compte'];

        // Vérifier si les informations visuelles existent déjà
        $verifier_tb_info_visuelle = $RECRUTEMENT->trouver_infos_identification_visuelle($id_compte);

        if ($verifier_tb_info_visuelle) {
            // Mise à jour de la photo dans la base de données
            $photo = $RECRUTEMENT->modifier_photo($datas['document_base64'], $datas['file_type'], $id_compte);

            if($photo){
                $json = array(
                    'status' => 'success',
                    'image_base64' => $datas['document_base64'],
                    'file_type' => $datas['file_type'],
                    'message' => $photo['message']
                );
            }else{
                $json = array(
                    'status' => 'false',
                    'message' => 'Erreur lors de l\'enregistrement de la photo.'
                );
            }

        } else {
            $photo = $RECRUTEMENT->editer_photo($id_compte,$datas['document_base64'],$datas['file_type']);
            $json = array(
                'status' => 'success',
                'image_base64' => $datas['document_base64'],
                'file_type' => $datas['file_type'],
                'message' => $photo['message']
            );
        }


        // Définir le répertoire temporaire personnalisé
        //ini_set('upload_tmp_dir', '/var/www/html/IMPORTS/RECRUTEMENT_AGAC/tmp');

        //if (isset($_FILES['photo_identification_input']) && $_FILES['photo_identification_input']['error'] === UPLOAD_ERR_OK) {
        /*if (isset($_FILES['photo_identification_input'])) {
            $destination = '/var/www/html/IMPORTS/RECRUTEMENT_AGAC/tmp';
            move_uploaded_file($_FILES['photo_identification_input']['name'], $destination);
            echo json_encode(array(
                'status' => 'success',
                'message' => 'téléchargement OK.'
            ));
        } else {
            echo json_encode(array(
                'status' => 'false',
                'message' => 'Erreur lors du téléchargement de l\'image.'
            ));
        }*/

    } else {
        echo json_encode(['status' => 'error', 'message' => 'Méthode non autorisée']);
    }

    echo json_encode($json);