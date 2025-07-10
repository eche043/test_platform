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
        $num_guid = $_POST["guid"];


        require_once '../../../Classes/PARTENAIRES.php';
        $PARTENAIRES = new PARTENAIRES();
        $editer_statut = $PARTENAIRES->editer_num_guid($id_demande, $num_guid, $user['ID_UTILISATEUR']);

        $json = array(
            'status' => $editer_statut['status'],
            'message' => $editer_statut['message']
        );

        echo json_encode($json);
    }
}else{
    session_destroy();
    echo '<script>window.location.href="'.URL.'"</script>';
}
?>