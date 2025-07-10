<?php
/**
 * Created by PhpStorm.
 * User: mohamed.kone
 * Date: 06/02/2020
 * Time: 21:11
 */

$code_acte = $_POST['code'];
$libelle_acte = $_POST['libelle'];
$titre_acte = $_POST['titre'];

require_once "../../Classes/UTILISATEURS.php";
require_once "../../Classes/ACTESMEDICAUX.php";
$ACTESMEDICAUX = new ACTESMEDICAUX();
$actes = $ACTESMEDICAUX->trouver_actes(strtoupper($libelle_acte),$titre_acte,$code_acte);
$nb_actes = count($actes);

if($nb_actes == 0) {
    echo '<p align="center" class="text-danger">Aucun résultat de correspond à votre recherche</p>';
}else {
    echo '<p align="center" class="text-success">Résultats: <b>'.$nb_actes.'</b></p>';
    ?>
    <table class="table table-bordered table-hover table-sm">
        <thead class="bg-info">
        <tr>
            <th width="5">N°</th>
            <th>CODE</th>
            <th>LIBELLE</th>
            <th>LETTRE CLE</th>
            <th>COEFF.</th>
            <th>TARIF</th>
            <th width="50" title="Soumis à entente préalable">SEP</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $ligne = 1;
        foreach ($actes as $acte) {
            ?>
            <tr>
                <td><?= $ligne;?></td>
                <td><b><?= $acte['CODE'];?></b></td>
                <td><?= $acte['LIBELLE'];?></td>
                <td><?= $acte['LETTRE_CLE'];?></td>
                <td align="right"><?= $acte['COEFFICIENT'];?></td>
                <td align="right">
                    <?php
                    if($acte['TYPE_ACTE'] == 'NGAP') {
                        $lettres_cle = $ACTESMEDICAUX->lettre_cle_trouve($acte['LETTRE_CLE']);

                        $tarif = $lettres_cle['PRIX_UNITAIRE'] * $acte['COEFFICIENT'];
                    }else {
                        $tarif = $acte['TARIF'];
                    }
                    echo '<b>'.number_format($tarif,'0','',' ').'</b>';
                    ?>
                </td>
                <td><b style="color: #<?php if($acte['entente_prealable'] == 1) {echo '5cb85c';}else {echo 'FF0000';} ?>"><?php if($acte['ENTENTE_PREALABLE'] == 1) {echo 'OUI';}else {echo 'NON';} ?></b></td>
            </tr>
            <?php
            $ligne++;
        }
        ?>
        </tbody>
    </table>
    <?php
}

