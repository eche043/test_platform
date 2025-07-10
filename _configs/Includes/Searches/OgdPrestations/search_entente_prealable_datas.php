<?php
require_once '../../../Classes/UTILISATEURS.php';
if(isset($_SESSION['ECMU_USER_ID'])) {
    $session_user = $_SESSION['ECMU_USER_ID'];
    if (!empty($session_user)) {
        $UTILISATEURS = new UTILISATEURS();
        $utilisateur_existe = $UTILISATEURS->trouver($session_user, null, null);
        $utilisateur_existe['CODE_OGD_P'];
        if (!empty($utilisateur_existe['ID_UTILISATEUR'])) {


            require_once "../../../Classes/ENTENTESPREALABLES.php";
            require_once "../../../Classes/ASSURES.php";
            require_once "../../../Classes/ETABLISSEMENTSSANTE.php";


            $ENTENTEPREALABLE = new ENTENTESPREALABLES();
            $ASSURES = new ASSURES();
            $ETABLISSEMENTSANTE = new ETABLISSEMENTSSANTE();
            $q = $ENTENTEPREALABLE->liste_entente_prealable_par_ogd($utilisateur_existe['CODE_OGD_P']);
            $nb_ententesprealables = count($q);
            if($nb_ententesprealables == 0) {
                echo '<p align="center" class="alert-primary">AUCUNE DEMANDE EN ATTENTE</p>';
            }else {
                echo '<p align="center"><b class="alert-danger">'.number_format($nb_ententesprealables,'0','',' ').' DEMANDE(S) EN ATTENTE DE VALIDATION.</b></p>';
                ?>
                <table class="table table-bordered table-sm dataTable">
                    <thead class="bg-primary">
                    <tr>
                        <th>N°</th>
                        <th>N°EP</th>
                        <th>TYPE</th>
                        <th width="120">DATE DEMANDE</th>
                        <th width="100">N° SECU</th>
                        <th>NOM & PRENOM</th>
                        <th width="5"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $ligne = 1;
                    foreach ($q as $entente) {
                        $assure = $ASSURES->trouver_assure($entente['NUM_SECU']);
                        ?>
                        <tr>
                            <td><?= $ligne;?></td>
                            <td><?= $entente['NUM_ENTENTE_PREALABLE'];?></td>
                            <td><?= $entente['TYPE_EP'];?></td>
                            <td><?= date('d/m/Y',strtotime($entente['DATE_REG']));?></td>
                            <td><?= $assure['NUM_SECU'];?></td>
                            <td><?= $assure['NOM'].' '.$assure['PRENOM'];?></td>
                            <td><a class="btn btn-btn-xs btn-info btn-sm" href="<?= URL.'ogd-prestations/details-entente-prealable.php?numero='.$entente['NUM_ENTENTE_PREALABLE'];?>"><b class="fa fa-eye"></b></a></td>
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
?>

