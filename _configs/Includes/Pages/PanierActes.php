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

            require_once '../../Classes/ACTESMEDICAUX.php';
            $ACTESMEDICAUX = new ACTESMEDICAUX();
            $actes_medicaux = $ACTESMEDICAUX->trouver_acte();
            ?>
            <div class="col-12">
                <p align="center" class="display-4"><a href="<?= URL.'panier-soins.php';?>"><b class="fa fa-chevron-circle-left"></b></a> Actes médicaux</p><br />
                <?php include '../Pages/Forms/form_recherche_panier_acte.php'?>
                <div id="div_resultats_recherche"></div>
            </div>
            <script type="application/javascript" src="<?= JS.'ecmu.js';?>"></script>
            <?php
        }
    }

}else{
    session_destroy();
    echo '<script>window.location.href="'.URL.'connexion.php"</script>';
}
?>
