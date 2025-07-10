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
                if(in_array('COORD',$modules)) {
                    require_once '../../../Classes/FACTURES.php';
                    require_once '../../../Classes/ETABLISSEMENTSSANTE.php';
                    require_once '../../../Classes/COORDINATIONS.php';
                    $FACTURES = new FACTURES();
                    $ETABLISSEMENTSSANTE = new ETABLISSEMENTSSANTE();
                    $COORDINATIONS = new COORDINATIONS();
                    $centre = $UTILISATEURS->trouver_centre_coordination($user['ID_UTILISATEUR']);
                    if($centre){ ?>
                        <div class="col">
                            <p class="titres_p"><i class="fa fa-newspaper"></i> Assurés </p>
                                <form id="form_recherche_assu">
                                    <div class="form-row align-items-center">
                                        <div class="col-sm-2 my-1">
                                            <label class="sr-only" for="num_secu_input">N° Sécu</label>
                                            <div class="input-group input-group-sm">
                                                <input type="text" class="form-control form-control-sm" maxlength="13" id="num_secu_input_assu" placeholder="N° Sécu" autocomplete="off" />
                                            </div>
                                        </div>
                                        <div class="col my-1">
                                            <label class="sr-only" for="nom_prenom_input">Nom & Prénom(s)</label>
                                            <div class="input-group input-group-sm">
                                                <input type="text" class="form-control form-control-sm" maxlength="100" id="nom_prenom_input" placeholder="Nom & Prénom(s)" autocomplete="off" />
                                            </div>
                                        </div>
                                        <div class="col-sm-1 my-1">
                                            <button type="submit" id="button_recherche_assu" class="btn btn-success btn-sm btn-block"><i class="fa fa-search"></i></button>
                                        </div>
                                    </div>
                                </form>
                                <hr />
                                <div id="div_resultats_assu"></div>
                        </div>
                        <script>
                            $(function () {
                                $('#dataTable').DataTable();
                            });

                            $(".datepicker").datepicker({
                                maxDate: 0
                            }).attr('readonly', 'readonly');
                        </script>
                        <script type="text/javascript" src="<?= JS.'page_centre_coordination.js'?>"></script>
                    <?php }
                    else{
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
        $('.dataTable').DataTable();
    });

</script>