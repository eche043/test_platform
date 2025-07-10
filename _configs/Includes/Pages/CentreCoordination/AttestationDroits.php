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
                            require_once '../../../Classes/ATTESTATIONSDROITS.php';
                            $ATTESTATIONSDROITS = new ATTESTATIONSDROITS();
                            $id_attestation = trim($_POST['id']);
                            $attestation = $ATTESTATIONSDROITS->trouver($id_attestation,null);
                            $nb_attestation = count($attestation);

                            if($nb_attestation==1) {
                                require_once '../../../Classes/ASSURES.php';
                                $ASSURES = new ASSURES();
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
                                                <tr class="table-light">
                                                    <td>N° SECU</td>
                                                    <td><b><?= $attestation[0]['NUM_SECU']; ?></b></td>
                                                </tr>
                                                <tr class="table-light">
                                                    <td>NOM & PRENOM(S)</td>
                                                    <td><b><?= $assure['NOM'] . ' ' . $assure['PRENOM']; ?></b></td>
                                                </tr>
                                                <tr class="table-light">
                                                    <td>DATE DE NAISSANCE</td>
                                                    <td><b><?= date('d/m/Y', strtotime($assure['DATE_NAISSANCE'])); ?></b></td>
                                                </tr>
                                                <tr class="table-light">
                                                    <td>GENRE</td>
                                                    <td><b><?= $assure['SEXE']; ?></b></td>
                                                </tr>
                                                <tr class="table-light">
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
                                                        <td>
                                                            <?php
                                                            if(!empty($attestation[0]['DATE_DEBUT_VALIDITE']) && !empty($attestation[0]['DATE_FIN_VALIDITE'])) {
                                                                ?>
                                                                DU
                                                                <b><?php if(!empty($attestation[0]['DATE_DEBUT_VALIDITE'])) {echo date('d/m/Y', strtotime($attestation[0]['DATE_DEBUT_VALIDITE']));} ?></b>
                                                                AU
                                                                <b><?php if(!empty($attestation[0]['DATE_FIN_VALIDITE'])) {echo date('d/m/Y', strtotime($attestation[0]['DATE_FIN_VALIDITE']));} ?></b>
                                                                <?php
                                                            }else {
                                                                echo '<b class="text-danger">UNE ERREUR EST SURVENUE LORS DE LA VALIDATION. VEUILLEZ CONTACTER VOTRE ADMINISTRATEUR.</b>';
                                                            }
                                                            ?>
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
                                                    if(!empty($attestation[0]['DATE_DEBUT_VALIDITE']) && !empty($attestation[0]['DATE_FIN_VALIDITE'])) {
                                                        if(strtotime($attestation[0]['DATE_FIN_VALIDITE']) >= strtotime(date('Y-m-d',time()))) {
                                                            ?>
                                                            <button type="button" id="button_attestation_consultation_droits"
                                                                    class="btn btn-success btn-sm">Consultation de droits
                                                            </button>
                                                            <button type="button" id="button_attestation_impression" class="btn btn-warning btn-sm">
                                                                <i class="fa fa-print"></i> Imprimer l'attestation
                                                            </button>
                                                            <input type="hidden" value="<?= $assure['NUM_SECU']; ?>" id="num_secu_input"/>
                                                            <input type="hidden" value="<?= $attestation[0]['ID']; ?>" id="attestation_id_input"/>
                                                            <?php
                                                        }else {
                                                            echo '<b class="text-danger">CETTE ATTESTATION DE DROITS A EXPIRE DEPUIS LE '.date('d/m/Y',strtotime($attestation[0]['DATE_FIN_VALIDITE'])).'.</b>';
                                                        }
                                                    }
                                                }
                                                ?>
                                            </p>
                                            <p align="center" id="p_resultats_attestation_consultation"></p>
                                        </div>
                                    </div>
                                    <script type="application/javascript" src="<?= JS . 'page_agent_demandes.js'; ?>"></script>
                                </div>
                                <?php
                            }else{
                                echo 'echec'.$id_attestation;
                            }
                        }
                        else
                        {
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