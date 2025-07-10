<?php
require_once '../../../Classes/UTILISATEURS.php';

if(isset($_SESSION['ECMU_USER_ID']) || !empty($_SESSION['ECMU_USER_ID'])) {
    $UTILISATEURS = new UTILISATEURS();
    $user = $UTILISATEURS->trouver($_SESSION['ECMU_USER_ID'],NULL,NULL);
    if(empty($user['ID_UTILISATEUR'])) {
        session_destroy();
        echo '<script>window.location.href="'.URL.'"</script>';
    }else {
        $modules = array_diff(explode(';',stream_get_contents($user['PROFIL'],-1)),array(""));
        $nb_modules = count($modules);
        if($nb_modules == 0) {
            session_destroy();
            echo '<script>window.location.href="'.URL.'"</script>';
        }else{
			require_once '../../../Classes/DISTRIBUTIONMASQUES.php';
            require_once '../../../Classes/ASSURES.php';
            $DISTRIBUTIONMASQUES = new DISTRIBUTIONMASQUES();
            $ASSURES = new ASSURES();
            if(in_array('AGAC',$modules)) {
                $user_ets = $UTILISATEURS->trouver_ets_utilisateur($user['ID_UTILISATEUR']);
                $user_profil = explode(';',$user['FSE']);
                if(in_array('MED',$user_profil)) {
                    ?>
                    <div class="col">
                        <p class="titres_p"><i class="fa fa-diagnoses"></i>DISTRIBUTION MASQUES</p>
                        <?php include "../Forms/form_distribution_masques.php"; ?>
                        <p class="align_center"></p>
                        <div  id="p_resultats_distribution">
                                <?php
                                $liste_distribution_masques = $DISTRIBUTIONMASQUES->historique_par_pharmacie($user_ets['CODE_ETS']);
                                $nb_masques_distribues = count($liste_distribution_masques);
                                if($nb_masques_distribues != 0) {
                                    ?>
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
                                        foreach ($liste_distribution_masques as $dst_masq) {
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
                                }else {
                                    echo '<p class="align_center alert alert-info">AUCUN MASQUE N\'A ETE SERVI.</p>';
                                }
                                ?>
                        </div>
                        <script type="application/javascript" src="<?= JS . 'page_distribution_masques.js'; ?>"></script>
						<script>
                            $(function () {
                                $('.dataTable').DataTable();
                            })
                        </script>
                    </div>
                    <?php
                }else{
                    echo '<script>window.location.href="'.URL.'"</script>';
                }
            }
        }
    }

}else{
    session_destroy();
    echo '<script>window.location.href="'.URL.'"</script>';
}

?>
<script>
    $(".datepicker").datepicker({
        maxDate: 0
    }).attr('readonly', 'readonly');
</script>
