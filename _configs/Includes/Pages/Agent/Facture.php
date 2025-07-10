<?php
require_once '../../../Functions/function_convert_special_characters_to_normal.php';
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
            if(in_array('AGAC',$modules)) {
                $user_ets = $UTILISATEURS->trouver_ets_utilisateur($user['ID_UTILISATEUR']);
                if(!empty($user_ets['CODE_ETS'])) {
                    if(isset($_POST['type']) && !empty($_POST['type']) && isset($_POST['num']) && !empty($_POST['num'])) {
                        require_once '../../../Classes/FACTURES.php';
                        $FACTURES = new FACTURES();

                        $facture = $FACTURES->trouver($_POST['num']);
                        if(!empty($facture['FEUILLE'])) {
                            if($user_ets['CODE_ETS'] == $facture['ETABLISSEMENT']) {
                                if($facture['TYPE_FEUILLE'] == $_POST['type']) {
                                    require_once '../../../Classes/OGD.php';
                                    require_once '../../../Classes/ASSURES.php';
                                    require_once '../../../Classes/ACTESMEDICAUX.php';
                                    $OGD = new OGD();
                                    $ASSURES = new ASSURES();
                                    $ACTESMEDICAUX = new ACTESMEDICAUX();
                                    $assure = $ASSURES->trouver($facture['NUM_SECU']);
                                    if(!empty($assure['NUM_SECU'])) {
                                        if(!empty($facture['PS'])){
                                            $ps = $FACTURES->verifier_facture_ps($facture['PS'],NULL,$facture['ETABLISSEMENT'],strtoupper(date('Y-m-d',strtotime($facture['DATE_SOINS']))));
                                        }else{
                                            $ps['nom_prenom'] = NULL;
                                            $ps['libelle_specialite'] = NULL;
                                        }


                                        $genre = $ASSURES->trouver_genre($facture['GENRE']);
                                        $type = $FACTURES->trouver_type_facture($_POST['type']);
                                        if(!empty($type['CODE'])) {
                                            $ogd = $OGD->trouver('PRST',$facture['NUM_OGD']);
                                            ?>
                                            <div id="messages_erreur"></div>
                                            <div class="container" id="div_facture">
                                                <p class="titres_p"><small>Bon de prise en charge médicale</small> <?= $type['LIBELLE'];?> (<b id="type_facture_b"><?= $type['CODE'];?></b>)</p>
                                                <p align="right">
                                                    <a href="<?= URL.'agent/';?>" class="btn btn-primary btn-sm"><i class="fa fa-chevron-left"></i> Retourner</a>
                                                    <?php
                                                    if($type['CODE'] != 'MED') {
                                                        echo '<button type="button" class="btn btn-success btn-sm" id="btn_imprimer_facture"><i class="fa fa-print"></i> Imprimer</button>';
                                                    }
                                                    ?>
                                                </p>
                                                <p class="titres_factures_p_dark">Identification</p>
                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <table class="table table-sm table-bordered">
                                                            <tr>
                                                                <td colspan="2" align="center" class="h6"><b>Identification de la feuille de soins</b></td>
                                                            </tr>
                                                            <tr style="background-color: #ccc" align="center">
                                                                <td width="50%"><b>Date de soins</b></td>
                                                                <td><b>N° OGD</b></td>
                                                            </tr>
                                                            <tr align="center">
                                                                <td><?= date('d/m/Y',strtotime($facture['DATE_SOINS']));?></td>
                                                                <td><?= $facture['NUM_OGD'].' | '.$ogd['LIBELLE'];?></td>
                                                            </tr>
                                                            <tr style="background-color: #ccc" align="center">
                                                                <td width="50%"><b>N° FS Initiale</b></td>
                                                                <td><b>N° Transaction</b></td>
                                                            </tr>
                                                            <tr align="center">
                                                                <td><?= $facture['NUM_FS_INITIALE'];?></td>
                                                                <td id="num_facture_td"><?= $facture['FEUILLE'];?></td>
                                                            </tr>
                                                            <tr style="background-color: #ccc" align="center">
                                                                <td width="50%" colspan="2"><b>N° EP CNAM</b></td>
                                                            </tr>
                                                            <tr align="center">
                                                                <td colspan="2"><?php if(empty($facture['NUM_EP_CNAM'])){echo '&nbsp;';}else {echo $facture['NUM_EP_CNAM'];} ?></td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <table class="table table-sm table-bordered">
                                                            <tr>
                                                                <td colspan="3" align="center" class="h6"><b>Identification de l'assuré</b></td>
                                                            </tr>
                                                            <tr style="background-color: #ccc" align="center">
                                                                <td colspan="3"><b>Nom & Prénom(s)</b></td>
                                                            </tr>
                                                            <tr align="center">
                                                                <td colspan="3"><b style="color: #4cae4c"><?= $facture['NOM'].' '.$facture['PRENOM'] ;?></b></td>
                                                            </tr>
                                                            <tr style="background-color: #ccc" align="center">
                                                                <td><b>N° Sécu</b></td>
                                                                <td><b>Né(e) le</b></td>
                                                                <td><b>Genre</b></td>
                                                            </tr>
                                                            <tr align="center">
                                                                <td><b style="color: #ff0000"><?= $facture['NUM_SECU'];?></b></td>
                                                                <td><?= date('d/m/Y',strtotime($facture['DATE_NAISSANCE']));?></td>
                                                                <td><?= $genre['LIBELLE'];?></td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                    <div class="col-sm-12"></div>
                                                </div>

                                                <p class="titres_factures_p_dark">Etablissement d'accueil</p>
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <table class="table table-sm table-bordered">
                                                            <tr style="background-color: #ccc" align="center">
                                                                <td width="150"><b>Code</b></td>
                                                                <td><b>Nom</b></td>
                                                            </tr>
                                                            <tr align="center">
                                                                <td id="code_ets_td"><?= $facture['ETABLISSEMENT'];?></td>
                                                                <td><?= $facture['NOM_ETS'];?></td>
                                                            </tr>
                                                            <tr>
                                                                <td style="background-color: #ccc"><b>Type d'établissement</b></td>
                                                                <td align="center">
                                                                    <?php
                                                                    if($facture['TYPE_ETS'] == 'U') {
                                                                        echo "URGENCE";
                                                                    }elseif($facture['TYPE_ETS'] == 'T') {
                                                                        echo "CENTRE MEDICAL REFERENT";
                                                                    }elseif($facture['TYPE_ETS'] == 'H') {
                                                                        echo "ELOIGNEMENT";
                                                                    }elseif($facture['TYPE_ETS'] == 'A') {
                                                                        echo $facture['AUTRE_TYPE_ETS'];
                                                                    }elseif($facture['TYPE_ETS'] == 'P') {
                                                                        echo "PHARMACIE";
                                                                    }elseif($facture['TYPE_ETS'] == 'R') {
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
                                                        <table class="table table-sm table-bordered">
                                                            <tr style="background-color: #ccc" align="center">
                                                                <td width="50%"><b>Type complément</b></td>
                                                                <td><b>Programme spécial</b></td>
                                                            </tr>
                                                            <tr align="center">
                                                                <td>
                                                                    <?php
                                                                    if($facture['INFO_COMPLEMENTAIRE'] == 'MG') {
                                                                        echo "MATERNITE/GROSSESSE";
                                                                    }elseif($facture['INFO_COMPLEMENTAIRE'] == 'AVP') {
                                                                        echo "AVP / IMM V.:".$facture['NUM_IMM_VEHICULE'];
                                                                    }elseif($facture['INFO_COMPLEMENTAIRE'] == 'ATMP') {
                                                                        echo "AT/MP";
                                                                    }elseif($facture['INFO_COMPLEMENTAIRE'] == 'AUT') {
                                                                        echo $facture['AUTRE_INFO_COMPLEMENTAIRE'];
                                                                    }else {
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
                                                        <table class="table table-sm table-bordered">
                                                            <tr style="background-color: #ccc" align="center">
                                                                <td width="150"><b>Code</b></td>
                                                                <td><b>Nom & Prénom(s)</b></td>
                                                                <td><b>Spécialité</b></td>
                                                            </tr>
                                                            <tr align="center">
                                                                <td><?= $facture['PS'];?></td>
                                                                <td><?=
                                                                    $ps['nom_prenom'];?></td>
                                                                <td><?= $ps['libelle_specialite'];?></td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </div>

                                                <?php
                                                if(($facture['TYPE_FEUILLE'] == 'AMB' && $facture['STATUT'] != 'C') || $facture['TYPE_FEUILLE']=='DEN') {
                                                    ?>
                                                    <p class="titres_factures_p_dark">Affection</p>
                                                    <div class="row">
                                                        <div class="col-sm-12">
                                                            <table class="table table-sm table-bordered">
                                                                <tr align="center">
                                                                    <?php
                                                                    echo '<td width="50%"><b>Affection 1</b>: '.$facture['AFFECTION1'].'</td>';
                                                                    echo '<td width="50%"><b>Affection 2</b>: '.$facture['AFFECTION2'].'</td>';
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
                                                        <table class="table table-sm table-bordered">
                                                            <thead class="bg-primary">
                                                            <tr>
                                                                <th width="100">Acte</th>
                                                                <th>Désignation</th>
                                                                <?php
                                                                if($facture['TYPE_FEUILLE'] != 'EXP' && $facture['TYPE_FEUILLE'] != 'MED' ) {
                                                                    echo '<th width="160">Période</th>';
                                                                }
                                                                if($facture['TYPE_FEUILLE'] == 'MED') {
                                                                    echo '<th width="10">Qté Presc.</th>';
                                                                    echo '<th width="10">Qté Serv.</th>';
                                                                }
                                                                if($facture['TYPE_FEUILLE'] != 'MED'){
                                                                    if($facture['TYPE_FEUILLE'] != 'DEN') {
                                                                        echo '<th width="10">Qté</th>';
                                                                    }else{
                                                                        echo '<th width="10">N° Dent</th>';
                                                                    }
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
                                                            $total_part_ac = 0;
                                                            foreach ($actes as $acte) {
                                                                $details_acte = $FACTURES->trouver_facture_acte($facture['FEUILLE'],$acte['CODE']);
																$acte_code = $details_acte['CODE'];
																$designation = strtoupper($details_acte['LIBELLE']);
																$montant = $acte['MONTANT'] * $acte['QUANTITE'];
                                                                $part_cmu = $acte['PART_RO'] * $acte['QUANTITE'];
                                                                //$part_ac = $acte['PART_RC'] * $acte['QUANTITE'];
																/*if($acte['MONTANT'] > $details_acte['MONTANT_BASE']){
																	$montant_base = $details_acte['MONTANT_BASE'] * $acte['QUANTITE'];
																	$part_cmu = round($montant_base * 0.7);
																}
																else{
																	$part_cmu = round($montant * 0.7);
																}*/
                                                                /* $montant = $acte['MONTANT'] * $acte['QUANTITE'];
                                                                $part_cmu = round($montant * 0.7); */
                                                                if($facture['CODE_OGD_AFFILIATION'] == '03016000') {$part_ac = $montant - $part_cmu; $part_assure = ($montant - ($part_cmu + $part_ac));}else {$part_ac = $acte['PART_RC'] * $acte['QUANTITE']; $part_assure = $acte['PART_ASSURE'] * $acte['QUANTITE'];}

                                                                ?>
                                                                <tr>
                                                                    <td><?= $details_acte['CODE'];?></td>
                                                                    <td><?= strtoupper(conversionCaractere($details_acte['LIBELLE']));?></td>
                                                                    <?php
                                                                    if($facture['TYPE_FEUILLE'] != 'EXP' && $facture['TYPE_FEUILLE'] != 'MED') {
                                                                        echo '<td>'.date('d/m/Y',strtotime($acte['DEBUT'])).' - '.date('d/m/Y',strtotime($acte['FIN'])).'</td>';
                                                                    }

                                                                    if($facture['TYPE_FEUILLE'] == 'MED') {
                                                                        echo '<td align="right">'.$acte['QUANTITE_PRESCRITE'].'</td>';
                                                                    }
                                                                    if($facture['TYPE_FEUILLE'] != 'DEN') {
                                                                        echo '<td align="right">'.$acte['QUANTITE'].'</td>';
                                                                    }else{
                                                                        echo '<td align="right">'.$acte['NUM_DENT'].'</td>';
                                                                    }
                                                                    ?>
                                                                    <td align="right"><?= $acte['MONTANT'];?></td>
                                                                    <td align="right"><?= number_format($montant,'0','',' ');?></td>
                                                                    <td align="right"><?= number_format($part_cmu,'0','',' ');?></td>
                                                                    <td align="right"><?= number_format($part_ac,'0','',' ');?></td>
                                                                    <td align="right"><?= number_format($part_assure,'0','',' ');?></td>
                                                                </tr>
                                                                <?php
                                                                $total_montant = $total_montant + $montant;
                                                                $total_part_cmu = $total_part_cmu + $part_cmu;
                                                                $total_part_ac = $total_part_ac + $part_ac;
                                                                $total_part_assure = $total_part_assure + $part_assure;
                                                            }
                                                            ?>
                                                            <tr class="bg-warning" style="color: #ffffff">
                                                                <td colspan="<?php if($facture['TYPE_FEUILLE'] != 'EXP') {echo 5;}else {echo 4;} ?>"><b>TOTAL</b></td>
                                                                <td align="right"><b><?= number_format($total_montant,'0','',' ');?></b></td>
                                                                <td align="right"><b><?= number_format($total_part_cmu,'0','',' ');?></b></td>
                                                                <td align="right"><b><?= number_format($total_part_ac,'0','',' ');?></b></td>
                                                                <td align="right"><b><?= number_format($total_part_assure,'0','',' ');?></b></td>
                                                            </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            <script type="application/javascript" src="<?= JS.'page_agent_facture.js';?>"></script>
                                            <?php
                                        }else {
                                            echo '<script>window.location.href="'.URL.'agent/"</script>';
                                        }
                                    }else {
                                        echo '<script>window.location.href="'.URL.'agent/"</script>';
                                    }
                                }else {
                                    echo '<script>window.location.href="'.URL.'agent/"</script>';
                                }
                            }else {
                                echo '<script>window.location.href="'.URL.'agent/"</script>';
                            }
                        }else{
                            echo '<script>window.location.href="'.URL.'agent/"</script>';
                        }
                    }else {
                        echo '<script>window.location.href="'.URL.'agent/"</script>';
                    }
                }else {
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
    })
</script>
