<?php
/**
 * Created by PhpStorm.
 * User: mohamed.kone
 * Date: 20/04/2021
 * Time: 15:19
 */

include "BDD.php";
class DUPLICATA extends BDD
{
    public function lister_type_document(){
        $a = $this->bdd->prepare("SELECT * from REF_TYPE_DOCUMENT");
        $a->execute(array());
        $json = $a->fetchAll();
        return $json;
    }
    public function editer_reedition_carte($id_demande,$num_secu,$nom,$prenoms,$date_naissance,$numero_telephone,$type_piece,$numero_piece,$date_fin_validite,$scan_piece,$motif_demande,$scan_carte_cmu,$scan_declaration_perte){
        $a = $this->bdd->prepare("
        INSERT INTO ECMU_REEDITION_CARTES(ID_DEMANDE,NUM_SECU,NOM,PRENOMS,DATE_NAISSANCE,NUM_TELEPHONE,TYPE_PIECE,NUMERO_PIECE,DATE_FIN_VALIDITE_PIECE,SCAN_PIECE,MOTIF_DEMANDE,SCAN_CARTE_CMU,SCAN_DECLARATION_PERTE,STATUT_VALIDATION)
        VALUES(:ID_DEMANDE,:NUM_SECU,:NOM,:PRENOMS,:DATE_NAISSANCE,:NUM_TELEPHONE,:TYPE_PIECE,:NUMERO_PIECE,:DATE_FIN_VALIDITE_PIECE,:SCAN_PIECE,:MOTIF_DEMANDE,:SCAN_CARTE_CMU,:SCAN_DECLARATION_PERTE,:STATUT_VALIDATION)
        ");
        $a->execute(array(
            'ID_DEMANDE' => $id_demande,
            'NUM_SECU'=> $num_secu,
            'NOM' => $nom,
            'PRENOMS' => $prenoms,
            'DATE_NAISSANCE' => $date_naissance,
            'NUM_TELEPHONE'=> $numero_telephone,
            'TYPE_PIECE'=> $type_piece,
            'NUMERO_PIECE'=> $numero_piece,
            'DATE_FIN_VALIDITE_PIECE' => $date_fin_validite,
            'SCAN_PIECE' => $scan_piece,
            'MOTIF_DEMANDE' => $motif_demande,
            'SCAN_CARTE_CMU' => $scan_carte_cmu,
            'SCAN_DECLARATION_PERTE' => $scan_declaration_perte,
            'STATUT_VALIDATION' => 0,
        )) OR DIE(print_r(array($this->bdd->errorInfo(),$id_demande,$num_secu,$nom,$prenoms,$date_naissance,$numero_telephone,$type_piece,$numero_piece,$date_fin_validite,$scan_piece,$motif_demande,"UNE ERREUR EST SURVENUE")));

        $json = array(
            'status'=>"success",
            'message' => 'ENREGISTREMENT EFFECTUE AVEC SUCCES'.'<br>'.'Numéro dossier : '.'<b>'.$id_demande.'</b>');

        return $json;
    }
    public function trouver_reedition_carte($id_demande){
        $a = $this->bdd->prepare("SELECT * FROM ECMU_REEDITION_CARTES WHERE ID_DEMANDE = ? ");
        $a->execute(array($id_demande));
        $json = $a->fetch();
        return $json;
    }
    public function trouver_libelle_motif($id_motif){
        $a = $this->bdd->prepare("SELECT MOTIF_LIBELLE FROM ECMU_REEDITION_CARTES_MOTIFS WHERE MOTIF_CODE = ? ");
        $a->execute(array($id_motif));
        $json = $a->fetch();
        return $json;
    }
    public function trouver_reedition_carte_num_secu($num_secu){
        $a = $this->bdd->prepare("SELECT * FROM ECMU_REEDITION_CARTES WHERE NUM_SECU = ? ");
        $a->execute(array($num_secu));
        $json = $a->fetch();
        return $json;
    }
    public function trouver_carte($id_demande, $num_secu){
        $a = $this->bdd->prepare("SELECT * FROM ECMU_REEDITION_CARTES WHERE ID_DEMANDE = ? OR NUM_SECU = ? ");
        $a->execute(array($id_demande,$num_secu));
        $json = $a->fetch();
        return $json;
    }
    public function trouver_num_suivi_carte($id_demande)
    {
            $a = $this->bdd->prepare("SELECT * FROM ECMU_REEDITION_CARTES WHERE ID_DEMANDE = ? ");
            $a->execute(array($id_demande));
            $json = $a->fetch();
            return $json;
    }
    public function trouver_num_suivi_num_secucarte($id_demande)
    {
            if(strlen($id_demande) == 13){
                $a = $this->bdd->prepare("SELECT * FROM ECMU_REEDITION_CARTES WHERE NUM_SECU = ? ");
                $a->execute(array($id_demande));
                $json = $a->fetch();

            }elseif(strlen($id_demande) == 25){
                $a = $this->bdd->prepare("SELECT * FROM ECMU_REEDITION_CARTES WHERE ID_DEMANDE = ? ");
                $a->execute(array($id_demande));
                $json = $a->fetch();
            }else{
                $json = false;
            }

        return $json;
    }
    public function trouver_motif($code){
        $a = $this->bdd->prepare("SELECT * from ECMU_REEDITION_CARTES_MOTIFS WHERE MOTIF_CODE = ?");
        $a->execute(array($code));
        $json = $a->fetch();
        return $json;
    }
    public function verif_statut_carte($num_secu){
        $a = $this->bdd->prepare("SELECT NUM_SECU,STATUT_VALIDATION FROM ECMU_REEDITION_CARTES WHERE NUM_SECU = ? ");
        $a->execute(array($num_secu));
        $json = $a->fetch();
        return $json;
    }
	
    public function lister_motifs(){
        $a = $this->bdd->prepare("SELECT * from ECMU_REEDITION_CARTES_MOTIFS WHERE MOTIF_STATUT = ? ");
        $a->execute(array(1));
        $json = $a->fetchAll();
        return $json;
    }
}