<?php
/**
 * Created by PhpStorm.
 * User: mohamed.kone
 * Date: 06/02/2020
 * Time: 14:20
 */

include "../../../../_configs/Classes/UTILISATEURS.php";
$code_ets = trim($_POST['code_ets']);
$type_facture = trim($_POST['type_facture']);
if(empty($_POST['date_soins'])) {
    $date_soins = NULL;
}else {
    $date_soins = strtoupper(date('Y-m-d',strtotime(str_replace('/','-',$_POST['date_soins']))));;
}
$num_facture = trim($_POST['num_facture']);
$num_secu = trim($_POST['num_secu']);
$nom = strtoupper(trim($_POST['nom']));
if((!empty($type_facture) || !empty($date_soins) || strlen($num_facture) >= 2 || strlen($num_secu) >= 3 || strlen($nom) >= 3)) {
    include "../../../../_configs/Classes/FACTURES.php";
    $FACTURES = new FACTURES();
    $factures = $FACTURES->moteur_recherche_factures_assurances($code_ets, $type_facture,$date_soins,$num_facture,$num_secu,$nom, 'SNEDAICMU');
    $nb_factures = count($factures);

    if($nb_factures == 0) {
        echo '<p align="center" class="alert alert-info">Aucun résultat ne correspond à votre recherche.</p>';
    }else {
        echo '<p align="center" class="alert-success"><b>'.number_format($nb_factures,'0','',' ').'</b> RESULTAT(S) CORRESPOND(ENT) À VOTRE RECHERCHE</p>';
        ?>
        <table class="table table-sm table-bordered table-hover">
            <thead class="bg-dark" style="color: #ffffff">
            <tr>
                <th width="5">N°</th>
                <th width="260">ETABLISSEMENT</th>
                <th width="80">DATE SOINS</th>
                <th width="260">TYPE FACTURE</th>
                <th width="100">N° FACTURE</th>
                <th width="100">N° FS. INITIALE</th>
                <th width="100">N° SÉCU</th>
                <th>NOM & PRÉNOM(S)</th>
                <th width="5">ACTES</th>
                <th width="5">STATUT</th>
                <!--<th width="5"></th>-->
            </tr>
            </thead>
            <tbody>
            <?php
            $ligne = 1;
            foreach ($factures as $facture) {
                ?>
                <tr>
                    <td class="align_right"><?= $ligne;?></td>
                    <td><?= $facture['NOM_ETS'];?></td>
                    <td class="align_center"><?= date('d/m/Y',strtotime($facture['DATE_SOINS']));?></td>
                    <td><?= $facture['LIBELLE_TYPE_FACTURE'];?></td>
                    <td align="right"><a href="<?= URL.'assurance/facture.php?type='.$facture['CODE_TYPE_FACTURE'].'&num='.$facture['FEUILLE'];?>"><b><?= $facture['FEUILLE'];?></b></a></td>
                    <td align="right"><b><?= $facture['NUM_FS_INITIALE'];?></b></td>
                    <td><b><?= $facture['NUM_SECU'];?></b></td>
                    <td><?= $facture['NOM_PRENOM'];?></td>
                    <td align="right"><?= $facture['NB_ACTES'];?></td>
                    <td><b><?= $facture['STATUT'];?></b></td><!--
                    <td>
                        <?php
/*                        if(empty($facture['STATUT']) || $facture['STATUT'] == 'C' || $facture['STATUT'] == 'N' || $facture['STATUT'] == 'F' || $facture['STATUT'] == 'R') {
                            if(empty($facture['NUM_BORDEREAU'])){$tip_bord = 'AUCUN BORDEREAU';}else{$tip_bord = 'Bordereau N°'.$facture['NUM_BORDEREAU'];}
                            echo '<a href="'.URL.'agent/facture-edition.php?type='.$facture['CODE_TYPE_FACTURE'].'&num='.$facture['FEUILLE'].'" class="badge badge-success tp_infos" data-toggle="tooltip" data-placement="left" title="'.$tip_bord.'" ><i class="fa fa-edit"></i></a>';
                        }else {
                            if(empty($facture['NUM_BORDEREAU'])){$tip_bord = 'AUCUN BORDEREAU';}else{$tip_bord = 'Bordereau N°'.$facture['NUM_BORDEREAU'];}
                            echo '<button type="button" class="badge badge-secondary tp_infos" data-toggle="tooltip" data-placement="left" title="'.$tip_bord.'" ><i class="fa fa-info"></i></button>';
                        }
                        */?>
                    </td>-->
                </tr>
                <?php
                $ligne++;
            }
            ?>
            </tbody>
        </table>
        <script>
            $(".tp_infos").tooltip();
        </script>
        <?php
    }
}else {
    echo '<p align="center" class="alert alert-danger">Veuillez renseigner au moins un champ.</p>';
}