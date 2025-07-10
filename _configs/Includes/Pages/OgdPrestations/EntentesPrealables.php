<?php
require_once '../../../Classes/UTILISATEURS.php';
if(isset($_SESSION['ECMU_USER_ID'])) {
    $session_user = $_SESSION['ECMU_USER_ID'];
    if (!empty($session_user)) {
        $UTILISATEURS = new UTILISATEURS();
        $utilisateur_existe = $UTILISATEURS->trouver($session_user, null, null);
        $utilisateur_existe['CODE_OGD_P'];
        if (!empty($utilisateur_existe['ID_UTILISATEUR'])) {
			$modules = array_diff(explode(';', stream_get_contents($utilisateur_existe['PROFIL'], -1)), array(""));
            if (!empty($utilisateur_existe['CODE_OGD_P'])) {
                if(in_array('OGDP', $modules)) {
                    $user_profil = explode(';',$utilisateur_existe['FSE']);
                    if(in_array('EP',$user_profil)) {

						require_once '../../../Classes/ENTENTESPREALABLES.php';
						require_once '../../../Classes/ASSURES.php';
						$ENTENTESPREALABLES = new ENTENTESPREALABLES();
						$ASSURES = new ASSURES();
						$q = $ENTENTESPREALABLES->liste_entente_prealable_par_ogd($utilisateur_existe['CODE_OGD_P']);
						$etablissement = $ENTENTESPREALABLES->liste_des_etablissements_par_ogd_ayant_une_entente($utilisateur_existe['CODE_OGD_P']);
						$nb_ententesprealables = count($q); ?>
						<div class="col">						  
							<p class="titres_p"><b class="fa fa-file"></b> Demandes d'ententes préalables</p><hr>
							<form id="form_recherche_entente_prealable">
								<div class="form-group row">
									<div class="col-sm-2">
										<select class="form-control form-control-sm" id="statut_input">
											<option value="">Statut</option>
											<option value="1">Validée</option>
											<option value="2">Réfusée</option>
										</select>
									</div>
									<div class="col-sm-2">
										<select class="form-control form-control-sm" id="type_ep_input">
											<option value="">TYPE EP</option>
											<option value="HOS">HOSPITALISATION</option>
											<option value="EXP">BIOLOGIE/IMAGERIE</option>
										</select>
									</div>
									<div class="col-sm-2">
										<input type="text" class="form-control form-control-sm datepicker" id="date_demande_input" placeholder="date de demande" autocomplete="off" readonly />
									</div>
									<div class="col-sm-5">
										<select class="form-control form-control-sm" id="entente_ets_input">
											<option value="">Etablissement</option>
											<?php
											foreach ($etablissement as $ets) {
												?>
												<option value="<?= $ets['INP']; ?>"><?= $ets['RAISON_SOCIALE']; ?></option>
												<?php
											}
											?>
										</select>
									</div>
									<div class="col-sm-1">
										<button type="submit" id="btn_recherche" class="btn btn-success btn-block btn-sm"><i class="fa fa-search"></i></button>
									</div>
								</div><hr>
							</form>
							<div id="div_resultats_recherche">
								<?php
									if($nb_ententesprealables == 0) {
										echo '<p align="center" class="alert-primary">AUCUNE DEMANDE EN ATTENTE</p>';
									}
                                ?>
							</div>
                        </div>
                        <?php
                    }else{
                        echo '<script>window.location.href="'.URL.'ogd-prestations/"</script>';
                    }
                }else{
                    echo '<script>window.location.href="'.URL.'ogd-prestations/"</script>';
                }
            }else{
                echo '<script>window.location.href="'.URL.'ogd-prestations/"</script>';
            }
        }else{
            echo '<script>window.location.href="'.URL.'ogd-prestations/"</script>';
        }
    }else{
        echo '<script>window.location.href="'.URL.'ogd-prestations/"</script>';
    }
}
?>
<script>
    $("#form_recherche_entente_prealable").submit(function () {
        $("#btn_recherche").prop('disabled', true);
        $("#btn_recherche").removeClass('btn-success');
        $("#btn_recherche").addClass('btn-danger');
        $("#btn_recherche").html('...');
        var statut = $("#statut_input").val(),
            type_ep = $("#type_ep_input").val(),
            date_demande = $("#date_demande_input").val(),
            entente_ets = $("#entente_ets_input").val();

        if (statut || type_ep || date_demande || entente_ets) {
            $.ajax({
                url: '../_configs/Includes/Submits/OgdPrestations/submit_moteur_recherche.php',
                type: 'post',
                data: {
                    'statut': statut,
                    'type_ep': type_ep,
                    'date_demande': date_demande,
                    'entente_ets': entente_ets
                },
                success: function (data) {
                    $("#btn_recherche").prop('disabled', false);
                    $("#btn_recherche").removeClass('btn-danger');
                    $("#btn_recherche").addClass('btn-success');

                    $("#btn_recherche").html('<i class="fa fa-search"></i>');
                    $("#div_resultats_recherche").html(data);
                }
            });
        } else {
            $("#div_resultats_recherche").html('<p align="center" class="alert-danger">Veuillez SVP renseigner au moins un champ</p>');
        }

        return false;
    });

    $(".datepicker").datepicker({
        maxDate: 0
    }).attr('readonly', 'readonly');

</script>

<script type="text/javascript">entente_prealable();</script>
