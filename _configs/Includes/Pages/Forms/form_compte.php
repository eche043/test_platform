
<form id="form_compte">
    <div class="form-row">
        <div class="form-group col-md-12">
            <input type="text" class="form-control form-control-sm" id="num_secu_input" maxlength="13" placeholder="N° Sécu" autocomplete="off" required />
        </div>
        <div class="form-group col-md-12">
            <input type="text" class="form-control form-control-sm" id="nom_input" maxlength="30" placeholder="Nom" autocomplete="off" readonly />
        </div>
        <div class="form-group col-md-12">
            <input type="text" class="form-control form-control-sm" id="prenom_input" maxlength="50" placeholder="Prénom(s)" autocomplete="off" readonly />
        </div>
        <div class="form-group col-md-12">
            <input type="email" class="form-control form-control-sm" id="email_compte_input" maxlength="100" placeholder="Email" autocomplete="off" required />
        </div>
        <div class="form-group col-md-12">
            <input type="text" class="form-control form-control-sm" id="num_telephone_input" maxlength="10" placeholder="N° Téléphone" autocomplete="off" required />
        </div>
        <div class="form-group col-md-12">
            <button type="submit" id="button_creer" class="btn btn-sm btn-info btn-block"><i class="fa fa-check"></i> Valider</button>
        </div>
        <div class="form-group col-md-12">
            <p class="align_center" id="p_mot_de_passe_oublie">Vous avez déjà un compte ? <a id="a_connexion_1" href="#">Connectez-vous</a></p>
        </div>
    </div>
</form>