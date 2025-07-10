<?php
require '../../Classes/CALENDRIER.php';
$CALENDRIER = new CALENDRIER();

$annee = $_POST['annee'];
$annee1 = date('Y',strtotime('+1 year',time()));
if($annee=='2019'){
    for($m = 7; $m<=12; $m++){
        $mois = $CALENDRIER->trouver_mois(str_pad($m,2,'0',STR_PAD_LEFT));

        $json[$m][] = $mois;
    }
}elseif($annee >= $annee1){
    for($m = 1; $m<=date('m',time()); $m++){
        $mois = $CALENDRIER->trouver_mois(str_pad($m,2,'0',STR_PAD_LEFT));

        $json[$m][] = $mois;
    }
}else{
    for($m = 1; $m<=12; $m++){
        $mois = $CALENDRIER->trouver_mois(str_pad($m,2,'0',STR_PAD_LEFT));

        $json[$m][] = $mois;
    }
}

echo json_encode($json);