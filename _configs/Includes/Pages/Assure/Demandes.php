<?php
require_once '../../../Classes/UTILISATEURS.php';
require_once '../../../Classes/ASSURES.php';
require_once '../../../Classes/ATTESTATIONSDROITS.php';

if(isset($_SESSION['ECMU_USER_ID']) || !empty($_SESSION['ECMU_USER_ID'])) {
    $UTILISATEURS = new UTILISATEURS();
    $ASSURES = new ASSURES();
    $ATTESTATIONSDROITS = new ATTESTATIONSDROITS();

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
                $attestations = $ATTESTATIONSDROITS->trouver(null,$user['NUM_SECU']);
                if(count($attestations)==0){
                    echo '<p align="center" class="alert alert-success">AUCUNE DEMANDE D\'ATTESTATION DE DROITS ENREGISTREE.</p>';
                }else {
                    ?>
                    <div class="col">
                        <p class="titres_p">Attestations de Droits</p>
                        <div class="row">
                            <div class="col-sm-12">
                                <table class="table table-sm table-bordered table-hover dataTable" id="dataTable">
                                    <thead class="bg-info">
                                    <tr>
                                        <th width="5">N°</th>
                                        <th width="120">DATE DEMANDE</th>
                                        <th>MOTIF DEMANDE</th>
                                        <th width="150">STATUT</th>
                                        <th width="5"></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $ligne = 1;
                                    foreach ($attestations as $attestation){
                                        ?>
                                        <tr>
                                            <td align="right"><b><?=$ligne;?></b></td>
                                            <td><?=date('d/m/Y',strtotime($attestation['DATE_REG']));?></td>
                                            <td><?=$attestation['MOTIF_DEMANDE'];?></td>
                                            <td>
                                                <?php
                                                    if ($attestation['STATUT_ATTESTATION'] == 0) {
                                                        $statut = 'EN ATTENTE DE VALIDATION';
                                                    } elseif ($attestation['STATUT_ATTESTATION'] == 1) {
                                                        $statut = 'VALIDEE';
                                                    } elseif ($attestation['STATUT_ATTESTATION'] == 2) {
                                                        $statut = 'REFUSEE';
                                                    } elseif ($attestation['STATUT_ATTESTATION'] == 3) {
                                                        $statut = 'SUSPENDUE';
                                                    } elseif ($attestation['STATUT_ATTESTATION'] == 4) {
                                                        $statut = 'PERIMEE';
                                                    }
                                                    echo $statut;
                                                ?>
                                            </td>
                                            <td><a href="<?= URL.'assure/attestation.php?id='.$attestation['ID'];?>" class="badge badge-info"><b class="fa fa-eye"></b></a></td>
                                        </tr>
                                        <?php
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
        $('.dataTable').DataTable();
    })
</script>
