<?php
    require_once '../../../Classes/UTILISATEURS.php';
    if(isset($_SESSION['ECMU_USER_ID'])) {
    echo '<script>window.location.href="'.URL.'"</script>';
}else {
        require_once '../../../Classes/RECRUTEMENT.php';
        $RECRUTEMENT = new RECRUTEMENT();
        $id_compte = $_POST['id_agac'];
        $telephone = $RECRUTEMENT->trouver_telephone_compte($id_compte);
        $centres = $RECRUTEMENT->trouver_centre_agac($telephone['NUMERO_TELEPHONE']);
        //var_dump($centres);
        if($centres){
          $raison_sociales = $centres["STRUCTURE_SANITAIRE"];
        }
        $nationalites  = $RECRUTEMENT->liste_nationalite();
        $sexes = $RECRUTEMENT->lister_sexe();
        $documents = $RECRUTEMENT->lister_type_piece();
        $statut_matrimoniales = $RECRUTEMENT->lister_situation_familiale();
        $professions = $RECRUTEMENT->liste_profession();
        $civilites = $RECRUTEMENT->lister_civilite();

        ?>
<div class="col">
    <div class="row justify-content-md-center">
        <div class="col col-sm-10" id="div_login">
            <div class="col-sm-12 mb-4">
                <nav class="nav nav-pills flex-column flex-sm-row">
                    <span class="flex-sm-fill text-sm-center nav-link active" id="onglet_information_biographique">INFORMATIONS BIOGRAPHIQUES</span>
                    <span class="flex-sm-fill text-sm-center nav-link disabled"  id="onglet_information_familiale">Contexte Familial</span>
                    <span class="flex-sm-fill text-sm-center nav-link disabled" id="onglet_identification_agac">Identification visuelle</span>
                    <span class="flex-sm-fill text-sm-center nav-link disabled" id="onglet_affectation">Affectation</span>
                </nav>
                <hr>
            </div>


            <p id="p_resultats_form_biographique" class="text-center"></p>
            <p id="p_resultats_form_recrutement_agac_infos_famille" class="text-center"></p>
            <p id="p_resultats_form_identification_visuelle" class="text-center"></p>
            <p class="col-md-4 offset-md-4 mb-4 text-success" id="resultat_form_onglet_affectation"></p>



            <?php
                $trouver_info_biographique = $RECRUTEMENT->trouver_infos_biographique($id_compte) ;
                $trouver_info_famille  = $RECRUTEMENT->trouver_infos_famille($id_compte);
                if($trouver_info_biographique){
                    ?>
                    <p id="p_resultats_form_biographique_modif" class="text-center"></p>
                    <p id="resultat_form_onglet_modifier_affectation" class="text-center"></p>

                    <form id="form_recrutement_agac_modifier_infos_biographiques">
                        <div class="form-row">
                            <div class="col-md-4 mb-4">
                                <input type="hidden" id="id_compte_input" value="<?= $id_compte ?>">
                                <label for="num_secu_agac_modif_input" class="form-label text-dark"><b>Numéro de sécurité sociale</b></label>
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-sm" id="num_secu_agac_modif_input" value="<?= $trouver_info_biographique["NUMERO_SECU"] ?>" name="num_secu_agac_modif_input" maxlength="13" placeholder="Numéro de sécurité sociale" autocomplete="off" />
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <label for="id_compte_modif_input" class="form-label text-danger"><b>Téléphone(*)</b></label>
                                <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-phone"></i> <!-- Icône Font Awesome -->
                                            </span>
                                    <input type="text" id="id_compte_modif_input" name="id_compte_modif_input" value="<?= $id_compte ?>" hidden>
                                    <input type="text" class="form-control form-control-sm" id="numero_telephone_modif_input" name="numero_telephone_modif_input" maxlength="10" placeholder="Numéro de téléphone AGAC" value="<?= $telephone['NUMERO_TELEPHONE'] ?>" readonly/>
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <label for="civilite_agac_modif_input" class="form-label text-danger"><b>Civilité(*)</b></label>
                                <div class="input-group">
                                    <select id="civilite_agac_modif_input" name="civilite_agac_modif_input" class="form-control form-control-sm" required>
                                        <?php
                                        $civilite_agac = $RECRUTEMENT->trouver_civilite($trouver_info_biographique["CIVILITE"])?>
                                        <option value="<?= $trouver_info_biographique["CIVILITE"] ?>"><?= $civilite_agac["LIBELLE"] ?></option>
                                        <?php foreach($civilites as $civilite){
                                                    if($civilite["CODE"] !== $civilite_agac['CODE']){?>
                                                            <option value="<?= $civilite["CODE"] ?>"><?= $civilite["LIBELLE"] ?></option>
                                        <?php } }?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <label for="numero_cnps_modif_input" class="form-label"><b>Numéro CNPS</b></label>
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-sm" id="numero_cnps_modif_input" name="numero_cnps_modif_input" maxlength="25" placeholder="Numéro CNPS" autocomplete="off" value="<?php if(isset($trouver_info_biographique['NUMERO_CNPS'])){ echo $trouver_info_biographique['NUMERO_CNPS']; } ?>"  />
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <label for="nom_agac_modif_input" class="form-label text-danger"><b>Nom (*)</b></label>
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-sm" id="nom_agac_modif_input" name="nom_agac_modif_input" placeholder="Nom" autocomplete="off" value="<?= $trouver_info_biographique['NOM'] ?>" required/>
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <label for="nom_agac_modif_input" class="form-label text-danger"><b>Prénoms (*)</b></label>
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-sm" name="prenoms_agac_modif_input" id="prenoms_agac_modif_input" placeholder="Prénom(s)" autocomplete="off" value="<?= $trouver_info_biographique['PRENOMS'] ?>" required/>
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <label for="nom_agac_modif_input" class="form-label text-danger"><b>Date de Naissance (*)</b></label>
                                <div class="input-group">
                                    <input type="date" class="form-control form-control-sm" name="date_naissance_agac_modif_input" id="date_naissance_agac_modif_input" value="<?= date('Y-m-d',strtotime($trouver_info_biographique['DATE_NAISSANCE']))  ?>"  placeholder="Date de naissance" autocomplete="off" required/>
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <label for="lieu_naiss_agac_modif_input" class="form-label text-danger"><b>Lieu de Naissance (*)</b></label>
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-sm" id="lieu_naiss_agac_modif_input" name="lieu_naiss_agac_modif_input" placeholder="Lieu de naissance" value="<?= $trouver_info_biographique['LIEU_NAISSANCE'] ?>"  autocomplete="off" required />
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <label for="nationalite_agac_modif_input" class="form-label text-danger"><b>Nationalité (*)</b></label>
                                <div class="input-group">
                                    <select id="nationalite_agac_modif_input" name="nationalite_agac_modif_input" class="form-control form-control-sm" required>
                                        <?php foreach ($nationalites AS $nationalite) {
                                            if($trouver_info_biographique["NATIONALITE"] == $nationalite["CODE"]){?>
                                                <option value="<?= $trouver_info_biographique["NATIONALITE"] ?>" selected="selected"><?= $nationalite["LIBELLE"] ?></option>
                                            <?php } ?>
                                            <option value="<?= $nationalite["CODE"] ?>"><?= $nationalite["LIBELLE"] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <label for="nationalite_agac_modif_input" class="form-label text-danger"><b>Sexe (*)</b></label>
                                <div class="input-group">
                                    <select id="sexe_agac_modif_input" name="sexe_agac_modif_input" class="form-control form-control-sm" required>
                                        <?php foreach ($sexes AS $sexe) {
                                            if($trouver_info_biographique["SEXE"] == $sexe["CODE"]){?>
                                                <option value="<?= $trouver_info_biographique["SEXE"] ?>" selected="selected"><?= $sexe["LIBELLE"] ?></option>
                                            <?php }
                                            if($sexe["CODE"] != $trouver_info_biographique["SEXE"]){ ?>
                                                <option value="<?= $sexe["CODE"] ?>"><?= $sexe["LIBELLE"] ?></option>
                                            <?php }
                                            ?>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4 mb-4">
                                <label for="adresse_email_agac_modif_input" class="form-label text-dark"><b>Adresse Email</b></label>
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-sm" value="<?= $trouver_info_biographique["ADRESSE_MAIL"] ?>" id="adresse_email_agac_modif_input" name="adresse_email_agac_modif_input" placeholder="Adresse Email" autocomplete="off" />
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <label for="type_piece_agac_modif_input" class="form-label text-danger"><b>Type de pièce (*)</b></label>
                                <div class="input-group">
                                    <select id="type_piece_agac_modif_input" name="type_piece_agac_modif_input" class="form-control form-control-sm" required>

                                        <?php foreach ($documents AS $document) {
                                            if($trouver_info_biographique["TYPE_DE_PIECE"] == $document["CODE"] ){ ?>
                                                <option value="<?= $trouver_info_biographique["TYPE_DE_PIECE"] ?>"><?= $document["LIBELLE"] ?></option>
                                            <?php }
                                            if( $document["CODE"] != $trouver_info_biographique["TYPE_DE_PIECE"] ){ ?>
                                                <option value="<?= $document["CODE"] ?>"><?= $document["LIBELLE"] ?></option>
                                            <?php }
                                            ?>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <label for="numero_piece_agac_modif_input" class="form-label text-danger"><b>Numéro pièce (*)</b></label>
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-sm" id="numero_piece_agac_modif_input" name="numero_piece_agac_modif_input" value="<?= $trouver_info_biographique["NUMERO_PIECE"] ?>" placeholder="Numéro de la pièce" autocomplete="off" required />
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <label for="numero_piece_agac_modif_input" class="form-label text-danger"><b>Situation matrimoniale (*)</b></label>
                                <div class="input-group">
                                    <select id="situation_matrimoniale_agac_modif_input" name="situation_matrimoniale_agac_modif_input" class="form-control form-control-sm" required>
                                        <?php foreach ($statut_matrimoniales AS $statut_matrimoniale) {
                                            if($trouver_info_biographique["SITUATION_MATRIMONIALE"] == $statut_matrimoniale["CODE"]){?>
                                                <option value="<?= $trouver_info_biographique["SITUATION_MATRIMONIALE"] ?>" selected="selected"><?= $statut_matrimoniale["LIBELLE"] ?></option>
                                            <?php }
                                            if($trouver_info_biographique["SITUATION_MATRIMONIALE"] != $statut_matrimoniale["CODE"]){?>
                                                <option value="<?= $statut_matrimoniale["CODE"] ?>"><?= $statut_matrimoniale["LIBELLE"] ?></option>
                                            <?php }
                                        } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <label for="nombre_enfant_agac_modif_input" class="form-label text-danger"><b>Nombre d'enfants(*)</b></label>
                                <div class="input-group">
                                    <select id="nombre_enfant_agac_modif_input" name="nombre_enfant_agac_modif_input" class="form-control form-control-sm" required>
                                        <option value="<?= $trouver_info_biographique["NOMBRE_ENFANTS"] ?>" selected="selected"><?= $trouver_info_biographique["NOMBRE_ENFANTS"] ?></option>
                                        <?php
                                            for($i = 0;$i<=6;$i++){
                                                if($i !== (int)$trouver_info_biographique["NOMBRE_ENFANTS"]){?>
                                                    <option value="<?= $i ?>"><?= $i ?></option>
                                            <?php }}
                                        ?>
                                    </select>
                                </div>

                            </div>
                            <div id="champs_modif_enfants" class="row col-sm-12" style="display: none;"> <!-- Conteneur pour les champs enfants -->
                                <!-- Les champs d'entrée pour les enfants seront ajoutés ici par jQuery -->
                            </div>

                            <div class="col-sm-12">
                                <hr>
                                <div class="row">
                                    <div class="col-md-4 mb-4">
                                        <label for="nom_banque_modif_input" class="form-label text-dark"><b>Nom Banque</b></label>
                                        <div class="input-group">
                                            <input type="text" class="form-control form-control-sm" value="<?= $trouver_info_biographique["NOM_BANQUE"] ?>" id="nom_banque_modif_input" name="nom_banque_modif_input" placeholder="Nom Banque" autocomplete="off" />
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-4">
                                        <label for="code_banque_modif_input" class="form-label text-dark"><b>Code Banque</b></label>
                                        <div class="input-group">
                                            <input type="text" class="form-control form-control-sm" value="<?= $trouver_info_biographique["CODE_BANQUE"] ?>" id="code_banque_modif_input" name="code_banque_modif_input" placeholder="Code Banque" autocomplete="off" maxlength="5" />
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-4">
                                        <label for="code_guichet_modif_input" class="form-label text-dark"><b>Code Agence / Code Guichet</b></label>
                                        <div class="input-group">
                                            <input type="text" class="form-control form-control-sm" value="<?= $trouver_info_biographique["CODE_GUICHET"] ?>" id="code_guichet_modif_input" name="code_guichet_modif_input" placeholder="Code Guichet" autocomplete="off" maxlength="5" />
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-4">
                                        <label for="numero_compte_modif_input" class="form-label text-dark"><b>Numéro Compte</b></label>
                                        <div class="input-group">
                                            <input type="text" class="form-control form-control-sm" value="<?= $trouver_info_biographique["NUMERO_COMPTE"] ?>" id="numero_compte_modif_input" name="numero_compte_modif_input" placeholder="Numéro Compte" autocomplete="off" maxlength="12" />
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-4">
                                        <label for="cle_rib_modif_input" class="form-label text-dark"><b>Clé RIB</b></label>
                                        <div class="input-group">
                                            <input type="text" class="form-control form-control-sm" value="<?= $trouver_info_biographique["CLE_RIB"] ?>" id="cle_rib_modif_input" name="cle_rib_modif_input" placeholder="Clé RIB" autocomplete="off" maxlength="2" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <hr>
                                <div class="col-md-4 offset-md-10 mb-4">
                                    <div class="input-group">
                                        <button type="submit" id="btn_submit_modifier_etape1" class="btn btn-sm btn-warning col-sm-5"><i class="fa fa-exchange-alt"></i> Etape Suivante </button>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </form>
                    <form id="form_recrutement_agac_modifier_infos_famille">
                        <div class="form-row">
                            <div class="col-sm-12 text-primary">
                                <b>INFORMATION DU PERE</b>
                                <hr>
                            </div>
                            <input type="text" class="form-control form-control-sm" id="id_compte" name="id_compte" maxlength="10" placeholder="Numéro de téléphone AGAC" value="<?= $id_compte ?>" hidden/>
                            <div class="col-md-4 mb-4">
                                <label for="prenoms_pere_modif_input" class="form-label text-danger"><b>Nom du père(*)</b></label>
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-sm" id="nom_pere_modif_input" name="nom_pere_modif_input" value="<?php if($trouver_info_famille){echo $trouver_info_famille['NOM_PERE'];} ?>" placeholder="nom du père"  required/>
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <label for="prenoms_pere_modif_input" class="form-label text-danger"><b>Prénom(s) du père(*)</b></label>
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-sm" id="prenoms_pere_modif_input" name="prenoms_pere_modif_input" value="<?php if($trouver_info_famille){echo $trouver_info_famille['PRENOMS_PERE'] ;}?>" placeholder="prénoms du père"  required/>
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <label for="date_naissance_pere_input" class="form-label text-dark"><b>Date de naissance du père</b></label>
                                <div class="input-group">
                                    <input type="date" class="form-control form-control-sm" id="date_naissance_pere_modif_input" name="date_naissance_pere_modif_input" value="<?php if($trouver_info_famille){echo date('Y-m-d',strtotime($trouver_info_famille['DATE_NAISSANCE_PERE']));} ?>" placeholder="Date de naissance du père" />
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-sm-12 text-primary">
                                <b>INFORMATION DE LE MÈRE</b>
                                <hr>
                            </div>
                            <div class="col-md-4 mb-4">
                                <label for="nom_mere_modif_input" class="form-label text-danger"><b>Nom de la mère(*)</b></label>
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-sm" id="nom_mere_modif_input" name="nom_mere_modif_input" value="<?php if($trouver_info_famille){echo $trouver_info_famille['NOM_MERE'];}?>" placeholder="Nom de la mère" required/>
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <label for="prenoms_mere_modif_input" class="form-label text-danger"><b>Prénom(s) de la mère(*)</b></label>
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-sm" id="prenoms_mere_modif_input" name="prenoms_mere_modif_input" value="<?php if($trouver_info_famille){echo $trouver_info_famille['PRENOMS_MERE'];}?>" placeholder="Prénom(s) de la mère" required/>
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <label for="date_naissance_mere_modif_input" class="form-label text-dark"><b>Date de naissance de la mère</b></label>
                                <div class="input-group">
                                    <input type="date" class="form-control form-control-sm" id="date_naissance_mere_modif_input" name="date_naissance_mere_modif_input" value="<?php if($trouver_info_famille){echo date('Y-m-d',strtotime($trouver_info_famille['DATE_NAISSANCE_MERE']));}?>"   placeholder="Date de naissance de la mère"/>
                                </div>
                            </div>
                        </div>
                        <div id="champs_option_modif_conjoint" class="row col-sm-12" style="display: none;"> <!-- Conteneur pour les champs enfants -->
                            <div class="col-sm-12 text-primary">
                                <b>INFORMATION DU CONJOINT</b>
                                <hr>
                            </div>
                            <div class="row col-sm-12">
                                <div class="col-md-4 mb-4 additional-fields">
                                    <label for="num_secu_conjoint_modif_input" class="form-label text-dark"><b>Numéro secu du conjoint</b></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-sm" id="num_secu_conjoint_modif_input" name="num_secu_conjoint_modif_input" maxlength="13" value="<?php if($trouver_info_famille){echo $trouver_info_famille["NUMERO_SECU_CONJOINT"];}?>" placeholder="Numéro Secu du conjoint"/>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-4 additional-fields">
                                    <label for="nom_conjoint_modif_input" class="form-label text-danger"><b>Nom du conjoint(*)</b></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-sm" id="nom_conjoint_modif_input" name="nom_conjoint_modif_input" value="<?php if($trouver_info_famille){echo $trouver_info_famille['NOM_CONJOINT'];}?>" placeholder="Nom du conjoint"/>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-4 additional-fields">
                                    <label for="prenoms_conjoint_modif_input" class="form-label text-danger"><b>Prénom(s) du conjoint(*)</b></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-sm" id="prenoms_conjoint_modif_input" name="prenoms_conjoint_modif_input" value="<?php if($trouver_info_famille){echo $trouver_info_famille['PRENOMS_CONJOINT'];}?>" placeholder="Prénom(s) du conjoint"/>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-4 additional-fields">
                                    <label for="date_naissance_conjoint_modif_input" class="form-label text-danger"><b>Date de naissance du conjoint(*)</b></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-sm" id="date_naissance_conjoint_modif_input" name="date_naissance_conjoint_modif_input" placeholder="Date de naissance du conjoint" value="<?php if($trouver_info_famille){echo date('d-m-Y', strtotime($trouver_info_famille['DATE_NAISSANCE_CONJOINT']));}?>"/>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-4 additional-fields">
                                    <label for="profession_conjoint_modif_input" class="form-label text-danger"><b>Profession du conjoint(*)</b></label>
                                    <div class="input-group">
                                        <select id="profession_conjoint_modif_input" name="profession_conjoint_modif_input" class="form-control form-control-sm">
                                            <option value="">Profession du Conjoint</option>
                                            <?php foreach ($professions as $profession) {
                                                if ( $trouver_info_famille && $trouver_info_famille["PROFESSION_CONJOINT"] == $profession['CODE']) { ?>
                                                    <option value="<?= $profession["CODE"] ?>" selected><?= $profession["LIBELLE"] ?></option>
                                                <?php } else { ?>
                                                    <option value="<?= $profession["CODE"] ?>"><?= $profession["LIBELLE"] ?></option>
                                                <?php } ?>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-sm-12 text-primary">
                                <b>EN CAS D'URGENCE CONTACTER</b>
                                <hr>
                            </div>
                            <div class="col-md-8 mb-4">
                                <label for="nom_personne_urgence_modif_input" class="form-label text-danger"><b>Personne à contacter en cas d'urgence 1</b></label>
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-sm" id="nom_personne_urgence_modif_input" name="nom_personne_urgence_modif_input" value="<?php if($trouver_info_famille){echo $trouver_info_famille["NOM_PERSONNE_URGENCE"];}?>"/>
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <label for="telephone_personne_urgence_modif_input" class="form-label text-danger"><b>Téléphone Personne à contacter 1</b></label>
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-sm" id="telephone_personne_urgence_modif_input" maxlength="10" name="telephone_personne_urgence_modif_input" value="<?php if($trouver_info_famille){echo $trouver_info_famille["TELEPHONE_PERSONNE_URGENCE"];}?>" required/>
                                </div>
                            </div>
                            <div class="col-md-8 mb-4">
                                <label for="nom_personne_urgence_deux_modif_input" class="form-label text-danger"><b>Personne à contacter en cas d'urgence 2</b></label>
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-sm" id="nom_personne_urgence_deux_modif_input" name="nom_personne_urgence_deux_modif_input" value="<?php if($trouver_info_famille){echo $trouver_info_famille["NOM_PERSONNE_URGENCE_DEUX"];}?>"/>
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <label for="telephone_personne_urgence_deux_modif_input" class="form-label text-danger"><b>Téléphone Personne à contacter 2</b></label>
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-sm" id="telephone_personne_urgence_deux_modif_input" maxlength="10" name="telephone_personne_urgence_deux_modif_input" value="<?php if($trouver_info_famille){echo $trouver_info_famille["TELEPHONE_PERSONNE_URGENCE_DEUX"];}?>" required/>
                                </div>
                            </div>
                            <div class="col-md-8 mb-4">
                                <label for="nom_personne_urgence_trois_modif_input" class="form-label text-danger"><b>Personne à contacter en cas d'urgence 3</b></label>
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-sm" id="nom_personne_urgence_trois_modif_input" name="nom_personne_urgence_trois_modif_input" value="<?php if($trouver_info_famille){echo $trouver_info_famille["NOM_PERSONNE_URGENCE_TROIS"];}?>"/>
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <label for="telephone_personne_urgence_trois_modif_input" class="form-label text-danger"><b>Téléphone Personne à contacter 3</b></label>
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-sm" id="telephone_personne_urgence_trois_modif_input" maxlength="10" name="telephone_personne_urgence_trois_modif_input" value="<?php if($trouver_info_famille){echo $trouver_info_famille["TELEPHONE_PERSONNE_URGENCE_TROIS"];}?>" required/>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <hr>
                            <div class="col-md-4 offset-md-10 mb-4">
                                <div class="input-group">
                                    <button type="submit" id="btn_submit_modifier_etape2" class="btn btn-sm btn-warning col-sm-5"><i class="fa fa-exchange-alt"></i> Etape Suivante</button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <form id="recrutement_agac_modifier_identification_visuelle" enctype="multipart/form-data">
                        <div class="form-row justify-content-md-center">
                            <input type="text" class="form-control form-control-sm" id="id_compte_modif_et3" name="id_compte_modif_et3" maxlength="10" placeholder="Numéro de téléphone AGAC" value="<?= $id_compte ?>" hidden/>
                            <div class="col-md-12">
                                Veuillez inserrer ici une photo d'identité
                            </div>
                            <div class="col-md-8 mb-4">
                                <div class="input-group">
                                    <input type="file" class="form-control form-control-sm" id="photo_identification_modif_input" name="photo_identification_modif_input" placeholder="Veuillez sélectionner une photo d'identification" required accept="image/*"/>
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <div class="input-group">
                                    <button type="submit" id="btn_submit_modif_etape3" class="btn btn-sm btn-warning col-sm-8"><i class="fa fa-exchange-alt"></i> Confirmer la photo </button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <form id="form_onglet_modifier_affectation">
                        <div class="form-row">
                            <input type="text" class="form-control form-control-sm" id="id_compte_et_modif_4" name="id_compte_et_modif_4" maxlength="10" placeholder="Numéro de téléphone AGAC" value="<?= $id_compte ?>" hidden/>
                            <div class="row col-md-12">
                                <div class="col-sm-9" style="visibility: hidden">
                                    <input class="form-control form-control-sm" name="centre_sante_modif_input" id="centre_sante_modif_input"  placeholder="Nom du Centre de santé"  readonly/>
                                </div>
                                <div class="col-sm-3 mb-4">
                                    <div class="input-group">
                                        <button type="submit" id="btn_submit_modif_etape4" class="btn btn-sm btn-warning col-sm-12"><i class="fa fa-exchange-alt"></i> Confirmer les informations </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <script type="text/javascript" src="<?= JS.'page_recrutement_modif.js?v=1';?>"></script>
            <?php }else{
                $centres = $RECRUTEMENT->trouver_centre_agac($telephone['NUMERO_TELEPHONE']);
                if($centres){
                    $raison_sociales = $centres["STRUCTURE_SANITAIRE"];
                }?>
                    <form id="form_recrutement_agac_infos_biographiques">
                        <div class="form-row">
                            <div class="col-md-4 mb-4">
                                <label for="num_secu_agac_input" class="form-label"><b>Numéro de sécurité sociale</b></label>
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-sm" id="num_secu_agac_input" name="num_secu_agac_input" maxlength="13" placeholder="Numéro de sécurité sociale" autocomplete="off"  />
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <label for="numero_telephone_input" class="form-label text-danger"><b>Numéro de téléphone(*)</b></label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-phone"></i> <!-- Icône Font Awesome -->
                                    </span>
                                    <input type="text" class="form-control form-control-sm" id="numero_telephone_input" name="numero_telephone_input" maxlength="10" placeholder="Numéro de téléphone AGAC" value="<?= $telephone['NUMERO_TELEPHONE'] ?>" readonly/>
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <label for="numero_telephone_input" class="form-label text-danger"><b>Civilité(*)</b></label>
                                <div class="input-group">
                                    <select id="civilite_agac_input" name="civilite_agac_input" class="form-control form-control-sm" required>
                                        <option value="">Civilité</option>
                                        <?php foreach($civilites as $civilite){ ?>
                                            <option value="<?= $civilite["CODE"] ?>"><?= $civilite["LIBELLE"] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <label for="numero_cnps_input" class="form-label"><b>Numéro CNPS</b></label>
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-sm" id="numero_cnps_input" name="numero_cnps_input" maxlength="13" placeholder="Numéro CNPS" autocomplete="off"  />
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <label for="nom_agac_input" class="form-label text-danger"><b>Nom(*)</b></label>
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-sm" id="nom_agac_input" name="nom_agac_input" placeholder="Nom" autocomplete="off" required/>
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <label for="prenoms_agac_input" class="form-label text-danger"><b>Prénoms(*)</b></label>
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-sm" name="prenoms_agac_input" id="prenoms_agac_input" placeholder="Prénom(s)" autocomplete="off" required/>
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <label for="date_naissance_agac_input" class="form-label text-danger"><b>Date de naissance(*)</b></label>
                                <div class="input-group">
                                    <input type="date" class="form-control form-control-sm " name="date_naissance_agac_input" id="date_naissance_agac_input" placeholder="Date de naissance" autocomplete="off" required/>
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <label for="lieu_naiss_agac_input" class="form-label text-danger"><b>Lieu de naissance(*)</b></label>
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-sm" id="lieu_naiss_agac_input" name="lieu_naiss_agac_input" placeholder="Lieu de naissance" autocomplete="off" required />
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <label for="nationalite_agac_input" class="form-label text-danger"><b>Nationalité (*)</b></label>
                                <div class="input-group">
                                    <select id="nationalite_agac_input" name="nationalite_agac_input" class="form-control form-control-sm" required>
                                        <option value="">Nationalité</option>
                                        <?php foreach ($nationalites AS $nationalite) {
                                            if ($nationalite["CODE"]==='CIV') {
                                                ?>
                                                <option value="<?= $nationalite["CODE"] ?>" selected="selected"><?= $nationalite["LIBELLE"] ?></option>
                                                <?php
                                            }
                                            else{
                                                ?>
                                                <option value="<?= $nationalite["CODE"] ?>"><?= $nationalite["LIBELLE"] ?></option>
                                                <?php
                                            } }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <label for="nom_agac_input" class="form-label text-danger"><b>Sexe(*)</b></label>
                                <div class="input-group">
                                    <select id="sexe_agac_input" name="sexe_agac_input" class="form-control form-control-sm" required>
                                        <option value="">Sexe</option>
                                        <?php foreach ($sexes AS $sexe) { ?>
                                            <option value="<?= $sexe["CODE"] ?>"><?= $sexe["LIBELLE"] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <label for="nom_agac_input" class="form-label text-danger"><b>Lieu de résidence(*)</b></label>
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-sm" id="lieu_residence_agac_input" name="lieu_residence_agac_input" placeholder="Lieu de Résidence" autocomplete="off" required />
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <label for="nom_agac_input" class="form-label"><b>Adresse Email</b></label>
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-sm" id="adresse_email_agac_input" name="adresse_email_agac_input" placeholder="Adresse Email" autocomplete="off" />
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <label for="nom_agac_input" class="form-label text-danger"><b>Type pièce(*)</b></label>
                                <div class="input-group">
                                    <select id="type_piece_agac_input" name="type_piece_agac_input" class="form-control form-control-sm" required>
                                        <option value="">Type de pièce</option>
                                        <?php foreach ($documents AS $document) { ?>
                                            <option value="<?= $document["CODE"] ?>"><?= $document["LIBELLE"] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <label for="nom_agac_input" class="form-label text-danger"><b>Numéro de la pièce(*)</b></label>
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-sm" id="numero_piece_agac_input" name="numero_piece_agac_input" placeholder="Numéro de la pièce" autocomplete="off" required />
                                </div>
                            </div>

                            <div class="col-md-4 mb-4">
                                <label for="nom_agac_input" class="form-label text-danger"><b>Situation matrimoniale(*)</b></label>
                                <div class="input-group">
                                    <select id="situation_matrimoniale_agac_input" name="situation_matrimoniale_agac_input" class="form-control form-control-sm" required>
                                        <option value="">Situation Matrimoniale</option>
                                        <?php foreach ($statut_matrimoniales AS $statut_matrimoniale) { ?>
                                            <option value="<?= $statut_matrimoniale["CODE"] ?>"><?= $statut_matrimoniale["LIBELLE"] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <label for="nom_agac_input" class="form-label text-danger"><b>Nombre d'enfants(*)</b></label>
                                <div class="input-group">
                                    <select id="nombre_enfant_agac_input" name="nombre_enfant_agac_input" class="form-control form-control-sm" required>
                                        <option value="">Nombre d'enfants</option>
                                        <option value="0">0</option>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                        <option value="6">6</option>
                                    </select>
                                </div>

                            </div>
                            <div id="champs_enfants" class="row col-sm-12" style="display: none;"> <!-- Conteneur pour les champs enfants -->
                                <!-- Les champs d'entrée pour les enfants seront ajoutés ici par jQuery -->
                            </div>

                            <div class="col-sm-12">
                                <hr>
                                <div class="row">
                                    <div class="col-md-4 mb-4">
                                        <label for="nom_banque_input" class="form-label text-dark"><b>Nom Banque</b></label>
                                        <div class="input-group">
                                            <input type="text" class="form-control form-control-sm" value="" id="nom_banque_input" name="nom_banque_input" placeholder="Nom Banque" autocomplete="off" />
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-4">
                                        <label for="code_banque_input" class="form-label text-dark"><b>Code Banque</b></label>
                                        <div class="input-group">
                                            <input type="text" class="form-control form-control-sm" value="" id="code_banque_input" name="code_banque_input" placeholder="Code Banque" autocomplete="off" maxlength="5" />
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-4">
                                        <label for="code_guichet_input" class="form-label text-dark"><b>Code Agence / Code Guichet</b></label>
                                        <div class="input-group">
                                            <input type="text" class="form-control form-control-sm" value="" id="code_guichet_input" name="code_guichet_input" placeholder="Code Guichet" autocomplete="off" maxlength="5" />
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-4">
                                        <label for="numero_compte_input" class="form-label text-dark"><b>Numéro Compte</b></label>
                                        <div class="input-group">
                                            <input type="text" class="form-control form-control-sm" value="" id="numero_compte_input" name="numero_compte_input" placeholder="Numéro Compte" autocomplete="off" maxlength="12" />
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-4">
                                        <label for="cle_rib_input" class="form-label text-dark"><b>Clé RIB</b></label>
                                        <div class="input-group">
                                            <input type="text" class="form-control form-control-sm" value="" id="cle_rib_input" name="cle_rib_input" placeholder="Clé RIB" autocomplete="off" maxlength="2" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <hr>
                                <div class="col-md-4 offset-md-10 mb-4">
                                    <div class="input-group">
                                        <button type="submit" id="btn_submit_etape1" class="btn btn-sm btn-warning col-sm-5"><i class="fa fa-exchange-alt"></i> Etape Suivante </button>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </form>
                    <form id="form_recrutement_agac_infos_famille">
                        <div class="form-row">
                            <div class="col-sm-12 text-primary">
                                <b>INFORMATION DU PERE</b>
                                <hr>
                            </div>
                            <input type="text" class="form-control form-control-sm" id="id_compte" name="id_compte" maxlength="10" placeholder="Numéro de téléphone AGAC" value="<?= $id_compte ?>" hidden/>
                            <div class="col-md-4 mb-4">
                                <label for="nom_pere_input" class="form-label text-danger"><b>Nom du père</b></label>
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-sm" id="nom_pere_input" name="nom_pere_input" placeholder="Nom du père" required/>
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <label for="nom_pere_input" class="form-label text-danger"><b>Prénom(s) du père(*)</b></label>
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-sm" id="prenoms_pere_input" name="prenoms_pere_input" placeholder="Prénom(s) du père" required/>
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <label for="date_naissance_pere_input" class="form-label text-dark"><b>Date de naissance du père</b></label>
                                <div class="input-group">
                                    <input type="date" class="form-control form-control-sm" id="date_naissance_pere_input" name="date_naissance_pere_input" placeholder="Date de naissance de du père"/>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-sm-12 text-primary">
                                <b>INFORMATION DE LA MERE</b>
                                <hr>
                            </div>
                            <div class="col-md-4 mb-4">
                                <label for="nom_mere_input" class="form-label text-danger"><b>Nom de la mère(*)</b></label>
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-sm" id="nom_mere_input" name="nom_mere_input" placeholder="Nom de la mère" required/>
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <label for="nom_mere_input" class="form-label text-danger"><b>Prénom(s) de la mère(*)</b></label>
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-sm" id="prenoms_mere_input" name="prenoms_mere_input" placeholder="Prénom(s) de la mère" required/>
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <label for="date_naissance_mere_input" class="form-label text-dark"><b>Date de naissance de la mère</b></label>
                                <div class="input-group">
                                    <input type="date" class="form-control form-control-sm" id="date_naissance_mere_input" name="date_naissance_mere_input" placeholder="Date de naissance de la mère"/>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div id="champs_option_conjoint" class="row col-sm-12" style="display: none;"> <!-- Conteneur pour les champs enfants -->
                                <div class="col-sm-12 text-primary">
                                    <b>INFORMATION DU CONJOINT</b>
                                    <hr>
                                </div>

                                <div class="col-md-4 mb-4 additional-fields">
                                    <label for="num_secu_conjoint_input" class="form-label text-dark"><b>Numéro secu du conjoint</b></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-sm " id="num_secu_conjoint_input" name="num_secu_conjoint_input" maxlength="13"  placeholder="Numéro secu du conjoint"/>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-4 additional-fields">
                                    <label for="nom_conjoint_input" class="form-label text-danger"><b>Nom du conjoint</b></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-sm " id="nom_conjoint_input" name="nom_conjoint_input" placeholder="Nom du conjoint"/>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-4 additional-fields">
                                    <label for="prenoms_conjoint_input" class="form-label text-danger"><b>Prénom(s) du conjoint</b></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-sm " id="prenoms_conjoint_input" name="prenoms_conjoint_input" placeholder="Prénom(s) du conjoint"/>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-4 additional-fields">
                                    <label for="date_naissance_conjoint_input" class="form-label text-danger"><b>Date naissance du conjoint</b></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-sm" id="date_naissance_conjoint_input" name="date_naissance_conjoint_input" placeholder="Date de Naissance du conjoint"/>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-4 additional-fields">
                                    <label for="profession_conjoint_input" class="form-label text-danger"><b>Profession du conjoint</b></label>
                                    <div class="input-group">
                                        <select id="profession_conjoint_input" name="profession_conjoint_input" class="form-control form-control-sm" >
                                            <option value="">Profession du Conjoint</option>
                                            <?php foreach ($professions as $profession){ ?>
                                                <option value="<?= $profession["CODE"] ?>"><?= $profession["LIBELLE"] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-4 additional-fields">
                                    <label for="telephone_conjoint_input" class="form-label text-danger"><b>Numéro téléphone du conjoint</b></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-sm " id="telephone_conjoint_input" name="telephone_conjoint_input" maxlength="10"   placeholder="Téléphone du conjoint"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8 mb-4">
                                <label for="nom_personne_urgence_input" class="form-label text-danger"><b>Personne à contacter en cas d'urgence 1</b></label>
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-sm" id="nom_personne_urgence_input" name="nom_personne_urgence_input" placeholder="Nom et Prénom(s) de la personne à contacter en cas d'urgence"/>
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <label for="nom_personne_urgence_input" class="form-label text-danger"><b>Téléphone Personne à contacter 1</b></label>
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-sm" id="telephone_personne_urgence_input" maxlength="10" name="telephone_personne_urgence_input" placeholder="Téléphone personne urgence" required/>
                                </div>
                            </div>
                            <div class="col-md-8 mb-4">
                                <label for="nom_personne_urgence_deux_input" class="form-label text-danger"><b>Personne à contacter en cas d'urgence 2</b></label>
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-sm" id="nom_personne_urgence_deux_input" name="nom_personne_urgence_deux_input" placeholder="Nom et Prénom(s) de la personne à contacter en cas d'urgence"/>
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <label for="telephone_personne_urgence_deux_input" class="form-label text-danger"><b>Téléphone Personne à contacter 2 </b></label>
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-sm" id="telephone_personne_urgence_deux_input" maxlength="10" name="telephone_personne_urgence_deux_input" placeholder="Téléphone personne urgence" required/>
                                </div>
                            </div>
                            <div class="col-md-8 mb-4">
                                <label for="nom_personne_urgence_trois_input" class="form-label text-danger"><b>Personne à contacter en cas d'urgence 3</b></label>
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-sm" id="nom_personne_urgence_trois_input" name="nom_personne_urgence_trois_input" placeholder="Nom et Prénom(s) de la personne à contacter en cas d'urgence"/>
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <label for="telephone_personne_urgence_trois_input" class="form-label text-danger"><b>Téléphone Personne à contacter 3</b></label>
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-sm" id="telephone_personne_urgence_trois_input" maxlength="10" name="telephone_personne_urgence_trois_input" placeholder="Téléphone personne urgence" required/>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <hr>
                                        <div class="col-md-4 offset-md-10 mb-4">
                                            <div class="input-group">
                                                <button type="submit" id="btn_submit_etape2" class="btn btn-sm btn-warning col-sm-5"><i class="fa fa-exchange-alt"></i> Etape Suivante </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <form id="recrutement_agac_identification_visuelle" enctype="multipart/form-data">
                        <div class="form-row justify-content-md-center">
                            <input type="text" class="form-control form-control-sm" id="id_compte_etape3" name="id_compte_etape3" value="<?= $id_compte ?>" hidden/>
                            <div class="col-md-12" align="center">
                                <?php
                                    $info_identification_visuelle = $RECRUTEMENT->trouver_infos_identification_visuelle($id_compte);
                                    if($info_identification_visuelle){
                                         $image_mime_type = "image/png"; // Type MIME de l'image
                                    $image_base64 = stream_get_contents($info_identification_visuelle['PHOTO']);
                                    echo '<img src="data:'.$info_identification_visuelle["TYPE_PHOTO"].';base64,' . $image_base64 . '" alt="Image" width="100" height="100">';
                                    }

                                ?>
                                </td>
                            </div>
                            <div class="col-md-12">
                                Veuillez inserrer ici une photo d'identité
                            </div>
                            <div class="col-md-8 mb-4">
                                <div class="input-group">
                                    <input type="file" class="form-control form-control-sm" id="photo_identification_input" name="photo_identification_input" placeholder="Veuillez sélectionner une photo d'identification" <?php if(empty($info_identification_visuelle['PHOTO'])){echo 'required';} ?> accept="image/*"/>
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <div class="input-group">
                                    <button type="submit" id="btn_submit_etape3" class="btn btn-sm btn-warning col-sm-8"><i class="fa fa-exchange-alt"></i> Confirmer la photo </button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <form id="form_onglet_affectation">
                        <div class="form-row">
                            <div class="row col-md-12">
                                <input type="text" class="form-control form-control-sm" id="id_compte_etape_4" name="id_compte_etape_4" maxlength="10"  value="<?= $id_compte ?>" hidden/>
                                <div class="col-sm-9" style="visibility: hidden">
                                    <input class="form-control form-control-sm" name="centre_sante_agac_input" id="centre_sante_agac_input" value="<?= $raison_sociales ?>" readonly/>
                                </div>
                                <div class="col-sm-3 mb-4">
                                    <div class="input-group">
                                        <button type="submit" id="btn_submit_etape4" class="btn btn-sm btn-warning col-sm-12"><i class="fa fa-exchange-alt"></i> Confirmer les informations </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
            <?php } ?>

        </div>
    </div>
</div>

 <script type="text/javascript" src="<?= JS.'page_recrutement.js?v=1';?>"></script>



<?php } ?>
