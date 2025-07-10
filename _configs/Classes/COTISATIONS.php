<?php


class COTISATIONS extends BDD
{
    public function trouver_cotisation_web($code_collectivite,$date_debut,$date_fin) {
        $a = $this->bdd->prepare('SELECT * 
FROM COTISATION_PAIEMENT_WEB WHERE CODE_COLLECTIVITE = ? AND WALLET = ? AND DATE_REG BETWEEN ? AND ? AND STATUT = ?');
        $a->execute(array($code_collectivite,'ecmu',$date_debut,$date_fin,1));
        $json = $a->fetchAll();
        return $json;
    }




}