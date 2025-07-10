<?php
require_once '../../../Classes/UTILISATEURS.php';
require_once '../../../Functions/functions-cicmu.php';
require_once '../../../Functions/function_conversion_caractere.php';
if(isset($_SESSION['ECMU_USER_ID'])){
    $session_user = $_SESSION['ECMU_USER_ID'];
    if(!empty($session_user)){
        $UTILISATEURS = new UTILISATEURS();
        $utilisateur = $UTILISATEURS->trouver($session_user,null,null);

        if(!empty($utilisateur['ID_UTILISATEUR'])){
            $trouver_ets = $UTILISATEURS->trouver_ets_utilisateur($session_user);
            require_once '../../../Classes/ASSURES.php';
            $ASSURES = new ASSURES();
            if($utilisateur['ACTIF'] != 1){
                $json = array(
                    'status' => false,
                    'message' => 'VOTRE IDENTIFIANT A ETE DESACTIVE. VOUS NE POUVEZ PAS EFFECTUER CETTE ACTION. VEUILLEZ CONTACTER VOTRE ADMINISTRATEUR SYSTEME.'
                );
            }else{
                $type_facture = trim($_POST['type_facture']);
                $date_soins = strtoupper(date('Y-m-d',strtotime(str_replace('/','-',$_POST['date_soins']))));
                $date_sortie = strtoupper(date('Y-m-d',strtotime(str_replace('/','-',$_POST['date_sortie']))));
                $type_sortie = trim($_POST['type_sortie']);
                $num_fs_initiale = trim($_POST['num_fs_initiale']);
                $num_facture = trim($_POST['num_facture']);
                $num_ep_cnam = trim($_POST['num_ep_cnam']);
                $num_ac = trim($_POST['num_ac']);
                $code_ac = trim($_POST['code_ac']);
                if(isset($_POST['type_ets'])){
                    $type_ets = trim($_POST['type_ets']);
                }else{
                    $type_ets = NULL;
                }
                $type_ets_autre = trim($_POST['type_ets_autre']);
                $info_complementaire = trim($_POST['info_complementaire']);
                $info_compl_autre = trim($_POST['info_compl_autre']);
                $code_programme = trim($_POST['code_programme']);
                $code_ps = trim($_POST['code_ps']);
                $nom_ps = trim($_POST['nom_ps']);
                $code_specialite_ps = trim($_POST['code_specialite_ps']);
                if(isset($_POST['code_affection'])) {
                    $code_affection = explode(',',str_replace('[','',str_replace(']','',str_replace('"','',json_encode($_POST['code_affection'])))));
                }else {
                    $code_affection = array(NULL,NULL);
                }
                $code_actes = explode(',',str_replace('[','',str_replace(']','',str_replace('"','',json_encode($_POST['code_acte'])))));
                $nom_actes = explode(',',str_replace('[','',str_replace(']','',str_replace('"','',json_encode($_POST['nom_acte'])))));
                $quantite_presc = explode(',',str_replace('[','',str_replace(']','',str_replace('"','',json_encode($_POST['quantite_presc'])))));
                $quantite = explode(',',str_replace('[','',str_replace(']','',str_replace('"','',json_encode($_POST['quantite'])))));
                $prix_unitaire = explode(',',str_replace('[','',str_replace(']','',str_replace('"','',json_encode($_POST['prix_unitaire'])))));

                require_once '../../../Classes/FACTURES.php';
                require_once '../../../Classes/CICMU.php';
                $FACTURES = new FACTURES();
                $CICMU = new CICMU();
                $facture = $FACTURES->trouver($num_facture);
                if(!empty($facture['FEUILLE'])) {
                    $array_statut = array('','C','F','N');
                    if(in_array($facture['STATUT'],$array_statut)) {
                        $array_statut_fs_initiale = array('A');
                        if($type_facture == 'AMB' || $type_facture == 'DEN') {
                            $nb_actes = count($code_actes);
                            $ligne = 0;
                            if($nb_actes != 0) {
                                $get_token = get_token_file();
                                if($get_token) {
                                    $retour = json_decode($get_token, true);
                                    $token = "Bearer " . $retour['tokens']['accessToken']['id'];
                                    $parametres = json_decode(file_get_contents('php://input'));
                                    $trouver_transaction = trouver_transaction_cicmu($token, 'BASIC');
                                    if(isset($trouver_transaction->items)){
                                        if(count($trouver_transaction->items)>0){
                                            $trouver_complementary = trouver_transaction_cicmu($token, 'COMPLEMENTARY');
                                            if(isset($trouver_complementary->items)){
                                                if(count($trouver_complementary->items)>0){
                                                    $montant = 0;
                                                    $part_cmu = 0;
                                                    $part_ac = 0;
                                                    $part_assure = 0;
                                                    for ($i = 0; $i < $nb_actes; $i++) {
                                                        $trouver_acte_adjuje = $CICMU->trouver_acte_adjuje($code_actes[$i], $num_facture);
                                                        if ($trouver_acte_adjuje) {
                                                            $code_acte_ad = $trouver_transaction->items[0]->careItems[$i]->code;
                                                            if($code_acte_ad===$code_actes[$i]){
                                                                $montant_ad = $trouver_transaction->items[0]->careItems[$i]->totalAmountSupported->amount;
                                                                $montant_complementaire = $trouver_complementary->items[0]->careItems[$i]->totalAmountSupported->amount;
                                                                $update_montant_adj = $CICMU->maj_adjudication($trouver_transaction->items[0]->id, $trouver_transaction->items[0]->id, $num_facture, $code_actes[$i], $montant_ad, $montant_complementaire, 1);
                                                                $montant = $montant + ($trouver_acte_adjuje['MONTANT']*$trouver_acte_adjuje['QUANTITE_SERVIE']);
                                                                $part_ac = $part_ac +$montant_complementaire;
                                                                $part_cmu = $part_cmu +$montant_ad;
                                                            }
                                                        }else{
                                                            $json[$i] = array(
                                                                'status' => false,
                                                                'message' => 'AUCUNE ADJUDICATION TROUVEE COTE CLIENT.'
                                                            );;
                                                        }
                                                        $ligne++;

                                                    }
                                                    if($ligne === $nb_actes){
                                                        $part_assure = $montant - ($part_cmu+$part_ac);
                                                        /*$message ='<table class="prestation_table">
                    <thead>
                    <tr>
                        <th>TOTAL</th>
                        <th width="100">PART CMU.</th>
                        <th width="100">PART AC.</th>
                        <th width="100">PART ASSURE</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td align="right">
                            <input type="text" class="form-control form-control-sm " autocomplete="off" placeholder="" readonly value="'.$montant.'"/>
                        </td>
                        <td align="right">
                            <input type="text" class="form-control form-control-sm " autocomplete="off" placeholder="" readonly value="'.$part_cmu.'"/>
                        </td>
                        <td align="right">
                            <input type="text" class="form-control form-control-sm " autocomplete="off" placeholder="" readonly value="'.$part_ac.'"/>
                        </td>
                        <td align="right">
                            <input type="text" class="form-control form-control-sm " autocomplete="off" placeholder="" readonly value="'.$part_assure.'"/>
                        </td>
                    </tr>
                    </tbody>
                </table>';*/


                                                        $json = array(
                                                            'status' => true,
                                                            'total' => $montant,
                                                            'part_cmu' => $part_cmu,
                                                            'part_ac' => $part_ac,
                                                            'part_assure' => $part_assure,
                                                            'message' => 'SUCCES'
                                                        );
                                                    }
                                                }
                                                else{
                                                    $json = array(
                                                        'status' => false,
                                                        'message' => 'AUCUNE ADJUDICATION TROUVEE COTE AC.'
                                                    );
                                                }
                                            }
                                            else{
                                                $json = $trouver_complementary;
                                            }
                                        }
                                        else{
                                            $json = array(
                                                'status' => false,
                                                'message' => 'AUCUNE ADJUDICATION TROUVEE COTE RGB.'
                                            );;
                                        }
                                    }
                                    else{
                                        $json = $trouver_transaction;
                                    }

                                    //echo $trouver_transaction->items[0]->careItems[0]->totalAmountSupported->amount;
                                    //echo $trouver_transaction->items[0]->careItems[0]->code;
                                }
                                else{
                                    $json = $get_token;
                                }
                            }else {
                                $json = array(
                                    'status' => false,
                                    'message' => 'VEUILLEZ RENSEIGNER AU MOINS UN ACTE.'
                                );
                            }
                        }
                    }else {
                        $json = array(
                            'status' => false,
                            'message' => 'LE N° DE FACTURE SAISI EST NE PEUT EDITE ACTUELLEMENT AVEC LE STATUT: '.$facture['STATUT'].'. VEUILLEZ CONTACTER VOTRE ADMINISTRATEUR SYSTEME.'
                        );
                    }
                }else {
                    $json = array(
                        'status' => false,
                        'message' => 'LE N° DE FACTURE SAISI EST INCORRECT. VEUILLEZ CONTACTER VOTRE ADMINISTRATEUR SYSTEME.'
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

