
<script type="application/javascript" src="<?= NODE_MODULES.'jquery/dist/jquery.js';?>"></script>
<script type="application/javascript" src="<?= NODE_MODULES.'bootstrap/dist/js/bootstrap.js';?>"></script>
<script type="application/javascript" src="<?= NODE_MODULES.'bootstrap/dist/js/bootstrap.bundle.js';?>"></script>
<script type="application/javascript" src="<?= NODE_MODULES.'@fortawesome/fontawesome-free/js/all.js';?>"></script>
<script type="application/javascript" src="<?= NODE_MODULES.'@fortawesome/fontawesome-free/js/fontawesome.min.js';?>"></script>
<script type="application/javascript" src="<?= JS.'functions.js';?>"></script>
<script>
    $("#deconnexion_link_n1").click(function () {
        $.ajax({
            url: '_configs/Includes/Submits/submit_logout.php',
            dataType: 'json',
            success: function (data) {
                if(data['status'] == 'success') {
                    window.location.href="";
                }
            }
        });
        return false;
    });
</script>
<?php
if(ACTIVE_URL == URL) {
    echo "<script>afficher_page_index();</script>";
}
if(ACTIVE_URL == URL.'profil.php') {
    echo "<script>afficher_page_profil();</script>";
}
if(ACTIVE_URL == URL.'infos-utiles.php') {
    echo "<script>afficher_page_infos_utiles();</script>";
}
if(ACTIVE_URL == URL.'panier-soins.php') {
    echo "<script>afficher_page_panier_soins();</script>";
}
if(ACTIVE_URL == URL.'panier-actes.php') {
    echo "<script>afficher_page_panier_actes();</script>";
}
if(ACTIVE_URL == URL.'panier-medicaments.php') {
    echo "<script>afficher_page_panier_medicaments();</script>";
}
if(ACTIVE_URL == URL.'panier-pathologies.php') {
    echo "<script>afficher_page_panier_pathologies();</script>";
}
if(ACTIVE_URL == URL.'reseau-soins.php') {
    echo "<script>afficher_page_reseau_soins();</script>";
}
if(ACTIVE_URL == URL.'connexion.php') {
    echo "<script>afficher_page_connexion();</script>";
}
if(ACTIVE_URL == URL.'centre-sante.php') {
    echo "<script>afficher_page_centre_sante_index();</script>";
}
if(ACTIVE_URL == URL.'duplicata.php') {
    echo "<script>afficher_page_demande_duplicata();</script>";
}

?>
<footer>&copy; <a href="https://www.ipscnam.ci" target="_blank">CNAM</a> - 2017 - <?= date('Y',time());?></footer>
</body>
</html>