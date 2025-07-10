<?php
/**
 * Created by PhpStorm.
 * User: mohamed.kone
 * Date: 04/02/2020
 * Time: 17:44
 */

class FACTURES extends BDD
{

    public function lister_facture_a_traiter($code_ets){
        $a = $this->bdd->prepare('SELECT * FROM fs WHERE ETABLISSEMENT = ? AND  TYPE_FEUILLE IS NULL  AND (STATUT = ? OR STATUT = ? OR STATUT IS NULL) ORDER BY DATE_SOINS DESC, DATE_REG DESC');
        $a->execute(array($code_ets,'N',''));
        $json = $a->fetchAll();
        return $json;
    }

    public function lister_facture_a_finaliser($code_ets){
        $a = $this->bdd->prepare('SELECT * FROM fs WHERE ETABLISSEMENT = ? AND STATUT = ? AND TYPE_FEUILLE != ? ORDER BY DATE_SOINS DESC');
        $a->execute(array($code_ets,'C','MED'));
        $json = $a->fetchAll();
        return $json;
    }

    public function lister_facture_en_pharmacie($code_ets){
        $a = $this->bdd->prepare('SELECT * FROM fs WHERE ETABLISSEMENT = ? AND TYPE_FEUILLE = ? AND (STATUT = ? OR STATUT = ? OR STATUT IS NULL) ORDER BY DATE_SOINS DESC');
        $a->execute(array($code_ets,'MED','C','N'));
        $json = $a->fetchAll();
        return $json;
    }

    public function lister($date_soins,$code_ogd,$code_csp,$num_secu,$genre,$code_ets,$type_ets,$code_ps,$code_ps_specialite,$statut,$user){
        $a = $this->bdd->prepare('SELECT * FROM fs WHERE DATE_SOINS LIKE TO_DATE(?) AND NUM_OGD LIKE ? AND CODE_CSP LIKE ? AND NUM_SECU LIKE ? AND GENRE LIKE ? AND ETABLISSEMENT LIKE ? AND TYPE_ETS LIKE ? AND PS LIKE ? AND CODE_SPECIALITE LIKE ? AND STATUT LIKE ? AND USER_EDIT LIKE ? ORDER BY FEUILLE DESC');
        $a->execute(array('%'.$date_soins.'%','%'.$code_ogd.'%','%'.$code_csp.'%','%'.$num_secu.'%','%'.$genre.'%','%'.$code_ets.'%','%'.$type_ets.'%','%'.$code_ps.'%','%'.$code_ps_specialite.'%','%'.$statut.'%','%'.$user.'%'));
        $json = $a->fetchAll();
        return $json;

    }

    public function lister_actes($num_facture){
        $a = $this->bdd->prepare('SELECT * FROM fs_actes WHERE FEUILLE = ?');
        $a->execute(array($num_facture));
        $json = $a->fetchAll();
        return $json;
    }

    public function trouver($num_facture){
        $a = $this->bdd->prepare('SELECT * FROM FS WHERE FEUILLE = ?');
        $a->execute(array($num_facture));
        $json = $a->fetch();
        return $json;
    }

    public function trouver_type_facture($code_type) {
        $a = $this->bdd->prepare('SELECT CODE, LIBELLE FROM PROFIL_FSE WHERE CODE = ?');
        $a->execute(array($code_type));
        $json = $a->fetch();
        return $json;
    }

    public function lister_types_factures() {
        $a = $this->bdd->prepare('SELECT CODE,LIBELLE FROM PROFIL_FSE ORDER BY CODE ASC');
        $a->execute(array());
        $json = $a->fetchAll();
        return $json;
    }

    public function moteur_recherche_factures($code_ets,$type_facture,$date_soins,$num_facture,$num_secu,$nom) {
        $query = "
        SELECT 
            A.DATE_SOINS, 
            C.CODE AS CODE_TYPE_FACTURE, 
            C.LIBELLE AS LIBELLE_TYPE_FACTURE, 
            A.FEUILLE,
            A.ETABLISSEMENT, 
            A.NUM_FS_INITIALE, 
            A.NUM_SECU,
			A.NUM_BORDEREAU,			
            (A.NOM || ' ' || A.PRENOM) AS NOM_PRENOM,
            COUNT(B.CODE) AS NB_ACTES, 
            A.STATUT,
            SUM(B.MONTANT * B.QUANTITE) AS MONTANT
        FROM 
            FS A, 
            FS_ACTE B,
            PROFIL_FSE C 
        WHERE 
            A.FEUILLE = B.FEUILLE 
            AND A.TYPE_FEUILLE = C.CODE               
            AND A.ETABLISSEMENT  LIKE ? 
            AND A.DATE_SOINS LIKE ? 
            AND A.TYPE_FEUILLE LIKE ?           
            AND A.FEUILLE LIKE ? 
            AND A.NUM_SECU LIKE ? 
            AND (A.NOM || ' ' || A.PRENOM) LIKE ?
        GROUP BY 
            A.DATE_SOINS,
            C.CODE, 
            C.LIBELLE, 
            A.FEUILLE, 
            A.ETABLISSEMENT,
            A.NUM_FS_INITIALE,
            A.NUM_SECU,
			A.NUM_BORDEREAU,
            (A.NOM || ' ' || A.PRENOM),
            A.STATUT
         ORDER BY DATE_SOINS DESC     
        ";
        $a = $this->bdd->prepare($query);
        $a->execute(array($code_ets,'%'.$date_soins.'%','%'.$type_facture.'%','%'.$num_facture.'%','%'.$num_secu.'%','%'.$nom.'%'));
        $json = $a->fetchAll();
        return $json;
    }

    public function trouver_facture_liste_actes($num_facture) {
        $a = $this->bdd->prepare('SELECT * FROM FS_ACTE WHERE FEUILLE = ?');
        $a->execute(array($num_facture));
        $json = $a->fetchAll();
        return $json;
    }

