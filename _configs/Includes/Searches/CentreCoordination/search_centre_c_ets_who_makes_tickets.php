<?php
require_once '../../../Classes/UTILISATEURS.php';
require_once '../../../Classes/TICKETS.php';
$TICKETS = new TICKETS();
if (isset($_POST['ets'])) {
    $ets = strtoupper($_POST['ets']);
    $cc = $_POST['cc'];
    $etablissements = $TICKETS->afficher_ets_ticket($ets,$cc);
    foreach ($etablissements as $etablissement) {
        echo '<div class="etablissement bg-dark text-light" style="padding: 10px;border: white solid 1px" onclick="selectionets(this);">'.$etablissement['RAISON_SOCIALE'].'</div>';
    }
}
else
{
}
?>
