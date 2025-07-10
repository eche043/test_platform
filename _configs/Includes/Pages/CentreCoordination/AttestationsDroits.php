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
                if($centre)
                {
                    if(isset($_POST['code_ets']) && !empty($_POST['code_ets']))
                    {
                        $ets = $COORDINATIONS->trouver_ets($centre['CODE_CENTRE'],$_POST['code_ets']);
                        if($ets)
                        {
                            ?>
                            <div class="col">
                                <p class="titres_p">ATTESTATIONS DE DROITS</p>
                                <!--<hr>--><input type="hidden" id="code_ets_input" value="<?= $ets['CODE_ETS'];?>" />
                                <?php include "../Forms/form_attestation_droits.php"; ?>
                                <p id="p_resultats_attestations" class="align_center"></p>
                                <script type="application/javascript" src="<?= JS.'page_centre_coordination_demandes.js';?>"></script>
                            </div>
                            <?php
                        }
                        else
                        {
                            echo '<script>window.location.href="'.URL.'centre-coordination/"</script>';
                        }
                    }
                    else
                    {
                        $etablissements = $COORDINATIONS->lister_ets($centre['CODE_CENTRE']);
                        $nb_ets = count($etablissements);
                        ?>
                        <div class="col">
                            <p class="titres_p"><i class="fa fa-newspaper"></i> ATTESTATION DE DROIT </p>
                            <?php
                            if($nb_ets) {
                                ?>
                                <table class="table table-bordered table-hover table-sm table-responsive-sm dataTable">
                                    <thead class="bg-secondary text-white">
                                    <tr>
                                        <th width="5">N°</th>
                                        <th width="100">CODE ETS</th>
                                        <th>RAISON SOCIALE</th>
                                        <th>VILLE</th>
                                        <th width="120">DATE DEBUT</th>
                                        <th width="5"></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $ligne = 1;
                                    foreach ($etablissements as $etablissement) {
                                        ?>
                                        <tr>
                                            <td align="right"><b><?= $ligne;?></b></td>
                                            <td><b><?= $etablissement['CODE_ETS'] ?></b></td>
                                            <td><?= $etablissement['RAISON_SOCIALE'] ?></td>
                                            <td><?= $etablissement['VILLE'] ?></td>
                                            <td align="center"><?= date("d/m/Y",strtotime($etablissement['DATE_DEBUT'])) ?></td>
                                            <td>
                                                <a href="attestations-droits.php?code-ets=<?= $etablissement['CODE_ETS'] ?>"><i class="fa fa-eye" aria-hidden="true"></i></a>
                                            </td>
                                        </tr>
                                        <?php
                                        $ligne++;
                                    }
                                    ?>
                                    </tbody>
                                </table>
                                <?php
                            }else {
                                echo '<p class="align_center alert alert-info">AUCUNE ETABLISSEMENT DE SANTE ENREGISTRE POUR CE CENTRE DE COORDINATION</p>';
                            }
                            ?>
                        </div>
                        <script type="text/javascript" src="<?= JS.'page_centre_coordination.js'?>"></script>
                        <?php
                    }
                }
                else
                {
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