    public function trouver_facture_acte($num_facture,$code_acte) {
        $a = $this->bdd->prepare('SELECT B.DATE_SOINS, B.ETABLISSEMENT, A.* FROM FS_ACTE A JOIN FS B ON B.FEUILLE = A.FEUILLE WHERE A.FEUILLE = ? AND UPPER(A.CODE) = UPPER(?)');
        $a->execute(array($num_facture, $code_acte));
        $acte = $a->fetch();
        if(!empty($acte['CODE'])) {
            if($acte['TYPE'] == 'm') { $type_acte = 'MED';}else{$type_acte = 'NGAP';}
            $date_soins = strtoupper(date('Y-m-d', strtotime(str_replace('/', '-', $acte['DATE_SOINS']))));
            $acte_libelle = $this->trouver_acte($type_acte, $acte['ETABLISSEMENT'], $acte['CODE'], null, $date_soins);
            
            if(!empty($acte_libelle['libelle'])) {
                $json = array(
                    'status' => true,
                    'CODE' => $acte['CODE'],
                    'LIBELLE' => $acte_libelle['libelle'],
                    'DATE_DEBUT' => $acte['DEBUT'],
                    'DATE_FIN' => $acte['FIN'],
                    'QUANTITE' => $acte['QUANTITE'],
                    'QUANTITE_PRESCRITE' => $acte['QUANTITE_PRESCRITE'],
                    'MONTANT' => $acte['MONTANT'],
                    'NUM_DENT' => $acte['NUM_DENT'],
                    'POURCENTAGE' => $acte['POURCENTAGE'],
                    'MONTANT_BASE' => $acte['MONTANT_BASE'],
                    'PART_RO' => $acte['PART_RO'],
                    'PART_ASSURE' => $acte['PART_ASSURE'],
                    'PART_RC' => $acte['PART_RC'],
                    'MONTANT_BASE_AC' => $acte['MONTANT_BASE_AC'],
                    'TAUX_RO' => $acte['TAUX_RO'],
                    'TAUX_RC' => $acte['TAUX_RC']
                );
            }else {
				if($acte['TYPE'] == 'a') {
                    $b = $this->bdd->prepare('SELECT LIBELLE FROM ECMU_REF_FS_ACTES_MEDICAUX WHERE UPPER(CODE) = UPPER(?) AND DATE_FIN IS NULL');
                    $b->execute(array($acte['CODE']));
                    $acte_libelle = $b->fetch();
                    $montant_base = $acte['MONTANT'];
                }else {
                    if(substr($acte['CODE'],0,5)=='06188' || substr($acte['CODE'],0,5)=='22500'){
                        $b = $this->bdd->prepare('SELECT EAN13, LIBELLE, PP FROM ECMU_REF_FS_MEDICAMENTS WHERE EAN13 = ? AND DATE_DEBUT_VALIDITE <= ? AND (DATE_FIN_VALIDITE >= ? OR DATE_FIN_VALIDITE IS NULL)');
                    }else{
                        $b = $this->bdd->prepare('SELECT EAN13, LIBELLE, PP FROM ECMU_REF_FS_MEDICAMENTS WHERE CODE = ? AND DATE_DEBUT_VALIDITE <= ? AND (DATE_FIN_VALIDITE >= ? OR DATE_FIN_VALIDITE IS NULL)');
                    }
                    $b->execute(array($acte['CODE'],$acte['DATE_SOINS'],$acte['DATE_SOINS']));
                    $acte_libelle = $b->fetch();
                    $montant_base = $acte_libelle['PP'];

                    if(substr($acte_libelle['EAN13'],0,5)=='22500'){
                        $acte['CODE'] = $acte_libelle['EAN13'];
                    }
                }
                $json = array(
                    'status' => true,
                    'CODE' => $acte['CODE'],
                    'LIBELLE' => $acte_libelle['LIBELLE'],
                    'DATE_DEBUT' => $acte['DEBUT'],
                    'DATE_FIN' => $acte['FIN'],
                    'QUANTITE' => $acte['QUANTITE'],
                    'QUANTITE_PRESCRITE' => $acte['QUANTITE_PRESCRITE'],
                    'MONTANT' => $acte['MONTANT'],
                    'NUM_DENT' => $acte['NUM_DENT'],
                    'POURCENTAGE' => $acte['POURCENTAGE'],
                    'MONTANT_BASE' => $acte['MONTANT_BASE'],
                    'PART_RO' => $acte['PART_RO'],
                    'PART_ASSURE' => $acte['PART_ASSURE'],
                    'PART_RC' => $acte['PART_RC'],
                    'MONTANT_BASE_AC' => $acte['MONTANT_BASE_AC'],
                    'TAUX_RO' => $acte['TAUX_RO'],
                    'TAUX_RC' => $acte['TAUX_RC']
                );
            }
        }else {
            $json = array(
                'status' => true,
                'CODE' => NULL,
                'LIBELLE' => NULL,
                'DATE_DEBUT' => NULL,
                'DATE_FIN' => NULL,
                'QUANTITE' => NULL,
                'QUANTITE_PRESCRITE' => NULL,
                'MONTANT' => NULL,
                'NUM_DENT' => NULL,
                'POURCENTAGE' => NULL,
                'MONTANT_BASE' => NULL,
                'PART_RO' => NULL,
                'PART_ASSURE' => NULL,
                'PART_RC' => NULL,
                'MONTANT_BASE_AC' => NULL,
                'TAUX_RO' => NULL,
                'TAUX_RC' => NULL
            );
        }
        return $json;
    }

    public function trouver_facture_initiale($num_facture) {
        $a = $this->bdd->prepare('SELECT * FROM fs WHERE (TYPE_FEUILLE = ? OR TYPE_FEUILLE = ? OR TYPE_FEUILLE IS NULL) AND FEUILLE = ?');
        $a->execute(array('AMB','DEN',$num_facture));
        $json = $a->fetch();
        return $json;
    }

    public function maj_type_facture($num_facture,$type,$num_fs_initiale,$num_ep,$user) {
        if($type == 'HOS'){
            $a = $this->bdd->prepare('UPDATE fs SET TYPE_FEUILLE = ?, NUM_FS_INITIALE = ?, NUM_EP_CNAM = ?, DATE_EDIT = SYSDATE, USER_EDIT = ? WHERE FEUILLE = ?');
            $a->execute(array($type,$num_fs_initiale,$num_ep,$user,$num_facture)) OR DIE("ERREUER MIS A JOUR TYPE FACTURE");
        }else{
            $a = $this->bdd->prepare('UPDATE fs SET TYPE_FEUILLE = ? WHERE FEUILLE = ?');
            $a->execute(array($type,$num_facture)) OR DIE("ERREUER MIS A JOUR TYPE FACTURE");

        }
        $json =array(
            'status' => true,
            'message' => 'LE TYPE DE LA FACTURE A ETE MIS A JOUR.'
        );

        return $json;
    }

    public function maj_affection_facture($code_affection_1, $code_affection_2, $num_facture) {
        $a = $this->bdd->prepare('UPDATE fs SET AFFECTION1 = ?, AFFECTION2 = ? WHERE FEUILLE = ?');
        $a->execute(array($code_affection_1, $code_affection_2,$num_facture)) OR DIE("ERREUER MIS A JOUR AFFECTION FACTURE");
        $json =array(
            'status' => true,
            'message' => 'LES AFFECTIONS DE LA FACTURE ONT ETE MISES A JOUR.'
        );
        return $json;
    }
	
	public function maj_ps_facture($code_ps,$code_specialite_ps, $num_facture) {
        $a = $this->bdd->prepare('UPDATE fs SET PS = ?, CODE_SPECIALITE = ? WHERE FEUILLE = ?');
        $a->execute(array($code_ps,$code_specialite_ps,$num_facture)) OR DIE("ERREUR MIS A JOUR PS FACTURE");
        $json =array(
            'status' => true,
            'message' => 'LE PS DE LA FACTURE A ETE MIS A JOUR.'
        );
        return $json;
    }
	
    public function maj_facture($type_facture, $date_soins, $num_facture, $num_fs_initiale, $num_ep_cnam, $num_ep_ac, $num_matricule_ac, $type_ets, $autre_type_ets, $info_complementaire, $autre_info_complementaire, $num_immatriculation_vehicule, $date_accident, $code_programme, $code_affection_1, $code_affection_2, $date_fin, $motif_fin, $code_ps, $code_ps_specialite, $statut, $user){
        $a = $this->bdd->prepare('UPDATE FS SET TYPE_FEUILLE = ?, DATE_SOINS = ?, NUM_FS_INITIALE = ?, NUM_EP_CNAM = ?, NUM_EP_AC = ?, NUM_MATRICULE_AC = ?, TYPE_ETS = ?, AUTRE_TYPE_ETS = ?, INFO_COMPLEMENTAIRE = ?, AUTRE_INFO_COMPLEMENTAIRE = ?, NUM_IMM_VEHICULE = ?, DATE_ACCIDENT = ?, PROG_SPECIAL = ?, AFFECTION1 = ?, AFFECTION2 = ?, DATE_FIN = ?, MOTIF_FIN = ?, PS = ?, CODE_SPECIALITE = ?, STATUT = ?, DATE_EDIT = SYSDATE, USER_EDIT = ? WHERE FEUILLE = ?');
        $a->execute(array($type_facture, $date_soins, $num_fs_initiale, $num_ep_cnam, $num_ep_ac, $num_matricule_ac, $type_ets, $autre_type_ets, $info_complementaire, $autre_info_complementaire, $num_immatriculation_vehicule, $date_accident, $code_programme, $code_affection_1, $code_affection_2, $date_fin, $motif_fin, $code_ps, $code_ps_specialite, $statut, $user, $num_facture))OR DIE('Erreur MAJ FACTURE');
        $json = array(
            'status' => true,
            'message' => "LA FACTURE N° ".$num_facture." A ETE MISE A JOUR AVEC SUCCES."
        );
        return $json;
    }

