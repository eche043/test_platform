<?php


class OGD extends BDD
{
    public function trouver($type, $code) {
        if($type == 'PRST') {
            $a = $this->bdd->prepare('SELECT GRAND_REGIME, CAISSE, CODE, LIBELLE FROM REF_OGD WHERE CODE = ?');
            $a->execute(array($code));
        }
        elseif($type == 'AFFL') {
            $a = $this->bdd->prepare('SELECT CODE, LIBELLE FROM REF_OGD_COTISATION WHERE CODE = ?');
            $a->execute(array($code));
        }
        $json = $a->fetch();
        return $json;
    }

    public function trouver_ogd_cotisation($code){
        $a = $this->bdd->prepare('SELECT CODE, LIBELLE FROM REF_OGD_COTISATION WHERE CODE = ?');
        $a->execute(array($code));
        $json = $a->fetch();
        return $json;
    }
}