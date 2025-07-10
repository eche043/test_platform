<?php


class ASSURANCE extends BDD
{
    public function trouver($code) {
        $a = $this->bdd->prepare('
        SELECT VERSION,CODE,LIBELLE,TYPE_ASSURANCE,DEBUT_VALIDITE FROM REF_ASSURANCES_MUTUELLES WHERE CODE = ?');
        $a->execute(array($code));
        $json = $a->fetch();
        return $json;
    }

    public function liste_assures_par_mutuelle($code_mutuelle,$date_debut,$date_fin){
        $a = $this->bdd->prepare('SELECT * FROM ECMU_POPULATIONS_ASSURANCE WHERE CODE_ASSURANCE = ? AND DATE_REG BETWEEN ? AND ?');
        $a->execute(array($code_mutuelle,$date_debut,$date_fin));
        $json = $a->fetchAll();
        return $json;
    }

    public function trouver_numero_matricule_ac($numero_matricule) {
        $a = $this->bdd->prepare('
        SELECT * FROM TB_POPULATIONS_MUGEFCI WHERE MATRICULE_BENEFICIAIRE_MUGEFCI = ?');
        $a->execute(array($numero_matricule));
        $json = $a->fetch();
        return $json;
    }
}