    public function ajouter_facture_acte($type_acte, $num_facture, $code_acte, $date_debut, $date_fin, $quantite, $quantite_prescrite, $montant, $num_dent, $pourcentage, $part_ro, $part_rc, $part_assure, $montant_base_ac, $montant_base, $taux_ro, $taux_rc, $user) {
        $a = $this->bdd->prepare('INSERT INTO FS_ACTE(TYPE, FEUILLE, CODE, DEBUT, FIN, QUANTITE, QUANTITE_PRESCRITE, MONTANT, NUM_DENT, POURCENTAGE, PART_RO,PART_RC,PART_ASSURE,MONTANT_BASE_AC,MONTANT_BASE,TAUX_RO,TAUX_RC, USER_REG) VALUES(:TYPE, :FEUILLE, :CODE, :DEBUT, :FIN, :QUANTITE, :QUANTITE_PRESCRITE, :MONTANT, :NUM_DENT, :POURCENTAGE, :PART_RO, :PART_RC, :PART_ASSURE, :MONTANT_BASE_AC, :MONTANT_BASE, :TAUX_RO, :TAUX_RC, :USER_REG)');
        $a->execute(array(
            'TYPE' => $type_acte,
            'FEUILLE' => $num_facture,
            'CODE' => $code_acte,
            'DEBUT' => $date_debut,
            'FIN' => $date_fin,
            'QUANTITE' => $quantite,
            'QUANTITE_PRESCRITE' => $quantite_prescrite,
            'MONTANT' => $montant,
            'NUM_DENT' => $num_dent,
            'POURCENTAGE' => $pourcentage,
            'PART_RO' => $part_ro,
            'PART_RC' => $part_rc,
            'PART_ASSURE' => $part_assure,
            'MONTANT_BASE_AC' => $montant_base_ac,
            'MONTANT_BASE' => $montant_base,
            'TAUX_RO' => $taux_ro,
            'TAUX_RC' => $taux_rc,
            'USER_REG' => $user
        ))OR DIE($type_acte.' Erreur AJOUT ACTE '.var_dump($this->bdd->errorInfo()));
		// if($a->errorCode() == '00000') {
			// $json = array(
				// 'status' => true,
				// 'code' => $code,
				// 'message' => 'ENREGISTREMENT EFFECTUE AVEC SUCCES.'
			// );
		// }else {
			// $json = array(
				// 'status' => false,
				// 'message' => $a->errorInfo()[2]
			// );
		// }
        $json = array(
            'status' => true
        );
        return $json;
    }

    public function supprimer_facture_actes($num_facture) {
        $a = $this->bdd->prepare('DELETE FROM FS_ACTE WHERE FEUILLE = ?');
        $a->execute(array($num_facture))OR DIE('Erreur DELETE ACTE');
        $b = $this->bdd->prepare('COMMIT');
        $b->execute(array())OR DIE('Erreur DELETE ACTE COMMIT');

        $json = array(
            'status' => true
        );
        return $json;
    }

