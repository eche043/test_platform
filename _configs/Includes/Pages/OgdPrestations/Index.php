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
            if(in_array('OGDP',$modules)) {
               require_once '../../../Classes/OGD.php';
                $OGD = new OGD();
                $ogd = $OGD->trouver('PRST',$user['CODE_OGD_P']);
                ?>
                <div class="col" id="">

                    <p class="h3" align="center">
                        ESPACE OGD PRESTATIONS CMU
                        <br>
                    </p>
                    <p align="center">
                        <a href="#"><i class="fa fa-book-reader"></i> <b><?= $user['CODE_OGD_P'].' : <u>'.$ogd['LIBELLE'] ?></u></b></a>
                    </p>

                </div>
                    <script type="text/javascript" src="<?= JS.'page_ogd_prestations.js'?>"></script>
                <?php
            }else {
                echo '<script>window.location.href="'.URL.'"</script>';
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
