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
            if(in_array('AGAC',$modules)) {
                $user_profil = explode(';',$user['FSE']);
                $user_ets = $UTILISATEURS->trouver_ets_utilisateur($user['ID_UTILISATEUR']);
                if(!empty($user_ets['CODE_ETS'])) {
                    require_once '../../../Classes/FACTURES.php';
                    $FACTURES = new FACTURES();
                    ?>
                    <div class="col"><br />
                        <?php
                        if($user['MODE_DEGRADE'] == 1) {
                            ?>
                            <p class="align_center">
                                <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#consultationDroitsModal"><i class="fa fa-telegram"></i> Consultation de droits</button>
                            </p>
                            <div class="modal fade" id="consultationDroitsModal" tabindex="-1" role="dialog" aria-labelledby="consultationDroitsModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-sm" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="consultationDroitsModalLabel">Consultation de droits</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <?php include "../Forms/form_consultation_droits.php";?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                        if(in_array('AMB',$user_profil) || in_array('DEN',$user_profil) || in_array('EXP',$user_profil) || in_array('HOS',$user_profil)) {
                            ?>
                            <p class="titres_p"><i class="fa fa-newspaper"></i> Factures à traiter</p>
                            <div class="row">
                                <div class="col">
                                    <?php
                                    $factures_a_traiter = $FACTURES->lister_facture_a_traiter($user_ets['CODE_ETS']);
                                    $nb_factures_a_traiter = count($factures_a_traiter);
                                    if($nb_factures_a_traiter != 0) {
                                        ?>
                                        <table class="table table-bordered table-hover table-sm table-responsive-sm dataTable">
                                            <thead class="bg-secondary text-white">
                                            <tr>
                                                <th width="5">N°</th>
                                                <th width="50">DATE</th>
                                                <th width="10">HEURE</th>
                                                <th width="100">N° FACTURE</th>
                                                <th width="100">N° SECU</th>
                                                <th>NOM & PRENOM(S)</th>
                                                <th width="5"><i class="fa fa-edit"></i></th>
                                                <th width="5"><i class="fa fa-trash"></i></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                            $ligne_a_traiter = 1;
                                            foreach ($factures_a_traiter as $facture_a_traiter) {
                                                ?>
                                                <tr>
                                                    <td class="align_right"><?= $ligne_a_traiter;?></td>
                                                    <td class="align_center"><?= date('d/m/Y',strtotime($facture_a_traiter['DATE_REG']));?></td>
                                                    <td class="align_center"><?= date('H:i',strtotime($facture_a_traiter['DATE_REG']));?></td>
                                                    <td class="align_right"><b><?= $facture_a_traiter['FEUILLE'];?></b></td>
                                                    <td><b class="info_assure"><?= $facture_a_traiter['NUM_SECU'];?></b></td>
                                                    <td><b class="info_assure"><?= $facture_a_traiter['NOM'].' '.$facture_a_traiter['PRENOM'];?></b></td>
                                                    <td><a href="<?= URL.'agent/facture-selection-type.php?num='.$facture_a_traiter['FEUILLE'];?>" class="badge badge-success"><i class="fa fa-edit"></i></a></td>
                                                    <td><a href="<?= URL.'agent/facture-annulation.php?num='.$facture_a_traiter['FEUILLE'];?>" class="badge badge-danger"><i class="fa fa-trash"></i></a></td>
                                                </tr>
                                                <?php
                                                $ligne_a_traiter++;
                                            }
                                            ?>
                                            </tbody>
                                        </table>
                                        <?php
                                    }else {
                                        echo '<p class="align_center alert alert-info">AUCUNE FACTURE EN ATTENTE DE TRAITEMENT</p>';
                                    }
                                    ?>
                                </div>
                            </div>

                            <p class="titres_p"><i class="fa fa-list-alt"></i> Factures à finaliser</p>
                            <div class="row">
                                <div class="col">
                                    <?php
                                    $factures_a_finaliser = $FACTURES->lister_facture_a_finaliser($user_ets['CODE_ETS']);
                                    $nb_factures_a_finaliser = count($factures_a_finaliser);
                                    if($nb_factures_a_finaliser != 0) {
                                        ?>
                                        <table class="table table-bordered table-hover table-sm table-responsive-sm dataTable">
                                            <thead class="bg-secondary text-white">
                                            <tr>
                                                <th width="5">N°</th>
                                                <th width="50">DATE</th>
                                                <th width="10">HEURE</th>
                                                <th width="100">N° FACTURE</th>
                                                <th width="100">N° SECU</th>
                                                <th>NOM & PRENOM(S)</th>
                                                <th width="5"><i class="fa fa-edit"></i></th>
                                                <th width="5"><i class="fa fa-trash"></i></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                            $ligne_a_finaliser = 1;
                                            foreach ($factures_a_finaliser as $facture_a_finaliser) {
                                                if(in_array($facture_a_finaliser['TYPE_FEUILLE'],$user_profil)) {
                                                    ?>
                                                    <tr>
                                                        <td class="align_right"><?= $ligne_a_finaliser;?></td>
                                                        <td class="align_center"><?= date('d/m/Y',strtotime($facture_a_finaliser['DATE_REG']));?></td>
                                                        <td class="align_center"><?= date('H:i',strtotime($facture_a_finaliser['DATE_REG']));?></td>
                                                        <td class="align_right"><b><?= $facture_a_finaliser['FEUILLE'];?></b></td>
                                                        <td><b class="info_assure"><?= $facture_a_finaliser['NUM_SECU'];?></b></td>
                                                        <td><b class="info_assure"><?= $facture_a_finaliser['NOM'].' '.$facture_a_finaliser['PRENOM'];?></b></td>
                                                        <td><a href="<?= URL.'agent/facture-selection-type.php?num='.$facture_a_finaliser['FEUILLE'];?>" class="badge badge-success"><i class="fa fa-edit"></i></a></td>
                                                        <td><a href="<?= URL.'agent/facture-annulation.php?num='.$facture_a_finaliser['FEUILLE'];?>" class="badge badge-danger"><i class="fa fa-trash"></i></a></td>
                                                    </tr>
                                                    <?php
                                                }
                                                $ligne_a_finaliser++;
                                            }
                                            ?>
                                            </tbody>
                                        </table>
                                        <?php
                                    }else {
                                        echo '<p class="align_center alert alert-info">AUCUNE FACTURE EN ATTENTE DE TRAITEMENT</p>';
                                    }
                                    ?>
                                </div>
                            </div>

                            <?php
                        }

                        if(in_array('MED',$user_profil)) {
                            ?>
                            <p class="titres_p"><i class="fa fa-pills"></i> Factures en pharmacie</p><br />
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="row justify-content-md-center">
                                        <div class="col-sm-3">
                                            <?php include "../Forms/form_trouver_medicament_fs_iniale.php";?>
                                        </div>
                                        <div class="col-sm-12" id="div_resultats_med"></div>
                                    </div>
                                </div>
                                <div class="col">
                                    <?php
                                    $factures_en_pharmacie = $FACTURES->lister_facture_en_pharmacie($user_ets['CODE_ETS']);
                                    $nb_factures_en_pharmacie = count($factures_en_pharmacie);
                                    if($nb_factures_en_pharmacie != 0) {
                                        ?>
                                        <table class="table table-bordered table-hover table-sm table-responsive-sm dataTable">
                                            <thead class="bg-secondary text-white">
                                            <tr>
                                                <th width="5">N°</th>
                                                <th width="50">DATE</th>
                                                <th width="10">HEURE</th>
                                                <th width="100">N° FACTURE</th>
                                                <th width="100">N° SECU</th>
                                                <th>NOM & PRENOM(S)</th>
                                                <th width="5"><i class="fa fa-edit"></i></th>
                                                <th width="5"><i class="fa fa-trash"></i></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                            $ligne_en_pharmacie = 1;
                                            foreach ($factures_en_pharmacie as $facture_en_pharmacie) {
                                                ?>
                                                <tr>
                                                    <td class="align_right"><?= $ligne_en_pharmacie;?></td>
                                                    <td class="align_center"><?= date('d/m/Y',strtotime($facture_en_pharmacie['DATE_REG']));?></td>
                                                    <td class="align_center"><?= date('H:i',strtotime($facture_en_pharmacie['DATE_REG']));?></td>
                                                    <td class="align_right"><b><?= $facture_en_pharmacie['FEUILLE'];?></b></td>
                                                    <td><b class="info_assure"><?= $facture_en_pharmacie['NUM_SECU'];?></b></td>
                                                    <td><b class="info_assure"><?= $facture_en_pharmacie['NOM'].' '.$facture_en_pharmacie['PRENOM'];?></b></td>
                                                    <td><a href="<?= URL.'agent/facture-selection-type.php?num='.$facture_en_pharmacie['FEUILLE'];?>" class="badge badge-success"><i class="fa fa-edit"></i></a></td>
                                                    <td><a href="<?= URL.'agent/facture-annulation.php?num='.$facture_en_pharmacie['FEUILLE'];?>" class="badge badge-danger"><i class="fa fa-trash"></i></a></td>
                                                </tr>
                                                <?php
                                                $ligne_en_pharmacie++;
                                            }
                                            ?>
                                            </tbody>
                                        </table>
                                        <?php
                                    }else {
                                        echo '<p class="align_center alert alert-info">AUCUNE FACTURE EN ATTENTE DE TRAITEMENT</p>';
                                    }
                                    ?>
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                    <?php
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
<script type="application/javascript" src="<?= JS.'page_agent.js';?>"></script>
<script>
    $(function () {
        $('.dataTable').DataTable();
    })
</script>
