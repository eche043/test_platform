<?php
/**
 * Created by PhpStorm.
 * User: mohamed.kone
 * Date: 05/02/2020
 * Time: 15:26
 */

class BORDEREAUX extends BDD
{
    public function lister($code_ets) {
        $a = $this->bdd->prepare('SELECT A.DATE_REG AS DATE_DEMANDE, A.NUMERO_BORDEREAU AS NUM_BORDEREAU, A.DATE_DEBUT_PERIODE AS DATE_DEBUT, A.DATE_FIN_PERIODE AS DATE_FIN, A.NUM_OGD_BORDEREAU AS CODE_OGD, A.TYPE_FEUILLE AS TYPE_FACTURE, COUNT(DISTINCT B.FEUILLE) AS NOMBRE_FACTURES FROM BORDEREAU_DE_TRANSMISSION A JOIN fs B ON (A.NUMERO_BORDEREAU = B.NUM_BORDEREAU AND A.CODE_ETS_BORDEREAU = ?) GROUP BY A.NUMERO_BORDEREAU, A.DATE_REG, A.DATE_DEBUT_PERIODE, A.DATE_FIN_PERIODE, A.NUM_OGD_BORDEREAU, A.TYPE_FEUILLE');
        $a->execute(array($code_ets));
        $json = $a->fetchAll();
        return $json;
    }

    public function trouver($code_ets,$num_bordereau) {
        $a = $this->bdd->prepare('SELECT * FROM BORDEREAU_DE_TRANSMISSION WHERE CODE_ETS_BORDEREAU = ? AND NUMERO_BORDEREAU = ? ');
        $a->execute(array($code_ets,$num_bordereau));
        $json = $a->fetch();
        return $json;
    }

    public function lister_types_factures($code_ets,$statut1,$statut2){
        $a = $this->bdd->prepare('SELECT DISTINCT(A.TYPE_FEUILLE) AS CODE, B.LIBELLE FROM fs A JOIN PROFIL_FSE B ON A.TYPE_FEUILLE = B.CODE AND A.ETABLISSEMENT = ? AND (A.STATUT = ? OR A.STATUT = ?)');
        $a->execute(array($code_ets,$statut1,$statut2));
        $json = $a->fetchAll();
        return $json;
    }



    public function lister_bordereaux_facture($code_ets,$num_bordereau){
        $a = $this->bdd->prepare('SELECT A.NUM_SECU, A.FEUILLE, A.NUM_FS_INITIALE, A.DATE_SOINS, A.NUM_OGD, A.PS AS CODE_PS, A.AFFECTION1, COUNT(B.CODE) AS NOMBRE_ACTES, SUM(B.MONTANT * B.QUANTITE) AS MONTANT, SUM(B.MONTANT * B.QUANTITE * 0.7) AS PART_CMU from FS A JOIN FS_ACTE B ON A.FEUILLE = B.FEUILLE AND A.ETABLISSEMENT = ? AND A.NUM_BORDEREAU = ? GROUP BY A.FEUILLE,  A.NUM_SECU, A.NUM_FS_INITIALE, A.DATE_SOINS, A.NUM_OGD, A.PS, A.AFFECTION1');
        $a->execute(array($code_ets,$num_bordereau));
        $json = $a->fetchAll();
        return $json;
    }

    public function lister_ogd_factures($type_facture,$code_ets,$statut1,$statut2){
        $a = $this->bdd->prepare('SELECT DISTINCT NUM_OGD AS CODE, NOM_OGD AS LIBELLE FROM fs WHERE TYPE_FEUILLE = ? AND ETABLISSEMENT = ? AND (STATUT = ? OR STATUT = ?)');
        $a->execute(array($type_facture,$code_ets,$statut1,$statut2));
        $json = $a->fetchAll();
        return $json;
    }

    public function lister_factures_bordereaux($code_ets,$type_facture,$code_ogd,$date_debut,$date_fin){
        $a = $this->bdd->prepare('SELECT FEUILLE AS NUM_FACTURE, NUM_FS_INITIALE FROM fs WHERE ETABLISSEMENT = ? AND NUM_OGD = ? AND TYPE_FEUILLE = ? AND (DATE_SOINS BETWEEN ? AND ?) AND (STATUT = ? OR STATUT = ?) AND NUM_BORDEREAU IS NULL AND (STATUT_BORDEREAU = ? OR STATUT_BORDEREAU IS NULL)');
        $a->execute(array($code_ets,$code_ogd,$type_facture,$date_debut,$date_fin,'F','T',0)) OR DIE(print_r(array($this->bdd->errorInfo())));
        $json = $a->fetchAll();
        return $json;
    }

    public function ajouter_nouveau_bordereau($type_facture, $code_ogd, $date_debut_periode, $date_fin_periode, $code_ets, $montant_total, $user){


        $a = $this->bdd->prepare("insert into bordereau_de_transmission(TYPE_FEUILLE, DATE_DEBUT_PERIODE, DATE_FIN_PERIODE, NUM_OGD_BORDEREAU, CODE_ETS_BORDEREAU, TOTAL_MONTANT_BORDEREAU,USER_REG)
values(:TYPE_FEUILLE, :DATE_DEBUT_PERIODE, :DATE_FIN_PERIODE, :NUM_OGD_BORDEREAU, :CODE_ETS_BORDEREAU, :TOTAL_MONTANT_BORDEREAU,:USER_REG)");

        $a ->execute(array(
            'TYPE_FEUILLE'=>$type_facture,
            'DATE_DEBUT_PERIODE'=>$date_debut_periode,
            'DATE_FIN_PERIODE'=>$date_fin_periode,
            'NUM_OGD_BORDEREAU'=>$code_ogd,
            'CODE_ETS_BORDEREAU'=>$code_ets,
            'TOTAL_MONTANT_BORDEREAU'=>$montant_total,
            'USER_REG'=> $user
        )) OR DIE ('ERREUR INSERTION');

        $b = $this->bdd->prepare('SELECT MAX(NUMERO_BORDEREAU) AS NUM_BORDEREAU FROM BORDEREAU_DE_TRANSMISSION WHERE CODE_ETS_BORDEREAU = ? AND TYPE_FEUILLE = ? AND NUM_OGD_BORDEREAU = ? ');
        $b->execute(array($code_ets,$type_facture,$code_ogd));
        $lastID = $b->fetch();

        $json =array(
            'status' => true,
            'num_bordereau' => $lastID['NUM_BORDEREAU']
        );
        return $json;
    }

    public function mise_a_jour_numero_bordereau_facture($type_facture, $numero_bordereau, $numero_facture){
        if($type_facture=='MED'){
            $a = $this->bdd->prepare('UPDATE fs SET STATUT_BORDEREAU = ?, NUM_BORDEREAU = ?  WHERE FEUILLE = ? AND TYPE_FEUILLE = ?');
            $a->execute(array(1, $numero_bordereau, $numero_facture,$type_facture)) OR DIE('ECHEC MISE A JOUR NUMERO BORDEREAU DE FACTURE MED');
        }else{
            $a = $this->bdd->prepare('UPDATE fs SET STATUT_BORDEREAU = ?, NUM_BORDEREAU = ?  WHERE FEUILLE = ? AND TYPE_FEUILLE = ?');
            $a->execute(array(1, $numero_bordereau,$numero_facture,$type_facture)) OR DIE('ECHEC MISE A JOUR NUMERO BORDEREAU DE FACTURE');
        }
        $json =array(
            'status' => true
        );
        return $json;
    }




}