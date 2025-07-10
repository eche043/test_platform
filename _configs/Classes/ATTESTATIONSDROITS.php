<?php
/**
 * Created by PhpStorm.
 * User: fabrice.bile
 * Date: 05/02/2020
 * Time: 19:33
 */

class ATTESTATIONSDROITS extends  BDD
{
    public function trouver($id_attestation, $num_secu){
        if(!empty($id_attestation)){
            $a = $this->bdd->prepare("SELECT ID, NUM_SECU, TO_CHAR(MOTIF_DEMANDE) AS MOTIF_DEMANDE, DATE_DEBUT_VALIDITE, DATE_FIN_VALIDITE, STATUT_ATTESTATION, DATE_REG FROM attestation_droits WHERE ID = ?");
            $a->execute(array($id_attestation));
        }elseif(!empty($num_secu)){
            $a = $this->bdd->prepare("SELECT ID, NUM_SECU, TO_CHAR(MOTIF_DEMANDE) AS MOTIF_DEMANDE, DATE_DEBUT_VALIDITE, DATE_FIN_VALIDITE, STATUT_ATTESTATION, DATE_REG FROM attestation_droits WHERE NUM_SECU = ?");
            $a->execute(array($num_secu));
        }
        $json = $a->fetchAll();
        return $json;
    }

    public function dernieres_attestations_assure($num_secu){
        $a = $this->bdd->prepare('SELECT * FROM (SELECT * FROM attestation_droits WHERE NUM_SECU = ? ORDER BY DATE_REG DESC) WHERE ROWNUM <= 5');
        $a->execute(array($num_secu));
        $json = $a->fetchAll();
        return $json;
    }

    public function nouvelle_demande_attestation($num_secu,$motif,$user){
        $a = $this->bdd->prepare('SELECT * FROM ATTESTATION_DROITS WHERE NUM_SECU = ?');
        $a->execute(array($num_secu));
        $attestations = $a->fetchAll();
        $nb_attestations = count($attestations);
        if ($nb_attestations == 0) {
            $b = $this->bdd->prepare('INSERT INTO ATTESTATION_DROITS(NUM_SECU, MOTIF_DEMANDE, STATUT_ATTESTATION, USER_REG) VALUES(:NUM_SECU, :MOTIF_DEMANDE, :STATUT_ATTESTATION, :USER_REG)');
            $b->execute(array(
                'NUM_SECU' => $num_secu,
                'MOTIF_DEMANDE' => $motif,
                'STATUT_ATTESTATION' => 0,
                'USER_REG' => $user
            ))OR DIE('ERREUR INSERTION ATTESTATION');
            $sh = $this->bdd->prepare('SELECT MAX(ID) AS ID FROM ATTESTATION_DROITS WHERE NUM_SECU = ?');
            $sh->execute(array($num_secu));
            $lastId = $sh->fetch();

            $json = array(
                'status' => true,
                'last_id'=>$lastId['ID'],
                'message' => 'LA NOUVELLE ATTESTATION A ETE ENREGISTREE AVEC SUCCES.'
            );
        }else {
            $b = $this->bdd->prepare('SELECT * FROM ATTESTATION_DROITS WHERE NUM_SECU = ? AND STATUT_ATTESTATION = ?');
            $b->execute(array($num_secu,0));
            $attestation = $b->fetch();
            if(empty($attestation['NUM_SECU'])) {
                $c = $this->bdd->prepare('SELECT * FROM ATTESTATION_DROITS WHERE NUM_SECU = ? AND STATUT_ATTESTATION = ? AND (DATE_FIN_VALIDITE >= ? OR DATE_FIN_VALIDITE IS NULL)');
                $c->execute(array($num_secu,1));
                $attestation = $c->fetch();
                if(empty($attestation['NUM_SECU'])) {
                    $d = $this->bdd->prepare('INSERT INTO ATTESTATION_DROITS(NUM_SECU, MOTIF_DEMANDE, STATUT_ATTESTATION, USER_REG) VALUES(:NUM_SECU, :MOTIF_DEMANDE, :STATUT_ATTESTATION, :USER_REG)');
                    $d->execute(array(
                        'NUM_SECU' => $num_secu,
                        'MOTIF_DEMANDE' => $motif,
                        'STATUT_ATTESTATION' => 0,
                        'USER_REG' => $user
                    ))OR DIE('ERREUR INSERTION ATTESTATION');
                    $sh = $this->bdd->prepare('SELECT MAX(ID) AS ID FROM ATTESTATION_DROITS WHERE NUM_SECU = ?');
                    $sh->execute(array($num_secu));
                    $lastId = $sh->fetch();

                    $json = array(
                        'status' => true,
                        'last_id'=>$lastId['ID'],
                        'message' => 'LA NOUVELLE ATTESTATION A ETE ENREGISTREE AVEC SUCCES.'
                    );
                }else {
                    $json = array(
                        'status' => false,
                        'message' => 'IL EXISTE DEJA UNE ATTESTAION EN COURS DE VALIDITE POUR CET ASSURE.'
                    );
                }
            }else {
                $json = array(
                    'status' => false,
                    'message' => 'IL EXISTE DEJA UNE ATTESTAION EN ATTENTE DE VALIDATION POUR CET ASSURE.'
                );
            }
        }

        return $json;
    }
}