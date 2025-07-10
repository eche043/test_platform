<?php
/**
 * Created by PhpStorm.
 * User: fabrice.bile
 * Date: 04/02/2020
 * Time: 19:27
 */

class ASSURES extends BDD {
    public function trouver_genre($code){
        $a = $this->bdd->prepare('SELECT CODE, LIBELLE FROM REF_SEXE WHERE CODE = ?');
        $a->execute(array($code));
        $json = $a->fetch();
        return $json;
    }

    public function trouver($num_secu){
        $a = $this->bdd->prepare('SELECT NUM_SECU, NOM, NOM_PATRONYMIQUE, PRENOM,CIVILITE, DATE_NAISSANCE,SEXE, STATUT_CMR, CODE_OGD_PRESTATIONS_PROV,CODE_OGD_PRESTATIONS,DATE_AFFILIATION,SITUATION_FAMILIALE,NATIONALITE,NAISSANCE_PAYS,ADRESSE_PAYS,PROFESSION,COLLECTIVITE_EMPLOYEUR,QUALITE_CIVILE,CODE_OGD_COTISATIONS,EXECUTANT_REFERENT,NAISSANCE_NOM_ACHEMINEMENT,NAISSANCE_SECTEUR_NAISSANCE, NAISSANCE_CODE_POSTAL,AUXILIAIRE_ADRESSE_1,AUXILIAIRE_ADRESSE_2,ADRESSE_NOM_ACHEMINEMENT,ADRESSE_CODE_POSTAL,COLLECTIVITE_SERVICE, COLLECTIVITE_FONCTION,COLLECTIVITE_MATRICULE_SALARIE,CATEGORIE_PROFESSIONNELLE,PAYEUR_NUM_SECU, ACTIVE FROM ecmu_assures WHERE NUM_SECU = ? AND STATUT != ? AND DATE_DECES IS NULL');
        $a->execute(array($num_secu,'RAD'));
        $json = $a->fetch();
        return $json;
    }

    public function maj_cmr($num_secu, $code_ets, $user){

        $assure = $this->trouver($num_secu);
        if(!empty($assure['NUM_SECU'])){
            if($assure['STATUT_CMR']!=1){
                $b = $this->bdd->prepare('UPDATE ECMU_ASSURES SET EXECUTANT_REFERENT_PROV = ?, STATUT_CMR = ?, DATE_MAJ_CMR = ?, DATE_EDIT = SYSDATE, USER_EDIT = ? WHERE NUM_SECU = ?');
                $b -> execute(array($code_ets, 1, date('Y-m-d',time()), $user, $num_secu)) OR DIE('ERREUR MISE A JOUR CMR ');
                $json = array(
                    'status' => true,
                    'message' => 'LE CMR DE L\'ASSURE A ETE MIS A JOUR AVEC SUCCES.'
                );
            }else{
                $json = array(
                    'status' => false,
                    'message' => 'UNE DEMANDE DE MISE A JOUR CMR EST DEJA EN COURS POUR CET ASSURE.'
                );
            }
        }else{
            $json = array(
                'status' => false,
                'message' => 'CET ASSURE N\'EXISTE PAS DANS LE SYSTEME.'
            );
        }

        return $json;
    }

    public function trouver_assure_genre($code) {
        $a = $this->bdd->prepare('SELECT CODE,LIBELLE FROM REF_SEXE WHERE CODE = ?');
        $a->execute(array($code));
        $json = $a->fetch();
        return $json;
    }

