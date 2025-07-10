<p id="p_resultat_consultation_droits" class="align_center"></p>
<form id="form_consultation_droits">
    <div class="form-row align-items-center">
        <div class="col-sm-12 my-1">
            <label class="sr-only" for="population_num_secu_input">N° Sécu</label>
            <div class="input-group input-group-sm">
                <input type="text" class="form-control form-control-sm" id="num_secu_input" maxlength="13" placeholder="N° Sécu" autocomplete="off" />
            </div>
        </div>
        <div class="col-sm-12 my-1">
            <label class="sr-only" for="prenom_input">Prénom</label>
            <div class="input-group input-group-sm">
                <input type="text" class="form-control form-control-sm" id="prenom_input" placeholder="Prénom" autocomplete="off" readonly />
            </div>
        </div>
        <div class="col-sm-12 my-1">
            <label class="sr-only" for="nom_input">Nom</label>
            <div class="input-group input-group-sm">
                <input type="text" class="form-control form-control-sm" id="nom_input" placeholder="Nom" autocomplete="off" readonly />
            </div>
        </div>
        <div class="col-sm-12 my-1">
            <label class="sr-only" for="date_naissance_input">Date de naissance</label>
            <div class="input-group input-group-sm">
                <input type="text" class="form-control form-control-sm" id="date_naissance_input" placeholder="Date de naissance" autocomplete="off" readonly />
            </div>
        </div>
        <div class="align-items-center col-sm my-1">
            <button type="submit" class="btn btn-primary btn-block btn-sm" id="button_consultation_droits"><i class="fa fa-check"></i> Vérifier</button>
        </div>
    </div>
</form>