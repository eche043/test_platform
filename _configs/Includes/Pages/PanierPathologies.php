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
            <div class="col-12">
                <p align="center" class="display-4"><a href="<?= URL.'panier-soins.php';?>"><b class="fa fa-chevron-circle-left"></b></a> Pathologies</p><br />
                <?php include "../Pages/Forms/form_recherche_pathologies.php"?>
                <div id="div_resultats_recherche"></div>
                <script type="application/javascript" src="<?= JS.'ecmu.js';?>"></script>
            </div>
            <?php
        }
    }

}else{
    session_destroy();
    echo '<script>window.location.href="'.URL.'connexion.php"</script>';
}
?>
