<?php
require_once '../../../Classes/UTILISATEURS.php';
if(isset($_SESSION['ECMU_USER_ID'])){
    $session_user = $_SESSION['ECMU_USER_ID'];
    if(!empty($session_user)){
        $UTILISATEURS = new UTILISATEURS();
        $utilisateur_existe = $UTILISATEURS->trouver($session_user,null,null);

        if(!empty($utilisateur_existe['ID_UTILISATEUR'])){
            if($utilisateur_existe['ACTIF'] != 1){
                ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <p>VOTRE IDENTIFIANT A ETE DESACTIVE. VOUS NE POUVEZ PAS EFFECTUER CETTE ACTION. VEUILLEZ CONTACTER VOTRE ADMINISTRATEUR SYSTEME.</p>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <?php
            }else{
                require_once '../../../Classes/ETABLISSEMENTSSANTE.php';
                require_once '../../../Classes/ASSURES.php';
                require_once '../../../Classes/DISTRIBUTIONMASQUES.php';
                $ETABLISSEMENTSSANTE = new ETABLISSEMENTSSANTE();
                $ASSURES = new ASSURES();
                $DISTRIBUTIONMASQUES = new DISTRIBUTIONMASQUES();

                $numero_secu = trim($_POST['numero_secu']);
                $code_ets = trim($_POST['code_ets']);
                $date_debut = strtoupper(date('Y-m-d',strtotime(str_replace('/','-',$_POST['date_debut']))));
                $date_fin = strtoupper(date('Y-m-d',strtotime(str_replace('/','-',$_POST['date_fin']))));

                $distributions = $DISTRIBUTIONMASQUES->moteur_recherche_par_centre($numero_secu, $date_debut, $date_fin, $code_ets);
                $nb_masques_distribues = count($distributions);
                if($nb_masques_distribues == 0) {
                    ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <p align="center">AUCUN RESULTAT CORRESPONDANT POUR CETTE RECHERCHE.</p>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <?php
                } else {
                    ?>
                    <p align="center"><button type="button" class="btn btn-sm btn-info" id="btn_generer_bordereau_masq_dst">Télécharger Bordereau</button></p>
                    <table class="table table-bordered table-hover table-sm table-responsive-sm dataTable">
                        <thead class="bg-secondary text-white">
                        <tr>
                            <th width="5" class="align_center">N°</th>
                            <th width="100" class="align_center">N° SECU</th>
                            <th class="align_center">NOM & PRENOM(S) DES ASSURES</th>
                            <th class="align_center">DISTRIBUE PAR</th>
                            <th width="50" class="align_center">DATE</th>
                            <th width="10" class="align_center">HEURE</th>
                            <th width="50" class="align_center">DATE FIN</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $ligne = 1;
                        foreach ($distributions as $dst_masq) {
                            $assure = $ASSURES->trouver($dst_masq['NUM_SECU']);
                            $agent = $UTILISATEURS->trouver($dst_masq['USER_REG'],null,null);
                            ?>
                            <tr>
                                <td class="align_right"><?= $ligne;?></td>
                                <td class="align_center"><b><?= $dst_masq['NUM_SECU'];?></b></td>
                                <td><b class="info_assure"><?= $assure['NOM'].' '.$assure['PRENOM'];?></b></td>
                                <td><?= $agent['NOM'].' '.$agent['PRENOM'];?></td>
                                <td class="align_center"><?= date('d/m/Y',strtotime($dst_masq['M_DATE_DEBUT']));?></td>
                                <td class="align_center"><?= date('H:i',strtotime($dst_masq['DATE_REG']));?></td>
                                <td class="align_center"><?= date('d/m/Y',strtotime($dst_masq['M_DATE_FIN']));?></td>
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
        }else{
            ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <p>VOTRE IDENTIFIANT EST INCORRECT. VOUS NE POUVEZ PAS EFFECTUER CETTE ACTION. VEUILLEZ CONTACTER VOTRE ADMINISTRATEUR SYSTEME.</p>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php
        }
    }else{
        ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <p>VOTRE IDENTIFIANT EST INCORRECT. VOUS NE POUVEZ PAS EFFECTUER CETTE ACTION. VEUILLEZ CONTACTER VOTRE ADMINISTRATEUR SYSTEME.</p>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <?php
    }
}else{
    ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <p>VOTRE IDENTIFIANT EST INCORRECT. VOUS NE POUVEZ PAS EFFECTUER CETTE ACTION. VEUILLEZ CONTACTER VOTRE ADMINISTRATEUR SYSTEME.</p>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <?php
}
?>
<script>
    $(function () {
        $('.dataTable').DataTable();
    })
</script>
<script>
    $("#btn_generer_bordereau_masq_dst").click(function () {
        mywindow = window.open( "bordereau-distribution-masque-impression.php?numero_secu="+"<?=$numero_secu;?>"+"&code_ets="+"<?=$code_ets;?>"+"&date_debut="+"<?=$date_debut;?>"+"&date_fin="+"<?=$date_fin;?>");

        mywindow.focus();
        return true;
    });
</script>