<?php

class PARTENAIRES extends BDD
{
    public function trouver($code_partenaire) {
        $a = $this->bdd->prepare('SELECT * FROM PARTENAIRES_ASSOCIES WHERE CODE_PARTENAIRE = ?');
        $a->execute(array($code_partenaire));
        $json = $a->fetch();
        return $json;
    }

    /** GESTION DES DUPLICATAS */
    public function lister_demandes_en_attente_traitement($statut){
        $a = $this->bdd->prepare("SELECT * FROM ECMU_REEDITION_CARTES WHERE STATUT_VALIDATION = ? AND STATUT_TRANSMISSION IS NULL AND STATUT_PRODUCTION IS NULL AND STATUT_ACHEMINEMENT IS NULL ORDER BY DATE_REG DESC");
        $a->execute(array($statut));
        $json = $a->fetchAll();
        return $json;
    }

    public function lister_motifs(){
        $a = $this->bdd->prepare("SELECT * from ECMU_REEDITION_CARTES_MOTIFS");
        $a->execute(array());
        $json = $a->fetchAll();
        return $json;
    }

    public function trouver_motif($code_motif){
        $a = $this->bdd->prepare("SELECT * from ECMU_REEDITION_CARTES_MOTIFS WHERE MOTIF_CODE = ?");
        $a->execute(array($code_motif));
        $json = $a->fetch();
        return $json;
    }

    public function trouver_reedition_carte($id_demande){
        $a = $this->bdd->prepare("SELECT * FROM ECMU_REEDITION_CARTES WHERE ID_DEMANDE = ? ");
        $a->execute(array($id_demande));
        $json = $a->fetch();
        return $json;
    }

