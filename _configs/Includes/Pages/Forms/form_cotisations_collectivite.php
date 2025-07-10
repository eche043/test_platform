<div class="modal fade" id="modalCotisationCollectivite" tabindex="-1" role="dialog" aria-labelledby="modalCotisationCollectiviteLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCotisationCollectiviteLabel">Déclaration Cotisations</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <p class="align_center" id="p_resultats_cotisation_collectivite"></p>
            <form id="form_cotisation_collectivite">
                <div class="modal-body">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-2">
                                <label for="annee_decl_cotisation_input" class="col-form-label">Année:</label>
                                <select name="annee_decl_cotisation_input" id="annee_decl_cotisation_input" class="custom-select custom-select-sm">
                                    <option value="">Année</option>
                                    <?php
                                        for($annee = 2019 ; $annee<=date('Y',strtotime('+1 year',time())); $annee++){
                                            echo '<option value="'.$annee.'">'.$annee.'</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="col-sm-4">
                                <label for="mois_decl_cotisation_input" class="col-form-label">Mois:</label>
                                <select name="mois_decl_cotisation_input" id="mois_decl_cotisation_input" class="custom-select custom-select-sm" disabled>
                                    <option value="">Mois</option>
                                </select>
                            </div>
                        </div>
                        <br>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12 d-none" id="div_total_declarations">
                                <!--<table class="table table-bordered table-hover table-sm table-responsive-sm dataTable">
                                    <thead class="bg-secondary text-white">
                                        <tr align="center">
                                            <th>Total Personnes Déclarées</th>
                                            <th>Total Montant à Payer (F CFA)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td align="center"><?/*=$nb_pop;*/?></td>
                                            <td align="center"><?/*=$nb_pop*1000;*/?></td>
                                        </tr>
                                    </tbody>
                                </table>-->
                            </div>
                        </div>
                        <br>
                    </div>
                    <div class="modal-footer d-none" id="div_validation" >
                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Fermer</button>
                        <button type="submit" id="submit_cotisation_collectivite" class="btn btn-primary btn-sm"><i class="fa fa-check"></i> Valider</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>