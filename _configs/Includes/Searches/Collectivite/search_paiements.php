<?php
$date_debut = date('Y-m-d',strtotime($_POST['date_debut'])).' 00:00:00';
$date_fin = date('Y-m-d',strtotime($_POST['date_fin'])).' 23:59:59';
$code_collectivite = trim($_POST['code_collectivite']);
if(!empty($date_debut) && !empty($date_fin) && !empty($code_collectivite)) {
    require_once '../../../Classes/UTILISATEURS.php';
    require_once '../../../Classes/ASSURES.php';
    require_once '../../../Classes/COTISATIONS.php';
    $ASSURES = new ASSURES();
    $COTISATIONS = new COTISATIONS();

    $paiements = $COTISATIONS->trouver_cotisation_web($code_collectivite,$date_debut,$date_fin);
    $nb_paiements = count($paiements);
    if($nb_paiements == 0) {
        echo '<p align="center" class="text-danger">Aucun paiement effectué sur cette période.</p>';
    }else {
        ?>
        <p align="right"><button type="button" id="btn_imprimer_etat" class="btn btn-warning btn-sm"><i class="fa fa-print"></i> Imprimer</button></p>
        <input type="hidden" value="<?= $_POST['date_debut'];?>" id="date_debut_input" />
        <input type="hidden" value="<?= $_POST['date_fin'];?>" id="date_fin_input" />
        <p align="center" class="text-success"><b><?= $nb_paiements;?></b> paiement(s) effectué(s) de <?= date('d-m-Y',strtotime($date_debut)).' au '.date('d-m-Y',strtotime($date_fin));?></p>
        <table class="table table-bordered table-sm tavle-hover">
            <thead class="bg-info">
            <tr>
                <th width="50">N°</th>
                <th width="100">DATE</th>
                <th width="150">N° TRANSACTION</th>
                <th>TYPE</th>
                <th width="120">N° SECU</th>
                <th>NOM</th>
                <th>PRENOMS</th>
                <th>MONTANT</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $ligne = 1;
            $montant = 0;
            foreach ($paiements as $paiement) {
                $assure = $ASSURES->trouver_assure($paiement['NUM_SECU']);
                ?>
                <tr>
                    <td align="right"><?= $ligne;?></td>
                    <td><?= date('d/m/Y',strtotime($paiement['DATE_REG']));?></td>
                    <td><?= $paiement['NUM_TRANSACTION'];?></td>
                    <td><?= str_replace('I','INDIVIDUEL',str_replace('F','FAMILIAL',$paiement['PAYMENT_TYPE']));?></td>
                    <td><?= $assure['NUM_SECU'];?></td>
                    <td><?= $assure['NOM'];?></td>
                    <td><?= $assure['PRENOM'];?></td>
                    <td align="right"><?= number_format($paiement['PAID_TRANSACTION_AMOUNT'],'0','',' ');?></td>
                </tr>
                <?php
                $montant = $montant + $paiement['PAID_TRANSACTION_AMOUNT'];
                $ligne++;
            }
            ?>
            <tr class="bg-success" style="font-weight: bold">
                <td colspan="7">TOTAL</td>
                <td align="right"><?= number_format($montant,'0','',' ');?></td>
            </tr>
            </tbody>
        </table>
        <?php
    }
    $bdd = null;
}
?>
<script>
    $("#btn_imprimer_etat").click(function () {
        var date_debut = $("#date_debut_input").val(),
            date_fin = $("#date_fin_input").val();
        mywindow = window.open('imprimer-etat.php?date_debut='+date_debut+'&date_fin='+date_fin,'Reçu de paiement','height=500,width=1500');
        mywindow.focus();
        return false;
    });
</script>
