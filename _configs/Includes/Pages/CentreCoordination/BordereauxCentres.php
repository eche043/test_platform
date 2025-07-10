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
            if(in_array('COORD',$modules))
            {
                require_once '../../../Classes/COORDINATIONS.php';
                $COORDINATIONS = new COORDINATIONS();
                $centre = $UTILISATEURS->trouver_centre_coordination($user['ID_UTILISATEUR']);
                if($centre)
                {
                    $etablissements = $COORDINATIONS->lister_ets($centre['CODE_CENTRE']);
                    $nb_ets = count($etablissements);
                    ?>
                    <div class="col">
                        <p class="titres_p"><i class="fa fa-newspaper"></i> Bordereaux par Etablissement</p>
                        <?php
                        if($nb_ets) {
                            ?>
                            <table class="table table-bordered table-hover table-sm table-responsive-sm dataTable">
                                <thead class="bg-secondary text-white">
                                <tr>
                                    <th width="5">N°</th>
                                    <th width="100">CODE ETS</th>
                                    <th>RAISON SOCIALE</th>
                                    <th>VILLE</th>
                                    <th width="120">DATE DEBUT</th>
                                    <th width="5"></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $ligne = 1;
                                foreach ($etablissements as $etablissement) {
                                    ?>
                                    <tr>
                                        <td align="right"><b><?= $ligne;?></b></td>
                                        <td><b><?= $etablissement['CODE_ETS'] ?></b></td>
                                        <td><?= $etablissement['RAISON_SOCIALE'] ?></td>
                                        <td><?= $etablissement['VILLE'] ?></td>
                                        <td align="center"><?= date("d/m/Y",strtotime($etablissement['DATE_DEBUT'])) ?></td>
                                        <td>
                                            <a href="bordereaux.php?code-ets=<?= $etablissement['CODE_ETS'] ?>"><i class="fa fa-eye" aria-hidden="true"></i></a>
                                        </td>
                                    </tr>
                                    <?php
                                    $ligne++;
                                }
                                ?>
                                </tbody>
                            </table>
                            <?php
                        }else {
                            echo '<p class="align_center alert alert-info">AUCUN BORDEREAU TROUVE POUR CE CENTRE DE COORDINATION</p>';
                        }
                        ?>
                    </div>

                    <script type="text/javascript" src="<?= JS.'page_centre_coordination.js'?>"></script>
                    <?php
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
        $('#dataTable').DataTable();
    });

</script>