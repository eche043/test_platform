<?php
require_once '../../../Classes/UTILISATEURS.php';
if(isset($_SESSION['ECMU_USER_ID'])){
    $session_user = $_SESSION['ECMU_USER_ID'];
    if(!empty($session_user)){
        $UTILISATEURS = new UTILISATEURS();
        $utilisateur_existe = $UTILISATEURS->trouver($session_user,null,null);

        if(!empty($utilisateur_existe['ID_UTILISATEUR'])){
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
                require_once '../../../Classes/ASSURES.php';
                require_once '../../../Classes/ACTESMEDICAUX.php';
                require_once '../../../Classes/ENTENTESPREALABLES.php';
                $ASSURES = new ASSURES();
                $ENTENTESPREALABLES = new ENTENTESPREALABLES();
                $ACTESMEDICAUX = new ACTESMEDICAUX();

                $id_entente_prealable = trim($_POST['id_entente_prealable']);

                $entente_prealable = $ENTENTESPREALABLES->trouver_distinct_entente($id_entente_prealable);
                if (empty($entente_prealable['NUM_ENTENTE_PREALABLE'])) {
                    ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <p align="center">AUCUNE DEMANDE TROUVEE</p>
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
                            <th>N° ENTENTE</th>
                            <th>N° SECU</th>
                            <th>NOM & PRENOM</th>
                            <th>TYPE DEMANDE</th>
                            <th>
                            <?php if($entente_prealable['TYPE_EP']=="EXP"){
                                echo 'ACTES';
                            }else{
                                echo 'TYPE HOSPIT.';
                            }
                            ?>
                            </th>
                            <th>MOTIF DEMANDE</th>
                            <th>STATUT</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                            $assure = $ASSURES->trouver($entente_prealable['NUM_SECU']);

                            if($entente_prealable['TYPE_EP']=="HOS"){
                                $type_entente = "HOSPITALISATION";
                                if($entente_prealable['TYPE_HOSP']=="HC"){
                                    $acte_medical = "HOSPITALISATION CHIRURGICALE";
                                }elseif($entente_prealable['TYPE_HOSP']=="HM"){
                                    $acte_medical = "HOSPITALISATION MEDICALE";
                                }elseif($entente_prealable['TYPE_HOSP']=="HO"){
                                    $acte_medical = "HOSPITALISATION OBSTETRICALE";
                                }

                            }else if($entente_prealable['TYPE_EP']=="EXP"){
                                $type_entente = "BIOLOGIE-IMAGERIE";
                                $actes = $ENTENTESPREALABLES->trouver_all_entente($id_entente_prealable,null,null);
                                $acte_medical = "";
                                foreach ($actes as $acte) {
                                    $actes_medicaux = $ACTESMEDICAUX->trouver($acte['CODE_ACTE_MEDICAL']);
                                    //var_dump($actes_medicaux);
                                    $acte_medical = $acte_medical. $acte['CODE_ACTE_MEDICAL'] . ": " . $actes_medicaux['LIBELLE'] . "<br/>";
                                }
                            }

                            if($entente_prealable['STATUT']=="1"){
                                $statut_demande = "VALIDEE";
                                $motif_rejet = "";
                            }else if($entente_prealable['STATUT']=="2"){
                                $statut_demande = "REJETEE";
                                $motif_rejet = $entente_prealable['MOTIF'];
                            }else if($entente_prealable['STATUT']=="0" || $entente_prealable['STATUT']==null){
                                $statut_demande = "EN COURS D'ANALYSE";
                                $motif_rejet = "";
                            }else{
                                $statut_demande = "EXPIRE";
                                $motif_rejet = "";
                            }
                            ?>
                            <tr>
                                <td><?= $entente_prealable['NUM_ENTENTE_PREALABLE']; ?></td>
                                <td><b><?= $entente_prealable['NUM_SECU']; ?></b></td>
                                <td><?= $assure['NOM'].' '.$assure['PRENOM']; ?></td>
                                <td><b><?= $type_entente;?></b></td>
                                <td style="text-align:left"><b><?= $acte_medical;?></b></td>
                                <td><b><?= $entente_prealable['MOTIF_DEMANDE'];?></b></td>
                                <td><b><?= $statut_demande;?></b></td>
                            </tr>
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

