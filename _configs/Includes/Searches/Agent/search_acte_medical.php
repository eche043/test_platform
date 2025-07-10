<?php
use GuzzleHttp\Pool;
use GuzzleHttp\Client;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\RequestException;

require_once '../../../Classes/UTILISATEURS.php';
require '../../../../vendor/autoload.php';
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
                $code_acte = trim($_POST['code_acte']);
                if(!empty($code_acte)) {
                    require_once '../../../Classes/ACTESMEDICAUX.php';
                    require_once '../../../Classes/ETABLISSEMENTSSANTE.php';
                    require_once '../../../Classes/LETTRESCLE.php';
                    $ACTESMEDICAUX = new ACTESMEDICAUX();
                    $ETABLISSEMENTSSANTE = new ETABLISSEMENTSSANTE();
                    $LETTRESCLE = new LETTRESCLE();

                    $acte = $ACTESMEDICAUX->trouver($code_acte);
                    if(!empty($acte_medical['CODE'])){
                        $json = array(
                            'status' => false,
                            'message' => "LE CODE DE L'ACTE EST INCORRECT. PRIERE SAISIR UN CODE VALIDE."
                        );
                    }else {
                        $client = new Client([
                            'timeout' => 60,
                            'verify' => false
                        ]);
                        $headers = [
                            'Authorization' => 'Bearer 1|pC8jCa84ZSYMjg4fwKWluPKzIWOFRMuSB81dX9ci316eec2b',
                            'accept' => 'application/json'
                        ];
                        $code_ets = trim($_POST['code_ets']);
                        $date_soins = date('d-M-y',time());
                        $request = new Request('GET', 'https://api.ipscnam.ci:3128/api/referentiels/actes-medicaux/'.$code_acte, $headers);

                        try{
                            $res = $client->sendAsync($request)->wait();
                            $reponse = json_decode($res->getBody());
                            if($reponse->code){
                                $json = array(
                                    'status' => true,
                                    'code_acte' => $reponse->code,
                                    'libelle' => $reponse->denomination,
                                    'date_debut' => $date_soins,
                                    'date_fin' => $date_soins,
                                    'entente_prealable' => 1,
                                    'tarif' => 0
                                );
                            }
                            else{
                                $json = array(
                                    'status' => false,
                                    'message' => $reponse['success']
                                );
                            }
                        }catch (\Exception $e){
                            //$json = ;
                            $json = array(
                                'status' => false,
                                'message' => $e->getMessage()
                            );
                        }
                        /*if(!empty($code_ets)) {
                            $reseau = $ETABLISSEMENTSSANTE->trouver_reseau_soins($code_ets);
                            if(empty($reseau)) {
                                if($acte['TYPE_ACTE'] == 'NGAP') {
                                    $lettre_cle = $LETTRESCLE->trouver($acte['LETTRE_CLE']);
                                    $tarif = ($acte['COEFFICIENT'] * $lettre_cle['PRIX_UNITAIRE']);
                                }else {
                                    $tarif = $acte['TARIF'];
                                }

                                $json = array(
                                    'status' => true,
                                    'code_acte' => $acte['CODE'],
                                    'libelle' => $acte['LIBELLE'],
                                    'date_debut' => $date_soins,
                                    'date_fin' => $date_soins,
                                    'entente_prealable' => $acte['ENTENTE_PREALABLE'],
                                    'tarif' => $tarif
                                );
                            }else {

                                $acte_reseau = $ACTESMEDICAUX->trouver_reseau_acte_medical($reseau['RESEAU_ID'],$code_acte,$date_soins);
                                if (empty($acte_reseau)) {
                                    if ($acte['TYPE_ACTE'] == 'NGAP') {
                                        $lettre_cle = $LETTRESCLE->trouver($acte['LETTRE_CLE']);
                                        $tarif = ($acte['COEFFICIENT'] * $lettre_cle['PRIX_UNITAIRE']);
                                    } else {
                                        $tarif = $acte['TARIF'];
                                    }

                                    $json = array(
                                        'status' => true,
                                        'code_acte' => $acte['CODE'],
                                        'libelle' => $acte['LIBELLE'],
                                        'date_debut' => $date_soins,
                                        'date_fin' => $date_soins,
                                        'entente_prealable' => $acte['ENTENTE_PREALABLE'],
                                        'tarif' => $tarif
                                    );
                                } else {
                                    if (empty($acte_reseau['TARIF']) || $acte_reseau['TARIF'] == 0) {
                                        $tarif = 0;
                                    } else {
                                        $tarif = $acte_reseau['TARIF'];
                                    }
                                    $json = array(
                                        'status' => true,
                                        'code_acte' => $acte['CODE'],
                                        'libelle' => $acte['LIBELLE'],
                                        'date_debut' => $date_soins,
                                        'date_fin' => $date_soins,
                                        'entente_prealable' => $acte['ENTENTE_PREALABLE'],
                                        'tarif' => $tarif
                                    );
                                }

                            }
                        }else{
                            $json = array(
                                'status' => false,
                                'message' => 'LES INFORMATIONS SAISIES SONT INCORRECTS. PRIERE ENTRER DES DONNEES VALIDES.'
                            );
                        }*/
                    }
                }else{
                    $json = array(
                        'status' => false,
                        'message' => 'LES INFORMATIONS SAISIES SONT INCORRECTS. PRIERE ENTRER DES DONNEES VALIDES.'
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


