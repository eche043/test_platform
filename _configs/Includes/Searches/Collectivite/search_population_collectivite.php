<?php

require_once '../../../Classes/UTILISATEURS.php';
if(isset($_SESSION['ECMU_USER_ID'])){
    $session_user = $_SESSION['ECMU_USER_ID'];
    if(!empty($session_user)){
        $UTILISATEURS = new UTILISATEURS();
        $utilisateur_existe = $UTILISATEURS->trouver($session_user,null,null);
        if(!empty($utilisateur_existe['ID_UTILISATEUR'])){
            $trouver_ets = $UTILISATEURS->trouver_ets_utilisateur($session_user);
            if($utilisateur_existe['ACTIF'] != 1){
                echo '<p class="alert alert-danger" align="center">VOTRE IDENTIFIANT A ETE DESACTIVE. VOUS NE POUVEZ PAS EFFECTUER CETTE ACTION. VEUILLEZ CONTACTER VOTRE ADMINISTRATEUR SYSTEME.</p>';
            }else{
                if(empty($_POST["code_collectivite"])){
                    echo '<p class="alert alert-danger" align="center">VOTRE COLLECTIVITE N\'A PAS ETE DEFINIE. VOUS NE POUVEZ PAS EFFECTUER CETTE ACTION. VEUILLEZ CONTACTER VOTRE ADMINISTRATEUR SYSTEME.</p>';
                }else{
                    require_once '../../../Classes/ASSURES.php';
                    require_once '../../../Classes/COLLECTIVITES.php';
                    $ASSURES = new ASSURES();
                    $COLLECTIVITES = new COLLECTIVITES();

                    $code_collectivite = $_POST["code_collectivite"];
                    $num_secu = trim($_POST['num_secu']);
                    $num_matricule = trim($_POST['num_matricule']);
                    $nom_prenom = strtoupper(trim($_POST['nom_prenom']));
                    $trouver_ogd_collectivite = $COLLECTIVITES->trouver($code_collectivite);
                    if(isset($trouver_ogd_collectivite['CODE_OGD_COTISATIONS']) && !empty($trouver_ogd_collectivite['CODE_OGD_COTISATIONS'])) {
                        $populations = $COLLECTIVITES->moteur_recherche_population_collectivite($trouver_ogd_collectivite['CODE_OGD_COTISATIONS'], $code_collectivite,$num_matricule,$num_secu,$nom_prenom);
                        $nb_pop = count($populations);
                        if($nb_pop!=0){
                            ?>
                            <div class="row">
                                <div class="col-sm-12">
                                    <table class="table table-bordered table-sm table-hover" id="populations_table">
                                        <thead class="bg-info">
                                        <tr>
                                            <th width="5">N°</th>
                                            <td>TYPE</td>
                                            <th width="100">N° SECU PAY.</th>
                                            <th width="100">N° SECU AYD.</th>
                                            <th width="125">N° MATRICULE PAY.</th>
                                            <th width="125">N° MATRICULE AYD.</th>
                                            <th width="10">CIVILITE</th>
                                            <th>NOM & PRENOM(S)</th>
                                            <th width="80">DATE NAISS.</th>
                                            <th width="10">GENRE</th>
                                            <th width="5"></th>
                                            <th width="5"></th>
                                            <th width="5"></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        $ligne = 1;
                                        foreach ($populations as $population) {
                                            if($population['TYPE']=='T' || $population['TYPE']=='RET' || $population['TYPE']=='SAL' || $population['TYPE']=='PAY'){
                                                if($population['NUM_MATRICULE']==$population['PAYEUR_NUM_MATRICULE']){
                                                    $type_benef = 'PAYEUR';
                                                    $class_type= 'style="font-weight: bold"';
                                                }else{
                                                    $type_benef = '<b style="color: #ff253a">PAYEUR</b>';
                                                    $class_type= '';
                                                }

                                            }elseif($population['TYPE']=='C' || $population['TYPE']=='CJT'){
                                                $type_benef = 'CONJOINT';
                                                $class_type= '';
                                            }else{
                                                $type_benef = 'ENFANT';
                                                $class_type= '';
                                            }
                                            ?>
                                            <tr <?=$class_type;?>>
                                                <td align="right"><?= $ligne;?></td>
                                                <td align="center"><?=$type_benef;?></td>
                                                <td><?= $population['PAYEUR_NUM_SECU'];?></td>
                                                <td><a target="_blank" href="<?=URL.'affiliation/ogd/assure.php?code-ogd='.$trouver_ogd_collectivite['CODE_OGD_COTISATIONS'].'&num-secu='.$population['NUM_SECU'];?>"><?=$population['NUM_SECU'];?></td>
                                                <td><?= $population['PAYEUR_NUM_MATRICULE'];?></td>
                                                <td><b><?= $population['NUM_MATRICULE'];?></b></td>
                                                <td><?= $population['CIVILITE'];?></td>
                                                <td><?= $population['NOM'].' '.$population['PRENOMS'];?></td>
                                                <td><?php if(!empty($population['DATE_NAISSANCE'])){echo date('d/m/Y',strtotime($population['DATE_NAISSANCE']));} ?></td>
                                                <td><?= $population['SEXE'];?></td>
                                                <td>
                                                    <a target="_blank" href="<?= URL . 'affiliation/ogd/population.php?code-ogd=' . $trouver_ogd_collectivite['CODE_OGD_COTISATIONS'] . '&num-matricule=' . $population['PAYEUR_NUM_MATRICULE']; ?>" class="badge badge-secondary"><i class="fa fa-eye"></i></a>
                                                </td>
                                                <td><button type="button" class="badge badge-info details_population" data-toggle="modal" data-target="#editionPopulationModal" id="<?=$population['ID'];?>"><i class="fa fa-edit"></i></button></td>
                                                <td><button type="button" class="badge badge-warning decl_cot_individu" data-toggle="modal" data-target="#declarationCotisationIndividuelModal" id="<?=$population['ID'];?>"><i class="fa fa-dollar-sign"></i></button></td>
                                            </tr>
                                            <?php
                                            $ligne++;
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <?php
                        }else{

                            echo '<p class="alert alert-danger" align="center">AUCUNE POPULATION N\'A ETE TROUVEE. PRIERE VERIFIER VOS DECLARATIONS DE POPULATIONS</p>';
                        }
                    }else{
                        echo '<p class="alert alert-danger" align="center">L\'OGD DE VOTRE COLLECTIVITE N\'A PAS ETE DEFINI. VEUILLEZ CONTACTER VOTRE ADMINISTRATEUR SYSTEME.</p>';
                    }
                }
            }
        }else{
            echo '<p class="alert alert-danger" align="center">VOTRE IDENTIFIANT EST INCORRECT. VOUS NE POUVEZ PAS EFFECTUER CETTE ACTION. VEUILLEZ CONTACTER VOTRE ADMINISTRATEUR SYSTEME.</p>';
        }
    }else{
        echo '<p class="alert alert-danger" align="center">VOTRE SESSION EST INACTIVE. PRIERE VOUS RECONNECTER POUR POURSUIVRE CETTE ACTION.</p>';
    }
}else{
    echo '<p class="alert alert-danger" align="center">VOTRE SESSION EST INACTIVE. PRIERE VOUS RECONNECTER POUR POURSUIVRE CETTE ACTION.</p>';
}
?>
