<form id="form_facture">
    <div>
        <p class="titres_factures_p_dark">Identification</p>
        <div class="row">
            <div class="col-sm-12" id="identification_div_results"></div>
            <div class="col-sm-6">
                <div class="form-group row">
                    <label for="date_soins_input" class="col-sm-2 col-form-label-sm">Date soins</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control form-control-sm datepicker" id="date_soins_input" placeholder="" autocomplete="off" value="<?php if(empty($facture['DATE_SOINS'])){echo date('d/m/Y',time());}else {echo date('d/m/Y',strtotime($facture['DATE_SOINS']));} ?>" required />
                    </div>
                </div>
                <div class="form-group row">
                    <label for="num_ogd_input" class="col-sm-2 col-form-label-sm">OGD</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control form-control-sm" id="num_ogd_input" placeholder="" autocomplete="off" value="<?= $facture['NUM_OGD'];?>" required disabled />
                    </div>
                    <div class="col-sm-6">
                        <input type="text" class="form-control form-control-sm" id="nom_ogd_input" placeholder="" autocomplete="off" value="<?= $facture['NOM_OGD'];?>" required disabled />
                    </div>
                </div>
                <div class="form-group row">
                    <label for="num_fs_initiale_input" class="col-sm-2 col-form-label-sm">N° FS init.</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control form-control-sm" id="num_fs_initiale_input" placeholder="" autocomplete="off"  value="<?= $facture['NUM_FS_INITIALE'];?>" <?php if(!empty($facture['NUM_FS_INITIALE'])){echo 'disabled';}?> <?php if($type['CODE'] != 'AMB' && $type['CODE'] != 'DEN') {echo 'required';} ?> />
                    </div>
                    <label for="num_fs_input" class="col-sm-2 col-form-label-sm">N° facture</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control form-control-sm" id="num_fs_input" placeholder="" autocomplete="off"  value="<?= $facture['FEUILLE'];?>" required disabled />
                    </div>
                </div>
                <div class="form-group row">
                    <label for="num_ep_cnam_input" class="col-sm-8 col-form-label-sm">N° entente préalable CNAM</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control form-control-sm" id="num_ep_cnam_input" placeholder="" value="<?= $facture['NUM_EP_CNAM'];?>" autocomplete="off" <?php if($type['CODE'] == 'HOS') {echo 'disabled';} ?> />
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group row">
                    <label for="num_secu_input" class="col-sm-2 col-form-label-sm">N° sécu</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control form-control-sm" id="num_secu_input" placeholder="" autocomplete="off" value="<?= $facture['NUM_SECU'];?>" required disabled />
                    </div>
                </div>
                <div class="form-group row">
                    <label for="nom_prenom_input" class="col-sm-2 col-form-label-sm">Nom</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control form-control-sm" id="nom_prenom_input" placeholder="" autocomplete="off" value="<?= $facture['NOM'].' '.$facture['PRENOM'];?>" required disabled />
                    </div>
                </div>
                <div class="form-group row">
                    <label for="date_naissance_input" class="col-sm-2 col-form-label-sm">Date nais.</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control form-control-sm" id="date_naissance_input" placeholder="" autocomplete="off" value="<?= date('d/m/Y',strtotime($facture['DATE_NAISSANCE']));?>" required disabled />
                    </div>
                    <label for="genre_input" class="col-sm-2 col-form-label-sm">Genre</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control form-control-sm" id="genre_input" placeholder="" autocomplete="off" value="<?= $genre['LIBELLE'];?>" required disabled />
                    </div>
                </div>
                <div class="form-group row">
                    <label for="genre_input" class="col-sm-2 col-form-label-sm">Complément.</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control form-control-sm" id="num_ac_input" placeholder="" autocomplete="off" value="<?php if($facture['CODE_CSP'] == 'IND'){echo $facture['NUM_SECU'];}else{echo $facture['NUM_MATRICULE_AC'];} ?>" required <?php if($facture['NUM_OGD'] !== '02100000'){echo "disabled";} ?> />
                    </div>
                    <div class="col-sm-6">
                        <input type="text" class="form-control form-control-sm" id="code_ac_input" placeholder="" autocomplete="off" value="<?php if($facture['CODE_CSP'] == 'IND'){echo 'CNAM';} ?>" required disabled />
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div>
        <p class="titres_factures_p_dark">Etablissement d'accueil</p>
        <div class="row">
            <div class="col-sm-12" id="etablissement_sante_div_results"></div>
            <div class="col-sm-12">
                <div class="form-group row">
                    <label for="code_ets_input" class="col-sm-1 col-form-label-sm">Code</label>
                    <div class="col-sm-2">
                        <input type="text" class="form-control form-control-sm" id="code_ets_input" placeholder="" autocomplete="off" value="<?= $facture['ETABLISSEMENT'];?>" required disabled />
                    </div>
                    <label for="nom_ets_input" class="col-sm-1 col-form-label-sm">Nom</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control form-control-sm" id="nom_ets_input" placeholder="" autocomplete="off" value="<?= $facture['NOM_ETS'];?>" required disabled />
                        <input type="hidden" value="<?= $type['CODE'];?>" id="type_facture_input" />
                    </div>
                    <?php
                    if($type['CODE'] == 'MED'){
                        ?>
                        <div class="col-sm-2">
                            <input type="text" class="form-control form-control-sm" id="code_ets_initial_input" placeholder="" autocomplete="off" value="<?= $facture_initiale['ETABLISSEMENT'];?>" required disabled hidden/>
                        </div>
                        <?php
                    }
                    ?>
                </div>
                <div style="display: <?php if($type['CODE'] == 'AMB' || $type['CODE'] == 'DEN' || $type['CODE'] == 'HOS'){echo 'block';}else {echo 'none';} ?>">
                    <div class="form-group row">
                        <div class="col">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="type_ets_input" id="cmr_input" value="T" <?php if($facture['TYPE_ETS'] == 'T'){echo 'checked';}else {echo 'disabled';} ?> />
                                <label class="form-check-label col-form-label-sm" for="cmr_input">CMR</label>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="type_ets_input" id="urgence_input" value="U" <?php if($facture['TYPE_ETS'] == 'U'){echo 'checked';}else {if($facture['TYPE_ETS'] == 'T') {echo 'disabled';}} ?> />
                                <label class="form-check-label col-form-label-sm" for="urgence_input">URGENCE</label>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="type_ets_input" id="eloignement_input" value="H" <?php if($facture['TYPE_ETS'] == 'H'){echo 'checked';}else {if($facture['TYPE_ETS'] == 'T') {echo 'disabled';}} ?> />
                                <label class="form-check-label col-form-label-sm" for="eloignement_input">ELOIGNEMENT</label>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="type_ets_input" id="reference_input" value="R" <?php if($facture['TYPE_ETS'] == 'R'){echo 'checked';}else {if($facture['TYPE_ETS'] == 'T') {echo 'disabled';}} ?> />
                                <label class="form-check-label col-form-label-sm" for="reference_input">REFERENCE</label>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="type_ets_input" id="autre_input" value="A"  <?php if($facture['TYPE_ETS'] == 'A' || empty($facture['TYPE_ETS'])){echo 'checked';}else {if($facture['TYPE_ETS'] == 'T') {echo 'disabled';}} ?> />
                                <label class="form-check-label col-form-label-sm" for="autre_input">AUTRE</label>
                            </div>
                        </div>
                        <div class="col" hidden>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="type_ets_input" id="autre_input" value="P"  <?php if($facture['TYPE_ETS'] == 'P' || empty($facture['TYPE_ETS'])){echo 'checked';}else {if($facture['TYPE_ETS'] == 'T') {echo 'disabled';}} ?> />
                                <label class="form-check-label col-form-label-sm" for="autre_input">PHARMACIE</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row" id="autre_type_ets_div" <?php if($facture['TYPE_ETS'] != 'A' && !empty($facture['TYPE_ETS'])) {echo 'hidden';} ?>>
                        <div class="col-sm-3"></div>
                        <label for="type_ets_autre_input" class="col-sm-2 col-form-label-sm">Spécifier autre</label>
                        <div class="col-sm-7">
                            <input type="text" class="form-control form-control-sm" id="type_ets_autre_input" maxlength="100" placeholder="" autocomplete="off" <?php if($type['CODE'] != 'MED' && $type['CODE'] != 'EXP' ){if($facture['TYPE_ETS'] == 'A' || empty($facture['TYPE_ETS'])) {echo 'required';}}?>/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div>
        <p class="titres_factures_p_dark">Informations complémentaires sur l'assuré</p>
        <div class="row">
            <div class="col-sm-12" id="infos_complementaires_div_results"></div>
            <div class="col-sm-12">
                <div class="form-group row">
                    <div class="col">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="info_complementaire_input" id="mg_input" value="MG" <?php if($facture['INFO_COMPLEMENTAIRE'] == 'MG'){echo 'checked';} ?> />
                            <label class="form-check-label col-form-label-sm" for="mg_input">MATERNITE/GROSSESSE</label>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="info_complementaire_input" id="avp_input" value="AVP" <?php if($facture['INFO_COMPLEMENTAIRE'] == 'AVP'){echo 'checked';} ?> />
                            <label class="form-check-label col-form-label-sm" for="avp_input">ACCIDENT VP</label>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="info_complementaire_input" id="atmp_input" value="ATMP" <?php if($facture['INFO_COMPLEMENTAIRE'] == 'ATMP'){echo 'checked';} ?> />
                            <label class="form-check-label col-form-label-sm" for="atmp_input">AT/MP</label>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="info_complementaire_input" id="autre_info_input" value="AUT" <?php if($facture['INFO_COMPLEMENTAIRE'] == 'AUT'){echo 'checked';} ?> />
                            <label class="form-check-label col-form-label-sm" for="autre_info_input">AUTRE</label>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="info_complementaire_input" id="aucun_input" value="" <?php if($facture['INFO_COMPLEMENTAIRE'] == '' || $facture['INFO_COMPLEMENTAIRE'] == ' '){echo 'checked';} ?> />
                            <label class="form-check-label col-form-label-sm" for="aucun_input">AUCUN</label>
                        </div>
                    </div>
                </div>
                <div class="form-group row" id="avp_div" <?php if($facture['INFO_COMPLEMENTAIRE'] != 'ATMP'){echo 'hidden';} ?>>
                    <div class="col-sm-3"></div>
                    <label for="type_ets_autre_input" class="col-sm-2 col-form-label-sm">Date & N° Imm.</label>
                    <div class="col-sm-2">
                        <input type="text" class="form-control form-control-sm" id="date_accident_input" placeholder="Date" autocomplete="off" />
                    </div>
                    <div class="col-sm-5">
                        <input type="text" class="form-control form-control-sm" id="num_imm_vehicule_input" placeholder="N° Imm. du véhicule" autocomplete="off" />
                    </div>
                </div>
                <div class="form-group row" id="autre_info_complementaire_div" <?php if($facture['TYPE_ETS'] != 'AUT'){echo 'hidden';} ?>>
                    <div class="col-sm-3"></div>
                    <label for="info_compl_autre_input" class="col-sm-2 col-form-label-sm">Spécifier autre</label>
                    <div class="col-sm-7">
                        <input type="text" class="form-control form-control-sm" id="info_compl_autre_input" placeholder="" autocomplete="off" />
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-sm-3">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="programme_input" id="programme_spec_input" value="option1">
                            <label class="form-check-label" for="programme_spec_input">Programme spécial</label>
                        </div>
                    </div>

                    <div class="col" id="program_div">
                        <div class="row">
                            <label for="code_programme_input" id="label_code_programme_input" class="col-sm-1 col-form-label-sm">Code</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control form-control-sm" id="code_programme_input" placeholder="" autocomplete="off" />
                            </div>
                            <label for="nom_programme_input" id="label_nom_programme_input" class="col-sm-1 col-form-label-sm">Nom</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control form-control-sm" id="nom_programme_input" placeholder="" autocomplete="off" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div>
        <p class="titres_factures_p_dark">Professionnel de santé</p>
        <div class="row">
            <div class="col-sm-12" id="professionnel_sante_div_results"></div>
            <div class="col-sm-12">
                <div class="form-group row">
                    <label for="code_ets_input" class="col-sm-1 col-form-label-sm">Code</label>
                    <div class="col-sm-2">
                        <input type="text" class="form-control form-control-sm" id="code_ps_input" maxlength="9" value="<?php if(!empty($facture['PS'])){echo $facture['PS'];}else{if($type['CODE'] != 'MED' && !empty($facture['NUM_FS_INITIALE'])){echo $facture_initiale['PS'];}} ?>" placeholder="" autocomplete="off" <?php if(!empty($facture['STATUT']) || ($type['CODE'] == 'EXP' || $type['CODE'] == 'HOS' || $type['CODE'] == 'MED')){echo 'required';} ?> />
                    </div>
                    <label for="nom_ets_input" class="col-sm-2 col-form-label-sm">Nom & Prénom(s)</label>
                    <div class="col-sm-7">
                        <input type="text" class="form-control form-control-sm" id="nom_ps_input" value="<?php
                        if($type['CODE'] == 'MED' && !empty($ps_initiale['nom_prenom'])){
                            echo $ps_initiale['nom_prenom'];
                        }else{
                            if(!empty($facture['PS'])){
                                echo $ps['nom_prenom'];
                            }
                        }
                        ?>" placeholder="" autocomplete="off" <?php if(!empty($facture['STATUT'])){echo 'required';} ?> />
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-sm-3"></div>
                    <label for="specialite_input" class="col-sm-2 col-form-label-sm">Spécialité médicale</label>
                    <div class="col-sm-2">
                        <input type="text" class="form-control form-control-sm" id="code_specialite_input" value="<?php if($type['CODE'] == 'MED' && !empty($ps_initiale['code_specialite'])){echo $ps_initiale['code_specialite'];}else{if(!empty($facture['PS'])){echo $ps['code_specialite'];}} ?>" placeholder="Code" autocomplete="off" required readonly />
                    </div>
                    <div class="col-sm-5">
                        <input type="text" class="form-control form-control-sm" id="libelle_specialite_input" value="<?php if($type['CODE'] == 'MED' && !empty($ps_initiale['libelle_specialite'])){echo $ps_initiale['libelle_specialite'];}else{if(!empty($facture['PS'])){echo $ps['libelle_specialite'];}} ?>" placeholder="Libellé" autocomplete="off" required readonly />
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="div_factures_affections" <?php if(($type['CODE'] == 'AMB'  && (empty($facture['STATUT'])) || $facture['STATUT']=='N') || $type['CODE'] == 'EXP') {echo 'hidden';} ?>>
        <p class="titres_factures_p_dark">Affection</p>
        <div class="row">
            <div class="col-sm-12" id="affections_div_results"></div>
            <div class="col-sm-12">
                <div class="form-group row">
                    <label for="code_affection1_input" class="col-sm-4 col-form-label-sm">Code Affection 1</label>
                    <div class="col-sm-2">
                        <input type="text" class="form-control form-control-sm code_affection_input" id="code_affection1_input" value="<?php if($type['CODE'] != 'AMB' && $type['CODE'] != 'DEN' && isset($facture_initiale['AFFECTION1'])){echo trim($facture_initiale['AFFECTION1']);}else{echo trim($facture['AFFECTION1']);} ?>" placeholder="" maxlength="3" autocomplete="off" />
                    </div>
                    <label for="code_affection2_input" class="col-sm-4 col-form-label-sm">Code Affection 2</label>
                    <div class="col-sm-2">
                        <input type="text" class="form-control form-control-sm code_affection_input" id="code_affection2_input" placeholder="" value="<?php if($type['CODE'] != 'AMB' && $type['CODE'] != 'DEN' && isset($facture_initiale['AFFECTION2'])){echo trim($facture_initiale['AFFECTION2']);}else{echo trim($facture['AFFECTION2']);} ?>" maxlength="3" autocomplete="off" />
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div>
        <p class="titres_factures_p_dark">Prestations</p>
        <div class="row">
            <div class="col-sm-12" id="prestations_div_results"></div>
            <div class="col-sm-12">
                <table class="prestation_table">
                    <thead>
                    <tr>
                        <th width="5">N°</th>
                        <th width="125">CODE</th>
                        <th>DESIGNATION</th>
                        <th width="100" <?php if($type['CODE'] == 'EXP' || $type['CODE'] == 'MED') {echo 'hidden';} ?>>DEBUT</th>
                        <th width="100" <?php if($type['CODE'] == 'EXP' || $type['CODE'] == 'MED') {echo 'hidden';} ?>>FIN</th>
                        <th width="70">PRIX UN.</th>
                        <th width="65" <?php if($type['CODE'] != 'MED') {echo 'hidden';} ?>>QTE PRESC.</th>
                        <th width="65" <?php if($type['CODE'] == 'DEN') {echo 'hidden';} ?>>QTE <?php if($type['CODE'] == 'MED') {echo 'SERV.';} ?></th>
                        <th width="70" <?php if($type['CODE'] != 'DEN') {echo 'hidden';} ?>>N° DENT</th>
                        <th width="70">P.B.CMU</th>
                        <th width="80">PART CMU</th>
                        <th width="70">P.B.AC</th>
                        <th width="80">PART AC</th>
                        <th width="80">PART ASS.</th>
                        <th width="100">PRIX TOT.</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if($type['CODE'] == 'EXP' || $type['CODE'] == 'HOS') {
                        $nb_lignes = 10;
                    }else {
                        $nb_lignes = 3;
                    }
                    $actes = $FACTURES->trouver_facture_liste_actes($facture['FEUILLE']);
                    $montant = 0;
                    $montant_base = 0;
                    $part_cmu = 0;
                    $part_ac = 0;
                    $part_assure = 0;

                    for($a = 1; $a <= $nb_lignes; $a++) {
                        $num = $a - 1;
                        if(!empty($actes[$num]['CODE'])) {
                            $acte[$num] = $FACTURES->trouver_facture_acte($facture['FEUILLE'],strtoupper(trim($actes[$num]['CODE'])));
                        }
                        if($facture['STATUT'] == 'R' && !empty($actes[$num]['MOTIF_REJET'])) {
                            $rejet[$num] = $MOTIFSREJETS->trouver($actes[$num]['MOTIF_REJET']);
                        }
                        else{
                            $rejet[$num]['LIBELLE'] = '';
                        }
                        ?>
                        <tr class="tp_infos" data-toggle="tooltip" data-placement="top" title="<?=$rejet[$num]['LIBELLE'];?>" >
                            <td><b><?= $a;?></b></td>
                            <td><input type="text" id="code_acte_<?= $a;?>_input" value="<?php if(!empty($acte[$num]['CODE'])){echo $acte[$num]['CODE'];};?>" class="form-control form-control-sm code_acte_input <?php if(isset($actes[$num]['MOTIF_REJET']) && !empty($actes[$num]['MOTIF_REJET'])){echo 'is-invalid';} ?>" maxlength="<?php if($type['CODE'] == 'MED'){echo 13;}else {echo 7;} ?>" autocomplete="off" placeholder="" <?php if($a == 1){echo 'required';} ?> /></td>
                            <td><input type="text" id="designation_<?= $a;?>_input" value="<?php if(!empty($acte[$num]['CODE'])){echo $acte[$num]['LIBELLE'];};?>" class="form-control form-control-sm nom_acte_input <?php if(isset($actes[$num]['MOTIF_REJET']) && !empty($actes[$num]['MOTIF_REJET'])){echo 'is-invalid';} ?>" autocomplete="off" placeholder="" <?php if($a == 1){echo 'required';} ?> /></td>
                            <td <?php if($type['CODE'] == 'EXP' || $type['CODE'] == 'MED') {echo 'hidden';} ?>><input type="text" id="date_debut_<?= $a;?>_input" value="<?php if(!empty($acte[$num]['CODE'])){echo date('d/m/Y',strtotime($acte[$num]['DATE_DEBUT']));}?>" class="form-control form-control-sm datepicker date_debut_input" autocomplete="off" placeholder="" readonly /></td>
                            <td <?php if($type['CODE'] == 'EXP' || $type['CODE'] == 'MED') {echo 'hidden';} ?>><input type="text" id="date_fin_<?= $a;?>_input" value="<?php if(!empty($acte[$num]['CODE'])){echo date('d/m/Y',strtotime($acte[$num]['DATE_FIN']));}?>" class="form-control form-control-sm datepicker date_fin_input" autocomplete="off" placeholder="" readonly /></td>
                            <td>
                                <input type="text" id="prix_unitaire_<?= $a;?>_input" value="<?php if(!empty($acte[$num]['CODE'])){echo $acte[$num]['MONTANT'];}else {echo 0;}?>" class="form-control form-control-sm prix_unitaire_input" autocomplete="off" placeholder=""/>
                            </td>
                            <td <?php if($type['CODE'] != 'MED') {echo 'hidden';} ?>>
                                <select class="form-control form-control-sm quantite_presc_input" id="quantite_presc_<?= $a;?>_input" <?php if($a == 1){echo 'required';} ?>>
                                    <?php
                                    for ($b = 1; $b <= 100; $b++) {
                                        ?>
                                        <option value="<?= $b; ?>" <?php if(!empty($acte[$num]['CODE']) && $acte[$num]['QUANTITE_PRESCRITE'] == $b){echo 'selected';};?>><?= $b;?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </td>
                            <td <?php if($type['CODE'] == 'DEN') {echo 'hidden';} ?>>
                                <select class="form-control form-control-sm quantite_input" id="quantite_<?= $a;?>_input" <?php if($a == 1){echo 'required';} ?>>
                                    <?php
                                    for ($b = 1; $b <= 100; $b++) {
                                        ?>
                                        <option value="<?= $b; ?>" <?php if(!empty($acte[$num]['CODE']) && $acte[$num]['QUANTITE'] == $b){echo 'selected';};?>><?= $b;?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </td>
                            <td <?php if($type['CODE'] != 'DEN') {echo 'hidden';} ?>>
                                <select class="form-control form-control-sm num_dent_input" id="num_dent_<?= $a;?>_input">
                                    <?php
                                    for ($c = 0; $c <= 85; $c++) {
                                        ?>
                                        <option value="<?= $c; ?>" <?php if(!empty($acte[$num]['CODE']) && $acte[$num]['NUM_DENT'] == $c){echo 'selected';};?>><?= $c;?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </td>
                            <td>
                                <input id="prix_unitaire_base_<?= $a;?>_input" value="<?php if(!empty($acte[$num]['CODE'])){echo $acte[$num]['MONTANT_BASE'];}else {echo 0;}?>" class="form-control form-control-sm prix_unitaire_base_input" autocomplete="off"/  readonly>
                            </td>
                            <td>
                                <input type="text" id="montant_cmu_<?= $a;?>_input" value="<?php if(!empty($acte[$num]['CODE'])){echo $acte[$num]['PART_RO'] * $acte[$num]['QUANTITE'];}else {echo 0;}?>" class="form-control form-control-sm montant_cmu_input" autocomplete="off" placeholder="" readonly />
                            </td>
                            <td>
                                <input id="prix_unitaire_base_ac_<?= $a;?>_input" value="<?php if(!empty($acte[$num]['CODE'])){echo $acte[$num]['MONTANT_BASE_AC'];}else {echo 0;}?>" class="form-control form-control-sm prix_unitaire_base_ac_input" autocomplete="off"/  readonly>
                            </td>
                            <td>
                                <input type="text" id="montant_ac_<?= $a;?>_input" value="<?php if(!empty($acte[$num]['CODE'])){echo $acte[$num]['PART_RC'];}else {echo 0;}?>" class="form-control form-control-sm montant_ac_input" autocomplete="off" placeholder="" readonly />
                            </td>
                            <td>
                                <input type="text" id="montant_ass_<?= $a;?>_input" value="<?php if(!empty($acte[$num]['CODE'])){echo $acte[$num]['PART_ASSURE'];}else {echo 0;}?>" class="form-control form-control-sm montant_ass_input" autocomplete="off" placeholder="" readonly />
                            </td>
                            <td>
                                <input type="text" id="montant_<?= $a;?>_input" value="<?php if(!empty($acte[$num]['CODE'])){echo $acte[$num]['MONTANT'] * $acte[$num]['QUANTITE'];}else {echo 0;}?>" class="form-control form-control-sm montant_input" autocomplete="off" placeholder="" readonly />
                                <input type="hidden" id="montant_base_<?= $a;?>_input" value="<?php if(!empty($acte[$num]['CODE'])){echo $acte[$num]['MONTANT_BASE'] * $acte[$num]['QUANTITE'];}else {echo 0;}?>" class="form-control form-control-sm montant_base_input" autocomplete="off" placeholder="" readonly />
                                <input type="hidden" id="taux_remboursement_<?= $a;?>_input" value="<?php if(!empty($acte[$num]['CODE'])){echo $acte[$num]['TAUX_RO'];}else {echo 0;}?>" class="form-control form-control-sm taux_remboursement_input" autocomplete="off" placeholder="" readonly />
                                <input type="hidden" id="taux_remboursement_ac_<?= $a;?>_input" value="<?php if(!empty($acte[$num]['CODE'])){echo $acte[$num]['TAUX_RC'];}else {echo 0;}?>" class="form-control form-control-sm taux_remboursement_ac_input" autocomplete="off" placeholder="" readonly />
                            </td>
                        </tr>
                        <?php
                        if(!empty($acte[$num]['CODE'])) {
                            $montant = $montant + ($acte[$num]['MONTANT'] * $acte[$num]['QUANTITE']);
                            $montant_base = $montant + ($acte[$num]['MONTANT'] * $acte[$num]['QUANTITE']);
                            if((($acte[$num]['MONTANT'] * $acte[$num]['QUANTITE'])-($acte[$num]['MONTANT_BASE'] * $acte[$num]['QUANTITE']))> 0){
                                $montant_base = ($acte[$num]['MONTANT_BASE'] * $acte[$num]['QUANTITE']);
                                $part_cmu = $part_cmu + intval(round($montant_base * 0.7));
                                //$part_cmu = intval(round($mont * 0.7));
                            }
                            else{
                                $part_cmu = intval(round($montant * 0.7));
                            }
                            if($facture['CODE_OGD_AFFILIATION'] == '03016000') {
                                $part_ac = $montant - $part_cmu;
                            }else {
                                $part_ac = 0;
                            }
                            $part_assure = ($montant - ($part_cmu +$part_ac));
                        }

                    }
                    ?>
                    </tbody>
                </table><br />
                <table width="100%">
                    <tr>
                        <td class="align_right"><b>Total</b></td>
                        <td width="100"><input type="text" aria-label="" id="total_input" value="<?= number_format($montant,'0','',' ');?>" class="form-control form-control-sm" autocomplete="off" placeholder="" readonly /></td>
                    </tr>
                    <tr>
                        <td class="align_right"><b>Part CMU</b></td>
                        <td width="100"><input type="text" aria-label="" id="part_cmu_input" value="<?= number_format($part_cmu,'0','',' ');?>" class="form-control form-control-sm" autocomplete="off" placeholder="" readonly /></td>
                    </tr>
                    <tr>
                        <td class="align_right"><b>Part AC</b></td>
                        <td width="100"><input type="text" aria-label="" id="part_ac_input" value="<?= number_format($part_ac,'0','',' ');?>" class="form-control form-control-sm" autocomplete="off" placeholder="" readonly /></td>
                    </tr>
                    <tr>
                        <td class="align_right text-success"><b>Part assuré</b></td>
                        <td width="100"><input type="text" aria-label="" id="part_assure_input" value="<?= number_format($part_assure,'0','',' ');?>" style="font-weight: bold; font-size: 1.3rem" class="form-control form-control-sm border-success" autocomplete="off" placeholder="" readonly /></td>
                    </tr>
                    <?php
                    if($type['CODE'] == 'EXP' || $type['CODE'] == 'MED') {

                        ?>
                        <tr <?php if(empty($facture['STATUT']) ||  $facture['STATUT'] == 'N') {echo 'hidden';} ?>>
                            <td align="right"><b>N° Reçu</b></td>
                            <td width="100"><input type="text" id="num_recu_input" value="<?= $facture['NUM_RECU'];?>" class="form-control form-control-sm" autocomplete="off" placeholder="" /></td>
                        </tr>
                        <?php
                    }
                    ?>
                </table><br />
            </div>
        </div>
    </div>
    <?php
    if($type['CODE'] == 'AMB'){
        if(empty($facture['STATUT']) || $facture['STATUT'] == 'N') {
            $affichage_sortie = 'none';
        }else {
            $affichage_sortie = 'block';
        }
    }elseif ($type['CODE'] == 'MED' || $type['CODE'] == 'EXP') {
        $affichage_sortie = 'none';
    }else {
        $affichage_sortie = 'block';
    }
    ?>
   <div style="display: <?= $affichage_sortie;?>">
       <p class="titres_factures_p_dark">Sortie</p>
       <div class="row">
           <div class="col-sm-12" id="sortie_div_results"></div>
           <div class="col-sm-12">
               <div class="form-group row">
                   <div class="col">
                       Type
                   </div>
                   <div class="col">
                       <div class="form-check form-check-inline">
                           <input class="form-check-input" type="radio" name="type_sortie_input" id="sor_input" value="SOR" <?php if($facture['MOTIF_FIN'] == 'SOR'){echo 'checked';}elseif (empty($facture['motif_fin'])){echo 'checked';} ?> />
                           <label class="form-check-label col-form-label-sm" for="sor_input">EXEAT</label>
                       </div>
                   </div>
                   <div class="col">
                       <div class="form-check form-check-inline">
                           <input class="form-check-input" type="radio" name="type_sortie_input" id="exa_input" value="EXA" <?php if($facture['MOTIF_FIN'] == 'EXA'){echo 'checked';} ?> />
                           <label class="form-check-label col-form-label-sm" for="exa_input">EXAMENS</label>
                       </div>
                   </div>
                   <div class="col">
                       <div class="form-check form-check-inline">
                           <input class="form-check-input" type="radio" name="type_sortie_input" id="ref_input" value="REF" <?php if($facture['MOTIF_FIN'] == 'REF'){echo 'checked';} ?> />
                           <label class="form-check-label col-form-label-sm" for="ref_input">REFERE</label>
                       </div>
                   </div>
                   <div class="col">
                       <div class="form-check form-check-inline">
                           <input class="form-check-input" type="radio" name="type_sortie_input" id="hos_input" value="HOS" <?php if($facture['MOTIF_FIN'] == 'HOS'){echo 'checked';} ?> />
                           <label class="form-check-label col-form-label-sm" for="hos_input">HOSPITALISATION</label>
                       </div>
                   </div>
                   <div class="col">
                       <div class="form-check form-check-inline">
                           <input class="form-check-input" type="radio" name="type_sortie_input" id="dec_input" value="DEC" <?php if($facture['MOTIF_FIN'] == 'DEC'){echo 'checked';} ?> />
                           <label class="form-check-label col-form-label-sm" for="dec_input">DECES</label>
                       </div>
                   </div>
                   <label for="code_ets_input" class="col-sm-1 col-form-label-sm">Date</label>
                   <div class="col-sm-2">
                       <input type="text" class="form-control form-control-sm datepicker" id="date_sortie_input" value="<?php if(!empty($facture['DATE_FIN'])){echo date('d/m/Y',strtotime($facture['DATE_FIN']));}else {echo date('d/m/Y',time());} ?>" placeholder="Date sortie" autocomplete="off" />
                   </div>
               </div>
           </div>
       </div>
   </div>
    <hr />
    <table width="100%">
        <?php
            if(strstr(ACTIVE_URL,'CentreSaisie')) {
                $url_redic = URL.'centre-saisie/';
            }else{
                $url_redic = URL.'agent/';
            }
        ?>
        <tr>
            <td></td>

            <td width="100"><a href="<?= $url_redic;?>" class="btn btn-block btn-sm btn-dark"><i class="fa fa-chevron-circle-left"></i> Sortir</a></td>
            <td width="100"><a href="<?= $url_redic.'facture-annulation.php?num='.$facture['FEUILLE'];?>" class="btn btn-block btn-sm btn-danger"><i class="fa fa-trash"></i> Annuler</a></td>
            <td width="100" id="td_button_adjudication">
                <button type="button" id="soummettre_adjudication_btn" class="btn btn-block btn-sm btn-info"><i class="fa fa-hand-paper"></i>Soumettre</button>
            </td>
            <td width="100" >
                <button type="submit" id="validation_btn" class="btn btn-block btn-sm btn-success" <?php if(!$facture['DATE_EDIT']){echo "disabled";}?>><i class="fa fa-check-circle"></i> Valider</button>
            </td>
        </tr>
    </table><br />
</form>
