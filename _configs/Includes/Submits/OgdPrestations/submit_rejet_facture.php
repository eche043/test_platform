<?php
require_once '../../../Classes/UTILISATEURS.php';
if(isset($_SESSION['ECMU_USER_ID']) || !empty($_SESSION['ECMU_USER_ID'])) {
    $UTILISATEURS = new UTILISATEURS();
    $user = $UTILISATEURS->trouver($_SESSION['ECMU_USER_ID'], NULL, NULL);
    if (!empty($user['ID_UTILISATEUR'])) {
        require_once '../../../Classes/FACTURES.php';
        $FACTURES = new FACTURES();

        $numero_facture = trim($_POST['numero_facture']);

        $facture = $FACTURES->trouver_facture(null,$numero_facture);

        if(!empty($facture['FEUILLE'])) {
            $code_actes = explode(',',str_replace('[','',str_replace(']','',str_replace('"','',json_encode($_POST['code_acte'])))));
            $motifs_rejets = explode(',',str_replace('[','',str_replace(']','',str_replace('"','',json_encode($_POST['motif_rejet'])))));

            $nb_actes = count($code_actes);
            $ligne = 0;
            for ($i = 0; $i < $nb_actes; $i++) {
                $maj_motif_rejet_acte = $FACTURES->motif_rejet_liquidation_facture($numero_facture,$motifs_rejets[$i],$code_actes[$i], $user['ID_UTILISATEUR']);
                if($maj_motif_rejet_acte['status'] == true) {
                    $ligne++;
                }
            }
            if($ligne == $nb_actes) {
                $rejet_facture = $FACTURES->liquidation_facture('R',$numero_facture,$user['ID_UTILISATEUR']);
                if($rejet_facture['status'] == true){
                    $json = array(
                        'status' => true,
                        'message' => $rejet_facture['message']
                    );
                }else{
                    $json = array(
                        'status' => false,
                        'message' => "LE REJET DE LA FACTURE A ECHOUE."
                    );
                }
            }else{
                $json = array(
                    'status' => false,
                    'message' => "LE REJET DE LA FACTURE A ECHOUE."
                );
            }
        }else{
            $json = array(
                'status' => false,
                'message' => "CETTE FEUILLE EST ERRONNEE."
            );
        }
    }else{
        $json = array(
            'status' => false,
            'message' => 'VOTRE IDENTIFIANT EST INCORRECT. VOUS NE POUVEZ PAS EFFECTUER CETTE ACTION. VEUILLEZ CONTACTER VOTRE ADMINISTRATEUR SYSTEME.'
        );
    }
}else{
    $json = array(
        'status' => false,
        'message' => 'VOTRE IDENTIFIANT EST INCORRECT. VOUS NE POUVEZ PAS EFFECTUER CETTE ACTION. VEUILLEZ CONTACTER VOTRE ADMINISTRATEUR SYSTEME.'
    );
}
echo json_encode($json);



