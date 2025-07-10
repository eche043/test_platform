<?php
/**
 * Created by PhpStorm.
 * User: mohamed.kone
 * Date: 05/02/2020
 * Time: 18:20
 */

if(isset($_POST['type_facture']) && !empty($_POST['code_ets']) && !empty($_POST['statut1']) && !empty($_POST['statut2'])){

    $type_facture = trim($_POST['type_facture']);
    $code_ets = trim($_POST['code_ets']);
    require_once '../../../Classes/UTILISATEURS.php';
    require_once '../../../Classes/BORDEREAUX.php';
    $BORDEREAUX = new BORDEREAUX();


    $factures = $BORDEREAUX->lister_ogd_factures($type_facture,$code_ets,$_POST['statut1'],$_POST['statut2']);
    foreach($factures as $facture){
        $json[]= array(
            'code'=>$facture['CODE'],
            'libelle'=>$facture['LIBELLE']
        );
    }
    echo json_encode($json);

}
?>


