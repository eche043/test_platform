<?php
use GuzzleHttp\Pool;
use GuzzleHttp\Client;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\RequestException;
require_once '../../../Classes/UTILISATEURS.php';
require '../../../../vendor/autoload.php';
if(isset($_SESSION['ECMU_USER_ID'])) {
    $session_user = $_SESSION['ECMU_USER_ID'];
    if (!empty($session_user)) {
        $UTILISATEURS = new UTILISATEURS();
        $utilisateur_existe = $UTILISATEURS->trouver($session_user, null, null);
        $utilisateur_existe['CODE_OGD_P'];
        if (!empty($utilisateur_existe['ID_UTILISATEUR'])) {
            $ententePrealable = array();
            if(isset($_POST['numero'])) {
                require_once '../../../Classes/ACTESMEDICAUX.php';
                require_once '../../../Classes/ENTENTESPREALABLES.php';
                require_once '../../../Classes/ASSURES.php';
                require_once '../../../Classes/ETABLISSEMENTSSANTE.php';
                $ACTESMEDICAUX = new ACTESMEDICAUX();
                $ENTENTESPREALABLES = new ENTENTESPREALABLES();
                $ASSURES= new ASSURES();
                $ETABLISSEMENTSSANTE = new ETABLISSEMENTSSANTE();

                //$ententePrealable = $ENTENTESPREALABLES->trouver_entente_prealable($_POST["numero"]);
                $client = new Client([
                    'timeout' => 60,
                    'verify' => false
                ]);
                $headers = [
                    'Authorization' => 'Bearer 1|pC8jCa84ZSYMjg4fwKWluPKzIWOFRMuSB81dX9ci316eec2b',
                    'accept' => 'application/json'
                ];

                $request = new Request('GET', 'https://10.10.4.85:3128/api/prestations/ententes-prealables/'.$_POST["numero"], $headers);

                /*if($utilisateur_existe['CODE_OGD_P'] != $ententePrealable['CODE_OGD']){
                    echo '<script>window.location.href="'.URL.'"</script>';
                }else{
                    $assure = $ASSURES->trouver($ententePrealable['NUM_SECU']);
                    $ets = $ETABLISSEMENTSSANTE->trouver_etablissement_sante($ententePrealable['CODE_ETS']);
                }*/

                try{
                    $res = $client->sendAsync($request)->wait();
                    $reponse = json_decode($res->getBody());

                    if(isset($reponse->numero)){
                        $ententePrealable = array(
                            'status' => true,
                            'numero' => $reponse->numero,
                            'entente_prealable' => $reponse,
                        );
                    }
                    else{
                        $ententePrealable = array(
                            'status' => $reponse['success'],
                            'message' => $reponse['message'],
                            'numero'=>null
                        );
                    }
                }
                catch (\Exception $e){
                    //$json = ;
                    $ententePrealable = array(
                        'status' => false,
                        'message1' => $e->getMessage(),
                        'numero'=>null,
                        'message' => "UNE ERREUR EST SURVENUE LORS DE LA CONSULTATION DES INFORMATIONS."
                    );
                }
            }
            //var_dump(array($ententePrealable));

            $modules = array_diff(explode(';', stream_get_contents($utilisateur_existe['PROFIL'], -1)), array(""));
            $nb_modules = count($modules);
            if ($nb_modules == 0) {
                session_destroy();
                echo '<script>window.location.href="' . URL . '"</script>';
            }
            else {
                if (in_array('OGDP', $modules)) {
                    $user_hab = explode(';', $utilisateur_existe['FSE']);
                    if (in_array('RPT', $user_hab)) {
                        ?>
                        <input type="hidden" id="num_ep_input" value="<?= $_POST['numero']; ?>"/>
                        <input type="hidden" id="user_ogd_input" value="<?= $utilisateur_existe['CODE_OGD_P']; ?>"/>
                        <p class="titres_p"><i class="fa fa-handshake"></i> Demande entente préalable N° <?=$_POST["numero"];?></p>
                        <?php
                        if (!empty($ententePrealable['numero'])) {
                            $entente_prealable = $ententePrealable['entente_prealable'];
                            if(empty($entente_prealable->actes[0]->statut->code)) {
                                ?>
                               <!-- <div class="align-middle">
                                    <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#btn_valider_ep" title="Valider"><b class="fa fa-check"></b> Valider la Demande</button>
                                    <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#btn_refuser_ep" title="Réfuser"><b class="fa fa-minus-circle"></b> Réfuser la Demande</button>
                                    <hr>
                                </div>-->
                                <div class="modal fade" id="btn_valider_ep" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title" id="myModalLabel">Valider L'entente préalable N° <?= $_POST['numero'];?></h4>
                                            </div>
                                            <div class="modal-body">
                                                <p align="center" id="p_resultats_validation"></p>
                                                <form class="form-horizontal" id="form_valider_ep">
                                                    <?php
                                                    /*if($ententePrealable['TYPE_EP'] == 'EXP') {
                                                        $c = $ENTENTESPREALABLES->trouver_liste_actes_entente_par_numero($ententePrealable['NUM_ENTENTE_PREALABLE']);
                                                        */?><!--
                                                        <table class="table table-striped table-sm">
                                                            <thead class="alert alert-primary">
                                                            <tr>
                                                                <th width="50">N°</th>
                                                                <th>CODE</th>
                                                                <th>LIBELLE</th>
                                                                <th width="50">VALIDER</th>
                                                                <th width="50">REFUSER</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            <?php
/*                                                            $ligne1 = 1;
                                                            foreach ($c as $acte_a_valide) {
                                                                $acte_medical_a_valider = $ACTESMEDICAUX->trouver_un_acte($acte_a_valide['CODE_ACTE_MEDICAL']);
                                                                */?>
                                                                <tr>
                                                                    <td><?php /*= $ligne1;*/?></td>
                                                                    <td id="code_acte_td"><b style="color: #FF0000"><?php /*= $acte_a_valide['CODE_ACTE_MEDICAL'];*/?></b></td>
                                                                    <td><?php /*= $acte_medical_a_valider['LIBELLE'];*/?></td>
                                                                    <td style="text-align: right">
                                                                        <input type="radio" name="optionsRadios<?php /*=$acte_a_valide['CODE_ACTE_MEDICAL'];*/?>" id="val_<?php /*=$acte_a_valide['CODE_ACTE_MEDICAL'];*/?>" value="<?php /*=$acte_a_valide['CODE_ACTE_MEDICAL'];*/?>" class="optionsRadios" checked>
                                                                    </td>
                                                                    <td style="text-align: right">
                                                                        <input type="radio" name="optionsRadios<?php /*=$acte_a_valide['CODE_ACTE_MEDICAL'];*/?>" id="ref_<?php /*=$acte_a_valide['CODE_ACTE_MEDICAL'];*/?>" value="<?php /*=$acte_a_valide['CODE_ACTE_MEDICAL'];*/?>" class="optionsRadios">
                                                                    </td>
                                                                </tr>
                                                                <tr id="tr_motif_<?php /*=$acte_a_valide['CODE_ACTE_MEDICAL'];*/?>" hidden>
                                                                    <td colspan="5">
                                                                        <textarea class="form-control motif" id="motif_<?php /*=$acte_a_valide['CODE_ACTE_MEDICAL'];*/?>" placeholder="Motif du refus pour <?php /*= $acte_medical_a_valider['LIBELLE'];*/?>"></textarea>
                                                                    </td>
                                                                </tr>
                                                                <?php
/*                                                                $ligne1++;
                                                            }
                                                            */?>
                                                            </tbody>
                                                        </table>
                                                        <?php
/*                                                    }else {
                                                        */?>
                                                        <div class="form-group">
                                                            <div class="col-sm-12">
                                                                <b>Voulez-vous vraiment valider cette demande ?</b>
                                                            </div>
                                                        </div>
                                                        --><?php
/*                                                    }*/
                                                    ?>
                                                    <div class="form-group">
                                                        <div class="col-sm-12">
                                                            <button type="submit" class="btn btn-primary btn-sm" data-loading-text="Validation en cours..." id="btn_validation_valide">Vailder</button>
                                                            <a class="btn btn-light btn-sm" href="<?= URL.'ogd-prestations/details-entente-prealable.php?numero='.$_POST['numero'];?>">Annuler</a>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal fade" id="btn_refuser_ep" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title" id="myModalLabel">Refuser L'entente préalable N° <?= $_POST['numero'];?></h4>
                                            </div>
                                            <div class="modal-body">
                                                <p align="center" id="p_resultats_refus"></p>
                                                <form class="form-horizontal" id="form_refuser_ep">
                                                    <div class="form-group">
                                                        <div class="col-sm-12">
                                                            <b>Motif du refus</b>
                                                            <textarea class="form-control" id="motif_refus_input" placeholder="Saisissez ici le motif de refus de la demande" required></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="col-sm-12">
                                                            <button type="submit" class="btn btn-primary btn-sm" data-loading-text="Validation en cours..." id="btn_validation_refus">Valider</button>
                                                            <a class="btn btn-light btn-sm" href="<?= URL.'ogd-prestations/details-entente-prealable.php?numero='.$_POST['numero'];?>">Annuler</a>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            } ?>


                            <table class="table table-bordered table-hover table-sm">
                                <tr>
                                    <td colspan="2" class="alert alert-info" align="center"><b>ASSURE</b></td>
                                </tr>
                                <tr>
                                    <td class="table_left_title" width="200">Numéro sécu</td>
                                    <td><b style="color: #FF0000"><?= $entente_prealable->patient->numero_secu;?></b></td>
                                </tr>
                                <tr>
                                    <td class="table_left_title">Nom</td>
                                    <td><b><?= $entente_prealable->patient->nom;?></b></td>
                                </tr>
                                <tr>
                                    <td class="table_left_title">Prénom(s)</td>
                                    <td><b><?= $entente_prealable->patient->prenoms;?></b></td>
                                </tr>
                                <tr>
                                    <td class="table_left_title">Sexe</td>
                                    <td>
                                        <b>
                                        <?= $entente_prealable->patient->genre;?>
                                        </b>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="table_left_title">Date de naissance</td>
                                    <td><b><?= date('d/m/Y',strtotime($entente_prealable->patient->date_naissance));?></b></td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="alert alert-info" align="center"><b>ETABLISSEMENT</b></td>
                                </tr>
                                <tr>
                                    <td class="table_left_title" width="200">Code</td>
                                    <td><b style="color: #FF0000"><?= $entente_prealable->etablissement->code;?></b></td>
                                </tr>
                                <tr>
                                    <td class="table_left_title" width="200">Raison sociale</td>
                                    <td><b><?= $entente_prealable->etablissement->denomination;?></b></td>
                                </tr>
                                <!--<tr>
                                    <td colspan="2" class="alert alert-danger" align="center"><b>TYPE DE DEMANDE</b></td>
                                </tr>
                                <tr>
                                    <td colspan="2" align="center">
                                        <b style="color: #00cc00">
                                            <?php
/*                                            if($ententePrealable['TYPE_EP'] == 'HOS') {
                                                echo 'HOSPITALISATION';
                                            }else {
                                                echo 'BIOLOGIE / IMAGERIE';
                                            }
                                            */?>
                                        </b>
                                    </td>
                                </tr>-->
                                <!--<tr>
                                    <td colspan="2" class="alert alert-danger" align="center"><b>MOTIF DE LA DEMANDE</b></td>
                                </tr>
                                <tr>
                                    <td colspan="2"><?php /*= $ententePrealable['MOTIF_DEMANDE'];*/?></td>
                                </tr>-->

                                    <tr>
                                        <td colspan="2" class="alert alert-danger" align="center"><b>ACTE DEMANDE</b></td>
                                    </tr>
                                    <?php
                                    $i=0;
                                    foreach ($entente_prealable->actes as $acte) {
                                        ?>
                                        <tr>
                                            <td class="table_left_title" width="200">Code</td>
                                            <td><b style="color: #FF0000"><?= $acte->code;?></b> ( <?php if(empty($acte->statut->code)){echo'<b class="text-warning">EN ATTENTE </b><button class="badge badge-warning btn_editer_ep" id="#btn_editer_ep_'.$acte->code.'" title="Editer"><b class="fa fa-edit"></b></button>';}elseif($acte->statut->code=='VAL'){echo "<b class='text-success'>DEMANDE VALIDEE</b>";}elseif($acte->statut->code=='REF'){echo "<b class='text-warning'>DEMANDE REJETEE&#10145;MOTIF: </b><i>".$acte->statut->motif."</i>";}?> )</td>
                                        </tr>
                                        <tr>
                                            <td class="table_left_title" width="200">Libellé</td>
                                            <td><b><?= $acte->denomination;?></b></td>
                                        </tr>
                                        <tr hidden id="tr_validation_<?=$acte->code;?>">
                                            <td class="table_left_title" width="200"></td>
                                            <td>
                                                <div class="form-group row">
                                                    <div class="col">
                                                        <div class="form-check form-check-inline">
                                                            <input type="radio" name="optionsRadios<?=$acte->code;?>" id="val_<?=$acte->code;?>" value="VAL_<?=$acte->code;?>" class="optionsRadios">
                                                            <label class="form-check-label col-form-label-sm" >VALIDER</label>
                                                        </div>
                                                    </div>
                                                    <div class="col">
                                                        <div class="form-check form-check-inline">
                                                            <input type="radio" name="optionsRadios<?=$acte->code;?>" id="ref_<?=$acte->code;?>" value="REF_<?=$acte->code;?>" class="optionsRadios">
                                                            <label class="form-check-label col-form-label-sm" >REJETER</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr id="tr_motif_<?=$acte->code;?>" hidden>
                                            <td></td>
                                            <td><textarea class="form-control motif" id="motif_<?=$acte->code;?>" placeholder="Motif du refus"></textarea></td>
                                        </tr>
                                        <?php
                                        $i++;
                                    }
                                    ?>
                                <input type="hidden" value="<?=$i;?>" id="total_demandes_ep">
                                </table>
                                <div class="align-middle"><hr>
                                    <button class="btn btn-info btn-sm" id="btn_enregistrer_ep" title="Valider"><b class="fa fa-check"></b> Enregistrer</button>
                                </div>
                            <?php
                        }
                        else{
                            echo '<p class="alert alert-danger">'.$ententePrealable['message'].'</p>';
                        }
                        ?>
                            <script type="text/javascript" src="<?= JS.'page_entente_prealable.js'?>"></script>
						<?php
                    }else {
                        echo '<script>window.location.href="' . URL . '"</script>';
                    }
                }else {
                    echo '<script>window.location.href="' . URL . '"</script>';
                }
            }
        }else{
            session_destroy();
            echo '<script>window.location.href="' . URL . '"</script>';
        }
    }else{
        session_destroy();
        echo '<script>window.location.href="' . URL . '"</script>';
    }
}
?>

