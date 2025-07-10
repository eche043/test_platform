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
            if(in_array('PS',$modules)) {
                $user_ets = $UTILISATEURS->trouver_ets_utilisateur($user['ID_UTILISATEUR']);
                if(!empty($user_ets['CODE_ETS'])) {
                    require_once '../../../Classes/ASSURANCE.php';
                    $ASSURANCE = new ASSURANCE();
                    $libelle_mutuelle = $ASSURANCE->trouver($user['CODE_MUTUELLE']);
                    ?>
                    <div class="col">
                        <p class="titres_p">
                            <b class="fa fa-building"></b>
                            <?= $user['CODE_MUTUELLE'].' - '.$libelle_mutuelle['LIBELLE'];?>
                        </p><hr><br />
                        <p class="h3" align="center">Rechercher un souscripteur</p><br /><br />
                        <div class="col-sm-12">
                            <form id="form_etat">
                                <div class="form-group row">
                                    <div class="col-sm-2">
                                        <input type="text" class="form-control form-control-sm datepicker" id="date_debut_input" autocomplete="off" placeholder="Date début" required />
                                    </div>
                                    <div class="col-sm-2">
                                        <input type="text" class="form-control form-control-sm datepicker" id="date_fin_input" autocomplete="off" placeholder="Date fin" required />
                                    </div>
                                    <div class="col-sm-1">
                                        <input type="hidden" id="code_mutuelle_input" value="<?= $user['CODE_MUTUELLE'];?>" />
                                        <button type="submit" class="btn btn-success btn-block btn-sm"><i class="fa fa-search"></i></button>
                                    </div>
                                </div>
                            </form>
                            <div id="div_resultats_recherche"></div>
                        </div>
                    </div>

                    <script type="text/javascript" src="<?= JS.'assurance.js'?>"></script>
                    <?php
                }else {
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
        $('#dataTable').DataTable();
    });

</script>
