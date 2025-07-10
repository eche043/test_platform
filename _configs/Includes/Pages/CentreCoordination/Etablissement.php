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
                require_once '../../../Classes/COORDINATIONS.php';
                $COORDINATIONS = new COORDINATIONS();
                $centre = $UTILISATEURS->trouver_centre_coordination($user['ID_UTILISATEUR']);
                if($centre){
                    $ets = $COORDINATIONS->trouver_ets($centre['CODE_CENTRE'],$_POST['code']);
                    if($ets)
                    {
                        ?>
                        <div class="col">
                            <p class="titres_p"><i class="fa fa-newspaper"></i><?= $ets['RAISON_SOCIALE'] ?></p>
                            <div class="container">
                                <div class="row justify-content-md-center">
                                    <div class="col-sm-3" data-aos="zoom-out-up">
                                        <a href="<?= URL.'centre-coordination/factures.php?code-ets='.$ets['CODE_ETS'];?>" class="btn btn-block btn-lg btn-outline-primary">Factures</a>
                                    </div>
                                    <div class="col-sm-3" data-aos="zoom-out-up">
                                        <a href="<?= URL.'centre-coordination/terminaux-biometriques.php?code-ets='.$ets['CODE_ETS'];?>" class="btn btn-block btn-lg btn-outline-primary">Terminaux biométriques</a>
                                    </div>
                                    <div class="col-sm-3" data-aos="zoom-out-down">
                                        <a href="<?= URL.'centre-coordination/professionnels-sante.php?code-ets='.$ets['CODE_ETS'];?>" class="btn btn-block btn-lg btn-outline-primary">Professionnels de santé</a>
                                    </div>
                                    <div class="col-sm-3" data-aos="zoom-out-down">
                                        <a href="<?= URL.'centre-coordination/bordereaux.php?code-ets='.$ets['CODE_ETS'];?>" class="btn btn-block btn-lg btn-outline-primary">Bordereaux</a>
                                    </div>
                                </div>
                                <br>
                                <div class="row justify-content-md-center">
                                    <div class="col-sm-3" data-aos="zoom-out-down">
                                        <a href="<?= URL.'centre-coordination/demandes.php?code-ets='.$ets['CODE_ETS'];?>" class="btn btn-block btn-lg btn-outline-primary">Demandes</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <script type="text/javascript" src="<?= JS.'page_centre_coordination.js'?>"></script>
                        <?php
                    }else{
                        echo '<script>window.location.href="'.URL.'centre-coordination/"</script>';
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
        $('#dataTable').DataTable();
    });

</script>