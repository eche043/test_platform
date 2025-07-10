<?php
require_once '../../../Classes/UTILISATEURS.php';
require_once '../../../Classes/COLLECTIVITES.php';
$COLLECTIVITES = new COLLECTIVITES();

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
            if(in_array('ENT',$modules)) {
                $code_collectivite = $_POST['code-collectivite'];
                $user_collectivite = $COLLECTIVITES->trouver($code_collectivite);
                $populations_actives = $COLLECTIVITES->trouver_populations_collectivite_par_statut($code_collectivite,1);
                $populations_retirees = $COLLECTIVITES->trouver_populations_collectivite_par_statut($code_collectivite,0);
                if($user_collectivite['CODE_OGD_COTISATIONS']=='03011000'){$libelle_ogd ='CNPS';}else{$libelle_ogd ='MAT.';}
                $chemin = '../_publics/images/logos_collectivites/';
                ?>
                <div class="col">
                    <p class="titres_p"><b class="fa fa-user-times"></b>Retrait de Populations</p>
                    <input type="hidden" id="code_collectivite_input" name="code_collectivite_input" value="<?=$code_collectivite;?>">
                    <div class="row">
                        <div id="div_afficher_liste_populations_collectivite_actives" class="col">
                            <?php
                            if(count($populations_actives)==0){
                                ?>
                                <p class="alert alert-danger" align="center">AUCUNE POPULATION RETIRER.</p>
                                <?php
                            }else{
                                ?>
                                <p id="resultats_retrait_populations" align="center"></p>
                                <form class="form-horizontal" id="form_retrait_populations_collectivite">
                                    <div class="form-group">
                                        <table class="table table-bordered table-hover table-sm table-responsive-sm dataTable" id="tb_retirer_all_population">
                                            <thead class="bg-secondary text-white">
                                            <tr align="center">
                                                <td width="5"></td>
                                                <td>N° <?=$libelle_ogd;?> PAY.</td>
                                                <td>N° SECU PAY.</td>
                                                <td>NOM PAY.</td>
                                                <td>PRENOMS PAY.</td>
                                                <td>DATE NAISSANCE PAY.</td>
                                                <td>N° <?=$libelle_ogd;?> BENEF.</td>
                                                <td>N° SECU BENEF.</td>
                                                <td>TYPE BENEF.</td>
                                                <td>NOM BENEF.</td>
                                                <td>PRENOMS BENEF.</td>
                                                <td>DATE NAISSANCE BENEF.</td>
                                                <td>GENRE BENEF.</td>
                                                <td width="5"><input type="checkbox" id="retirer_all_population"/></td>
                                            </tr>
                                            </thead>

                                            <tbody>
                                            <?php
                                            $i=1;
                                            foreach ($populations_actives as $pop) {
                                                if($pop['TYPE']=='T' || $pop['TYPE']=='RET' || $pop['TYPE']=='SAL' || $pop['TYPE']=='PAY'){
                                                    $type_benef = 'PAYEUR';
                                                    $class_type= 'style="font-weight: bold"';
                                                }elseif($pop['TYPE']=='C' || $pop['TYPE']=='CJT'){
                                                    $type_benef = 'CONJOINT';
                                                    $class_type= '';
                                                }else{
                                                    $type_benef = 'ENFANT';
                                                    $class_type= '';
                                                }
                                                ?>
                                                <tr>
                                                    <td width="5" align="center"><?=$i;?></td>
                                                    <td align="center"><?=$pop['PAYEUR_NUM_MATRICULE'];?></td>
                                                    <td align="center"><?=$pop['PAYEUR_NUM_SECU'];?></td>
                                                    <td><?=$pop['PAYEUR_NOM'];?></td>
                                                    <td><?=$pop['PAYEUR_PRENOMS'];?></td>
                                                    <td align="center"><?php if(!empty($pop['PAYEUR_DATE_NAISSSANCE'])){echo date('d/m/Y',strtotime($pop['PAYEUR_DATE_NAISSSANCE']));}?></td>
                                                    <td align="center"><?=$pop['BENEFICIAIRE_NUM_MATRICULE'];?></td>
                                                    <td align="center"><?=$pop['BENEFICIAIRE_NUM_SECU'];?></td>
                                                    <td align="center"><?=$type_benef;?></td>
                                                    <td><?=$pop['BENEFICIAIRE_NOM'];?></td>
                                                    <td><?=$pop['BENEFICIAIRE_PRENOMS'];?></td>
                                                    <td align="center"><?php if(!empty($pop['BENEFICIAIRE_DATE_NAISSANCE'])){echo date('d/m/Y',strtotime($pop['BENEFICIAIRE_DATE_NAISSANCE']));}?></td>
                                                    <td><?=$pop['BENEFICIAIRE_SEXE'];?></td>
                                                    <td><input type="checkbox" class="retirer_population" name="td_retirer_population" id="<?=$pop['ID'];?>" value="<?=$pop['ID'];?>"/></td>
                                                </tr>
                                                <?php
                                                $i++;
                                            }
                                            ?>
                                            </tbody>
                                        </table>

                                    </div>
                                    <div class="modal-footer" >
                                        <button type="submit" id="btn_validation_retrait_population" class="btn btn-danger btn-sm"><i class="fa fa-user-times"></i> Retirer</button>
                                    </div>
                                </form>

                                <?php
                            }
                            ?>
                        </div>
                    </div>
                    <br>
                    <p class="titres_p"><b class="fa fa-user-check"></b>Réactiver Populations</p>
                    <div class="row">
                        <div id="div_afficher_liste_populations_collectivite_retirees" class="col">
                            <?php
                            if(count($populations_retirees)==0){
                                ?>
                                <p class="alert alert-danger" align="center">AUCUNE POPULATION A REACTIVER.</p>
                                <?php
                            }else{
                                ?>
                                <p id="resultats_activer_populations" align="center"></p>
                                <form class="form-horizontal" id="form_activer_populations_collectivite">
                                    <div class="form-group">
                                        <table class="table table-bordered table-hover table-sm table-responsive-sm dataTable" id="tb_activer_all_population">
                                            <thead class="bg-secondary text-white">
                                            <tr align="center">
                                                <td width="5"></td>
                                                <td>N° <?=$libelle_ogd;?> PAY.</td>
                                                <td>N° SECU PAY.</td>
                                                <td>NOM PAY.</td>
                                                <td>PRENOMS PAY.</td>
                                                <td>DATE NAISSANCE PAY.</td>
                                                <td>N° <?=$libelle_ogd;?> BENEF.</td>
                                                <td>N° SECU BENEF.</td>
                                                <td>TYPE BENEF.</td>
                                                <td>NOM BENEF.</td>
                                                <td>PRENOMS BENEF.</td>
                                                <td>DATE NAISSANCE BENEF.</td>
                                                <td>GENRE BENEF.</td>
                                                <td width="5"><input type="checkbox" id="activer_all_population"/></td>
                                            </tr>
                                            </thead>

                                            <tbody>
                                            <?php
                                            $i=1;
                                            foreach ($populations_retirees as $pop) {
                                                if($pop['TYPE']=='T' || $pop['TYPE']=='RET' || $pop['TYPE']=='SAL' || $pop['TYPE']=='PAY'){
                                                    $type_benef = 'PAYEUR';
                                                    $class_type= 'style="font-weight: bold"';
                                                }elseif($pop['TYPE']=='C' || $pop['TYPE']=='CJT'){
                                                    $type_benef = 'CONJOINT';
                                                    $class_type= '';
                                                }else{
                                                    $type_benef = 'ENFANT';
                                                    $class_type= '';
                                                }
                                                ?>
                                                <tr>
                                                    <td width="5" align="center"><?=$i;?></td>
                                                    <td align="center"><?=$pop['PAYEUR_NUM_MATRICULE'];?></td>
                                                    <td align="center"><?=$pop['PAYEUR_NUM_SECU'];?></td>
                                                    <td><?=$pop['PAYEUR_NOM'];?></td>
                                                    <td><?=$pop['PAYEUR_PRENOMS'];?></td>
                                                    <td align="center"><?php if(!empty($pop['PAYEUR_DATE_NAISSSANCE'])){echo date('d/m/Y',strtotime($pop['PAYEUR_DATE_NAISSSANCE']));}?></td>
                                                    <td align="center"><?=$pop['BENEFICIAIRE_NUM_MATRICULE'];?></td>
                                                    <td align="center"><?=$pop['BENEFICIAIRE_NUM_SECU'];?></td>
                                                    <td align="center"><?=$type_benef;?></td>
                                                    <td><?=$pop['BENEFICIAIRE_NOM'];?></td>
                                                    <td><?=$pop['BENEFICIAIRE_PRENOMS'];?></td>
                                                    <td align="center"><?php if(!empty($pop['BENEFICIAIRE_DATE_NAISSANCE'])){echo date('d/m/Y',strtotime($pop['BENEFICIAIRE_DATE_NAISSANCE']));}?></td>
                                                    <td><?=$pop['BENEFICIAIRE_SEXE'];?></td>
                                                    <td><input type="checkbox" class="activer_population" id="<?=$pop['ID'];?>" value="<?=$pop['ID'];?>"/></td>
                                                </tr>
                                                <?php
                                                $i++;
                                            }
                                            ?>
                                            </tbody>
                                        </table>
                                    <div class="modal-footer" >
                                        <button type="submit" id="btn_validation_reactiver_population" class="btn btn-success btn-sm"><i class="fa fa-user-check"></i> Réactiver</button>
                                    </div>
                                </form>

                                <?php
                            }
                            ?>
                        </div>
                    </div>
                    <br>
                </div>
                <script type="text/javascript" src="<?= JS.'page_collectivite.js?v=1'?>"></script>
                <script>
                    $(function () {
                        $('.dataTable').DataTable();
                    })
                </script>
            <?php }
        }
    }
}else{
    session_destroy();
    echo '<script>window.location.href="'.URL.'"</script>';
}
?>
