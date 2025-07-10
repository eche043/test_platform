<?php
require_once '../../../Classes/UTILISATEURS.php';
require_once '../../../Classes/ASSURES.php';
require_once '../../../Classes/FACTURES.php';

if(URL) {
    echo '<script>window.location.href="'.URL.'"</script>';
}else {
    $statuts_facture = array('','C','N','A','R');

    if(isset($_SESSION['ECMU_USER_ID']) || !empty($_SESSION['ECMU_USER_ID'])) {
        $UTILISATEURS = new UTILISATEURS();
        $ASSURES = new ASSURES();
        $FACTURES = new FACTURES();

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
                if(in_array('ASSU',$modules)) {
                    $num_secu = $user['NUM_SECU'];
                    $factures = $FACTURES->consommations_assure($num_secu);
                    if(count($factures)==0){
                        echo '<p align="center" class="alert alert-success">AUCUNE CONSOMMATION ENREGISTREE.</p>';
                    }else{
                        ?>
                        <div class="col">
                            <p class="titres_p">Consommations</p>
                            <div class="row">
                                <div class="col-sm-12">
                                    <table class="table table-sm table-bordered table-hover dataTable" id="dataTable">
                                        <thead class="bg-info">
                                        <tr>
                                            <th width="5">N°</th>
                                            <th>ETABLISSEMENT</th>
                                            <th width="120">DATE SOINS</th>
                                            <th>TYPE PRESTATIONS</th>
                                            <th width="150">NUMERO FACTURE</th>
                                            <th width="150">MONTANT</th>
                                            <th width="150">PART CMU</th>
                                            <th width="150">PART ASSURE</th>
                                            <th width="5"></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        $ligne = 1;
                                        foreach ($factures as $facture){
                                            if(!in_array($facture['STATUT'],$statuts_facture)){
                                                $type_feuille = $FACTURES->trouver_type_facture($facture['TYPE_FEUILLE']);
                                                $part_cmu = round($facture['MONTANT'] * 0.7);
                                                if($facture['CODE_CSP'] == 'IND') {
                                                    $part_ac = $facture['MONTANT'] - $part_cmu;
                                                }else {
                                                    $part_ac = 0;
                                                }
                                                $part_assure = ($facture['MONTANT'] - ($part_cmu + $part_ac));
                                                ?>
                                                <tr>
                                                    <td align="right"><b><?=$ligne;?></b></td>
                                                    <td><b><?=$facture['NOM_ETS'];?></b></td>
                                                    <td><?=date('d/m/Y',strtotime($facture['DATE_SOINS']));?></td>
                                                    <td><?=$type_feuille['LIBELLE'];?></td>
                                                    <td class="align_right"><?=$facture['FEUILLE'];?></td>
                                                    <td class="align_right"><b><?=$facture['MONTANT'].' FCFA';?></b></td>
                                                    <td class="align_right"><b><?=$part_cmu.' FCFA';?></b></td>
                                                    <td class="align_right"><b><?=$part_assure.' FCFA';?></b></td>
                                                    <td><a href="<?= URL.'assure/facture.php?numero='.$facture['FEUILLE'].'&type='.$facture['TYPE_FEUILLE'];?>" class="badge badge-info"><b class="fa fa-eye"></b></a></td>
                                                </tr>
                                                <?php
                                            }
                                            $ligne++;
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <?php
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
            var groupColumn = 1;
            var table = $('#dataTable').DataTable({
                "columnDefs": [
                    { "visible": false, "targets": groupColumn }
                ],
                "order": [[ groupColumn, 'asc' ]],
                "displayLength": 25,
                "drawCallback": function ( settings ) {
                    var api = this.api();
                    var rows = api.rows( {page:'current'} ).nodes();
                    var last=null;

                    api.column(groupColumn, {page:'current'} ).data().each( function ( group, i ) {
                        if ( last !== group ) {
                            $(rows).eq( i ).before(
                                '<tr class="group"><td colspan="8" class="alert-dark">'+group+'</td></tr>'
                            );

                            last = group;
                        }
                    } );
                }
            } );

            // Order by the grouping
            $('#dataTable tbody').on( 'click', 'tr.group', function () {
                var currentOrder = table.order()[0];
                if ( currentOrder[0] === groupColumn && currentOrder[1] === 'asc' ) {
                    table.order( [ groupColumn, 'desc' ] ).draw();
                }
                else {
                    table.order( [ groupColumn, 'asc' ] ).draw();
                }
            } );
        });
    </script>
    <?php
}

?>