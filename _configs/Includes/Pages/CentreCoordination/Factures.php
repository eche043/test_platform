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
            if(in_array('COORD',$modules)) {
                require_once '../../../Classes/FACTURES.php';
                require_once '../../../Classes/ETABLISSEMENTSSANTE.php';
                require_once '../../../Classes/COORDINATIONS.php';
                $FACTURES = new FACTURES();
                $ETABLISSEMENTSSANTE = new ETABLISSEMENTSSANTE();
                $COORDINATIONS = new COORDINATIONS();
                require_once '../../../Classes/UTILISATEURS.php';
                $UTILISATEURS = new UTILISATEURS();
                $centre = $UTILISATEURS->trouver_centre_coordination($user['ID_UTILISATEUR']);
                if($centre){
                    $ets = $COORDINATIONS->trouver_ets($centre['CODE_CENTRE'],$_POST['code_ets']);
                    if($ets)
                    {
                        $factures_a_traiter = $FACTURES->lister_facture_a_traiter($ets['CODE_ETS']);
                        $nb_factures_a_traiter = count($factures_a_traiter);

                            ?>
                            <div class="col"><br>
                                <p class="align_center">
                                    <input type="hidden" id="code_ets_input" value="<?=$_POST['code_ets'];?>">
                                    <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#consultationDroitsModal"><i class="fa fa-telegram" aria-hidden="true"></i>
                                        Consultation de droits</button>
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
                            if($nb_factures_a_traiter != 0)
                            {
                            ?>
                                <p class="titres_p"><i class="fa fa-newspaper"></i> Factures à traiter</p>
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
                                            <td><a href="<?= URL.'centre-coordination/facture-selection-type.php?num='.$facture_a_traiter['FEUILLE'].'&code-ets='.$_POST['code_ets'];?>" class="badge badge-success"><i class="fa fa-edit"></i></a></td>
                                            <td><a href="<?= URL.'centre-coordination/facture-annulation.php?num='.$facture_a_traiter['FEUILLE'].'&code-ets='.$_POST['code_ets'];?>" class="badge badge-danger"><i class="fa fa-trash"></i></a></td>
                                        </tr>
                                        <?php
                                        $ligne_a_traiter++;
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </div>

                            <?php
                        }
                        else
                        {
                            echo '<p class="align_center alert alert-info">AUCUNE FACTURE EN ATTENTE DE TRAITEMENT</p>';
                        }
                        $factures_a_finaliser = $FACTURES->lister_facture_a_finaliser($ets['CODE_ETS']);
                        $nb_factures_a_finaliser = count($factures_a_finaliser);
                        if($nb_factures_a_finaliser != 0)
                        {
                            ?>
                            <hr>
                            <div class="col">
                                <p class="titres_p"><i class="fa fa-newspaper"></i> Factures à finaliser</p>
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
                                        ?>
                                        <tr>
                                            <td class="align_right"><?= $ligne_a_finaliser;?></td>
                                            <td class="align_center"><?= date('d/m/Y',strtotime($facture_a_finaliser['DATE_REG']));?></td>
                                            <td class="align_center"><?= date('H:i',strtotime($facture_a_finaliser['DATE_REG']));?></td>
                                            <td class="align_right"><b><?= $facture_a_finaliser['FEUILLE'];?></b></td>
                                            <td><b class="info_assure"><?= $facture_a_finaliser['NUM_SECU'];?></b></td>
                                            <td><b class="info_assure"><?= $facture_a_finaliser['NOM'].' '.$facture_a_finaliser['PRENOM'];?></b></td>
                                            <td><a href="<?= URL.'centre-coordination/facture-selection-type.php?num='.$facture_a_finaliser['FEUILLE'].'&code-ets='.$_POST['code_ets'];?>" class="badge badge-success"><i class="fa fa-edit"></i></a></td>
                                            <td><a href="<?= URL.'centre-coordination/facture-annulation.php?num='.$facture_a_finaliser['FEUILLE'].'&code-ets='.$_POST['code_ets'];?>" class="badge badge-danger"><i class="fa fa-trash"></i></a></td>
                                        </tr>
                                        <?php
                                        $ligne_a_finaliser++;
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php
                        }
                        else
                        {
                            echo '<p class="align_center alert alert-info">AUCUNE FACTURE EN ATTENTE DE TRAITEMENT</p>';
                        }
                        ?>
                        <div class="col">
                            <p class="titres_p"><i class="fa fa-pills"></i> Factures en pharmacie</p><br />
                            <div class="col-sm-12">
                                <div class="row justify-content-md-center">
                                    <div class="col-sm-3">
                                        <?php include "../Forms/form_trouver_medicament_fs_iniale.php";?>
                                    </div>
                                    <div class="col-sm-12" id="div_resultats_med"></div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <?php
                                $factures_en_pharmacie = $FACTURES->lister_facture_en_pharmacie($ets['CODE_ETS']);
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
                                                <td><a href="<?= URL.'centre-coordination/facture-selection-type.php?num='.$facture_en_pharmacie['FEUILLE'].'&code-ets='.$_POST['code_ets'];?>" class="badge badge-success"><i class="fa fa-edit"></i></a></td>
                                                <td><a href="<?= URL.'centre-coordination/facture-annulation.php?num='.$facture_en_pharmacie['FEUILLE'].'&code-ets='.$_POST['code_ets'];?>" class="badge badge-danger"><i class="fa fa-trash"></i></a></td>
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


                        <script type="text/javascript" src="<?= JS.'page_centre_coordination.js'?>"></script>
                        <?php
                    }else{
                        echo '<script>window.location.href="'.URL.'centre-coordination/"</script>';
                    }
                }
                else{
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
<script>
    $(function () {
        $('.dataTable').DataTable();
    });

</script>