<?php
if(!empty(count($_POST))) {
    $type_facture = trim($_POST['type_facture']);
    $num_facture = trim($_POST['num_facture']);
    $code_acte = trim($_POST['code_acte']);
    $libelle_acte = null;
    $date_soins = strtoupper(date('Y-m-d', strtotime(str_replace('/', '-', $_POST['date_soins']))));
}else{
    $type_facture = trim($_GET['type_facture']);
    $num_facture = trim($_GET['num_facture']);
    $code_acte = null;
    $libelle_acte = trim($_GET['libelle_acte']);
    $date_soins = strtoupper(date('Y-m-d', strtotime(str_replace('/', '-', $_GET['date_soins']))));
}
if(!empty($type_facture) && !empty($num_facture) && !empty($date_soins)) {
    require_once '../../../Classes/UTILISATEURS.php';
    require_once '../../../Classes/FACTURES.php';
    $FACTURES = new FACTURES();
    $facture = $FACTURES->trouver($num_facture);
    if(!empty($facture['FEUILLE'])) {
        $json = $FACTURES->trouver_acte($type_facture,$facture['ETABLISSEMENT'],$code_acte,$libelle_acte,$date_soins);
    }else {
        $json = array(
            'status' => false,
            'message' => 'N° FACTURE INCORRECT.'
        );
    }
}else {
    $json = array(
        'status' => false,
        'message' => 'VEUILLEZ RENSEIGNER TOUS LES CHAMPS SVP.'
    );
}
echo json_encode($json);
flush();