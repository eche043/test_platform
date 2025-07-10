<?php
/**
 * Created by PhpStorm.
 * User: mohamed.kone
 * Date: 07/02/2020
 * Time: 08:05
 */

class MEDICAMENTS extends BDD
{
    public function trouver($code,$libelle,$forme,$conditionnement){
        $a = $this->bdd->prepare('SELECT * 
        FROM ecmu_ref_fs_medicaments 
        WHERE CODE LIKE ? 
        AND (UPPER(LIBELLE) LIKE UPPER(?) OR UPPER(DCI1) LIKE UPPER(?))
        AND FORME LIKE ? 
        AND CONDITIONNEMENT LIKE ? 
        AND PANIER = ? 
        AND DATE_FIN_VALIDITE IS NULL
');
        $a->execute(array('%'.$code.'%','%'.$libelle.'%','%'.$libelle.'%','%'.$forme.'%','%'.$conditionnement.'%',1));
        $json = $a->fetchAll();
        return $json;
    }

    public function trouver_forme(){
        $a = $this->bdd->prepare('SELECT DISTINCT(FORME) FROM ECMU_REF_FS_MEDICAMENTS WHERE PANIER = 1 ORDER BY FORME ASC');
        $a->execute(array());
        $json = $a->fetchAll();
        return $json;
    }

    public function trouver_conditionnement(){
        $a = $this->bdd->prepare('SELECT DISTINCT(CONDITIONNEMENT) FROM ecmu_ref_fs_medicaments WHERE PANIER = 1 ORDER BY CONDITIONNEMENT ASC');
        $a->execute(array());
        $json = $a->fetchAll();
        return $json;
    }

    public function trouver_medicament($type_code,$code_medicament) {
        if($type_code=='EAN13'){
            $a = $this->bdd->prepare('SELECT * FROM ECMU_REF_FS_MEDICAMENTS WHERE EAN13 = ?');
        }else{
            $a = $this->bdd->prepare('SELECT * FROM ECMU_REF_FS_MEDICAMENTS WHERE CODE = ?');
        }
        $a->execute(array($code_medicament));
        $json = $a->fetch();
        return $json;
    }




}