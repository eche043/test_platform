<?php
/**
 * Created by PhpStorm.
 * User: mohamed.kone
 * Date: 05/02/2020
 * Time: 20:09
 */


$type_facture = $_POST['type_facture'];
$code_ets = $_POST['code_ets'];
$code_ogd = $_POST['ogd_input'];

if(empty($_POST['date_debut'])) {
    $date_debut = NULL;
}else {
     $date_debut = strtoupper(date('Y-m-d',strtotime(str_replace('/','-',trim($_POST['date_debut'])))));
}

if(empty($_POST['date_fin'])) {
    $date_fin = NULL;
}else {
      $date_fin = strtoupper(date('Y-m-d',strtotime(str_replace('/','-',trim($_POST['date_fin'])))));
}

require_once '../../../Classes/UTILISATEURS.php';
require_once '../../../Classes/BORDEREAUX.php';

$BORDEREAUX = new BORDEREAUX();
$factures = $BORDEREAUX->lister_factures_bordereaux($code_ets,$type_facture,$code_ogd,$date_debut,$date_fin);

foreach ($factures AS $facture){
    echo "<option value='".$facture['NUM_FACTURE']."'>".$facture['NUM_FACTURE']."</option>";
}
