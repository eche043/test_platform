<form id="form_maj_cmr">
    <?php
    if(in_array('CSAI',$modules) && ACTIVE_URL == URL.'_configs/Includes/Pages/CentreSaisie/Cmr.php' ){
    ?>
    <div class="form-row">
        <div class="form-group col-md-2">
            <input type="text" aria-label="Code Centre" class="form-control form-control-sm" id="code_ets_input" maxlength="9" placeholder="Code Centre" autocomplete="off" disabled/>
        </div>
        <div class="form-group col-md-7">
            <input type="text" aria-label="Raison Sociale" class="form-control form-control-sm" id="raison_sociale_ets_input" placeholder="Raison Sociale" required/>
        </div>
    </div>
    <?php
    }
    ?>
    <div class="form-row">
        <div class="form-group col-md-2">
            <input type="text" aria-label="Numéro Sécu" class="form-control form-control-sm" id="num_secu_input" maxlength="13" placeholder="Numéro Sécu" autocomplete="off" required/>
        </div>
        <div class="form-group col-md-2">
            <input type="text" aria-label="Nom" class="form-control form-control-sm" id="nom_input" placeholder="Nom" autocomplete="off" disabled/>
        </div>
        <div class="form-group col-md-5">
            <input type="text" aria-label="Prénom(s)" class="form-control form-control-sm" id="prenom_input"  placeholder="Prénoms" autocomplete="off" disabled/>
        </div>
        <div class="form-group col-md-2">
            <input type="text" aria-label="Date de Naissance" class="form-control form-control-sm" id="date_naissance_input" placeholder="Date de Naissance" autocomplete="off" disabled />
        </div>
        <div class="form-group col-md-1">
            <button type="submit" id="button_maj_cmr" class="btn btn-sm btn-info btn-block"><i class="fa fa-check"></i> Valider</button>
        </div>
    </div>
</form>