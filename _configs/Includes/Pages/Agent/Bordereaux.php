<?php
require_once '../../../Classes/UTILISATEURS.php';

if(isset($_SESSION['ECMU_USER_ID']) || !empty($_SESSION['ECMU_USER_ID'])) {
    $UTILISATEURS = new UTILISATEURS();
    $user = $UTILISATEURS->trouver($_SESSION['ECMU_USER_ID'],NULL,NULL);
//    var_dump($user_ETS);
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
            ?>
            <div class="col">
                <?php
                $user_ETS = $UTILISATEURS->trouver_ets_utilisateur($user['ID_UTILISATEUR']);
                if(in_array('AGAC',$modules)) {
                    require_once '../../../Classes/BORDEREAUX.php';
                    require_once '../../../Classes/FACTURES.php';
                    require_once '../../../Classes/OGD.php';
                    $BORDEREAUX = new BORDEREAUX();
                    $FACTURES = new FACTURES();
                    $OGD = new OGD();
                    $bordereaux = $BORDEREAUX->lister($user_ETS['CODE_ETS']);
                    ?>
                    <p class="titres_p"><i class="fa fa-newspaper"></i> Bordereaux de transmission</p>
                    <p class="align_right"><button class="btn btn-primary btn-sm" data-toggle="modal" data-target=".bd-example-modal-xl" type="button">Générer un bordereau</button></p><br />
<!--                    Modal généré bordereau-->
                    <div class="modal fade bd-example-modal-xl" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-xl" role="document">
                            <div class="modal-content">

                                <p id="resultat_bordereau" align="center"></p>
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalScrollableTitle"><i class="fa fa-file"></i> Générer un nouveau bordereau de soins</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <?php
                                    $type_factures = $BORDEREAUX->lister_types_factures($user_ETS['CODE_ETS'],"F","T");
                                    include '../Forms/form_bordereau.php'; ?>
                                </div>

                            </div>

                        </div>
                    </div>
                    <?php
                    $nb_bordereaux = count($bordereaux);
                    if($nb_bordereaux ==  0 ){
                        echo '<p align="center" class="alert alert-danger">Aucun bordereau encore généré</p>';
                    }else{ ?>
                        <table class="table table-bordered table-sm table-hover" id="dataTable">
                            <thead class="bg-info">
                            <tr>
                                <th width="5">N°</th>
                                <th width="100">DATE DEMANDE</th>
                                <th width="100">N° BORDEREAU</th>
                                <th width="100">DATE DÉBUT</th>
                                <th width="100">DATE FIN</th>
                                <th>OGD</th>
                                <th>TYPE FACTURE</th>
                                <th width="10">FACTURES</th>
                                <th width="5"><i class="fa fa-eye"></i></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $ligne = 1;
                            foreach ($bordereaux as $bordereau) {
                                $type_facture = $FACTURES->trouver_type_facture($bordereau['TYPE_FACTURE']);
                                $ogd = $OGD->trouver('PRST',$bordereau['CODE_OGD']);
                                ?>
                                <tr>
                                    <td align="right"><?= $ligne;?></td>
                                    <td><?= date('d/m/Y',strtotime($bordereau['DATE_DEMANDE']));?></td>
                                    <td align="right"><b><?= $bordereau['NUM_BORDEREAU'];?></b></td>
                                    <td><?= date('d/m/Y',strtotime($bordereau['DATE_DEBUT']));?></td>
                                    <td><?= date('d/m/Y',strtotime($bordereau['DATE_FIN']));?></td>
                                    <td><?= $ogd['LIBELLE'];?></td>
                                    <td><?= $type_facture['LIBELLE'];?></td>
                                    <td align="right"><b><?= $bordereau['NOMBRE_FACTURES'];?></b></td>
                                    <td><a href="<?= URL.'agent/bordereau.php?num='.$bordereau['NUM_BORDEREAU'];?>" class="badge badge-info"><i class="fa fa-eye"></i></a></td>
                                </tr>
                                <?php
                                $ligne++;
                            }
                            ?>
                            </tbody>
                        </table>
                        <?php
                    }
                }
                ?>
            </div>
            <?php
        }
    }

}else{
    session_destroy();
    echo '<script>window.location.href="'.URL.'"</script>';
}

?>
<script type="text/javascript" src="<?= JS.'page_agent_bordereaux.js'?>"></script>
<script>
    $(function () {
        $('#dataTable').DataTable();
    });

    $(".datepicker").datepicker({
        maxDate: 0
    }).attr('readonly', 'readonly');
</script>