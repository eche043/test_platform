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
                            $terminaux = $COORDINATIONS->lister_terminaux($ets['CODE_ETS']);
                            $nb_terminaux = count($terminaux);
                                ?>
                                    <div class="col">
                                    <p class="titres_p">
                                        <i class="fa fa-newspaper"></i> Terminaux Biométriques <?= $ets['RAISON_SOCIALE'] ?></p>
                                    <p>
                                        <a href="<?= URL.'centre-coordination/terminaux-biometriques-edition.php?code-ets='. $ets['CODE_ETS'] ;?>" class="btn btn-sm btn-success"><i class="fa fa-plus"></i> Ajouter un Nouveau terminal</a>

                                        <a href="<?= URL.'centre-coordination/terminaux-biometriques-historique.php?code-ets='. $ets['CODE_ETS'] ;?>" class="btn btn-sm btn-danger"><i class="fa fa-eye"></i> Historique des terminaux</a>
                                    </p>
                                    <hr />
                                    <?php
                                        if($nb_terminaux != '0')
                                        {
                                            ?>
                                            <table class="table table-bordered table-hover table-sm table-responsive-sm dataTable mt-3">
                                                <thead class="bg-secondary text-white">
                                                <tr>
                                                    <th width="5">N°</th>
                                                    <th width="100">N° IMEI</th>
                                                    <th width="100">N° TELEPHONE</th>
                                                    <th width="120">CODE ETS</th>
                                                    <th width="10">TYPE TERMINAL</th>
                                                    <th width="10"></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php
                                                $ligne = 1;
                                                foreach ($terminaux as $terminal) {

                                                    $infos_terminal = $COORDINATIONS->detail_terminaux($terminal['TERMINAL'] ,$terminal['CODE_ETS']);
                                                    ?>
                                                    <tr>
                                                        <td align="right"><b><?= $ligne;?></b></td>
                                                        <td><b><?= $terminal['TERMINAL'] ?></b></td>
                                                        <td><?= $infos_terminal['NUMERO_TELEPHONE'] ?></td>
                                                        <td><?= $infos_terminal['ETABLISSEMENT'] ?></td>
                                                        <td><?= $infos_terminal['TERMINAL_TYPE'] ?></td>
                                                        <td>
                                                            <a type="button" class="text-danger" data-toggle="modal" data-target="#modalRetraitCentre_<?= $terminal['TERMINAL'] ?>">
                                                                <i class="fa fa-trash" aria-hidden="true"></i>
                                                            </a>
                                                        </td>
                                                        <div class="modal fade" id="modalRetraitCentre_<?= $terminal['TERMINAL'] ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                            <div class="modal-dialog modal-sm" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h6 class="modal-title" id="exampleModalLabel">Retirer le Terminal du centre</h6>
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <p id="resultat_form_retrait_terminal_centre"></p>
                                                                        <form id="form_retrait_terminal_centre">
                                                                            <input type="text" value="<?= $terminal['CODE_ETS'] ?>" id="code_centre_input" hidden>
                                                                            <input type="text" value="<?= $infos_terminal['NUMERO_TELEPHONE'] ?>" id="telephone_terminal_input" hidden>
                                                                            <button type="submit" class="btn btn-primary btn-sm col-sm-12 btn_retirer_terminal" id="btn_retirer_terminal_<?= $terminal['TERMINAL'] ?>">Rétirer le Terminal</button>
                                                                        </form>

                                                                    </div>

                                                                </div>
                                                            </div>
                                                        </div>
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
                                                ?>
                                                <p class="align_center alert alert-info">AUCUN TERMINAL BIOMETRIQUE ENREGISTRE POUR CET ETABLISSEMENT DE SANTE</p>
                                                <?php
                                            }
                                    ?>
                                   <script type="application/javascript" src="<?= JS.'page_centre_coordination_terminaux.js';?>"></script>
                                </div>
                                <?php
                        }else{
                            echo '<script>window.location.href="'.URL.'centre-coordination/"</script>';
                        }
                    }
                    else
                    {
                       ?>



                        <?php
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
