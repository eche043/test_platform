<?php
/**
 * Created by PhpStorm.
 * User: fabrice.bile
 * Date: 06/02/2020
 * Time: 14:00
 */

class ENTENTESPREALABLES extends BDD
{
    public function trouver_distinct_entente($num_entente){
        $a = $this->bdd->prepare("SELECT DISTINCT(NUM_ENTENTE_PREALABLE), NUM_SECU, TYPE_EP, STATUT, TO_CHAR(MOTIF_DEMANDE) AS MOTIF_DEMANDE, TO_CHAR(MOTIF) AS MOTIF, TYPE_HOSP FROM demande_entente_prealable WHERE NUM_ENTENTE_PREALABLE = ?");
        $a->execute(array($num_entente));

        $json = $a->fetch();
        return $json;
    }

    public function trouver_all_entente($num_entente, $num_secu, $code_ets){
        if(!empty($num_entente)){
            $a = $this->bdd->prepare("SELECT * FROM demande_entente_prealable WHERE NUM_ENTENTE_PREALABLE = ?");
            $a->execute(array($num_entente));
        }elseif(!empty($num_secu)){
            $a = $this->bdd->prepare("SELECT NUM_SECU FROM demande_entente_prealable WHERE NUM_SECU = ? AND CODE_ETS = ?");
            $a->execute(array($num_secu,$code_ets));
        }

        $json = $a->fetchAll();
        return $json;
    }

    public function trouver_lastId_entente_prealable(){
        $a = $this->bdd->prepare("SELECT Max(ID) as LASTID FROM demande_entente_prealable");
        $a->execute(array());
        $json = $a->fetch();
        return $json;
    }

    public function trouver_entente_hospitalisation_valide($typeEp,$typeHosp,$numFsInit,$num_secu){
        $a = $this->bdd->prepare('SELECT * FROM demande_entente_prealable WHERE TYPE_EP = ? AND TYPE_HOSP = ? AND NUM_FS_INITIALE = ? AND (STATUT = ? OR STATUT = ? OR STATUT IS NULL) AND NUM_SECU = ? ORDER BY DATE_REG DESC');
        $a->execute(array($typeEp,$typeHosp,$numFsInit,0,1,$num_secu));
        $json = $a->fetch();
        return $json;
    }

    public function trouver_entente_biologie_valide($typeEp,$numFsInit,$num_secu){
        $a = $this->bdd->prepare('SELECT * FROM demande_entente_prealable WHERE TYPE_EP = ? AND NUM_FS_INITIALE = ? AND (STATUT = ? OR STATUT = ? OR STATUT IS NULL) AND NUM_SECU = ? ORDER BY DATE_REG DESC');
        $a->execute(array($typeEp,$numFsInit,0,1,$num_secu));
        $json = $a->fetch();
        return $json;
    }

    public function ajouter_nouvelle_demande_entente($type_ep, $type_hosp, $num_fs_initiale, $code_acte_medical, $num_secu, $code_ets, $num_entente_prealable, $motif_demande, $code_ogd, $statut, $user_reg){
        $a = $this->bdd->prepare("INSERT INTO demande_entente_prealable(TYPE_EP, TYPE_HOSP, NUM_FS_INITIALE, CODE_ACTE_MEDICAL, NUM_SECU, CODE_ETS, NUM_ENTENTE_PREALABLE, MOTIF_DEMANDE, CODE_OGD, STATUT, USER_REG) VALUES(:TYPE_EP, :TYPE_HOSP, :NUM_FS_INITIALE, :CODE_ACTE_MEDICAL, :NUM_SECU, :CODE_ETS, :NUM_ENTENTE_PREALABLE, :MOTIF_DEMANDE, :CODE_OGD, :STATUT, :USER_REG)");
        $a ->execute(array(
            'TYPE_EP' => $type_ep,
            'TYPE_HOSP' => $type_hosp,
            'NUM_FS_INITIALE' => $num_fs_initiale,
            'CODE_ACTE_MEDICAL' => $code_acte_medical,
            'NUM_SECU' => $num_secu,
            'CODE_ETS' => $code_ets,
            'NUM_ENTENTE_PREALABLE' => $num_entente_prealable,
            'MOTIF_DEMANDE' => $motif_demande,
            'CODE_OGD' => $code_ogd,
            'STATUT' => $statut,
            'USER_REG' => $user_reg
        )) OR DIE ('ECHEC ENREGISTREMENT DEMANDE');
        $json =array(
            'status' => true
        );
        return $json;
    }

