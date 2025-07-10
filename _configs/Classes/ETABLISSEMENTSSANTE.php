<?php
/**
 * Created by PhpStorm.
 * User: fabrice.bile
 * Date: 06/02/2020
 * Time: 19:46
 */

class ETABLISSEMENTSSANTE extends BDD
{
    public function trouver_reseau_soins($code_ets){
        $a = $this->bdd->prepare('SELECT RESEAU_ID,CODE_ETS FROM ecmu_reseaux_ets WHERE CODE_ETS = ? AND RESEAU_STATUT = ?');
        $a->execute(array($code_ets,1));

        $json = $a->fetch();
        return $json;
    }

    public function moteur_recherche($code,$ville,$raison_sociale){
        $a = $this->bdd->prepare("
        SELECT 
            A.INP AS CODE_ETS, 
            A.RAISON_SOCIALE AS RAISON_SOCIALE,
            A.VILLE AS VILLE
        FROM 
            ECMU_REF_ETABLISSEMENT_SANTE A 
        JOIN PS_ETS B ON A.INP = B.CODE_ETS
            AND B.STATUT = ?
            AND A.INP LIKE ?
            AND A.VILLE LIKE ?
            AND A.RAISON_SOCIALE LIKE ?
        GROUP BY 
            A.INP,
            A.RAISON_SOCIALE, 
            A.VILLE 
        ORDER BY 
            A.RAISON_SOCIALE ASC
        ");
        $a->execute(array(1,'%'.$code.'%','%'.$ville.'%','%'.$raison_sociale.'%'));
         $json = $a->fetchAll();
        return $json;
    }

    public function trouver($code) {
        $a = $this->bdd->prepare('SELECT DISTINCT(INP),RAISON_SOCIALE FROM ecmu_ref_etablissement_sante WHERE INP = ?');
        $a->execute(array($code));
        $json = $a->fetch();
        return $json;
    }

    public function lister_ville(){
        $a = $this->bdd->prepare('SELECT DISTINCT(VILLE) FROM ECMU_REF_ETABLISSEMENT_SANTE ORDER BY VILLE ASC');
        $a->execute(array());
        $json = $a->fetchAll();
        return $json;
    }

    public function trouver_etablissement_sante($code) {
        $a = $this->bdd->prepare('SELECT * FROM ECMU_REF_ETABLISSEMENT_SANTE WHERE INP = ?');
        $a->execute(array($code));
        $json = $a->fetch();
        return $json;
    }

    public function liste_pharmacie() {
        $a = $this->bdd->prepare('
        SELECT INP AS CODE, RAISON_SOCIALE FROM ECMU_REF_ETABLISSEMENT_SANTE WHERE PHARMACIE = 1 ORDER BY RAISON_SOCIALE ASC');
        $a->execute(array());
        $json = $a->fetchAll();
        return $json;
    }

    public function trouver_pharmacie_pharmacie($raison_sociale) {
        $a = $this->bdd->prepare('
        SELECT DISTINCT(INP) AS CODE_ETS, RAISON_SOCIALE FROM ECMU_REF_ETABLISSEMENT_SANTE WHERE UPPER(RAISON_SOCIALE) LIKE UPPER(?) AND PHARMACIE = 1 AND INP IN (SELECT DISTINCT(CODE_ETS) FROM PS_ETS WHERE STATUT = 1) AND DATE_FIN_CONV IS NULL ORDER BY RAISON_SOCIALE ASC');
        $a->execute(array('%'.$raison_sociale.'%'));
        $json = $a->fetchAll();
        return $json;
    }

    public function trouver_ets_raison_sociale($raison_sociale) {
        $a = $this->bdd->prepare('SELECT DISTINCT(INP) AS CODE_ETS, RAISON_SOCIALE FROM ecmu_ref_etablissement_sante WHERE RAISON_SOCIALE LIKE ?');
        $a->execute(array('%'.$raison_sociale.'%'));
        $json = $a->fetchAll();
        return $json;
    }
	
	
    public function trouver_ets_valide($code) {
        $a = $this->bdd->prepare('SELECT INP AS CODE, RAISON_SOCIALE, ADRESSE_GEOGRAPHIQUE, SECTEUR_ACTIVITE, TYPE_ETS, CATEGORIE_PROFESSIONNELLE, TELEPHONE,  TELEPHONE_2, VILLE, FAX, EMAIL, DATE_CREATION, DATE_MISE_A_JOUR, DATE_DEBUT_CONV, DATE_FIN_CONV, MOTIF_FIN_CONVENTION, LONGIT, LATIT, PHARMACIE, ADRESSE_POSTALE, ADRESSE_GEOGRAPHIQUE, NIVEAU_SANITAIRE, LIBELLE_SPECIALITE, REGION, DEPARTEMENT, VILLAGE, USER_EDIT, USER_REG, DATE_EDIT, DATE_REG FROM ECMU_REF_ETABLISSEMENT_SANTE WHERE INP = ? AND DATE_FIN_CONV IS NULL');
        $a->execute(array($code));
        $json = $a->fetch();
        return $json;
    }

}