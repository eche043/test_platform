<?php
require_once '../../../Classes/UTILISATEURS.php';

$raison_sociale = trim($_GET['raison_sociale']);
$code_centre = trim($_GET['code_centre']);
if(!empty($raison_sociale)) {
    require_once '../../../Classes/RECRUTEMENT.php';
    $RECRUTEMENT = new RECRUTEMENT();

        $liste_ets = $RECRUTEMENT->trouver_ets_raison_sociale($raison_sociale);
        if(count($liste_ets)==0)
        {
            $json[] = array(
                'status' => false,
                'message' => 'AUCUN ETABLISSEMENT NE CORRESPOND A CETTE RECHERCHE.'
            );
        }
        else
        {
            foreach($liste_ets as $ets) {
                $json[] = array(
                    'status' => true,
                    'message' => count($liste_ets) . ' RESULTATS TROUVES.',
                    'code' => $ets["CODE_ETS"],
                    'value' => $ets['RAISON_SOCIALE'],
                    'label' => $ets['RAISON_SOCIALE']
                );
            }
        }

}else{
    $json[] = array(
        'status' => false,
        'message' => 'LES INFORMATIONS SAISIES SONT INCORRECTS. PRIERE ENTRER DES DONNEES VALIDES.'
    );
}

echo json_encode($json);
flush();
?>


