<?php
    header('Content-Type: application/json');

    $url = "https://developpement.ipscnam.ci/ecnam/webservices/cmu/applications/connexion.php";

    if(isset($_POST['code_identifiant'])){
        $identifiant = $_POST['code_identifiant'];
        if($_POST['mot_de_passe']){
            $mot_de_passe = $_POST['mot_de_passe'];
            $parametres = array(
                'code_identifiant' => $identifiant,
                'mot_de_passe' => $mot_de_passe
            );
            $options = array(
                'http' => array(
                    'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                    'method'  => 'POST',
                    'content' => http_build_query($parametres),
                )
            );
            $context  = stream_context_create($options);
            $retour = json_decode(file_get_contents($url, false, $context));
            $json = array(
                'status'  => $retour->body->status,
                'identifiant'=> $retour->num_secu,
                'nom'=> $retour->nom,
                'prenoms'=> $retour->prenoms,
                'date_naissance'=> $retour->num_secu,
            ) ;
        }else{
            $json = array(
                'status' => false,
                'message' => 'Veuillez renseigner le mot de passe'
            );
        }
    }else{
        $json = array(
            'status' => false,
            'message' => 'Veuillez renseigner le code identifiant'
        );
    }

    echo json_encode($json);