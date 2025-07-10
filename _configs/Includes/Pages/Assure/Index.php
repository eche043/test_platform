<?php
require_once '../../../Classes/UTILISATEURS.php';
require_once '../../../Classes/ASSURES.php';
require_once '../../../Classes/OGD.php';
require_once '../../../Classes/FACTURES.php';
require_once '../../../Classes/ATTESTATIONSDROITS.php';

$statuts_facture = array('','C','N','A','R');

if(isset($_SESSION['ECMU_USER_ID']) || !empty($_SESSION['ECMU_USER_ID'])) {
    $UTILISATEURS = new UTILISATEURS();
    $ASSURES = new ASSURES();
    $ATTESTATIONSDROITS = new ATTESTATIONSDROITS();
    $FACTURES = new FACTURES();
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
                ?>
                <div class="col"><br />
                    <div class="accordion" id="accordionHome">
                        <div class="card">
                            <div class="card-header" id="headingOne">
                                <h2 class="mb-0">
                                    <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                        <i class="fa fa-list"></i> Historique Cotisations
                                    </button>
                                </h2>
                            </div>

                            <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionHome">
                                <div class="card-body">
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
                                                            <td align="right"><?= $ligne;?></td>
                                                            <td class="bg-secondary"></td>
                                                            <td class="bg-secondary"></td>
                                                            <td class="bg-secondary"></td>
                                                            <td class="bg-secondary"></td>
                                                            <td align="right"><b><?= number_format(($paiement['NOMBRE'] * 1000),'0','',' ');?> F CFA</b></td>
                                                            <td><?= $ogd_paiement['LIBELLE'];?></td>
                                                            <td width="5"><?php if(!$paiement['STATUT']) {echo '<b class="text-warning fa fa-clock<"></b>';}else{echo '<b class="text-success fa fa-check"></b>';} ?></td>
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
                            </div>
                        </div>
<!--                        -->
<!--                        <div class="card">-->
<!--                            <div class="card-header" id="headingTwo">-->
<!--                                <h2 class="mb-0">-->
<!--                                    <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">-->
<!--                                        <i class="fa fa-list"></i> Historique Consommations-->
<!--                                    </button>-->
<!--                                </h2>-->
<!--                            </div>-->
<!--                            <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionHome">-->
<!--                                <div class="card-body">-->
<!--                                    <div class="row">-->
<!--                                        <div class="col">-->
<!--                                            --><?php
//                                            $factures = $FACTURES->dernieres_consommations_assure($user['NUM_SECU']);
//                                            if(count($factures) == 0) {
//                                                echo '<p class="h4 align_center alert-info">VOUS N\'AVEZ BENEFICIE D\'AUCUNE PRESTATION POUR L\'INSTANT.</p>';
//                                            }
//                                            else{
//                                                ?>
<!--                                                <table-->
<!--                                                        class="table table-bordered table-hover table-sm table-responsive-sm dataTable">-->
<!--                                                    <thead class="bg-secondary text-white">-->
<!--                                                    <tr>-->
<!--                                                        <th>N°</th>-->
<!--                                                        <th>ETABLISSEMENT</th>-->
<!--                                                        <th>DATE</th>-->
<!--                                                        <th align="center">TYPE PRESTATIONS</th>-->
<!--                                                        <th align="center">NUMERO FACTURE</th>-->
<!--                                                        <th>MONTANT TOTAL</th>-->
<!--                                                        <th>PART CMU</th>-->
<!--                                                        <th>PART ASSURE</th>-->
<!--                                                    </tr>-->
<!--                                                    </thead>-->
<!--                                                    <tbody>-->
<!--                                                    --><?php
//                                                    $ligne = 1;
//                                                    foreach ($factures as $facture) {
//                                                        if(!in_array($facture['STATUT'],$statuts_facture)) {
//                                                            $type_feuille = $FACTURES->trouver_type_facture($facture['TYPE_FEUILLE']);
//                                                            ?>
<!--                                                            <tr>-->
<!--                                                                <td width="5" align="right"><b>--><?//= $ligne; ?><!--</b></td>-->
<!--                                                                <td>--><?//= $facture['NOM_ETS']; ?><!--</td>-->
<!--                                                                <td width="120">--><?//= date('d/m/Y', strtotime($facture['DATE_SOINS'])); ?><!--</td>-->
<!--                                                                <td>--><?//= $type_feuille['LIBELLE']; ?><!--</td>-->
<!--                                                                <td align="right"><b>--><?//= $facture['FEUILLE']; ?><!--</b></td>-->
<!--                                                                <td align="right"><b>--><?//= $facture['MONTANT'] . ' FCFA'; ?><!--</b></td>-->
<!--                                                                <td align="right"><b>--><?//= $facture['PART_CMU'] . ' FCFA'; ?><!--</b></td>-->
<!--                                                                <td align="right"><b>--><?//= $facture['PART_ASSURE'] . ' FCFA'; ?><!--</b></td>-->
<!--                                                            </tr>-->
<!--                                                            --><?php
//                                                        }
//                                                        $ligne++;
//                                                    }
//                                                    ?>
<!--                                                    </tbody>-->
<!--                                                </table>-->
<!--                                                --><?php
//                                            }
//                                            ?>
<!--                                        </div>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                        <div class="card">-->
<!--                            <div class="card-header" id="headingThree">-->
<!--                                <h2 class="mb-0">-->
<!--                                    <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">-->
<!--                                        <i class="fa fa-list"></i> Historique Demandes-->
<!--                                    </button>-->
<!--                                </h2>-->
<!--                            </div>-->
<!--                            <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordionHome">-->
<!--                                <div class="card-body">-->
<!--                                    <div class="row">-->
<!--                                        <div class="col">-->
<!--                                            --><?php
//                                            $attestationsDroits = $ATTESTATIONSDROITS->dernieres_attestations_assure($user['NUM_SECU']);
//                                            if(count($attestationsDroits) == 0) {
//                                                echo '<p class="h4 align_center alert-info">VOUS N\'AVEZ EFFECTUE AUCUNE DEMANDE POUR L\'INSTANT.</p>';
//                                            }else {
//                                                ?>
<!--                                                <table-->
<!--                                                        class="table table-bordered table-hover table-sm table-responsive-sm dataTable">-->
<!--                                                    <thead class="bg-secondary text-white">-->
<!--                                                    <tr>-->
<!--                                                        <th>N°</th>-->
<!--                                                        <th>DATE DE DEMANDE</th>-->
<!--                                                        <th>MOTIF DE LA DEMANDE</th>-->
<!--                                                        <th>STATUT</th>-->
<!--                                                    </tr>-->
<!--                                                    </thead>-->
<!--                                                    <tbody>-->
<!--                                                    --><?php
//                                                    $ligne = 1;
//                                                    foreach ($attestationsDroits as $attestations) {
//                                                        ?>
<!--                                                        <tr>-->
<!--                                                            <td width="5" align="right"><b>--><?//= $ligne; ?><!--</b></td>-->
<!--                                                            <td width="150">--><?//= date('d/m/Y', strtotime($attestations['DATE_REG'])); ?><!--</td>-->
<!--                                                            <td>--><?//= $attestations['MOTIF_DEMANDE']; ?><!--</td>-->
<!--                                                            <td>-->
<!--                                                                <b>-->
<!--                                                                    --><?php
//                                                                    if ($attestations['STATUT_ATTESTATION'] == 0) {
//                                                                        $statut = 'EN ATTENTE DE VALIDATION';
//                                                                    } elseif ($attestations['STATUT_ATTESTATION'] == 1) {
//                                                                        $statut = 'VALIDEE';
//                                                                    } elseif ($attestations['STATUT_ATTESTATION'] == 2) {
//                                                                        $statut = 'REFUSEE';
//                                                                    } elseif ($attestations['STATUT_ATTESTATION'] == 3) {
//                                                                        $statut = 'SUSPENDUE';
//                                                                    } elseif ($attestations['STATUT_ATTESTATION'] == 4) {
//                                                                        $statut = 'PERIMEE';
//                                                                    }
//                                                                    echo $statut;
//                                                                    ?>
<!--                                                                </b>-->
<!--                                                            </td>-->
<!--                                                        </tr>-->
<!--                                                        --><?php
//                                                        $ligne++;
//                                                    }
//                                                    ?>
<!--                                                    </tbody>-->
<!--                                                </table>-->
<!--                                                --><?php
//                                            }
//                                            ?>
<!--                                        </div>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                        -->
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
    $(function () {
        $('.dataTable').DataTable();
    })
</script>
