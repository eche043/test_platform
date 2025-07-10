<?php
/**
 * Created by PhpStorm.
 * User: mohamed.kone
 * Date: 06/02/2020
 * Time: 21:03
 */
?>
<form id="recherche_factures_assurances_form">
    <div class="form-row">
        <div class="col-sm-3">
            <select class="form-control form-control-sm" id="type_facture_input">
                <option value="">Type de facture</option>
                <?php
                foreach ($type_factures as $type_facture) {
                    if(in_array($type_facture['CODE'],$acces_fse)) {
                        echo '<option value="'.$type_facture['CODE'].'">'.$type_facture['LIBELLE'].'</option>';
                    }
                }
                ?>
            </select>
        </div>
        <div class="col-sm-2">
            <input type="hidden" value="<?php if(isset($_POST['code_ets'])){ echo $ets['CODE_ETS']; }else{ echo $user_ets['CODE_ETS']; } ;?>" id="code_ets_input" />
            <input type="text" id="date_soins_input" class="form-control form-control-sm datepicker" placeholder="Date de soins" autocomplete="off" />
        </div>
        <div class="col-sm-2">
            <input type="text" id="num_facture_input" maxlength="11" class="form-control form-control-sm" placeholder="N° Facture" autocomplete="off" />
        </div>
        <div class="col-sm-2">
            <input type="text" id="num_secu_input" maxlength="13" class="form-control form-control-sm" placeholder="N° Sécu" autocomplete="off" />
        </div>
        <div class="col">
            <input type="text" id="nom_input" class="form-control form-control-sm" placeholder="Nom du patient" autocomplete="off" />
        </div>
        <div class="col-sm-1">
            <button type="submit" class="btn btn-primary btn-sm btn-block" id="search_facture_btn"><i class="fa fa-search"></i></button>
        </div>
    </div>
</form>
