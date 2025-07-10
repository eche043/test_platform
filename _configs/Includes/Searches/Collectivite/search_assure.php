<?php
$num_secu = trim($_POST['num_secu']);
$code_collectivite = trim($_POST['code_collectivite']);
if(!empty($code_collectivite)) {
    if(!empty($num_secu)) {
        require_once '../../../Classes/UTILISATEURS.php';
        require_once '../../../Classes/ASSURES.php';
        $ASSURES = NEW ASSURES();
        $assure = $ASSURES->trouver_assure_autre_payeur($num_secu);
        $nb_assures = count($assure);
        if($nb_assures == 0) {
            echo '<p align="center" class="text-danger">Le numéro sécu saisi est incorrect.</p>';
        }else {
            ?>
            <table class="table table-bordered table-hover table-sm">
                <thead class="bg-info">
                <tr>
                    <th width="50">N°</th>
                    <th width="120">N° SECU</th>
                    <th>CIVILITE</th>
                    <th>NOM</th>
                    <th>NOM PATRONYMIQUE</th>
                    <th>PRENOM(S)</th>
                    <th>GENRE</th>
                    <th width="130">DATE NAISSANCE</th>
                    <th width="5"></th>
                </tr>
                </thead>
                <tbody>
                <?php
                $ligne = 1;
                //foreach ($assures as $assure) {
                $civilite = $ASSURES->trouver_assure_civilite($assure['CIVILITE']);
                $genre = $ASSURES->trouver_assure_genre($assure['SEXE']);
                ?>
                <tr>
                    <td align="right"><?= $ligne;?></td>
                    <td><b><?= $assure['NUM_SECU'];?></b></td>
                    <td><?= $civilite['LIBELLE'];?></td>
                    <td><?= $assure['NOM'];?></td>
                    <td><?= $assure['NOM_PATRONYMIQUE'];?></td>
                    <td><?= $assure['PRENOM'];?></td>
                    <td><?= $genre['LIBELLE'];?></td>
                    <td><?= date('d/m/Y',strtotime($assure['DATE_NAISSANCE']));?></td>
                    <td><a href="<?= URL.'collectivite/assure.php?num-secu='.$assure['NUM_SECU'];?>" class="badge badge-primary"><i class="fa fa-eye"></i></a></td>
                </tr>
                <?php
                //$ligne++;
                //}
                ?>
                </tbody>
            </table>
            <?php
        }
    }else {
        echo '<p align="center" class="text-danger">Veuillez saisir un numéro sécu SVP</p>';
    }
}else {
    echo '<p align="center" class="text-danger">Une erreur est survenue lors de la recherche de l\'assuré. Veuillez contacter le service support</p>';
}