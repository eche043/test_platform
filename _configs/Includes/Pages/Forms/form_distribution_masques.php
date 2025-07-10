<nav class="navbar navbar-light">
    <a class="navbar-brand"><button type="button" id="button_modal_nouveau_service_masque" data-toggle="modal" data-target="#modalEntente" class="btn btn-sm btn-success"><i class="fa fa-plus"></i> Servir Masque</button></a>
    <form id="form_search_distribution_masques" class="form-inline">
        <input type="text" aria-label="Numéro Sécu" class="form-control form-control-sm mr-sm-2" id="search_numero_secu_input" placeholder="Numéro Sécu" autocomplete="off" />
        <input type="text" aria-label="Date Début" class="form-control form-control-sm mr-sm-2 datepicker" id="search_date_debut_input" value="<?=date('d/m/Y');?>" placeholder="Date Début" autocomplete="off" />
        <input type="text" aria-label="Date Fin" class="form-control form-control-sm mr-sm-2 datepicker" id="search_date_fin_input" value="<?=date('d/m/Y');?>" placeholder="Date Fin" autocomplete="off" />
        <button type="submit" id="button_search_masque_servi" class="btn btn-sm btn-info my-2 my-sm-0 "><i class="fa fa-search"></i> Rechercher</button>
    </form>
</nav>

<div class="modal fade" id="modalEntente" tabindex="-1" role="dialog" aria-labelledby="modalEntenteLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEntenteLabel">Nouvelle Distribution de masques</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <p class="align_center" id="p_resultats_distribution_masques"></p>
            <form id="form_nouvelle_distribution_masque">
                <div class="modal-body">
                    <!--<div class="form-group">
                        <div class="row">
                            <div class="col-sm-4">
                                <label for="type_recepient_dstMasq_select" class="col-form-label">Type Personne:</label>
                                <select class="form-control form-control-sm" name="type_recepient_dstMasq_select" id="type_recepient_dstMasq_select" required>
                                    <option value="">Sélectionner</option>
                                    <option value="ENROLE_AVEC_CARTE">ENROLE AVEC CARTE CMU</option>
                                    <option value="ENROLE_SANS_CARTE">ENROLE SANS CARTE CMU</option>
                                    <option value="NON_ENROLE">NON ENROLE</option>
                                </select>
                            </div>
                        </div>
                    </div>-->
                    <div class="form-group" id="div_numero_secu_dstMasq">
                        <div class="row">
                            <div class="col-sm-4">
                                <label for="numero_secu_dstMasq_input" class="col-form-label">Numéro Sécutité sociale</label>
                                <input class="form-control form-control-sm" type="text" name="numero_secu_dstMasq_input" id="numero_secu_dstMasq_input" maxlength="13" autocomplete="off" required/>
                            </div>
                        </div>
                    </div>
                    <div class="" id="div_details_recipient_dstMasq">
                        <div class="form-group" id="div_nom_prenoms_dstMasq">
                            <div class="row">
                                <div class="col-sm-4">
                                    <label for="new_nom_dstMasq_input" class="col-form-label">Nom:</label>
                                    <input class="form-control form-control-sm" name="new_nom_dstMasq_input" id="new_nom_dstMasq_input" disabled/>
                                </div>
                                <div class="col-sm-8">
                                    <label for="new_prenom_dstMasq_input" class="col-form-label">Prénoms:</label>
                                    <input type="text" class="form-control form-control-sm" id="new_prenom_dstMasq_input" disabled>
                                </div>
                                <div class="col-sm-4">
                                    <label for="new_date_naissance_dstMasq_input" class="col-form-label">Date de Naissance:</label>
                                    <input type="text" class="form-control form-control-sm" id="new_date_naissance_dstMasq_input" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="form-group" id="div_numero_telephone_dstMasq">
                            <div class="row">
                                <div class="col-sm-4">
                                    <label for="new_numero_telephone_dstMasq_input" class="col-form-label">Numéro de Téléphone:</label>
                                    <input class="form-control form-control-sm" type="text" name="new_numero_telephone_dstMasq_input" maxlength="10" id="new_numero_telephone_dstMasq_input" autocomplete="off" required/>
                                </div>
                            </div>
                        </div>
                        <div class="form-group" id="div_quantite_dst_Masq">
                            <div class="row">
                                <div class="col-sm-4">
                                    <label for="quantite_servie_dstMasq_input" class="col-form-label">Quantité Servie ( 1 boîte de 50 ):</label>
                                    <select class="form-control form-control-sm quantite_servie_dstMasq_input" id="quantite_servie_dstMasq_input" required disabled>
                                        <?php
                                        for ($b = 1; $b <= 10; $b++) {
                                            ?>
                                            <option value="<?= $b; ?>" ><?= $b;?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group" id="div_date_dstMasq">
                            <div class="row">
                                <div class="col-sm-4">
                                    <label for="date_debut_dstMasq_input" class="col-form-label">Date Début </label>
                                    <input class="form-control form-control-sm datepicker" type="text" name="date_debut_dstMasq_input" value="<?=date('d/m/Y')?>" id="date_debut_dstMasq_input" autocomplete="off" disabled/>
                                </div>
                                <div class="col-sm-4">
                                    <label for="date_fin_dstMasq_input" class="col-form-label">Date Fin</label>
                                    <input class="form-control form-control-sm datepicker" type="text" name="date_fin_dstMasq_input" value="<?=date('d/m/Y',strtotime( '+1 MONTH'));?>" id="date_fin_dstMasq_input" autocomplete="off" disabled/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer d-none" id="div_validation" >
                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Fermer</button>
                        <input type="hidden" id="code_ets_input" value="<?=$user_ets["CODE_ETS"];?>">
                        <button type="submit" id="button_nouvelle_distribution_masque" class="btn btn-primary btn-sm"><i class="fa fa-check"></i> Valider</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

