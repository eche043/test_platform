<?php


class DISTRIBUTIONMASQUES extends BDD {

    public function trouver($num_secu){
        $a = $this->bdd->prepare('SELECT * FROM ECMU_DISTRIBUTION_MASQUES WHERE NUM_SECU = ? ORDER BY DATE_REG');
        $a->execute(array($num_secu));
        $json = $a->fetch();
        return $json;
    }
	public function historique_par_pharmacie($code_ets){
        $a = $this->bdd->prepare('SELECT * FROM ECMU_DISTRIBUTION_MASQUES WHERE CODE_ETS = ? ORDER BY DATE_REG DESC');
        $a->execute(array($code_ets));
        $json = $a->fetchAll();
        return $json;
    }
    public function moteur_recherche($num_secu){
        $a = $this->bdd->prepare('SELECT * FROM ECMU_DISTRIBUTION_MASQUES WHERE NUM_SECU = ? AND M_DATE_FIN > SYSDATE ORDER BY DATE_REG');
        $a->execute(array($num_secu));
        $json = $a->fetch();
        return $json;
    }
	
	public function moteur_recherche_par_centre($num_secu, $date_debut, $date_fin, $code_ets){
        $a = $this->bdd->prepare('SELECT * FROM ECMU_DISTRIBUTION_MASQUES WHERE NUM_SECU LIKE ? AND (M_DATE_DEBUT BETWEEN ? AND ?) AND CODE_ETS = ? AND M_DATE_FIN > SYSDATE ORDER BY DATE_REG DESC');
        $a->execute(array('%'.$num_secu.'%', $date_debut, $date_fin, $code_ets));
        $json = $a->fetchAll();
        return $json;
    }
	
    public function nouvelle_enregistrement($numero_secu, $numero_telephone, $quantite, $date_debut, $date_fin, $code_ets, $user){
        $mot = $this->moteur_recherche($numero_secu);
        if(!isset($mot['NUM_SECU'])) {
            $a = $this->bdd->prepare('INSERT INTO ECMU_DISTRIBUTION_MASQUES(NUM_SECU, NUMERO_TELEPHONE, M_DATE_DEBUT, M_DATE_FIN, M_QUANTITE, CODE_ETS, USER_REG) VALUES(:NUM_SECU, :NUMERO_TELEPHONE, :M_DATE_DEBUT, :M_DATE_FIN, :M_QUANTITE, :CODE_ETS, :USER_REG)');
            $a->execute(array(
                'NUM_SECU' => $numero_secu,
                'NUMERO_TELEPHONE' => $numero_telephone,
                'M_DATE_DEBUT' => $date_debut,
                'M_DATE_FIN' => $date_fin,
                'M_QUANTITE' => $quantite,
                'CODE_ETS' => $code_ets,
                'USER_REG' => $user
            )) OR DIE(print_r(array($numero_secu, $numero_telephone, $quantite, $date_debut, $date_fin, $code_ets, $user, $this->bdd->errorInfo())));
            $json = array(
                'status' => true,
                'message' => ''
            );
        }else{
            $json = array(
                'status' => false,
                'message' => "CET ASSURE NE POURRA ETRE SERVIR Q'APRES LE ".date('d/m/Y',strtotime($mot['M_DATE_FIN']))
            );
        }
        return $json;
    }

    public function trouver_premier_retrait($num_secu){
        $a = $this->bdd->prepare('SELECT * FROM ECMU_DISTRIBUTION_MASQUES WHERE NUM_SECU = ? ORDER BY DATE_REG ASC ');
        $a->execute(array($num_secu));
        $json = $a->fetch();
        return $json;
    }

}