<?php
/**
 * Created by PhpStorm.
 * User: fabrice.bile
 * Date: 06/02/2020
 * Time: 19:59
 */

class LETTRESCLE extends BDD
{
    public function trouver($code_lettre){
        $a = $this->bdd->prepare('SELECT CODE,LIBELLE,PRIX_UNITAIRE FROM ecmu_ref_fs_lettre_cle WHERE CODE = ? AND FIN_VALIDITE IS NULL');
        $a->execute(array(trim($code_lettre)));

        $json = $a->fetch();
        return $json;
    }
}