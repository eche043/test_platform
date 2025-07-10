<?php

$code_motif = $_POST['code_motif'];
require_once '../../Classes/DUPLICATA.php';
$DUPLICATA = new DUPLICATA();

$motif_demande = $DUPLICATA->trouver_motif($code_motif);

$statut_reenrolement = $motif_demande["STATUT_REENROLEMENT"];
$statut_paiement = $motif_demande["STATUT_PAIEMENT"];
$statut_carte = $motif_demande["STATUT_CARTE_IDENTITE"];
$statut_passport = $motif_demande["STATUT_PASSPORT"];
$statut_declaration_perte = $motif_demande["STATUT_DECLARATION_PERTE"];
$statut_carte_abimee = $motif_demande["STATUT_CARTE_ABIMEE"];

$json = array(
    "status" => "success",
    "statut_reenrolement" => $statut_reenrolement,
    "statut_paiement" => $statut_paiement,
    "statut_carte" => $statut_carte,
    "statut_passport" => $statut_passport,
    "statut_declaration_perte" => $statut_declaration_perte,
    "statut_carte_abimee" => $statut_carte_abimee,
);

echo json_encode($json);

?>