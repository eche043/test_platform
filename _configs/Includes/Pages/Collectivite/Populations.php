<?php

require_once '../../../Classes/UTILISATEURS.php';
require_once '../../../Classes/COLLECTIVITES.php';
$UTILISATEURS = new UTILISATEURS();
$COLLECTIVITES = new COLLECTIVITES();
if(isset($_SESSION['ECMU_USER_ID']) || !empty($_SESSION['ECMU_USER_ID'])) {
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
            if(in_array('ENT',$modules)) {
                $code_collectivite = $_POST['code-collectivite'];
                $user_collectivite = $COLLECTIVITES->trouver($code_collectivite);
                //$populations = $COLLECTIVITES->trouver_populations_collectivite_par_statut($code_collectivite,1);
                if($user_collectivite['CODE_OGD_COTISATIONS']=='03011000'){$libelle_ogd ='CNPS';}else{$libelle_ogd ='MAT.';}
                //$nb_populations = count($populations);
                $total_populations = $COLLECTIVITES->total_populations_collectivite_par_statut($code_collectivite,1);
                $nb_populations = $total_populations['TOTAL'];
                $chemin = '../_publics/images/logos_collectivites/';
                $processing_folder = DIR.'IMPORTS/COLLECTIVITES/'.$code_collectivite.'/PROCESSING_FILES/';
                $dossier = DIR.'IMPORTS/COLLECTIVITES/'.$code_collectivite.'/TEMP_FILES/';
                $path_rapport_export = DIR.'EXPORTS/POPULATIONS_COLLECTIVITES/RAPPORTS_ANALYSE/'.$code_collectivite.'/TEMP_FILES/';
                $path_process_export = DIR.'EXPORTS/POPULATIONS_COLLECTIVITES/'.$code_collectivite.'/';
                $total_fichiers = 0;
                $total_fichiers_p = 0;
                $f = '';
                if (file_exists($processing_folder)) {
                    $fichiers_processing = array_diff(scandir($processing_folder), array(".", ".."));
                    if (!empty($fichiers_processing)) {
                        foreach ($fichiers_processing as $values => $filenamep) {
                            $f = $filenamep;
                            $total_fichiers++;
                        }
                        if ($total_fichiers === 1) {

                            $extension = strrchr($f, '.');
                            $filerpt = str_replace($extension, '', $f);
                            if(file_exists($path_process_export . $filerpt . '.txt')){
                                $lines = file($path_process_export . $filerpt . '.txt');

                                $file = escapeshellarg($path_process_export.$filerpt.'.txt');
                                $l_c = `tail -n 1 $file`;

                                if (substr($lines[2], 0, 18) === "TOTAL A TRAITER : ") {
                                    $total_a_analyser = (int)substr($lines[2], 18);
                                } else {
                                    $total_a_analyser = 0;
                                }

                                if (substr($l_c, 0, 10) === "Ligne N") {
                                    $total_analyse = (int)substr($l_c, 10);
                                }elseif (substr($l_c, 0, 23) === "TOTAL LIGNES TRAITEES: ") {
                                    $total_analyse = (int)substr($l_c, 23);
                                } else {
                                    $total_analyse = 0;
                                }
                            }
                            else{
                                $total_a_analyser =0;
                                $total_analyse = 0;
                            }
                        }
                        else{
                            $total_a_analyser =0;
                            $total_analyse = 0;
                        }
                    }else{
                        if (file_exists($dossier)) {
                            $fichiers = array_diff(scandir($dossier), array(".", ".."));
                            if (!empty($fichiers)) {

                                foreach ($fichiers as $values => $filename) {
                                    $f = $filename;
                                    $total_fichiers++;
                                }
                                if($total_fichiers===1) {
                                    $extension = strrchr($f, '.');
                                    $filerpt = str_replace($extension, '', $f);
                                    $lines = file($path_rapport_export . $filerpt . '.txt');

                                    $file = escapeshellarg($path_rapport_export.$filerpt.'.txt');
                                    $l_c = `tail -n 1 $file`;

                                    if (substr($lines[2], 0, 18) === "TOTAL A TRAITER : ") {
                                        $total_a_analyser = (int)substr($lines[2], 18);
                                    } else {
                                        $total_a_analyser = 0;
                                    }

                                    if (substr($l_c, 0, 10) === "Ligne N") {
                                        $total_analyse = (int)substr($l_c, 10);
                                    }elseif (substr($l_c, 0, 24) === "TOTAL LIGNES ANALYSEES: ") {
                                        $total_analyse = (int)substr($l_c, 24);
                                    } else {
                                        $total_analyse = 0;
                                    }
                                }
                                else {
                                    $total_a_analyser = 0;
                                    $total_analyse = 0;
                                }
                            }
                        }
                    }
                }
                else{
                    if(!file_exists(DIR.'IMPORTS/COLLECTIVITES/'.$code_collectivite.'/PROCESSING_FILES/')) {
                        mkdir(DIR.'IMPORTS/COLLECTIVITES/'.$code_collectivite.'/PROCESSING_FILES/',0777, TRUE);
                    }
                }
                ?>
                <div class="col">
                    <p class="titres_p"><?=$user_collectivite['RAISON_SOCIALE']?>  (<b class="text-danger"><?=number_format($nb_populations,'0','',' ');?> <?php if($nb_populations > 1){echo '<b class="fa fa-users"></b>';}else {echo '<b class="fa fa-user"></b>';} ?></b>)</p>
                    <div>
                        <nav class="navbar navbar-light">
                            <div class="container-fluid">
                                <div class="navbar-brand">
                                    <button type="button" id="btn_chargement_population_collectivite" class="btn btn-sm btn-success" data-toggle="collapse" data-target="#ChargerFichierPopulationsCollectivite" aria-expanded="false" aria-controls="ChargerFichierPopulationsCollectivite"><i class="fa fa-list-alt"></i> Charger liste Populations</button>
                                    <button type="button" id="btn_edition_individu_collectivite" class="btn btn-sm btn-success" data-toggle="modal" data-target="#modalPopulationCollectivite" ><i class="fa fa-user-plus"></i> Déclarer un individu</button>
                                    <a id="btn_retirer_individu_collectivite" class="btn btn-sm btn-warning" href="<?=URL.'collectivite/retrait_populations.php?code-collectivite='.$code_collectivite;?>"><i class="fa fa-user-minus"></i> Rétirer un individu</a>
                                </div>
                                <?php
                                if($nb_populations > 0){
                                    ?>

                                <div class="d-flex">
                                    <button type="button" id="btn_download_population_collectivite" class="btn btn-sm btn-info" ><i class="fa fa-download"></i>Télécharger la liste</button>
                                    <span id="id_span_loader"></span>
                                </div>
                                    <?php
                                }
                                ?>
                            </div>
                        </nav>
                        <div class="row">
                            <div class="container collapse <?php if($total_fichiers>0){echo 'show';}?>" id="ChargerFichierPopulationsCollectivite">
                                <div class="col" id="div_chargement_population" <?php if($total_fichiers>0){echo 'hidden';}?>>
                                    <div class="row justify-content-md-center">
                                        <div class="col col-md-6">
                                            <form id="form_upload_populations_collectivite" class="form_upload_files"  method="post" action="../_configs/Includes/Submits/Collectivite/submit_upload_fichier_populations.php" enctype="multipart/form-data">
                                                <div class="form-group row justify-content-md-center">
                                                    <div class="col-md-8">
                                                        <input type="file" class="form-control form-control-file form-control-sm" id="fichier_population_collectivite_input" name="fichier_population_collectivite_input">
                                                        <input type="hidden" id="code_collectivite_input" name="code_collectivite_input" value="<?=$code_collectivite;?>">
                                                    </div>
                                                </div>
                                                <div class="form-group row justify-content-md-center">
                                                    <div class="col-md-4">
                                                        <button type="submit" name="submit_upload_population_collectivite_btn"  id="submit_upload_population_collectivite_btn" class="btn btn-primary btn-sm btn-block btn_upload_file">Charger</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="progress" >
                                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-info" role="progressbar" style="width: 0%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                                            <span class="sr-only" id="span_affichage">0% Complete</span>
                                        </div>
                                    </div>
                                    <p id="reponse_upload_p"></p>
                                </div>
                                <div class="row justify-content-md-center" id="div_fichier_en_cours" <?php if($total_fichiers==0){echo 'hidden';}?>>
                                    <div class="col-sm-6 my-1" id="div_analyse_en_cours">
                                        <input type="hidden" value="<?=$total_fichiers;?>" id="total_fichiers_input">
                                        <button class="btn <?php if (!empty($fichiers_processing)) {echo 'btn-outline-success';}else{echo 'btn-outline-info';} ?> btn-block btn-sm" disabled="disabled" id="btn_analyse" type="button">FICHIER: <b><?=$f;?></b> <br><?php if (!empty($fichiers_processing)) {echo 'FINALISATION ...<br> <b id="b_total_analyse">'.$total_analyse.'</b> ligne(s) traitées(s) sur <b id="b_total_a_analyse">';}
                                            else{ echo 'ANALYSE EN COURS...<br> <b id="b_total_analyse">'.$total_analyse.'</b> ligne(s) analysée(s) sur <b id="b_total_a_analyse">';} ?><?=$total_a_analyser;?></b></button>
                                    </div>
                                </div>
                                <div class="row justify-content-md-center" id="div_reset_analyse" hidden >
                                    <div class="col-sm-6 my-1">
                                        <button class="btn btn-block btn-sm" id="btn_reset_analyse" type="button"><b>Charger un autre fichier</b></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php include "../Forms/form_population_collectivite.php";?>
                        <hr>
                        <div class="row">
                            <div id="div_afficher_liste_populations_collectivite" class="col">
                                <?php
                                    if($nb_populations===0){
                                    ?>
                                        <p class="alert alert-danger" align="center">AUCUNE POPULATION ENREGISTREE.</p>
                                    <?php
                                    }
                                    else{
                                        include "../Forms/form_recherche_population_collectivite.php";
                                    }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <script type="text/javascript" src="<?= JS.'page_collectivite.js'?>"></script>
                <script>
                    $(function () {
                        $('.dataTable').DataTable();
                    })
                </script>
                <?php
            }
        }
    }
}else{
    session_destroy();
    echo '<script>window.location.href="'.URL.'"</script>';
}
?>
<script>
    $(".datepicker").datepicker({
        changeMonth: true,
        changeYear: true,
        maxDate: 0
    });
</script>

