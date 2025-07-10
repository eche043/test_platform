<?php
/**
 * Created by PhpStorm.
 * User: mohamed.kone
 * Date: 07/02/2020
 * Time: 07:46
 */

$code = $_POST['code'];
$libelle = $_POST['libelle'];
$forme = $_POST['forme'];
$conditionnement = $_POST['conditionnement'];

require_once '../../Classes/UTILISATEURS.php';
require_once "../../Classes/MEDICAMENTS.php";
$MEDICAMENTS = new MEDICAMENTS();
$actes = $MEDICAMENTS->trouver($code,strtoupper($libelle),$forme,$conditionnement);
$nb_medicaments = count($actes);
if($nb_medicaments == 0) {
    echo '<p align="center" class="text-danger">Aucun résultat de correspond à votre recherche</p>';
}else {
    echo '<p align="center" class="text-success">Résultats: <b>'.$nb_medicaments.'</b></p>';
    ?>
    <table class="table table-bordered table-hover table-sm">
        <thead class="bg-info">
        <tr>
            <th>N°</th>
            <th>Code</th>
            <th>Nom Commercial</th>
            <th>Dosage</th>
            <th>Forme</th>
            <th>Conditionnement</th>
            <th>Tarif</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $i = 1;
        foreach ($actes as $resultat_medoc) {
            ?>
            <tr>
                <td><?= $i;?></td>
                <td><b><?= $resultat_medoc['EAN13'];?></b></td>
                <td><?= $resultat_medoc['LIBELLE'];?></td>
                <td><?php if(!empty($resultat_medoc['DOSAGE1'])){ echo $resultat_medoc['DOSAGE1'].' '.$resultat_medoc['UNITE1'];} else{echo $resultat_medoc['DOSAGE_UNITE'];}?></td>
                <td><?= $resultat_medoc['FORME'];?></td>
                <td><?= $resultat_medoc['CONDITIONNEMENT'];?></td>
                <td align="right"><b><?= number_format((floor($resultat_medoc['PP'] /5) *5),'0','',' ');?></b></td>
            </tr>
            <?php
            $i++;
        }
        ?>
        </tbody>

    </table><br />
    <?php
}

?>