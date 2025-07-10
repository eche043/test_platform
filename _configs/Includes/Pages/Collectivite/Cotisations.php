<?php
require_once '../../../Classes/UTILISATEURS.php';
require_once '../../../Classes/COLLECTIVITES.php';
$COLLECTIVITES = new COLLECTIVITES();

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
            if(in_array('ENT',$modules)) {
                $code_collectivite = $_POST['code-collectivite'];
                $user_collectivite = $COLLECTIVITES->trouver($code_collectivite);

                $annees_declarations = $COLLECTIVITES->lister_annees_declarations($user_collectivite['CODE_OGD_COTISATIONS'],$code_collectivite);
                if(!isset($_POST['annee'])) {
                    if(count($annees_declarations)==0){
                        $annee_t = date('Y',time());
                    }else{
                        $annee_t = $annees_declarations[0]['ANNEE'];
                    }
                }else {
                    if(empty($_POST['annee'])) {
                        if(count($annees_declarations)==0){
                            $annee_t = date('Y',time());
                        }else{
                            $annee_t = $annees_declarations[0]['ANNEE'];
                        }
                    }else {
                        $annee_t = $_POST['annee'];
                    }
                }

                $cotisations = $COLLECTIVITES->liste_des_declarations_cotisations_annee($user_collectivite["CODE_OGD_COTISATIONS"], $code_collectivite, $annee_t);

                $chemin = '../_publics/images/logos_collectivites/';

                ?>
                <style>
                    .ui-datepicker-calendar {
                        display: none;
                    }
                </style>
                <div class="col">
                    <input type="hidden" id="code_collectivite_input" name="code_collectivite_input" value="<?=$code_collectivite;?>">
                    <p class="titres_p"><b class="fa fa-money-bill-wave-alt"></b> Cotisations</p>
                    <div>
                        <nav class="navbar navbar-light">
                            <div class="navbar-brand">
                                <button type="button" id="btn_nouvelle_cotisation_collectivite" class="btn btn-sm btn-success" data-toggle="modal" data-target="#modalCotisationCollectivite" > Déclarer Cotisations</button>
                            </div>
                        </nav>
                        <?php include "../Forms/form_cotisations_collectivite.php";?>
                        <hr>
                        <?php
                        if(count($annees_declarations)==0){
                            echo '<p class="alert alert-warning" align="center"><b>AUCUNE DECLARATION</b></p>';
                        }else{
                            $annee_tod = date('Y',time());
                            ?>
                        <div>
                            <tr class="bg-white">
                                <th colspan="10">
                                    <?php
                                    foreach ($annees_declarations as $annees_declaration) {
                                        ?>
                                        <a href="<?= URL.'collectivite/cotisations.php?code-collectivite='.$code_collectivite.'&annee='.$annees_declaration['ANNEE'];?>" class="btn btn-sm <?php if($annees_declaration['ANNEE'] == $annee_t){echo 'btn-outline-dark disabled';}else {echo 'btn-dark';} ?>"><?= $annees_declaration['ANNEE'];?></a>&nbsp;
                                        <?php
                                    }
                                    ?>
                                </th>
                            </tr>
                            <p></p>
                            <?php
                                if(count($cotisations)==0){
                                    echo '<p class="alert alert-warning" align="center"><b>AUCUNE DECLARATION</b></p>';
                                }else {
                                    ?>
                                    <table class="table table-bordered table-hover table-sm">
                                        <thead class="bg-secondary text-white">
                                        <tr align="center">
                                            <td width="5"></td>
                                            <td>DATE DECLARATION</td>
                                            <td>ID DECLARATION</td>
                                            <td>ID PAIEMENT</td>
                                            <td>PERIODE</td>
                                            <td>POPULATION</td>
                                            <td>MONTANT DECLARE</td>
                                            <td>MONTANT PAYE</td>
                                            <td width="5"></td>
                                        </tr>
                                        </thead>

                                        <tbody>
                                        <?php
                                        $i = 1;
                                        $total_paye = 0;
                                        $total_declare = 0;
                                        foreach ($cotisations as $cot) {
                                            ?>
                                            <tr>
                                                <td width="5" align="right"><?= $i; ?></td>
                                                <td align="center"><?= date('d/m/Y',strtotime($cot['DATE_DECLARATION']));?></td>
                                                <td align="right"><?= $cot['ID_DECLARATION'];?></td>
                                                <td align="right"><?= $cot['ID_PAIEMENT'];?></td>
                                                <td align="center"><?= $cot['MOIS'].'/'.$cot['ANNEE'];?></td>
                                                <td align="right"><?= number_format($cot['EFFECTIF'],'0','',' '); ?></td>
                                                <td align="right"><?= number_format($cot['MONTANT'],'0','',' '). ' F CFA'; ?></td>
                                                <td align="right"><?= ''; ?></td>
                                                <td align="center"><button type="button" class="badge badge-info btn_imprimer_declaration" id="<?= $cot['ID_DECLARATION'];?>"><i class="fa fa-print"></i></button></td>
                                            </tr>
                                            <?php
                                            $total_paye = $total_paye+$cot['MONTANT'];
                                            $total_declare = $total_declare+0;
                                            $i++;
                                        }
                                        ?>
                                        <tr class="bg-danger text-white" style="font-weight: bold">
                                            <td colspan="6"> TOTAL </td>
                                            <td align="right"><?= number_format($total_paye,'0','',' ').' F CFA'; ?></td>
                                            <td align="right"><?= ''; ?></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <?php
                                }
                            ?>
                        </div>
                        <?php
                        }
                        ?>
                    </div>
                </div>
                <script type="text/javascript" src="<?= JS.'page_collectivite.js'?>"></script>
            <?php }
        }
    }
}else{
    session_destroy();
    echo '<script>window.location.href="'.URL.'"</script>';
}
?>

