<?php
require_once '../../../Classes/UTILISATEURS.php';
require_once '../../../Functions/functions-cicmu.php';
require_once '../../../Functions/function_conversion_caractere.php';
        if(isset($_POST['code_affection'])) {
            $code_affection = explode(',', str_replace('[', '', str_replace(']', '', str_replace('"', '', json_encode($_POST['code_affection'])))));

            $type_facture = trim($_POST['type_facture']);
            $date_soins = strtoupper(date('Y-m-d', strtotime(str_replace('/', '-', $_POST['date_soins']))));
            $num_facture = trim($_POST['num_facture']);
            $num_ep_cnam = trim($_POST['num_ep_cnam']);
            $num_ac = trim($_POST['num_ac']);
            $code_ac = trim($_POST['code_ac']);
            $code_ps = trim($_POST['code_ps']);
            $nom_ps = trim($_POST['nom_ps']);
            $code_specialite_ps = trim($_POST['code_specialite_ps']);

            $code_actes = explode(',', str_replace('[', '', str_replace(']', '', str_replace('"', '', json_encode($_POST['code_acte'])))));
            $nom_actes = explode(',', str_replace('[', '', str_replace(']', '', str_replace('"', '', json_encode($_POST['nom_acte'])))));
            $quantite_presc = explode(',', str_replace('[', '', str_replace(']', '', str_replace('"', '', json_encode($_POST['quantite_presc'])))));
            $quantite = explode(',', str_replace('[', '', str_replace(']', '', str_replace('"', '', json_encode($_POST['quantite'])))));
            $prix_unitaire = explode(',', str_replace('[', '', str_replace(']', '', str_replace('"', '', json_encode($_POST['prix_unitaire'])))));

            require_once '../../../Classes/FACTURES.php';
            require_once '../../../Classes/CICMU.php';
            $FACTURES = new FACTURES();
            $CICMU = new CICMU();
            $facture = $FACTURES->trouver($num_facture);
            if (!empty($facture['FEUILLE'])) {
                $array_statut = array('', 'C', 'F', 'N');
                if (in_array($facture['STATUT'], $array_statut)) {
                    $array_statut_fs_initiale = array('A');

                    $nb_actes = count($code_actes);
                    $ligne = 0;
                    if ($nb_actes != 0) {
                        if ($facture['STATUT'] == '') {
                            //$statut_fse = 'C';
                            $statut_fse = 'F';
                            $validaton = 1;
                        } elseif ($facture['STATUT'] == 'C') {
                            $statut_fse = 'F';
                            if (!empty($code_ps) && !empty($code_affection[0]) && !empty($date_sortie)) {
                                $validaton = 1;
                            } else {
                                $validaton = 0;
                            }
                        } else {
                            $statut_fse = 'F';
                            if (!empty($code_ps) && !empty($code_affection[0]) && !empty($date_sortie)) {
                                $validaton = 1;
                            } else {
                                $validaton = 0;
                            }
                        }
                        if ($validaton == 1) {
                            $get_token = get_token_file();
                            if ($get_token) {
                                $retour = json_decode($get_token, true);
                                $token = "Bearer " . $retour['tokens']['accessToken']['id'];
                                $parametres = json_decode(file_get_contents('php://input'));
                                $liste_affections = '';
                                if ($_POST['code_affection']) {
                                    $i = 1;
                                    foreach ($_POST['code_affection'] as $aff) {
                                        if ($i === count($_POST['code_affection'])) {
                                            $liste_affections = $liste_affections . '"' . $aff . '"';
                                        } else {
                                            $liste_affections = $liste_affections . '"' . $aff . '",';
                                        }
                                        $i++;
                                    }
                                }
                                $update_fs_cicmu = maj_facture($token, $facture['LIEN_ARCHIVAGE'], $num_ac, $code_ac, $code_ps, $nom_ps, $liste_affections);
                                if (isset($update_fs_cicmu->id)) {
                                    $body_adjud = '';

                                    for ($i = 0; $i < $nb_actes; $i++) {
                                        $ajouter_acte = $CICMU->inserer_adjudication(null, $facture['LIEN_ARCHIVAGE'], $num_facture, $code_actes[$i], $prix_unitaire[$i], null, $quantite_presc[$i], $quantite[$i], strtoupper(date('Y-m-d', strtotime($date_soins))), strtoupper(date('Y-m-d', strtotime($date_soins))), 'a', $code_ac,0);
                                        if ($ajouter_acte['status'] == true) {
                                            $body_adjud = $body_adjud . ' {"type": "ACT", "code": "' . $code_actes[$i] . '", "name": "' . conversionCaractere($nom_actes[$i]) . '", "prescribedQuantity": "' . $quantite_presc[$i] . '", "servedQuantity": "' . $quantite[$i] . '", "unitPrice": { "amount": "' . $prix_unitaire[$i] . '", "currency": "XOF" }, "startDateTime": "' . str_replace('GMT', 'T', str_replace('UTC', 'T', date('Y-m-dT00:00:00+00:00', strtotime($date_soins)))) . '"}';
                                            if ($i !== ($nb_actes - 1)) {
                                                $body_adjud = $body_adjud . ',';
                                            }
                                        $ligne++;
                                        }
                                    }
                                    if ($ligne == $nb_actes) {
                                        $demande_adj = adjudication($token, $facture['LIEN_ARCHIVAGE'], $body_adjud);

                                        if (empty(trim($demande_adj))) {
                                            $json = array(
                                                'status' => true,
                                                'message' => 'LA DEMANDE ADJUDICATION TRANSMISE'
                                            );
                                        } else {
                                            $json = array(
                                                'status' => true,
                                                'message' => 'LA DEMANDE D\'ADJUDICATION A ECHOUE. VEUILLEZ CONTACTER L\'ADMINISTRATEUR .'
                                            );
                                        }
                                    }
                                } else {
                                    $json = $update_fs_cicmu;
                                }
                            } else {
                                $json = $get_token;
                            }

                        }
                        else {
                            if (empty($code_ps)) {
                                $json = array(
                                    'status' => false,
                                    'message' => 'VEUILLEZ RENSEIGNER LE PROFESSIONNEL DE SANTE.'
                                );
                            } elseif (empty($code_affection[0])) {
                                $json = array(
                                    'status' => false,
                                    'message' => 'VEUILLEZ RENSEIGNER LA PATHOLOGIE.'
                                );
                            } elseif (empty($type_sortie)) {
                                $json = array(
                                    'status' => false,
                                    'message' => 'VEUILLEZ RENSEIGNER LE TYPE DE SORTIE.'
                                );
                            } elseif (empty($type_sortie)) {
                                $json = array(
                                    'status' => false,
                                    'message' => 'VEUILLEZ RENSEIGNER LA DATE DE SORTIE.'
                                );
                            } else {
                                $json = array(
                                    'status' => false,
                                    'message' => $validaton
                                );
                            }
                        }
                    } else {
                        $json = array(
                            'status' => false,
                            'message' => 'VEUILLEZ RENSEIGNER AU MOINS UN ACTE.'
                        );
                    }
                } else {
                    $json = array(
                        'status' => false,
                        'message' => 'LE N° DE FACTURE SAISI EST NE PEUT EDITE ACTUELLEMENT AVEC LE STATUT: ' . $facture['STATUT'] . '. VEUILLEZ CONTACTER VOTRE ADMINISTRATEUR SYSTEME.'
                    );
                }
            } else {
                $json = array(
                    'status' => false,
                    'message' => 'LE N° DE FACTURE SAISI EST INCORRECT. VEUILLEZ CONTACTER VOTRE ADMINISTRATEUR SYSTEME.'
                );
            }
        }
        else{
            $json = array(
                'status' => false,
                'message' => 'LES AFFECTIONS DOIVENT ETRE RENSEIGNEES POUR L\'ADJUDICATION'
            );
        }

echo json_encode($json);
?>

