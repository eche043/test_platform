<form action="" id="form_recheche_population_declaration">
    <div class="form-row align-items-center">
        <div class="col-sm-2 my-1">
            <label class="sr-only" for="search_num_matricule_input">N° Matricule</label>
            <div class="input-group input-group-sm">
                <input type="text" class="form-control form-control-sm" maxlength="13" id="search_num_matricule_input" placeholder="N° Matricule" autocomplete="off" />
            </div>
        </div>
        <div class="col-sm-2 my-1">
            <label class="sr-only" for="search_num_secu_input">N° Sécu</label>
            <div class="input-group input-group-sm">
                <input type="text" class="form-control form-control-sm" maxlength="13" id="search_num_secu_input" placeholder="N° Sécu" autocomplete="off" />
            </div>
        </div>
        <div class="col my-1">
            <label class="sr-only" for="search_nom_prenom_input">Nom & Prénom(s)</label>
            <div class="input-group input-group-sm">
                <input type="text" class="form-control form-control-sm" maxlength="100" id="search_nom_prenom_input" placeholder="Nom & Prénom(s)" autocomplete="off" />
            </div>
        </div>
        <div class="col-sm-1 my-1">
            <button type="submit" id="button_recherche" class="btn btn-success btn-sm btn-block"><i class="fa fa-search"></i></button>
        </div>
    </div>
</form>
<div id="div_resultats"></div>