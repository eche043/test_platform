<?php
$statut = $_POST['statut'];
$ets = trim($_POST['ets']);
$type = $_POST['type'];
$categorie = $_POST['categorie'];
$ticket = $_POST['ticket'];
$verrou = $_POST['verrou'];
$cc = $_POST['cc'];

if (isset($verrou)) {
    require_once '../../../Classes/UTILISATEURS.php';
    require_once '../../../Classes/TICKETS.php';
    require_once '../../../Classes/ASSURES.php';
    $TICKETS = new TICKETS();
    $ASSURES = new ASSURES();
    if (empty($statut) && empty($ets) && empty($type) && empty($categorie) && empty($ticket)) {
        $tickets_list = $TICKETS->moteur_recherche($ticket, $ets, $categorie, $type, 'N;C;R',true,$cc);
    }
    else
    {
        $tickets_list = $TICKETS->moteur_recherche($ticket, $ets, $categorie, $type, $statut,false,$cc);
    }
    $nb_tickets = count($tickets_list);
    if ($nb_tickets == 0) {
        echo '<p align="center" class="alert-primary"><b>AUCUN RESULTAT TROUVE</b></p>';
    } else {
        ?>
        <div class="row">
            <div class="col-sm-12">
                <table class="table table-bordered rounded-lg table-sm table-hover" id="tickets_table">
                    <thead class="bg-info">
                    <tr>
                        <th style="width: 5px">N°</th>
                        <th>DATE CREATION</th>
                        <th>N° SECU</th>
                        <th>ASSURE</th>
                        <th>TYPE</th>
                        <th>CATEGORIE</th>
                        <th>ETABLISSEMENT</th>
                        <th>CENTRE DE COORDINATION</th>
                        <th style="width: 110px">STATUT</th>
                        <th style="width: 5px"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $ligne = 1;
                    foreach ($tickets_list as $tick) {
                        if(trim($tick['NUM_SECU'])){
                            $nom_prenoms_assure=$ASSURES->trouver($tick['NUM_SECU']);
                            if($nom_prenoms_assure){
                                $nom_prenoms_assure = "{$nom_prenoms_assure['NOM']} {$nom_prenoms_assure['PRENOM']}";
                            }
                            else{
                                $nom_prenoms_assure = '';
                            }

                        }
                        else{
                            $nom_prenoms_assure="";
                        }
                        $date = new DateTime(date($tick['DATE_TICKET']));

                        ?>
                        <tr>
                            <td><b><?= $tick['ID_TICKET']; ?></b></td>
                            <td><b><?= $date->format('d-m-Y H:i:s'); ?></b></td>
                            <td><b><?= $tick['NUM_SECU']; ?></b></td>
                            <td><b><?= $nom_prenoms_assure; ?></b></td>
                            <td><b><?= $tick['LIBELLE_TYPE']; ?></b></td>
                            <td><b><?= $tick['LIBELLE_CATEGORIE']; ?></b></td>
                            <td><?= $tick['NOM_ETS']; ?></td>
                            <td><?= $tick['LIBELLE_CENTRE']; ?></td>
                            <td><?= $tick['LIBELLE_STATUT']; ?></td>
                            <td>
                                <a href="<?= URL . 'centre-coordination/ticket.php?id=' . $tick['ID_TICKET']; ?>"
                                   class="badge badge-info"><i class="fa fa-eye"></i></a></td>
                        </tr>
                        <?php
                        $ligne++;
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
    }

}
else{
    echo "VOUS N'ÊTES PAS AUTORISE A ACCEDER A LA PAGE. OUS! MERCI";
}
?>
<script>
    $('#tickets_table').DataTable({
        "paging": false,
        "searching": false
    });
</script>
