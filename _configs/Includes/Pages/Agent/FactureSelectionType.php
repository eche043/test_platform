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
            if(in_array('AGAC',$modules)) {
                $user_ets = $UTILISATEURS->trouver_ets_utilisateur($user['ID_UTILISATEUR']);
                if(!empty($user_ets['CODE_ETS'])) {
                    if(isset($_POST['num']) && !empty($_POST['num'])) {
                        require_once '../../../Classes/FACTURES.php';
                        $FACTURES = new FACTURES();
                        $facture = $FACTURES->trouver($_POST['num']);
                        if(!empty($facture['FEUILLE'])) {
                            if(empty($facture['TYPE_FEUILLE'])) {
                                $user_profil = explode(';',$user['FSE']);
                                $types = $FACTURES->lister_types_factures();
                                ?>
                                <div class="container">
                                    <div class="row justify-content-md-center" style="margin-top: 100px">
                                        <?php
                                        foreach ($types as $type) {
                                            if($type['CODE'] != 'MED' && in_array($type['CODE'],$user_profil)) {
                                                ?>
                                                <div class="col-sm-3">
                                                    <a href="<?php if($type['CODE'] == 'HOS'){echo '#';}else {echo URL.'agent/facture-edition.php?type='.$type['CODE'].'&num='.$facture['FEUILLE'];} ?>" <?php  if($type['CODE'] == 'HOS'){echo 'data-toggle="modal" data-target="#HOSModal"';} ?> style="height: 50px; line-height: 20px" class="btn btn-block btn-sm <?php if($type['CODE'] == 'HOS'){echo 'btn-danger';}else {echo 'btn-primary';} ?> box_profils"><?= $type['LIBELLE'];?></a>
                                                </div>
                                                <?php
                                                if($type['CODE'] == 'HOS') {
                                                    ?>
                                                    <div class="modal fade" id="HOSModal" tabindex="-1" role="dialog" aria-labelledby="HOSModalLabel" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="HOSModalLabel">ENTRER LE N° EP</h5>
                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <p class="align_center" id="p_resultats_ep"></p>
                                                                    <form id="form_ep">
                                                                        <div class="form-group">
                                                                            <label for="num_ep_input">N° entente préalable</label>
                                                                            <input type="text" maxlength="10" class="form-control form-control-sm" id="num_ep_cnam_input" aria-describedby="num EP" required />
                                                                        </div>
                                                                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal"><i class="fa fa-chevron-circle-left"></i> Retourner</button>
                                                                        <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-check"></i> Vérifier</button>
                                                                        <input type="hidden" value="<?= $facture['NUM_SECU'];?>" id="num_secu_input" />
                                                                        <input type="hidden" value="<?= $facture['FEUILLE'];?>" id="num_facture_input" />
                                                                        <input type="hidden" value="<?= date('d/m/Y',strtotime($facture['DATE_SOINS']));?>" id="date_soins_input" />
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <script type="application/javascript" src="<?= JS.'page_agent_facture.js';?>"></script>
                                                    <?php
                                                }
                                            }
                                        }
                                        ?>
                                    </div>
                                </div>
                                <?php
                            }else {
                                echo '<script>window.location.href="'.URL.'agent/facture-edition.php?type='.$facture['TYPE_FEUILLE'].'&num='.$facture['FEUILLE'].'"</script>';
                            }
                        }else{
                            echo '<script>window.location.href="'.URL.'agent/"</script>';
                        }
                    }else {
                        echo '<script>window.location.href="'.URL.'agent/"</script>';
                    }
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
        $('.dataTable').DataTable();
    })
</script>
