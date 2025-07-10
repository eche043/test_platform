<?php
require_once '../../../Classes/UTILISATEURS.php';

if(isset($_SESSION['ECMU_USER_ID']) || !empty($_SESSION['ECMU_USER_ID'])) {
    $UTILISATEURS = new UTILISATEURS();
    $user = $UTILISATEURS->trouver($_SESSION['ECMU_USER_ID'],NULL,NULL);
//    var_dump($user_ETS);
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
            ?>
            <div class="col">
                <?php
                $user_ETS = $UTILISATEURS->trouver_ets_utilisateur($user['ID_UTILISATEUR']);
                if(in_array('AGAC',$modules)) {
                    require_once '../../../Classes/BORDEREAUX.php';
                    require_once '../../../Classes/CICMU.php';
                    require_once '../../../Classes/FACTURES.php';
                    require_once '../../../Classes/OGD.php';
                    $BORDEREAUX = new BORDEREAUX();
                    $CICMU = new CICMU();
                    $FACTURES = new FACTURES();
                    $OGD = new OGD();
                    if(isset($_POST['num']) && !empty($_POST['num'])){
                        $code_ets = $user_ETS['CODE_ETS'];
                        $num_bordereau = $_POST['num'];
                        $bordereau = $BORDEREAUX->trouver($code_ets,$num_bordereau);
                        if($bordereau){
                            $ogd = $OGD->trouver('PRST',$bordereau['NUM_OGD_BORDEREAU']);
                            if(!empty($bordereau['CODE_AC'])){
                                $factures = $CICMU->lister_bordereaux_facture($bordereau['CODE_ETS_BORDEREAU'], $bordereau['NUMERO_BORDEREAU']);
                            }
                            else {
                                $factures = $BORDEREAUX->lister_bordereaux_facture($bordereau['CODE_ETS_BORDEREAU'], $bordereau['NUMERO_BORDEREAU']);
                            }
                            $nbre_actes = 0;
                            $part_cmu = 0;
                            $part_ac = 0;
                            foreach ($factures as $fact){
                                if(!empty($bordereau['CODE_AC'])){
                                    $part_cmu = $part_cmu+(float)$fact['PART_CMU'];
                                    $part_ac = $part_ac+(float)$fact['PART_AC'];
                                    $nbre_actes = $nbre_actes + $fact['NOMBRE_ACTES'];
                                }
                                else {
                                    $trouver_facture = $FACTURES->trouver($fact['FEUILLE']);
                                    $nbre_actes = $nbre_actes + $fact['NOMBRE_ACTES'];
                                    if ($trouver_facture['CODE_OGD_AFFILIATION'] === '03016000') {
                                        $part_ac = $fact['MONTANT'] - $fact['PART_CMU'];
                                    }
                                    $part_cmu = $part_cmu + ((float)$fact['PART_CMU'] + $part_ac);
                                }
                            }

                            ?>

                            <p class="titres_p"><i class="fa fa-newspaper"></i> Bordereau de transmission n° <?= $_POST['num'];?></p>
                            <table width="100%">
                                <tr>
                                    <input type="hidden" id="code_ets_input" value="<?= $user_ETS['CODE_ETS'];?>" />
                                    <td width="150">Date de la facture</td>
                                    <td width="450"><b><?= date('d/m/Y',strtotime($bordereau['DATE_REG']));?></b></td>
                                    <td width="150">Période</td>
                                    <td>du <b><?= date('d/m/Y',strtotime($bordereau['DATE_DEBUT_PERIODE']));?></b> au <b><?= date('d/m/Y',strtotime($bordereau['DATE_FIN_PERIODE']));?></b></td>
                                </tr>
                                <tr>
                                    <td>N° de la facture</td>
                                    <td><b id="num_bordereau_b"><?= $bordereau['NUMERO_BORDEREAU'];?></b></td>
                                    <td>OGD</td>
                                    <td><b><?= $ogd['LIBELLE'];?></b></td>
                                </tr>
                                <tr>
                                    <td>Nbr. de factures</td>
                                    <td><b><?= COUNT ($factures);?></b></td>
                                    <td>Type de factures</td>
                                    <td><b><?= $bordereau['TYPE_FEUILLE'];?></b></td>
                                </tr>
                                <tr>
                                    <td>Nbr. d'actes</td>
                                    <td><b><?= $nbre_actes;?></b></td>
                                    <td>Part CMU</td>
                                    <td><b><?= $part_cmu ?> F CFA</b></td>
                                </tr>
                                <tr>
                                    <td>Assurance Compl.</td>
                                    <td><b><?= $bordereau['CODE_AC'];?></b></td>
                                    <td>Part AC</td>
                                    <td><b><?= $part_ac ?> F CFA</b></td>
                                </tr>
                            </table><hr />

                            <p align="center"><button type="button" id="imprimer_bordereau" class="btn btn-warning btn-sm"><i class="fa fa-print"></i> Imprimer le bordereau</button></p>
                            <table class="table table-sm table-bordered table-hover">
                                <thead class="bg-primary">
                                <tr>
                                    <th>N°</th>
                                    <th>N° Sécu</th>
                                    <th>N° Facture</th>
                                    <th>N° Fs. Initiale</th>
                                    <th width="100">Date soins</th>
                                    <th width="110">Code Prestataire</th>
                                    <th width="100">Code affection</th>
                                    <th width="100">Montant_total</th>
                                    <th width="100">Part assuré</th>
                                    <th width="100">Part AC</th>
                                    <th width="100">Part CMU</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $ligne = 1;

                                foreach ($factures as $facture) {
                                    if(!empty($bordereau['CODE_AC'])) {
                                        $part_cmu_facture = $facture['PART_CMU'];
                                        $part_ass_compl = $facture['PART_AC'];
                                        $part_assure = $facture['MONTANT'] - ((float)$facture['PART_CMU']+(float)$facture['PART_AC']);
                                    }
                                    else{
                                        if ($facture['CODE_OGD_AFFILIATION'] === '03016000') {
                                            $part_ass_compl = $facture['MONTANT'] - $facture['PART_CMU'];
                                            $part_assure = 0;
                                        } else {
                                            $part_ass_compl = 0;
                                            $part_assure = $facture['MONTANT'] - $facture['PART_CMU'];
                                        }
                                        $part_cmu_facture =round($part_cmu + $part_ass_compl);
                                    }
                                    ?>
                                    <tr>
                                        <td align="right"><?= $ligne;?></td>
                                        <td align="center"><?= $facture['NUM_SECU'];?></td>
                                        <td align="right"><?= $facture['FEUILLE'];?></td>
                                        <td align="right"><?= $facture['NUM_FS_INITIALE'];?></td>
                                        <td align="center"><?= date('d/m/Y',strtotime($facture['DATE_SOINS']));?></td>
                                        <td align="center"><?= $facture['CODE_PS'];?></td>
                                        <td align="center"><?= $facture['AFFECTION1'];?></td>
                                        <td align="right"><?= number_format($facture['MONTANT'],'0','',' ');?></td>
                                        <td align="right"><?= number_format($part_assure,'0','',' ');?></td>
                                        <td align="right"><?= number_format($part_ass_compl,'0','',' ');?></td>
                                        <td align="right"><?= number_format($part_cmu_facture,'0','',' ');?></td>
                                    </tr>
                                    <?php
                                    $ligne++;
                                }
                                ?>
                                </tbody>
                            </table>
                            <?php
                        }else{
                            echo '<script>window.location.href="'.URL.'agent/bordereaux.php"</script>';
                        }
                    }else{
                        echo '<script>window.location.href="'.URL.'agent/bordereaux.php"</script>';
                    }

                }
                ?>
            </div>
            <?php
        }
    }

}else{
    session_destroy();
    echo '<script>window.location.href="'.URL.'"</script>';
}

?>
<script>
    $(function () {
        $('#dataTable').DataTable();
    });

    $("#imprimer_bordereau").click(function() {
        var num_bordereau = $("#num_bordereau_b").html(),
            code_ets = $("#code_ets_input").val();
        mywindow = window.open('bordereau-impression.php?num='+num_bordereau+'&code_ets='+code_ets,'Feuille de soins','height=700,width=1000');
        mywindow.focus();
        return true;
    });
</script>