    public function moteur_rechercher_duplicata($id_demande, $num_secu, $date_debut, $date_fin, $statut, $motif ){
        if(empty($id_demande)){ $cond_id = "(erc.ID_DEMANDE LIKE ? OR erc.ID_DEMANDE IS NULL)";}else{ $cond_id = "erc.ID_DEMANDE LIKE ?";}
        if(empty($num_secu)){ $cond_secu = "(erc.NUM_SECU LIKE ? OR erc.NUM_SECU IS NULL)";}else{ $cond_secu = "erc.NUM_SECU LIKE ?";}
        if(empty($motif)){ $cond_motif = "(erc.MOTIF_DEMANDE LIKE ? OR erc.MOTIF_DEMANDE IS NULL)";}else{ $cond_motif = "erc.MOTIF_DEMANDE LIKE ?";}

        if(empty($statut)){
            $a = $this->bdd->prepare("
                        SELECT erc.ID_DEMANDE, erc.NUM_SECU, erc.NOM, erc.PRENOMS, erc.PRENOMS, erc.DATE_NAISSANCE, erc.DATE_REG AS DATE_DEMANDE, erc.MOTIF_DEMANDE, erm.MOTIF_LIBELLE, erc.NUM_TRANSACTION_PAIEMENT FROM ECMU_REEDITION_CARTES erc JOIN ECMU_REEDITION_CARTES_MOTIFS erm ON erc.MOTIF_DEMANDE = erm.MOTIF_CODE
                        WHERE ".$cond_id." AND ".$cond_secu." AND ".$cond_motif." AND erc.STATUT_VALIDATION = ? AND (erc.DATE_REG BETWEEN ? AND ?)
                        ORDER BY erc.DATE_REG DESC
            ");
            $a->execute(array('%'.$id_demande.'%','%'.$num_secu.'%','%'.$motif.'%',1,$date_debut,$date_fin));
        }else{
            if($statut=='CAT'){
                $cond_statut = "erc.STATUT_VALIDATION = ? AND erc.STATUT_TRANSMISSION IS NULL AND erc.STATUT_PRODUCTION IS NULL AND erc.STATUT_ACHEMINEMENT IS NULL";
            }
            elseif($statut=='CAP'){
                $cond_statut = "erc.STATUT_TRANSMISSION = ? AND erc.STATUT_PRODUCTION IS NULL AND erc.STATUT_ACHEMINEMENT IS NULL";
            }
            elseif($statut=='CAA'){
                $cond_statut = "erc.STATUT_PRODUCTION = ? AND erc.STATUT_ACHEMINEMENT IS NULL";
            }
            elseif($statut=='CAR'){
                $cond_statut = "erc.STATUT_ACHEMINEMENT = ?";
            }
            $a = $this->bdd->prepare("
                            SELECT erc.ID_DEMANDE, erc.NUM_SECU, erc.NOM, erc.PRENOMS, erc.DATE_NAISSANCE, erc.DATE_REG AS DATE_DEMANDE, erc.MOTIF_DEMANDE, erm.MOTIF_LIBELLE, erc.NUM_TRANSACTION_PAIEMENT FROM ECMU_REEDITION_CARTES erc JOIN ECMU_REEDITION_CARTES_MOTIFS erm ON erc.MOTIF_DEMANDE = erm.MOTIF_CODE
                            WHERE ".$cond_id." AND ".$cond_secu." AND ".$cond_motif."  AND ".$cond_statut." AND (erc.DATE_REG BETWEEN ? AND ?)
                            ORDER BY erc.DATE_REG DESC
                ");
            $a->execute(array('%'.$id_demande.'%','%'.$num_secu.'%','%'.$motif.'%',1,$date_debut,$date_fin));
        }

        $json = $a->fetchAll();
        return $json;
    }

    public function editer_statut_transmission($user_edit,$num_secu,$id_transmission){
        $a = $this->bdd->prepare("UPDATE ECMU_REEDITION_CARTES SET STATUT_TRANSMISSION = ?, DATE_TRANSMISSION = SYSDATE, USER_EDIT = ? WHERE NUM_SECU = ? AND ID_DEMANDE = ?");
        $a->execute(array(1,$user_edit,$num_secu,$id_transmission));

        $b = $this->bdd->prepare('COMMIT;');
        $b->execute(array());

        $json = array(
            'status' => "success",
            'message' => "Informations de transmission mis à jour avec succès"
        );
        return $json;
    }

    public function editer_statut_acheminement($statut,$date_acheminement,$lieu_acheminement,$numero_rangement,$user_edit,$num_secu,$id_transmission){
        $a = $this->bdd->prepare("UPDATE ECMU_REEDITION_CARTES SET STATUT_ACHEMINEMENT = ?, DATE_ACHEMINEMENT = ?,LIEU_ACHEMINEMENT = ?,NUMERO_RANGEMENT = ?, USER_EDIT = ? WHERE NUM_SECU = ? AND ID_DEMANDE = ?");
        $a->execute(array($statut,$date_acheminement,$lieu_acheminement,$numero_rangement,$user_edit,$num_secu,$id_transmission));

        $b = $this->bdd->prepare('COMMIT;');
        $b->execute(array());

        $json = array(
            'status' => "success",
            'message' => "Informations d'acheminement mis à jour avec succès"
        );
        return $json;
    }

    public function editer_statut_retrait_carte($date,$statut,$lieu_retrait,$user_edit,$num_secu,$id_transmission){
        $a = $this->bdd->prepare("UPDATE ECMU_REEDITION_CARTES SET DATE_RETRAIT = ?,STATUT_RETRAIT = ?,LIEU_RETRAIT = ?,STATUT_VALIDATION = ?,USER_EDIT = ? WHERE NUM_SECU = ? AND ID_DEMANDE = ? ");
        $a->execute(array($date,$statut,$lieu_retrait,'3',$user_edit,$num_secu,$id_transmission));
        $json = array(
            'status' => "success",
            'message' => "Informations de retrait mis à jour avec succès"
        );
        return $json;
    }

    public function editer_statut_production($statut,$date_prod,$user_edit,$num_secu,$id_transmission){
        $a = $this->bdd->prepare("UPDATE ECMU_REEDITION_CARTES SET STATUT_PRODUCTION = ?, DATE_PRODUCTION = ?, USER_EDIT = ?, DATE_REG = SYSDATE WHERE NUM_SECU = ? AND ID_DEMANDE = ?");
        $a->execute(array($statut,$date_prod,$user_edit,$num_secu,$id_transmission));

        $b = $this->bdd->prepare('COMMIT;');
        $b->execute(array());

        $json = array(
            'status' => "success",
            'message' => "Informations de production mis à jour avec succès"
        );
        return $json;
    }

    public function editer_num_guid($id_demande, $num_guid, $user){
        $a = $this->bdd->prepare("UPDATE ECMU_REEDITION_CARTES SET GUID_ENROLEMENT = ?, USER_EDIT = ? WHERE ID_DEMANDE = ?");
        $a->execute(array($num_guid, $user, $id_demande));
        $json = array(
            'status' => "success",
            'message' => "Numéro Guid enrégistré avec succès."
        );
        return $json;
    } 

    /** FIN GESTION DES DUPLICATAS */

}