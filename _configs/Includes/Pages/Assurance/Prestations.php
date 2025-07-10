<?php
require_once '../../../Classes/UTILISATEURS.php';
require_once '../../../Classes/FACTURES.php';
$FACTURES = new FACTURES();

if(isset($_SESSION['ECMU_USER_ID']) || !empty($_SESSION['ECMU_USER_ID'])) {
    $UTILISATEURS = new UTILISATEURS();
    $user = $UTILISATEURS->trouver($_SESSION['ECMU_USER_ID'],NULL,NULL);
    if(empty($user['ID_UTILISATEUR'])) {
        session_destroy();
        echo '<script>window.location.href="'.URL.'"</script>';
    }else {
        $modules = array_diff(explode(';',stream_get_contents($user['PROFIL'],-1)),array(""));
        $nb_modules = count($modules);
        $acces_fse = preg_split('/;/', $user['FSE'], null, PREG_SPLIT_NO_EMPTY);
        $user_ets = $UTILISATEURS->trouver_ets_utilisateur($user['ID_UTILISATEUR']);
        if($nb_modules == 0) {
            session_destroy();
            echo '<script>window.location.href="'.URL.'"</script>';
        }else{
            if(in_array('AGAC',$modules)) {

                $type_factures = $FACTURES->lister_types_factures();
                ?>
                <div class="col">
                    <p class="titres_p"><i class="fa fa-newspaper"></i> Historique des prestations</p>
                    <?php include "../Forms/form_recherche_factures_assurance.php"?>
                    <hr />


                    <div id="resultats_div"></div>

                    <script type="text/javascript" src="<?= JS.'page_assurance.js'?>"></script>
                </div>
            <?php }
        }
    }

}else{
    session_destroy();
    echo '<script>window.location.href="'.URL.'"</script>';
}

?>
