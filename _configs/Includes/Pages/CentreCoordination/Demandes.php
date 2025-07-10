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
                            ?>
                            <div class="col">
                                <p class="titres_p"><i class="fa fa-newspaper"></i> <?= $ets['RAISON_SOCIALE'] ?></p>
                                <div class="row justify-content-md-center">
                                    <div class="col-sm-3" data-aos="zoom-out-up">
                                        <a href="<?= URL.'centre-coordination/ententes-prealables.php?code-ets='.$ets['CODE_ETS'];?>" class="btn btn-block btn-lg btn-outline-primary">Entente Préalable</a>
                                    </div>
                                    <div class="col-sm-3" data-aos="zoom-out-up">
                                        <a href="<?= URL.'centre-coordination/attestations-droits.php?code-ets='.$ets['CODE_ETS'];?>" class="btn btn-block btn-lg btn-outline-primary">Attestation de droits</a>
                                    </div>
                                    <div class="col-sm-3" data-aos="zoom-out-down">
                                        <a href="<?= URL.'centre-coordination/cmr.php?code-ets='.$ets['CODE_ETS'];?>" class="btn btn-block btn-lg btn-outline-primary">Centre médical réferent</a>
                                    </div>
                                </div>
                            </div>

                            <script type="text/javascript" src="<?= JS.'page_centre_coordination_bordereaux.js'?>"></script>
                            <?php
                        }
                    }else
                    {
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
        $('.dataTable').DataTable();
    });

</script>