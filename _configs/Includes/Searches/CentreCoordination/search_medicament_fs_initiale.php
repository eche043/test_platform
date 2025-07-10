<?php
$num_fs_initiale = trim($_POST['num_fs_initiale']);
require_once '../../../Classes/UTILISATEURS.php';
if(isset($_SESSION['ECMU_USER_ID'])) {
    $session_user = $_SESSION['ECMU_USER_ID'];
    if (!empty($session_user)) {
        $UTILISATEURS = new UTILISATEURS();
        $utilisateur_existe = $UTILISATEURS->trouver($session_user, null, null);
        if (!empty($utilisateur_existe['ID_UTILISATEUR'])) {
            $login = $utilisateur_existe['LOGIN'];
            if (!empty($num_fs_initiale)) {
                include "../../../../_configs/Classes/FACTURES.php";
                $FACTURES = new FACTURES();
                $facture = $FACTURES->trouver($num_fs_initiale);
                if (!empty($facture['FEUILLE'])) {
                    ?>
                    <table class="table table-bordered table-hover table-sm">
                        <thead class="bg-danger">
                        <tr>
                            <th width="100">N° SECU</th>
                            <th width="100">N° FACTURE</th>
                            <th>NOM & PRENOM(S)</th>
                            <th>ETABLISSEMENT</th>
                            <th width="5"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><b><?= $facture['NUM_SECU']; ?></b></td>
                            <td align="right"><b><?= $facture['FEUILLE']; ?></b></td>
                            <td><?= $facture['NOM'] . ' ' . $facture['PRENOM']; ?></td>
                            <td><?= $facture['NOM_ETS']; ?></td>
                            <td>
                                <button type="button" id="btn_nouveau_medicament"
                                        class="badge badge-success"><i class="fa fa-edit"></i></button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <p align="center" id="resultat_p_med"></p>
                    <?php
                } else {
                    echo '<p class="alert alert-info align_center"><b>LE N° DE LA FACTURE SAISI EST INCORRECT.</b></p>';
                }
            }
        }else{
            echo '<p class="alert alert-info align_center"><b>VOTRE IDENTIFIANT EST INCORRECT. VOUS NE POUVEZ PAS EFFECTUER CETTE ACTION. VEUILLEZ CONTACTER VOTRE ADMINISTRATEUR SYSTEME.</b></p>';
        }
    }else{
        echo '<p class="alert alert-info align_center"><b>VOTRE IDENTIFIANT EST INCORRECT. VOUS NE POUVEZ PAS EFFECTUER CETTE ACTION. VEUILLEZ CONTACTER VOTRE ADMINISTRATEUR SYSTEME.</b></p>';
    }
}else{
    echo '<p class="alert alert-info align_center"><b>VOTRE IDENTIFIANT EST INCORRECT. VOUS NE POUVEZ PAS EFFECTUER CETTE ACTION. VEUILLEZ CONTACTER VOTRE ADMINISTRATEUR SYSTEME.</b></p>';
}
?>
<script>
    $("#btn_nouveau_medicament").click(function () {
        $("#resultat_p_med").hide();
        $("#btn_nouveau_medicament").prop('disabled', true);
        $("#btn_nouveau_medicament").html('<i class="fa fa-braille"></i>');
        $("#btn_nouveau_medicament").removeClass('btn-success');
        $("#btn_nouveau_medicament").addClass('btn-warning');
        $.ajax({
            url: '../_configs/Includes/Searches/CentreCoordination/search_consultation_droits.php',
            type: 'post',
            data: {
                'type_envoi': 'PHCIE',
                'num_facture': '<?= $num_fs_initiale;?>',
                'login': '<?= $login;?>'
            },
            dataType: 'json',
            success: function (data) {
                if (data['status'] == false) {
                    $("#resultat_p_med").show();
                    $("#btn_nouveau_medicament").prop('disabled', false);
                    $("#btn_nouveau_medicament").html('<i class="fa fa-edit"></i>');
                    $("#btn_nouveau_medicament").removeClass('btn-warning');
                    $("#btn_nouveau_medicament").addClass('btn-success');
                    $("#resultat_p_med").addClass('alert alert-danger');
                    $("#resultat_p_med").html(data['message']);
                } else {
                    setTimeout(function () {
                        window.location.href = "facture-selection-type.php?num=" + data['num_transaction'];
                    }, 1000);
                }
            }
        });
        return false;
    });
</script>
