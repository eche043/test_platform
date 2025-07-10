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
                $centre = $UTILISATEURS->trouver_centre_coordination($user['ID_UTILISATEUR']);
                if($centre){
                    if(isset($_POST['code_ets']) && !empty($_POST['code_ets']))
                    {
                        $ets = $COORDINATIONS->trouver_ets($centre['CODE_CENTRE'],$_POST['code_ets']);
                        if($ets)
                        {
                            $historiques = $COORDINATIONS->historique_terminaux_centre($ets['CODE_ETS']);
                            $nb_historiques = count($historiques);
                                ?>
                                    <div class="col">

                                        <div class="col">
                                            <p class="titres_p">
                                                <i class="fa fa-newspaper"></i> HISTORIQUE DES Terminaux Biométriques </p>
                                            <p>
                                                <a href="<?= URL.'centre-coordination/terminaux-biometriques-edition.php?code-ets='. $ets['CODE_ETS'] ;?>" class="btn btn-sm btn-success"><i class="fa fa-plus"></i> Ajouter un Nouveau terminal</a>
                                            </p>
                                            <hr />
                                            <?php
                                            if($nb_historiques != '0')
                                            {
                                                ?>
                                                <table class="table table-bordered table-hover table-sm table-responsive-sm dataTable mt-3">
                                                    <thead class="bg-secondary text-white">
                                                    <tr>
                                                        <th width="5">N°</th>
                                                        <th width="100">N° IMEI</th>
                                                        <th width="100">N° TELEPHONE</th>
                                                        <th width="120">CODE ETS</th>
                                                        <th width="10">TYPE TERMINAL</th>
                                                        <th width="10">DATE DEBUT</th>
                                                        <th width="10">DATE FIN</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php
                                                    $ligne = 1;
                                                    foreach ($historiques as $historique) {

                                                        $infos_terminal = $COORDINATIONS->detail_terminaux($historique['TERMINAL'] ,$historique['CODE_ETS']);
                                                        ?>
                                                        <tr>
                                                            <td align="right"><b><?= $ligne;?></b></td>
                                                            <td><b><?= $infos_terminal['TERMINAL'] ?></b></td>
                                                            <td><?= $infos_terminal['NUMERO_TELEPHONE'] ?></td>
                                                            <td><?= $infos_terminal['ETABLISSEMENT'] ?></td>
                                                            <td><?= $infos_terminal['TERMINAL_TYPE'] ?></td>
                                                            <td><?= date('d/m/Y',strtotime(date( $historique['DATE_DEBUT']))) ?></td>
                                                            <td><?= date('d/m/Y',strtotime(date( $historique['DATE_FIN']))) ?></td>

                                                        </tr>
                                                        <?php
                                                        $ligne++;
                                                    }
                                                    ?>
                                                    </tbody>
                                                </table>
                                                <?php
                                            }
                                            else
                                            {
                                                ?>
                                                <p class="align_center alert alert-info">AUCUN TERMINAL BIOMETRIQUE ENREGISTRE POUR CET ETABLISSEMENT DE SANTE</p>
                                                <?php
                                            }
                                            ?>
                                        </div>

                                        <script type="application/javascript" src="<?= JS.'page_centre_coordination_terminaux.js';?>"></script>

                                </div>
                                <?php
                        }else{
                            echo '<script>window.location.href="'.URL.'centre-coordination/"</script>';
                        }
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
