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
                if($centre){
                    if(isset($_POST['code_ets']) && !empty($_POST['code_ets']))
                    {
                        $ets = $COORDINATIONS->trouver_ets($centre['CODE_CENTRE'],$_POST['code_ets']);
                        if($ets)
                        {
                            $terminaux = $COORDINATIONS->lister_terminaux($ets['CODE_ETS']);
                            $nb_terminaux = count($terminaux);
                                ?>
                                    <div class="col">
                                    <p class="titres_p">
                                        <i class="fa fa-newspaper"></i> Ajouter un Nouveau Terminal  : <?= $ets['RAISON_SOCIALE'] ?></p>
                                    <p>
                                        <a href="<?= URL.'centre-coordination/terminaux-biometriques-historique.php?code-ets='.$ets['CODE_ETS'];?>" class="btn btn-sm btn-danger"><i class="fa fa-eye"></i> Historique des terminaux</a>
                                    </p>
                                    <hr />
                                        <p id="resultat_edition_terminal"></p>
                                        <form id="form_edition_terminal">
                                            <div class="form-group row">
                                                <label class="col-form-label col-sm-2">Type</label>
                                                <div class="col-sm-3">
                                                    <select class="custom-select custom-select-sm" id="terminal_type_input" required>
                                                        <option value="">Sélectionner le type</option>
                                                        <option value="CredenceOne" <?php if(isset($_POST['id']) && $terminal['TERMINAL_TYPE'] == 'CredenceOne'){echo 'selected';} ?>>CredenceOne</option>
                                                        <option value="Famoco" <?php if(isset($_POST['id']) && $terminal['TERMINAL_TYPE'] == 'Famoco'){echo 'selected';} ?>>Famoco</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-form-label col-sm-2">Numéro SIM</label>
                                                <div class="col-sm-3">
                                                    <input type="text" class="form-control form-control-sm" id="terminal_numero_telephone_input" autocomplete="off" value="<?php if(isset($_POST['id'])){echo trim($terminal['NUMERO_TELEPHONE']);} ?>" placeholder="N° Téléphone" required/>
                                                </div>
                                                <label class="col-form-label col-sm-2">Code IMEI</label>
                                                <div class="col-sm-5">
                                                    <input type="text" class="form-control form-control-sm" id="terminal_code_imei_input" autocomplete="off" value="<?php if(isset($_POST['id'])){echo trim($terminal['TERMINAL']);} ?>" <?php if(isset($_POST['id'])){echo 'readonly';} ?> placeholder="IMEI" required/>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-form-label col-sm-2">Etablissement</label>
                                                <div class="col-sm-3">
                                                    <input type="text" class="form-control form-control-sm" maxlength="9" id="terminal_code_ets_input" autocomplete="off" value="<?= $ets['CODE_ETS'] ?>" placeholder="Code ETS" readonly/>
                                                </div>
                                                <div class="col-sm-7">
                                                    <input type="text" class="form-control form-control-sm" id="terminal_libelle_ets_input" autocomplete="off" value="<?= $ets['RAISON_SOCIALE'] ?>" placeholder="Raison sociale" disabled/>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="" class="col-sm-2 col-form-label"></label>
                                                <div class="col-sm-4">
                                                    <button type="submit" id="btn_valider" class="btn btn-primary btn-sm"><i class="fa fa-save"></i> Enregistrer</button>
                                                    <input type="hidden" id="id_terminal_input" value="<?php if(isset($_POST['id'])){echo $terminal['ID'];}?>" />
                                                    <a href="terminaux-biometriques.php?code-ets=<?= $ets['CODE_ETS'] ?>" class="btn btn-sm btn-light">Retourner</a>
                                                </div>
                                            </div>
                                        </form>

                                        <script type="application/javascript" src="<?= JS.'page_centre_coordination_terminaux.js';?>"></script>

                                </div>
                                <?php
                        }else{
                            echo '<script>window.location.href="'.URL.'centre-coordination/"</script>';
                        }
                    }

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
        $('.dataTable').DataTable();
    });

</script>
