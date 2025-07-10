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
            if($nb_modules == 1){

                $u_profil = str_replace('AGAC','agent',str_replace('MUT','assurance',str_replace('DCS','centre-sante',str_replace('ENT','collectivite',str_replace('OGDP','ogd-prestations',str_replace('PS','professionnel-sante',str_replace('ASSU','assure',str_replace('CSAI','centre-saisie',str_replace('APA','partenaire',str_replace('COORD','centre-coordination',$modules[0]))))))))));
                echo '<script>window.location.href="'.URL.$u_profil.'/"</script>';
            }else{
                if(date('A',time()) == 'AM') {
                    $salutations = 'Bonjour';
                }else {
                    $salutations = 'Bonsoir';
                }
                ?>
                <div class="container">
                    <div class="row">
                        <div class="col-sm-2">
                            <p class="align_center"><img width="100" src="<?= IMAGES.'logo_cnam.png';?>" alt="Logo CNAM" /></p>
                        </div>
                        <div class="col">
                            <p class="h4 align_center" style="margin-top: 20px"><?= $salutations.' '.ucfirst(strtolower($user['PRENOM']));?><br /> Bienvenue sur ECMU</p>
                        </div>
                    </div>
                    <hr />

                    <div class="row" style="margin-top: 100px">
                        <?php
                        for ($i = 0; $i < $nb_modules; $i++) {
                            $profil = $UTILISATEURS->trouver_profil($modules[$i]);
                            $user_profil = str_replace('AGAC','agent',
                                str_replace('MUT','assurance',
                                    str_replace('DCS','centre-sante',
                                    str_replace('COORD','centre-coordination',
                                        str_replace('ENT','collectivite',
                                            str_replace('OGDP','ogd-prestations',
                                                str_replace('PS','professionnel-sante',
                                                    str_replace('ASSU','assure',
                                                        str_replace('CSAI','centre-saisie',
                                                            str_replace('APA','partenaire',$profil['CODE']))))))))));
                            ?>

                            <div class="col-sm-4">
                                <a href="<?= URL.$user_profil.'/';?>" class="btn btn-block btn-sm btn-outline-primary box_profils"><?= $profil['LIBELLE'];?></a>
                            </div>
                            <?php
                        }
                        ?>
                        <div class="col-sm-4">
                            <a href="<?= URL.'infos-utiles.php';?>" target="_blank" class="btn btn-block btn-sm btn-info box_profils"><i class="fa fa-info-circle"></i> Info utiles</a>
                        </div>

                        <div class="col-sm-4" hidden>
                            <button type="button" id="deconnexion_link" class="bg-danger btn btn-block btn-sm btn-info box_profils">
                                <i class="fa fa-power-off"></i> Déconnexion
                            </button>
                        </div>
                    </div>
                </div>
                <script type="application/javascript" src="<?= JS.'ecmu.js';?>"></script>
                <?php
            }
        }
    }

}else{
    session_destroy();
    echo '<script>window.location.href="'.URL.'connexion.php"</script>';
}
?>
