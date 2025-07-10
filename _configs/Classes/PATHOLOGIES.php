<?php
/**
 * Created by PhpStorm.
 * User: mohamed.kone
 * Date: 07/02/2020
 * Time: 09:26
 */

class PATHOLOGIES extends BDD
{
    public function trouver($code,$libelle){
        $a = $this->bdd->prepare('SELECT * FROM ECMU_REF_FS_AFFECTIONS WHERE CODE LIKE ? AND UPPER(LIBELLE) LIKE ? AND PANIER = ?');
        $a->execute(array('%'.$code.'%','%'.$libelle.'%',1));
        $json = $a->fetchAll();
        return $json;
    }
}