<?php
require_once '../../../Classes/UTILISATEURS.php';
if(isset($_SESSION['ECMU_USER_ID'])) {
    $session_user = $_SESSION['ECMU_USER_ID'];
    if (!empty($session_user)) {
        $UTILISATEURS = new UTILISATEURS();
        $utilisateur_existe = $UTILISATEURS->trouver($session_user, null, null);
        if (!empty($utilisateur_existe['ID_UTILISATEUR'])) {

            require_once "../../../Classes/PARTENAIRES.php";

            $PARTENAIRES = new PARTENAIRES();

            $numero_secu_demande = trim($_POST['numero_secu_demande']);

            $date_debut = strtoupper(date('Y-m-d',strtotime(str_replace('/','-',trim($_POST['date_debut']))))).' 00:00:00';
            $date_fin = strtoupper(date('Y-m-d',strtotime(str_replace('/','-',trim($_POST['date_fin']))))).' 23:59:59';
            $numero_suivi_demande = trim($_POST['numero_suivi_demande']);
            $motif_demande = trim($_POST['motif_demande']);
            $statut_demande = trim($_POST['statut_demande']);

            if(empty($numero_secu_demande) && empty($date_debut) && empty($date_fin) && empty($numero_suivi_demande) && empty($motif_demande) && empty($statut_demande)){
                echo '<p class="alert alert-danger" align="center">PRIERE DEFINIR UN CRITERE DE RECHERCHE.</p>';
            }else{
                $demandes_duplicata = $PARTENAIRES->moteur_rechercher_duplicata($numero_suivi_demande,$numero_secu_demande,$date_debut,$date_fin,$statut_demande,$motif_demande);
                $total_demande = count($demandes_duplicata);
                if(count($demandes_duplicata) == 0){
                    ?>
                    <p class="alert alert-danger" align="center">AUCUNE DEMANDE EN ATTENTE.</p>
                    <?php
                }
                else{
                    $ver_pai = $total_demande;
                    foreach ($demandes_duplicata as $demande) {
                        $motif = $PARTENAIRES->trouver_motif($demande['MOTIF_DEMANDE']);
                        if($motif['STATUT_PAIEMENT'] == 1 ){
                            if(empty($demande['NUM_TRANSACTION_PAIEMENT'])){
                                $ver_pai --;
                            }
                        }
                    }

                    if($ver_pai==0){
                        echo '<p class="alert alert-danger" align="center">AUCUNE DEMANDE EN ATTENTE.</p>';
                    }else{
                    ?>
                    <table class="table table-bordered table-hover table-sm table-responsive-sm dataTable">
                        <thead class="bg-secondary text-white">
                        <tr align="center">
                            <td width="5"></td>
                            <td>DATE DEMANDE</td>
                            <td>N° DEMANDE</td>
                            <td>N° SECU </td>
                            <td>NOM</td>
                            <td>PRENOMS</td>
                            <td>DATE NAISSANCE</td>
                            <td>MOTIF DEMANDE</td>
                            <td width="5"></td>
                        </tr>
                        </thead>

                        <tbody>
                        <?php
                        $i=1;
                        foreach ($demandes_duplicata as $demande) {
                            $pai = 1;
                            $motif = $PARTENAIRES->trouver_motif($demande['MOTIF_DEMANDE']);
                            if($motif['STATUT_PAIEMENT'] == 1 ){
                                if(empty($demande['NUM_TRANSACTION_PAIEMENT'])){
                                    $pai = 0;
                                }
                            }
                            if($pai==1){
                            ?>
                            <tr>
                                <td width="5" align="center"><?=$i;?></td>
                                <td align="center"><?=date('d/m/Y',strtotime($demande['DATE_DEMANDE']));?></td>
                                <td align="center"><?=$demande['ID_DEMANDE'];?></td>
                                <td align="center"><?=$demande['NUM_SECU'];?></td>
                                <td align="center"><?=$demande['NOM'];?></td>
                                <td align="center"><?=$demande['PRENOMS'];?></td>
                                <td align="center"><?=date('d/m/Y',strtotime($demande['DATE_NAISSANCE']));?></td>
                                <td align="center"><?=$motif['MOTIF_LIBELLE'];?></td>
                                <td><a href="<?=URL.'partenaire/demande-duplicata.php?id='.$demande['ID_DEMANDE'];?>" class="badge badge-info details_population" id="<?=$demande['ID_DEMANDE'];?>"><i class="fa fa-eye"></i></a></td>
                            </tr>
                            <?php
                            }
                            $i++;
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

}
?>
