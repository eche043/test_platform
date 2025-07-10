<?php

require_once '../../../Classes/UTILISATEURS.php';
if(isset($_SESSION['ECMU_USER_ID'])) {
    $session_user = $_SESSION['ECMU_USER_ID'];
    if (!empty($session_user)) {
        $UTILISATEURS = new UTILISATEURS();
        $utilisateur_existe = $UTILISATEURS->trouver($session_user, null, null);

        if (!empty($utilisateur_existe['ID_UTILISATEUR'])) {


 $date_debut = date('Y-m-d',strtotime($_POST['date_debut'])).' 00:00:00';
 $date_fin = date('Y-m-d',strtotime($_POST['date_fin'])).' 23:59:59';
 $code_mutuelle = trim($_POST['code_mutuelle']);
if(!empty($date_debut) && !empty($date_fin) && !empty($code_mutuelle)) {
    require_once '../../../Classes/ASSURANCE.php';
    $ASSURANCE = new ASSURANCE();

    $assurance = $ASSURANCE->liste_assures_par_mutuelle($code_mutuelle,$date_debut,$date_fin);
    $nb = count($assurance);
    if($nb == 0) {
        echo '<p align="center" class="text-danger">Aucun souscripteur enrolé à la CMU.</p>';
    }else {
        ?>
        <p align="right"><button type="button" id="btn_imprimer_etat" class="btn btn-warning btn-sm"><i class="fa fa-print"></i> Imprimer</button></p>
        <input type="hidden" value="<?= $_POST['DATE_DEBUT'];?>" id="date_debut_input" />
        <input type="hidden" value="<?= $_POST['DATE_FIN'];?>" id="date_fin_input" />
        <p align="center" class="text-success"><b><?= $nb;?></b> enregistrement(s) effectué(s) de <?= date('d-M-Y',strtotime($date_debut)).' au '.date('d-M-Y',strtotime($date_fin));?></p>
        <table class="table table-bordered table-sm tavle-hover">
            <thead class="bg-info">
            <tr>
                <th width="50">N°</th>
                <th width="100">N° SECU</th>
                <th width="100">N° SECU PAYEUR </th>
                <th>NOM</th>
                <th>PRENOMS</th>
                <th>DATE NAISSANCE</th>
                <th>TYPE PIECE</th>
                <th>NUMERO PIECE</th>
                <th>NUMERO POLICE</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $ligne = 1;
            $i = 0;
            foreach ($assurance as $a) {
                ?>
                <tr>
                    <td align="right"><?= $ligne;?></td>
                    <td><?= $a['NUM_SECU'];?></td>
                    <td><?= $a['NUM_SECU_PAYEUR'];?></td>
                    <td><?= $a['NOM'];?></td>
                    <td><?= $a['PRENOMS'];?></td>
                    <td><?= date('d-M-y',strtotime($a['DATE_NAISSANCE']));?></td>
                    <td><?= $a['CODE_TYPE_PIECE'];?></td>
                    <td><?= $a['NUMERO_PIECE'];?></td>
                    <td><?= $a['NUMERO_POLICE'];?></td>
                </tr>
                <?php
                $i++;
                $ligne++;
            }
            ?>
            <tr class="bg-success" style="font-weight: bold">
                <td colspan="8">TOTAL</td>
                <td align="right"><?= number_format($i,'0','',' ');?></td>
            </tr>
            </tbody>
        </table>
        <?php
    }

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

<?php        }
    }

}

?>