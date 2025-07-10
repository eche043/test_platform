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
                    require_once '../../../Classes/COORDINATIONS.php';
                    $COORDINATIONS = new COORDINATIONS();
                    $centre = $UTILISATEURS->trouver_centre_coordination($user['ID_UTILISATEUR']);
                    if($centre){
                        $etablissements = $COORDINATIONS->lister_ets($centre['CODE_CENTRE']);
                        $nb_ets = count($etablissements);
                        ?>
                        <div class="col">
                            <p class="titres_p"><i class="fa fa-newspaper"></i> Informations Biographiques</p>

                        </div>
                        <hr>
                        <?php
                        if(isset($_POST['num_secu'])) {
                            require_once '../../../Functions/function_conversion_caractere.php';
                            require_once '../../../Classes/COORDINATIONS.php';
                            require_once '../../../Classes/OGD.php';
                            $COORDINATIONS = new COORDINATIONS();
                            $OGD = new OGD();
                            $assure = $COORDINATIONS->trouver_assure($_POST['num_secu']);
                            if(!empty($assure['NUM_SECU'])) {
                                $premiere_cotisation = $COORDINATIONS->trouver_premiere_cotisations($assure['NUM_SECU']);
                                $civilite = $COORDINATIONS->trouver_civilite($assure['CIVILITE']);
                                $genre = $COORDINATIONS->trouver_genre($assure['SEXE']);
                                $nationalite = $COORDINATIONS->trouver_nationalite($assure['NATIONALITE'],NULL);
                                $situation_familiale = $COORDINATIONS->trouver_situation_familiale($assure['SITUATION_FAMILIALE'],NULL);
                                $csp = $COORDINATIONS->trouver_csp($assure['CATEGORIE_PROFESSIONNELLE'],NULL);
                                $qualite_civile = $COORDINATIONS->trouver_qualite_civile($assure['QUALITE_CIVILE'],NULL);
                                $ogd = $OGD->trouver('AFFL',$_POST['code_ogd']);
                                $droits = $COORDINATIONS->consultation_droits_ecnam($assure['NUM_SECU'],date('Y-m-d',time()));
                                $derniere_cotisation = $COORDINATIONS->trouver_derniere_cotisations($assure['NUM_SECU']);
                                if($droits) {
                                    if(isset($droits['nb_dus']) && isset($droits['nb_cotises'])) {
                                        $solde = ($droits['nb_dus'] - $droits['nb_cotises']);
                                    }else {
                                        $solde = NULL;
                                    }
                                }else {
                                    $solde = NULL;
                                }
                                if($assure['PROFESSION']) {
                                    $profession = $COORDINATIONS->trouver_profession($assure['PROFESSION']);
                                }else {
                                    $profession = array(
                                        'CODE' => NULL,
                                        'LIBELLE' => NULL
                                    );
                                }
                                $ogd_prestations = $OGD->trouver('PRST',$assure['CODE_OGD_PRESTATIONS_PROV']);
                                if(!$ogd_prestations) {
                                    $ogd_prestations['LIBELLE'] = null;
                                }
                                ?>
                                <div class="col">
                                    <div class="row">
                                        <div class="col">
                                            <table class="table table-bordered table-striped table-hover table-sm">
                                                <thead class="bg-info">
                                                <tr class="text-white">
                                                    <th colspan="2"><b><?php if($assure['ACTIVE'] == 0){echo '<b class="fa fa-user-alt-slash text-danger"></b>';}else {echo '<b class="fa fa-user success"></b>';} ?> ETAT CIVIL</b></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <td width="120">N° SECU</td>
                                                    <td><b class="text-danger"><?= $assure['NUM_SECU'];?></b></td>
                                                </tr>
                                                <tr>
                                                    <td>CIVILITE</td>
                                                    <td><b><?= strtoupper($civilite['LIBELLE']);?></b></td>
                                                </tr>
                                                <tr>
                                                    <td>NOM</td>
                                                    <td><b><?= $assure['NOM'];?></b></td>
                                                </tr>
                                                <tr>
                                                    <td>NOM PATRONY.</td>
                                                    <td><b><?= $assure['NOM_PATRONYMIQUE'];?></b></td>
                                                </tr>
                                                <tr>
                                                    <td>PRENOM(S)</td>
                                                    <td><b><?= $assure['PRENOM'];?></b></td>
                                                </tr>
                                                <tr>
                                                    <td>SEXE</td>
                                                    <td><b><?= strtoupper($genre['LIBELLE']);?></b></td>
                                                </tr>
                                                <tr>
                                                    <td>DATE DE NAISS.</td>
                                                    <td><b><?= date('d/m/Y',strtotime($assure['DATE_NAISSANCE']));?></b></td>
                                                </tr>
                                                <tr>
                                                    <td>NATIONALITE</td>
                                                    <td><b><?= strtoupper($nationalite['LIBELLE']);?></b></td>
                                                </tr>
                                                <tr>
                                                    <td>SITUATION MAT.</td>
                                                    <td><b><?= strtoupper($situation_familiale['LIBELLE']);?></b></td>
                                                </tr>
                                                </tbody>
                                                <thead class="bg-info">
                                                <tr class="text-white">
                                                    <th colspan="2"><b>INFO NAISSANCE</b></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <td width="120">PAYS</td>
                                                    <td><b><?= $assure['NAISSANCE_PAYS'];?></b></td>
                                                </tr>
                                                <tr>
                                                    <td>DEPARTEMENT</td>
                                                    <td><b><?= $assure['NAISSANCE_NOM_ACHEMINEMENT'];?></b></td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="col">
                                            <table class="table table-bordered table-striped table-hover table-sm">
                                                <thead class="bg-info">
                                                <tr class="text-white">
                                                    <th colspan="2"><b>ADRESSE</b></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <td width="120">PAYS</td>
                                                    <td><b><?= $assure['ADRESSE_PAYS'];?></b></td>
                                                </tr>
                                                <tr>
                                                    <td>ADRESSE 1</td>
                                                    <td><b><?= $assure['AUXILIAIRE_ADRESSE_1'];?></b></td>
                                                </tr>
                                                <tr>
                                                    <td>ADRESSE 2</td>
                                                    <td><b><?= $assure['AUXILIAIRE_ADRESSE_2'];?></b></td>
                                                </tr>
                                                <tr>
                                                    <td>DEPARTEMENT</td>
                                                    <td><b><?= $assure['ADRESSE_NOM_ACHEMINEMENT'];?></b></td>
                                                </tr>
                                                </tbody>
                                            </table>
                                            <table class="table table-bordered table-striped table-hover table-sm">
                                                <thead class="bg-secondary">
                                                <tr class="text-white">
                                                    <th colspan="6"><b>COTISATIONS</b></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php
                                                    $paiements_organismes = $COORDINATIONS->trouver_assure_paiements_ogd($assure['NUM_SECU']);
                                                    $ligne = 1;
                                                    $nb_paiements_ogd = 0;
                                                    foreach ($paiements_organismes as $paiement) {
                                                        $ogd_paiement = $OGD->trouver('AFFL',$paiement['CODE_OGD']);
                                                        ?>
                                                        <tr>
                                                            <td align="right"><?= $ligne;?></td>
                                                            <td colspan="2">Cotisations Oganisme</td>
                                                            <td align="right"><b><?= number_format(($paiement['NOMBRE'] * 1000),'0','',' ');?></b></td>
                                                            <td><?= $ogd_paiement['LIBELLE'];?></td>
                                                            <td width="5"><?php if(!$paiement['STATUT']) {echo '<b class="text-warning fa fa-clock<"></b>';}else{echo '<b class="text-success fa fa-check"></b>';} ?></td>
                                                        </tr>
                                                        <?php
                                                        $nb_paiements_ogd = $nb_paiements_ogd + intval($paiement['NOMBRE'] * 1000);
                                                        $ligne++;
                                                    }


                                                    $paiements_electroniques = $COORDINATIONS->trouver_assure_paiements_electroniques($assure['NUM_SECU'],1);
                                                    $nb_paiements_electroniques = 0;
                                                    foreach ($paiements_electroniques as $paiement) {
                                                        ?>
                                                        <tr>
                                                            <td align="right"><?= $ligne;?></td>
                                                            <td><?= date('d/m/Y H:i',strtotime($paiement['DATE_REG']));?></td>
                                                            <td align="right"><?= $paiement['NUM_TRANSACTION'];?></td>
                                                            <td align="right"><b><?= number_format($paiement['ASSURE_MONTANT'],'0','',' ');?></b></td>
                                                            <td><?= $paiement['WALLET'];?></td>
                                                            <td width="5"><?php if($paiement['STATUT_TRAITEMENT'] == 0) {echo '<b class="text-warning fa fa-clock<"></b>';}else{echo '<b class="text-success fa fa-check"></b>';} ?></td>
                                                        </tr>
                                                        <?php
                                                        $nb_paiements_electroniques = $nb_paiements_electroniques + intval($paiement['ASSURE_MONTANT']);
                                                        $ligne++;
                                                    }
                                                ?>
                                                </tbody>
                                                <tfoot class="bg-secondary text-white">
                                                <tr>
                                                    <th colspan="3">TOTAL</th>
                                                    <th style="text-align: right"><?= number_format(intval($nb_paiements_ogd + $nb_paiements_electroniques),'0','',' ');?></th>
                                                </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                        <div class="col">
                                            <div class="col">
                                                <table class="table table-bordered table-striped table-hover table-sm">
                                                    <thead class="bg-info">
                                                    <tr class="text-white">
                                                        <th colspan="2"><b>TYPE ASSURE</b></th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <tr>
                                                        <td width="120">DATE DELIVRANCE CARTE</td>
                                                        <td><b class="text-success"><?php if(!empty($assure['DATE_DELIVRANCE_CARTE'])) {echo date('d/m/Y',strtotime($assure['DATE_DELIVRANCE_CARTE']));} ?></b></td>
                                                    </tr>
                                                    <tr>
                                                        <td>QUALITE CIVILE</td>
                                                        <td><b><?= strtoupper($qualite_civile['LIBELLE']);?></b></td>
                                                    </tr>
                                                    <tr>
                                                        <td>CSP</td>
                                                        <td><b><?= strtoupper($csp['LIBELLE']);?></b></td>
                                                    </tr>
                                                    </tbody>
                                                    <?php
                                                        if(!empty($collectivite['CODE'])) {
                                                            ?>
                                                            <thead class="bg-info">
                                                            <tr class="text-white">
                                                                <th colspan="2"><b>COLLECTIVITE</b></th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            <tr>
                                                                <td>PROFESSION</td>
                                                                <td><b><?= strtoupper(conversionCaractere($profession['LIBELLE']));?></b></td>
                                                            </tr>
                                                            <tr>
                                                                <td width="120">RAISON SOCIALE</td>
<!--                                                                <td><a href="--><?//= URL.'affiliation/ogd/collectivite.php?code-ogd='.$ogd['CODE'].'&code-collectivite='.$collectivite['CODE'];?><!--" target="_blank"><b>--><?//= $collectivite['RAISON_SOCIALE'];?><!--</b></a></td>-->
                                                                <td><a href="#" target="_blank"><b><?= $collectivite['RAISON_SOCIALE'];?></b></a></td>
                                                            </tr>
                                                            <tr>
                                                                <td>SERVICE</td>
                                                                <td><b><?= $assure['COLLECTIVITE_SERVICE'];?></b></td>
                                                            </tr>
                                                            <tr>
                                                                <td>FONCTION</td>
                                                                <td><b><?= $assure['COLLECTIVITE_FONCTION'];?></b></td>
                                                            </tr>
                                                            </tbody>
                                                            <?php
                                                        }
                                                    ?>
                                                    <thead class="bg-info">
                                                    <tr class="text-white">
                                                        <th colspan="2"><b>PRESTATIONS</b></th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <tr>
                                                        <td width="120">OGD</td>
                                                        <td><b><?= $ogd_prestations['LIBELLE'];?></b></td>
                                                    </tr>
                                                    <tr>
                                                        <td>DROITS</td>
                                                        <td>
                                                            <?php
                                                                $consultation_droits = $COORDINATIONS->consultation_droits_ecnam($assure['NUM_SECU'],date('Y-m-d',time()));
                                                                if($consultation_droits['success'] == false) {
                                                                    echo '<b style="display: block; width: 100%" class="bg-warning text-danger">'.$consultation_droits['message'].'</b>';
                                                                }else {
                                                                    if($consultation_droits['droitsOuverts'] == true) {
                                                                        echo '<b style="display: block; width: 100%" class="bg-success text-white">OUVERTS</b>';
                                                                    }else {
                                                                        echo '<b style="display: block; width: 100%" class="bg-danger text-white">FERMES</b>';
                                                                    }
                                                                }
                                                            ?>
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col">
                                        <?php
                                            if(($ogd['CODE'] == '03016000' && $solde != 0)) {
                                                echo '<p align="center"><button type="button" id="'.$ogd['CODE'].'_'.$assure['NUM_SECU'].'_'.$solde.'" class="btn btn-danger btn-sm btn_maj_cotisations"><i class="fa fa-eject"></i> Mettre à jour les cotisations</button></p>';
                                            }
                                        ?>
                                        <table class="table table-bordered table-sm">
                                            <thead class="bg-info">
                                            <tr class="text-white">
                                                <th colspan="13"><b>VENTILATIONS</b></th>
                                            </tr>
                                            </thead>
                                            <thead class="bg-info">
                                            <tr>
                                                <th>ANNEE</th>
                                                <?php
                                                    require_once '../../../Classes/CALENDRIER.php';
                                                    $CALENDRIER = new CALENDRIER();
                                                    for ($mois = 1; $mois <= 12; $mois++) {
                                                        echo '<th width="80">'.$CALENDRIER->trouver_mois(str_pad($mois,'2','0', STR_PAD_LEFT)).'</th>';
                                                    }
                                                ?>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                                if($derniere_cotisation) {
                                                    $annee_cotisation = date('Y',strtotime($derniere_cotisation['DATE_DEBUT']));
                                                }else {
                                                    $annee_cotisation = date('Y',time());
                                                }
                                                for ($annee = $annee_cotisation; $annee >= date('Y',strtotime($assure['DATE_AFFILIATION']));$annee--) {
                                                    ?>
                                                    <tr>
                                                        <td class="h3 text-danger"><b><?= $annee;?></b></td>
                                                        <?php
                                                            for ($mois_1 = 1; $mois_1 <= 12; $mois_1++) {
                                                                $mois_chargement = $COORDINATIONS->trouver_mois_cotisations($assure['NUM_SECU'],$annee,str_pad($mois_1,'2','0', STR_PAD_LEFT));
                                                                ?>
                                                                <td>
                                                                    <button type="button" class="btn btn-block btn-sm
                                                                                <?php
                                                                        if($mois_chargement['MOIS'] == $mois_1) {
                                                                            if($mois_chargement['STATUT'] == 'CRE') {
                                                                                echo 'btn-warning';
                                                                            }else {
                                                                                echo 'btn-success';
                                                                            }
                                                                        }else{
                                                                            echo 'btn-secondary';
                                                                        } ?> button_ventaltion">
                                                                        <?php
                                                                            if(empty($mois_chargement['STATUT'])){
                                                                                echo '&nbsp;';
                                                                            }else {
                                                                                echo $mois_chargement['STATUT'];
                                                                            } ?></button>
                                                                </td>
                                                                <?php
                                                            }
                                                        ?>
                                                    </tr>
                                                    <?php
                                                }
                                            ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <?php
                            }
                        }
                        ?>

                        <script type="text/javascript" src="<?= JS.'page_centre_coordination.js'?>"></script>
                        <?php
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
        $('#dataTable').DataTable();
    });

</script>