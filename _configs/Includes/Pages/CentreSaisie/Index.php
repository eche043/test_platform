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
                require_once '../../../Classes/FACTURES.php';
                require_once '../../../Classes/ETABLISSEMENTSSANTE.php';
                $FACTURES = new FACTURES();
                $ETABLISSEMENTSSANTE = new ETABLISSEMENTSSANTE();
                ?>
                <div class="col">
                    <p class="titres_p"><b class="fa fa-file"></b> Factures</p>
                    <div class="row justify-content-md-center">
                        <div class="col col-sm-4">
                            <form id="form_recherche_facture">
                                <div class="form-group row">
                                    <label for="num_facture_input" class="col-sm-3 col-form-label-sm">N° facture</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control form-control-sm" id="num_facture_input" autocomplete="off" style="text-align: right" placeholder="Exemple: 12345" required />
                                    </div>
                                    <div class="col-sm-3">
                                        <button type="submit" id="btn_recherche_facture" class="btn btn-success btn-block btn-sm"><i class="fa fa-search"></i></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="row">
                        <div class="col-sm-12">
                            <div id="resultats_recherche_div_factures"></div><br /><br />
                            <p class="titres_p"><b class="fa fa-pills"></b> Nouveaux médicaments</p>
                            <div class="row justify-content-md-center">
                                <div class="col col-sm-4">
                                    <form id="form_recherche_medicaments_fs_initiale">
                                        <div class="form-group row">
                                            <label for="num_facture_initiale_input" class="col-sm-3 col-form-label-sm">N° facture</label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control form-control-sm" id="num_facture_initiale_input" autocomplete="off" style="text-align: right" placeholder="Exemple: 12345" required />
                                            </div>
                                            <div class="col-sm-3">
                                                <input type="hidden" id="user_input" value="<?=$user['ID_UTILISATEUR'];?>">
                                                <button type="submit" id="btn_recherche_facture_initiale" class="btn btn-success btn-block btn-sm"><i class="fa fa-search"></i></button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div id="resultats_recherche_div_medicaments"></div><hr /><br /><br />
                        </div>
                    </div>
                </div>
                <script type="text/javascript" src="<?= JS.'page_centre_saisie.js'?>"></script>
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

</script>