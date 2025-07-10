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
            if(in_array('ASSU',$modules)) {
                require_once '../../../Classes/ATTESTATIONSDROITS.php';
                require_once '../../../Classes/ASSURES.php';
                $ATTESTATIONSDROITS = new ATTESTATIONSDROITS();
                $ASSURES = new ASSURES();
                $id_attestation = $_POST['id_attestation'];
                $attestation = $ATTESTATIONSDROITS->trouver($id_attestation,null);
                $nb_attestation = count($attestation);

                if($nb_attestation==1) {
                    $assure = $ASSURES->trouver($attestation[0]['NUM_SECU']);
                    ?>
                    <div class="col">
                        <p class="titres_p"> ATTESTATION DE DROITS N°<?= $id_attestation; ?></p>
                        <!-- <hr>-->
                        <div class="row">
                            <div class="col-sm-6">
                                <table class="table table-bordered table-hover table-sm">
                                    <tr>
                                        <td colspan="2" class="bg-info" align="center"><b>ASSURE</b></td>
                                    </tr>
                                    <tr>
                                        <td>N° SECU</td>
                                        <td><b><?= $attestation[0]['NUM_SECU']; ?></b></td>
                                    </tr>
                                    <tr>
                                        <td>NOM & PRENOM(S)</td>
                                        <td><b><?= $assure['NOM'] . ' ' . $assure['PRENOM']; ?></b></td>
                                    </tr>
                                    <tr>
                                        <td>DATE DE NAISSANCE</td>
                                        <td><b><?= date('d/m/Y', strtotime($assure['DATE_NAISSANCE'])); ?></b></td>
                                    </tr>
                                    <tr>
                                        <td>GENRE</td>
                                        <td><b><?= $assure['SEXE']; ?></b></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="bg-info" align="center"><b>MOTIF DE LA DEMANDE</b></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"><?= $attestation[0]['MOTIF_DEMANDE']; ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="bg-info" align="center"><b>ATTESTATION</b></td>
                                    </tr>
                                    <tr>
                                        <td>DATE DE DEMANDE</td>
                                        <td><b><?= date('d/m/Y', strtotime($attestation[0]['DATE_REG'])); ?></b></td>
                                    </tr>
                                    <tr>
                                        <td>STATUT</td>
                                        <td>
                                            <b>
                                                <?php
                                                if ($attestation[0]['STATUT_ATTESTATION'] == 0) {
                                                    $statut = 'EN ATTENTE DE VALIDATION';
                                                } elseif ($attestation[0]['STATUT_ATTESTATION'] == 1) {
                                                    $statut = 'VALIDEE';
                                                } elseif ($attestation[0]['STATUT_ATTESTATION'] == 2) {
                                                    $statut = 'REFUSEE';
                                                } elseif ($attestation[0]['STATUT_ATTESTATION'] == 3) {
                                                    $statut = 'SUSPENDUE';
                                                } elseif ($attestation[0]['STATUT_ATTESTATION'] == 4) {
                                                    $statut = 'PERIMEE';
                                                }
                                                echo $statut;
                                                ?>
                                            </b>
                                        </td>
                                    </tr>
                                    <?php
                                    if ($attestation[0]['STATUT_ATTESTATION'] != 2 && $attestation[0]['STATUT_ATTESTATION'] != 0) {
                                        ?>
                                        <tr>
                                            <td>VALIDITE</td>
                                            <td>DU
                                                <b><?= date('d/m-Y', strtotime($attestation[0]['DATE_DEBUT_VALIDITE'])); ?></b>
                                                AU
                                                <b><?= date('d/m/Y', strtotime($attestation[0]['DATE_FIN_VALIDITE'])); ?></b>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </table>
                            </div>
                            <div class="col-sm-6">
                                <p>
                                    <?php
                                    if ($attestation[0]['STATUT_ATTESTATION'] == 1) {
                                        ?>
                                        <button type="button" id="button_attestation_impression"
                                                class="btn btn-warning btn-sm">
                                            <i class="fa fa-print"></i> Imprimer l'attestation
                                        </button>
                                        <input type="hidden" value="<?= $assure['NUM_SECU']; ?>" id="num_secu_input"/>
                                        <input type="hidden" value="<?= $attestation[0]['ID']; ?>"
                                               id="attestation_id_input"/>
                                        <?php
                                    }
                                    ?>
                                </p>
                                <p align="center" id="p_resultats_attestation_consultation"></p>
                            </div>
                        </div>

                    </div>
                    <?php
                }else{
                    echo '<script>window.location.href="'.URL.'assure/demandes.php"</script>';
                }
            }
        }
    }
}else{
    session_destroy();
    echo '<script>window.location.href="'.URL.'"</script>';
}
?>
<script type="application/javascript">
        $("#button_attestation_impression").click(function() {
            var attestation_id = $("#attestation_id_input").val();
            mywindow = window.open('attestation-impression.php?id='+attestation_id,'Attestation de Droits','height=700,width=500');
            mywindow.focus();
            return true;
        });
</script>
