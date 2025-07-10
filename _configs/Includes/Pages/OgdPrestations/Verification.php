<?php
require_once '../../../Classes/UTILISATEURS.php';

if(isset($_SESSION['ECMU_USER_ID']) || !empty($_SESSION['ECMU_USER_ID'])) {
    $UTILISATEURS = new UTILISATEURS();
    $user = $UTILISATEURS->trouver($_SESSION['ECMU_USER_ID'],NULL,NULL);
    if(empty($user['ID_UTILISATEUR'])) {
        session_destroy();
        echo '<script>window.location.href="'.URL.'"</script>';
    }else {
        $modules = array_diff(explode(';',stream_get_contents($user['PROFIL'],-1)),array(""));
        $nb_modules = count($modules);
        if($nb_modules == 0) {
            session_destroy();
            echo '<script>window.location.href="'.URL.'"</script>';
        }else{ 
			if(in_array('OGDP', $modules)) {
                $user_hab = explode(';',$user['FSE']);
                if(in_array('RPT', $user_hab)) {
					?>
					<div class="col">
						<input type="hidden" id="user_input" value="<?= $user['ID_UTILISATEUR'];?>" />
						<p class="titres_p"><b class="fa fa-check-double"></b> Vérification</p><hr>
						<div class="col-sm-12">
							<div class="row justify-content-md-center">
								<div class="col-sm-3">
									<form id="form_recherche_numero_facture">
										<div class="form-group row">
											<div class="col-sm-8">
												<input type="text" class="form-control form-control-sm" id="numero_facture_input" placeholder="numéro de la facture" autocomplete="off" style="text-align: right" />
											</div>
											<div class="col-sm-4">
												<input type="hidden" id="type_verification_input" value="DECA"/>
												<button type="submit" id="btn_recherche" class="btn btn-success btn-block btn-sm"><i class="fa fa-search"></i></button>
											</div>
										</div><hr>
									</form>
								</div>
							</div>
						</div>
						<div id="factures_div"></div>
					</div>
					<script type="text/javascript" src="<?= JS.'page_ogd_prestations.js'?>"></script>
					<?php
                }else{
                    echo '<script>window.location.href="'.URL.'"</script>';
                }
            }else{
                echo '<script>window.location.href="'.URL.'"</script>';
            }
        }
    }
}else{
    session_destroy();
    echo '<script>window.location.href="'.URL.'"</script>';
}
?>
<script>
    $(function () {
        $('#dataTable').DataTable();
    });

</script>

