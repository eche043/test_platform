<?php
require_once '../../../Classes/UTILISATEURS.php';
require_once '../../../Classes/ASSURES.php';
require_once '../../../Classes/OGD.php';

if(isset($_SESSION['ECMU_USER_ID']) || !empty($_SESSION['ECMU_USER_ID'])) {
    $UTILISATEURS = new UTILISATEURS();
    $ASSURES = new ASSURES();
    $OGD = new OGD();

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
            if(in_array('ASSU',$modules)) {
                $assure = $ASSURES->trouver($user['NUM_SECU']);
            ?>
                <div class="container"><br>
                    <!--<p><button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#payerCotisationsModal" > PAYER MA COTISATION</button></p>
                    <div class="modal fade" id="payerCotisationsModal" tabindex="-1" role="dialog" aria-labelledby="payerCotisationsTitle" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" id="closeModal" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                                </div>
                                <div class="modal-body">
                                    <iframe height="600px" width="100%" src="https://recette-ecmu.ipscnam.ci/paiement/" name="iframe_a"></iframe>
                                </div>
                            </div>
                        </div>
                    </div>-->
                    <p class="titres_p">Compte cotisant</p>
                    <div class="row">
                        <div class="col">
                            <?php
                            $ventilation = $ASSURES->trouver_ventilations_cotisations_assure($user['NUM_SECU']);
                            if(count($ventilation) == 0) {
                                ?>
                                <p class="align_center alert <?php if($assure['DATE_AFFILIATION'] < date('Y-m-d',time())){echo 'alert-danger';}else {echo 'alert-success';} ?>"><b>AUCUNE COTISATION N'A ENCORE ETE ENREGISTREE POUR CET ASSURE</b></p>
                                <?php
                            }else {
                                ?>
                                <div>
                                    <table style="width: 100%">
                                        <thead class="bg-primary text-white">
                                        <tr align="center">
                                            <th>ANNEE</th>
                                            <?php
                                            require_once '../../../Classes/CALENDRIER.php';
                                            $CALENDRIER = new CALENDRIER();
                                            for ($mois = 1; $mois <= 12; $mois++) {
                                                echo '<th style="width: 85px">'.$CALENDRIER->trouver_mois(str_pad($mois,'2','0', STR_PAD_LEFT)).'</th>';
                                            }
                                            ?>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        $annne_en_cours = date('Y',time());
                                        $annee_affiliation = date('Y',strtotime($assure['DATE_AFFILIATION']));

                                        for($annee_cotisation = $annne_en_cours; $annee_cotisation >= $annee_affiliation; $annee_cotisation--) {

                                            ?>
                                            <tr>
                                                <td class="bg-primary text-white align_center"><b><?= $annee_cotisation;?></b></td>
                                                <?php
                                                for($m = 1; $m <= 12; $m++) {
                                                    if($m <= 9) {
                                                        $m = "0".$m;
                                                    }
                                                    $cotisation = $ASSURES->trouver_cotisation($assure['NUM_SECU'],$annee_cotisation,$m);
                                                    ?>
                                                    <td>
                                                        <?php
                                                        $date = date('Y-m-d',time());
                                                        if(empty($cotisation['BENEFICIAIRE_NUM_SECU'])) {
                                                            $mois_en_cours = $annee_cotisation.'-'.$m;

                                                            $date_affiliation = date('Y-m-d',strtotime($assure['DATE_AFFILIATION']));

                                                            if($mois_en_cours <= $date && $mois_en_cours > $date_affiliation) {
                                                                echo '<button class="btn btn-danger btn-block btn-sm" disabled>&nbsp;</button>';
                                                            }elseif($date <= $mois_en_cours){
                                                                echo '<button class="btn btn-light btn-block btn-sm" disabled>&nbsp;</button>';
                                                            }
                                                        }else {
                                                            ?>
                                                            <button class="btn btn-block btn-sm <?php if($cotisation['STATUT'] == 'FIN' || $cotisation['STATUT'] == 'CRE'){echo 'btn-success';} ?>"><?='&nbsp;';?></button>
                                                            <?php
                                                        }
                                                        ?>
                                                    </td>
                                                    <?php
                                                }
                                                ?>
                                            </tr>
                                            <?php
                                        }

                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                    </div><BR>





                    <p class="titres_p">Cotisations</p>
                    <div class="row">
                        <div class="col">
                            <?php
                            $historique_cotisation = $ASSURES->historique_cotisations_assure($user['NUM_SECU']);
                            $paiements_organismes = $ASSURES->trouver_assure_paiements_ogd($user['NUM_SECU']);
                            if(count($historique_cotisation) == 0 && !$paiements_organismes) {
                                echo '<p class="h4 align_center alert-info">VOUS N\'AVEZ EFFECTUE AUCUN PAIEMENT DE COTISATIONS POUR L\'INSTANT.</p>';
                            }else {
                                ?>
                                <table class="table table-sm table-bordered table-hover dataTable" id="dataTable">
                                    <thead class="bg-info">
                                    <tr>
                                        <th style="width: 5px">N°</th>
                                        <th style="width: 100px">DATE</th>
                                        <th style="width: 100px">HEURE</th>
                                        <th style="width: 100px">N° TRANSACTION</th>
                                        <th style="width: 150px">TYPE REGLEMENT</th>
                                        <th style="width: 100px">MONTANT</th>
                                        <th>CANAL DE PAIEMENT</th>
                                        <th style="width: 5px"></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $ligne = 1;
                                    foreach ($paiements_organismes as $paiement) {
                                        $ogd_paiement = $OGD->trouver('AFFL',$paiement['CODE_OGD']);
                                        ?>
                                        <tr>
                                            <td class="align_right"><?= $ligne;?></td>
                                            <td class="bg-secondary"></td>
                                            <td class="bg-secondary"></td>
                                            <td class="bg-secondary"></td>
                                            <td class="bg-secondary"></td>
                                            <td class="align_right"><b><?= number_format(($paiement['NOMBRE'] * 1000),'0','',' ');?> F CFA</b></td>
                                            <td><?= $ogd_paiement['LIBELLE'];?></td>
                                            <td style="width: 5px"><?php if(!$paiement['STATUT']) {echo '<b class="text-warning fa fa-clock<"></b>';}else{echo '<b class="text-success fa fa-check"></b>';} ?></td>
                                        </tr>
                                        <?php
                                        $ligne++;
                                    }
                                    foreach ($historique_cotisation as $cotisation) {
                                        $statut = $ASSURES->statut_paiements();

                                        if($cotisation['STATUT'] == 0) {
                                            $icone = '<b class="fa fa-times"></b>';
                                        }elseif($cotisation['STATUT'] == 1) {
                                            $icone = '<b class="fa fa-check text-success"></b>';
                                        }elseif($cotisation['STATUT'] == 2) {
                                            $icone = '<b class="fa fa-expand text-warning"></b>';
                                        }elseif($cotisation['STATUT'] == 3) {
                                            $icone = '<b class="fa fa-ellipsis-h text-warning"></b>';
                                        }elseif($cotisation['STATUT'] == 4) {
                                            $icone = '<b class="fa fa-asterisk text-danger"></b>';
                                        }elseif($cotisation['STATUT'] == 5) {
                                            $icone = '<b class="fa fa-bell-slash text-warning"></b>';
                                        }elseif($cotisation['STATUT'] == 6) {
                                            $icone = '<b class="fa fa-exclamation-triangle text-danger"></b>';
                                        }
                                        ?>
                                        <tr <?php if ($cotisation['STATUT'] == 0){echo 'class="bg-danger text-white"';} ?>>
                                            <td align="right"><?= $ligne;?></td>
                                            <td><?= date('d/m/Y',strtotime($cotisation['DATE_REG']));?></td>
                                            <td><?= date('H:i',strtotime($cotisation['DATE_REG']));?></td>
                                            <td><b><?= $cotisation['NUM_TRANSACTION'];?></b></td>
                                            <td><?= str_replace('F','FAMILIAL',str_replace('I','INDIVIDUEL',$cotisation['PAYMENT_TYPE']));?></td>
                                            <td align="right"><b><?= number_format($cotisation['PAID_TRANSACTION_AMOUNT'],'0','',' ');?> F CFA</b></td>
                                            <td><?= $cotisation['WALLET'];?></td>
                                            <td title="<?= $statut[$cotisation['STATUT']];?>"><?= $icone;?></td>
                                        </tr>
                                        <?php
                                        $ligne++;
                                    }
                                    ?>
                                    </tbody>
                                </table>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
            <?php
            }
        }
    }
}else{
    session_destroy();
    echo '<script>window.location.href="'.URL.'"</script>';
}
?>
<script>
    $('.close').click(function() {
        $('.modal').modal('hide');
    });
    $(".dataTable").DataTable();
</script>