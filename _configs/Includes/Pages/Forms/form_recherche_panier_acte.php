<?php
/**
 * Created by PhpStorm.
 * User: mohamed.kone
 * Date: 06/02/2020
 * Time: 22:08
 */
?>

<form id="form_recherche_panier_acte">
    <div class="form-group row">
        <div class="col-sm-2">
            <input type="text" class="form-control form-control-sm" placeholder="Code" id="code_input" autocomplete="off" maxlength="9" />
        </div>
        <div class="col-sm-6">
            <input type="text" class="form-control form-control-sm" placeholder="Libellé" id="libelle_input" autocomplete="off" maxlength="100" />
        </div>
        <div class="col-sm-3">
            <select class="form-control form-control-sm" id="titre_input">
                <option value="">Titre</option>
                <?php
                foreach ($actes_medicaux as $titre) {
                    echo'<option value="'.$titre['TITRE'].'">'.$titre['TITRE'].'</option>';
                }

                ?>
            </select>
        </div>
        <div class="col-sm-1">
            <button type="submit" class="btn btn-sm btn-success btn-block"><i class="fa fa-search"></i></button>
        </div>
    </div>
</form>
