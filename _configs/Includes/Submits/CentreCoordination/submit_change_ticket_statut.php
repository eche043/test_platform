<?php
require_once '../../../Classes/UTILISATEURS.php';
require_once '../../../Classes/TICKETS.php';

if(isset($_SESSION['ECMU_USER_ID'])) {
    $UTILISATEURS = new UTILISATEURS();
    $TICKETS = new TICKETS();

    $user = $UTILISATEURS->trouver($_SESSION['ECMU_USER_ID'],NULL,NULL);
    if(empty($user['ID_UTILISATEUR'])) {
        $json = array(
            'status' => 'failed',
            'message' => 'AUCUNE SESSION DISPONIBLE POUR CET UTILISATEUR.!!! CONTACTEZ VOTRE ADMINISTRATEUR'
        );
    }
    else
    {
        if(isset($_POST['ticket_id']) && isset($_POST['code_statut']))
        {
            $ticket_id = $_POST['ticket_id'];
            $code_statut = $_POST['code_statut'];
            $centre_coordination = $_POST['cc'];
            $update = $TICKETS->editer_statut($ticket_id, $code_statut,NULL,$user['ID_UTILISATEUR'], NULL);
            $json = $update;
            if($json['status']==true){
                $tick = $TICKETS->moteur_recherche($ticket_id, '', '', '', '', false,$centre_coordination,$centre_coordination);
                if($tick[0]['TELEPHONE'])
                {
                    $message = strtoupper("Le statut du ticket {$ticket_id} a changé");
                    $TICKETS->envoyer_sms($tick[0]['TELEPHONE'],$message);
                }
                if($code_statut=='R' || $code_statut=='r'){
                    if($tick[0]['TELEPHONE_ASSURE']){
                        $message = strtoupper("Votre probleme a ete traite, allez vers l'agent d'accueil pour plus d'informations");
                        $TICKETS->envoyer_sms($tick[0]['TELEPHONE_ASSURE'],$message);
                    }
                }
            }
        }
        else{
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
