<?php
$num_secu = trim($_POST['num_secu']);
if(!empty($num_secu)) {
    require_once '../../../Classes/UTILISATEURS.php';
    require_once '../../../Classes/ASSURES.php';
    $ASSURES = new ASSURES();

    $cotisations = $ASSURES->trouver_liste_paiements($num_secu);
    $nb_cotisations = count($cotisations);
    if($nb_cotisations == 0) {
        echo '<p align="center" class="text-danger">Aucun paiement externe encore effectué</p>';
    }else {
        ?>
        <table class="table table-bordered table-hover table-stripted table-sm">
            <thead class="bg-dark text-white">
            <tr>
                <th width="50">N°</th>
                <th width="100">DATE</th>
                <th width="150">N° TRANSACTION</th>
                <th width="150">TYPE</th>
                <th width="150">MONTANT</th>
                <th width="100">DEVISE</th>
                <th>WALLET</th>
                <th width="5"></th>
                <th width="5"></th>
            </tr>
            </thead>
            <tbody>
            <?php
            $ligne = 1;
            foreach ($cotisations as $cotisation) {
                if($ligne <= 9) {
                    ?>
                    <tr>
                        <td align="right"><?= $ligne;?></td>
                        <td><?= date('d/m/Y',strtotime($cotisation['DATE_REG']));?></td>
                        <td><b><?= $cotisation['NUM_TRANSACTION'];?></b></td>
                        <td><?= str_replace('I','INDIVIDUEL',str_replace('F','FAMILIAL',$cotisation['PAYMENT_TYPE']));?></td>
                        <td align="right"><b><?= number_format($cotisation['TRANSACTION_AMOUNT'],'0','', ' ');?></b></td>
                        <td><?= $cotisation['CURRENCY'];?></td>
                        <td><?= $cotisation['WALLET'];?></td>
                        <td>
                            <?php
                            if($cotisation['STATUT'] == 0) {
                                echo '<i class="fa fa-times-circle text-danger"></i>';
                            }else {
                                echo '<i class="fa fa-check-circle text-success"></i>';
                            }
                            ?>
                        </td>
                        <td><button type="button" class="badge badge-info btn_imprimer" id="<?= $cotisation['NUM_TRANSACTION'].'_'.$cotisation['NUM_ORDRE'];?>"><i class="fa fa-eye"></i></button></td>
                    </tr>
                    <?php
                }
                $ligne++;
            }
            ?>
            </tbody>
        </table>
        <?php
    }
}
?>
<script>
    $(".btn_imprimer").click(function () {
        var id = this.id,
            table = id.split('_');
        transaction_id = table[0],
            order_id = table[1];
        mywindow = window.open('imprimer-recu.php?transaction_id='+transaction_id+'&order_num='+order_id,'Reçu de paiement','height=500,width=500');
        mywindow.focus();
        return true;
    });


</script>
