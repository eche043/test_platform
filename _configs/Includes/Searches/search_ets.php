<?php
$code = trim($_POST['code']);
$raison_sociale = trim(strtoupper($_POST['raison_sociale']));
$ville = trim(strtoupper($_POST['ville']));

if(!empty($code) || !empty($raison_sociale)|| !empty($ville)) {
    require_once "../../Classes/UTILISATEURS.php";
    require_once "../../Classes/ETABLISSEMENTSSANTE.php";
    $ETABLISSEMENTSSANTE = new ETABLISSEMENTSSANTE();

    $ets_n1 = $ETABLISSEMENTSSANTE->moteur_recherche($code,$ville,$raison_sociale);
    $nb_ets = count($ets_n1);
    if($nb_ets == 0) {
        echo '<p align="center" class="text-danger">Aucun résultat de correspond à votre recherche</p>';
    }else {
        echo '<p align="center" class="text-success">Résultats: <b>'.$nb_ets.'</b></p>';
        ?>
        <table class="table table-bordered table-hover table-sm">
            <thead class="bg-info">
            <tr>
                <th width="5">N°</th>
                <th>Code</th>
                <th>Raison sociale</th>
                <th>Ville</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $ligne = 1;
            foreach ($ets_n1 as $ets) {
                ?>
                <tr>
                    <td align="right"><?= $ligne;?></td>
                    <td width="50"><b><?= $ets['CODE_ETS'];?></b></td>
                    <td><?= $ets['RAISON_SOCIALE'];?></td>
                    <td><?= $ets['VILLE'];?></td>
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