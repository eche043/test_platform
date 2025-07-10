<?php
require_once '../../../Classes/UTILISATEURS.php';
if(URL) {
    echo '<script>window.location.href="'.URL.'"</script>';
}else {
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
                    $type =  $_POST['type'];
                    $num =  $_POST['numero'];
                    if(isset($type) && isset($num)) {
                        require_once "../../../Classes/ASSURES.php";
                        require_once "../../../Classes/ACTESMEDICAUX.php";
                        require_once "../../../Classes/MEDICAMENTS.php";
                        require_once "../../../Classes/PROFESSIONNELSANTE.php";
                        require_once "../../../Classes/FACTURES.php";
                        $ASSURES = new ASSURES();
                        $ACTESMEDICAUX = new ACTESMEDICAUX();
                        $MEDICAMENTS = new MEDICAMENTS();
                        $PROFESSIONNELSANTE = new PROFESSIONNELSANTE();
                        $FACTURES = new FACTURES();
                        $facture = $FACTURES->trouver_facture(NULL,$num);
                        if(empty($facture['FEUILLE'])) {
                            echo '<script>window.location.href="'.URL.'assure/"</script>';
                        }
                        if($facture['TYPE_FEUILLE'] != $type) {
                            echo '<script>window.location.href="'.URL.'assure/"</script>';
                        }
                        if($facture['NUM_SECU'] != $user['NUM_SECU']) {
                            echo '<script>window.location.href="'.URL.'assure/"</script>';
                        }
                        $type_facture = $FACTURES->trouver_type_facture($type);
                        $genre = $ASSURES->trouver_assure_genre($facture['GENRE']);
                        $ogd = $FACTURES->trouver_ogd($facture['NUM_OGD']);
                        $date_soins_facture = date('d/m/Y',strtotime($facture['DATE_SOINS']));
                        $ps = $FACTURES->verifier_facture_ps($facture['PS'],null,$facture['ETABLISSEMENT'],$facture['DATE_SOINS']);
                        if($ps['status']==false){
                            $ps['nom_prenom'] = NULL;
                            $ps['libelle_specialite'] = NULL;
                        }
                        echo '<input type="hidden" value="'.$type.'" id="type_fs_input" />';
                        if(empty($facture)) {
                            echo '<script>window.location.href="'.URL.'assure/"</script>';
                        }

                    }else {
                        echo '<script>window.location.href="'.URL.'assure/"</script>';
                    }
                    ?>
                    <div class="container">
                        <p class="titres_p"><i class="fa fa-<?= str_replace('AMB','stethoscope',str_replace('DEN','tooth',str_replace('EXP','heartbeat',str_replace('HOS','bed',str_replace('MED','pills',$type)))));?>"></i> <small>Bon de prise en charge médicale</small> (<?= $type_facture['LIBELLE'];?>)</p>
                        <p align="right">
                            <a href="<?= URL.'assure/consommations.php';?>" class="btn btn-primary btn-sm"><i class="fa fa-chevron-left"></i> Retourner</a>
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
                                        if($acte['TYPE'] == 'm') {
                                            if(substr($acte['CODE'],0,5)=='06188' && substr($acte['CODE'],0,5)=='22500'){
                                                $type_code = 'EAN13';
                                            }else{
                                                $type_code = 'CEGEDIM';
                                            }
                                            $medicament = $MEDICAMENTS->trouver_medicament($type_code,$acte['CODE']);
                                            $designation = $medicament['LIBELLE'];
                                            $acte_code = $medicament['EAN13'];
                                        }else {
                                            $ngap = $ACTESMEDICAUX->trouver_un_acte($acte['CODE']);
                                            $designation = $ngap['LIBELLE'];
                                            $acte_code = $acte['CODE'];
                                        }
                                        $montant = $acte['MONTANT'] * $acte['QUANTITE'];
                                        $part_cmu = round($montant * 0.7);
                                        if($facture['CODE_CSP'] == 'IND') {
                                            $part_ac = $montant - $part_cmu;
                                        }else {
                                            $part_ac = 0;
                                        }
                                        $part_assure = ($montant - ($part_cmu + $part_ac));
                                        ?>
                                        <tr>
                                            <td><?= $acte_code;?></td>
                                            <td><?= $designation;?></td>
                                            <?php
                                            if($facture['TYPE_FEUILLE'] != 'EXP' && $facture['TYPE_FEUILLE'] != 'MED') {
                                                echo '<td>'.date('d/m/Y',strtotime($acte['DEBUT'])).' / '.date('d/m/Y',strtotime($acte['FIN'])).'</td>';
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
                    <?php
                }
            }
        }
    }else{
        session_destroy();
        echo '<script>window.location.href="'.URL.'"</script>';
    }
}
?>