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
                $num_fs_initiale =  trim($_POST['num_fs_initiale']);
                $num_secu = trim($_POST['num_secu']);
                if(!empty($num_fs_initiale)&&!empty($num_secu)) {
                    require_once '../../../Classes/ASSURES.php';
                    require_once '../../../Classes/FACTURES.php';
                    $ASSURES = new ASSURES();
                    $FACTURES = new FACTURES();
                    $facture = $FACTURES->trouver_facture_initiale($num_fs_initiale);
                    if(empty($facture['FEUILLE'])) {
                        $json = array(
                            'status' => false,
                            'message' => "LE N° DE FEUILLE DE SOINS RENSEIGNÉ EST INCORRECT"
                        );
                    }else {
                        if($facture['NUM_SECU']==$num_secu){
                            if($facture['STATUT'] == 'A') {
                                $json = array(
                                    'status' => false,
                                    'message' => "LE N° DE FEUILLE DE SOINS RENSEIGNÉ A ÉTÉ ANNULÉ."
                                );
                            }elseif($facture['STATUT'] == 'R') {
                                $json = array(
                                    'status' => false,
                                    'message' => "LE N° DE FEUILLE DE SOINS RENSEIGNÉ A ÉTÉ RÉFUSÉ."
                                );
                            }else {
                                if(empty($facture['TYPE_FEUILLE'])) {
                                    $c = $FACTURES->maj_type_facture($facture['FEUILLE'],'AMB',null,null,null);
                                }

                                $date = date('Y-m-d',time());
                                $now = strtotime($date); // or your date as well
                                $your_date = strtotime($facture['DATE_SOINS']);
                                $datediff = $now - $your_date;

                                $validite = ($datediff / (60 * 60 * 24));
                                if(round($validite)>= 7){
                                    $json = array(
                                        'status' => false,
                                        'message' => "LE NUMÉRO DE FEUILLE DE SOINS INITIALE A EXPIRÉ. PRIÈRE DE RENOUVELER LA FEUILLE DE SOINS INITIALE."
                                    );
                                }else{
                                    $json = array(
                                        'status' => true,
                                        'num_secu' => $facture['NUM_SECU'],
                                        'nom' => $facture['NOM'],
                                        'prenom' => $facture['PRENOM'],
                                        'date_naissance' => $facture['DATE_NAISSANCE']
                                    );
                                }

                            }
                        }else{
                            $json = array(
                                'status' => false,
                                'message' => "LE N° DE FEUILLE DE SOINS RENSEIGNÉ NE CORRESPOND PAS À L'ASSURÉ PRECISÉ."
                            );
                        }
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