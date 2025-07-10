<?php
/**
 * Created by PhpStorm.
 * User: mohamed.kone
 * Date: 07/02/2020
 * Time: 10:21
 */
?>
<form id="form_recherche_ets">
    <div class="form-group row">

        <div class="col-sm-2">
            <input type="text" class="form-control form-control-sm" placeholder="Code" id="code_input" autocomplete="off" maxlength="9" />
        </div>

        <div class="col-sm-7">
            <input type="text" class="form-control form-control-sm" placeholder="Raison sociale" id="raison_sociale_input" autocomplete="off" maxlength="100" />
        </div>

        <div class="col-sm-2">

            <select class="form-control form-control-sm" id="ville_input" autocomplete="off">
                <option value="">Choisir la ville</option>
                <?php foreach ($villes As $ville){ ?><option value="<?= $ville['VILLE'] ?>"><?= $ville['VILLE'] ?></option> <?php } ?>
            </select>

        </div>


        <div class="col-sm-1">
            <button type="submit" class="btn btn-sm btn-success btn-block"><i class="fa fa-search"></i></button>
        </div>
    </div>
</form>
