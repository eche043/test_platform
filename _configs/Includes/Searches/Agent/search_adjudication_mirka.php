<?php
use GuzzleHttp\Pool;
use GuzzleHttp\Client;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\RequestException;

require_once '../../../Classes/UTILISATEURS.php';
require_once '../../../Classes/FACTURES.php';
require_once '../../../Functions/function_conversion_caractere.php';
require '../../../../vendor/autoload.php';

$UTILISATEURS = new UTILISATEURS();
$FACTURES = new FACTURES();

if(isset($_POST['num_facture'])){

    $type_facture = trim($_POST['type_facture']);
    $date_soins = strtoupper(date('Y-m-d', strtotime(str_replace('/', '-', $_POST['date_soins']))));
    $num_facture = trim($_POST['num_facture']);
    $numero_initial = trim($_POST['num_fs_initiale']);
    $num_ep_cnam = trim($_POST['num_ep_cnam']);
    $num_ac = trim($_POST['num_ac']);

    $code_organisme = trim($_POST['code_organisme']);
    $code_etablissement = trim($_POST['code_etablissement']);
    $code_ps = trim($_POST['code_ps']);
    $nom_ps = trim($_POST['nom_ps']);
    $code_specialite_ps = trim($_POST['code_specialite_ps']);
    $pathologies =  "";
    $_POST['code_affection'] = array('B52');
    if (isset($_POST['code_affection'])) {
        $i = 1;
        foreach ($_POST['code_affection'] as $aff) {
            $pathologies = $pathologies."&pathologies[]=".$aff;

            $i++;
        }
    }
    else{
        $pathologies = "&pathologies=";
    }

    $code_actes = explode(',', str_replace('[', '', str_replace(']', '', str_replace('"', '', json_encode($_POST['code_acte'])))));
    $nom_actes = explode(',', str_replace('[', '', str_replace(']', '', str_replace('"', '', json_encode($_POST['nom_acte'])))));
    $quantite_presc = explode(',', str_replace('[', '', str_replace(']', '', str_replace('"', '', json_encode($_POST['quantite_presc'])))));
    $quantite = explode(',', str_replace('[', '', str_replace(']', '', str_replace('"', '', json_encode($_POST['quantite'])))));
    $prix_unitaire = explode(',', str_replace('[', '', str_replace(']', '', str_replace('"', '', json_encode($_POST['prix_unitaire'])))));
    $dents = explode(',', str_replace('[', '', str_replace(']', '', str_replace('"', '', json_encode($_POST['num_dent'])))));

    $ligne = 0;
    $nb_actes = count($code_actes);
    $actes = "";
    $parametre_fs_init = "";
    if($type_facture==="MED"){
        $parametre_fs_init = "&numero_initial=".$numero_initial;
    }
    for ($i = 0; $i < $nb_actes; $i++) {
        $actes = $actes ."&actes[$i][code]=".strtoupper($code_actes[$i])."&actes[$i][prix_unitaire]=".$prix_unitaire[$i] ;
        if ($dents[$i]) {
            $actes = $actes ."&actes[$i][dents][]=".$dents[$i];
        }
        $ligne++;
    }

    $first_ac = substr($num_ac,0,6);
    $second_ac = substr($num_ac,6,1);
    $third_ac = substr($num_ac,7,4);
    if(is_numeric($first_ac) && !is_numeric($second_ac) && is_numeric($third_ac)) {

        $client = new Client([
            'timeout' => 60,
            'verify' => false
        ]);
        $headers = [
            'Authorization' => 'Bearer 1|pC8jCa84ZSYMjg4fwKWluPKzIWOFRMuSB81dX9ci316eec2b',
            //'Authorization' => 'Bearer 79|VdcGNkIF1YKKeqZeUuYN5TgjYqgOZdlGdQnDb6Fj0dc9a4b6',
            'Accept' => 'application/json'
        ];

        $request = new Request('GET', 'https://10.10.4.85:3128/api/prestations/factures/' . $num_facture . '/actes?code_etablissement=' . $code_etablissement . '&numero_matricule=' . $num_ac . '&code_type_facture=' . $type_facture . $parametre_fs_init . '&date_soins=' . $date_soins . $pathologies .
            '&code_organisme=' . $code_organisme . $actes, $headers);
        //$res = $client->sendAsync($request)->wait();
        try {
            $res = $client->sendAsync($request)->wait();
            $reponse = json_decode($res->getBody());
            if ($reponse->success === true) {
                $json = array(
                    'status' => true,
                    'actes' => $reponse->actes
                );
            } else {
                $json = array(
                    'status' => false,
                    'message' => $reponse['success']
                );
            }
        } catch (\Exception $e) {
            //$json = ;
            $json = array(
                'status' => false,
                'message' => $e->getMessage()
            );
        }
    }
    else{
        $json = array(
            'status' => false,
            'message' => "LE FORMAT DU N° MATRICULE SAISI EST INCORRECT."
        );
    }
    /*$curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://10.10.4.85:3128/api/prestations/factures/'.$num_facture.'/actes?code_etablissement='.$code_etablissement.'&code_type_facture='.$type_facture.'&date_soins='.$date_soins.'&'.$pathologies.
        '&code_organisme='.$code_organisme.$actes,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer 1|pC8jCa84ZSYMjg4fwKWluPKzIWOFRMuSB81dX9ci316eec2b',
            'accept: application/json'
        ),
    ));*/

    //$response = curl_exec($curl);

    /*curl_close($curl);
    var_dump(array($response,'https://10.10.4.85:3128/api/prestations/factures/'.$num_facture.'/actes?code_etablissement='.$code_etablissement.'&code_type_facture='.$type_facture.'&date_soins='.$date_soins.'&'.$pathologies.
        '&code_organisme='.$code_organisme.$actes)); exit();*/

    /*    curl --location --globoff 'https://api.ipscnam.ci:3128/api/prestations/factures/28273/actes?code_etablissement=000600127&code_type_facture=DEN&date_soins=2024-08-23%2009%3A42&pathologies=&code_organisme=02998000&actes%5B0%5D%5Bcode%5D=MAGC001&actes%5B0%5D%5Bdents%5D%5B%5D=11&actes%5B0%5D%5Bdents%5D%5B%5D=12' \
--header 'Accept: application/json' \
--header 'Authorization: Bearer 1|pC8jCa84ZSYMjg4fwKWluPKzIWOFRMuSB81dX9ci316eec2b'

    https://api.ipscnam.ci:3128/api/prestations/factures/28273/
    actes?code_etablissement=000100124
    &code_type_facture=DEN&date_soins=2024-08-28&pathologies&code_organisme=02998000
    &actes%5B0%5D%5Bcode%5D=MAGC001&actes%5B0%5D%5Bprix_unitaire%5D=1100
    &actes%5B0%5D%5Bdents%5D%5B%5D=11&actes%5B0%5D%5Bdents%5D%5B%5D=12

    "numero_initial": "28275",
     "code_etablissement": "000100124",
     "code_type_facture": "MED",
     "date_soins": "2024-08-28",
     "pathologies": [],
     "code_organisme": "02998000",
     "actes": [
           {
                "code": "BYDZ004",
                "prix_unitaire": 500
           }, {
                "code": "MQTA030",
                "prix_unitaire": 5000
           }, {
                "code": "TETY005",
                "prix_unitaire": 7000
           }
     ]


    */



    /*$facture = $FACTURES->trouver($num_facture);
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
    }*/
}
else{
    $json = array(
        'status' => false,
        'message' => 'LE NUMERO DE FACTURE DOIT ETRE RENSEIGNE. VEUILLEZ CONTACTER VOTRE ADMINISTRATEUR SYSTEME.'
    );
}
echo json_encode($json);
?>

