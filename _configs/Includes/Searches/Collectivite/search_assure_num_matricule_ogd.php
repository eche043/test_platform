<?php
if(isset($_POST['num_matricule_ogd']) && !empty($_POST['num_matricule_ogd'])){
    $num_matricule_ogd = trim($_POST['num_matricule_ogd']);

    require_once '../../../Classes/UTILISATEURS.php';
    require_once '../../../Classes/ASSURES.php';

    $ASSURES = new ASSURES();

    $assure = $ASSURES->trouver_par_num_matricule_ogd($num_matricule_ogd);

    if(empty($assure['BENEFICIAIRE_NUM_OGD'])){
        $json = array(
            'status' => false,
            'message' => 'CE NUMERO CNPS EST INCONNU DANS NOTRE BASE.'
        );
    }else{
        if(empty($assure['BENEFICIAIRE_DATE_NAISSANCE'])){
            $date_naissance = null;
        }else{
            $date_naissance = date('d/m/Y',strtotime($assure['BENEFICIAIRE_DATE_NAISSANCE']));
        }
        $json = array(
            'status' => true,
            'num_matricule_ogd' => $assure['BENEFICIAIRE_NUM_OGD'],
            'num_secu' => $assure['BENEFICIAIRE_NUM_SECU'],
            'nom' => $assure['BENEFICIAIRE_NOM'],
            'prenom' => $assure['BENEFICIAIRE_PRENOMS'],
            'date_naissance' => $date_naissance,
            'sexe' => $assure['BENEFICIAIRE_SEXE'],
            'civilite' => $assure['BENEFICIAIRE_CIVILITE'],
            'lieu_naissance' => trim($assure['BENEFICIAIRE_LIEU_NAISSANCE']),
            'lieu_residence' => trim($assure['BENEFICIAIRE_LIEU_RESIDENCE'])
        );
    }
    echo json_encode($json);
}