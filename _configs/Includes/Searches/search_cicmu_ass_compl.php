<?php
if(isset($_POST['type_facture']) && !empty($_POST['code_ets'])  && !empty($_POST['code_ogd']) && !empty($_POST['statut1']) && !empty($_POST['statut2'])){

    $type_facture = trim($_POST['type_facture']);
    $code_ogd = trim($_POST['code_ogd']);
    $code_ets = trim($_POST['code_ets']);
    require_once '../../Classes/UTILISATEURS.php';
    require_once '../../Classes/CICMU.php';
    $CICMU = new CICMU();


    $factures = $CICMU->lister_ass_compl_factures($type_facture,$code_ets,$code_ogd,$_POST['statut1'],$_POST['statut2']);
    if(count($factures)>0){
        foreach($factures as $facture){
            $json[]= array(
                'code'=>$facture['CODE'],
                'libelle'=>$facture['LIBELLE']
            );
        }
    }else{
        $json= array();
    }
}
else{
    $json= array();
}
echo json_encode($json);
?>