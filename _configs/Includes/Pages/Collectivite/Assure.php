<?php
$num_secu = $_POST['num_secu'];
require_once "../../../Functions/function_convert_special_characters_to_normal.php";
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
        }else {
            if (in_array('PS', $modules)) {
                require_once '../../../Classes/ASSURES.php';
                require_once '../../../Classes/OGD.php';
                require_once '../../../Classes/COLLECTIVITES.php';
                $COLLECTIVITES = new COLLECTIVITES();
                $ASSURES = new ASSURES();
                $OGD = new OGD();
                $assure = $ASSURES->trouver_assure_autre_payeur($num_secu);
                 $csp = $ASSURES->trouver_csp($assure['CATEGORIE_PROFESSIONNELLE']);
                $ogd = $OGD->trouver_ogd_cotisation($assure['CODE_OGD_COTISATIONS']);
                $collectivite = $COLLECTIVITES->trouver($assure['COLLECTIVITE_EMPLOYEUR']);

                $profession = $ASSURES->trouver_assure_profession($assure['PROFESSION']);
                $nb_assures = count($assure);
                if ($nb_assures == 0) {
                    echo '<p align="center" class="text-danger">Le numéro sécu saisi est incorrect.</p>';
                } else {
                    ?>
                    <div class="col"><br>
                        <p class="titres_p"><i class="fa fa-user"></i> <?=$assure['NOM'].' '.$assure['PRENOM'];?></p>
                        <div class="row">
                            <div class="col-sm-6">
                                <button type="button" class="btn btn-success btn-sm" data-toggle="collapse"
                                        href="#collapsePaiement" role="button" aria-expanded="false"
                                        aria-controls="collapseExample"><i class="fa fa-credit-card"></i> Effectuer un
                                    paiement
                                </button>
                                <button type="button" class="btn btn-warning btn-sm" hidden><i
                                        class="fa fa-american-sign-language-interpreting"></i> Les ayants droits
                                </button>
                                <a href="<?= URL . 'ENT/historique.php?num-secu=' . $assure['num_secu']; ?>"
                                   class="btn btn-danger btn-sm"><i class="fa fa-list"></i> Historique</a>
                            </div>
                        </div><br/><br/>
                        <div class="row">
                            <div class="container">
                                <div class="row justify-content-md-center">
                                    <div class="col col-sm-4">
                                        <div class="collapse" id="collapsePaiement">
                                            <div class="card card-body">
                                                <p align="center" id="resultats_p"></p>
                                                <form id="form_payer_cotisation">
                                                    <div class="form-group row">
                                                        <div class="col-sm-12">
                                                            <select class="form-control form-control-sm" id="type_input"
                                                                    required>
                                                                <option value="">Type</option>
                                                                <option value="I">Individuel</option>
                                                                <option value="F">Familial</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-sm-12">
                                                            <select class="form-control form-control-sm" id="montant_input"
                                                                    required>
                                                                <option value="">Montant</option>
                                                                <?php
                                                                for ($i = 1000; $i <= 24000; $i = $i + 1000) {
                                                                    echo '<option value="' . $i . '">' . number_format($i, '0', '', ' ') . '</option>';
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-sm-6">
                                                            <input type="hidden" value="<?= $assure['NUM_SECU']; ?>"
                                                                   id="num_secu_input"/>
                                                            <input type="hidden" value="<?= $assure['COLLECTIVITE_EMPLOYEUR']; ?>" id="code_collectivite_assure_input"/>
                                                            <input type="hidden" value="<?= $user['CODE_COLLECTIVITE']; ?>" id="code_collectivite_input"/>
                                                            <button type="submit" class="btn btn-sm btn-success btn-block">
                                                                Valider
                                                            </button>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <button type="reset" class="btn btn-sm btn-dark btn-block"
                                                                    data-toggle="collapse" href="#collapsePaiement"
                                                                    role="button" aria-expanded="false"
                                                                    aria-controls="collapseExample">Annuler
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                            <hr/>
                                        </div>
                                    </div>
                                </div>
                                <br/>
                            </div>
                            <div class="col-sm-5">
                                <div class="card">
                                    <div class="card-header bg-primary text-white">IDENTIFICATION</div>
                                    <div class="card-body">
                                        <table class="table table-bordered table-hover table-sm table-striped">
                                            <tr>
                                                <td width="170">N° Sécu</td>
                                                <td><b><?= $assure['NUM_SECU']; ?></b></td>
                                            </tr>
                                            <tr>
                                                <td>Civilité</td>
                                                <td><b><?php
                                                        $civilite = $ASSURES->trouver_assure_civilite($assure['CIVILITE']);
                                                        echo strtoupper(conversionCaractere($civilite['LIBELLE']));?></b>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Nom</td>
                                                <td><b><?= $assure['NOM']; ?></b></td>
                                            </tr>
                                            <tr>
                                                <td>Nom patronymique</td>
                                                <td><b><?= $assure['NOM_PATRONYMIQUE']; ?></b></td>
                                            </tr>
                                            <tr>
                                                <td>Prénom(s)</td>
                                                <td><b><?= $assure['PRENOM']; ?></b></td>
                                            </tr>
                                            <tr>
                                                <td>Date naissance</td>
                                                <td><b><?= date('d/m/Y', strtotime($assure['DATE_NAISSANCE'])); ?></b></td>
                                            </tr>
                                            <tr>
                                                <td>Genre</td>
                                                <td><b>
                                                        <?php
                                                        $sexe = $ASSURES->trouver_assure_genre($assure['SEXE']);
                                                        echo strtoupper(conversionCaractere($sexe['LIBELLE']));
                                                        ?></b></td>
                                            </tr>
                                            <tr>
                                                <td>Catégorie professionnelle</td>
                                                <td><b><?= strtoupper(conversionCaractere($csp['LIBELLE'])); ?></b></td>
                                            </tr>
                                            <tr>
                                                <td>Profession</td>
                                                <td><b><?= strtoupper(conversionCaractere($profession['LIBELLE'])); ?></b>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>OGD Prestations</td>
                                                <td><b><?= strtoupper(conversionCaractere($ogd['LIBELLE'])); ?></b></td>
                                            </tr>
                                            <tr>
                                                <td>Collectivité</td>
                                                <td>
                                                    <b><?= strtoupper(conversionCaractere($collectivite['RAISON_SOCIALE'])); ?></b>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-7">
                                <div class="card">
                                    <div class="card-header">DERNIERS PAIEMENTS</div>
                                    <div class="card-body">
                                        <div id="derniers_paiements_div"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <script type="text/javascript" src="<?= JS.'page_collectivite.js'?>"></script>
                <?php }
            }
        }
    }
}else{
    session_destroy();
    echo '<script>window.location.href="'.URL.'"</script>';
}

?>