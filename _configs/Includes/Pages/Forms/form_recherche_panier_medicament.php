<?php
/**
 * Created by PhpStorm.
 * User: mohamed.kone
 * Date: 07/02/2020
 * Time: 10:25
 */
?>
<form id="form_recherche_panier_medicament">
    <div class="form-group row">
        <div class="col-sm-2">
            <input type="text" class="form-control form-control-sm" placeholder="Code" id="code_input" autocomplete="off" maxlength="12" />
        </div>
        <div class="col-sm-3">
            <input type="text" class="form-control form-control-sm" placeholder="Libellé" id="libelle_input" autocomplete="off" maxlength="100" />
        </div>
        <div class="col-sm-3">
            <select class="form-control form-control-sm" id="forme_input">
                <option value="">Forme</option>
                <?php
                foreach ($forme_med as $forme){
                    echo'<option value="'.$forme["FORME"].'">'.$forme["FORME"].'</option>';
                }
                ?>
            </select>
        </div>
        <div class="col-sm-3">
            <select class="form-control form-control-sm" id="conditionnement_input">
                <option value="">Conditionnement</option>
                <?php
                foreach ($conditionnement_med as $conditionnement) {
                    echo'<option value="'.$conditionnement['CONDITIONNEMENT'].'">'.$conditionnement['CONDITIONNEMENT'].'</option>';
                }
                ?>
            </select>
        </div>
        <div class="col-sm-1">
            <button type="submit" class="btn btn-sm btn-success btn-block"><i class="fa fa-search"></i></button>
        </div>
    </div>
</form>
