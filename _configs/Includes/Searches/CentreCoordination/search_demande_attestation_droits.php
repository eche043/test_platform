<?php
require_once '../../../Classes/UTILISATEURS.php';
if(isset($_SESSION['ECMU_USER_ID'])){
    $session_user = $_SESSION['ECMU_USER_ID'];
    if(!empty($session_user)){
        $UTILISATEURS = new UTILISATEURS();
        $utilisateur_existe = $UTILISATEURS->trouver($session_user,null,null);

        if(!empty($utilisateur_existe['ID_UTILISATEUR'])){
            $num_secu = trim($_POST['num_secu']);
            require_once '../../../Classes/ASSURES.php';
            require_once '../../../Classes/ATTESTATIONSDROITS.php';
            $ASSURES = new ASSURES();
            $ATTESTATIONSDROITS = new ATTESTATIONSDROITS();
            if($utilisateur_existe['ACTIF'] != 1){
                ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <p>VOTRE IDENTIFIANT A ETE DESACTIVE. VOUS NE POUVEZ PAS EFFECTUER CETTE ACTION. VEUILLEZ CONTACTER VOTRE ADMINISTRATEUR SYSTEME.</p>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <?php
            }else{
                $attestations = $ATTESTATIONSDROITS->trouver(null,$num_secu);
                if (count($attestations)==0) {
                ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <p align="center">AUCUNE ATTESTATION TROUVEE</p>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php
                } else {
                    ?>
                    <table class="table table-sm table-bordered table-hover" id="dataTable">
                        <thead class="bg-info">
                        <tr>
                            <th>N°</th>
                            <th>N° SECU</th>
                            <th>NOM</th>
                            <th>PRENOM</th>
                            <th>DATE DEMANDE</th>
                            <th>STATUT</th>
                            <th>DATE DEBUT</th>
                            <th>DATE FIN</th>
                            <th width="5"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $ligne = 1;
                        foreach ($attestations as $attestation) {
                            $assure = $ASSURES->trouver($attestation['NUM_SECU']);
                            if (empty($attestation['DATE_DEBUT_VALIDITE'])) {
                                $date_debut = '';
                                $date_fin = '';
                            } else {
                                $date_debut = date('d/m/Y', strtotime($attestation['DATE_DEBUT_VALIDITE']));
                                $date_fin = date('d/m/Y', strtotime($attestation['DATE_FIN_VALIDITE']));
                            }
                            ?>
                            <tr>
                                <td><?= $ligne; ?></td>
                                <td><?= $attestation['NUM_SECU']; ?></td>
                                <td><b><?= $assure['NOM']; ?></b></td>
                                <td><b><?= $assure['PRENOM']; ?></b></td>
                                <td><?= date('d/m/Y', strtotime($attestation['DATE_REG'])); ?></td>
                                <td><?php
                                    if ($attestation['STATUT_ATTESTATION'] == 0) {
                                        $statut = 'EN ATTENTE';
                                    } elseif ($attestation['STATUT_ATTESTATION'] == 1) {
                                        $statut = 'VALIDEE';
                                    } elseif ($attestation['STATUT_ATTESTATION'] == 2) {
                                        $statut = 'REFUSEE';
                                    } else {
                                        $statut = 'ANNULLEE';
                                    }
                                    echo $statut;
                                    ?>
                                </td>
                                <td><b style="color: blue"><?= $date_debut; ?></b></td>
                                <td><b style="color: blue"><?= $date_fin; ?></td>
                                <td>
                                    <a href="<?= URL . 'centre-coordination/attestation-droits.php?id='.$attestation['ID'].'&code-ets='.$_POST['code_ets'] ?>"
                                       class="badge badge-info"><i class="fa fa-eye"></i></a></td>
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
        }else{
            ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <p>VOTRE IDENTIFIANT EST INCORRECT. VOUS NE POUVEZ PAS EFFECTUER CETTE ACTION. VEUILLEZ CONTACTER VOTRE ADMINISTRATEUR SYSTEME.</p>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php
        }
    }else{
        ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <p>VOTRE IDENTIFIANT EST INCORRECT. VOUS NE POUVEZ PAS EFFECTUER CETTE ACTION. VEUILLEZ CONTACTER VOTRE ADMINISTRATEUR SYSTEME.</p>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <?php
    }
}else{
    ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <p>VOTRE IDENTIFIANT EST INCORRECT. VOUS NE POUVEZ PAS EFFECTUER CETTE ACTION. VEUILLEZ CONTACTER VOTRE ADMINISTRATEUR SYSTEME.</p>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php
}

?>

