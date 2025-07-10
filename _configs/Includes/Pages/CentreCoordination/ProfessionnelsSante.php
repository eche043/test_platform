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
                if($centre){
                    if(isset($_POST['code_ets']) && !empty($_POST['code_ets']))
                    {
                        $ets = $COORDINATIONS->trouver_ets($centre['CODE_CENTRE'],$_POST['code_ets']);
                        if($ets)
                        {
                            $lister_ps = $COORDINATIONS->lister_ps_par_etablissement($_POST['code_ets']);
                            $nb_ps = count($lister_ps);
                            ?>
                            <div class="col">
                                <p class="titres_p"><i class="fa fa-newspaper"></i> Professionnels de Santé <?= $ets['RAISON_SOCIALE'] ?></p>
                                <p class="align_center"><br>
                                    <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#ajouterPsModal"><i class="fa fa-plus"></i>
                                        Ajouter un professionnel de santé
                                    </button>
                                </p>
                                <div class="modal fade" id="ajouterPsModal" tabindex="-1" role="dialog" aria-labelledby="ajouterPsModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="consultationDroitsModalLabel">Ajouter un professionnel de santé</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <p id="p_resultat_form_ajouter_ps_centre_coord" class="align_center"></p>
                                                <form id="form_ajouter_ps_centre_coord">
                                                    <div class="form-row align-items-center">
                                                        <div class="col-sm-2 my-1">
                                                            <label class="sr-only" for="population_num_secu_input">Code PS</label>
                                                            <div class="input-group input-group-sm">
                                                                <input type="text" value="<?= $_POST['code_ets'] ?>" id="code_ets_input" hidden>
                                                                <input type="text" class="form-control form-control-sm" id="code_ps_input" maxlength="9" placeholder="Code PS" autocomplete="off" />
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-8 my-1">
                                                            <label class="sr-only" for="prenom_input">Prénom</label>
                                                            <div class="input-group input-group-sm">
                                                                <input type="text" class="form-control form-control-sm" id="nom_prenom_input" placeholder="Nom et Prénom(s)" autocomplete="off" readonly />
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-2 my-1">
                                                            <button type="submit" class="btn btn-primary btn-block btn-sm" id="btn_ajouter_ps_centre_coord"><i class="fa fa-check"></i> Vérifier</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                                if($nb_ps)
                                {
                                    ?>
                                    <table class="table table-bordered table-hover table-sm table-responsive-sm dataTable mt-3">
                                        <thead class="bg-secondary text-white">
                                        <tr>
                                            <th width="5">N°</th>
                                            <th width="100">CODE ETS</th>
                                            <th width="100">CODE PS</th>
                                            <th>NOM ET PRENOM</th>
                                            <th width="120"></th>
                                            <th width="5"></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        $ligne = 1;
                                        foreach ($lister_ps as $ps) {
                                            ?>
                                            <tr>
                                                <td align="right"><b><?= $ligne;?></b></td>
                                                <td><b><?= $ps['CODE_ETS'] ?></b></td>
                                                <td><?= $ps['CODE_PS'] ?></td>
                                                <td><?= $ps['NOM'].' '.$ps['PRENOM'] ?></td>
                                                <td align="center"></td>
                                                <td>
                                                    <a href="historique.php?code-ets="><i class="fa fa-eye" aria-hidden="true"></i></a>
                                                </td>
                                            </tr>
                                            <?php
                                            $ligne++;
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                    <?php
                                }
                                else
                                {
                                    echo '<p class="align_center alert alert-info">AUCUN PROFESSIONNEL DE SANTE ENREGISTRE POUR CET ETABLISSEMENT DE SANTE</p>';
                                }
                                ?>
                                <script type="application/javascript" src="<?= JS.'page_centre_coordination_demandes.js';?>"></script>
                            </div>
                            <?php
                        }else{
                            echo '<script>window.location.href="'.URL.'centre-coordination/"</script>';
                        }
                    }
                }
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
