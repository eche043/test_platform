<?php
if(isset($_POST['num_secu']) && !empty($_POST['num_secu'])){
    $num_secu = trim($_POST['num_secu']);

    require_once '../../Classes/UTILISATEURS.php';
    require_once '../../Classes/ASSURES.php';

    $ASSURES = new ASSURES();

    $assure = $ASSURES->trouver($num_secu);

    if(empty($assure['NUM_SECU'])){
        $json = array(
            'status' => false,
            'message' => 'LE NUMERO SECU EST INCORRECT.'
        );
    }else{
        $json = array(
            'status' => true,
            'num_secu' => $assure['NUM_SECU'],
            'nom' => $assure['NOM'],
            'prenom' => $assure['PRENOM'],
            'date_naissance' => date('d/m/Y',strtotime($assure['DATE_NAISSANCE'])),
            'csp' => $assure['CATEGORIE_PROFESSIONNELLE'],
			'payeur_num_secu' => $assure['PAYEUR_NUM_SECU']
        );
    }
    echo json_encode($json);
}