<?php
/**
 * Created by PhpStorm.
 * User: mohamed.kone
 * Date: 05/02/2020
 * Time: 17:09
 */
?>
<p id="resultat_bordereau" align="center"></p>
<form id="form_bordereau">
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group row">
                <label for="type_facture_input" class="col-sm-3 col-form-label-sm">Type de facture</label>
                <div class="col-sm-9">
                    <select class="form-control form-control-sm" id="type_facture_input" required>
                        <option value="">Sélectionner</option>
                        <?php
                        foreach ($type_factures as $type_facture) {
//                            if(in_array($type_facture['CODE'],$acces_fse)) {
//
//                            }
                            echo '<option value="'.$type_facture['CODE'].'">'.$type_facture['LIBELLE'].'</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="form-group row" id="div_select_ogd">
                <label for="select_ogd_p" class="col-sm-3 col-form-label-sm">OGD</label>
                <div class="col-sm-9">
                    <select name="select_ogd_input" id="select_ogd_input" class="form-control form-control-sm" disabled required>
                        <option value="">Sélectionner</option>
                    </select>
                </div>
            </div>
            <div id="div_select_periode">
                <div class="form-group row">
                    <label for="date_debut_periode" class="col-sm-3 col-form-label-sm">Date D&eacute;but</label>
                    <div class="col-sm-3">
                        <input class="form-control form-control-sm datepicker" type="text" placeholder="Date début" id="date_debut_input" disabled required />
                    </div>
                    <label for="date_fin_periode" class="col-sm-3 col-form-label-sm">Date Fin</label>
                    <div class="col-sm-3">
                        <input class="form-control form-control-sm datepicker" type="text" placeholder="Date fin" id="date_fin_input" disabled required />
                    </div>
                </div>
            </div>

        </div>
        <div class="col-sm-6">
            <div class="form-group row">
                <div class="col-sm-5">
                    <select name="select_numeros_factures" id="select_numeros_factures" class="form-control form-control-sm" multiple="multiple" size="18"></select>
                </div>
                <div class="col-sm-2">
                    <table>
                        <tr>
                            <td>
                                <button type="button" class="btn btn-block btn-dark " id="btnLeft"><b class="fa fa-angle-left"></b></button>
                            </td>
                            <td>
                                <button type="button" class="btn btn-block btn-dark " id="btnRight"><b class="fa fa-angle-right"></b></button>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <button type="button" class="btn btn-block btn-dark " id="btnAllLeft"><b class="fa fa-angle-double-left"></b></button>
                            </td>
                            <td>
                                <button type="button" class="btn btn-block btn-dark " id="btnAllRight"><b class="fa fa-angle-double-right"></b></button>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-sm-5">
                    <select name="select_numeros_factures_finalises" id="select_numeros_factures_finalises" class="form-control form-control-sm" multiple="multiple" size="18" required></select>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-sm-12">
                    <button type="submit" class="btn btn-block btn-primary btn-sm" id="btn_generer_bordereau"><i class="fa fa-download"></i> Généner le bordereau</button>
                    <?php
                        if(isset($user_ETS['CODE_ETS']) && !empty($user_ETS['CODE_ETS']))
                        {
                            ?>
                            <input type="hidden" value="<?= $user_ETS['CODE_ETS'];?>" id="code_ets_input">
                            <?php
                        }else
                        {
                            ?>
                            <input type="hidden" value="<?= $ets['CODE_ETS'];?>" id="code_ets_input">
                            <?php
                        }
                    ?>
                </div>
            </div>
        </div>
    </div>
</form>
