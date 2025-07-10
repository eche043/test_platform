<?php


class FICHIERS extends BDD
{
    public function trouver_fichier($nom_fichier, $norme) {
        $a = $this->bdd->prepare('SELECT * FROM HISTORIQUE_FICHIERS WHERE NOM_FICHIER = ? AND NORME = ?');
        $a->execute(array($nom_fichier, $norme));
        $json = $a->fetch();
        return $json;
    }

    public function inserer_historique_fichier($num_transaction, $type_mouvement, $norme, $version, $date_fichier, $source, $destination, $occurrences, $nom_fichier, $user) {

        $verif_fichier = $this->bdd->prepare('SELECT NOM_FICHIER FROM HISTORIQUE_FICHIERS WHERE NOM_FICHIER = ?');
        $verif_fichier->execute(array($nom_fichier));
        $nb_verif_fichier = $verif_fichier->fetch();

        if(!isset($nb_verif_fichier['NOM_FICHIER'])){
            $a = $this->bdd->prepare('INSERT INTO HISTORIQUE_FICHIERS(NUM_TRANSACTION, TYPE_MOUVEMENT, NORME, VERSION, DATE_FICHIER, SOURCE, DESTINATION, OCCURRENCES, NOM_FICHIER, USER_REG) 
            VALUES(:NUM_TRANSACTION, :TYPE_MOUVEMENT, :NORME, :VERSION, :DATE_FICHIER, :SOURCE, :DESTINATION, :OCCURRENCES, :NOM_FICHIER, :USER_REG)');
            $a->execute(array(
                'NUM_TRANSACTION'=> $num_transaction,
                'TYPE_MOUVEMENT' => $type_mouvement,
                'NORME' => $norme,
                'VERSION' => $version,
                'DATE_FICHIER' => $date_fichier,
                'SOURCE' => $source,
                'DESTINATION' => $destination,
                'OCCURRENCES' => $occurrences,
                'NOM_FICHIER' => $nom_fichier,
                'USER_REG' => $user
            ))OR DIE(print_r(array($num_transaction.'->'.$type_mouvement.'->'.$norme.'->'.$version.'->'.$date_fichier.'->'.$source.'->'.$destination.'->'.$occurrences.'->'.$nom_fichier.'->'.$user,$this->bdd->errorInfo())));

            $json = array(
                'status' => true
            );

        }else{
            $json = array(
                'status' => false,
                'message' => 'CE FICHIER A DEJA ETE TRAITE.'
            );
        }

        return $json;
    }

    public function trouver_last_fichier() {
        $a = $this->bdd->prepare('SELECT MAX(ID_FICHIER) AS "LAST_ID" FROM HISTORIQUE_FICHIERS');
        $a->execute(array());
        $json = $a->fetch();
        return $json;
    }
}