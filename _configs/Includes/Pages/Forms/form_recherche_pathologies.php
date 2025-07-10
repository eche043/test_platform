<?php
/**
 * Created by PhpStorm.
 * User: mohamed.kone
 * Date: 07/02/2020
 * Time: 10:23
 */
?>
<form id="form_recherche_pathologies">
    <div class="form-group row">
        <div class="col-sm-2">
            <input type="text" class="form-control form-control-sm" placeholder="Code" id="code_input" autocomplete="off" maxlength="9" />
        </div>
        <div class="col-sm-9">
            <input type="text" class="form-control form-control-sm" placeholder="Libellé" id="libelle_input" autocomplete="off" maxlength="100" />
        </div>
        <div class="col-sm-1">
            <button type="submit" class="btn btn-sm btn-success btn-block"><i class="fa fa-search"></i></button>
        </div>
    </div>
</form>
