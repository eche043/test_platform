<?php
require_once '../../../Classes/UTILISATEURS.php';
require_once '../../../Classes/TICKETS.php';
require_once '../../../Classes/ASSURES.php';
if(isset($_SESSION['ECMU_USER_ID'])) {
    $UTILISATEURS = new UTILISATEURS();
    $TICKETS = new TICKETS();
    $user = $UTILISATEURS->trouver($_SESSION['ECMU_USER_ID'],NULL,NULL);
    if(empty($user['ID_UTILISATEUR'])) {
        $json = array(
            'status' => 'failed',
            'message' => 'AUCUNE SESSION DISPONIBLE POUR CET UTILISATEUR.!!! CONTACTEZ VOTRE userISTRATEUR1'
        );
    }
    else
    {
        if(isset($_POST['ticket_id']) && isset($_POST['description']))
        {
            $ticket_id = $_POST['ticket_id'];
            $description = $_POST['description'];
            $type_user = $_POST['type_user'];
            $centre_coordination = $_POST['cc'];
            $add = $TICKETS->ajouter_ticket_action($ticket_id, $description,$user['ID_UTILISATEUR'],NULL,$type_user);
            $json = $add;
            if($json==true){
                $tick = $TICKETS->moteur_recherche($ticket_id, '', '', '', '', false,$centre_coordination);

                if($tick[0]['TELEPHONE']){
                    $message = strtoupper("Nouveau message en rapport avec le ticket N {$ticket_id}");
                    $TICKETS->envoyer_sms($tick[0]['TELEPHONE'],$message);
                }
            }
        }
        else
        {
            $json = array(
                'status' => 'failed',
                'message' => 'LES PARAMETRES SAISIS SONT INCORRECTS. VEUILLEZ REESSAYER SVP.'
            );
        }

    }
}
else{
    $json = array(
        'status' => 'failed',
        'message' => 'LES PARAMETRES SAISIS SONT INCORRECTS. VEUILLEZ REESSAYER SVP.'
    );
}
echo json_encode($json,NULL);
?>