    public function liste_des_etablissements_par_ogd_ayant_une_entente($code_ogdp){
        $a = $this->bdd->prepare('
SELECT 
DISTINCT(ECMU_REF_ETABLISSEMENT_SANTE.INP), 
ECMU_REF_ETABLISSEMENT_SANTE.RAISON_SOCIALE 
FROM 
DEMANDE_ENTENTE_PREALABLE,
ECMU_REF_ETABLISSEMENT_SANTE 
WHERE 
DEMANDE_ENTENTE_PREALABLE.CODE_ETS = ECMU_REF_ETABLISSEMENT_SANTE.INP 
AND DEMANDE_ENTENTE_PREALABLE.CODE_OGD =  ?');
        $a->execute(array($code_ogdp));
        $json = $a ->fetchAll();
        return $json;
    }

    public function liste_entente_prealable_par_ogd($code_ogdp){
        $a = $this->bdd->prepare('
SELECT 
DISTINCT(NUM_ENTENTE_PREALABLE),
TYPE_EP,NUM_SECU,
DATE_REG FROM DEMANDE_ENTENTE_PREALABLE WHERE CODE_OGD =  ? AND (STATUT = ? OR STATUT IS NULL) ORDER BY DATE_REG DESC');
        $a->execute(array($code_ogdp,0));
        $json = $a ->fetchAll();
        return $json;
    }

    public function moteur_recherche($statut,$typeEp,$code_ogd,$date_demande,$code_ets){

        $a = $this->bdd->prepare('SELECT ID, TYPE_EP, TYPE_HOSP, NUM_FS_INITIALE, CODE_ACTE_MEDICAL, NUM_SECU, CODE_ETS, NUM_ENTENTE_PREALABLE, MOTIF_DEMANDE, CODE_OGD, STATUT, MOTIF, DATE_VALIDATION, DATE_REG, USER_REG, DATE_EDIT, USER_EDIT FROM DEMANDE_ENTENTE_PREALABLE WHERE STATUT LIKE ? AND TYPE_EP LIKE ?  AND CODE_OGD LIKE ? AND DATE_REG LIKE ? AND CODE_ETS LIKE ? ORDER BY DATE_REG DESC');
        $a->execute(array('%'.$statut.'%','%'.$typeEp.'%','%'.$code_ogd.'%','%'.$date_demande.'%','%'.$code_ets.'%')) or die(print_r(array($this->bdd->errorInfo())));
        $json = $a->fetchAll();
        return $json;
    }

    public function trouver_entente_prealable($numero_entente_prealable){
        $a = $this->bdd->prepare('
SELECT 
DISTINCT
(NUM_ENTENTE_PREALABLE), 
TYPE_EP, 
TYPE_HOSP, 
NUM_FS_INITIALE, 
CODE_ACTE_MEDICAL, 
NUM_SECU, 
CODE_ETS, 
to_char(MOTIF_DEMANDE) AS MOTIF_DEMANDE, 
CODE_OGD, 
STATUT, 
to_char(MOTIF) AS MOTIF, DATE_VALIDATION, DATE_REG, USER_REG, ADMIN_REG, DATE_EDIT, USER_EDIT, ADMIN_EDIT 
FROM DEMANDE_ENTENTE_PREALABLE 
WHERE NUM_ENTENTE_PREALABLE = ?');
        $a ->execute(array($numero_entente_prealable));
        $json = $a->fetch();
        return $json;
    }

    public function trouver_liste_actes_entente_par_numero($numero){
        $a = $this->bdd->prepare('SELECT TYPE_EP, TYPE_HOSP, NUM_FS_INITIALE, CODE_ACTE_MEDICAL, NUM_SECU, CODE_ETS, TO_CHAR(MOTIF_DEMANDE) AS "MOTIF_DEMANDE", CODE_OGD, STATUT, TO_CHAR(MOTIF) AS "MOTIF", DATE_VALIDATION, DATE_REG, USER_REG, DATE_EDIT, USER_EDIT FROM DEMANDE_ENTENTE_PREALABLE WHERE NUM_ENTENTE_PREALABLE = ?');
        $a->execute(array($numero));
        $json = $a->fetchAll();

        return $json;
    }

    public function trouver_type_hospitalisation_par_code($code_type_hosp){
        $a = $this->bdd->prepare('SELECT CODE_TYPE_HOSP, LIBELLE_HOSP FROM TYPE_HOSPITALISATION WHERE CODE_TYPE_HOSP = ?');
        $a->execute(array($code_type_hosp));

        $json = $a->fetch();

        return $json;
    }

    public function validation_entente_prealable_type_exp_par_acte($num_ep,$motif_refus,$statut,$code_acte,$date_validation,$user){
        //$date = date('d-M-y H:i:s',time());

        $a = $this->bdd->prepare('UPDATE DEMANDE_ENTENTE_PREALABLE SET MOTIF = ?,STATUT = ?,DATE_VALIDATION = ?,DATE_EDIT = SYSDATE,USER_EDIT = ? WHERE NUM_ENTENTE_PREALABLE = ? AND CODE_ACTE_MEDICAL = ?');
        $a->execute(array($motif_refus,$statut,$date_validation,$user,$num_ep,$code_acte)) OR DIE("Error Refus EP de acte".$code_acte);

        $json = array(
            'status' => true
        );
        return $json;
    }

    public function validation_entente_prealable_type_hosp($num_ep,$statut,$user){
        /*$date = date('d-M-y H:i:s',time());
        $date_validation=date('d-M-y',strtotime($date));*/

        $a = $this->bdd->prepare('UPDATE DEMANDE_ENTENTE_PREALABLE SET STATUT = ?,DATE_VALIDATION = SYSDATE,DATE_EDIT = SYSDATE,USER_EDIT = ? WHERE NUM_ENTENTE_PREALABLE = ?');
        $a->execute(array($statut,$user,$num_ep)) OR DIE("Error validation EP hosp");

        $json = array(
            'status' => true
        );
        return $json;
    }

    public function refus_entente_prealable($num_ep,$motif_refus,$statut,$user){

        $a = $this->bdd->prepare('UPDATE DEMANDE_ENTENTE_PREALABLE SET MOTIF = ?, STATUT = ?, DATE_EDIT = SYSDATE, USER_EDIT = ? WHERE NUM_ENTENTE_PREALABLE = ?');
        $a->execute(array($motif_refus,$statut,$user,$num_ep)) OR DIE(print_r(array("Error validation EP hosp",$num_ep,$statut,$motif_refus,$user,$this->bdd->errorInfo())));

        $json = array(
            'status' => true
        );
        return $json;
    }

}