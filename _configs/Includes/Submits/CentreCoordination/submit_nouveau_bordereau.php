<?php
/**
 * Created by PhpStorm.
 * User: mohamed.kone
 * Date: 06/02/2020
 * Time: 08:31
 */
require_once '../../../Classes/UTILISATEURS.php';
if(isset($_SESSION['ECMU_USER_ID']) || !empty($_SESSION['ECMU_USER_ID'])) {
    $UTILISATEURS = new UTILISATEURS();
     $user = $UTILISATEURS->trouver($_SESSION['ECMU_USER_ID'], NULL, NULL);
    if (!empty($user['ID_UTILISATEUR'])) {
        $type_facture = $_POST['type_facture'];
        $numeros_factures = $_POST['numeros_factures'];
        $code_ogd = $_POST['code_ogd'];
        $date_debut_periode = $_POST['date_debut'];
        $date_fin_periode = $_POST['date_fin'];
        $code_ets = $_POST['code_ets'];
        $montant_total = '0';
        $id_utilisateur = $user['ID_UTILISATEUR'];


        require_once '../../../Classes/BORDEREAUX.php';

        $BORDEREAUX = new BORDEREAUX();

        $date_debut = strtoupper(date('Y-m-d',strtotime(str_replace('/','-',trim($date_debut_periode)))));
        $date_fin = strtoupper(date('Y-m-d',strtotime(str_replace('/','-',trim($date_fin_periode)))));


        $nouveau_bordereau = $BORDEREAUX->ajouter_nouveau_bordereau($type_facture, $code_ogd, $date_debut, $date_fin, $code_ets, $montant_total,$id_utilisateur);
//        var_dump($nouveau_bordereau);

        if($nouveau_bordereau['status'] == true){
            foreach ($numeros_factures as $numero_facture) {
                $ajout_numero_bordereau = $BORDEREAUX->mise_a_jour_numero_bordereau_facture($type_facture, $nouveau_bordereau['num_bordereau'], $numero_facture);
            }
            $json = array(
                'status' => true,
                'num_bordereau' => $nouveau_bordereau['num_bordereau']
            );
        }
    }else{
        $json = array(
            'status' => false,
            'num_bordereau' => " CET UTILISATEUR N\'EXISTE PAS"
        );
    }
}else{

}


echo json_encode($json);

