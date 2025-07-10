<?php
require_once '../../Classes/UTILISATEURS.php';
if(isset($_SESSION['ECMU_USER_ID'])) {
    echo '<script>window.location.href="'.URL.'"</script>';
}else {
    ?>
    <div class="col">
        <div class="row justify-content-md-center">

            <div class="col col-sm-3" id="div_login">
                <p class="align_center"><img src="<?= IMAGES.'logo_cnam.png';?>" width="100" alt="LOGO CNAM" /><br /><b class="display-4">ecmu</b><hr /></p>
                <p id="p_resultats" class="align_center"></p>
                <?php include "Forms/form_connexion.php";?>
                <?php include "Forms/form_mot_de_passe_oublie.php";?>
                <?php include "Forms/form_compte.php";?>
            </div>
        </div>
    </div>
    <?php
    echo '<script type="application/javascript" src="'.JS.'page_connexion.js"></script>';
}
?>