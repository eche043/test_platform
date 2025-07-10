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
            if($utilisateur_existe['ACTIF'] != 1){
                ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <p>VOTRE IDENTIFIANT A ETE DESACTIVE. VOUS NE POUVEZ PAS EFFECTUER CETTE ACTION. VEUILLEZ CONTACTER VOTRE ADMINISTRATEUR SYSTEME.</p>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <?php
            }else{
                require_once '../../../Classes/ASSURES.php';
                require_once '../../../Classes/ACTESMEDICAUX.php';
                require_once '../../../Classes/ENTENTESPREALABLES.php';
                $ASSURES = new ASSURES();
                $ENTENTESPREALABLES = new ENTENTESPREALABLES();
                $ACTESMEDICAUX = new ACTESMEDICAUX();

                $id_entente_prealable = trim($_POST['id_entente_prealable']);
                //715|WTX2uVR850ZKYtc3auj9oCNK1UXPemWcqHisR1C6e4f44cbb
                //$entente_prealable = $ENTENTESPREALABLES->trouver_distinct_entente($id_entente_prealable);
                $client = new Client([
                    'timeout' => 60,
                    'verify' => false
                ]);
                $headers = [
                    'Authorization' => 'Bearer 715|WTX2uVR850ZKYtc3auj9oCNK1UXPemWcqHisR1C6e4f44cbb',
                    'accept' => 'application/json'
                ];

                $request = new Request('GET', 'https://10.10.4.85:3128/api/prestations/ententes-prealables/'.$id_entente_prealable, $headers);
                //$res = $client->sendAsync($request)->wait();
                try{
                    $res = $client->sendAsync($request)->wait();
                    $reponse = json_decode($res->getBody());

                    if(isset($reponse->numero)){
                        $entente_prealable = array(
                            'status' => true,
                            'numero' => $reponse->numero,
                            'entente_prealable' => $reponse,
                        );
                    }
                    else{
                        $entente_prealable = array(
                            'status' => $reponse['success'],
                            'message' => $reponse['message'],
                            'numero'=>null
                        );
                    }
                }catch (\Exception $e){
                    //$json = ;
                    $json = array(
                        'status' => false,
                        'message' => $e->getMessage()
                    );
                }
                $user_ets = $UTILISATEURS->trouver_ets_utilisateur($utilisateur_existe['ID_UTILISATEUR']);
                if(!empty($user_ets['CODE_ETS'])) {
                    $user_ets = $user_ets['CODE_ETS'];
                }
                else{
                    $user_ets = null;
                }
                if (empty($entente_prealable['numero'])) {
                    ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <p align="center">AUCUNE DEMANDE TROUVEE</p>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <?php
                } else {
                    if($entente_prealable['entente_prealable']->etablissement->code === $user_ets){
                    ?>
                    <table class="table table-sm table-bordered table-hover" id="dataTable">
                        <thead class="bg-info">
                        <tr>
                            <th>N° ENTENTE</th>
                            <th>N° SECU</th>
                            <th>NOM & PRENOM</th>
                            <th>STATUT</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                            /*$assure = $ASSURES->trouver($entente_prealable['NUM_SECU']);

                            if($entente_prealable['TYPE_EP']=="HOS"){
                                $type_entente = "HOSPITALISATION";
                                if($entente_prealable['TYPE_HOSP']=="HC"){
                                    $acte_medical = "HOSPITALISATION CHIRURGICALE";
                                }elseif($entente_prealable['TYPE_HOSP']=="HM"){
                                    $acte_medical = "HOSPITALISATION MEDICALE";
                                }elseif($entente_prealable['TYPE_HOSP']=="HO"){
                                    $acte_medical = "HOSPITALISATION OBSTETRICALE";
                                }

                            }else if($entente_prealable['TYPE_EP']=="EXP"){
                                $type_entente = "BIOLOGIE-IMAGERIE";
                                $actes = $ENTENTESPREALABLES->trouver_all_entente($id_entente_prealable,null,null);
                                $acte_medical = "";
                                foreach ($actes as $acte) {
                                    $actes_medicaux = $ACTESMEDICAUX->trouver($acte['CODE_ACTE_MEDICAL']);
                                    //var_dump($actes_medicaux);
                                    $acte_medical = $acte_medical. $acte['CODE_ACTE_MEDICAL'] . ": " . $actes_medicaux['LIBELLE'] . "<br/>";
                                }
                            }

                            if($entente_prealable['STATUT']=="1"){
                                $statut_demande = "VALIDEE";
                                $motif_rejet = "";
                            }else if($entente_prealable['STATUT']=="2"){
                                $statut_demande = "REJETEE";
                                $motif_rejet = $entente_prealable['MOTIF'];
                            }else if($entente_prealable['STATUT']=="0" || $entente_prealable['STATUT']==null){
                                $statut_demande = "EN COURS D'ANALYSE";
                                $motif_rejet = "";
                            }else{
                                $statut_demande = "EXPIRE";
                                $motif_rejet = "";
                            }*/
                            ?>
                            <tr>
                                <td><?= $entente_prealable['numero']; ?></td>
                                <td><b><?= $entente_prealable['entente_prealable']->patient->numero_secu; ?></b></td>
                                <td><?= $entente_prealable['entente_prealable']->patient->nom.' '.$entente_prealable['entente_prealable']->patient->prenoms; ?></td>

                                <td align="left"><b><?php
                                        foreach($entente_prealable['entente_prealable']->actes as $actes){
                                            echo $actes->statut->code;
                                            if($actes->statut->code === 'REF'){$statut =  '<i class="text-danger">'.$actes->statut->denomination.'</i>'.' - Motif: '.$actes->statut->motif;}elseif($actes->statut->code === 'VAL'){$statut = '<i class="text-info">'.$actes->statut->denomination.'</i>';}else{$statut = '<i class="text-info"> EN ATTENTE</i>';}
                                            echo $actes->code.' : '.$statut.'<br>';
                                        }
                                        ?>
                                    </b>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <?php
                    }
                    else{
                        ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <p align="center">AUCUNE DEMANDE TROUVEE</p>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <?php
                    }
                }
            }
        }else{
            ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <p>VOTRE IDENTIFIANT EST INCORRECT. VOUS NE POUVEZ PAS EFFECTUER CETTE ACTION. VEUILLEZ CONTACTER VOTRE ADMINISTRATEUR SYSTEME.</p>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php
        }
    }else{
        ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <p>VOTRE IDENTIFIANT EST INCORRECT. VOUS NE POUVEZ PAS EFFECTUER CETTE ACTION. VEUILLEZ CONTACTER VOTRE ADMINISTRATEUR SYSTEME.</p>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <?php
    }
}else{
    ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <p>VOTRE IDENTIFIANT EST INCORRECT. VOUS NE POUVEZ PAS EFFECTUER CETTE ACTION. VEUILLEZ CONTACTER VOTRE ADMINISTRATEUR SYSTEME.</p>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <?php
}

?>

