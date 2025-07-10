<?php
/**
 * Created by PhpStorm.
 * User: fabrice.bile
 * Date: 06/02/2020
 * Time: 14:51
 */

class ACTESMEDICAUX extends BDD
{
    public function trouver($code_acte){
        $a = $this->bdd->prepare("SELECT CODE,LIBELLE, TYPE_ACTE,TARIF,ENTENTE_PREALABLE,COEFFICIENT,PANIER,LETTRE_CLE FROM ECMU_REF_FS_ACTES_MEDICAUX WHERE CODE = ? AND PANIER = ?");
        $a->execute(array($code_acte,1));
        $json = $a->fetch();
        return $json;
    }

    public function trouver_reseau_acte_medical ($id_reseau,$code_acte,$date_soins){
        $a = $this->bdd->prepare("SELECT RESEAU_ID, CODE_ACTE, DATE_DEBUT_VALIDITE,TARIF FROM ECMU_RESEAUX_ACTES_MEDICAUX WHERE RESEAU_ID = ? AND CODE_ACTE = ? AND DATE_DEBUT_VALIDITE <= ? AND (DATE_FIN_VALIDITE >= ? OR DATE_FIN_VALIDITE IS NULL)");
        $a->execute(array($id_reseau,$code_acte,$date_soins,$date_soins));
        $json = $a->fetch();
        return $json;
    }

    public function trouver_acte(){
        $a = $this->bdd->prepare("SELECT DISTINCT(TITRE) FROM ECMU_REF_FS_ACTES_MEDICAUX WHERE TITRE != ' ' AND PANIER = 1 ORDER BY TITRE ASC");
        $a->execute(array());
        $json = $a->fetchAll();
        return $json;
    }

    public function trouver_un_acte($code_acte) {
        $a = $this->bdd->prepare('SELECT * FROM ECMU_REF_FS_ACTES_MEDICAUX WHERE CODE = ?');
        $a->execute(array($code_acte));
        $json = $a->fetch();
        return $json;
    }

    public function trouver_actes($libelle_acte,$titre_acte,$code_acte){
        $a = $this->bdd->prepare('SELECT * FROM ECMU_REF_FS_ACTES_MEDICAUX 
        WHERE  LIBELLE LIKE ?
        AND  TITRE LIKE ? 
        AND CODE LIKE ?
        ');
        $a->execute(array('%'.$libelle_acte.'%','%'.$titre_acte.'%','%'.$code_acte.'%'));
        $json = $a->fetchAll();
        return $json;
    }

    public function lettre_cle_trouve($code_lettre_cle) {
        $a = $this->bdd->prepare('SELECT * FROM ECMU_REF_FS_LETTRE_CLE WHERE CODE = ? AND FIN_VALIDITE IS NULL');
        $a->execute(array($code_lettre_cle));
        $json = $a->fetch();
        return $json;
    }
}
