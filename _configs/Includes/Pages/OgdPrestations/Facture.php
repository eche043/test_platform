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
            if(in_array('OGDP', $modules)) {
                $user_hab = explode(';',$user['FSE']);
                if (in_array('RPT', $user_hab) || in_array('LIQ', $user_hab) ) {
                    if (isset($_POST['type']) && isset($_POST['numero'])) {
                        require_once '../../../Classes/ASSURES.php';
                        require_once '../../../Classes/ACTESMEDICAUX.php';
                        require_once '../../../Classes/MEDICAMENTS.php';
                        require_once '../../../Classes/PROFESSIONNELSANTE.php';
                        require_once '../../../Classes/FACTURES.php';
                        require_once '../../../Classes/MOTIFSREJETS.php';

                        $ASSURES = new ASSURES();
                        $ACTESMEDICAUX = new ACTESMEDICAUX();
                        $MOTIFSREJETS = new MOTIFSREJETS();
                        $MEDICAMENTS = new MEDICAMENTS();
                        $PROFESSIONNELSANTE = new PROFESSIONNELSANTE();
                        $FACTURES = new FACTURES();

                        $facture = $FACTURES->trouver_facture(null, $_POST['numero']);

                        if (empty($facture['FEUILLE'])) {
                            echo '<script>window.location.href="' . URL . 'ogd-prestations/"</script>';
                        }
                        if ($facture['STATUT'] == 'A') {
                            echo '<script>window.location.href="' . URL . 'ogd-prestations/"</script>';
                        }

                        $type_facture = $FACTURES->trouver_type_facture($facture['TYPE_FEUILLE']);
                        $genre = $ASSURES->trouver_assure_genre($facture['GENRE']);
                        $ogd = $FACTURES->trouver_ogd($facture['NUM_OGD']);

                        $facture_date_soins = $facture['DATE_SOINS'];
                        $ps = $FACTURES->verifier_facture_ps($facture['PS'],null,$facture['ETABLISSEMENT'] , $facture_date_soins);
                        echo '<input type="hidden" value="' . $_POST['type'] . '" id="type_verification_input" />';
						
						$pointage_facture = $FACTURES->trouver_verification_facture($facture['FEUILLE']);

						$statut_verification = array('T','Y','B');
                    } else {
                        echo '<script>window.location.href="' . URL . 'ogd-prestations/"</script>';
                    }
                    ?>
                    <input type="hidden" id="user_input" value="<?= $user['ID_UTILISATEUR']; ?>"/>
                    <p class="titres_p"><i
                                class="fa fa-<?= str_replace('AMB', 'stethoscope', str_replace('DEN', 'tooth', str_replace('EXP', 'heartbeat', str_replace('HOS', 'bed', str_replace('MED', 'pills', $_POST['type']))))); ?>"></i>
                        <small>Bon de prise en charge médicale</small> (<?= $type_facture['LIBELLE']; ?>)</p>
                    <p align="right">
                        <a href="<?= URL . 'ogd-prestations/verification.php'; ?>" class="btn btn-primary btn-sm"><i
                                    class="fa fa-chevron-left"></i> Retourner</a>
                        <?php
                        if(!$pointage_facture){
                            ?>
                            <button type="button" data-toggle="modal" data-target="#modal_reception_facture"
                                    class="btn btn-info btn-sm"><b class="fa fa-thumbtack"></b> Confirmer Réception
                            </button></td>
                            <?php
                        }
                        if (($facture['STATUT_BORDEREAU'] == 0 && $_POST['type'] == 'DECA') || ($facture['STATUT_BORDEREAU'] == 1 && $_POST['type'] == 'LIQ' && $facture['STATUT'] != 'B')) {
                            ?>
                            <button type="button" data-toggle="modal" data-target="#modal_validation_facture"
                                    class="btn btn-success btn-sm" <?php 
                            /*if ($facture['STATUT'] != 'T' && $facture['STATUT'] != 'Y' && $facture['STATUT'] != 'B') {*/
                            if (!in_array($facture['STATUT'], $statut_verification)) {
								echo "disabled";
                            } ?>><b class="fa fa-check"></b> Valider
                            </button></td>
                            <?php
                        }
                        if ($facture['STATUT_BORDEREAU'] == 1 && $_POST['type'] == 'LIQ' && $facture['STATUT'] != 'B') {
                            ?>
                            <button type="button" data-toggle="modal" data-target="#modal_rejet_facture"
                                    class="btn btn-danger btn-sm" <?php if ($facture['STATUT'] != 'T' && $facture['STATUT'] != 'Y' && $facture['STATUT'] != 'B') {
                                echo "disabled";
                            } ?>><b class="fa fa-times"></b> Refuser
                            </button></td>
                            <?php
                        }
                        ?>
                    </p>
                    <p class="titres_factures_p_dark">Identification</p>
                    <div class="row">
                        <div class="col-sm-6">
                            <table class="table table-bordered">
                                <tr>
                                    <td colspan="2" align="center" class="h4"><b>Identification de la feuille de soins</b>
                                    </td>
                                </tr>
                                <tr style="background-color: #ccc" align="center">
                                    <td width="50%"><b>Date de soins</b></td>
                                    <td><b>N° OGD</b></td>
                                </tr>
                                <tr align="center">
                                    <td><?= date('d-m-Y', strtotime($facture['DATE_SOINS'])); ?></td>
                                    <td><?= $facture['NUM_OGD'] . ' | ' . $ogd['LIBELLE']; ?></td>
                                </tr>
                                <tr style="background-color: #ccc" align="center">
                                    <td width="50%"><b>N° FS Initiale</b></td>
                                    <td><b>N° Transaction</b></td>
                                </tr>
                                <tr align="center">
                                    <td><?= $facture['NUM_FS_INITIALE']; ?></td>
                                    <td id="num_facture_td"><?= $facture['FEUILLE']; ?></td>
                                </tr>
                                <tr style="background-color: #ccc" align="center">
                                    <td width="50%" colspan="2"><b>N° EP CNAM</b></td>
                                </tr>
                                <tr align="center">
                                    <td colspan="2"><?php if (empty($facture['NUM_EP_CNAM'])) {
                                            echo '&nbsp;';
                                        } else {
                                            echo $facture['NUM_EP_CNAM'];
                                        } ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-sm-6">
                            <table class="table table-bordered">
                                <tr>
                                    <td colspan="3" align="center" class="h4"><b>Identification de l'assuré</b></td>
                                </tr>
                                <tr style="background-color: #ccc" align="center">
                                    <td colspan="3"><b>Nom & Prénom(s)</b></td>
                                </tr>
                                <tr align="center">
                                    <td colspan="3"><b
                                                style="color: #4cae4c"><?= $facture['NOM'] . ' ' . $facture['PRENOM']; ?></b>
                                    </td>
                                </tr>
                                <tr style="background-color: #ccc" align="center">
                                    <td><b>N° Sécu</b></td>
                                    <td><b>Né(e) le</b></td>
                                    <td><b>Genre</b></td>
                                </tr>
                                <tr align="center">
                                    <td><b style="color: #ff0000"><?= $facture['NUM_SECU']; ?></b></td>
                                    <td><?= date('d-m-Y', strtotime($facture['DATE_NAISSANCE'])); ?></td>
                                    <td><?= $genre['LIBELLE']; ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-sm-12"></div>
                    </div>
                    <p class="titres_factures_p_dark">Etablissement d'accueil</p>
                    <div class="row">
                        <div class="col-sm-12">
                            <table class="table table-bordered">
                                <tr style="background-color: #ccc" align="center">
                                    <td width="150"><b>Code</b></td>
                                    <td><b>Nom</b></td>
                                </tr>
                                <tr align="center">
                                    <td id="code_ets_td"><?= $facture['ETABLISSEMENT']; ?></td>
                                    <td><?= $facture['NOM_ETS']; ?></td>
                                </tr>
                                <tr>
                                    <td style="background-color: #ccc"><b>Type d'établissement</b></td>
                                    <td align="center">
                                        <?php
                                        if ($facture['TYPE_ETS'] == 'U') {
                                            echo "URGENCE";
                                        } elseif ($facture['TYPE_ETS'] == 'T') {
                                            echo "CENTRE MEDICAL REFERENT";
                                        } elseif ($facture['TYPE_ETS'] == 'H') {
                                            echo "ELOIGNEMENT";
                                        } elseif ($facture['TYPE_ETS'] == 'A') {
                                            echo $facture['AUTRE_TYPE_ETS'];
                                        } elseif ($facture['TYPE_ETS'] == 'P') {
                                            echo "PHARMACIE";
                                        } elseif ($facture['TYPE_ETS'] == 'R') {
                                            echo "REFERENCE";
                                        }
                                        ?>

                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <p class="titres_factures_p_dark">Informations complémentaires sur l'assuré</p>
                    <div class="row">
                        <div class="col-sm-12">
                            <table class="table table-bordered">
                                <tr style="background-color: #ccc" align="center">
                                    <td width="50%"><b>Type complément</b></td>
                                    <td><b>Programme spécial</b></td>
                                </tr>
                                <tr align="center">
                                    <td>
                                        <?php
                                        if ($facture['INFO_COMPLEMENTAIRE'] == 'MG') {
                                            echo "MATERNITE/GROSSESSE";
                                        } elseif ($facture['INFO_COMPLEMENTAIRE'] == 'AVP') {
                                            echo "AVP / IMM V.:" . $facture['NUM_IMM_VEHICULE'];
                                        } elseif ($facture['INFO_COMPLEMENTAIRE'] == 'ATMP') {
                                            echo "AT/MP";
                                        } elseif ($facture['INFO_COMPLEMENTAIRE'] == 'AUT') {
                                            echo $facture['AUTRE_INFO_COMPLEMENTAIRE'];
                                        } else {
                                            echo "AUCUN";
                                        }
                                        ?>
                                    </td>
                                    <td></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <p class="titres_factures_p_dark">Professionnel de santé</p>
                    <div class="row">
                        <div class="col-sm-12">
                            <table class="table table-bordered">
                                <tr style="background-color: #ccc" align="center">
                                    <td width="150"><b>Code</b></td>
                                    <td><b>Nom & Prénom(s)</b></td>
                                    <td><b>Spécialité</b></td>
                                </tr>
                                <tr align="center">
                                    <td><?= $facture['PS']; ?></td>
                                    <td><?= $ps['nom_prenom']; ?></td>
                                    <td><?= $ps['libelle_specialite']; ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <?php
                    if (($facture['TYPE_FEUILLE'] == 'AMB' && $facture['STATUT'] != 'C') || $facture['TYPE_FEUILLE'] == 'DEN') {
                        ?>
                        <p class="titres_factures_p_dark">Affection</p>
                        <div class="row">
                            <div class="col-sm-12">
                                <table class="table table-bordered">
                                    <tr align="center">
                                        <?php
                                        echo '<td width="50%"><b>Affection 1</b>: ' . $facture['AFFECTION1'] . '</td>';
                                        echo '<td width="50%"><b>Affection 2</b>: ' . $facture['AFFECTION2'] . '</td>';
                                        ?>
                                    </tr>

                                </table>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                    <p class="titres_factures_p_dark">Prestation(s)</p>
                    <div class="row">
                        <div class="col-sm-12">
                            <table class="table table-bordered">
                                <thead class="bg-primary">
                                <tr>
                                    <th width="100">Acte</th>
                                    <th>Désignation</th>
                                    <?php
                                    if ($facture['TYPE_FEUILLE'] != 'EXP') {
                                        echo '<th width="160">Période</th>';
                                    }

                                    if ($facture['TYPE_FEUILLE'] != 'DEN') {
                                        echo '<th width="10">Qté</th>';
                                    } else {
                                        echo '<th width="10">N° Dent</th>';
                                    }
                                    ?>
                                    <th width="70">Prix U.</th>

                                    <th width="70">Prix T.</th>
                                    <th width="70">P. CMU</th>
                                    <th width="70">P. AC</th>
                                    <th width="70">P. Assuré</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $actes = $FACTURES->trouver_facture_liste_actes($facture['FEUILLE']);
                                $total_montant = 0;
                                $total_part_assure = 0;
                                $total_part_cmu = 0;
                                foreach ($actes as $acte) {
                                    if ($acte['TYPE'] == 'm') {
                                        $medicament = $MEDICAMENTS->trouver_medicament('EAN13', $acte['CODE']);
                                        $designation = $medicament['LIBELLE'];
                                    } else {
                                        $ngap = $ACTESMEDICAUX->trouver(strtoupper($acte['CODE']));
                                        $designation = $ngap['LIBELLE'];
                                    }
                                    $montant = $acte['MONTANT'] * $acte['QUANTITE'];
                                    $part_cmu = round($montant * 0.7);
                                    $part_assure = ($montant - $part_cmu);
                                    ?>
                                    <tr>
                                        <td><?= $acte['CODE']; ?></td>
                                        <td><?= $designation; ?></td>
                                        <?php
                                        if ($facture['TYPE_FEUILLE'] != 'EXP') {
                                            echo '<td>' . date('d-m-Y', strtotime($acte['DEBUT'])) . ' / ' . date('d-m-Y', strtotime($acte['FIN'])) . '</td>';
                                        }

                                        if ($facture['TYPE_FEUILLE'] != 'DEN') {
                                            echo '<td align="right">' . $acte['QUANTITE'] . '</td>';
                                        } else {
                                            echo '<td align="right">' . $acte['NUM_DENT'] . '</td>';
                                        }
                                        ?>
                                        <td align="right"><?= $acte['MONTANT']; ?></td>
                                        <td align="right"><?= number_format($montant, '0', '', ' '); ?></td>
                                        <td align="right"><?= number_format($part_cmu, '0', '', ' '); ?></td>
                                        <td align="right">0</td>
                                        <td align="right"><?= number_format($part_assure, '0', '', ' '); ?></td>
                                    </tr>
                                    <?php
                                    $total_montant = $total_montant + $montant;
                                    $total_part_cmu = $total_part_cmu + $part_cmu;
                                    $total_part_assure = $total_part_assure + $part_assure;
                                }
                                ?>
                                <tr class="bg-warning" style="color: #ffffff">
                                    <td colspan="<?php if ($facture['TYPE_FEUILLE'] != 'EXP') {
                                        echo 5;
                                    } else {
                                        echo 4;
                                    } ?>"><b>TOTAL</b></td>
                                    <td align="right"><b><?= number_format($total_montant, '0', '', ' '); ?></b></td>
                                    <td align="right"><b><?= number_format($total_part_cmu, '0', '', ' '); ?></b></td>
                                    <td align="right">0</td>
                                    <td align="right"><b><?= number_format($total_part_assure, '0', '', ' '); ?></b></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal fade" id="modal_validation_facture" tabindex="-1" role="dialog"
                         aria-labelledby="myModalLabel">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="myModalLabel">VOULEZ VOUS VALIDER LA FACTURE <b
                                                style="color:#FF0000"><u><?= $_POST['numero']; ?></u></b>?</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                                aria-hidden="true">&times;</span></button>
                                </div>
                                <div class="modal-body">
                                    <p align="center" id="resultats_verification_facture"></p>
                                    <form class="form-horizontal" id="form_validation_facture">
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <button type="submit" class="btn btn-primary btn-sm" id="btn_validation">
                                                    Valider
                                                </button>
                                                <a class="btn btn-light btn-md" data-dismiss="modal">Annuler</a>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade" id="modal_rejet_facture" tabindex="-1" role="dialog">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title" id="myModalLabel">Rejet de la
                                        facture <b style="color:#FF0000"><u><?= $facture['FEUILLE']; ?></u></b></h4>
                                </div>
                                <p align="center" id="p_resultats_rejet"></p>
                                <div class="modal-body" id="body_modal_rejet">
                                    <p class="h4" align="center">SOUHAITEZ-VOUS VRAIMENT REJETER CETTE FACTURE ?<br/><small>SELECTIONNEZ
                                            LE MOTIF DU REJET</small></p>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <p align="center" id="resultats_rejet_facture"></p>
                                            <form class="form-horizontal" id="form_rejet_facture">
                                                <?php
                                                $u = $FACTURES->trouver_facture_liste_actes($facture['FEUILLE']);
                                                $nbe_u = count($u);
                                                $i = 1;
                                                foreach ($u as $u_acte) {
                                                    if ($u_acte['TYPE'] == 'm') {
                                                        $donnees_acte[$i] = $MEDICAMENTS->trouver_medicament("EAN13", $u_acte['CODE']);
                                                    } else {
                                                        $donnees_acte[$i] = $ACTESMEDICAUX->trouver_un_acte(strtoupper($u_acte['CODE']));
                                                    }
                                                    $i++;
                                                }
                                                if ($facture['TYPE_FEUILLE'] == 'HOS') {
                                                    $nb_actes = 9;
                                                } else {
                                                    $nb_actes = 3;
                                                }
                                                for ($o = 1; $o <= $nb_actes; $o++) {
                                                    if($o<=$nbe_u){
                                                    ?>
                                                    <div class="form-group row">
                                                        <div class="col-sm-3">
                                                            <input class="form-control form-control-sm code_acte" type="text"
                                                                   id="code_acte_<?= $o; ?>_input" maxlength="13"
                                                                   value="<?= $donnees_acte[$o]['CODE']; ?>"
                                                                   placeholder="Code acte" autocomplete="off" disabled/>
                                                        </div>
                                                        <div class="col-sm-9">
                                                            <input class="form-control form-control-sm" type="text" id=""
                                                                   value="<?= $donnees_acte[$o]['LIBELLE']; ?>"
                                                                   placeholder="libelle acte" autocomplete="off" disabled/>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-sm-12">
                                                            <select class="form-control form-control-sm motif_rejet"
                                                                    id="code_motif_<?= $o; ?>_input" <?php if (empty($donnees_acte[$o]['CODE'])) {
                                                                echo 'disabled';
                                                            } ?>>
                                                                <option value="">Sélectionnez le motif du rejet</option>
                                                                <?php
                                                                $m = $MOTIFSREJETS->liste();
                                                                foreach ($m as $motif) {
                                                                    echo '<option value="' . $motif['CODE'] . '">' . $motif['LIBELLE'] . '</option>';
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <?php
                                                    }
                                                }
                                                ?>
                                                <div class="form-group row">
                                                    <div class="col-sm-6">
                                                        <button type="submit" class="btn btn-danger btn-sm" id="btn_rejet_facture">Rejeter la facture </button>
                                                        <a class="btn btn-light btn-md" data-dismiss="modal">Annuler</a>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>					
                    <div class="modal fade" id="modal_reception_facture" tabindex="-1" role="dialog"
                         aria-labelledby="myModalLabel">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="myModalLabel">VOULEZ VOUS CONFIRMER LA RECEPTION DE LA FACTURE <b
                                                style="color:#FF0000"><u><?= $_POST['numero']; ?></u></b> ?</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                                aria-hidden="true">&times;</span></button>
                                </div>
                                <div class="modal-body">
                                    <p align="center" id="resultats_reception_facture"></p>
                                    <form class="form-horizontal" id="form_reception_facture">
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <button type="submit" class="btn btn-primary btn-sm" id="btn_validation_reception">
                                                    Valider
                                                </button>
                                                <a class="btn btn-light btn-md" data-dismiss="modal">Annuler</a>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php
                }else{
                    echo '<script>window.location.href="'.URL.'"</script>';
                }
            }else{
                echo '<script>window.location.href="'.URL.'"</script>';
            }
        }
    }
}else{
    session_destroy();
    echo '<script>window.location.href="'.URL.'"</script>';
}
?>
<script type="text/javascript" src="<?= JS.'page_ogd_prestations.js?v=1.0.1' ?>"></script>
<script>
    $(function () {
        $('#dataTable').DataTable();
    });
</script>

