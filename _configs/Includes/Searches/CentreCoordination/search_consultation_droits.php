<?php
require_once '../../../Classes/UTILISATEURS.php';
if(isset($_SESSION['ECMU_USER_ID'])){
    $session_user = $_SESSION['ECMU_USER_ID'];
    if(!empty($session_user)){
        $UTILISATEURS = new UTILISATEURS();
        $utilisateur_existe = $UTILISATEURS->trouver($session_user,null,null);

        if(!empty($utilisateur_existe['ID_UTILISATEUR'])){
            $trouver_ets = $UTILISATEURS->trouver_ets_utilisateur($session_user);
            require_once '../../../Classes/ASSURES.php';
            $ASSURES = new ASSURES();
            if($utilisateur_existe['ACTIF'] != 1){
                $json = array(
                    'status' => false,
                    'message' => 'VOTRE IDENTIFIANT A ETE DESACTIVE. VOUS NE POUVEZ PAS EFFECTUER CETTE ACTION. VEUILLEZ CONTACTER VOTRE ADMINISTRATEUR SYSTEME.'
                );
            }else{
                //$adresse_ip = '10.10.4.7/';
                $url = "https://test-mws.ipscnam.ci/ecnam/prestations/droits/json/";
                //$adresse_ip = 'recette-ecnam.ipscnam.ci/';
                //$adresse_ip = 'developpement.ipscnam.ci/ecnam/';

                $nb_parametres = count($_POST);
                $type_envoi = $_POST['type_envoi'];

                if($nb_parametres == 3) {

                    if($type_envoi == 'PHCIE') {
                        $num_facture = $_POST['num_facture'];
                        $login = $_POST['login'];
                        //$url = "https://".$adresse_ip."webservices/cmu/prestations/consultation_droits.php";
                        $parametres = [
                            'type_envoi' => $type_envoi,
                            'num_facture' => $num_facture,
                            'login' => $login
                        ];
                    }
                    if($type_envoi == 'CCORD') {
                        $num_secu = $_POST['num_secu'];
                        $code_ets = $_POST['code_ets'];
                        $id_user = $utilisateur_existe['ID_UTILISATEUR'];

                        //$url = "https://".$adresse_ip."webservices/cmu/prestations/consultation_droits.php";
                        $parametres = [
                            'type_envoi' => $type_envoi,
                            'code_ets' => $code_ets,
                            'num_secu' => $num_secu,
                            'id_user' => $id_user
                        ];
                    }
                }

                $ch = curl_init();

                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLINFO_HEADER_OUT, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $parametres);


                $result = curl_exec($ch);
                $err = curl_error($ch);
                curl_close($ch);

                $retour = json_decode($result);

                if($retour->status == 'success') {
                    if($retour->droitsOuverts == true) {
                        $json = array(
                            'status' => true,
                            'num_transaction' => $retour->numTransaction,
                            'message' => 'Message: '.$retour->commentaireRetour.'<br />Droits: <b>OUVERTS</b><br />N° FACTURE: <b>'.$retour->numTransaction.'</b><br />OGD PRESTAIONS: <b>'.$retour->codeOgdPrestations->libelle.'</b></b><br />TAUX DE COUVERTURE: <b>'.$retour->codeOgdPrestations->tauxCouverture.'</b><br />CMR: <b>'.$retour->exeReferent->libelle.'</b>'
                        );
                    }else {
                        $json = array(
                            'status' => false,
                            'message' => 'Message: '.$retour->commentaireRetour.'<br />Droits: <b>FERMES</b>'
                        );
                    }
                }else {
                    $json = array(
                        'status' => $retour->status,
                        'message' => $retour->message
                    );
                }
            }
        }else{
            $json = array(
                'status' => false,
                'message' => 'VOTRE IDENTIFIANT EST INCORRECT. VOUS NE POUVEZ PAS EFFECTUER CETTE ACTION. VEUILLEZ CONTACTER VOTRE ADMINISTRATEUR SYSTEME.'
            );
        }
    }else{
        $json = array(
            'status' => false,
            'message' => 'VOTRE IDENTIFIANT EST INCORRECT. VOUS NE POUVEZ PAS EFFECTUER CETTE ACTION. VEUILLEZ CONTACTER VOTRE ADMINISTRATEUR SYSTEME.'
        );
    }
}else{
    $json = array(
        'status' => false,
        'message' => 'VOTRE IDENTIFIANT EST INCORRECT. VOUS NE POUVEZ PAS EFFECTUER CETTE ACTION. VEUILLEZ CONTACTER VOTRE ADMINISTRATEUR SYSTEME.'
    );
}



echo json_encode($json);
?>

