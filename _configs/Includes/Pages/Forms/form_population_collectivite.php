<div class="modal fade" id="modalPopulationCollectivite" tabindex="-1" role="dialog" aria-labelledby="modalPopulationCollectiviteLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPopulationCollectiviteLabel">Individu</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <p class="align_center" id="p_resultats_population_collectivite"></p>
            <form id="form_population_collectivite">
                <div class="modal-body">
                    <div class="form-group" id="div_type_individu">
                        <div class="row">
                            <div class="col-sm-4">
                                <label for="type_individu_select" class="col-form-label">Type Individu:</label>
                                <select class="form-control form-control-sm" name="type_individu_select" id="type_individu_select" required>
                                    <option value="">Sélectionner</option>
                                    <option value="T">Payeur (Ouvrant-droit)</option>
                                    <option value="C">Conjoint(e)</option>
                                    <option value="E">Enfant</option>
                                </select>
                            </div>
                        </div>
                        <br><hr>
                    </div>
                    <div class="form-group d-none" id="div_infos_payeur">
                        <h5>Informations sur le payeur / ouvrant-droit</h5><br>
                        <div class="row">
                            <div class="col-sm-4">
                                <label for="numero_secu_payeur_input" class="col-form-label">Numéro Sécu:</label>
                                <input class="form-control form-control-sm" type="text" name="numero_secu_payeur_input" id="numero_secu_payeur_input" maxlength="13" autocomplete="off"/>
                            </div>
                            <div class="col-sm-4">
                                <label for="num_matricule_ogd_payeur_input" class="col-form-label">Numéro Matricule OGD (CNPS):</label>
                                <input class="form-control form-control-sm" type="text" name="num_matricule_ogd_payeur_input" id="num_matricule_ogd_payeur_input" autocomplete="off"/>
                            </div>
                            <div class="col-sm-4">
                                <label for="num_matricule_entreprise_payeur_input" class="col-form-label">Numéro Matricule Entreprise:</label>
                                <input class="form-control form-control-sm" type="text" name="num_matricule_entreprise_payeur_input" id="num_matricule_entreprise_payeur_input" autocomplete="off"/>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <label for="nom_payeur_input" class="col-form-label">Nom:</label>
                                <input class="form-control form-control-sm" name="nom_payeur_input" id="nom_payeur_input" />
                            </div>
                            <div class="col-sm-8">
                                <label for="prenom_payeur_input" class="col-form-label">Prénoms:</label>
                                <input type="text" class="form-control form-control-sm"  name="prenom_payeur_input" id="prenom_payeur_input" />
                            </div>
                            <div class="col-sm-4">
                                <label for="date_naissance_payeur_input" class="col-form-label">Date de Naissance:</label>
                                <input type="text" class="form-control form-control-sm datepicker" name="date_naissance_payeur_input" id="date_naissance_payeur_input" value="" autocomplete="off" readonly/>
                            </div>
                        </div>
                        <br><hr>
                    </div>
                    <div class="form-group d-none" id="div_infos_beneficiaire">
                        <h5> Informations sur le beneficiaire / ayant-droit</h5><br>
                        <div class="row">
                            <div class="col-sm-4">
                                <label for="numero_secu_beneficiaire_input" class="col-form-label">Numéro Sécu:</label>
                                <input class="form-control form-control-sm" type="text" name="numero_secu_beneficiaire_input" id="numero_secu_beneficiaire_input" maxlength="13" autocomplete="off"/>
                            </div>
                            <div class="col-sm-4">
                                <label for="num_matricule_ogd_beneficiaire_input" class="col-form-label">Numéro Matricule OGD (CNPS):</label>
                                <input class="form-control form-control-sm" type="text" name="num_matricule_ogd_beneficiaire_input" id="num_matricule_ogd_beneficiaire_input" autocomplete="off"/>
                            </div>
                            <div class="col-sm-4">
                                <label for="num_matricule_entreprise_beneficiaire_input" class="col-form-label">Numéro Matricule Entreprise:</label>
                                <input class="form-control form-control-sm" type="text" name="num_matricule_entreprise_beneficiaire_input" id="num_matricule_entreprise_beneficiaire_input" autocomplete="off"/>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <label for="nom_beneficiaire_input" class="col-form-label">Nom:</label>
                                <input class="form-control form-control-sm" name="nom_beneficiaire_input" id="nom_beneficiaire_input" />
                            </div>
                            <div class="col-sm-8">
                                <label for="prenom_beneficiaire_input" class="col-form-label">Prénoms:</label>
                                <input type="text" class="form-control form-control-sm" name="prenom_beneficiaire_input" id="prenom_beneficiaire_input" />
                            </div>
                            <div class="col-sm-4">
                                <label for="date_naissance_beneficiaire_input" class="col-form-label">Date de Naissance:</label>
                                <input type="text" class="form-control form-control-sm datepicker" name="date_naissance_beneficiaire_input" id="date_naissance_beneficiaire_input" value="" autocomplete="off" readonly />
                            </div>
                        </div>
                        <br><hr>
                    </div>
                    <div class="form-group d-none" id="div_infos_complementaires">
                        <h5> Informations complémentaires</h5><br>
                        <div class="row">
                            <div class="col-sm-4">
                                <label for="genre_select" class="col-form-label">Genre:</label>
                                <select class="form-control form-control-sm" name="genre_select" id="genre_select">
                                    <option value="">Sélectionner</option>
                                    <option value="M">Masculin</option>
                                    <option value="F">Féminin</option>
                                </select>
                            </div>
                            <div class="col-sm-4">
                                <label for="civilite_select" class="col-form-label">Civilite:</label>
                                <select class="form-control form-control-sm" name="civilite_select" id="civilite_select">
                                    <option value="">Sélectionner</option>
                                    <option value="M">Monsieur</option>
                                    <option value="MME">Madame</option>
                                    <option value="MLE">Mademoiselle</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <label for="lieu_naissance_input" class="col-form-label">Lieu de naissance:</label>
                                <input class="form-control form-control-sm" name="lieu_naissance_input" id="lieu_naissance_input"/>
                            </div>
                            <div class="col-sm-4">
                                <label for="lieu_resideance_input" class="col-form-label">Lieu de residence:</label>
                                <input class="form-control form-control-sm" name="lieu_resideance_input" id="lieu_resideance_input"/>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer d-none" id="div_validation" >
                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Fermer</button>
                        <input type="hidden" id="id_population_input" value=""/>
                        <button type="submit" id="button_enregistrement_population" class="btn btn-primary btn-sm"><i class="fa fa-check"></i> Valider</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>