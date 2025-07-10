<?php
    require_once '../../../Classes/UTILISATEURS.php';
    require_once '../../../Classes/ASSURES.php';
    require_once '../../../Classes/OGD.php';

    $num_secu = trim($_POST['num_secu']);
    $nom_prenom = strtoupper(trim($_POST['nom_prenom']));
    $module = trim($_POST['module']);

    $ASSURES= new ASSURES();
    $OGD= new OGD();



    if(empty($num_secu) && empty($nom_prenom)) {
        echo '<p align="center" class="alert alert-danger">VEUILLEZ SVP RENSEIGNER AU MOINS UN CHAMP</p>';
    }else {

        $assures = $ASSURES->moteur_recherche_assures($num_secu, $nom_prenom);
        $nb_assures = count($assures);

        if($nb_assures == 0) {
            echo '<p align="center" class="alert-primary"><b>AUCUN RESULTAT TROUVE</b></p>';
        }
        else {
            echo '<p align="center" class="alert-success"><b>'.number_format($nb_assures,'0','',' ').' RESULTAT(S) TROUVE(S)</b></p>';
            ?>
            <div class="row">
                <div class="col-sm-12">
                    <table align="center">
                        <tr>
                            <td>
                                <select class="form-control form-control-sm" id="export_assures" aria-label="Exporter">
                                    <option value="">EXPORTER</option>
                                    <option value="EXCEL" hidden>EXCEL</option>
                                    <option value="PDF">PDF</option>
                                </select>
                            </td>
                            <td>
                                <button type="button" id="btn_imprimer" class="btn btn-sm btn-info"><i class="fa fa-print"></i> Imprimer</button>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-sm-12">
                    <table class="table table-bordered table-sm table-hover" id="assures_table">
                        <thead class="bg-info">
                        <tr>
                            <th width="5">N°</th>
                            <th width="5">STATUT</th>
                            <th width="70">OGD AFF.</th>
                            <th width="70">OGD PST.</th>
                            <th width="10">CSP</th>
                            <th width="10">QUALITE</th>
                            <th width="10">CIVILITE</th>
                            <th width="10">GENRE</th>
                            <th width="100">N° SECU</th>
                            <th>NOM & PRENOM(S)</th>
                            <th width="100">DATE NAISS.</th>
                            <th width="100">DATE AFFIL.</th>
                            <th width="5"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                            $ligne = 1;
                            foreach ($assures as $assure) {
                                if($module == 'affiliation') {
                                    $ogd = $OGD->trouver('AFFL',$assure['CODE_OGD_COTISATIONS']);
                                }elseif($module == 'prestations') {
                                    $ogd = $OGD->trouver('PRST',$assure['CODE_OGD_PRESTATIONS_PROV']);
                                }
                                ?>
                                <tr>
                                    <td align="right"><?= $ligne;?></td>
                                    <td><?= $assure['STATUT'];?></td>
                                    <td><?= $assure['CODE_OGD_COTISATIONS'];?></td>
                                    <td><?= $assure['CODE_OGD_PRESTATIONS'];?></td>
                                    <td><?= $assure['CATEGORIE_PROFESSIONNELLE'];?></td>
                                    <td><?= $assure['QUALITE_CIVILE'];?></td>
                                    <td><?= $assure['CIVILITE'];?></td>
                                    <td><?= $assure['SEXE'];?></td>
                                    <td><b><?= $assure['NUM_SECU'];?></b></td>
                                    <td><?= $assure['NOM'].' '.$assure['PRENOM'];?></td>
                                    <td><?= date('d/m/Y',strtotime($assure['DATE_NAISSANCE']));?></td>
                                    <td><?= date('d/m/Y',strtotime($assure['DATE_AFFILIATION']));?></td>
                                    <td><a href="<?= URL.'centre-coordination/assure.php?code-ogd='.$ogd['CODE'].'&num-secu='.$assure['NUM_SECU'];?>" class="badge badge-info"><i class="fa fa-eye"></i></a></td>
                                </tr>
                                <?php
                                $ligne++;
                            }
                        ?>
                        </tbody>
                    </table>
                    <script>
                        $('#assures_table').DataTable();
                    </script>
                </div>
            </div>
            <?php
        }
    }
?>