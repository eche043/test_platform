<?php

class TICKETS extends VEONEBDD
{
    public function lister($code_ets, $statut,$centre_coordination)
    {
        $List_statut = explode(";", $statut);
        $a = $this->bdd->prepare("
        SELECT
            A.TICKET_ID AS ID_TICKET, 
            A.DATE_REG AS DATE_TICKET,
            A.CODE_CATEGORIE AS CODE_CATEGORIE,
            A.DESCRIPTION AS DESCRIPTION,
            D.LIBELLE_CATEGORIE AS LIBELLE_CATEGORIE,
            A.CODE_TYPE AS CODE_TYPE, E.LIBELLE_TYPE AS LIBELLE_TYPE,
            A.TITRE AS TITRE,
            A.TELEPHONE_ASSURE,
            A.CODE_ETS AS CODE_ETS,
            A.USER_REG AS USER_REG,
            A.ADMIN_REG AS ADMIN_REG,
            A.DATE_EDIT AS DATE_EDIT,
            A.ADMIN_EDIT AS ADMIN_EDIT,
            F.RAISON_SOCIALE AS NOM_ETS,
            B.CODE_STATUT AS CODE_STATUT,
            B.DATE_DEBUT AS DATE_DEBUT,
            C.LIBELLE AS LIBELLE_STATUT
        FROM
            TICKET A
        JOIN TICKET_STATUT B ON
            A.TICKET_ID = B.TICKET_ID 
        JOIN TICKET_STATUT_DICTIONNAIRE C 
            ON B.CODE_STATUT = C.CODE_STATUT 
        JOIN TICKET_CATEGORIE D 
            ON A.CODE_CATEGORIE = D.CODE_CATEGORIE 
        JOIN TICKET_TYPE E 
            ON A.CODE_TYPE = E.CODE_TYPE 
        JOIN ECMU_REF_ETABLISSEMENT_SANTE F     
            ON A.CODE_ETS = F.INP LEFT JOIN ETS_CENTRE_COORDINATION H ON A.CODE_ETS = H.CODE_ETS 
        LEFT JOIN CENTRE_COORDINATION I ON H.CODE_CENTRE = I.CODE_CENTRE_COORDINATION
        WHERE A.CODE_ETS LIKE ? AND H.CODE_CENTRE like ?         
            AND F.DATE_FIN_CONV IS NULL
             AND B.DATE_FIN IS NULL AND H.DATE_EDIT IS NULL            
             AND (B.CODE_STATUT LIKE ? OR B.CODE_STATUT IN (select regexp_substr(?,'[^;]+', 1, level) 
           from dual 
           connect BY regexp_substr(?, '[^;]+', 1, level) 
           is not null))
        ORDER BY 
             A.TICKET_ID DESC");
        $a->execute(array('%' . $code_ets . '%', $statut, $statut, $statut));
        $json = $a->fetchAll();
        return $json;
    }
    public function moteur_recherche($id_ticket, $nom_ets, $categorie, $type, $statut, $ticket_en_cours_uniquement,$code_centre)
    {
        $requete = "
SELECT
	A.TICKET_ID AS ID_TICKET, 
	A.DATE_REG AS DATE_TICKET,
	A.CODE_CATEGORIE AS CODE_CATEGORIE,
    A.DESCRIPTION AS DESCRIPTION,
	D.LIBELLE_CATEGORIE AS LIBELLE_CATEGORIE,
	A.CODE_TYPE AS CODE_TYPE, E.LIBELLE_TYPE AS LIBELLE_TYPE,
    A.NUM_SECU AS NUM_SECU,
    A.TITRE AS TITRE,
       A.TELEPHONE AS TELEPHONE,
       A.TELEPHONE_ASSURE AS TELEPHONE_ASSURE,
	A.CODE_ETS AS CODE_ETS,
	F.RAISON_SOCIALE AS NOM_ETS,
	B.CODE_STATUT AS CODE_STATUT,
    B.ADMIN_REG AS STATUT_ADMIN_REG,
    B.USER_REG AS STATUT_USER_REG,
	C.LIBELLE AS LIBELLE_STATUT,
    D.NOM  AS NOM_USER,
               D.PRENOM AS PRENOM_USER,
               D.EMAIL AS EMAIL_USER,
               E.NOM  AS NOM_ADMIN,
               E.PRENOM AS PRENOM_ADMIN,
               E.EMAIL AS EMAIL_ADMIN ,
                I.LIBELLE_CENTRE 
FROM
	TICKET A
JOIN TICKET_STATUT B ON
	A.TICKET_ID = B.TICKET_ID 
	JOIN TICKET_STATUT_DICTIONNAIRE C 
	    ON B.CODE_STATUT = C.CODE_STATUT 
	JOIN TICKET_CATEGORIE D 
	    ON A.CODE_CATEGORIE = D.CODE_CATEGORIE 
	JOIN TICKET_TYPE E 
	    ON A.CODE_TYPE = E.CODE_TYPE 
	JOIN ECMU_REF_ETABLISSEMENT_SANTE F 
	    ON A.CODE_ETS = F.INP LEFT JOIN UTILISATEURS D 
            ON A.USER_REG  = D.ID_UTILISATEUR 
        LEFT JOIN ADMINISTRATEURS E 
            ON A.ADMIN_REG = E.ID_USER 
        INNER JOIN ETS_CENTRE_COORDINATION H ON A.CODE_ETS = H.CODE_ETS 
        INNER JOIN CENTRE_COORDINATION I ON H.CODE_CENTRE = I.CODE_CENTRE_COORDINATION
WHERE
	            A.TICKET_ID LIKE ? 
	           AND F.RAISON_SOCIALE LIKE ?                   
            AND F.DATE_FIN_CONV IS NULL
	           AND A.CODE_CATEGORIE LIKE ? 
	           AND A.CODE_TYPE LIKE ? 
            AND H.CODE_CENTRE = ?
	           AND B.DATE_FIN IS NULL AND H.DATE_EDIT IS NULL"  ;
        if ($ticket_en_cours_uniquement == true) {
            $requete = $requete . " AND B.CODE_STATUT IN ('N','C','R') ";
            $requete = $requete . " ORDER BY A.TICKET_ID DESC";
            $a = $this->bdd->prepare($requete);
            $a->execute(array('%' . $id_ticket . '%', '%' . $nom_ets . '%', '%' . $categorie . '%', '%' . $type . '%',$code_centre));
        } else {
            $requete = $requete . " AND B.CODE_STATUT LIKE ?";
            $requete = $requete . " ORDER BY A.TICKET_ID DESC";
            $a = $this->bdd->prepare($requete);
            $a->execute(array('%' . $id_ticket . '%', '%' . $nom_ets . '%', '%' . $categorie . '%', '%' . $type . '%',$code_centre,'%'.$statut.'%'));
        }


        return $a->fetchAll();
    }


    private function ajouter_statut($id_ticket, $statut, $motif, $user, $admin)
    {
        $a = $this->bdd->prepare("INSERT INTO TICKET_STATUT(TICKET_ID, CODE_STATUT, COMMENTAIRE, USER_REG, ADMIN_REG)
        VALUES(:TICKET_ID, :CODE_STATUT, :COMMENTAIRE, :USER_REG, :ADMIN_REG)");
        $a->execute(array(
            'TICKET_ID' => $id_ticket,
            'CODE_STATUT' => $statut,
            'COMMENTAIRE' => $motif,
            'USER_REG' => $user,
            'ADMIN_REG' => $admin
        ));
        if ($a->errorCode() === '00000') {
            return array(
                'status' => true
            );
        } else {
            return array(
                'status' => false,
                'message' => $a->errorInfo()[2]
            );
        }
    }
    private function fermer_statut($id_ticket, $date_fin, $user, $admin)
    {
        $a = $this->bdd->prepare("UPDATE TICKET_STATUT SET DATE_FIN = ?, USER_EDIT = ?, ADMIN_EDIT = ? WHERE TICKET_ID = ? AND DATE_FIN IS NULL");
        $a->execute(array($date_fin, $user, $admin, $id_ticket));
        if ($a->errorCode() === '00000') {
            return array(
                'status' => true
            );
        } else {
            return array(
                'status' => false,
                'message' => $a->errorInfo()[2]
            );
        }
    }

    public function trouver_statut($id_ticket)
    {
        $a = $this->bdd->prepare('
        SELECT TICKET_ID AS ID_TICKET, CODE_STATUT, COMMENTAIRE, DATE_DEBUT, USER_REG, ADMIN_REG FROM TICKET_STATUT WHERE TICKET_ID = ? AND DATE_FIN IS NULL');
        $a->execute(array($id_ticket));
        return $a->fetch();
    }

    public function editer_statut($id_ticket, $statut, $motif, $user, $admin)
    {
        $ticket_statut = $this->trouver_statut($id_ticket);
        if ($ticket_statut) {
            $fermer = $this->fermer_statut($ticket_statut['ID_TICKET'], date('Y-m-d H:i:s', time()), $user, $admin);
            if ($fermer['status'] === true) {
                return $this->ajouter_statut($id_ticket, $statut, $motif, $user, $admin);
            } else {
                return $fermer;
            }
        } else {
            return $this->ajouter_statut($id_ticket, $statut, $motif, $user, $admin);
        }
    }
    public function lister_ticket_actions($ticket)
    {
        $a = $this->bdd->prepare('
        SELECT A.ACTION_ID AS ACTION_ID,
               A.DESCRIPTION AS DESCRIPTION,
               A.DATE_REG_A AS DATE_REG_A,
               A.TICKET AS TICKET_ID,
               A.TYPE_USER,
               A.ADMIN_REG AS ADMIN_REG_ID,
               A.USER_REG  AS USER_REG_ID,
               B.NOM  AS NOM_USER,
               B.PRENOM AS PRENOM_USER,
               B.EMAIL AS EMAIL_USER,
               C.NOM  AS NOM_ADMIN,
               C.PRENOM AS PRENOM_ADMIN,
               C.EMAIL AS EMAIL_ADMIN  
        FROM TICKET_ACTION A 
        LEFT JOIN UTILISATEURS B 
            ON A.USER_REG  = B.ID_UTILISATEUR 
        LEFT JOIN ADMINISTRATEURS C 
            ON A.ADMIN_REG = C.ID_USER
        WHERE A.TICKET = ?
        ORDER BY DATE_REG_A ASC');
        $a->execute(array($ticket));

        $json = $a->fetchAll();
        return $json;
    }

    public function lister_ticket_type()
    {
        $a = $this->bdd->prepare('SELECT * FROM TICKET_TYPE');
        $a->execute();
        $array = $a->fetchAll();
        foreach ($array as $line) {
            $json[] = array('id' => $line['CODE_TYPE'], 'libelle' => $line['LIBELLE_TYPE'],
                'date_reg' => $line['DATE_REG'],
                'date_edit' => $line['DATE_EDIT'],
                'user_reg' => $line['USER_REG'],
                'statut' => $line['STATUT']);
        }
        return $json;
    }
    public function afficher_liste_status_ticket()
    {
        $a = $this->bdd->prepare("
        SELECT * FROM TICKET_STATUT_DICTIONNAIRE");
        $a->execute();
        $array = $a->fetchAll();
        $json = $array;
        return $json;
    }
    public function lister_ticket_categorie()
    {
        $a = $this->bdd->prepare('SELECT * FROM ticket_categorie');
        $a->execute();
        $array = $a->fetchAll();
        foreach ($array as $line) {
            $json[] = array('id' => $line['CODE_CATEGORIE'],
                'libelle' => $line['LIBELLE_CATEGORIE'],
                'date_reg' => $line['DATE_REG'],
                'date_edit' => $line['DATE_EDIT'],
                'user_reg' => $line['USER_REG'],
                'statut' => $line['STATUT']);
        }
        return $json;
    }

    public function afficher_ets_ticket($ets,$centre_coordination)
    {
        $a = $this->bdd->prepare("
        SELECT DISTINCT B.RAISON_SOCIALE 
        FROM TICKET A JOIN ECMU_REF_ETABLISSEMENT_SANTE B 
            ON  A.CODE_ETS=B.INP JOIN ETS_CENTRE_COORDINATION C 
	    ON A.CODE_ETS = C.CODE_ETS WHERE B.RAISON_SOCIALE LIKE ? AND C.CODE_CENTRE = ?  AND C.DATE_EDIT IS NULL");
        $a->execute(array('%' . $ets . '%',$centre_coordination));
        $array = $a->fetchAll();
        $json = $array;
        return $json;
    }

    public function ajouter_ticket_action($ticket_id, $description, $user_reg, $admin_reg,$type_user)
    {
        $requete = 'INSERT INTO TICKET_ACTION (TICKET,DESCRIPTION,USER_REG,ADMIN_REG,TYPE_USER) VALUES (?,?,?,?,?)';

        $a = $this->bdd->prepare($requete);
        $a->execute(array($ticket_id, $description, $user_reg, $admin_reg,$type_user));
        if ($a->errorCode() == '00000') {
            $json = array(
                'status' => true,
                'message' => 'Insert OK'
            );
        } else {
            $json = array(
                'status' => false,
                'error_code' => $a->errorCode(),
                'message' => $a->errorInfo()[2]
            );
        }
        return $json;
    }


    public  function envoyer_sms($numero_assure,$message){
        $url = URL_ECNAM."webservices/envoi-sms.php";
        $parametres = array(
            'numero' => $numero_assure,
            'type_envoi' => 'I',
            'message' => $message
        );

        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($parametres),
            ),
            "ssl" => array(
                'verify_peer' => false,
                'verify_peer_name' => false
            )
        );
        $context  = stream_context_create($options);
        $retour = json_decode(file_get_contents($url, false, $context));
        return $retour;
    }
}
