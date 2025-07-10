<?php
/**
 * Created by PhpStorm.
 * User: mohamed.kone
 * Date: 07/02/2020
 * Time: 09:24
 */


    $code = $_POST['code'];
    $libelle = $_POST['libelle'];

    require_once "../../Classes/UTILISATEURS.php";
    require_once "../../Classes/PATHOLOGIES.php";
    $PATHOLOGIES = new PATHOLOGIES();
    $pathologies = $PATHOLOGIES->trouver($code,strtoupper($libelle));
    $nb_pathologies = count($pathologies);
    if($nb_pathologies == 0) {
    echo '<p align="center" class="text-danger">Aucun résultat de correspond à votre recherche</p>';
    }else {
    echo '<p align="center" class="text-success">Résultats: <b>'.$nb_pathologies.'</b></p>';
    ?>
    <table class="table table-bordered table-hover table-sm">
        <thead class="bg-primary">
        <tr>
            <th width="5">N°</th>
            <th>CODE</th>
            <th>LIBELLE</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $ligne = 1;
        foreach ($pathologies as $affection) {
            ?>
            <tr>
                <td><?= $ligne;?></td>
                <td><b><?= $affection['CODE'];?></b></td>
                <td><?= $affection['LIBELLE'];?></td>
            </tr>
            <?php
        }
        ?>
        </tbody>
    </table>
<?php
}
