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
            if(in_array('DCS',$modules)) {
                $user_ets = $UTILISATEURS->trouver_ets_utilisateur($user['ID_UTILISATEUR']);
                //if(!empty($user_ets['CODE_ETS'])) {
                    require_once '../../../Classes/ETABLISSEMENTSSANTE.php';
                    $ETABLISSEMENTSSANTE = new ETABLISSEMENTSSANTE();
                    $directeur_ets = $UTILISATEURS->trouver_directeur_ets($user['CODE_DCS'],1);
                    ?>
                    <div class="col">
                        <p class="titres_p"><i class="fa fa-newspaper"></i> Centre de santé</p>
                        <form id="recherche_facture_form_dcs">
                            <div class="form-group row">
                                <div class="col-sm-12">
                                    <select class="form-control form-control-sm custom-select" id="ets_input" required>
                                        <?php
                                        foreach ($directeur_ets as $user_et) {
                                            $ets = $ETABLISSEMENTSSANTE->trouver_etablissement_sante($user_et['CODE_ETS']);
                                            echo '<option value="'.$user_et['CODE_ETS'].'">'.$ets['RAISON_SOCIALE'].'</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-2">
                                    <input type="text" id="date_debut_input" name="date_debut" class="form-control form-control-sm datepicker" placeholder="Date début" autocomplete="off" value="<?= date('d/m/Y',strtotime('-1 week',time())); ?>" readonly />
                                </div>
                                <div class="col-sm-2">
                                    <input type="text" id="date_fin_input" name="date_fin_input" class="form-control form-control-sm datepicker" placeholder="Date fin" autocomplete="off" value="<?= date('d/m/Y',time()); ?>" readonly />
                                </div>
                                <div class="col-sm-7">
                                    <select id="ps_input" class="form-control form-control-sm custom-select" placeholder="PS" autocomplete="off">
                                        <?php ?>
                                        <option value="">Sélectionner un PS</option>
                                        <?php ?>
                                    </select>
                                </div>

                                <div class="col-sm-1">
                                    <button type="submit" class="btn btn-primary btn-sm btn-block" id="btn_recherche_facture_dcs"><i class="fa fa-search"></i></button>
                                </div>
                            </div>
                        </form><hr />


                        <div id="resultats_div"></div>
                    </div>

                    <script type="text/javascript" src="<?= JS.'page_centre_sante.js'?>"></script>
                    <?php
                /*}else {
                    echo '<script>window.location.href="'.URL.'"</script>';
                }*/
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