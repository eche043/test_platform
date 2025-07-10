<form id="form_connexion">
    <div class="form-row">
        <div class="form-group col-md-12">
            <input type="text" class="form-control form-control-sm" id="username_input" maxlength="100" placeholder="Code utilisateur / Email" autocomplete="off" />
        </div>
        <div class="form-group col-md-12">
            <input type="password" class="form-control form-control-sm" id="password_input" maxlength="40" placeholder="Mot de passe" autocomplete="off" />
        </div>
        <div class="form-group col-md-12">
            <button type="submit" id="button_connexion" class="btn btn-sm btn-info btn-block"><i class="fa fa-exchange-alt"></i> Connexion</button>
        </div>
        <div class="form-group col-md-12">
            <p class="align_right" id="p_mot_de_passe_oublie">Mot de passe <a id="a_mot_de_passe_oublie" href="#">oublié</a> ?</p>
            <p class="align_center" id="p_creer_compte">Assuré CMU, vous n'avez pas de compte ?  <br /><a id="a_compte" href="">créez-en</a> un ici</p>
        </div>
        <div class="form-group col-md-12">
            <p class="align_center h6"><a target="_blank" style="font-weight: bold;" class='text-danger' id="a_demande_duplicata" href="<?=URL.'duplicata.php'?>">Demande de Duplicata</a></p>
        </div>
    </div>
</form>