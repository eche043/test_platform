<?php

require_once '../../../Classes/UTILISATEURS.php';
require_once '../../../Classes/FACTURES.php';
$FACTURES = new FACTURES();

$date_debut = strtoupper(date('Y-m-d',strtotime(str_replace('/','-',trim($_POST['date_debut'])))));
$date_fin = strtoupper(date('Y-m-d',strtotime(str_replace('/','-',trim($_POST['date_fin'])))));
$code_ps = $_POST['code_ps'];
$code_ets = $_POST['code_ets'];

$factures = $FACTURES->lister_factures_par_statut($code_ets,$date_debut,$date_fin,$code_ps);
$nb_factures = count($factures);
if($nb_factures == 0) {
    echo '<p align="center" class="text-danger">Aucune information disponible sur cette période</p>';
}else {
    ?>
    <p align="center" class="alert-info">Factures par STATUT</p>
    <table class="table table-sm table-bordered">
        <thead class="bg-info">
        <tr>
            <th width="5">N°</th>
            <th width="100">CODE</th>
            <th>LIBELLE</th>
            <th width="150">NOMBRE</th>
            <th width="150">MONTANT</th>
            <th width="150">PART CMU</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $ligne = 1;
        $nombre = 0;
        $montant = 0;
        $part_cmu = 0;
        foreach ($factures as $facture) {
            $statut = $FACTURES->trouver_facture_statut($facture['STATUT']);
            ?>
            <tr>
                <td align="right"><?= $ligne;?></td>
                <td><b><?= $facture['STATUT'];?></b></td>
                <td><?= $statut['LIBELLE'];?></td>
                <td align="right"><?= number_format($facture['NOMBRE'],'0','',' ');?></td>
                <td align="right"><?= number_format($facture['MONTANT'],'0','',' ');?></td>
                <td align="right"><?= number_format(str_replace(",", ".", $facture['PART_CMU']),'0','',' ');?></td>
            </tr>
            <?php
            $nombre = $nombre + $facture['NOMBRE'];
            $montant = $montant + $facture['MONTANT'];
            $part_cmu = $part_cmu + str_replace(",", ".", $facture['PART_CMU']);
            $ligne++;
        }
        ?>
        <tr class="bg-danger text-white" style="font-weight: bolder">
            <td colspan="3">TOTAL</td>
            <td align="right"><?= number_format($nombre,'0','',' ');?></td>
            <td align="right"><?= number_format($montant,'0','',' ');?></td>
            <td align="right"><?= number_format($part_cmu,'0','',' ');?></td>
        </tr>
        </tbody>
    </table><hr />
    <?php
}




$factures = $FACTURES->lister_factures_par_type_facture($code_ets,$date_debut,$date_fin,$code_ps);
$nb_factures = count($factures);
if($nb_factures == 0) {
    echo '<p align="center" class="text-danger">Aucune information disponible sur cette période</p>';
}else {
    ?>
    <p align="center" class="alert-info">Factures par type de facture</p>
    <table class="table table-sm table-bordered">
        <thead class="bg-info">
        <tr>
            <th width="5">N°</th>
            <th width="100">CODE</th>
            <th>LIBELLE</th>
            <th width="150">NOMBRE</th>
            <th width="150">MONTANT</th>
            <th width="150">PART CMU</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $ligne = 1;
        $nombre = 0;
        $montant = 0;
        $part_cmu = 0;
        foreach ($factures as $facture) {
            $type = $FACTURES->trouver_type_facture($facture['TYPE_FACTURE']);
            ?>
            <tr>
                <td align="right"><?= $ligne;?></td>
                <td><b><?= $facture['TYPE_FACTURE'];?></b></td>
                <td><?= $type['LIBELLE'];?></td>
                <td align="right"><?= number_format($facture['NOMBRE'],'0','',' ');?></td>
                <td align="right"><?= number_format($facture['MONTANT'],'0','',' ');?></td>
                <td align="right"><?= number_format(str_replace(",", ".", $facture['PART_CMU']),'0','',' ');?></td>
            </tr>
            <?php
            $nombre = $nombre + $facture['NOMBRE'];
            $montant = $montant + $facture['MONTANT'];
            $part_cmu = $part_cmu + str_replace(",", ".", $facture['PART_CMU']);
            $ligne++;
        }
        ?>
        <tr class="bg-danger text-white" style="font-weight: bolder">
            <td colspan="3">TOTAL</td>
            <td align="right"><?= number_format($nombre,'0','',' ');?></td>
            <td align="right"><?= number_format($montant,'0','',' ');?></td>
            <td align="right"><?= number_format($part_cmu,'0','',' ');?></td>
        </tr>
        </tbody>
    </table><hr />
    <?php
}




$factures = $FACTURES->lister_factures_par_ogd($code_ets,$date_debut,$date_fin,$code_ps);
$nb_factures = count($factures);
if($nb_factures == 0) {
    echo '<p align="center" class="text-danger">Aucune information disponible sur cette période</p>';
}else {
    ?>
    <p align="center" class="alert-info">Factures par OGD</p>
    <table class="table table-sm table-bordered">
        <thead class="bg-info">
        <tr>
            <th width="5">N°</th>
            <th width="100">CODE OGD</th>
            <th>NOM OGD</th>
            <th width="150">NOMBRE</th>
            <th width="150">MONTANT</th>
            <th width="150">PART CMU</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $ligne = 1;
        $nombre = 0;
        $montant = 0;
        $part_cmu = 0;
        foreach ($factures as $facture) {
            ?>
            <tr>
                <td align="right"><?= $ligne;?></td>
                <td><b><?= $facture['CODE_OGD'];?></b></td>
                <td><?= $facture['LIBELLE_OGD'];?></td>
                <td align="right"><?= number_format($facture['NOMBRE'],'0','',' ');?></td>
                <td align="right"><?= number_format($facture['MONTANT'],'0','',' ');?></td>
                <td align="right"><?= number_format(str_replace(",", ".", $facture['PART_CMU']),'0','',' ');?></td>
            </tr>
            <?php
            $nombre = $nombre + $facture['NOMBRE'];
            $montant = $montant + $facture['MONTANT'];
            $part_cmu = $part_cmu + str_replace(",", ".", $facture['PART_CMU']);
            $ligne++;
        }
        ?>
        <tr class="bg-danger text-white" style="font-weight: bolder">
            <td colspan="3">TOTAL</td>
            <td align="right"><?= number_format($nombre,'0','',' ');?></td>
            <td align="right"><?= number_format($montant,'0','',' ');?></td>
            <td align="right"><?= number_format($part_cmu,'0','',' ');?></td>
        </tr>
        </tbody>
    </table>
    <?php
}
?>