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
                    $ets = $COORDINATIONS->trouver_ets($centre['CODE_CENTRE'],$_POST['code_ets']);
                    if($ets)
                    {
                        if(isset($_POST['num']) && !empty($_POST['num'])) {
                            require_once '../../../Classes/FACTURES.php';
                            $FACTURES = new FACTURES();

                            $facture = $FACTURES->trouver($_POST['num']);
                            if(!empty($facture['FEUILLE'])) {
                                if($ets['CODE_ETS'] == $facture['ETABLISSEMENT']) {
                                    require_once '../../../Classes/ASSURES.php';
                                    $ASSURES = new ASSURES();
                                    $assure = $ASSURES->trouver($facture['NUM_SECU']);
                                    if(!empty($assure['NUM_SECU'])) {
                                        if($facture['STATUT'] != 'A') {
                                            ?>
                                            <div class="container">
                                                <p class="titres_p"><i class="fa fa-trash"></i> Annulation facture n°: <b id="num_facture_b"><?= $facture['FEUILLE'];?></b></p>
                                                <p id="p_resultat_annulation_facture"></p>
                                                <?php include "../Forms/form_facture_annulation.php";?>
                                            </div>
                                            <script type="application/javascript" src="<?= JS.'page_centre_coordination_facture.js';?>"></script>
                                            <?php
                                        }else {
                                            echo '<script>window.location.href="'.URL.'centre-coordination/"</script>';
                                        }
                                    }else {
                                        echo '<script>window.location.href="'.URL.'centre-coordination/"</script>';
                                    }
                                }else {
                                    echo '<script>window.location.href="'.URL.'centre-coordination/"</script>';
                                }
                            }else{
                                echo '<script>window.location.href="'.URL.'centre-coordination/"</script>';
                            }
                        }else {
                            echo '<script>window.location.href="'.URL.'centre-coordination/"</script>';
                        }
                        ?>


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
        $('.dataTable').DataTable();
    });

</script>