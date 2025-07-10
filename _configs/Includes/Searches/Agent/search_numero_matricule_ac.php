<?php
require_once '../../../Classes/UTILISATEURS.php';
if(isset($_SESSION['ECMU_USER_ID'])){
    $session_user = $_SESSION['ECMU_USER_ID'];
    if(!empty($session_user)){
        $UTILISATEURS = new UTILISATEURS();
        $utilisateur_existe = $UTILISATEURS->trouver($session_user,null,null);

        if(!empty($utilisateur_existe['ID_UTILISATEUR'])){
            $trouver_ets = $UTILISATEURS->trouver_ets_utilisateur($session_user);
            if($utilisateur_existe['ACTIF'] != 1){
                $json = array(
                    'status' => false,
                    'message' => 'VOTRE IDENTIFIANT A ETE DESACTIVE. VOUS NE POUVEZ PAS EFFECTUER CETTE ACTION. VEUILLEZ CONTACTER VOTRE ADMINISTRATEUR SYSTEME.'
                );
            }else{
                $numero_matricule_ac = trim($_POST['numero_matricule_ac']);
                $num_secu = trim($_POST['num_secu']);

                if(!empty($num_secu) && !empty($numero_matricule_ac)) {
                    $first_ac = substr($numero_matricule_ac,0,6);
                    $second_ac = substr($numero_matricule_ac,6,1);
                    $third_ac = substr($numero_matricule_ac,7,4);
                    if(is_numeric($first_ac) && !is_numeric($second_ac) && is_numeric($third_ac)){
                        require_once '../../../Classes/ASSURES.php';
                        require_once '../../../Classes/ASSURANCE.php';
                        $ASSURES = new ASSURES();
                        $ASSURANCE = new ASSURANCE();

                        $assure = $ASSURES->trouver($num_secu);
                        $trouver_numero_matricule = $ASSURANCE->trouver_numero_matricule_ac($numero_matricule_ac);
                        if (isset($trouver_numero_matricule['MATRICULE_BENEFICIAIRE_MUGEFCI'])) {
                            if($assure['SEXE']==='F'){
                                if($trouver_numero_matricule['GENRE']==='FEMININ'){
                                    $json = array(
                                        'status' => true,
                                        'num_secu' => $trouver_numero_matricule['MATRICULE_BENEFICIAIRE_MUGEFCI'],
                                        'nom' => $trouver_numero_matricule['NOM'],
                                        'prenom' => $trouver_numero_matricule['PRENOMS'],
                                        'date_naissance' => date('d/m/Y', strtotime($trouver_numero_matricule['DATE_NAISSANCE']))
                                    );
                                }
                                else{
                                    $json = array(
                                        'status' => false,
                                        'message' => "LE MATRICULE SAISI NE CORRESPOND PAS À DE L'ASSURE."
                                    );
                                }
                            }
                            else{
                                if ($assure['NOM'] === $trouver_numero_matricule['NOM']) {
                                    $json = array(
                                        'status' => true,
                                        'num_secu' => $trouver_numero_matricule['MATRICULE_BENEFICIAIRE_MUGEFCI'],
                                        'nom' => $trouver_numero_matricule['NOM'],
                                        'prenom' => $trouver_numero_matricule['PRENOMS'],
                                        'date_naissance' => date('d/m/Y', strtotime($trouver_numero_matricule['DATE_NAISSANCE']))
                                    );
                                } else {
                                    $json = array(
                                        'status' => false,
                                        'message' => "LE MATRICULE SAISI NE CORRESPOND PAS AU NOM DE L'ASSURE."
                                    );
                                }
                            }
                        } else {
                            $json = array(
                                'status' => false,
                                'message' => "LE N° MATRICULE SAISI EST INCONNU."
                            );
                        }
                    }
                    else{
                        $json = array(
                            'status' => false,
                            'message' => "LE FORMAT DU N° MATRICULE SAISI EST INCORRECT."
                        );
                    }
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