<?php
require_once '../../Classes/UTILISATEURS.php';

if(isset($_SESSION['ECMU_USER_ID']) || !empty($_SESSION['ECMU_USER_ID'])) {
    $UTILISATEURS = new UTILISATEURS();
    $user = $UTILISATEURS->trouver($_SESSION['ECMU_USER_ID'],NULL,NULL);
    if(empty($user['ID_UTILISATEUR'])) {
        session_destroy();
        echo '<script>window.location.href="'.URL.'connexion.php"</script>';
    }else {
        $modules = array_diff(explode(';',stream_get_contents($user['PROFIL'],-1)),array(""));
        $nb_modules = count($modules);
        if($nb_modules == 0) {
            session_destroy();
            echo '<script>window.location.href="'.URL.'connexion.php"</script>';
        }else{
            ?>
            <div class="col">
                <div class="col-sm-12">
                    <div class="titres_p">Profil Utilisateur</div>
                    <div class="row" style="margin-top: 30px">
                        <div class="col-sm-6">
                            <p id="resultat_editer_photo_profil"></p>
                            <form enctype="multipart/form-data">
                                <div class="media">
                                    <?php
                                        if(empty($user['IMAGE'])){
                                    ?>
                                    <img  id="image_profil" onclick="openInputFile()"  src="<?=PUBLICS.'images/Icon-user.png';?>" class="mr-3" alt="...">
                                    <?php }else{ ?>
                                            <img  id="image_profil" onclick="openInputFile()" width="100px"  src="<?=PUBLICS.'images/photos_profils/'.$user['NOM'].$user['ID_UTILISATEUR'].'/'. $user['IMAGE']; ?>" class="mr-3" alt="...">
                                    <?php } ?>
                                    <input id="image_input" style="visibility: collapse;  width: 0px;" type="file" >
                                    <div class="media-body">
                                        <h5 class="mt-0"><?= $user['NOM'].' '.$user['PRENOM'] ?></h5>
                                        <p>
                                            <b style="text-decoration: underline">Contacts</b><br />
                                            <i class="fa fa-at"></i> <a href=mailto:<?= $user['EMAIL'];?>"><?= $user['EMAIL'];?></a><br />
                                            <i class="fa fa-mobile"></i> <?= $user['TELEPHONE'];?>
                                        </p>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                    <p class="align_right">
                                        <button type="button"
                                                class="btn btn-danger btn-sm col-sm-6"
                                                data-toggle="collapse"
                                                data-target="#collapseModifierPassword"
                                                aria-expanded="false" aria-controls="collapseModifierPassword" value="">Modifier votre mot de passe
                                        </button>
                                    </p>

                            </div>
                            <div class="collapse" id="collapseModifierPassword">
                                <div class="card card-body">

                                    <p id="resultat_reset_password" class="align_center"></p>
                                    <form class="form-group" id="form_changer_mot_de_passe">

                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label-sm">Ancien mot de passe</label>
                                            <div class="col-sm-9">
                                                <input type="password" id="ancien_mot_de_passe_input" class="form-control form-control-sm" placeholder="Ancien mot de passe" autocomplete="off" value="" />
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label-sm">Nouveau mot de passe</label>
                                            <div class="col-sm-9">
                                                <input type="password" id="nouveau_mot_de_passe_input" class="form-control form-control-sm" placeholder="Nouveau mot de passe" autocomplete="off" value="" />
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label-sm">Confirmer votre mot de passe</label>
                                            <div class="col-sm-9">
                                                <input type="password" id="confirm_nouveau_mot_de_passe_input" class="form-control form-control-sm" placeholder="Nouveau mot de passe" autocomplete="off" value="" />
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <p class="align_right">
                                                <button class="btn btn-success btn-xs btn-sm" type="submit" id="btn_form_changer_mot_de_passe">Enregistrer le mot de passe</button>
                                            </p>
                                        </div>
                                    </form>

                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <hr />
                <div class="row">
                    <div class="col">
                        <nav>
                            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                <?php
                                $a = 1;
                                foreach ($modules as $user_profil) {
                                    $profil = $UTILISATEURS->trouver_profil($user_profil);
                                    ?>
                                    <a class="nav-item nav-link <?php if ($a == 1){echo 'active';} ?>"
                                       id="nav-<?= $user_profil;?>-tab"
                                       data-toggle="tab"
                                       href="#nav-<?= $user_profil;?>"
                                       role="tab"
                                       aria-controls="nav-<?= $user_profil;?>"
                                       aria-selected="false"><?= $profil['LIBELLE'];?></a>
                                    <?php
                                    $a++;
                                }
                                ?>
                            </div>
                            <div class="tab-content" id="nav-tabContent">
                                <?php
                                $b = 1;
                                foreach ($modules as $user_profil) {
                                    $profil = $UTILISATEURS->trouver_profil($user_profil);
                                    ?>
                                    <div class="tab-pane fade <?php if ($b == 1){echo 'show active';} ?>" id="nav-<?= $user_profil;?>" role="tabpanel" aria-labelledby="nav-<?= $user_profil;?>-tab"><br />
                                        <?php
                                        require_once '../../../_configs/Classes/ETABLISSEMENTSSANTE.php';
                                        $ETABLISSEMENTSSANTE = new ETABLISSEMENTSSANTE();

                                        if($profil['CODE'] == 'PS') {?>
                                                <p>

                                                </p>
                                                <?php

                                            if(!empty($user['CODE_PS'])) {
                                                $ps_etablissements = $UTILISATEURS->lister_ps_etablissements($user['CODE_PS']);
                                                $nb_ps_etablissements = count($ps_etablissements);											
                                                if($nb_ps_etablissements == 0) {

                                                }else {
                                                    ?>
                                                    <table class="table table-bordered table-sm">
                                                        <thead class="bg-info">
                                                        <tr>
                                                            <th width="5">N°</th>
                                                            <th width="100">CODE</th>
                                                            <th>RAISON SOCIALE</th>
                                                            <th width="5"></th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <?php
                                                        $ligne = 1;
                                                        foreach ($ps_etablissements as $ps_etablissement) {
                                                            if(empty($ps_etablissement['DATE_EDIT'])) {
                                                                $date_maj = date('d/m/Y H:i',strtotime($ps_etablissement['DATE_REG']));
                                                                $editeur = $UTILISATEURS->trouver($ps_etablissement['USER_REG'],NULL,NULL);
                                                            }else {
                                                                $date_maj = date('d/m/Y H:i',strtotime($ps_etablissement['DATE_EDIT']));
                                                                $editeur = $UTILISATEURS->trouver($ps_etablissement['USER_EDIT'],NULL,NULL);
                                                            }

                                                            $ets = $ETABLISSEMENTSSANTE->trouver($ps_etablissement['CODE_ETS']);
                                                            if($ps_etablissement['STATUT'] == 0) {
                                                                $nouveau_statut = 1;
                                                            }else {
                                                                $nouveau_statut = 0;
                                                            }
															echo $nouveau_statut;
                                                            ?>
                                                            <tr>
                                                                <td align="right"><?= $ligne;?></td>
                                                                <td><a href=""><b><?= $ets['INP'];?></b></a></td>
                                                                <td><?= $ets['RAISON_SOCIALE'];?></td>
                                                                <td><?php if($ps_etablissement['STATUT'] == 1){echo '<b class="fa fa-user text-success"></b>';}else {echo '<b class="fa fa-user-alt-slash text-danger"></b>';} ?></td>
                                                            </tr>
                                                            <?php
                                                            $ligne++;
                                                        }
                                                        ?>
                                                        </tbody>
                                                    </table>
                                                    <?php
                                                }
                                            }
                                            ?>
                                            <div class="modal fade" id="edit_ps_ets_modal" tabindex="-1" role="dialog" aria-labelledby="edit_ps_ets_Label" aria-hidden="true">
                                                <div class="modal-dialog modal-sm" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="edit_ps_ets_Label"></h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p align="center" id="resultat_ps_ets_p"></p>
                                                            <form id="form_ps_ets">
                                                                <div class="form-row align-items-center">
                                                                    <div class="col-sm-12 my-1">
                                                                        <label class="sr-only" for="code_ps_ets_input">Code</label>
                                                                        <div class="input-group input-group-sm">
                                                                            <input type="text" class="form-control form-control-sm" id="code_ps_ets_input" maxlength="9" placeholder="Code" autocomplete="off" readonly required hidden />
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-12 my-1">
                                                                        <label class="sr-only" for="code_ets_ps_input">Code</label>
                                                                        <div class="input-group input-group-sm">
                                                                            <input type="text" class="form-control form-control-sm" id="code_ets_ps_input" maxlength="9" placeholder="Code" autocomplete="off" readonly required hidden />
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-12 my-1">
                                                                        <label class="sr-only" for="code_ets_ps_input">Code</label>
                                                                        <div class="input-group input-group-sm">
                                                                            <select class="form-control form-control-sm" id="code_statut_input" readonly>
                                                                                <option value="">Sélectionner</option>
                                                                                <option value="0">Désactiver</option>
                                                                                <option value="1">Activer</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-12 my-1">
                                                                        <button type="submit" id="button_enregister_ps_ets" class="btn btn-primary btn-sm btn-block"><i class="fa fa-save"></i> Enregistrer</button>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="modal fade" id="ajouter_ets_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-lg" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="exampleModalLabel">Ajouter <?= $user['PRENOM'];?> à un centre de santé</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p align="center" id="resultat_ets_p"></p>
                                                            <form id="form_ets">
                                                                <div class="form-row align-items-center">
                                                                    <div class="col-sm-2 my-1">
                                                                        <label class="sr-only" for="code_ets_input">Code</label>
                                                                        <div class="input-group input-group-sm">
                                                                            <input type="text" class="form-control form-control-sm" id="code_ets_input" maxlength="9" placeholder="Code" autocomplete="off" required />
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-8 my-1">
                                                                        <label class="sr-only" for="raison_sociale_input">Raison sociale</label>
                                                                        <div class="input-group input-group-sm">
                                                                            <input type="text" class="form-control form-control-sm" id="raison_sociale_input" placeholder="Raison sociale" autocomplete="off" readonly required />
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-2 my-1">
                                                                        <button type="submit" id="button_enregister_ets" class="btn btn-primary btn-sm btn-block"><i class="fa fa-save"></i> Enregistrer</button>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="modal fade" id="code_ps_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-lg" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="exampleModalLabel">Code PS de <?= $user['PRENOM'];?></h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p align="center" id="resultat_ps_p"></p>
                                                            <form id="form_ps">
                                                                <div class="form-row align-items-center">
                                                                    <div class="col-sm-2 my-1">
                                                                        <label class="sr-only" for="code_ps_input">Code</label>
                                                                        <div class="input-group input-group-sm">
                                                                            <input type="text" class="form-control form-control-sm" id="code_ps_input" value="<?= $user['CODE_PS'];?>" maxlength="9" placeholder="Code" autocomplete="off" required />
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-8 my-1">
                                                                        <label class="sr-only" for="nom_prenom_ps_input">Nom & Prénom(s)</label>
                                                                        <div class="input-group input-group-sm">
                                                                            <input type="text" class="form-control form-control-sm" id="nom_prenom_ps_input" placeholder="Nom & Prénom(s)" autocomplete="off" readonly required />
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-2 my-1">
                                                                        <button type="submit" id="button_enregister_ps" class="btn btn-primary btn-sm btn-block"><i class="fa fa-save"></i> Enregistrer</button>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                        if($profil['CODE'] == 'DCS') {

                                        }
                                        if($profil['CODE'] == 'AGAC') {
                                            $fse_habilitations = preg_split('/;/',$user['FSE'],'-1',PREG_SPLIT_NO_EMPTY);
                                            $agent_etablissements = $UTILISATEURS->trouver_agent_ets($user['ID_UTILISATEUR']);
                                            $nb_agent_etablissements = count($agent_etablissements);
                                            if($nb_agent_etablissements != 0) {
                                                ?>
                                                <table class="table table-bordered table-hover table-sm">
                                                    <thead class="bg-info">
                                                    <tr>
                                                        <th width="5">N°</th>
                                                        <th width="100">CODE</th>
                                                        <th>RAISON SOCIALE</th>
                                                        <th width="100">DATE DEBUT</th>
                                                        <th width="100">DATE FIN</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php
                                                    $ligne = 1;
                                                       $ets = $ETABLISSEMENTSSANTE->trouver($agent_etablissements['CODE_ETS']);
                                                        ?>
                                                        <tr class="<?php if(!empty($agent_etablissements['DATE_FIN_VALIDITE'])){echo 'list-group-item-primary';};?>">
                                                            <td align="right"><?= $ligne;?></td>
                                                            <td><b><?= $agent_etablissements['CODE_ETS'];?></b></td>
                                                            <td><?= $ets['RAISON_SOCIALE'];?></td>
                                                            <td><?= date('d/m/Y',strtotime($agent_etablissements['DATE_DEBUT_VALIDITE']));?></td>
                                                            <td><?php if(!empty($agent_etablissements['DATE_FIN_VALIDITE'])){echo date('d/m/Y',strtotime($agent_etablissements['DATE_FIN_VALIDITE']));}?></td>
                                                        </tr>
                                                        <?php
                                                        $ligne++;
//                                                    }
                                                    ?>
                                                    </tbody>
                                                </table>
                                                <?php
                                            }

                                            ?>
                                            <div class="modal fade" id="code_agent_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-sm" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="exampleModalLabel">Code AGENT de <?= $user['PRENOM'];?></h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p align="center" id="resultat_agent_p"></p>
                                                            <form id="form_agent">
                                                                <div class="form-row align-items-center">
                                                                    <div class="col-sm-12 my-1">
                                                                        <label class="sr-only" for="code_agent_input">Code</label>
                                                                        <div class="input-group input-group-sm">
                                                                            <input type="text" class="form-control form-control-sm" id="code_agent_input" value="<?php
                                                                            if(empty($user['CODE_AGENT'])){
                                                                                $nouveau_code = $UTILISATEURS->generer_code_agent();
                                                                                echo $nouveau_code['CODE_AGENT'];
                                                                            }else {
                                                                                echo $user['CODE_AGENT'];
                                                                            }
                                                                            ?>" placeholder="Code" autocomplete="off" required disabled />
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-12 my-1">
                                                                        <button type="submit" id="button_enregister_agent" class="btn btn-primary btn-sm btn-block"><i class="fa fa-save"></i> Enregistrer</button>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="modal fade" id="modifier_agent_ets_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-lg" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="exampleModalLabel">Modifier le centre de santé de <?= $user['PRENOM'];?></h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p align="center" id="resultat_agent_ets_p"></p>
                                                            <form id="form_agent_ets">
                                                                <div class="form-row align-items-center">
                                                                    <div class="col-sm-2 my-1">
                                                                        <label class="sr-only" for="code_agent_ets_input">Code</label>
                                                                        <div class="input-group input-group-sm">
                                                                            <input type="text" class="form-control form-control-sm" id="code_agent_ets_input" maxlength="9" placeholder="Code" autocomplete="off" required />
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-8 my-1">
                                                                        <label class="sr-only" for="raison_sociale_agent_input">Raison sociale</label>
                                                                        <div class="input-group input-group-sm">
                                                                            <input type="text" class="form-control form-control-sm" id="raison_sociale_agent_input" placeholder="Raison sociale" autocomplete="off" readonly required />
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-2 my-1">
                                                                        <button type="submit" id="button_enregister_agent_ets" class="btn btn-primary btn-sm btn-block"><i class="fa fa-save"></i> Enregistrer</button>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                        if($profil['CODE'] == 'OGDP') {

                                        }
                                        if($profil['CODE'] == 'CSAI') {

                                        }
                                        if($profil['CODE'] == 'ENT') {

                                        }
                                        ?>
                                    </div>
                                    <?php
                                    $b++;
                                }
                                ?>
                            </div>
                        </nav>
                    </div>
                </div>
            </div>
            <?php
        }
    }

}else{
    session_destroy();
    echo '<script>window.location.href="'.URL.'connexion.php"</script>';
}
?>

<script type="text/javascript">

    function openInputFile(){
        if(confirm("Voulez vous changer votre photo de profil")){
            $("#image_input").click();
            upload_photo_profil();
        }
    }

    function upload_photo_profil(){
        $("#image_input").change(function(){
            var file_data = $('#image_input').prop('files')[0];
            var form_data = new FormData();
            form_data.append('file', file_data);
            $.ajax({
                url: '_configs/Includes/Submits/submit_editer_photo_profil.php',
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                data: form_data,
                type: 'post',
                success: function (data) {
                    if(data['status'] == true) {
                        $("#form_editer_administrateur").hide();
                        $("#resultat_editer_photo_profil").removeClass('alert alert-danger');
                        $("#resultat_editer_photo_profil").addClass('alert alert-success');
                        $("#resultat_editer_photo_profil").html(data['message']);
                        setTimeout(function () {
                            window.location.href = "profil.php";
                        }, 1000);
                    }else {
                        $("#resultat_editer_photo_profil").removeClass('alert alert-success');
                        $("#resultat_editer_photo_profil").addClass('alert alert-danger');
                        $("#resultat_editer_photo_profil").html(data['message']);
                    }
                }
            });
            return false;
        });
    }


    $("#form_changer_mot_de_passe").submit(function () {
        var $btn = $("#btn_form_changer_mot_de_passe").button('loading'),
            ancien_mot_de_passe = $("#ancien_mot_de_passe_input").val(),
            mot_de_passe = $("#nouveau_mot_de_passe_input").val(),
            mot_de_passe2 = $("#confirm_nouveau_mot_de_passe_input").val();

        if(mot_de_passe !== mot_de_passe2){
            $("#resultat_reset_password").html('<b class="alert alert-danger">LES DEUX MOTS DE PASSE SAISIS NE SONT IDENTIQUES. PRIERE VERIFIER.</b></div>');
            $("#confirm_nouveau_mot_de_passe_input").val('');
        }else{
            $.ajax({
                url: '_configs/Includes/Submits/submit_modifier_mot_de_passe.php',
                type: 'POST',
                data: {
                    'mot_de_passe':mot_de_passe,
                    'ancien_mot_de_passe':ancien_mot_de_passe,
                    'mot_de_passe2':mot_de_passe2
                },
                dataType: 'json',
                success: function (data) {
                    $btn.button('reset');
                    if(data['status'] == true) {
                        $("#btn_form_changer_mot_de_passe").prop('disabled',true);
                        $("#form_changer_mot_de_passe_administrateur").hide();
                        $("#resultat_reset_password").removeClass('alert alert-danger');
                        $("#resultat_reset_password").addClass('alert alert-success');
                        $("#resultat_reset_password").html('MOT DE PASSE MODIFIER AVEC SUCCES.<br />');
                        $.ajax({
                            url: '_configs/Includes/Submits/submit_deconnexion.php',
                            dataType: 'json',
                            success: function (data) {
                                if(data['status'] == true) {
                                    setTimeout(function () {
                                        window.location.reload();
                                    }, 3000);
                                }
                            }
                        });
                    }else {
                        $("#resultat_reset_password").removeClass('alert alert-success');
                        $("#resultat_reset_password").addClass('alert alert-danger');
                        $("#resultat_reset_password").html(data['message']);
                    }
                }
            });
        }
        return false;
    });

</script>
