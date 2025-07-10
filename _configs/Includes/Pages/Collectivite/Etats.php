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

                    ?>

                <div class="col">
                    <div class="row">
                        <div class="col-sm-12">
                            <p class="titres_p"><i class="fa fa-user"></i> Etats</p>
                            <form id="form_etat">
                                <div class="form-group row">
                                    <div class="col-sm-2">
                                        <input type="text" class="form-control form-control-sm datepicker" id="date_debut_input" value="01/01/2019" autocomplete="off" placeholder="Date début" required />
                                    </div>
                                    <div class="col-sm-2">
                                        <input type="text" class="form-control form-control-sm datepicker" id="date_fin_input" autocomplete="off" placeholder="Date fin" required />
                                    </div>
                                    <div class="col-sm-1">
                                        <input type="hidden" id="code_collectivite_input" value="<?= $user['CODE_COLLECTIVITE'];?>" />
                                        <button type="submit" class="btn btn-success btn-block btn-sm"><i class="fa fa-search"></i></button>
                                    </div>
                                </div>
                            </form>
                            <div id="div_resultats_recherche"></div>
                        </div>
                    </div>
                </div>


                    <script type="text/javascript" src="<?= JS.'page_collectivite.js'?>"></script>
                    <?php

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

    $(".datepicker").datepicker({
        maxDate: 0
    }).attr('readonly', 'readonly');

</script>

