<?php
require_once '../../../Classes/UTILISATEURS.php';
if(URL) {
    echo '<script>window.location.href="'.URL.'"</script>';
}else {
require_once '../../../Classes/ASSURES.php';

if(isset($_SESSION['ECMU_USER_ID']) || !empty($_SESSION['ECMU_USER_ID'])) {
$UTILISATEURS = new UTILISATEURS();
$ASSURES = new ASSURES();

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
$num_secu = $user['NUM_SECU'];
require_once '../../../Classes/COLLECTIVITES.php';
require_once '../../../Classes/ATTESTATIONSDROITS.php';
require_once '../../../Classes/ETABLISSEMENTSSANTE.php';
require_once '../../../Classes/OGD.php';

$ATTESTATIONSDROITS = new ATTESTATIONSDROITS();
$ETABLISSEMENTSSANTE = new ETABLISSEMENTSSANTE();
$COLLECTIVITES = new COLLECTIVITES();
$OGD = new OGD();

$assure = $ASSURES->trouver($num_secu);
$ayants_droits = $ASSURES->liste_ayants_droits($assure['NUM_SECU'],$assure['NUM_SECU']);
$genre = $ASSURES->trouver_assure_genre($assure['SEXE']);
$civilite = $ASSURES->trouver_assure_civilite($assure['CIVILITE']);
$nationalite = $ASSURES->trouver_nationalite($assure['NATIONALITE']);
$situation_familiale = $ASSURES->trouver_situation_matrimoniale($assure['SITUATION_FAMILIALE']);
$naissance_pays = $ASSURES->trouver_pays($assure['NAISSANCE_PAYS']);
$coordonnees = $ASSURES->trouver_coordonnees($num_secu);
$identifiants = $ASSURES->trouver_identifiants($num_secu);
$adresse_pays = $ASSURES->trouver_pays($assure['ADRESSE_PAYS']);
$profession = $ASSURES->trouver_assure_profession($assure['PROFESSION']);
$collectivite = $COLLECTIVITES->trouver($assure['COLLECTIVITE_EMPLOYEUR']);
$qualite_civile = $ASSURES->trouver_qualite_civile($assure['QUALITE_CIVILE']);
$ogd = $OGD->trouver_ogd_cotisation($assure['CODE_OGD_COTISATIONS']);
$cmr = $ETABLISSEMENTSSANTE->trouver($assure['EXECUTANT_REFERENT']);/*
                $photo_profil = $utilisateur->trouver_photo_utilisateur($num_secu);*/
$assure['NOM'].' '.$assure['PRENOM'];


$init_nom = substr($assure['NOM'],0,1);
$init_prenom = substr($assure['PRENOM'],0,1);
?>
<div class="col">
    <p class="titres_p">Mes Infos</p>
    <div class="row ">
        <div class="col-sm-2" style="padding-right: 0">
            <div class="" style="height: 200px; width: 100%; text-align: center;">   <? echo $assure['photo'] ?>
                <a href="#" data-toggle="modal" data-target="#bd-example-modal-lg">
                    <img style="width: 100%;height: 100%;border-radius: 100px;" src='<?php if($user['PHOTO'] == ''){echo URL.'_publics/images/avatar.png';}else{echo URL.'_publics/images/image_profil/'.$num_secu.'/'.$user['PHOTO'];}?>' class="img-fluid img-thumbnail" height="100%" alt="<?=$init_nom.''.$init_prenom;?>">
                </a>
            </div>
        </div>
        <div class="col-sm-10">
            <div class="row">
                <div class="col-sm-6">
                    <table class="table table-bordered table-hover table-sm">
                        <tr>
                            <td colspan="2" class="alert-dark" align="center"><b>ETAT CIVIL</b></td>
                        </tr>
                        <tr>
                            <td class="table_left_title" width="200">Numéro sécu</td>
                            <td><b style="color: #FF0000"><?= $assure['NUM_SECU'];?></b></td>
                        </tr>
                        <tr>
                            <td class="table_left_title">Civilité</td>
                            <td><?= '<b>'.$civilite['LIBELLE'].'</b>';?></td>
                        </tr>
                        <tr>
                            <td class="table_left_title">Nom</td>
                            <td><b><?= $assure['NOM'];?></b></td>
                        </tr>
                        <tr>
                            <td class="table_left_title">Nom patronymique</td>
                            <td><b><?= $assure['NOM_PATRONYMIQUE'];?></b></td>
                        </tr>
                        <tr>
                            <td class="table_left_title">Prénom(s)</td>
                            <td><b><?= $assure['PRENOM'];?></b></td>
                        </tr>
                        <tr>
                            <td class="table_left_title">Sexe</td>
                            <td><?= '<b>'.$genre['LIBELLE'].'</b>'; ?></td>
                        </tr>
                        <tr>
                            <td class="table_left_title">Date de naissance</td>
                            <td><b><?= date('d/m/Y',strtotime($assure['DATE_NAISSANCE']));
                                    ?></b></td>
                        </tr>
                        <tr>
                            <td class="table_left_title">Nationalité</td>
                            <td><?= '<b>'.$nationalite['LIBELLE'].'</b>';?></td>
                        </tr>
                        <tr>
                            <td class="table_left_title">Situation familiale</td>
                            <td><?= '<b>'.$situation_familiale['LIBELLE'].'</b>';?></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="alert-dark" align="center"><b>INFOS NAISSANCE</b></td>
                        </tr>
                        <tr>
                            <td class="table_left_title" width="200">Pays</td>
                            <td><?= '<b>'.$naissance_pays['LIBELLE'].'</b>';?> </td>
                        </tr>
                        <tr>
                            <td class="table_left_title">Lieu</td>
                            <td><b><?= $assure['NAISSANCE_NOM_ACHEMINEMENT'];?></b></td>
                        </tr>
                        <tr>
                            <td class="table_left_title">Secteur</td>
                            <td><b><?= $assure['NAISSANCE_SECTEUR_NAISSANCE'];?></b></td>
                        </tr>
                        <tr>
                            <td class="table_left_title">Code postal</td>
                            <td><b><?= $assure['NAISSANCE_CODE_POSTAL'];?></b></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="alert-dark" align="center"><b>ADRESSE</b></td>
                        </tr>
                        <tr>
                            <td class="table_left_title">Pays</td>
                            <td><?= '<b>'.$adresse_pays['LIBELLE'].'</b>';?></td>
                        </tr>
                        <tr>
                            <td class="table_left_title">Auxiliaire adresse 1</td>
                            <td><b><?= $assure['AUXILIAIRE_ADRESSE_1'];?></b></td>
                        </tr>
                        <tr>
                            <td class="table_left_title">Auxiliaire adresse 2</td>
                            <td><b><?= $assure['AUXILIAIRE_ADRESSE_2'];?></b></td>
                        </tr>
                        <tr>
                            <td class="table_left_title">Nom acheminement</td>
                            <td><b><?= $assure['ADRESSE_NOM_ACHEMINEMENT'];?></b></td>
                        </tr>
                        <tr>
                            <td class="table_left_title">Code postal</td>
                            <td><b><?= $assure['ADRESSE_CODE_POSTAL'];?></b></td>
                        </tr>
                    </table>
                </div>
                <div class="col-sm-6">
                    <table class="table table-bordered table-hover table-sm">
                        <tr>
                            <td colspan="2" class="alert-dark" align="center" width="200"><b>COORDONNEES</b></td>
                        </tr>
                        <?php
                        foreach ($coordonnees as $coordonnee) {
                            ?>
                            <tr>
                                <td class="table_left_title" width="200">
                                    <?php
                                    if($coordonnee['TYPE_COORD'] == 'MOBPER') {
                                        $type_coordonnee = 'Mobile';
                                    }elseif($coordonnee['TYPE_COORD'] == 'TELFIX') {
                                        $type_coordonnee = 'Téléphone';
                                    }elseif($coordonnee['TYPE_COORD'] == 'MELPER') {
                                        $type_coordonnee = 'Adresse Email';
                                    }
                                    echo $type_coordonnee;
                                    ?>
                                </td>
                                <td><b><?= $coordonnee['VALEUR'];?></b></td>
                            </tr>
                            <?php
                        }
                        ?>
                        <tr>
                            <td colspan="2" class="alert-dark" align="center"><b>COLLECTIVITE</b></td>
                        </tr>
                        <tr>
                            <td class="table_left_title">Employeur</td>
                            <td>
                                <b>
                                    <?=$collectivite['RAISON_SOCIALE'];?>
                                </b>
                            </td>
                        </tr>
                        <tr>
                            <td class="table_left_title">Service</td>
                            <td><b><?= $assure['COLLECTIVITE_SERVICE'];?></b></td>
                        </tr>
                        <tr>
                            <td class="table_left_title">Profession</td>
                            <td><?= '<b>'.$profession['LIBELLE'].'</b>';?></td>
                        </tr>
                        <tr>
                            <td class="table_left_title">Fonction</td>
                            <td><b><?= $assure['COLLECTIVITE_FONCTION'];?></b></td>
                        </tr>
                        <tr>
                            <td class="table_left_title">Matricule</td>
                            <td><b><?= $assure['COLLECTIVITE_MATRICULE_SALARIE'];?></b></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="alert-dark" align="center"><b>CENTRE MEDICAL REFERENT</b></td>
                        </tr>
                        <tr>
                            <td class="table_left_title">CMR</td>
                            <td><b><?= $cmr['INP'].' - '.$cmr['RAISON_SOCIALE'];?></b></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="alert-dark" align="center"><b>IDENTIFIANTS</b></td>
                        </tr>
                        <?php
                        foreach ($identifiants as $identifiant) {
                            ?>
                            <tr>
                                <td class="table_left_title">
                                    <?php
                                    $type_identifiant = $ASSURES->trouver_type_identifiant($identifiant['TYPE_IDENTIFIANT']);
                                    echo $type_identifiant['LIBELLE'];
                                    ?>
                                </td>
                                <td><b><?= $identifiant['NUMERO'];?></b></td>
                            </tr>
                            <?php
                        }
                        ?>
                        <tr>
                            <td colspan="2" class="alert-dark" align="center"><b>COTISATIONS</b></td>
                        </tr>
                        <tr>
                            <td class="table_left_title">OGD</td>
                            <td><b><?=$ogd['LIBELLE'];?></b></td>
                        </tr>
                        <tr>
                            <td class="table_left_title">Qualité civile</td>
                            <td><?= '<b>'.$qualite_civile['LIBELLE'].'</b>';?></td>
                        </tr>
                        <tr>
                            <td class="table_left_title">Date affiliation</td>
                            <td>
                                <b>
                                    <?php
                                    if($assure['DATE_AFFILIATION'] != '0000-00-00') {
                                        echo date('d/m/Y',strtotime($assure['DATE_AFFILIATION']));
                                    }else {
                                        echo '';
                                    }
                                    ?>
                                </b>
                            </td>
                        </tr>
                    </table>
                </div>
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
