<?php
require_once '../../../Classes/UTILISATEURS.php';
if(isset($_SESSION['ECMU_USER_ID']) || !empty($_SESSION['ECMU_USER_ID'])) {
    $UTILISATEURS = new UTILISATEURS();
    $user = $UTILISATEURS->trouver($_SESSION['ECMU_USER_ID'], NULL, NULL);
    if (!empty($user['ID_UTILISATEUR'])) {

        if(trim($_POST['statut']) == 0 || trim($_POST['statut']) == '') {
            $statut = NULL;
        }else{  $statut = trim($_POST['statut']);}

        $type_ep = trim($_POST['type_ep']);

        if(isset($_POST['date_demande'])) {
            if(empty($_POST['date_demande'])) {
                $date_demande =  NULL;
            }else {
                $date_demande = date('d-M-y',strtotime(trim($_POST['date_demande'])));
            }
        }else {
            $date_demande =  NULL;
        }

        $code_ets = trim($_POST['entente_ets']);

        if(!empty($statut || !empty($type_ep) || !empty($date_demande) || !empty($code_ets))) {
            require_once "../../../Classes/ENTENTESPREALABLES.php";
            require_once "../../../Classes/ASSURES.php";
            require_once "../../../Classes/ETABLISSEMENTSSANTE.php";
            require_once "../../../Classes/OGD.php";

            $ENTENTESPREALABLES = new ENTENTESPREALABLES();
            $EtablissementSante = new ETABLISSEMENTSSANTE();
            $OGDPrestations = new OGD();
            $ASSURES = new ASSURES();

            if(!empty($user['CODE_OGD_P'])){

                $code_ogd = $user['CODE_OGD_P'];

                $ententesprealables = $ENTENTESPREALABLES->moteur_recherche($statut, $type_ep, $code_ogd, $date_demande, $code_ets);
//                $ententesprealables = $ENTENTESPREALABLES->moteur_recherche('1', '%EXP%', '%02101000%', '%%', '%000100130%' );
                $nb_ententesprealables = count($ententesprealables);

                if($nb_ententesprealables == 0) {
                    echo '<p align="center" class="alert-info"><b>Aucun</b> résultat ne correspond à votre recherche</p>';
                }else {
                    echo '<p align="center" class="alert-info"><b>'.number_format($nb_ententesprealables,'0','',' ').'</b> résultat(s) correspond(ent) à votre recherche</p>';
                    ?>
                    <table class="table table-sm table-bordered table-hover" id="dataTable">
                        <thead class="bg-info">
                        <tr>
                            <th>N°</th>
                            <th>N° EP</th>
                            <th width="100">DATE</th>
                            <th>TYPE EP</th>
                            <th>PATIENT</th>
                            <th>ETABLISSEMENT</th>
                            <th width="100">STATUT</th>
                            <th width="5"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $ligne = 1;
                        foreach ($ententesprealables as $ententeprealable) {

                            $assure = $ASSURES->trouver($ententeprealable['NUM_SECU']);
                            $ets = $EtablissementSante->trouver_etablissement_sante($ententeprealable['CODE_ETS']);

                            if($ententeprealable['STATUT'] == 0 || $ententeprealable['STATUT'] == NULL) {
                                $valeur = 'EN ATTENTE';
                            }elseif($ententeprealable['STATUT'] == 1) {
                                $valeur = 'VALIDEE';
                            }elseif($ententeprealable['STATUT'] == 2) {
                                $valeur = 'REFUSEE';
                            }else {
                                $valeur = 'PERIMEE';
                            }
                            ?>
                            <tr>
                                <td align="right"><?= $ligne;?></td>
                                <td align="right"><b><?= $ententeprealable['NUM_ENTENTE_PREALABLE'];?></b></td>
                                <td><?= date('d/m/Y',strtotime($ententeprealable['DATE_REG']));?></td>
                                <td><?= $ententeprealable['TYPE_EP'];?></td>
                                <td><?= $assure['NOM'].' '.$assure['PRENOM'];?></td>
                                <td><?= $ets['RAISON_SOCIALE'];?></td>
                                <td><?= $valeur;?></td>
                                <td><a href="<?= URL.'ogd-prestations/details-entente-prealable.php?numero='.$ententeprealable['NUM_ENTENTE_PREALABLE'];?>" class="btn btn-info btn-sm"><i class="fa fa-eye"></i></a></td>
                            </tr>
                            <?php
                            $ligne++;
                        }
                        ?>
                        </tbody>
                    </table>
                    <?php

                }
            }
        }

    }else{
        $json = array(
            'status' => false,
            'num_bordereau' => " CET UTILISATEUR N\'EXISTE PAS"
        );
    }
}else{

}


//echo json_encode($json);
