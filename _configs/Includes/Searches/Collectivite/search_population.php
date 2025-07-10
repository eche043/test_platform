<?php
if(isset($_POST['id'])){
    $id_population = trim($_POST['id']);

    if(!empty($id_population)) {
            require_once '../../../Classes/UTILISATEURS.php';
            require_once '../../../Classes/COLLECTIVITES.php';
            $COLLECTIVITES = NEW COLLECTIVITES();
            $trouver_population = $COLLECTIVITES->trouver_population($id_population, null,null,null,null,null);
            if(empty($trouver_population['ID'])) {
                $json = array(
                    'status' => false,
                    'message' => 'CET IDENTIFIANT NE CORRESPOND A AUCUN INDIVIDU ENREGISTRE.'
                );
            }else {
                $status = array('status'=>true);
                if(empty($trouver_population['PAYEUR_DATE_NAISSSANCE'])){
                    $date_naiss_payeur = array(
                        'DATE_NAISSSANCE_PAYEUR'=> null
                    );
                }else{
                    $date_naiss_payeur = array(
                        'DATE_NAISSSANCE_PAYEUR'=> date('d/m/Y',strtotime($trouver_population['PAYEUR_DATE_NAISSSANCE']))
                    );
                }
                if(empty($trouver_population['BENEFICIAIRE_DATE_NAISSANCE'])){
                    $date_naiss_benef = array(
                        'DATE_NAISSANCE_BENEFICIAIRE'=> null
                    );null;
                }else {
                    $date_naiss_benef = array(
                        'DATE_NAISSANCE_BENEFICIAIRE' => date('d/m/Y', strtotime($trouver_population['BENEFICIAIRE_DATE_NAISSANCE']))
                    );
                }
				
				if($trouver_population['TYPE']=='RET' || $trouver_population['TYPE'] =='SAL' || $trouver_population['TYPE'] == 'PAY'){
                    $type = array('TYPE_POP'=>'T');
                }elseif($trouver_population['TYPE']=='CJT' || $trouver_population['TYPE']=='C'){
                    $type = array('TYPE_POP'=>'C');
                }elseif($trouver_population['TYPE']=='ENF' || $trouver_population['TYPE']=='E'){
                    $type = array('TYPE_POP'=>'E');
                }else{
                    $type = array('TYPE_POP'=>'A');
                }
                $json = $trouver_population+$status+$date_naiss_payeur+$date_naiss_benef+$type;
            }
    }else {
        $json = array(
            'status' => false,
            'message' => 'L\' IDENTIFIANT N\'EST PAS DEFINI.'
        );
    }
}else {
    $json = array(
        'status' => false,
        'message' => 'L\' IDENTIFIANT N\'EST PAS DEFINI.'
    );
}
echo json_encode($json);