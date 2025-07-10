<?php
require_once '../../../Classes/UTILISATEURS.php';
if(isset($_SESSION['ECMU_USER_ID'])) {
    $session_user = $_SESSION['ECMU_USER_ID'];
    if (!empty($session_user)) {
        $UTILISATEURS = new UTILISATEURS();
        $utilisateur_existe = $UTILISATEURS->trouver($session_user, null, null);

        if (!empty($utilisateur_existe['ID_UTILISATEUR'])) {
             $num_facture = trim($_POST['num_facture']);
             $user = trim($utilisateur_existe['ID_UTILISATEUR']);

            if (!empty($num_facture) && !empty($user)) {

                require_once '../../../Classes/FACTURES.php';
                require_once '../../../Classes/ETABLISSEMENTSSANTE.php';
                $FACTURES = new FACTURES();
                $ETABLISSEMENTSANTE = new ETABLISSEMENTSSANTE();

                $facture = $FACTURES->trouver_medicament_facture($num_facture);
                if (empty($facture['FEUILLE'])) {
                    echo '<p align="center" class="text-danger"><b>' . $facture['message'] . '</b></p>';
                } else {
                    ?>
                    <p align="center" id="resultat_p_med"></p>
                    <form id="form_nouveau_medicament">
                        <div class="form-group row">
                            <label for="num_fs_input" class="col-sm-2 col-form-label-sm">N° Facture</label>
                            <div class="col-sm-3">
                                <input type="text" id="num_fs_input" class="form-control form-control-sm"
                                       value="<?= $facture['FEUILLE']; ?>" readonly/>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="num_secu_input" class="col-sm-2 col-form-label-sm">Patient</label>
                            <div class="col-sm-3">
                                <input type="text" id="num_secu_input" class="form-control form-control-sm"
                                       value="<?= $facture['NUM_SECU']; ?>" readonly/>
                            </div>
                            <div class="col-sm-7">
                                <input type="text" class="form-control form-control-sm"
                                       value="<?= $facture['NOM'] . ' ' . $facture['PRENOM']; ?>" readonly/>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="code_ets_input" class="col-sm-2 col-form-label-sm">La pharmacie</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control form-control-sm" maxlength="9" name="code_ets_input" id="code_ets_input" disabled>
                            </div>
                            <div class="col-sm-7">
                                <input class="form-control form-control-sm" name="raison_sociale_input" id="raison_sociale_ets_input" required/>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="" class="col-sm-2 col-form-label-sm"></label>
                            <div class="col-sm-6">
                                <input type="hidden" value="<?= $user; ?>" id="user_input"/>
                                <button type="submit" id="btn_nouveau_medicament" class="btn btn-success btn-sm"><i
                                        class="fa fa-edit"></i> Valider
                                </button>
                            </div>
                        </div>
                    </form>
                    <?php
                }
            }
        }

    }
}
?>
<script>
    $("#form_nouveau_medicament").submit(function () {
        $("#resultat_p_med").hide();
        $("#btn_nouveau_medicament").prop('disabled', true);
        $("#btn_nouveau_medicament").html('<i class="fa fa-braille"></i>');
        $("#btn_nouveau_medicament").removeClass('btn-success');
        $("#btn_nouveau_medicament").addClass('btn-warning');

        var code_ets = $("#code_ets_input").val(),
            num_facture = $("#num_fs_input").val(),
            user = $("#user_input").val();

        $.ajax({
            url: '../_configs/Includes/Searches/Agent/search_consultation_droits.php',
            type: 'post',
            data: {
                'type_envoi': 'CSAI',
                'code_ets': code_ets,
                'num_facture': num_facture,
                'user': user
            },
            dataType: 'json',
            success: function (data) {
                if(data['status'] == false) {
                    $("#resultat_p_med").show();
                    $("#btn_nouveau_medicament").prop('disabled', false);
                    $("#btn_nouveau_medicament").html('<i class="fa fa-edit"></i>');
                    $("#btn_nouveau_medicament").removeClass('btn-warning');
                    $("#btn_nouveau_medicament").addClass('btn-success');
                    $("#resultat_p_med").addClass('alert alert-danger');
                    $("#resultat_p_med").html(data['message']);
                }else {
                    setTimeout(function () {
                        window.location.href="facture-edition.php?type=MED&num="+data['num_transaction'];
                    },1000);
                }
            }
        });
        return false;
    });

    $("#raison_sociale_ets_input").keyup(function () {
        $("#raison_sociale_ets_input").removeClass('is-valid');
        $("#raison_sociale_ets_input").addClass('is-invalid');
        var raison_sociale =  $(this).val().trim().toUpperCase();
        $("#raison_sociale_ets_input").autocomplete({
            source: function(request, response) {
                $.getJSON("../_configs/Includes/Searches/CentreSaisie/search_pharmacie.php", {
                        raison_sociale: raison_sociale
                    },
                    response);
            },
            minLength: 4,
            select: function(e, ui) {
                var code_ets = ui.item.code,
                    statut = ui.item.status,
                    message = ui.item.message,
                    raison_sociale_ets = ui.item.value;
                if(statut == true){
                    $("#code_ets_input").val(code_ets);
                    $("#raison_sociale_ets_input").val(raison_sociale_ets);
                    $("#raison_sociale_ets_input").removeClass('is-invalid');
                    $("#raison_sociale_ets_input").addClass('is-valid');
                }else{
                    $("#raison_sociale_ets_input").removeClass('is-valid');
                    $("#raison_sociale_ets_input").addClass('is-invalid');
                }

                return false;
            }

        },"option", "appendTo", ".eventInsForm");
    }).blur(function () {
        var code_ets = $("#code_ets_input").val();
        if(code_ets.length != 0 && code_ets.length != 9) {
            $("#raison_sociale_ets_input").removeClass('is-valid');
            $("#raison_sociale_ets_input").addClass('is-invalid');
            $("#code_ets_input").val('');
            $("#raison_sociale_ets_input").val('');
        }
    });
</script>

