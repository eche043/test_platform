<?php

class CICMU extends BDD
{
    public function inserer_adjudication($id_transaction, $id_transaction_caresheet, $numero_feuille, $code_acte, $montant, $montant_adjuje, $quantite_prescrite, $quantite_servie, $date_debut, $date_fin, $type_acte, $code_ac, $statut){
        $a = $this->bdd->prepare("INSERT INTO CICMU_ADJUDICATION(ID_TRANSACTION, ID_TRANSACTION_CARESHEET, NUMERO_FEUILLE, CODE_ACTE, MONTANT, MONTANT_ADJUJE, QUANTITE_PRESCRITE, QUANTITE_SERVIE, DATE_DEBUT, DATE_FIN, TYPE_ACTE, CODE_AC, STATUT)
        VALUES(:ID_TRANSACTION, :ID_TRANSACTION_CARESHEET, :NUMERO_FEUILLE, :CODE_ACTE, :MONTANT, :MONTANT_ADJUJE, :QUANTITE_PRESCRITE, :QUANTITE_SERVIE, :DATE_DEBUT, :DATE_FIN, :TYPE_ACTE, :CODE_AC, :STATUT)");
        $a->execute(array(
            'ID_TRANSACTION' => $id_transaction,
            'ID_TRANSACTION_CARESHEET' => $id_transaction_caresheet,
            'NUMERO_FEUILLE' => $numero_feuille,
            'CODE_ACTE' => $code_acte,
            'MONTANT' => $montant,
            'MONTANT_ADJUJE' => $montant_adjuje,
            'QUANTITE_PRESCRITE' => $quantite_prescrite,
            'QUANTITE_SERVIE' => $quantite_servie,
            'DATE_DEBUT' => $date_debut,
            'DATE_FIN' => $date_fin,
            'TYPE_ACTE' => $type_acte,
            'CODE_AC' => $type_acte,
            'STATUT' => $statut
        ));
        if ($a->errorCode() === '00000') {
            return array(
                'status' => true
            );
        } else {
            return array(
                'status' => false,
                'message' => $a->errorInfo()[2]
            );
        }
    }

    public function maj_adjudication($id_transaction, $id_transaction_complementaire, $numero_feuille, $code_acte, $montant_adjuje, $montant_complementaire, $statut){
        $a = $this->bdd->prepare("UPDATE CICMU_ADJUDICATION SET ID_TRANSACTION = ?, ID_TRANSACTION_COMPLEMENTAIRE = ?, MONTANT_ADJUJE = ?, MONTANT_COMPLEMENTAIRE = ?, STATUT = ?  WHERE NUMERO_FEUILLE = ? AND CODE_ACTE = ? ");
        $a->execute(array($id_transaction, $id_transaction_complementaire, $montant_adjuje, $montant_complementaire, $statut, $numero_feuille, $code_acte));
        if ($a->errorCode() === '00000') {
            return array(
                'status' => true
            );
        } else {
            return array(
                'status' => false,
                'message' => $a->errorInfo()[2]
            );
        }
    }

    public function trouver_acte_adjuje($code_acte, $numero_feuille){
        $a = $this->bdd->prepare('SELECT * FROM CICMU_ADJUDICATION WHERE NUMERO_FEUILLE = ? AND CODE_ACTE = ? AND STATUT = ?');
        $a->execute(array($numero_feuille, $code_acte, 0));
        $json = $a->fetch();
        return $json;
    }

    public function liste_actes_adjujes($numero_feuille){
        $a = $this->bdd->prepare('SELECT * FROM CICMU_ADJUDICATION WHERE NUMERO_FEUILLE = ? AND STATUT = ?');
        $a->execute(array($numero_feuille, 1));
        $json = $a->fetchAll();
        return $json;
    }

    public function lister_ass_compl_factures($type_facture,$code_ets, $code_ogd, $statut1,$statut2){
        $a = $this->bdd->prepare("SELECT DISTINCT A.NUM_EP_AC AS CODE, A.NUM_EP_AC AS LIBELLE FROM FS A 
WHERE A.TYPE_FEUILLE = ? AND A.ETABLISSEMENT = ? AND (A.STATUT = ? OR A.STATUT = ?) AND A.NUM_OGD = ? AND A.NUM_EP_AC IS NOT NULL AND A.NUM_SECU != A.NUM_EP_AC ");
        $a->execute(array($type_facture, $code_ets, $statut1, $statut2, $code_ogd));
        $json = $a->fetchAll();
        return $json;
    }

    public function lister_bordereaux_facture($code_ets,$num_bordereau){
        $a = $this->bdd->prepare('SELECT A.NUM_SECU, A.FEUILLE, A.NUM_FS_INITIALE, A.TYPE_FEUILLE AS TYPE_FACTURE, A.DATE_SOINS, A.NUM_OGD, A.PS AS CODE_PS, A.AFFECTION1, A.CODE_OGD_AFFILIATION, COUNT(B.CODE_ACTE) AS NOMBRE_ACTES, SUM(B.MONTANT * B.QUANTITE_SERVIE) AS MONTANT, SUM(B.MONTANT_ADJUJE) AS PART_CMU, SUM(B.MONTANT_COMPLEMENTAIRE) AS PART_AC FROM FS A JOIN CICMU_ADJUDICATION B ON A.FEUILLE = B.NUMERO_FEUILLE AND A.ETABLISSEMENT = ? AND A.NUM_BORDEREAU = ? AND B.STATUT = ? GROUP BY A.FEUILLE,  A.NUM_SECU, A.NUM_FS_INITIALE, A.TYPE_FEUILLE, A.DATE_SOINS, A.NUM_OGD, A.PS, A.AFFECTION1, A.CODE_OGD_AFFILIATION');
        $a->execute(array($code_ets,$num_bordereau, 1));
        $json = $a->fetchAll();
        return $json;
    }

}