    public function trouver_ets_bordereau($code_ets,$num_bordereau) {
        $a = $this->bdd->prepare('
SELECT
    A.DATE_REG AS DATE_DEMANDE,
    A.NUM_OGD_BORDEREAU AS NUM_OGD, 
    A.NUMERO_BORDEREAU AS NUM_BORDEREAU, 
    A.DATE_DEBUT_PERIODE AS DATE_DEBUT, 
    A.DATE_FIN_PERIODE AS DATE_FIN, 
    A.TYPE_FEUILLE AS TYPE_FACTURE,
    C.MONTANT * C.QUANTITE AS MONTANT,
    C.MONTANT * C.QUANTITE AS PART_CMU,
    B.FEUILLE AS NBRE_FACTURES, 
    C.CODE AS NBRE_ACTES
FROM 
    BORDEREAU_DE_TRANSMISSION A 
JOIN FS B ON A.NUMERO_BORDEREAU = B.NUM_BORDEREAU
JOIN FS_ACTE C ON B.FEUILLE = C.FEUILLE
AND A.CODE_ETS_BORDEREAU = ?
AND B.NUM_BORDEREAU = ?
AND B.FEUILLE = C.FEUILLE
ORDER BY A.DATE_REG DESC
        ');
        $a->execute(array($code_ets,$num_bordereau));
        $json = $a->fetch();
        return $json;
    }

    public function trouver_ogd($code_ogd) {
        $a = $this->bdd->prepare('SELECT CODE,LIBELLE FROM REF_OGD WHERE CODE = ?');
        $a->execute(array($code_ogd));
        $json = $a->fetch();
        return $json;
    }

    public function trouver_liste_facture_par_bordereau($num_bordereau) {
        $a = $this->bdd->prepare('
SELECT 
    A.NUM_SECU AS NUM_SECU, 
    A.FEUILLE AS NUM_FACTURE, 
    A.NUM_FS_INITIALE AS NUM_FS_INITIALE, 
    A.TYPE_FEUILLE AS TYPE_FACTURE,
    A.AFFECTION1 AS CODE_AFFECTION, 
    A.DATE_SOINS AS DATE_SOINS, 
    A.PS AS CODE_PS,
    SUM(B.MONTANT * B.QUANTITE) AS MONTANT,
    SUM(B.MONTANT * B.QUANTITE * 0.7) AS PART_CMU 
FROM 
    FS A JOIN FS_ACTE B ON A.FEUILLE = B.FEUILLE 
AND NUM_BORDEREAU = ?
GROUP BY 
    A.TYPE_FEUILLE,
    A.NUM_SECU, 
    A.FEUILLE, 
    A.NUM_FS_INITIALE, 
    A.TYPE_FEUILLE,
    A.AFFECTION1, 
    A.DATE_SOINS, 
    A.PS
ORDER BY A.DATE_SOINS DESC
        ');
        $a->execute(array($num_bordereau));
        $json = $a->fetchAll();
        return $json;
    }

    public function trouver_facture($code_ets,$num_facture) {
        $a = $this->bdd->prepare('SELECT * FROM fs WHERE ETABLISSEMENT LIKE ? AND FEUILLE = ?');
        $a->execute(array('%'.$code_ets.'%',$num_facture));
        $json = $a->fetch();
        return $json;
    }

    public function lister_factures_par_type_facture($ets,$date_debut,$date_fin,$ps){
        $a = $this->bdd->prepare('
        SELECT  
            A.TYPE_FEUILLE AS TYPE_FACTURE,
            COUNT(DISTINCT A.FEUILLE) AS NOMBRE,
            SUM(B.montant * B.quantite) AS MONTANT, 
            SUM(B.MONTANT * B.quantite * 0.7) AS PART_CMU
        FROM 
            FS A JOIN FS_ACTE B ON A.FEUILLE = B.FEUILLE  
        AND A.ETABLISSEMENT LIKE ? 
        AND (A.DATE_SOINS BETWEEN ? AND ?)
        AND A.PS LIKE ?
        AND A.STATUT != ?          
         
        GROUP BY A.TYPE_FEUILLE');
        $a->execute(array($ets,$date_debut,$date_fin,'%'.$ps.'%','A'));
        $json = $a->fetchAll();
        return $json;
    }

    public function lister_factures_par_statut($ets,$date_debut,$date_fin,$ps){
        $a = $this->bdd->prepare('
SELECT 
A.STATUT AS STATUT, 
COUNT(DISTINCT A.FEUILLE) AS NOMBRE, 
SUM(B.MONTANT * B.QUANTITE) AS MONTANT, 
SUM(B.MONTANT * B.QUANTITE * 0.7) AS PART_CMU 
FROM 
FS A JOIN FS_ACTE B ON A.FEUILLE = B.FEUILLE
AND A.ETABLISSEMENT LIKE ?             
AND (A.DATE_SOINS BETWEEN ? AND ?) 
AND A.PS LIKE ?
AND A.STATUT != ? 
GROUP BY A.STATUT
        ');
        $a->execute(array($ets,$date_debut,$date_fin,'%'.$ps.'%','A'));
        $json = $a->fetchAll();
        return $json;
    }

    public function trouver_facture_statut($statut) {
        $a = $this->bdd->prepare('SELECT STATUT_CODE AS CODE, STATUT_LIBELLE AS LIBELLE FROM ECMU_FACTURES_STATUT_DICTIONNAIRE WHERE STATUT_CODE = ?');
        $a->execute(array($statut));
        $json = $a->fetch();
        return $json;
    }

    public function lister_factures_par_ogd($ets,$date_debut,$date_fin,$ps){
        $a = $this->bdd->prepare('
SELECT 
    A.NUM_OGD AS CODE_OGD, 
    A.NOM_OGD AS LIBELLE_OGD, 
    COUNT(DISTINCT A.FEUILLE) AS NOMBRE, 
    SUM(B.MONTANT * B.QUANTITE) AS MONTANT, 
    SUM(B.MONTANT * B.QUANTITE * 0.7) AS PART_CMU 
FROM 
FS A JOIN FS_ACTE B ON A.FEUILLE = B.FEUILLE 
AND A.ETABLISSEMENT LIKE ? 
AND (A.DATE_SOINS BETWEEN ? AND ?) 
AND A.PS LIKE ? 
AND A.STATUT != ? 
GROUP BY A.NUM_OGD, A.NOM_OGD
');
        $a->execute(array($ets,$date_debut,$date_fin,'%'.$ps.'%','A'));
        $json = $a->fetchAll();
        return $json;
    }

    public function trouver_liste_factures($num_facture) {
        $a = $this->bdd->prepare('SELECT * FROM FS WHERE FEUILLE =? OR NUM_FS_INITIALE = ?');
        $a->execute(array($num_facture, $num_facture));
        $json = $a->fetchAll();
        return $json;
    }

    public function trouver_medicament_facture($num_facture) {
        $facture = $this->trouver_facture(NULL,$num_facture);
        if(empty($facture['FEUILLE'])) {
            $json = array(
                'status' => 'failed',
                'message' => "Le n° de la feuille de soins saisi est incorrect."
            );
        }else {
            if($facture['TYPE_FEUILLE'] == 'AMB' || $facture['TYPE_FEUILLE'] == 'DEN' || empty($facture['TYPE_FEUILLE'])) {
                if($facture['STATUT'] == 'A') {
                    $json = array(
                        'status' => 'failed',
                        'message' => "Cette feuille de soins a été annulé et ne peut donc être utilisée pour éditer une facture de médicaments."
                    );
                }else {
                    $a = $this->bdd->prepare('
                    SELECT FEUILLE FROM FS WHERE NUM_FS_INITIALE = ? AND TYPE_FEUILLE = ? AND STATUT != ?');
                    $a->execute(array($facture['FEUILLE'],'MED','A'));
                    $facture_existe = count($a->fetchAll());
                    if($facture_existe == 0) {
                        $json = $facture;
                    }else {
                        $json = array(
                            'status' => 'failed',
                            'message' => "Des médicaments ont déjà été servis pour cette feuille de soins."
                        );
                    }
                }
            }else {
                $json = array(
                    'status' => 'failed',
                    'message' => "Cette feuille de soins ne peut être utilisée pour éditer des médicaments."
                );
            }
        }

        return $json;
    }

    public function verifier_num_fs_initiale($num_fs,$num_secu,$date_soins) {
        $a = $this->bdd->prepare('SELECT * FROM FS WHERE FEUILLE = ? AND NUM_SECU = ? AND (TYPE_FEUILLE = ? OR TYPE_FEUILLE = ? OR TYPE_FEUILLE IS NULL)');
        $a->execute(array($num_fs,$num_secu,'AMB','DEN'));
        $facture = $a->fetch();
        if(empty($facture['FEUILLE'])) {
            $json = array(
                'status' => false,
                'message' => "LE N° DE FEUILLE DE SOINS RENSEIGNÉ EST INCORRECT"
            );
        }else {

            if($facture['STATUT'] == 'A') {
                $json = array(
                    'status' => false,
                    'message' => "LE N° DE FEUILLE DE SOINS RENSEIGNÉ A ÉTÉ ANNULÉ, IL NE PEUT DONC SERVIR À EDITER CETTE FACTURE."
                );
            }elseif($facture['STATUT'] == 'R') {
                $json = array(
                    'status' => false,
                    'message' => "LE N° DE FEUILLE DE SOINS RENSEIGNÉ A ÉTÉ REJETÉ, IL NE PEUT DONC SERVIR À EDITER CETTE FACTURE."
                );
            }else {
                $now = strtotime($date_soins);
                $your_date = strtotime($facture['DATE_SOINS']);
                if($now < $your_date) {
                    $json = array(
                        'status' => false,
                        'message' => "LA DATE DE SOINS DE LA FACTURE ACTUELLE EST INFÉRIEURE À CELLE DE LA FACTURE INITIALE."
                    );
                }else {
                    $datediff = $now - $your_date;
                    $validite = ($datediff / (60 * 60 * 24));

                    if($validite > 15) {
                        $json = array(
                            'status' => false,
                            'message' => "LA FEUILLE DE SOINS A EXPIRÉ. VEUILLEZ CHOISIR UN AUTRE NUMÉRO."
                        );
                    }else {
                        if(empty($facture['TYPE_FEUILLE'])) {
                            $b = $this->getBdd()->prepare('UPDATE FS SET TYPE_FEUILLE = ? WHERE FEUILLE = ?');
                            $b->execute(array('AMB',$facture['FEUILLE']));
                        }
						$facture['status'] = true;
                        $json = $facture;
                        /*$json = array(
                            'status' => true
                        );*/
                    }
                }
            }
        }
        return $json;
    }

     public function verifier_entente_prealable($num_ep_cnam, $num_secu, $date_soins) {
        $a = $this->bdd->prepare('SELECT DISTINCT(NUM_ENTENTE_PREALABLE),TYPE_EP,NUM_FS_INITIALE,DATE_VALIDATION,STATUT,TO_CHAR(MOTIF) AS "MOTIF" FROM DEMANDE_ENTENTE_PREALABLE WHERE NUM_ENTENTE_PREALABLE = ? AND NUM_SECU = ? AND ROWNUM = 1');
        $a->execute(array($num_ep_cnam,$num_secu));
        $entente_prealable = $a->fetch();
        //$json = array();

        if(empty($entente_prealable['NUM_ENTENTE_PREALABLE'])) {
            $json = array(
                'status' => false,
                'message' => "N° D'ENTENTE PREALABLE INCORRECT"
            );
        }else {
            $date = date('Y-m-d',time());

            $f = $this->bdd->prepare('SELECT NUM_EP_CNAM FROM FS WHERE NUM_EP_CNAM = ? AND STATUT != ?');
            $f->execute(array($num_ep_cnam,'A'));
            $num_deja_utilise = $f->rowCount();
            if($num_deja_utilise == 0) {
                if(empty($entente_prealable['STATUT'])) {
                    $json = array(
                        'status' => false,
                        'message' => "CETTE DEMANDE EST EN ATTENTE DE VALIDATION. VEUILLEZ CONTACTER LE SUPPORT SI LA VALIDATION MET DU TEMPS ".$num_deja_utilise
                    );
                }elseif($entente_prealable['STATUT'] == 1) {
                    $today = strtotime($date); // or your date as well
                    $date_validation = strtotime($entente_prealable['DATE_VALIDATION']);
                    $datediff = $today - $date_validation;
                    $validite = intval(($datediff / (60 * 60 * 24)));

                    if($validite <= 1460) {
                        if($entente_prealable['TYPE_EP']=='HOS'){
                            $json = array(
                                'status' => true,
                                'type_facture' => $entente_prealable['TYPE_EP'],
                                'fs_initiale' => $entente_prealable['NUM_FS_INITIALE'],
                                'message' => "CETTE DEMANDE EST VALIDE."
                            );
                        }else{
                            $b = $this->bdd->prepare('SELECT CODE_ACTE_MEDICAL FROM DEMANDE_ENTENTE_PREALABLE WHERE NUM_ENTENTE_PREALABLE = ? AND STATUT = ?');
                            $b->execute(array(trim($entente_prealable['NUM_ENTENTE_PREALABLE']),1));
                            $actes_ep = $b->fetchAll();
                            foreach ($actes_ep as $code_acte) {
                                $c = $this->bdd->prepare('SELECT * FROM ECMU_REF_FS_ACTES_MEDICAUX WHERE CODE = ? AND PANIER = ? AND DATE_FIN IS NULL');
                                $c->execute(array(trim($code_acte['CODE_ACTE_MEDICAL']),1));
                                $acte = $c->fetch();
                                if($acte['TYPE_ACTE'] == 'NGAP') {
                                    $l = $this->bdd->prepare('SELECT CODE,PRIX_UNITAIRE FROM ECMU_REF_FS_LETTRE_CLE WHERE CODE = ? AND FIN_VALIDITE IS NULL');
                                    $l->execute(array(trim($acte['LETTRE_CLE'])));
                                    $lettres_cle = $l->fetch();

                                    if(!empty($acte['LETTRE_CLE_2'])){
                                        $e = $this->bdd->prepare('SELECT CODE,PRIX_UNITAIRE FROM ECMU_REF_FS_LETTRE_CLE WHERE CODE = ? AND FIN_VALIDITE IS NULL');
                                        $e->execute(array(trim($acte['LETTRE_CLE_2'])));
                                        $lettres_cle_2 = $e->fetch();
                                    }else{
                                        $lettres_cle_2 ['PRIX_UNITAIRE'] = 0;
                                        $acte['COEFFICIENT_2']= 0;
                                    }

                                    $tarif = ($lettres_cle['PRIX_UNITAIRE'] * $acte['COEFFICIENT'])+($lettres_cle_2['PRIX_UNITAIRE'] * $acte['COEFFICIENT_2']);

                                    $actes[] = array(
                                        'code' => $acte['CODE'],
                                        'label' => $acte['LIBELLE'],
                                        'date_debut' => date('d/m/Y',strtotime($date)),
                                        'tarif' => $tarif,
                                        'ep' => $acte['ENTENTE_PREALABLE']
                                    );
                                }else {
                                    $actes[] = array(
                                        'code' => $acte['CODE'],
                                        'label' => $acte['LIBELLE'],
                                        'date_debut' => date('d/m/Y',strtotime($date)),
                                        'tarif' => $acte['TARIF'],
                                        'ep' => $acte['ENTENTE_PREALABLE']
                                    );
                                }

                            }
							
                            $json = array(
                                'status' => true,
                                'type_facture' => $entente_prealable['TYPE_EP'],
                                'actes'=>$actes
                            );
                        }
                    }else {
                        $json = array(
                            'status' => false,
                            'message' => "CETTE DEMANDE A EXPIRE DEPUIS ".($validite - 1460)." JOURS"
                        );
                    }

                }elseif($entente_prealable['STATUT'] == 2) {
                    $json = array(
                        'status' => false,
                        'message' => "CETTE DEMANDE A ETE REFUSEE POUR MOTIF DE: ".$entente_prealable['MOTIF']
                    );
                }else {
                    $json = array(
                        'status' => false,
                        'message' => "CETTE DEMANDE A EXPIRE"
                    );
                }
            }else {
                $json = array(
                    'status' => false,
                    'message' => "CE NUMERO EP A DEJA ETE UTILISE"
                );
            }

        }
        return $json;
    }
	
    public function verifier_facture_ps($code_ps, $nom_ps, $code_ets, $date_soins) {
        if(!empty($code_ps)) {
            $query = '
            SELECT 
                A.INP AS CODE_PS,
                A.NOM,
                A.PRENOM,
                A.LIBELLE_SPECIALITE AS CODE_SPECIALITE 
            FROM 
                ECMU_REF_PROFESSIONNEL_SANTE A
            JOIN PS_ETS B ON 
                A.INP = B.PS 
                AND B.CODE_ETS = ? 
                AND B.STATUT = ? 
                AND A.INP = ? 
                AND A.DATE_DEBUT_VALIDITE <= ? 
                AND (A.DATE_FIN_VALIDITE >= ? OR A.DATE_FIN_VALIDITE IS NULL)
            ';
            $a = $this->bdd->prepare($query);
            $a->execute(array($code_ets,1,$code_ps,$date_soins,$date_soins));
            $ps = $a->fetch();
            if(!empty($ps['CODE_PS'])) {


                $b = $this->bdd->prepare('SELECT LIBELLE FROM ECMU_REF_SPECIALITES_MEDICALES WHERE CODE = ?');
                $b->execute(array($ps['CODE_SPECIALITE']));
                $specialite = $b->fetch();

                $json = array(
                    'status' => true,
                    'code_ps' => $ps['CODE_PS'],
                    'nom_prenom' => $ps['NOM'].' '.$ps['PRENOM'],
                    'code_specialite' => $ps['CODE_SPECIALITE'],
                    'libelle_specialite' => $specialite['LIBELLE']
                );

            }else {
                $json = array(
                    'status' => false,
                    'message' => 'LE CODE PS: '.$code_ps.' EST INCORRECT POUR LA DATE ET L\'ÉTABLISSEMENT SÉLECTIONNÉS, VEUILLEZ REÉSSAYER SVP.'
                );
            }
        }else {
            $query = "
            SELECT 
            A.INP AS CODE_PS,
            A.NOM,
            A.PRENOM,
            A.LIBELLE_SPECIALITE AS CODE_SPECIALITE 
            FROM 
            ECMU_REF_PROFESSIONNEL_SANTE A
            JOIN PS_ETS B ON 
            A.INP = B.PS 
            AND B.CODE_ETS = ? 
            AND B.STATUT = ? 
            AND (A.NOM ||  ' ' || A.PRENOM) LIKE ? 
            AND A.DATE_DEBUT_VALIDITE <= TO_DATE(?) 
            AND (A.DATE_FIN_VALIDITE >= TO_DATE(?) OR A.DATE_FIN_VALIDITE IS NULL)
            ";
            $pr = $this->bdd->prepare($query);
            $pr->execute(array($code_ets,1,'%'.$nom_ps.'%',$date_soins,$date_soins));
            $professionnels = $pr->fetchAll();
            foreach($professionnels as $ps) {
                $b = $this->bdd->prepare('SELECT LIBELLE FROM ECMU_REF_SPECIALITES_MEDICALES WHERE CODE = ?');
                $b->execute(array($ps['CODE_SPECIALITE']));
                $specialite = $b->fetch();
                $json[] = array(
                    'code'=>$ps['CODE_PS'],
                    'label' => $ps['NOM'].' '.$ps['PRENOM'],
                    'value' => $ps['NOM'].' '.$ps['PRENOM'],
                    'code_specialite'=>$ps['CODE_SPECIALITE'],
                    'libelle_specialite'=>$specialite['LIBELLE']
                );
            }
        }
        return $json;
    }

    public function trouver_affection($code,$statut) {
        $a = $this->bdd->prepare('SELECT CODE,LIBELLE FROM ECMU_REF_FS_AFFECTIONS WHERE CODE = ? AND PANIER LIKE ?');
        $a->execute(array($code,'%'.$statut.'%'));
        $json = $a->fetch();
        return $json;
    }

    public function trouver_acte($type_acte, $code_ets, $code_acte, $libelle_acte, $date_soins) {
        if($type_acte == 'MED') {
            if(!empty($code_acte)) {
                $b = $this->bdd->prepare('SELECT RESEAU_ID,CODE_ETS FROM ECMU_RESEAUX_ETS WHERE CODE_ETS = ? AND RESEAU_STATUT = ?');
                $b->execute(array($code_ets,1));
                $reseau = $b->fetch();
                if(!empty($reseau['RESEAU_ID'])) {
                    if ($reseau['RESEAU_ID'] == 12) {
                        $c = $this->bdd->prepare('SELECT TARIF FROM ECMU_RESEAUX_MEDICAMENTS WHERE RESEAU_ID = ? AND CODE_MEDICAMENT = ? AND DATE_DEBUT_VALIDITE <= TO_DATE(?) AND (DATE_FIN_VALIDITE >= TO_DATE(?) OR DATE_FIN_VALIDITE IS NULL)');
                        $c->execute(array($reseau['RESEAU_ID'], $code_acte, $date_soins, $date_soins));
                        $medicament_reseau = $c->fetch();
                        if ($medicament_reseau) {
                            $a = $this->bdd->prepare('SELECT CODE, EAN13, LIBELLE, DCI1, PP, DOSAGE1, UNITE1 FROM ECMU_REF_FS_MEDICAMENTS WHERE CODE = ? AND PANIER = ?  AND DATE_DEBUT_VALIDITE <= ? AND (DATE_FIN_VALIDITE >= ? OR DATE_FIN_VALIDITE IS NULL) ORDER BY LIBELLE ASC');
                            $a->execute(array($code_acte, 1, $date_soins, $date_soins));
                            $acte = $a->fetch();
                            if(empty($acte['CODE'])) {
                                $json = array(
                                    'status' => false,
                                    'message' => "LE CODE " . $code_acte . " EST INCORRECT. "
                                );
                            }
                            else{
                                $json = array(
                                    'status' => true,
                                    'code_acte' => $acte["CODE"],
                                    'code_ean13' => $acte["EAN13"],
                                    'label' => $acte['LIBELLE'],
                                    'value' => $acte['LIBELLE'],
                                    'libelle' => $acte['LIBELLE'],
                                    'date_debut' => date('d/m/Y', strtotime($date_soins)),
                                    'date_fin' => date('d/m/Y', strtotime($date_soins)),
                                    'tarif' => $medicament_reseau['TARIF']
                                );
                            }
                        }
                        else{
                            $json = array(
                                'status' => false,
                                'message' => "LE CODE " . $code_acte . " EST INCORRECT. "
                            );
                        }
                    }
                    else{
                        $a = $this->bdd->prepare('SELECT CODE, EAN13, LIBELLE, DCI1, PP, DOSAGE1, UNITE1 FROM ECMU_REF_FS_MEDICAMENTS WHERE CODE = ? AND PANIER = ?  AND DATE_DEBUT_VALIDITE <= ? AND (DATE_FIN_VALIDITE >= ? OR DATE_FIN_VALIDITE IS NULL) AND CODE NOT IN (SELECT CODE_MEDICAMENT FROM ECMU_RESEAUX_MEDICAMENTS WHERE RESEAU_ID = ?) ORDER BY LIBELLE ASC');
                        $a->execute(array($code_acte, 1, $date_soins, $date_soins, 12));
                        $acte = $a->fetch();
                        if(empty($acte['CODE'])) {
                            $json = array(
                                'status' => false,
                                'message' => "LE CODE " . $code_acte . " EST INCORRECT. "
                            );
                        }
                        else{
                            $json = array(
                                'status' => true,
                                'code_acte' => $acte["CODE"],
                                'code_ean13' => $acte["EAN13"],
                                'label' => $acte['LIBELLE'],
                                'value' => $acte['LIBELLE'],
                                'libelle' => $acte['LIBELLE'],
                                'date_debut' => date('d/m/Y', strtotime($date_soins)),
                                'date_fin' => date('d/m/Y', strtotime($date_soins)),
                                'tarif' => $acte['PP']
                            );
                        }
                    }
                }
                else {
                    $a = $this->bdd->prepare('SELECT CODE, EAN13, LIBELLE, DCI1, PP, DOSAGE1, UNITE1 FROM ECMU_REF_FS_MEDICAMENTS WHERE CODE = ? AND PANIER = ?  AND DATE_DEBUT_VALIDITE <= ? AND (DATE_FIN_VALIDITE >= ? OR DATE_FIN_VALIDITE IS NULL) ORDER BY LIBELLE ASC');
                    $a->execute(array($code_acte, 1, $date_soins, $date_soins));
                    $acte = $a->fetch();
                    if(empty($acte['CODE'])) {
                        $json = array(
                            'status' => false,
                            'message' => "LE CODE " . $code_acte . " EST INCORRECT. "
                        );
                    }
                    else{
                        $json = array(
                            'status' => true,
                            'code_acte' => $acte["CODE"],
                            'code_ean13' => $acte["EAN13"],
                            'label' => $acte['LIBELLE'],
                            'value' => $acte['LIBELLE'],
                            'libelle' => $acte['LIBELLE'],
                            'date_debut' => date('d/m/Y', strtotime($date_soins)),
                            'date_fin' => date('d/m/Y', strtotime($date_soins)),
                            'tarif' => $acte['PP']
                        );
                    }
                }
            }
            else {
                $b = $this->bdd->prepare('SELECT RESEAU_ID,CODE_ETS FROM ECMU_RESEAUX_ETS WHERE CODE_ETS = ? AND RESEAU_STATUT = ?');
                $b->execute(array($code_ets,1));
                $reseau = $b->fetch();
                if(!empty($reseau['RESEAU_ID'])) {
                    if($reseau['RESEAU_ID']==12) {
                        $a = $this->bdd->prepare('SELECT CODE, EAN13, LIBELLE, DCI1, PP, DOSAGE1, UNITE1 FROM ECMU_REF_FS_MEDICAMENTS WHERE LIBELLE LIKE ? AND PANIER = ?  AND DATE_DEBUT_VALIDITE <= TO_DATE(?) AND (DATE_FIN_VALIDITE >= TO_DATE(?) OR DATE_FIN_VALIDITE IS NULL) ORDER BY LIBELLE ASC');
                        $a->execute(array('%'.$libelle_acte.'%',1,$date_soins,$date_soins));
                        $actes = $a->fetchAll();
                        if(count($actes) != 0) {
                            foreach ($actes as $acte) {
                                $c = $this->bdd->prepare('SELECT TARIF FROM ECMU_RESEAUX_MEDICAMENTS WHERE RESEAU_ID = ? AND CODE_MEDICAMENT = ? AND DATE_DEBUT_VALIDITE <= TO_DATE(?) AND (DATE_FIN_VALIDITE >= TO_DATE(?) OR DATE_FIN_VALIDITE IS NULL)');
                                $c->execute(array($reseau['RESEAU_ID'], $acte['CODE'], $date_soins, $date_soins));
                                $medicament_reseau = $c->fetch();
                                if ($medicament_reseau) {
                                    $json[] = array(
                                        'status' => true,
                                        'code_acte' => $acte["CODE"],
                                        'code_ean13' => $acte["EAN13"],
                                        'label' => $acte['LIBELLE'],
                                        'value' => $acte['LIBELLE'],
                                        'libelle' => $acte['LIBELLE'],
                                        'date_debut' => date('d/m/Y', strtotime($date_soins)),
                                        'date_fin' => date('d/m/Y', strtotime($date_soins)),
                                        'tarif' => $medicament_reseau['TARIF']
                                    );
                                }
                            }
                        }
                    }
                    else{
                        $am = $this->bdd->prepare('SELECT CODE, EAN13, LIBELLE, DCI1, PP, DOSAGE1, UNITE1 FROM ECMU_REF_FS_MEDICAMENTS WHERE LIBELLE LIKE ? AND PANIER = ?  AND DATE_DEBUT_VALIDITE <= TO_DATE(?) AND (DATE_FIN_VALIDITE >= TO_DATE(?) OR DATE_FIN_VALIDITE IS NULL) AND CODE NOT IN (SELECT CODE_MEDICAMENT FROM ECMU_RESEAUX_MEDICAMENTS WHERE RESEAU_ID = ?) ORDER BY LIBELLE ASC');
                        $am->execute(array('%'.$libelle_acte.'%',1,$date_soins,$date_soins, 12));
                        $actes = $am->fetchAll();
                        foreach ($actes AS $acte) {
                            $json[] = array(
                                'status' => true,
                                'code_acte' => $acte["CODE"],
                                'code_ean13' => $acte["EAN13"],
                                'label' => $acte['LIBELLE'],
                                'value' => $acte['LIBELLE'],
                                'libelle' => $acte['LIBELLE'],
                                'date_debut' => date('d/m/Y',strtotime($date_soins)),
                                'date_fin' => date('d/m/Y',strtotime($date_soins)),
                                'tarif' => $acte['PP']
                            );
                        }
                    }
                }
                else {
                    $an = $this->bdd->prepare('SELECT CODE, EAN13, LIBELLE, DCI1, PP, DOSAGE1, UNITE1 FROM ECMU_REF_FS_MEDICAMENTS WHERE LIBELLE LIKE ? AND PANIER = ?  AND DATE_DEBUT_VALIDITE <= TO_DATE(?) AND (DATE_FIN_VALIDITE >= TO_DATE(?) OR DATE_FIN_VALIDITE IS NULL) ORDER BY LIBELLE ASC');
                    $an->execute(array('%'.$libelle_acte.'%',1,$date_soins,$date_soins));
                    $actes = $an->fetchAll();
                    foreach ($actes AS $acte) {
                        $json[] = array(
                            'status' => true,
                            'code_acte' => $acte["CODE"],
                            'code_ean13' => $acte["EAN13"],
                            'label' => $acte['LIBELLE'],
                            'value' => $acte['LIBELLE'],
                            'libelle' => $acte['LIBELLE'],
                            'date_debut' => date('d/m/Y',strtotime($date_soins)),
                            'date_fin' => date('d/m/Y',strtotime($date_soins)),
                            'tarif' => $acte['PP']
                        );
                    }
                }
            }
        }else {
            if(!empty($code_acte)){
                $a = $this->bdd->prepare('SELECT CODE,TYPE_ACTE,UPPER(LIBELLE) AS LIBELLE,COEFFICIENT,LETTRE_CLE,ENTENTE_PREALABLE,TARIF, DATE_EFFET, DATE_FIN FROM ECMU_REF_FS_ACTES_MEDICAUX WHERE CODE = ? AND PANIER = ? AND DATE_EFFET <= TO_DATE(?) AND (DATE_FIN >= TO_DATE(?) OR DATE_FIN IS NULL)');
                $a->execute(array($code_acte,1,$date_soins,$date_soins));
                $acte = $a->fetch();

                if(empty($acte['CODE'])) {
                    $json = array(
                        'status' => false,
                        'message' => "LE CODE ".$code_acte." EST INCORRECT."
                    );
                }else {
                    if($acte['ENTENTE_PREALABLE'] == 1) {
                        $json = array(
                            'status' => false,
                            'message' => "CET ACTE EST SOUMIS À ENTENTE PRÉALABLE, VEUILLEZ FAIRE UNE DEMANDE À L'OGD."
                        );
                    }else {

                        $b = $this->bdd->prepare('SELECT RESEAU_ID,CODE_ETS FROM ECMU_RESEAUX_ETS WHERE CODE_ETS = ? AND RESEAU_STATUT = ?');
                        $b->execute(array($code_ets,1));
                        $reseau = $b->fetch();
                        if(empty($reseau['RESEAU_ID'])) {
                            if($acte['TYPE_ACTE'] == 'NGAP') {
                                $c = $this->bdd->prepare('SELECT CODE,LIBELLE,PRIX_UNITAIRE FROM ECMU_REF_FS_LETTRE_CLE WHERE CODE = ?');
                                $c->execute(array($acte['LETTRE_CLE']));
                                $lettre_cle = $c->fetch();
                                $tarif = ($acte['COEFFICIENT'] * $lettre_cle['PRIX_UNITAIRE']);
                            }else {
                                $tarif = $acte['TARIF'];
                            }

                            $json = array(
                                'status' => true,
                                'code_acte' => $acte['CODE'],
                                'libelle' => $acte['LIBELLE'],
                                'date_debut' => date('d/m/Y',strtotime($date_soins)),
                                'date_fin' => date('d/m/Y',strtotime($date_soins)),
                                'tarif' => $tarif,
                                'ep' => $acte['ENTENTE_PREALABLE']
                            );
                        }else {
                            $c = $this->bdd->prepare('SELECT TARIF FROM ECMU_RESEAUX_ACTES_MEDICAUX WHERE RESEAU_ID = ? AND CODE_ACTE = ? AND DATE_DEBUT_VALIDITE <= ? AND (DATE_FIN_VALIDITE >= ? OR DATE_FIN_VALIDITE IS NULL)');
                            $c->execute(array($reseau['RESEAU_ID'], $acte['CODE'],$date_soins,$date_soins));
                            $acte_reseau = $c->fetch();
                            if(!$acte_reseau) {
                                if($acte['TYPE_ACTE'] == 'NGAP') {
                                    $c = $this->bdd->prepare('SELECT CODE,LIBELLE,PRIX_UNITAIRE FROM ECMU_REF_FS_LETTRE_CLE WHERE CODE = ? AND DEBUT_VALIDITE <= ? AND (FIN_VALIDITE >= ? OR FIN_VALIDITE IS NULL)');
                                    $c->execute(array(trim($acte['LETTRE_CLE']),$date_soins,$date_soins));
                                    $lettre_cle = $c->fetch();
                                    $tarif = ($acte['COEFFICIENT'] * $lettre_cle['PRIX_UNITAIRE']);
                                }else {
                                    $tarif = $acte['TARIF'];
                                }

                                $json = array(
                                    'status' => true,
                                    'code_acte' => $acte['CODE'],
                                    'libelle' => $acte['LIBELLE'],
                                    'date_debut' => date('d/m/Y',strtotime($date_soins)),
                                    'date_fin' => date('d/m/Y',strtotime($date_soins)),
                                    'tarif' => $tarif
                                );
                            }else {
                                if(empty($acte_reseau['TARIF']) || $acte_reseau['TARIF'] == 0) {
                                    $tarif = 0;
                                }else {
                                    $tarif = $acte_reseau['TARIF'];
                                }
                                $json = array(
                                    'status' => true,
                                    'code_acte' => $acte['CODE'],
                                    'libelle' => $acte['LIBELLE'],
                                    'date_debut' => date('d/m/Y',strtotime($date_soins)),
                                    'date_fin' => date('d/m/Y',strtotime($date_soins)),
                                    'tarif' => $tarif
                                );
                            }
                        }
                    }
                }
            }else{
                $a = $this->bdd->prepare('SELECT CODE,TYPE_ACTE,UPPER(LIBELLE) AS LIBELLE,COEFFICIENT,LETTRE_CLE,ENTENTE_PREALABLE,TARIF, DATE_EFFET, DATE_FIN  FROM ECMU_REF_FS_ACTES_MEDICAUX WHERE UPPER(LIBELLE) LIKE ? AND PANIER = ? AND DATE_EFFET <= TO_DATE(?) AND (DATE_FIN >= TO_DATE(?) OR DATE_FIN IS NULL)');
                $a->execute(array('%'.$libelle_acte.'%', 1,$date_soins,$date_soins));
                $actes = $a->fetchAll();
                
                if(count($actes) != 0){
                    foreach ($actes as $acte) {
                        if($acte['ENTENTE_PREALABLE'] != 1) {
                            $b = $this->bdd->prepare('SELECT RESEAU_ID,CODE_ETS FROM ECMU_RESEAUX_ETS WHERE CODE_ETS = ? AND RESEAU_STATUT = ?');
                            $b->execute(array($code_ets,1));
                            $reseau = $b->fetch();
                            if(empty($reseau['RESEAU_ID'])) {
                                if($acte['TYPE_ACTE'] == 'NGAP') {
                                    $c = $this->bdd->prepare('SELECT CODE,LIBELLE,PRIX_UNITAIRE FROM ECMU_REF_FS_LETTRE_CLE WHERE CODE = ?');
                                    $c->execute(array($acte['LETTRE_CLE']));
                                    $lettre_cle = $c->fetch();
                                    $tarif = ($acte['COEFFICIENT'] * $lettre_cle['PRIX_UNITAIRE']);
                                }else {
                                    $tarif = $acte['TARIF'];
                                }

                                $json[] = array(
                                    'status' => true,
                                    'code_acte' => $acte['CODE'],
                                    'label' => $acte['LIBELLE'],
                                    'value' => $acte['LIBELLE'],
                                    'libelle' => $acte['LIBELLE'],
                                    'date_debut' => date('d/m/Y',strtotime($date_soins)),
                                    'date_fin' => date('d/m/Y',strtotime($date_soins)),
                                    'tarif' => $tarif,
                                    'ep' => $acte['ENTENTE_PREALABLE']
                                );
                            }else {
                                $c = $this->bdd->prepare('SELECT TARIF FROM ECMU_RESEAUX_ACTES_MEDICAUX WHERE RESEAU_ID = ? AND CODE_ACTE = ? AND DATE_DEBUT_VALIDITE <= TO_DATE(?) AND (DATE_FIN_VALIDITE >= TO_DATE(?) OR DATE_FIN_VALIDITE IS NULL)');
                                $c->execute(array($reseau['RESEAU_ID'], $acte['CODE'],$date_soins,$date_soins));
                                $acte_reseau = $c->fetch();
                                if(!$acte_reseau) {
                                    if($acte['TYPE_ACTE'] == 'NGAP') {
                                        $c = $this->bdd->prepare('SELECT CODE,LIBELLE,PRIX_UNITAIRE FROM ECMU_REF_FS_LETTRE_CLE WHERE CODE = ? AND DEBUT_VALIDITE <= TO_DATE(?) AND (FIN_VALIDITE >= TO_DATE(?) OR FIN_VALIDITE IS NULL)');
                                        $c->execute(array(trim($acte['LETTRE_CLE']),$date_soins,$date_soins));
                                        $lettre_cle = $c->fetch();
                                        $tarif = ($acte['COEFFICIENT'] * $lettre_cle['PRIX_UNITAIRE']);
                                    }else {
                                        $tarif = $acte['TARIF'].'1';
                                    }

                                    $json[] = array(
                                        'status' => true,
                                        'code_acte' => $acte['CODE'],
                                        'label' => $acte['LIBELLE'],
                                        'value' => $acte['LIBELLE'],
                                        'libelle' => $acte['LIBELLE'],
                                        'date_debut' => date('d/m/Y',strtotime($date_soins)),
                                        'date_fin' => date('d/m/Y',strtotime($date_soins)),
                                        'tarif' => $tarif
                                    );
                                }else {
                                    if(empty($acte_reseau['TARIF']) || $acte_reseau['TARIF'] == 0) {
                                        $tarif = 0;
                                    }else {
                                        $tarif = $acte_reseau['TARIF'];
                                    }
                                    $json[] = array(
                                        'status' => false,
                                        'code_acte' => $acte['CODE'],
                                        'label' => $acte['LIBELLE'],
                                        'value' => $acte['LIBELLE'],
                                        'libelle' => $acte['LIBELLE'],
                                        'date_debut' => date('d/m/Y',strtotime($date_soins)),
                                        'date_fin' => date('d/m/Y',strtotime($date_soins)),
                                        'tarif' => $tarif
                                    );
                                }
                            }
                        }
                    }
                }else{
                    $json[] = array();
                }

            }

        }
        return $json;
    }

    public function annuler_facture($num_facture,$motif_annulation,$user) {
        $a = $this->bdd->prepare('UPDATE fs SET STATUT = ?, MOTIF_ANNULATION = ?, DATE_EDIT = SYSDATE, USER_EDIT = ? WHERE FEUILLE = ?');
        $a->execute(array('A',$motif_annulation,$user,$num_facture))OR DIE('Erreur');
        $json = array(
            'status' => true,
            'message' => 'LA FACTURE A ETE ANNULEE AVEC SUCCES.'
        );
        return $json;
    }

    public function dernieres_consommations_assure($num_secu){
        $a = $this->bdd->prepare('SELECT * FROM (SELECT DISTINCT(FS_ACTE.FEUILLE), FS.NOM_ETS, FS.DATE_SOINS, FS.TYPE_FEUILLE,FS.STATUT, SUM(FS_ACTE.MONTANT * FS_ACTE.QUANTITE) AS MONTANT, SUM(FS_ACTE.MONTANT * FS_ACTE.QUANTITE * 0.7) AS PART_CMU, SUM(FS_ACTE.MONTANT * FS_ACTE.QUANTITE * 0.3) AS PART_ASSURE FROM FS,FS_ACTE WHERE FS.FEUILLE = FS_ACTE.FEUILLE AND NUM_SECU = ? GROUP BY FS_ACTE.FEUILLE,FS.NOM_ETS, FS.DATE_SOINS, FS.TYPE_FEUILLE,FS.STATUT ORDER BY FS.DATE_SOINS DESC) WHERE ROWNUM <=3');
        $a->execute(array($num_secu));
        $json = $a->fetchAll();
        return $json;
    }

    public function consommations_assure($num_secu){
        $a = $this->bdd->prepare('SELECT DISTINCT(FS_ACTE.FEUILLE), FS.NOM_ETS, FS.DATE_SOINS, FS.TYPE_FEUILLE,FS.CODE_CSP,FS.STATUT, SUM(FS_ACTE.MONTANT * FS_ACTE.QUANTITE) AS MONTANT, SUM(FS_ACTE.MONTANT * FS_ACTE.QUANTITE * 0.7) AS PART_CMU, SUM(FS_ACTE.MONTANT * FS_ACTE.QUANTITE * 0.3) AS PART_ASSURE FROM FS,FS_ACTE WHERE FS.FEUILLE = FS_ACTE.FEUILLE AND NUM_SECU = ? GROUP BY FS_ACTE.FEUILLE,FS.NOM_ETS, FS.DATE_SOINS, FS.TYPE_FEUILLE,FS.CODE_CSP,FS.STATUT ORDER BY FS.DATE_SOINS DESC');
        $a->execute(array($num_secu));
        $json = $a->fetchAll();
        return $json;
    }
	
	    public function trouver_facture_verification_deca($numero_facture,$code_ogd,$statut_feuille,$statut_bordereau){
        $a = $this->bdd->prepare("SELECT * FROM FS WHERE (FEUILLE = ? OR NUM_FS_INITIALE = ?) AND NUM_OGD = ? AND STATUT = ? AND STATUT_BORDEREAU = ?");
        $a->execute(array($numero_facture,$numero_facture,$code_ogd,$statut_feuille,$statut_bordereau)) OR DIE($this->bdd->errorInfo());
        $json = $a->fetchAll();
        return $json;
    }

    //// Fonction qui permet de Vérifier Les Factures Transmise dans Le DECA
    public function validation_verification_facture_deca($num_facture,$user){
        $a = $this->bdd->prepare('UPDATE FS SET STATUT_BORDEREAU = ?, DATE_EDIT = SYSDATE, USER_EDIT = ? WHERE FEUILLE = ?');
        $a->execute(array(1,$user,$num_facture));
        $json =array(
            'status' => true,
            'message' => "LA VERIFICATION DE LA FACTURE N°".$num_facture." S'EST TERMINEE AVEC SUCCES."
        );
        return $json;
    }

    //// Fonction qui permet de Vérifier Les Factures Transmise dans Le DECA
    public function liquidation_facture($statut,$num_facture,$user) {
        $a = $this->bdd->prepare('UPDATE FS SET STATUT = ?, DATE_EDIT = SYSDATE, USER_EDIT = ? WHERE FEUILLE = ?');
        $a->execute(array($statut, $user, $num_facture));
        $json =array(
            'status' => true,
            'message' => "LA LIQUIDATION DE LA FACTURE N°".$num_facture." S'EST TERMINEE AVEC SUCCES."
        );
        return $json;
    }

    public function motif_rejet_liquidation_facture($num_facture,$motif,$code_acte,$user) {
        $a = $this->bdd->prepare('UPDATE FS_ACTE SET MOTIF_REJET = ?, DATE_EDIT = SYSDATE, USER_EDIT = ? WHERE FEUILLE = ? AND UPPER(CODE) = UPPER(?)');
        $a->execute(array($motif, $user, $num_facture, $code_acte));
        $json =array(
            'status' => true
        );
        return $json;
    }

    public function insert_verification_facture($num_facture, $user){
        $a = $this->bdd->prepare('INSERT INTO FS_VERIFICATION_LIQUIDATION(FEUILLE, USER_REG) VALUES( :FEUILLE, :USER_REG)');
        $a->execute(array(
            'FEUILLE' => $num_facture,
            'USER_REG' => $user
        ))OR DIE($this->bdd->errorInfo());
        $json = array(
            'status' => true,'message' => 'ENREGISTREMENT EFFECTUE AVEC SUCCES.'
        );
        return $json;
    }
	
    public function trouver_facture_verification($numero_facture,$statut_bordereau){
        $a = $this->bdd->prepare("SELECT * FROM FS WHERE (FEUILLE = ? OR NUM_FS_INITIALE = ?) AND (STATUT IS NULL OR STATUT IN (?,?,?,?)) AND STATUT_BORDEREAU = ?");
        $a->execute(array($numero_facture,$numero_facture,'C','F','T','N',$statut_bordereau)) OR DIE($this->bdd->errorInfo());
        $json = $a->fetchAll();
        return $json;
    }
	
    public function trouver_verification_facture($numero_facture){
        $a = $this->bdd->prepare('SELECT * FROM FS_VERIFICATION_LIQUIDATION WHERE FEUILLE = ?');
        $a->execute(array($numero_facture));
        $json = $a->fetch();
        return $json;
    }
}