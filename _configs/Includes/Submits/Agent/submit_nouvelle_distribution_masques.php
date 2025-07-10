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

                require_once '../../../Classes/DISTRIBUTIONMASQUES.php';
				require_once '../../../Classes/ASSURES.php';

                $DISTRIBUTIONMASQUES = new DISTRIBUTIONMASQUES();
                $ASSURES = new ASSURES();

                $code_ets = trim($_POST['code_ets']);
                $numero_secu = trim($_POST['numero_secu']);
                $numero_telephone = trim($_POST['numero_telephone']);
                $qte_servie = trim($_POST['qte_servie']);
                $date_debut = date('Y-m-d',strtotime(str_replace('/','-',trim($_POST['date_debut']))));
                $date_fin = date('Y-m-d',strtotime(str_replace('/','-',trim($_POST['date_fin']))));

                if(!empty($code_ets) && !empty($numero_secu)) {
					$trouver_assure = $ASSURES->trouver($numero_secu);
                    if(isset($trouver_assure['NUM_SECU'])){
						if($trouver_assure['CATEGORIE_PROFESSIONNELLE']=='FCI' || $trouver_assure['CATEGORIE_PROFESSIONNELLE']=='REP' || $trouver_assure['CATEGORIE_PROFESSIONNELLE']=='MIL'){
                            $json = array(
                                'status' => false,
                                'message' => 'CET ASSURE EST UN FONCTIONNAIRE. PRIERE VOUS REFERER A LA MUGEF POUR LES MASQUES.'
                            );

                        }else {
                            if($trouver_assure['PAYEUR_NUM_SECU']!=$trouver_assure['NUM_SECU']){
                                $json = array(
                                    'status' => false,
                                    'message' => 'SEULS LES OUVRANTS DROITS (ASSURES PAYEURS CMU) PEUVENT BENEFICIER DE CES MASQUES.'
                                );
                            }else {
								if(strlen($numero_telephone)<10){
                                    $json = array(
                                        'status' => false,
                                        'message' => 'PRIERE CORRIGER LE NUMERO DE TELEPHONE. LA NUMEROTATION EST PASSEE A DIX CHIFFRES.'
                                    );
                                }else{
									if(substr($numero_telephone,0,1)==='0'){
										$adresse_ip = '10.10.4.7/';
										//$adresse_ip = 'developpement.ipscnam.ci/ecnam/';

										$operateur = 'hyperSms';
										$user = $utilisateur_existe['ID_UTILISATEUR'];
										//$url = "https://".$adresse_ip."webservices/cmu/prestations/envoi_sms.php";
										$url = "https://" . $adresse_ip . "webservices/envoi-sms.php";

										$message = "CHER ASSURE CMU, L'ETAT VOUS A OFFERT ".(50*$qte_servie)." MASQUES DANS LE CADRE DE LA LUTTE CONTRE LA COVID 19 POUR  ".(30*$qte_servie)." JOURS. MERCI DE RESPECTER LES MESURES BARRIERES.";
										$parametres =[
											'numero' => $numero_telephone,
                                            'type_envoi' => 'I',
											'message' => $message
										];
										$dist_masque = $DISTRIBUTIONMASQUES->nouvelle_enregistrement($numero_secu, $numero_telephone, $qte_servie, $date_debut, $date_fin, $code_ets, $user);
										if($dist_masque['status']==true){
											$ch = curl_init();
											curl_setopt($ch, CURLOPT_URL, $url);
											curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
											curl_setopt($ch, CURLINFO_HEADER_OUT, true);
											curl_setopt($ch, CURLOPT_POST, true);
											curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
											curl_setopt($ch, CURLOPT_POSTFIELDS, $parametres);

											$result = curl_exec($ch);

											curl_close($ch);

											$retour = json_decode($result);
											//var_dump($retour);
										}
										$json = array(
											'status' => $dist_masque['status'],
											'message' => $dist_masque['message']
										);
										/*if($retour->status == 'success') {

										}else{
											$json = array(
												'status' => false,
												'message' => $retour->message
											);
										}*/
									}else{
                                        $json = array(
                                            'status' => false,
                                            'message' => 'PRIERE CORRIGER LE NUMERO DE TELEPHONE. CE NUMERO N\'EST PAS VALIDE.'
                                        );
                                    }
								}
							}
						}
					}else{
                    $json = array(
                        'status' => false,
                        'message' => 'LES DONNEES SAISIES SONT INCORRECTES.'
                    );
                }
                }else{
                    $json = array(
                        'status' => false,
                        'message' => 'LES DONNEES SAISIES SONT INCORRECTES.'
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
