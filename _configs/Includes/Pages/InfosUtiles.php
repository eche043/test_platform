<?php
require_once '../../Classes/UTILISATEURS.php';

if(isset($_SESSION['ECMU_USER_ID']) || !empty($_SESSION['ECMU_USER_ID'])) {
    $UTILISATEURS = new UTILISATEURS();
    $user = $UTILISATEURS->trouver($_SESSION['ECMU_USER_ID'],NULL,NULL);
    if(empty($user['ID_UTILISATEUR'])) {
        session_destroy();
        echo '<script>window.location.href="'.URL.'connexion.php"</script>';
    }else {
        $modules = array_diff(explode(';',stream_get_contents($user['PROFIL'],-1)),array(""));
        //var_dump($modules);
        $nb_modules = count($modules);
        if($nb_modules == 0) {
            session_destroy();
            echo '<script>window.location.href="'.URL.'connexion.php"</script>';
        }else{
            ?>
            <div class="container">

                <div class="row" style="margin-top: 100px">

                    <div class="col-sm-4">
                        <a href="<?= URL.'panier-soins.php';?>" class="btn btn-block btn-sm btn-outline-primary box_profils"><i class="fa fa-shopping-cart"></i> Panier de soins</a>
                    </div>

                    <div class="col-sm-4">
                        <a href="<?= URL.'reseau-soins.php';?>" class="btn btn-block btn-sm btn-outline-primary box_profils"><i class="fa fa-map-marked-alt"></i>Réseau de soins</a>
                    </div>

                    <div class="col-sm-4" hidden>
                        <button type="button" id="deconnexion_link" class="bg-danger btn btn-block btn-sm btn-info box_profils">
                            <i class="fa fa-power-off"></i> Déconnexion
                        </button>
                    </div>
                </div>
            </div>

            <?php
        }
    }

}else{
    session_destroy();
    echo '<script>window.location.href="'.URL.'connexion.php"</script>';
}
?>
