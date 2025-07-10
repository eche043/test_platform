<?php
require_once '../../Classes/DUPLICATA.php';
$DUPLICATA = new DUPLICATA();

?>
<link rel="stylesheet" type="text/css" href="<?= CSS.'jquery-ui.css';?>" />

<div class="container">
    <!--<div class="col-sm-12">
        <div class="form-group" align="center">
            <button class="btn btn-primary btn-sm col-sm-5 my-2" type="submit" id="btn_suivi_demande"><b>Suivi de demande</b></button>
            <button class="btn btn-primary btn-sm col-sm-5 my-2" type="submit" id="btn_nouvelle_demande"><b>Nouvelle demande</b></button>
        </div>
    </div>-->

    <div class="col-sm-12 div_duplicata">
        <p class="text-center"><img src="<?= IMAGES ?>logo_cnam.png" width="100" height="100" alt=""></p>
        <div id="div_suivi">
            <p class="h5 text-center">Rechercher une demande de réédition de carte</p><hr />
            <div class="row justify-content-sm-center">
                <div class="col-sm-4" id="div_recherche_demande">
                    <form id="form_recherche_demande">
                        <div class="form-group">
                            <label for="num_secu_suivi_input">Numéro de sécurité sociale</label>
                            <input type="text" class="form-control form-control-sm" maxlength="13" id="num_secu_suivi_input" placeholder="Numéro de sécurité sociale" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label for="numero_suivi_input">Numéro de suivi</label>
                            <input type="text" class="form-control form-control-sm" maxlength="25" id="numero_suivi_input" placeholder="Num de suivi" autocomplete="off">
                        </div>
                        <button type="submit" id="btn_recherche_demande" class="btn btn-primary btn-sm btn-block"><i class="fa fa-search"></i> Rechercher</button>
                        <button type="button" id="btn_nouvelle_demande" class="btn btn-danger btn-sm btn-block"><i class="fa fa-plus"></i> Nouvelle demande</button>
                    </form><hr />
                </div>
                <div class="col-sm-12" id="div_resultats_suivi"></div>
            </div>
        </div>
        <div id="div_nouveau">
            <p class="h5 text-center">Nouvelle demande de réédition de cartes</p><hr />
            <div class="row justify-content-sm-center">
                <div class="col-sm-8">
                    <p id="resultat_form_editer_demande" class="text-center"></p>
                    <form id="form_editer_demande" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="image_scan_piece_input">Motif de la demande <small class="text-danger"><b>*</b></small></label>
                                    <select class="form-control form-control-sm" id="motif_demande_input" name="type_piece">
                                        <option value="">Choisir le motif de la demande</option>
                                        <?php
                                        $motifs= $DUPLICATA->lister_motifs();
                                        foreach ($motifs AS $motif){
                                            ?>
                                            <option value="<?= $motif['MOTIF_CODE'] ?>"><?= $motif['MOTIF_LIBELLE'] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="form-group" id="div_type_piece">
                                    <label for="type_piece_input">Type de pièce <small class="text-danger"><b><i>*</i></b></small></label>
                                    <select class="form-control form-control-sm" id="type_piece_input" name="type_piece" required>
                                        <option value="" selected>Choisir le type de pièce</option>
                                        <?php
                                        $type_documents = $DUPLICATA->lister_type_document();
                                        foreach ($type_documents AS $type_document){
                                            ?>
                                            <option value="<?= $type_document["CODE"] ?>"><?= $type_document["LIBELLE"] ?></option>
                                            <?php
                                        }  ?>
                                    </select>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <label for="num_piece_input">Numéro de pièce <small class="text-danger"><b><i>*</i></b></small></label>
                                            <input type="text" class="form-control form-control-sm" id="num_piece_input" aria-describedby="" placeholder="Numéro pièce" autocomplete="off" required>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="date_fin_validite_piece_input">Date fin validite piece <small class="text-danger"><b><i>*</i></b></small></label>
                                            <input type="text" class="datepicker form-control form-control-sm " id="date_fin_validite_piece_input" name="date_fin_validite_piece_input" autocomplete="off" value="" placeholder="Validite pièce" autocomplete="off" required />
                                        </div>
                                    </div>

                                </div>

                                <div class="form-group" id="div_cni_passport">
                                    <label for="image_scan_piece_input">Scan pièce (CNI/passport) <small class="text-danger"><b><i>(.jpeg,.jpg,.png,.pdf)*</i></b></small></label>
                                    <input type="file" class="form-control form-control-sm" id="image_scan_piece_input" name="image_scan_piece_input" accept=".png, .jpg, .jpeg">
                                </div>
                                <div class="form-group" id="declaration_perte_bloc">
                                    <label for="declaration_perte_input">Déclaration de perte <small class="text-danger"><b><i>(.jpeg,.jpg,.png,.pdf)*</i></b></small></label>
                                    <input type="file" class="form-control form-control-sm" id="declaration_perte_input" name="declaration_perte_input">
                                </div>
                                <div class="form-group" id="carte_abimee_bloc">
                                    <label for="carte_abimee_input">Carte abimée <small class="text-danger"><b><i>(.jpeg,.jpg,.png,.pdf)*</i></b></small></label>
                                    <input type="file" class="form-control form-control-sm" id="carte_abimee_input" name="carte_abimee_input">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="num_secu_input">Numéro de sécurité sociale <small class="text-danger"><b><i>*</i></b></small></label>
                                    <input type="text" class="form-control form-control-sm" maxlength="13" id="num_secu_input" aria-describedby="" placeholder="Numéro de sécurité sociale" autocomplete="off" required>
                                </div>
                                <div class="form-group">
                                    <label for="nom_input">Nom <small class="text-danger"><b><i>*</i></b></small></label>
                                    <input type="text" class="form-control form-control-sm" id="nom_input" aria-describedby="" placeholder="Nom" autocomplete="off" required>
                                </div>
                                <div class="form-group">
                                    <label for="prenom_input">Prénom(s) <small class="text-danger"><b><i>*</i></b></small></label>
                                    <input type="text" class="form-control form-control-sm" id="prenom_input" aria-describedby="" placeholder="Prénom(s)" autocomplete="off" required>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <label for="date_naiss_input">Date de naissance <small class="text-danger"><b><i>*</i></b></small></label>
                                            <input type="text" class="datepicker_date_naiss form-control form-control-sm" id="date_naiss_input" aria-describedby="" placeholder="Date de naissance" autocomplete="off" required>
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="telephone_input">Téléphone <small class="text-danger"><b><i>*</i></b></small></label>
                                            <input type="text" class="form-control form-control-sm" id="telephone_input" maxlength="10" aria-describedby="" placeholder="Téléphone" autocomplete="off" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr/>
                        <div class="form-group row">
                            <div class="col-sm-6">
                                <div class="row">
                                    <div class="col-sm">
                                        <button class="btn btn-success btn-block btn-sm" type="submit" id="btn_form_editer_administrateur"><i class="fa fa-save"></i> Enregistrer</button>
                                    </div>
                                    <div class="col-sm">
                                        <button class="btn btn-primary btn-block btn-sm" type="button" id="btn_form_retourner"><i class="fa fa-arrow-circle-left"></i> Retour au suivi</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="application/javascript" src="<?= JS.'page_duplicata.js';?>"></script>
<script type="application/javascript" src="<?= JS.'jquery-ui.js';?>"></script>





