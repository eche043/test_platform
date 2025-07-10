<?php

require_once '../../../Classes/UTILISATEURS.php';
if(isset($_SESSION['ECMU_USER_ID'])) {
    $session_user = $_SESSION['ECMU_USER_ID'];
    if (!empty($session_user)) {
        $UTILISATEURS = new UTILISATEURS();
        $utilisateur_existe = $UTILISATEURS->trouver($session_user, null, null);

        if (!empty($utilisateur_existe['ID_UTILISATEUR'])) {
            $num_facture = trim($_POST['num_facture']);
            if(!empty($num_facture)) {
                require_once '../../../Classes/FACTURES.php';
                $FACTURES = new FACTURES();
                $factures = $FACTURES->trouver_liste_factures($num_facture);
                $nb_facures = count($factures);
                if($nb_facures == 0) {
                    echo '<p align="center" class="text-info">Aucun résultat ne correspond à votre recherche</p>';
                }else {
                    ?>
                    <table class="table table-bordered table-sm table-hover">
                        <thead class="bg-info">
                        <tr>
                            <th width="5">N°</th>
                            <th width="100">N° FACTURE</th>
                            <th width="100">N° FS. INIT.</th>
                            <th width="20">TYPE</th>
                            <th>PATIENT</th>
                            <th>ETABLISSEMENT</th>
                            <th width="5"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $ligne = 1;
                        foreach ($factures as $facture) {
                            ?>
                            <tr>
                                <td align="right"><?= $ligne;?></td>
                                <td align="right"><?php if($facture['FEUILLE'] == $num_facture){echo '<b class="text-success">'.$facture['FEUILLE'].'</b>';}else {echo $facture['FEUILLE'];} ?></td>
                                <td align="right"><?php if($facture['NUM_FS_INITIALE'] == $num_facture){echo '<b class="text-success">'.$facture['NUM_FS_INITIALE'].'</b>';}else {echo $facture['NUM_FS_INITIALE'];} ?></td>
                                <td><?= $facture['TYPE_FEUILLE'];?></td>
                                <td><?= $facture['NOM'].' '.$facture['PRENOM'];?></td>
                                <td><?= $facture['NOM_ETS'];?></td>
                                <td><a href="<?= URL.'centre-saisie/facture.php?type='.$facture['TYPE_FEUILLE'].'&num='.$facture['FEUILLE'];?>" class="badge badge-info"><i class="fa fa-eye"></i></a></td>
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
    }

}

?>