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
                /*$json = array(
                    'status' => false,
                    'message' => 'VOTRE IDENTIFIANT A ETE DESACTIVE. VOUS NE POUVEZ PAS EFFECTUER CETTE ACTION. VEUILLEZ CONTACTER VOTRE ADMINISTRATEUR SYSTEME.'
                );*/
                echo '<p class="alert alert-danger" align="center">VOTRE IDENTIFIANT A ETE DESACTIVE. VOUS NE POUVEZ PAS EFFECTUER CETTE ACTION. VEUILLEZ CONTACTER VOTRE ADMINISTRATEUR SYSTEME.</p>';
            }else{
                if(empty($_POST["code_collectivite"])){
                    /*$json = array(
                        'status' => false,
                        'message' => 'VOTRE COLLECTIVITE N\'A PAS ETE DEFINIE. VOUS NE POUVEZ PAS EFFECTUER CETTE ACTION. VEUILLEZ CONTACTER VOTRE ADMINISTRATEUR SYSTEME.'
                    );*/

                    echo '<p class="alert alert-danger" align="center">VOTRE COLLECTIVITE N\'A PAS ETE DEFINIE. VOUS NE POUVEZ PAS EFFECTUER CETTE ACTION. VEUILLEZ CONTACTER VOTRE ADMINISTRATEUR SYSTEME.</p>';
                }else{
                    require_once '../../../Classes/ASSURES.php';
                    require_once '../../../Classes/COLLECTIVITES.php';
                    $ASSURES = new ASSURES();
                    $COLLECTIVITES = new COLLECTIVITES();

                    $code_collectivite = $_POST["code_collectivite"];
                    $trouver_ogd_collectivite = $COLLECTIVITES->trouver($code_collectivite);
                    if(isset($trouver_ogd_collectivite['CODE_OGD_COTISATIONS']) && !empty($trouver_ogd_collectivite['CODE_OGD_COTISATIONS'])) {

                        $mois = str_pad(trim($_POST["mois_decl_cotisation"]),2,'0',STR_PAD_LEFT);
                        $annee = trim($_POST["annee_decl_cotisation"]);
                        $trouver_populations = $COLLECTIVITES->trouver_population_pour_declarations($mois,$annee,$code_collectivite);
                        $nb_pop  = count($trouver_populations);
                        if($nb_pop!=0){
                            ?>
                            <table class="table table-bordered table-hover table-sm table-responsive-sm dataTable" id="table_declaration_cotisations">
                                <thead class="bg-secondary text-white">
                                <tr align="center">
                                    <th>Total Population </th>
                                    <th>Total Montant à Payer (F CFA)</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td align="center"><?=$nb_pop;?></td>
                                    <td align="center"><?=$nb_pop*1000;?></td>
                                </tr>
                                <tr>
                                    <td colspan="2" align="center"><button type="button" class="btn btn-link" id="button_details_declarations_population" data-toggle="collapse" data-target="#details_declarations_population"><b> Détails</b></button></td>
                                </tr>
                                </tbody>
                            </table>
                            <div class="collapse" id="details_declarations_population">
                                <table class="table table-bordered table-hover table-sm table-responsive-sm dataTable">
                                    <thead class="bg-info">
                                    <tr align="center">
                                        <th> </th>
                                        <th>NUM SECU </th>
                                        <th>NUM OGD (CNPS) </th>
                                        <th>NOM & PRENOMS</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $l = 1;
                                    foreach ($trouver_populations as $pop){
                                        ?>
                                        <tr>
                                            <td align="center"><?=$l;?></td>
                                            <td align="center"><?=$pop['BENEFICIAIRE_NUM_SECU'];?></td>
                                            <td align="center"><?=$pop['BENEFICIAIRE_NUM_MATRICULE'];?></td>
                                            <td align="left"><?=$pop['BENEFICIAIRE_NOM'].' '.$pop['BENEFICIAIRE_PRENOMS'];?></td>
                                        </tr>
                                        <?php
                                        $l++;
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php
                            /*
                            $json = array(
                                'status' => true,
                                'total' =>$nb_pop
                            );
                            */
                        }else{
                            /*$json = array(
                                'status' => false,
                                'message' => 'AUCUNE POPULATION N\'A ETE TROUVEE. PRIERE VERIFIER VOS DECLARATIONS DE POPULATIONS'
                            );*/
                            echo '<p class="alert alert-danger" align="center">AUCUNE POPULATION N\'A ETE TROUVEE. PRIERE VERIFIER VOS DECLARATIONS DE POPULATIONS</p>';
                        }
                    }else{
                        /*$json = array(
                            'status' => false,
                            'message' => 'L\'OGD DE VOTRE COLLECTIVITE N\'A PAS ETE DEFINI. VEUILLEZ CONTACTER VOTRE ADMINISTRATEUR SYSTEME.'
                        );*/
                        echo '<p class="alert alert-danger" align="center">L\'OGD DE VOTRE COLLECTIVITE N\'A PAS ETE DEFINI. VEUILLEZ CONTACTER VOTRE ADMINISTRATEUR SYSTEME.</p>';
                    }
                }
            }
        }else{
            /*$json = array(
                'status' => false,
                'message' => 'VOTRE IDENTIFIANT EST INCORRECT. VOUS NE POUVEZ PAS EFFECTUER CETTE ACTION. VEUILLEZ CONTACTER VOTRE ADMINISTRATEUR SYSTEME.'
            );*/
            echo '<p class="alert alert-danger" align="center">VOTRE IDENTIFIANT EST INCORRECT. VOUS NE POUVEZ PAS EFFECTUER CETTE ACTION. VEUILLEZ CONTACTER VOTRE ADMINISTRATEUR SYSTEME.</p>';
        }
    }else{
        /*$json = array(
            'status' => false,
            'message' => 'VOTRE SESSION EST INACTIVE. PRIERE VOUS RECONNECTER POUR POURSUIVRE CETTE ACTION.'
        );*/
        echo '<p class="alert alert-danger" align="center">VOTRE SESSION EST INACTIVE. PRIERE VOUS RECONNECTER POUR POURSUIVRE CETTE ACTION.</p>';
    }
}else{
    /*$json = array(
        'status' => false,
        'message' => 'VOTRE SESSION EST INACTIVE. PRIERE VOUS RECONNECTER POUR POURSUIVRE CETTE ACTION.'
    );*/
    echo '<p class="alert alert-danger" align="center">VOTRE SESSION EST INACTIVE. PRIERE VOUS RECONNECTER POUR POURSUIVRE CETTE ACTION.</p>';
}
//echo json_encode($json);
?>
