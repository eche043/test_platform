<p class="align_left">
    <button type="button" id="button_modal_demande_attestation" data-toggle="modal" data-target="#exampleModal" class="btn btn-sm btn-success"><i class="fa fa-plus"></i> Nouvelle Demande</button>
</p><br />

<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Nouvelle Demande d'Attestation de Droits</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <p class="align_center" id="p_resultats_nouvelle_demande_attestation"></p>
            <form id="form_nouvelle_demande_attestation">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="new_num_secu_input" class="col-form-label">Numero Sécu:</label>
                        <input type="text" class="form-control form-control-sm" maxlength="13" id="new_num_secu_input">
                    </div>
                    <div class="form-group">
                        <label for="new_nom_input" class="col-form-label">Nom:</label>
                        <input type="text" class="form-control form-control-sm" id="new_nom_input" disabled>
                    </div>
                    <div class="form-group">
                        <label for="new_prenom_input" class="col-form-label">Prénoms:</label>
                        <input type="text" class="form-control form-control-sm" id="new_prenom_input" disabled>
                    </div>
                    <div class="form-group">
                        <label for="new_date_naissance_input" class="col-form-label">Date de Naissance:</label>
                        <input type="text" class="form-control form-control-sm" id="new_date_naissance_input" disabled>
                    </div>
                    <div class="form-group">
                        <label for="motif_nouvelle_demande_attestation_text" class="col-form-label">Motif de la Demande:</label>
                        <textarea class="form-control form-control-sm" id="motif_nouvelle_demande_attestation_text" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Fermer</button>
                    <button type="submit" id="button_nouvelle_demande_attestation" class="btn btn-primary btn-sm"><i class="fa fa-check"></i> Valider</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!--<hr>-->
<form id="form_search_attestation_droits">
    <div class="form-row">
        <div class="form-group col-md-2">
            <input type="text" aria-label="Numéro Sécu" class="form-control form-control-sm" id="num_secu_input" maxlength="13" placeholder="Numéro Sécu" autocomplete="off" />
        </div>
        <div class="form-group col-md-2">
            <input type="text" aria-label="Nom" class="form-control form-control-sm" id="nom_input" placeholder="Nom" autocomplete="off" disabled/>
        </div>
        <div class="form-group col-md-4">
            <input type="text" aria-label="Prénom(s)" class="form-control form-control-sm" id="prenom_input"  placeholder="Prénoms" autocomplete="off" disabled/>
        </div>
        <div class="form-group col-md-2">
            <input type="text" aria-label="Date de Naissance" class="form-control form-control-sm" id="date_naissance_input" placeholder="Date de Naissance" autocomplete="off" disabled />
        </div>
        <div class="form-group col-md-2">
            <button type="submit" id="button_search_attestation" class="btn btn-sm btn-info btn-block"><i class="fa fa-search"></i> Rechercher</button>
        </div>
    </div>
</form>