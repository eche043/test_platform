<?php
$num_facture = trim($_POST['num_facture']);
$montant = trim($_POST['montant']);
$montant_base = trim($_POST['montant_base']);
require_once '../../../Classes/UTILISATEURS.php';
require_once '../../../Classes/FACTURES.php';
$FACTURES = new FACTURES();
$facture = $FACTURES->trouver($num_facture);
if(!empty($facture['FEUILLE']) && !empty($montant)) {
    $montant_diff = $montant-$montant_base;
    if($montant_diff>0){
        $part_cmu = intval(round($montant_base * 0.7));
    }
    else{
        $part_cmu = intval(round($montant * 0.7));
    }

    if($facture['CODE_OGD_AFFILIATION'] == '03016000') {
        $part_ac = $montant - $part_cmu;
    }else {
        $part_ac = 0;
    }
    $part_assure = ($montant - ($part_cmu +$part_ac));
    $json = array(
        'status' => true,
        'montant' => number_format($montant,'0','',' '),
        'part_cmu' => number_format($part_cmu,'0','',' '),
        'part_ac' => number_format($part_ac,'0','',' '),
        'part_assure' => number_format($part_assure,'0','',' ')
    );
}else {
    $json = array(
        'status' => true,
        'montant' => 0,
        'part_cmu' => 0,
        'part_ac' => 0,
        'part_assure' => 0
    );
}
echo json_encode($json);