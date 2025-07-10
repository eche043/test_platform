<?php
require_once '../../../Classes/UTILISATEURS.php';
if(isset($_SESSION['ECMU_USER_ID'])) {
    $session_user = $_SESSION['ECMU_USER_ID'];
    if (!empty($session_user)) {
        $UTILISATEURS = new UTILISATEURS();
        $utilisateur_existe = $UTILISATEURS->trouver($session_user, null, null);
        $utilisateur_existe['CODE_OGD_P'];
        if (!empty($utilisateur_existe['ID_UTILISATEUR'])) {

            $user = trim($_POST['user']);
            $numero_facture = trim($_POST['numero_facture']);
            $type_verification = trim($_POST['type_verification']);
            if(!empty($numero_facture) && !empty($type_verification)) {
            require_once "../../../Classes/FACTURES.php";
            require_once "../../../Classes/UTILISATEURS.php";

            $FACTURES = new FACTURES();
            if($type_verification=='DECA'){
            $factures = $FACTURES->trouver_facture_verification($numero_facture,0);
            //$factures = $FACTURES->trouver_facture_verification_deca($numero_facture,$utilisateur_existe['CODE_OGD_P'],'T',0);
//            $factures = $FACTURES->trouver_facture_verification_deca('896','02102000','T',0);
            }elseif($type_verification=='LIQ'){
            $factures = $FACTURES->trouver_facture_verification_deca($numero_facture,$utilisateur_existe['CODE_OGD_P'],'T',1);
            }
            $nb_factures = count($factures);
            if($nb_factures == 0) {
            echo '<p align="center" class="alert-danger">AUCUNE FACTURE NE CORRESPOND A VOTRE DEMANDE.</p>';
            }else {
            echo '<p align="center"><b class="alert-info">'.number_format($nb_factures,'0','',' ').' FACTURE(S) TROUVEE(S).</b></p>';
            ?>
            <table class="table table-bordered table-sm dataTable">
                <thead class="bg-primary">
                <tr>
                    <th>N°</th>
                    <th width="100">DATE DE SOINS</th>
                    <th>TYPE</th>
                    <th width="100">N° FACTURE</th>
                    <th width="100">N° FS INITIALE</th>
                    <th width="100">NUM SECU</th>
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
                        <td><?= $ligne;?></td>
                        <td><?=date('d/m/Y',strtotime($facture['DATE_SOINS']));?></td>
                        <td><?= $facture['TYPE_FEUILLE'];?></td>
                        <td><?= $facture['FEUILLE'];?></td>
                        <td><?= $facture['NUM_FS_INITIALE'];?></td>
                        <td title="<?=$facture['NOM'].' '.$facture['PRENOM'];?>"><?= $facture['NUM_SECU'];?></td>
                        <td title="<?=$facture['ETABLISSEMENT'];?>"><?= $facture['NOM_ETS'];?></td>
                        <td><a class="btn btn-btn-xs btn-info btn-sm" href="<?= URL.'ogd-prestations/facture.php?numero='.$facture['FEUILLE'].'&type='.$type_verification;?>"><b class="fa fa-eye"></b></a></td>
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