    public function trouver_assure_autre_payeur($num_secu) {
        $a = $this->bdd->prepare('
        SELECT * 
        FROM 
        ECMU_ASSURES 
        WHERE (CATEGORIE_PROFESSIONNELLE != ? 
        OR CATEGORIE_PROFESSIONNELLE != ? 
        OR CATEGORIE_PROFESSIONNELLE != ? 
        OR CATEGORIE_PROFESSIONNELLE != ? 
        OR CATEGORIE_PROFESSIONNELLE != ? 
        OR CATEGORIE_PROFESSIONNELLE != ?) 
        AND PAYEUR_NUM_SECU = ?');
        $a->execute(array('SAL','RET','REP','MIL','FCI','IND',$num_secu));
        $json = $a->fetch();
        return $json;
    }

    public function trouver_assure_civilite($code) {
        $a = $this->bdd->prepare('SELECT CODE,LIBELLE FROM REF_CIVILITE WHERE CODE = ?');
        $a->execute(array($code));
        $json = $a->fetch();
        return $json;
    }

    public function trouver_assure_profession($code) {
        $a = $this->bdd->prepare('SELECT CODE,LIBELLE FROM REF_PROFESSION WHERE CODE = ?');
        $a->execute(array($code));
        $json = $a->fetch();
        return $json;
    }

    public function trouver_csp($code){
        $a = $this->bdd->prepare('select LIBELLE from REF_CATEGORIE_PROFESSIONNELLE where CODE = ?');
        $a->execute(array($code));
        $json = $a->fetch();
        return $json;
    }

    public function trouver_liste_paiements($num_secu) {
        $a = $this->bdd->prepare('SELECT * FROM COTISATION_PAIEMENT_WEB 
WHERE NUM_SECU = ? 
AND STATUT = ?
ORDER BY DATE_REG DESC');
        $a->execute(array($num_secu,'1'));
        $json = $a->fetchAll();
        return $json;
    }

    public function verifier_paiement($num_ordre, $num_transaction) {
        $a = $this->bdd->prepare('SELECT * FROM COTISATION_PAIEMENT_WEB WHERE NUM_ORDRE = ? AND NUM_TRANSACTION = ?');
        $a->execute(array($num_ordre,$num_transaction));
        $paiement = $a->fetch();

        if(empty($paiement['NUM_TRANSACTION'])) {
            $json = array(
                'status' => false
            );
        }else {

            $json = array(
                'status' => true,
                'NUM_ORDRE' => $paiement['NUM_ORDRE'],
                'NUM_SECU' => $paiement['NUM_SECU'],
                'ASSURE_MONTANT' => $paiement['ASSURE_MONTANT'],
                'TRANSACTION_STATUT' => $paiement['STATUT'],
                'NUM_TRANSACTION' => $paiement['NUM_TRANSACTION'],
                'TRANSACTION_MONTANT' => $paiement['TRANSACTION_AMOUNT'],
                'CURRENCY' => $paiement['CURRENCY'],
                'MONTANT_PAYE' => $paiement['PAID_TRANSACTION_AMOUNT'],
                'WALLET' => $paiement['WALLET'],
                'DATE_REG' => $paiement['DATE_REG']
            );
        }
        return $json;
    }

    public function trouver_assure_active($num_secu) {
        $a = $this->bdd->prepare('SELECT * FROM ECMU_ASSURES WHERE NUM_SECU = ? and ACTIVE = ?');
        $a->execute(array($num_secu,1));
        $assure = $a->fetch();
//        echo $nb = count($assure);
        if(empty($assure['NUM_SECU'])){
            $json = array('status' =>false);
        }else{
            $json = $assure;
        }
        return $json;
    }

    public function trouver_assure($num_secu) {
        $a = $this->bdd->prepare('SELECT * FROM ECMU_ASSURES WHERE NUM_SECU = ?');
        $a->execute(array($num_secu));
        $nb = $a->fetch();
        if(empty($nb['NUM_SECU'])){
            $json = array('status' => false);
        }else{
            $json = $nb;
        }

        return $json;
    }

    public function dernieres_cotisations_assure($num_secu){
        $a = $this->bdd->prepare('SELECT * FROM (SELECT * FROM ECMU_COTISATION_VENTILATION WHERE BENEFICIAIRE_NUM_SECU = ? ORDER BY ANNEE DESC, MOIS DESC) WHERE ROWNUM <= 5');
        $a->execute(array($num_secu));
        $json = $a->fetchAll();
        return $json;
    }

    public function trouver_ventilations_cotisations_assure($num_secu){
        $a = $this->bdd->prepare('SELECT * FROM ECMU_COTISATION_VENTILATION WHERE BENEFICIAIRE_NUM_SECU = ? ');
        $a->execute(array($num_secu));
        $json = $a->fetchAll();
        return $json;
    }

    public function trouver_assure_paiements_ogd($num_secu){
        $a = $this->bdd->prepare('SELECT COUNT(ID_POPULATION) AS NOMBRE, CODE_OGD, STATUT FROM OGD_AFFILIATION_COTISATIONS oac WHERE BENEFICIAIRE_NUM_SECU = ? GROUP BY CODE_OGD, STATUT');
        $a->execute(array($num_secu));
        $json = $a->fetchAll();
        return $json;
    }

    public function historique_cotisations_assure($num_secu){
        $a = $this->bdd->prepare('SELECT * FROM COTISATION_PAIEMENT_WEB WHERE NUM_SECU = ? AND STATUT = ? AND PAYMENT_TYPE IN(?,?) ORDER BY DATE_REG DESC');
        $a->execute(array($num_secu,1,'I','F'));
        $json = $a->fetchAll();
        return $json;
    }

    public function trouver_annees_cotisation($num_secu){
        $a = $this->bdd->prepare('SELECT DISTINCT ANNEE FROM ECMU_COTISATION_VENTILATION WHERE BENEFICIAIRE_NUM_SECU = ? ORDER BY ANNEE DESC');
        $a->execute(array($num_secu));
        $json = $a->fetchAll();
        return $json;
    }

    public function trouver_cotisation($num_secu,$annee,$mois){
        $a = $this->bdd->prepare('SELECT * FROM ECMU_COTISATION_VENTILATION WHERE MOIS = ? AND ANNEE = ? AND BENEFICIAIRE_NUM_SECU = ?');
        $a->execute(array($mois,$annee,$num_secu));
        $json = $a->fetch();
        return $json;
    }

    public function statut_paiements() {
        $json = array(
            0 => 'Echec',
            1 => 'Succès',
            2 => 'Fonds insuffisants',
            3 => 'Transaction en cours de traiement',
            4 => 'Transaction inexistante',
            5 => 'Transaction annulée',
            6 => 'Statut de la transaction inconnu',
        );
        return $json;
    }

    public function liste_ayants_droits($num_secu){
        $a = $this->bdd->prepare('SELECT NUM_SECU, NOM, PRENOM, DATE_NAISSANCE,SEXE,PAYEUR_NUM_SECU FROM ECMU_ASSURES WHERE NUM_SECU != ? AND PAYEUR_NUM_SECU = ?');
        $a->execute(array($num_secu,$num_secu));
        $json = $a->fetchAll();
        return $json;
    }

    public function trouver_nationalite($code) {
        $a = $this->bdd->prepare('SELECT CODE,LIBELLE FROM REF_NATIONALITE WHERE CODE = ?');
        $a->execute(array($code));
        $json = $a->fetch();
        return $json;
    }

    public function trouver_situation_matrimoniale($code) {
        $a = $this->bdd->prepare('SELECT CODE,LIBELLE FROM REF_SITUATION_FAMILIALE WHERE CODE = ?');
        $a->execute(array($code));
        $json = $a->fetch();
        return $json;
    }
    public function trouver_pays($code) {
        $a = $this->bdd->prepare('SELECT CODE,LIBELLE FROM REF_GEOLOC_PAYS WHERE CODE = ?');
        $a->execute(array($code));
        $json = $a->fetch();
        return $json;
    }

    public function trouver_qualite_civile($code) {
        $a = $this->bdd->prepare('SELECT CODE,LIBELLE FROM REF_QUALITE_CIVILE WHERE CODE = ?');
        $a->execute(array($code));
        $json = $a->fetch();
        return $json;
    }

    public function trouver_coordonnees($num_secu) {
        $c = $this->bdd->prepare('SELECT * FROM ECMU_ASSURES_COORDONNEES WHERE NUM_SECU = ?');
        $c->execute(array($num_secu));
        $json = $c->fetchAll();
        return $json;
    }

    public function trouver_identifiants($num_secu) {
        $a = $this->bdd->prepare('SELECT * FROM ECMU_ASSURES_IDENTIFIANTS WHERE NUM_SECU = ?');
        $a->execute(array($num_secu));
        $json = $a->fetchAll();
        return $json;
    }

    public function trouver_type_identifiant($code) {
        $a = $this->bdd->prepare('SELECT code,libelle FROM REF_TYPE_IDENTIFIANT WHERE CODE = ?');
        $a->execute(array($code));
        $json = $a->fetch();
        return $json;
    }

    public function trouver_par_num_matricule_ogd($num_matricule_ogd){
        $a = $this->bdd->prepare('SELECT * FROM OGD_AFFILIATION_POPULATION WHERE BENEFICIAIRE_NUM_OGD = ?');
        $a->execute(array($num_matricule_ogd));
        $json = $a->fetch();
        return $json;
    }

    public function trouver_coordonnees_numero_mobile($num_secu) {
        $c = $this->bdd->prepare('SELECT * FROM ECMU_ASSURES_COORDONNEES WHERE NUM_SECU = ? AND TYPE_COORD = ?');
        $c->execute(array($num_secu, 'MOBPER'));
        $json = $c->fetchAll();
        return $json;
    }

    public function moteur_recherche_assures($num_secu, $nom_prenom) {
        $a = $this->bdd->prepare('
        SELECT
           NUM_SECU, NOM, NOM_PATRONYMIQUE, PRENOM,CIVILITE, DATE_NAISSANCE,SEXE, STATUT_CMR, CODE_OGD_PRESTATIONS_PROV,CODE_OGD_PRESTATIONS,DATE_AFFILIATION,SITUATION_FAMILIALE,NATIONALITE,NAISSANCE_PAYS,ADRESSE_PAYS,PROFESSION,COLLECTIVITE_EMPLOYEUR,QUALITE_CIVILE,CODE_OGD_COTISATIONS,EXECUTANT_REFERENT,NAISSANCE_NOM_ACHEMINEMENT,NAISSANCE_SECTEUR_NAISSANCE, NAISSANCE_CODE_POSTAL,AUXILIAIRE_ADRESSE_1,AUXILIAIRE_ADRESSE_2,ADRESSE_NOM_ACHEMINEMENT,ADRESSE_CODE_POSTAL,COLLECTIVITE_SERVICE, COLLECTIVITE_FONCTION,COLLECTIVITE_MATRICULE_SALARIE,CATEGORIE_PROFESSIONNELLE,PAYEUR_NUM_SECU, ACTIVE, STATUT  
        FROM
            ECMU_ASSURES
        WHERE 
            NUM_SECU LIKE ?
            AND (NOM || \' \' || PRENOM) LIKE ?
        ORDER BY 
            NOM, PRENOM ASC
        ');
        $a->execute(array('%'.$num_secu.'%','%'.$nom_prenom.'%'));
        $json = $a->fetchAll();
        return $json;
    }

    public function trouver_facture($num_secu) {
        $a = $this->bdd->prepare("SELECT * FROM FS WHERE NUM_SECU = ? AND STATUT IS NULL");
        $a->execute(array($num_secu));
        return $a->fetch();
    }

}