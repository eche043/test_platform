<?php
require_once '../../../Classes/UTILISATEURS.php';

if(isset($_SESSION['ECMU_USER_ID']) || !empty($_SESSION['ECMU_USER_ID'])) {
    $UTILISATEURS = new UTILISATEURS();
    $user = $UTILISATEURS->trouver($_SESSION['ECMU_USER_ID'],NULL,NULL);
    if(empty($user['ID_UTILISATEUR'])) {
        session_destroy();
        echo '<script>window.location.href="'.URL.'"</script>';
    }else {
        $modules = array_diff(explode(';',stream_get_contents($user['PROFIL'],-1)),array(""));
        $nb_modules = count($modules);
        if($nb_modules == 0) {
            session_destroy();
            echo '<script>window.location.href="'.URL.'"</script>';
        }else{
            if(in_array('APA',$modules)) {
                $user_apa = $UTILISATEURS->trouver_utilisateur_partenaire($user['ID_UTILISATEUR']);
                if(count($user_apa)!=0) {
                    require_once '../../../Classes/PARTENAIRES.php';
                    require_once '../../../Classes/OGD.php';
                    require_once '../../../Classes/ASSURES.php';
                    $PARTENAIRES = new PARTENAIRES();
                    $OGD = new OGD();
                    $ASSURES = new ASSURES();
                    $partenaire = $PARTENAIRES->trouver($user_apa['CODE_PARTENAIRE']);
                    $user_hab = explode(';',$user['FSE']);
                    if(in_array('DUPLICATA', $user_hab)) {
                        $demande = $PARTENAIRES->trouver_reedition_carte($_POST["id"]);
                        $assure = $ASSURES->trouver($demande["NUM_SECU"]);
                        $coordonnees_assure = $ASSURES->trouver_coordonnees_numero_mobile($demande["NUM_SECU"]);
                        $ogd = $OGD->trouver('AFFL',$assure['CODE_OGD_COTISATIONS']);
                        ?>
                        <div class="col">
                            <p class="titres_p"><i class="fa fa-handshake"></i> Partenaires: <b class="text-danger"><?=$partenaire['LIBELLE_PARTENAIRE'];?></b></p>
                            <hr />
                            <div class="col-sm-12">
                                <div class="row">
                                    <div class="col-sm-8">
                                        <table class="table table-bordered table-hover table-sm">
                                            <thead class="bg-secondary">
                                            <tr>
                                                <th>DATE DE LA DEMANDE</th>
                                                <th><?='le '.date('d/m/Y',strtotime($demande['DATE_REG'])).' à '.date(' H:i',strtotime($demande['DATE_REG']));?></th>
                                            </tr>
                                            <?php if ($demande["STATUT_VALIDATION"] == "2"){ ?>
                                                <tr class="bg-danger text-white">
                                                    <th>MOTIF REJET</th>
                                                    <th><?= $demande["MOTIF_REJET"] ?></th>
                                                </tr>
                                            <?php } ?>
                                            </thead>
                                            <tr>
                                                <td colspan="2" class="td_table_info bg-info" align="center"><b>ASSURE</b></td>
                                            </tr>
                                            <tr>
                                                <td class="table_left_title" width="200">Numéro suivi</td>
                                                <td><b style="color: #FF0000"><?= $demande['ID_DEMANDE']?></b></td>
                                            </tr>
                                            <tr>
                                                <td class="table_left_title" width="200">Numéro sécu</td>
                                                <td><b style="color: #FF0000" id="numero_secu_b"><?= $demande['NUM_SECU'];?></b></td>
                                            </tr>
                                            <tr>
                                                <td class="table_left_title">Civilité</td>
                                                <td>
                                                    <?php

                                                    $civilite = $ASSURES->trouver_assure_civilite($assure['CIVILITE']);
                                                    echo '<b>'.$civilite['LIBELLE'].'</b>';
                                                    ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="table_left_title">Nom</td>
                                                <td>
                                                    <?php
                                                        if( $assure['NOM'] == $demande['NOM']){
                                                            echo '<b class="text-success">'.$assure['NOM'].'</b>';
                                                        }else{
                                                            echo '<b class="text-dark">'.$assure['NOM'].'</b> <i class="text-danger">('.$demande['NOM'].')</i>';
                                                        }
                                                    ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="table_left_title">Prénom(s)</td>
                                                <td>
                                                    <?php
                                                    if( $assure['PRENOM'] == $demande['PRENOMS']){
                                                        echo '<b class="text-success">'.$assure['PRENOM'].'</b>';
                                                    }else{
                                                        echo '<b class="text-dark">'.$assure['PRENOM'].'</b> <i class="text-danger">('.$demande['PRENOMS'].')</i>';
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="table_left_title">Sexe</td>
                                                <td>
                                                    <?php
                                                    $sexe = $ASSURES->trouver_genre($assure['SEXE']);
                                                    echo '<b>'.$sexe['LIBELLE'].'</b>';
                                                    ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="table_left_title">Date de naissance</td>
                                                <td>
                                                    <?php
                                                    if( $assure['DATE_NAISSANCE'] == $demande['DATE_NAISSANCE']){
                                                        echo '<b class="text-success">'.date('d/m/Y',strtotime($assure['DATE_NAISSANCE'])).'</b>';
                                                    }else{
                                                        echo '<b class="text-dark">'.date('d/m/Y',strtotime($assure['DATE_NAISSANCE'])).'</b> <i class="text-danger">('.date('d/m/Y',strtotime($demande['DATE_NAISSANCE'])).')</i>';
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="table_left_title">Téléphone</td>
                                                <td><b><?=$demande['NUM_TELEPHONE']; ?></b></td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" class="td_table_danger bg-danger" align="center"><b>MOTIF DE LA DEMANDE</b></td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" align="center">
                                                    <?php
                                                    $motif = $PARTENAIRES->trouver_motif($demande['MOTIF_DEMANDE']);
                                                    echo '<b style="color: #00cc00">'.$motif['MOTIF_LIBELLE'].'</b>';
                                                    if($motif['STATUT_REENROLEMENT']=='1'){
                                                        if(!empty($demande['GUID_ENROLEMENT'])){
                                                            echo '- (GUID : <b style="color: #FF0000" style="text-decoration: underline;">'.$demande['GUID_ENROLEMENT'].'</b>)';
                                                        }else{
                                                            echo ' - (<b style="color: #FF0000"> REENROLEMENT REQUIS</b> <button class="badge badge-warning" data-toggle="modal" data-target="#modal_maj_guid"><i class="fa fa-edit"></i></button> )';
                                                        }
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                        </table>
                                        <div class="modal fade" id="modal_maj_guid" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="majGuidModalLabel">Renseigner le GUID</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div id="resultat_form_maj_guid"></div>
                                                        <form id="form_maj_guid">
                                                            <div class="form-row align-items-center">
                                                                <div class="col-sm-12 my-1">
                                                                    <input class="form-control form-control-sm" type="text" id="guid_input" value="" placeholder="GUID Réenrôlement" autocomplete="off" required>
                                                                </div>

                                                                <br><br>
                                                                <div class="col-sm-4"></div>
                                                                <div class="col-sm-3">
                                                                    <div class="input-group input-group-sm">
                                                                        <button type="submit" class="form-control form-control-sm btn btn-success btn-sm" id="button_valider_guid">Valider</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="card">
                                            <div class="card-body">
                                                <h4>DOCUMENTS JOINTS</h4>
                                                <?php
                                                $lien1 = ''; $lien2 = ''; $lien3 = '';

                                                if(!empty($demande['SCAN_PIECE'])){
                                                    if (file_exists(DIR . 'IMPORTS/DUPLICATA/' . $demande['NUM_SECU'] . '/'.$demande['SCAN_PIECE'])) {
                                                        $lien = URL . 'IMPORTS/DUPLICATA/' . $demande['NUM_SECU'] . '/'.$demande['SCAN_PIECE'];
                                                    } else {
                                                        $lien = URL_ECNAM . 'IMPORTS/DUPLICATA/' . $demande['NUM_SECU'] . '/'.$demande['SCAN_PIECE'];
                                                    }
                                                    $lien1 = $lien;
                                                    $extension1 = strtolower(strrchr($demande['SCAN_PIECE'],'.'));
                                                    if($extension1=='pdf' || $extension1=='PDF') {
                                                        ?>
                                                        <hr>PIECE D'IDENTITE : <b><a href="<?=$lien;?>" download="<?=$demande['SCAN_PIECE'];?>" id="btn_image_1" ><?=$demande['TYPE_PIECE'];?></a></b> <hr>
                                                        <?php
                                                    }else{
                                                        ?>
                                                        <hr>PIECE D'IDENTITE : <b><a href="#" id="<?=$demande['SCAN_PIECE'];?>" data-toggle="modal" data-target="#modal_type_piece" class="class_type_type"><?=$demande['TYPE_PIECE'];?></a></b> <hr>
                                                        <?php
                                                    }
                                                }
                                                if(!empty($demande['SCAN_CARTE_CMU'])){
                                                    if (file_exists(DIR . 'IMPORTS/DUPLICATA/' . $demande['NUM_SECU'] . '/'.$demande['SCAN_CARTE_CMU'])) {
                                                        $lien = URL . 'IMPORTS/DUPLICATA/' . $demande['NUM_SECU'] . '/'.$demande['SCAN_CARTE_CMU'];
                                                    } else {
                                                        $lien = URL_ECNAM . 'IMPORTS/DUPLICATA/' . $demande['NUM_SECU'] . '/'.$demande['SCAN_CARTE_CMU'];
                                                    }
                                                    $lien2 = $lien;
                                                    $extension2 = strtolower(strrchr($demande['SCAN_CARTE_CMU'],'.'));
                                                    if($extension2=='pdf' || $extension2=='PDF') {
                                                        ?>
                                                        CARTE CMU : <b><a href="<?=$lien;?>" download="<?=$demande['SCAN_CARTE_CMU'];?>" id="btn_image_2" ><i class="fa fa-eye"></i></a></b> <hr>
                                                        <?php
                                                    }else{
                                                    ?>
                                                    CARTE CMU : <b> <a href="#" id="btn_image_2" data-toggle="modal" data-target="#modal_type_piece" ><i class="fa fa-eye"></i></a> <hr>
                                                        <?php
                                                    }
                                                }
                                                if(!empty($demande['SCAN_DECLARATION_PERTE'])){
                                                    if (file_exists(DIR . 'IMPORTS/DUPLICATA/' . $demande['NUM_SECU'] . '/'.$demande['SCAN_DECLARATION_PERTE'])) {
                                                        $lien = URL . 'IMPORTS/DUPLICATA/' . $demande['NUM_SECU'] . '/'.$demande['SCAN_DECLARATION_PERTE'];
                                                    } else {
                                                        $lien = URL_ECNAM . 'IMPORTS/DUPLICATA/' . $demande['NUM_SECU'] . '/'.$demande['SCAN_DECLARATION_PERTE'];
                                                    }
                                                    $lien3 = $lien;
                                                    $extension3 = strtolower(strrchr($demande['SCAN_DECLARATION_PERTE'],'.'));
                                                    if($extension3=='.pdf' || $extension3=='.PDF') {
                                                        ?>
                                                        DECLARATION DE PERTE : <b><a href="<?=$lien;?>" target="_blank" download="<?=$demande['SCAN_DECLARATION_PERTE'];?>" id="btn_image_3" ><i class="fa fa-eye"></i></a></b> <hr>
                                                        <?php
                                                    }else{
                                                        ?>
                                                        DECLARATION DE PERTE : <b><a href="#" id="btn_image_3" data-toggle="modal" data-target="#modal_type_piece" ><i class="fa fa-eye"></i></a></b> <hr>
                                                        <?php
                                                    }
                                                }
                                                ?>

                                            </div>
                                        </div>
                                        <div class="modal fade" id="modal_type_piece" style="background-color: rgb(0,0,0); background-color: rgba(0,0,0,0.9);" tabindex="-1" role="dialog" aria-labelledby="typePieceModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header" style="">
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body" style="background-color: transparent;" id="typePieceModalBody">
                                                        <div id="img_1">
                                                            <?php
                                                            echo '<img src="'.$lien1 .'" style="width: 100%;">';
                                                            ?>
                                                        </div>
                                                        <div id="img_2">
                                                            <?php
                                                            echo '<img src="'.$lien2 .'" style="width: 100%;">';
                                                            ?>
                                                        </div>
                                                        <div id="img_3">
                                                            <?php
                                                            echo '<img src="'.$lien3 .'" style="width: 100%;">';
                                                            ?>

                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                        if($demande['STATUT_VALIDATION'] == 1) {
                                            ?>
                                            <br>
                                            <div class="card">
                                                <div class="card-body">
                                                    <h4>SUIVI</h4>
                                                    <hr>

                                                    <div class="col-sm-12">
                                                               <?php
                                                               if($demande['STATUT_TRANSMISSION'] == null){
                                                                   $partenaire = $PARTENAIRES->editer_statut_transmission($_SESSION['ECMU_USER_ID'],$demande['NUM_SECU'],$demande['ID_DEMANDE']);
                                                                   //echo '<p>TRAITEMENT EN COURS : <strong class="text-success"><i class="fa fa-check" aria-hidden="true"></i>'.date('d/m/Y',strtotime($demande['DATE_TRANSMISSION'])).'</strong> </p>';
                                                               }else{
                                                                   //echo '<p>TRAITEMENT EN COURS : <strong class="text-success"><i class="fa fa-check" aria-hidden="true"></i> '.date('d/m/Y',strtotime($demande['DATE_TRANSMISSION'])).'</strong> </p>';
                                                               }

                                                                if($demande['STATUT_PRODUCTION'] == 1){
                                                                    echo '<p>PRODUCTION : <strong class="text-success"> <i class="fa fa-check " aria-hidden="true"></i> '.date('d/m/Y',strtotime($demande['DATE_PRODUCTION'])).'</strong> </p>';
                                                                }else{
                                                                    echo '<p>PRODUCTION : <strong class="text-danger"> NON PRODUITE </strong> <button class="badge badge-warning" data-toggle="modal" data-target=".modal_statut_production-modal-sm"><i class="fa fa-edit"></i></button> </p>';
                                                                }
                                                                ?>

                                                                <div class="modal fade modal_statut_production-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                                    <div class="modal-dialog modal-sm">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <h5 class="modal-title" id="exampleModalLabel">Statut Production</h5>
                                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                    <span aria-hidden="true">&times;</span>
                                                                                </button>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                <div id="resultat_form_production"></div>
                                                                                <form id="form_production">
                                                                                    <div class="form-row align-items-center">
                                                                                        <div class="col-sm-8">
                                                                                            <input type="text" id="id_demande_input" value="<?= $_POST["id"] ?>" hidden>
                                                                                            <input type="text" id="num_secu_demande_input" value="<?= $demande["NUM_SECU"] ?>" hidden>
                                                                                            <input type="text" class="form-control form-control-sm datepicker" id="date_production_input" placeholder="Date de production" autocomplete="off" required>
                                                                                        </div>
                                                                                        <div class="col-sm-2">
                                                                                            <div class="input-group input-group-sm">
                                                                                                <button type="submit" class="btn_editer_motif btn btn-success btn-sm" id="btn_production">Valider</button>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </form>
                                                                            </div>

                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <?php
                                                                if($demande['STATUT_PRODUCTION'] == 1){
                                                                    if($demande['STATUT_ACHEMINEMENT'] == 1){
                                                                        echo '<p>ACHEMINEMENT : <strong class="text-success"> <i class="fa fa-check" aria-hidden="true"></i> le '.date('d/m/Y',strtotime($demande['DATE_ACHEMINEMENT'])).' - '.$demande['LIEU_ACHEMINEMENT'].' - '.$demande['NUMERO_RANGEMENT'].' </strong> </p>';
                                                                    }else{
                                                                        echo '<p>ACHEMINEMENT : <strong class="text-danger"> NON ACHEMINEE </strong> <button class="badge badge-warning" data-toggle="modal" data-target=".modal_statut_acheminement-modal-sm"><i class="fa fa-edit"></i></button> </p>';
                                                                    }
                                                                }

                                                                ?>
                                                                <div class="modal fade modal_statut_acheminement-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                                    <div class="modal-dialog modal-sm">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <h5 class="modal-title" id="exampleModalLabel">Acheminement</h5>
                                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                    <span aria-hidden="true">&times;</span>
                                                                                </button>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                <div id="resultat_form_acheminement"></div>
                                                                                <form id="form_acheminement">
                                                                                    <div class="form-row align-items-center">
                                                                                        <div class="col-sm-12">
                                                                                            <input type="text" id="id_demande_input" value="<?= $_POST["id"] ?>" hidden>
                                                                                            <input type="text" id="num_secu_demande_input" value="<?= $demande["NUM_SECU"] ?>" hidden>
                                                                                        </div>
                                                                                        <div class="col-sm-12 my-1">
                                                                                            <input class="form-control form-control-sm datepicker" type="text" id="date_acheminement_input" value="" placeholder="Date Acheminement" autocomplete="off">
                                                                                        </div>

                                                                                        <div class="col-sm-12 my-1">
                                                                                            <input class="form-control form-control-sm" type="text" id="lieu_acheminement_input" value="" placeholder="Lieu Acheminement" autocomplete="off" required>
                                                                                        </div>

                                                                                        <div class="col-sm-12 my-1">
                                                                                            <input class="form-control form-control-sm" type="text" id="numero_rangement_input" value="" placeholder="Numéro de Rangement" autocomplete="off" required>
                                                                                        </div>

                                                                                        <br><br>
                                                                                        <div class="col-sm-12">
                                                                                            <div class="input-group input-group-sm">
                                                                                                <button type="submit" class="form-control form-control-sm btn btn-success btn-sm" id="button_valider">Valider</button>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </form>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <?php
                                                                if($demande['STATUT_ACHEMINEMENT'] == 1){
                                                                    if($demande['STATUT_RETRAIT'] == 1){
                                                                        echo '<p>RETRAIT : <strong class="text-success"> <i class="fa fa-check" aria-hidden="true"></i> le '.date('d/m/Y',strtotime($demande['DATE_RETRAIT'])).' - '.$demande['LIEU_RETRAIT'].'  </strong> </p>';
                                                                    }else{
                                                                        echo '<p>RETRAIT : <strong class="text-danger"> NON DELIVREE</strong> <button class="badge badge-warning" data-toggle="modal" data-target=".modal_statut_retrait_carte-modal-sm"><i class="fa fa-edit"></i></button></p>';
                                                                    }
                                                                }

                                                                ?>
                                                                <div class="modal fade modal_statut_retrait_carte-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                                <div class="modal-dialog modal-sm">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <h5 class="modal-title" id="exampleModalLabel">Retrait Carte</h5>
                                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                <span aria-hidden="true">&times;</span>
                                                                            </button>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            <div id="resultat_form_retrait_carte"></div>
                                                                            <form id="form_retrait_carte">
                                                                                <div class="form-row align-items-center">
                                                                                    <div class="col-sm-12">
                                                                                        <input type="text" id="id_demande_input" value="<?= $_POST["id"] ?>" hidden>
                                                                                        <input type="text" id="num_secu_demande_input" value="<?= $demande["NUM_SECU"] ?>" hidden>
                                                                                    </div>
                                                                                    <div class="col-sm-12 my-1">
                                                                                            <input class="form-control form-control-sm datepicker" type="text" id="date_retrait_input" value="" placeholder="Date Retrait" autocomplete="off">
                                                                                    </div>
                                                                                    <div class="col-sm-12 my-1">
                                                                                            <input class="form-control form-control-sm" type="text" id="lieu_retrait_input" value="" placeholder="Lieu Retrait" autocomplete="off" required>

                                                                                    </div>
                                                                                    <br><br>
                                                                                    <div class="col-sm-12">
                                                                                        <div class="input-group input-group-sm">
                                                                                            <button type="submit" class="form-control form-control-sm btn btn-success btn-sm" id="button_valider_retrait">Valider</button>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </form>
                                                                        </div>

                                                                    </div>
                                                                </div>
                                                    </div>


                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <script type="text/javascript" src="<?= JS.'page_partenaire.js'?>"></script>
                        <?php
                    }else {
                        echo '<script>window.location.href="'.URL.'"</script>';
                    }
                }else {
                    echo '<script>window.location.href="'.URL.'"</script>';
                }
            }
        }
    }
}else{
    session_destroy();
    echo '<script>window.location.href="'.URL.'"</script>';
}
?>