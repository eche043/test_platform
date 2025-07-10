<nav class="navbar navbar-light">
    <a class="navbar-brand"><button type="button" id="button_modal_demande_entente_prealable" data-toggle="modal" data-target="#modalEntente" class="btn btn-sm btn-success"><i class="fa fa-plus"></i> Nouvelle Demande</button></a>
    <form id="form_search_entente_prealable" class="form-inline">
        <input type="text" aria-label="Numéro Entente préalable" class="form-control form-control-sm mr-sm-2" id="id_entente_input" placeholder="Numéro Entente préalable" autocomplete="off" />
        <button type="submit" id="button_search_entente" class="btn btn-sm btn-info my-2 my-sm-0"><i class="fa fa-search"></i> Rechercher</button>
    </form>
</nav>
<div class="modal fade" id="modalEntente" tabindex="-1" role="dialog" aria-labelledby="modalEntenteLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEntenteLabel">Nouvelle Demande d'Entente Préalable</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <p class="align_center" id="p_resultats_ep"></p>
            <form id="form_nouvelle_demande_entente">
                <div class="modal-body">
                    <div class="form-group">
                        <?php
                        if(in_array('CSAI',$modules) && ACTIVE_URL == URL.'_configs/Includes/Pages/CentreSaisie/EntentesPrealables.php' ){
                            ?>
                            <div class="form-group" id="">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <label for="code_ets_input" class="col-form-label">Code établissement:</label>
                                        <input type="text" class="form-control form-control-sm" maxlength="9" name="code_ets_input" id="code_ets_input" disabled>
                                    </div>
                                    <div class="col-sm-9">
                                        <label for="raison_sociale_input" class="col-form-label">Raison Sociale:</label>
                                        <input class="form-control form-control-sm" name="raison_sociale_input" id="raison_sociale_ets_input" required/>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-6 d-none">
                                <label for="new_num_secu_input" class="col-form-label">Type Demande:</label>
                                <select class="form-control form-control-sm" name="type_entente_prealable_select" id="type_entente_prealable_select">
                                    <option value="">Sélectionner</option>
                                    <option value="EXP">BIOLOGIE - IMAGERIE</option>
                                    <option value="HOS">HOSPITALISATION</option>
                                </select>
                            </div>
                            <div class="col-sm-6 d-none" id="div_type_hospitalisation">
                                <label for="new_nom_input" class="col-form-label" >Type d'Hospitalisation:</label>
                                <select class="form-control form-control-sm" name="type_hospitalisation_select" id="type_hospitalisation_select">
                                    <option value="">Sélectionner</option>
                                    <option value="HC">HOSPITALISATION CHIRURGICALE</option>
                                    <option value="HM">HOSPITALISATION MEDICALE</option>
                                    <option value="HO">HOSPITALISATION OBSTETRICALE</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group " id="div_numero_securite_sociale">
                        <div class="row">
                            <div class="col-sm-6">
                                <label for="new_num_secu_input" class="col-form-label">Numero Sécu:</label>
                                <input type="text" class="form-control form-control-sm" maxlength="13" id="numero_securite_sociale_input">
                            </div>
                            <div class="col-sm-6">
                                <label for="new_nom_input" class="col-form-label">Nom:</label>
                                <input class="form-control form-control-sm ep_libelle_acte" name="new_nom_input" id="new_nom_input" disabled />
                            </div>
                            <div class="col-sm-8">
                                <label for="new_prenom_input" class="col-form-label">Prénoms:</label>
                                <input type="text" class="form-control form-control-sm" id="new_prenom_input" disabled>
                            </div>
                            <div class="col-sm-4">
                                <label for="new_date_naissance_input" class="col-form-label">Date de Naissance:</label>
                                <input type="text" class="form-control form-control-sm" id="new_date_naissance_input" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="form-group d-none" id="div_numero_feuille_initiale" hidden>
                        <div class="row">
                            <div class="col-sm-4">
                                <label for="new_date_naissance_input" class="col-form-label">Numéro de Feuille de soins Initial:</label>
                                <input class="form-control form-control-sm" type="text" name="numero_feuille_initiale_input" id="numero_feuille_initiale_input" autocomplete="off"/>
                            </div>
                        </div>
                    </div>
                    <div class="form-group" id="div_resultats_ep">
                    </div>
                    <hr>
                    <div class="form-group " id="div_acte_1">
                        <label for="ep_code_acte_1_input" class="col-form-label">Code Acte 1</label>
                        <div class="row">
                            <div class="col-sm-3">
                                <input class="form-control form-control-sm ep_code_acte" name="ep_code_acte_1_input" id="ep_code_acte_1_input" autocomplete="off" />
                            </div>
                            <div class="col-sm-9">
                                <input class="form-control form-control-sm ep_libelle_acte" name="ep_libelle_acte_1_input" id="ep_libelle_acte_1_input" disabled />
                            </div>
                        </div>
                    </div>
                    <div class="form-group d-none" id="div_acte_2">
                        <label for="ep_code_acte_2_input" class="col-form-label">Code Acte 2</label>
                        <div class="row">
                            <div class="col-sm-3">
                                <input class="form-control form-control-sm ep_code_acte" name="ep_code_acte_2_input" id="ep_code_acte_2_input" autocomplete="off" />
                            </div>
                            <div class="col-sm-9">
                                <input class="form-control form-control-sm ep_libelle_acte" name="ep_libelle_acte_2_input" id="ep_libelle_acte_2_input" disabled />
                            </div>
                        </div>
                    </div>
                    <div class="form-group d-none" id="div_acte_3">
                        <label for="ep_code_acte_3_input" class="col-form-label">Code Acte 3</label>
                        <div class="row">
                            <div class="col-sm-3">
                                <input class="form-control form-control-sm ep_code_acte" name="ep_code_acte_3_input" id="ep_code_acte_3_input" autocomplete="off" />
                            </div>
                            <div class="col-sm-9">
                                <input class="form-control form-control-sm ep_libelle_acte" name="ep_libelle_acte_3_input" id="ep_libelle_acte_3_input" disabled />
                            </div>
                        </div>
                    </div>
                    <div class="form-group " id="div_motif_demande_entente_prealable">
                        <label for="motif_nouvelle_demande_attestation_text" class="col-form-label">Motif de la Demande:</label>
                        <textarea class="form-control form-control-sm" id="ep_motif_demande_input" required></textarea>
                    </div>
                </div>
                <div class="modal-footer" id="div_validation">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Fermer</button>
                    <?php
                    if(in_array('AGAC',$modules) && ACTIVE_URL == URL.'_configs/Includes/Pages/Agent/EntentesPrealables.php' )
                    {
                        ?>
                        <input type="hidden" id="code_ets_input" value="<?=$user_ets["CODE_ETS"];?>">
                        <?php
                    }
                    if(in_array('COORD',$modules) && ACTIVE_URL == URL.'_configs/Includes/Pages/CentreCoordination/EntentesPrealables.php' )
                    {
                        ?>
                        <input type="hidden" id="code_ets_input" value="<?=$ets["CODE_ETS"];?>">
                        <?php
                    }
                    ?>
                    <button type="submit" id="button_nouvelle_demande_attestation" class="btn btn-primary btn-sm"><i class="fa fa-check"></i> Valider</button>
                </div>
            </form>
        </div>
    </div>
</div>
