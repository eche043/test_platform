<?php
require_once '../../../Classes/UTILISATEURS.php';
require_once '../../../Classes/COLLECTIVITES.php';
$COLLECTIVITES = new COLLECTIVITES();

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
            if(in_array('ENT',$modules)) {
                $liste_collectivite_utilisateur = $UTILISATEURS->liste_collectivites_utilisateur($user['ID_UTILISATEUR']);
                if(count($liste_collectivite_utilisateur)!=0) {
                    //$user_collectivite = $COLLECTIVITES->trouver($user["CODE_COLLECTIVITE"]);
                    $chemin = '../_publics/images/logos_collectivites/';
                    $fichier = "FORMAT FICHIER.xlsx";
                    ?>
                    <div class="col">
                        <p class="titres_p">ESPACE COLLECTIVITE</p>
                        <p class="align_center alert alert-secondary">
                            <b class="h6"><i>Pour la déclaration des populations par chargement de fichier, cliquez ci-dessous pour télécharger le format attendu.</i></b>
                            <br><a class="h4" href="<?=URL.$fichier;?>"><i class="fa fa-download"></i> Télécharger le fichier ici </a>
                        </p>
                    </div>
                    <div class="col">
                        <?php
                        if(count($liste_collectivite_utilisateur)==1){
                            $lsc = $liste_collectivite_utilisateur[0];
                            $user_collectivite = $COLLECTIVITES->trouver($lsc["CODE_COLLECTIVITE"]);
                            /*$populations = $COLLECTIVITES->trouver_populations_collectivite_par_statut($lsc["CODE_COLLECTIVITE"],1);
                            $nb_populations = count($populations);*/
                            $total_populations = $COLLECTIVITES->total_populations_collectivite_par_statut($lsc["CODE_COLLECTIVITE"],1);
                            $nb_populations = $total_populations['TOTAL'];
                            ?>
                            <div class="row justify-content-md-center">
                                <h4>
                                    <?=$user_collectivite['CODE'] .' - '.$user_collectivite['RAISON_SOCIALE'];?>
                                </h4>
                            </div>
                            <div class="row justify-content-md-center">
                                <p class="align_center display-4 bg-dark text-white"><b><?= number_format($nb_populations,'0','',' ');?> <?php if($nb_populations > 1){echo '<b class="fa fa-users"></b>';}else {echo '<b class="fa fa-user"></b>';} ?></b></p>
                            </div>
                            <div class="row justify-content-md-center row-cols-2 nav-pills nav-fill">
                                <div class="col col-lg-2 nav-item"">
                                    <a class="btn btn-block btn-primary btn-sm box_profils" style="height: 50px; line-height: 20px" aria-current="page" href="<?=URL.'collectivite/populations.php?code-collectivite='.$user_collectivite['CODE'];?>">
                                        GESTION DES POPULATIONS
                                    </a>
                                </div>
                                <div class="col col-lg-2 nav-item"">
                                    <a class="btn btn-block btn-primary btn-sm box_profils" style="height: 50px; line-height: 20px" aria-current="page" href="<?=URL.'collectivite/cotisations.php?code-collectivite='.$user_collectivite['CODE'];?>">
                                        GESTION DES COTISATIONS
                                    </a>
                                </div>
                            </div>
                            <?php
                        }else{
                        ?>
                            <table class="table table-secondary table-striped table-bordered table-hover table-sm table-responsive-sm">
                                <thead>
                                <?php
                                foreach($liste_collectivite_utilisateur as $lsc){
                                    $user_collectivite = $COLLECTIVITES->trouver($lsc["CODE_COLLECTIVITE"]);
                                    /*$populations = $COLLECTIVITES->trouver_populations_collectivite_par_statut($lsc["CODE_COLLECTIVITE"],1);
                                    $nb_populations = count($populations);*/
                                    $total_populations = $COLLECTIVITES->total_populations_collectivite_par_statut($lsc["CODE_COLLECTIVITE"],1);
                                    $nb_populations = $total_populations['TOTAL'];
                                    ?>
                                    <tr>
                                        <th width="100" class="align-middle"><b><?=$user_collectivite['CODE'];?></b></th>
                                        <th class="align-middle"><?=$user_collectivite['RAISON_SOCIALE'];?></th>
                                        <th class="align-middle align_center"><?=$nb_populations; if ($nb_populations > 1) {
                                                echo ' <b class="fa fa-users"></b>';
                                            } else {
                                                echo ' <b class="fa fa-user"></b>';
                                            }?></th>
                                        <th width="5">
                                            <a class="btn btn-info btn-block" aria-current="page" href="<?=URL.'collectivite/populations.php?code-collectivite='.$user_collectivite['CODE'];?>">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                        </th>
                                    </tr>
                                    <?php
                                }
                                ?>
                                </thead>
                            </table>
                        <?php
                        }
                        ?>
                    </div>
                    <script type="text/javascript" src="<?= JS.'page_collectivite.js'?>"></script>

                    <?php
                }else{
                    echo '<p align="center" class="alert alert-danger">AUCUNE COLLECTIVITE DEFINIE POUR CET UTILISATEUR.</p>';
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
        $('#dataTable').DataTable();
    });

</script>