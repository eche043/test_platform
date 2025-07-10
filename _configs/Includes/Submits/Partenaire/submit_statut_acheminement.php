<?php
require_once '../../../Classes/UTILISATEURS.php';

if(isset($_SESSION['ECMU_USER_ID']) || !empty($_SESSION['ECMU_USER_ID'])) {
    $UTILISATEURS = new UTILISATEURS();
    $user = $UTILISATEURS->trouver($_SESSION['ECMU_USER_ID'],NULL,NULL);
    if(empty($user['ID_UTILISATEUR'])) {
        session_destroy();
        echo '<script>window.location.href="'.URL.'"</script>';
    }else {
         $id_demande = $_POST["id_demande"];
         $num_secu = $_POST["num_secu"];
         $date_acheminement = date('Y-m-d',strtotime(str_replace('/','-',trim($_POST["date_acheminement"]))));
         $lieu_acheminement = $_POST["lieu_acheminement"];
         $numero_rangement = $_POST["numero_rangement"];


         require_once '../../../Classes/PARTENAIRES.php';

         $PARTENAIRES = new PARTENAIRES();
         $editer_statut = $PARTENAIRES->editer_statut_acheminement("1",$date_acheminement,$lieu_acheminement,$numero_rangement,$user['ID_UTILISATEUR'],$num_secu,$id_demande);

         $reponse = array(
            'status' => $editer_statut['status'],
            'message' => $editer_statut['message']
         );

         $demande = $PARTENAIRES->trouver_reedition_carte($id_demande);

         require_once "../../../Functions/function_envoi_sms.php";
         $message = "BONJOUR, VOTRE CARTE A ETE PRODUITE, LIEU DE RETRAIT : {$demande["LIEU_ACHEMINEMENT"]}  VOTRE NUMERO DE RANGEMENT EST : {$demande["NUMERO_RANGEMENT"]} ";
         //echo "test";
         $envoi = envoi_sms('I',$demande['NUM_TELEPHONE'],$message);

         if($envoi->success == true){
            $json = $reponse;
         }else {
             $json = $envoi;
         }
         echo json_encode($json);
    }
}else{
    session_destroy();
    echo '<script>window.location.href="'.URL.'"</script>';
}
?>