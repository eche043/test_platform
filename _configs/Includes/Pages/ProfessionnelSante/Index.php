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
            if(in_array('PS',$modules)) {
                if(!empty($user['CODE_PS'])){

                    $user_ets_ps = $UTILISATEURS->trouver_ets_ps($user['CODE_PS'],1);
					if(count($user_ets_ps)!=0) {
						require_once '../../../Classes/FACTURES.php';
						require_once '../../../Classes/ETABLISSEMENTSSANTE.php';
						$FACTURES = new FACTURES();
						$ETABLISSEMENTSSANTE = new ETABLISSEMENTSSANTE();
						
						?>
						<div class="col">
							<p class="titres_p"><i class="fa fa-newspaper"></i> Professionnel de santé</p>
							<form id="recherche_facture_form_ps">

								<?php foreach($user_ets_ps as $ps) {?>
									<input type="hidden" value="<?php echo $ps['PS'] ?>"  id="ps_input" name="code_ps">
								<?php } ?>

								<div class="form-group row">
									<div class="col-sm-2">
										<input type="text" class="form-control form-control-sm datepicker" id="date_debut_input" name="date_debut" autocomplete="off"  placeholder="Date début" value="<?= date('d/m/Y',strtotime('-1 week',time())); ?>" readonly />
									</div>
									<div class="col-sm-2">
										<input type="text" id="date_fin_input" name="date_fin_input" class="form-control form-control-sm datepicker" placeholder="Date fin" autocomplete="off" value="<?= date('d/m/Y',time()); ?>" readonly />
									</div>
									<div class="col">
										<select class="form-control form-control-sm custom-select" id="ets_input" required>
											<?php
											foreach ($user_ets_ps AS $values)  {
												$ets = $ETABLISSEMENTSSANTE->trouver_ets_valide($values["CODE_ETS"]);
												echo '<option value="'.$values["CODE_ETS"].'">'.$ets["RAISON_SOCIALE"].'</option>';
											}
											?>
										</select>
									</div>
									<div class="col-sm-1">
										<button type="submit" class="btn btn-primary btn-sm btn-block" id="btn_recherche_facture_ps"><i class="fa fa-search"></i></button>
									</div>
								</div>

								<div class="form-group row">


								</div>
							</form>
							<hr />
							<div id="resultats_div"></div>
						</div>

						<script type="text/javascript" src="<?= JS.'page_professionnel_sante.js'?>"></script>
						<?php
					}else {
                        echo '<script>window.location.href="'.URL.'"</script>';
                    }
                }else {
                    echo '<script>window.location.href="'.URL.'"</script>';
                }
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