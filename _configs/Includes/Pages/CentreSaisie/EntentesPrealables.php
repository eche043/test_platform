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
            if(in_array('CSAI',$modules)) {
                $user_ets = $UTILISATEURS->trouver_ets_utilisateur($user['ID_UTILISATEUR']);
                ?>
                <div class="col">
                    <p class="titres_p">ENTENTES PREALABLES</p>
                    <?php include "../Forms/form_entente_prealable.php"; ?>
                    <p id="p_resultats_entente_prealable" class="align_center"></p>
                    <script type="application/javascript" src="<?= JS.'page_centre_saisie_demandes.js';?>"></script>
                </div>
                <?php
            }
        }
    }

}else{
    session_destroy();
    echo '<script>window.location.href="'.URL.'"</script>';
}

?>
