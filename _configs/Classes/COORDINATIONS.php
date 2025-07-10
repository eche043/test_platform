<?php


class COORDINATIONS extends BDD
{
    public function lister_centre(){
        $a = $this->bdd->prepare("SELECT * FROM CENTRE_COORDINATION" );
        $a->execute(array());
        $json = $a->fetchAll();
        return $json;
    }
    public function trouver_libelle_ville($id_ville){
        $a = $this->bdd->prepare('SELECT ID,LIBELLE FROM REF_GEOLOC_COMMUNE WHERE ID = ?');
        $a->execute(array($id_ville));
        $json = $a->fetch();
        return $json;
    }
    public function  lister_ets($code){
        $a = $this->bdd->prepare("SELECT A.CODE_CENTRE,B.INP AS CODE_ETS, B.RAISON_SOCIALE, B.VILLE, A.DATE_DEBUT FROM ETS_CENTRE_COORDINATION A JOIN ECMU_REF_ETABLISSEMENT_SANTE B ON A.CODE_ETS = B.INP AND A.CODE_CENTRE = ? AND A.DATE_FIN IS NULL AND B.DATE_FIN_CONV IS NULL ORDER BY CODE_ETS ASC" );
        $a->execute(array($code));
        $json = $a->fetchAll();
        return $json;
    }
    public function  trouver_ets($code_centre,$code_ets)
    {
        $a = $this->bdd->prepare("SELECT A.CODE_CENTRE,B.INP AS CODE_ETS, B.RAISON_SOCIALE, A.DATE_DEBUT FROM ETS_CENTRE_COORDINATION A JOIN ECMU_REF_ETABLISSEMENT_SANTE B ON A.CODE_ETS = B.INP AND A.CODE_CENTRE = ? AND A.CODE_ETS = ? AND A.DATE_FIN IS NULL AND B.DATE_FIN_CONV IS NULL" );
        $a->execute(array($code_centre,$code_ets));
        $json = $a->fetch();
        return $json;
    }

    public function trouver_ets_valide($code)
    {
        $a = $this->bdd->prepare('SELECT INP AS CODE, RAISON_SOCIALE, ADRESSE_GEOGRAPHIQUE, SECTEUR_ACTIVITE, TYPE_ETS, CATEGORIE_PROFESSIONNELLE, TELEPHONE,  TELEPHONE_2, VILLE, FAX, EMAIL, DATE_CREATION, DATE_MISE_A_JOUR, DATE_DEBUT_CONV, DATE_FIN_CONV, MOTIF_FIN_CONVENTION, LONGIT, LATIT, PHARMACIE, ADRESSE_POSTALE, ADRESSE_GEOGRAPHIQUE, NIVEAU_SANITAIRE, LIBELLE_SPECIALITE, REGION, DEPARTEMENT, VILLAGE, USER_EDIT, USER_REG, DATE_EDIT, DATE_REG FROM ECMU_REF_ETABLISSEMENT_SANTE WHERE INP = ? AND DATE_FIN_CONV IS NULL');
        $a->execute(array($code));
        $json = $a->fetch();
        return $json;
    }
    public function lister_ps_par_etablissement($code_ets)
    {
        $a = $this->bdd->prepare('SELECT
A.NOM,
A.PRENOM,
A.INP AS CODE_PS,
A.DATE_FIN_VALIDITE,
B.CODE_ETS
FROM 
ECMU_REF_PROFESSIONNEL_SANTE A
JOIN PS_ETS B ON A.INP = B.PS
AND CODE_ETS = ?
AND B.STATUT = ?                     
AND DATE_FIN_VALIDITE IS NULL'
        );
        $a->execute(array($code_ets,'1'));
        $json = $a->fetchAll();
        return $json;
    }

    public function lister_ps($code_ps)
    {
        $a = $this->bdd->prepare('SELECT NOM,PRENOM FROM ECMU_REF_PROFESSIONNEL_SANTE WHERE INP= ? AND DATE_FIN_VALIDITE IS NULL');
        $a->execute(array($code_ps));
        $ps = $a->fetch();

        $json = array
        (
            'status' => true,
            'nom_ps' => $ps['NOM'],
            'prenom_ps' => $ps['PRENOM'],
        );

        return $json;

    }

    public function trouver_ps_ets($code_ps, $statut) {
        $a = $this->bdd->prepare('SELECT PS_ETS.CODE_ETS, PS_ETS.DATE_REG, PS_ETS.DATE_EDIT, ECMU_REF_ETABLISSEMENT_SANTE.RAISON_SOCIALE FROM PS_ETS, ECMU_REF_ETABLISSEMENT_SANTE WHERE ECMU_REF_ETABLISSEMENT_SANTE.INP = PS_ETS.CODE_ETS AND PS = ? AND STATUT = ?');
        $a->execute(array($code_ps,$statut));
        $json = $a->fetchAll();
        return $json;
    }

    public function ajouter_ps_centre($code_ps,$code_ets,$user)
    {
        $ps_ets = $this->trouver_ps_ets($code_ps,'1');
        if($ps_ets)
        {
            if($ps_ets[0]['CODE_ETS'] != $code_ets)
            {
                $a = $this->bdd->prepare('UPDATE ECMU_REF_PROFESSIONNEL_SANTE SET DATE_FIN_VALIDITE = SYSDATE WHERE INP = ?');
                $a->execute(array($code_ps));
                $commit = $this->bdd->prepare('COMMIT;');
                $commit->execute(array());
                if($a)
                {
                    $b = $this->bdd->prepare('UPDATE PS_ETS SET STATUT = ?, DATE_EDIT = SYSDATE,USER_EDIT = ? WHERE PS = ?');
                    $b->execute(array('0',$user,$code_ps));
                    $commit = $this->bdd->prepare('COMMIT;');
                    $commit->execute(array());
                    if($b)
                    {
                        $b = $this->bdd->prepare(' INSERT INTO PS_ETS(PS,CODE_ETS,STATUT,USER_EDIT) VALUES (:PS,:CODE_ETS,:STATUT,:USER_REG) ');
                        $b->execute(array(
                            'PS' => $code_ps,
                            'CODE_ETS' =>$code_ets,
                            'STATUT' => "1",
                            'USER_REG' =>$user));
                        if($b)
                        {
                            $c = $this->bdd->prepare('UPDATE ECMU_REF_PROFESSIONNEL_SANTE SET DATE_FIN_VALIDITE = ?, DATE_EDIT = SYSDATE,USER_EDIT = ? WHERE INP = ?');
                            $c->execute(array(null,$user,$code_ps));
                            $commit = $this->bdd->prepare('COMMIT;');
                            $commit->execute(array());
                            $json = array(
                                'status' => 'success',
                                'message' => 'LE PROFESSIONNEL DE SANTE  '.$code_ps.' A ETE AJOUTE AU CENTRE '.$code_ets,
                            );
                        }


                    }
                }
            }else
            {
                $json = array(
                    'status' => 'false',
                    'message' => 'LE PROFESSIONNEL DE SANTE  '.$code_ps.' EXISTE DEJA DANS CE CENTRE',
                );
            }
        }

        return $json;
    }

    Public function lister_terminaux($code_ets)
    {
        $a = $this->bdd->prepare('SELECT * FROM TERMINAUX_HISTORIQUE_DEPLOIEMENT WHERE CODE_ETS = ? AND DATE_FIN IS NULL');
        $a->execute(array($code_ets));
        $json = $a->fetchAll();

        return $json;
    }

    /*public function lister_terminaux($code_ets) {
        $a = $this->bdd->prepare(
            '
            SELECT 
            B.NUMERO_TELEPHONE,
            A.CODE_ETS AS ETABLISSEMENT,
            B.TERMINAL,
            B.TERMINAL_TYPE
            FROM 
            TERMINAUX_HISTORIQUE_DEPLOIEMENT A
            JOIN TERMINAUX B 
            ON A.CODE_ETS = B.ETABLISSEMENT
            AND A.CODE_ETS = ?
            AND A.DATE_FIN IS NULL
'
        );
        $a->execute(array($code_ets));
        $json = $a->fetchAll();
        return $json;
    }*/

    /*public function ajouter_nouveau_terminal($terminal_type,$code_ets,$code_imei,$numero_telephone,$user){
        $a = $this->bdd->prepare('SELECT ID, TERMINAL, ETABLISSEMENT, NUMERO_TELEPHONE FROM TERMINAUX WHERE TERMINAL = ?');
        $a->execute(array($code_imei));
        $terminal = $a->fetch();

        if(!$terminal) {
            $b = $this->bdd->prepare('INSERT INTO TERMINAUX(TERMINAL, TERMINAL_TYPE, ETABLISSEMENT, NUMERO_TELEPHONE, USER_REG) VALUES(:TERMINAL, :TERMINAL_TYPE, :ETABLISSEMENT, :NUMERO_TELEPHONE, :USER_REG)');
            $b->execute(array(
                'TERMINAL' => $code_imei,
                'TERMINAL_TYPE' => $terminal_type,
                'ETABLISSEMENT' => $code_ets,
                'NUMERO_TELEPHONE' => $numero_telephone,
                'USER_REG' => $user
            ));
            if($b->errorCode() != '00000') {
                $json = array(
                    'status' => 'failed',
                    'message' => 'NIVEAU 1: '.$b->errorCode().' => '.$b->errorInfo()[1].' => '.$b->errorInfo()[2]
                );
            }else {
                $c = $this->bdd->prepare('INSERT INTO TERMINAUX_HISTORIQUE_DEPLOIEMENT(TERMINAL, CODE_ETS, DATE_DEBUT, USER_REG) VALUES(:TERMINAL, :CODE_ETS, :DATE_DEBUT, :USER_REG)');
                $c->execute(array(
                    'TERMINAL' => $code_imei,
                    'CODE_ETS' => $code_ets,
                    'DATE_DEBUT' => date('Y-m-d',time()),
                    'USER_REG' => $user
                ));
                if($c->errorCode() != '00000') {
                    $json = array(
                        'status' => 'failed',
                        'message' => 'NIVEAU 2: '.$c->errorCode().' => '.$c->errorInfo()[1].' => '.$c->errorInfo()[2]
                    );
                }else {
                    $json = array(
                        'status' => 'success',
                        'message' => 'LE TERMINAL A ÉTÉ AJOUTÉ AVEC SUCCÈS.'
                    );
                }
            }
        }


        return $json;
    }*/

    public function ajouter_nouveau_terminal($terminal_type,$code_ets,$code_imei,$numero_telephone,$user){
        $a = $this->bdd->prepare('SELECT ID, TERMINAL, ETABLISSEMENT, NUMERO_TELEPHONE FROM TERMINAUX WHERE TERMINAL = ?');
        $a->execute(array($code_imei));
        $terminal = $a->fetch();

        if(!$terminal) {
            $b = $this->bdd->prepare('INSERT INTO TERMINAUX(TERMINAL, TERMINAL_TYPE, ETABLISSEMENT, NUMERO_TELEPHONE, USER_REG) VALUES(:TERMINAL, :TERMINAL_TYPE, :ETABLISSEMENT, :NUMERO_TELEPHONE, :USER_REG)');
            $b->execute(array(
                'TERMINAL' => $code_imei,
                'TERMINAL_TYPE' => $terminal_type,
                'ETABLISSEMENT' => $code_ets,
                'NUMERO_TELEPHONE' => $numero_telephone,
                'USER_REG' => $user
            ));
            if($b->errorCode() != '00000') {
                $json = array(
                    'status' => 'failed',
                    'message' => 'NIVEAU 1: '.$b->errorCode().' => '.$b->errorInfo()[1].' => '.$b->errorInfo()[2]
                );
            }else {
                $c = $this->bdd->prepare('INSERT INTO TERMINAUX_HISTORIQUE_DEPLOIEMENT(TERMINAL, CODE_ETS, DATE_DEBUT, USER_REG) VALUES(:TERMINAL, :CODE_ETS, :DATE_DEBUT, :USER_REG)');
                $c->execute(array(
                    'TERMINAL' => $code_imei,
                    'CODE_ETS' => $code_ets,
                    'DATE_DEBUT' => date('Y-m-d',time()),
                    'USER_REG' => $user
                ));
                if($c->errorCode() != '00000') {
                    $json = array(
                        'status' => 'failed',
                        'message' => 'NIVEAU 2: '.$c->errorCode().' => '.$c->errorInfo()[1].' => '.$c->errorInfo()[2]
                    );
                }else {
                    $json = array(
                        'status' => 'success',
                        'message' => 'LE TERMINAL A ÉTÉ AJOUTÉ AVEC SUCCÈS.'
                    );
                }
            }
        }
        else{
            $b = $this->bdd->prepare('UPDATE TERMINAUX SET TERMINAL_TYPE= ?, ETABLISSEMENT = ?, NUMERO_TELEPHONE = ?, DATE_EDIT = SYSDATE, USER_EDIT = ? WHERE ID = ?');
            $b->execute(array($terminal_type, $code_ets, $numero_telephone, $user, $terminal['ID'])) OR DIE(print_r(array($this->bdd->errorInfo(), 'test')));
            if($b->errorCode() != '00000') {
                $json = array(
                    'status' => 'failed',
                    'message' => 'NIVEAU 3: '.$b->errorCode().' => '.$b->errorInfo()[1].' => '.$b->errorInfo()[2]
                );
            }else {
                $c = $this->bdd->prepare('UPDATE TERMINAUX_HISTORIQUE_DEPLOIEMENT SET DATE_FIN = SYSDATE, DATE_EDIT = SYSDATE, USER_EDIT = ? WHERE TERMINAL = ? AND DATE_FIN IS NULL');
                $c->execute(array($user,$code_imei));
                if($c->errorCode() != '00000') {
                    $json = array(
                        'status' => 'failed',
                        'message' => 'NIVEAU 4: '.$c->errorCode().' => '.$c->errorInfo()[1].' => '.$c->errorInfo()[2]
                    );
                }else {
                    $d = $this->bdd->prepare('INSERT INTO TERMINAUX_HISTORIQUE_DEPLOIEMENT(TERMINAL, CODE_ETS, USER_REG) VALUES(:TERMINAL, :CODE_ETS, :USER_REG)');
                    $d->execute(array(
                        'TERMINAL' => $code_imei,
                        'CODE_ETS' => $code_ets,
                        'USER_REG' => $user
                    ));
                    if($d->errorCode() != '00000') {
                        $json = array(
                            'status' => 'failed',
                            'message' => 'NIVEAU 5: '.$d->errorCode().' => '.$d->errorInfo()[1].' => '.$d->errorInfo()[2]
                        );
                    }else {
                        $json = array(
                            'status' => 'success',
                            'message' => 'LE TERMINAL A ÉTÉ MIS A JOUR AVEC SUCCÈS.'
                        );
                    }
                }
            }
        }
        return $json;
    }

    public function trouver_terminal($imei,$telephone,$code_ets){
        $a = $this->bdd->prepare('SELECT * FROM TERMINAUX WHERE TERMINAL = ? AND NUMERO_TELEPHONE = ? AND ETABLISSEMENT = ?');
        $a->execute(array($imei,$telephone,$code_ets));
        $json = $a->fetch();
        return $json;
    }

    public function detail_terminaux($imei,$code_ets){
        $a = $this->bdd->prepare('SELECT * FROM TERMINAUX WHERE TERMINAL = ? AND ETABLISSEMENT = ?');
        $a->execute(array($imei,$code_ets));
        $json = $a->fetch();
        return $json;
    }

    public function retirer_terminal_centre($imei,$code_ets,$user)
    {

        $b = $this->bdd->prepare('UPDATE TERMINAUX_HISTORIQUE_DEPLOIEMENT SET DATE_FIN = SYSDATE, DATE_EDIT = SYSDATE, USER_EDIT = ? WHERE DATE_FIN IS NULL AND TERMINAL = ? AND CODE_ETS = ? ');
        $b->execute(array($user,$imei,$code_ets));

        $commit = $this->bdd->prepare('COMMIT;');
        $commit->execute(array());

        $json = array(
            'status' => 'success',
            'message' => 'LE TERMINAL '.$imei.'  A ETE RETIRE DU CENTRE '.$code_ets,
        );
        return $json;
    }

    public function historique_terminaux_centre($code_ets)
    {
        $a = $this->bdd->prepare('SELECT * FROM TERMINAUX_HISTORIQUE_DEPLOIEMENT WHERE DATE_FIN IS NOT NULL AND CODE_ETS = ?');
        $a->execute(array($code_ets));
        $json = $a->fetchAll();

        return $json;
    }
    public function lister_genres() {
        $a = $this->bdd->prepare('SELECT CODE, LIBELLE FROM REF_SEXE ORDER BY LIBELLE ASC');
        $a->execute(array());
        $json = $a->fetchAll();
        return $json;
    }
    /*public function moteur_recherche_assures($ogd_affiliation, $ogd_prestations, $genre, $num_secu, $nom_prenom) {
        if(!empty($ogd_prestations)){
            $cond_ogd_prest = "AND CODE_OGD_PRESTATIONS_PROV LIKE ?";
        }else{
            $cond_ogd_prest = "AND (CODE_OGD_PRESTATIONS_PROV LIKE ? OR CODE_OGD_PRESTATIONS_PROV IS NULL)";
        }
        $a = $this->bdd->prepare('
        SELECT
           * 
        FROM
            ECMU_ASSURES
        WHERE 
            CODE_OGD_COTISATIONS LIKE ?
            '.$cond_ogd_prest.'
            AND SEXE LIKE ?
            AND NUM_SECU LIKE ?
            AND (NOM || \' \' || PRENOM) LIKE ?
        ORDER BY 
            NOM, PRENOM, PAYEUR_NUM_SECU, DATE_NAISSANCE, DATE_AFFILIATION ASC
        ');
        $a->execute(array('%'.$ogd_affiliation.'%','%'.$ogd_prestations.'%','%'.$genre.'%','%'.$num_secu.'%','%'.$nom_prenom.'%'));
        $json = $a->fetchAll();
        return $json;
    }

    public function trouver_ogd($type, $code) {
        if($type == 'AFFL') {
            $a = $this->bdd->prepare('SELECT * FROM REF_OGD_COTISATION WHERE CODE LIKE ?');
            $a->execute(array($code));
            $json = $a->fetch();
        }elseif ($type == 'PRST') {
            $a = $this->bdd->prepare('SELECT CODE, LIBELLE, CAISSE, NUMERO_CENTRE, GRAND_REGIME FROM REF_OGD WHERE CODE = ?');
            $a->execute(array($code));
            $json = $a->fetch();
        }else {
            $json = array(
                'status' => 'failed',
                'message' => 'DONNEES INCORRECTES'
            );
        }
        return $json;
    }*/
    public function trouver_assure($num_secu) {
        $a = $this->bdd->prepare('SELECT * FROM ECMU_ASSURES WHERE NUM_SECU = ?');
        $a->execute(array($num_secu));
        $json = $a->fetch();
        return $json;
    }
    public function trouver_premiere_cotisations($num_secu) {
        $a = $this->bdd->prepare('SELECT MIN(DATE_DEBUT) AS DATE_DEBUT FROM ECMU_COTISATION_VENTILATION WHERE BENEFICIAIRE_NUM_SECU = ?');
        $a->execute(array($num_secu));
        $json = $a->fetch();
        return $json;
    }
    public function trouver_civilite($code) {
        $a = $this->bdd->prepare('SELECT CODE, LIBELLE FROM REF_CIVILITE WHERE CODE LIKE ?');
        $a->execute(array('%'.$code.'%'));
        $json = $a->fetch();
        return $json;
    }
    public function trouver_genre($code) {
        $a = $this->bdd->prepare('SELECT CODE, LIBELLE FROM REF_SEXE WHERE CODE LIKE ?');
        $a->execute(array('%'.$code.'%'));
        $json = $a->fetch();
        return $json;
    }
    public function trouver_nationalite($code, $libelle) {
        $a = $this->bdd->prepare('SELECT CODE, LIBELLE FROM REF_NATIONALITE WHERE CODE LIKE ? AND LIBELLE LIKE ?');
        $a->execute(array('%'.$code.'%','%'.$libelle.'%'));
        $json = $a->fetch();
        return $json;
    }
    public function trouver_situation_familiale($code, $libelle) {
        $a = $this->bdd->prepare('SELECT CODE, LIBELLE FROM REF_SITUATION_FAMILIALE WHERE CODE LIKE ? AND LIBELLE LIKE ?');
        $a->execute(array('%'.$code.'%','%'.$libelle.'%'));
        $json = $a->fetch();
        return $json;
    }
    public function trouver_csp($code, $libelle) {
        $a = $this->bdd->prepare('SELECT CODE, CODE_OGD_COTISATIONS, LIBELLE FROM REF_CATEGORIE_PROFESSIONNELLE WHERE CODE LIKE ? AND LIBELLE LIKE ?');
        $a->execute(array('%'.$code.'%','%'.$libelle.'%'));
        $json = $a->fetch();
        return $json;
    }
    public function trouver_qualite_civile($code, $libelle) {
        $a = $this->bdd->prepare('SELECT CODE, LIBELLE FROM REF_QUALITE_CIVILE WHERE CODE LIKE ? AND LIBELLE LIKE ?');
        $a->execute(array('%'.$code.'%','%'.$libelle.'%'));
        $json = $a->fetch();
        return $json;
    }
    public function trouver_assure_paiements_ogd($num_secu){
        $a = $this->bdd->prepare('SELECT COUNT(ID_POPULATION) AS NOMBRE, CODE_OGD, STATUT FROM OGD_AFFILIATION_COTISATIONS oac WHERE BENEFICIAIRE_NUM_SECU = ? GROUP BY CODE_OGD, STATUT');
        $a->execute(array($num_secu));
        $json = $a->fetchAll();
        return $json;
    }
    public function trouver_assure_paiements_electroniques($num_secu,$statut){
        $a = $this->bdd->prepare('SELECT * FROM COTISATION_PAIEMENT_WEB WHERE NUM_SECU = ? AND STATUT = ? ORDER BY DATE_REG DESC');
        $a->execute(array($num_secu,$statut));
        $json = $a->fetchAll();
        return $json;
    }
    public function trouver_profession($code) {
        $a = $this->bdd->prepare('SELECT CODE, LIBELLE FROM REF_PROFESSION WHERE CODE = ?');
        $a->execute(array($code));
        $json = $a->fetch();
        return $json;
    }
    /* public function consultation_droits_ecnam($num_secu, $date_consultation) {
        $a = $this->bdd->prepare('SELECT NUM_SECU, NATIONALITE, CODE_OGD_PRESTATIONS_PROV, EXECUTANT_REFERENT, DATE_AFFILIATION, CODE_OGD_COTISATIONS, DATE_DELIVRANCE_CARTE, CATEGORIE_PROFESSIONNELLE FROM ECMU_ASSURES WHERE NUM_SECU = ?');
        $a->execute(array($num_secu));
        $assure = $a->fetch();
        if(empty($assure['NUM_SECU'])) {
            $json = array(
                'success' => false,
                'message' => 'N° SECU INTROUVABLE'
            );
        }else {
            if($assure['CODE_OGD_COTISATIONS'] == '03016000') {
                $taux_couverture = '100%';
            }else {
                $taux_couverture = '70%';
            }

            if($assure['NATIONALITE'] == 'CIV'){
                $delais = 3;
            }else {
                $delais = 3;
            }

            $og = $this->bdd->prepare('SELECT * FROM REF_OGD WHERE CODE = ?');
            $og->execute(array($assure['CODE_OGD_PRESTATIONS_PROV']));
            $ogd = $og->fetch();

            if(!$ogd) {
                $json = array(
                    'success' => false,
                    'message' => 'AUCUN OGD PRESTATIONS N\'EST DEFINI POUR CET ASSURE'
                );
            }else {
                if($ogd['CODE'] == '05104000' || $ogd['CODE'] == '02104000') {
                    $json = array(
                        'success' => false,
                        'message' => 'LE CODE OGD '.$ogd['CODE'].' N\'EST PAS AUTORISE A EFFECTUER DES PRESTATIONS.'
                    );
                }else {
                    if(!empty($assure['EXECUTANT_REFERENT'])){
                        $et = $this->bdd->prepare('SELECT INP AS CODE, RAISON_SOCIALE FROM ECMU_REF_ETABLISSEMENT_SANTE WHERE INP = ?');
                        $et->execute(array($assure['EXECUTANT_REFERENT']));
                        $ets = $et->fetch();
                    }else{
                        $ets = array(
                            'CODE'=>'',
                            'RAISON_SOCIALE'=>''
                        );
                    }


                    //$date_affiliation = $assure['DATE_AFFILIATION'];

                    if($assure['CODE_OGD_COTISATIONS'] == '03011000' || $assure['CODE_OGD_COTISATIONS'] == '03012000' || $assure['CODE_OGD_COTISATIONS'] == '03013000' || $assure['CODE_OGD_COTISATIONS'] == '03014000' || $assure['CODE_OGD_COTISATIONS'] == '03015000') {
                        $date_generalisation = '2019-07-01';
                        if(strtotime($assure['DATE_AFFILIATION']) < strtotime($date_generalisation)) {
                            $date_affiliation = $date_generalisation;
                        }else {
                            $date_affiliation = $assure['DATE_AFFILIATION'];
                        }
                    }else {
                        $date_affiliation = $assure['DATE_AFFILIATION'];
                    }

                    $pr = $this->bdd->prepare('SELECT MIN(DATE_DEBUT) AS DATE_DEBUT FROM ECMU_COTISATION_VENTILATION WHERE BENEFICIAIRE_NUM_SECU = ?');
                    $pr->execute(array($num_secu));
                    $premiere_cotisation = $pr->fetch();
                    if($premiere_cotisation) {
                        if(strtotime($premiere_cotisation['DATE_DEBUT']) < strtotime($date_affiliation)) {
                            $date_affiliation = $premiere_cotisation['DATE_DEBUT'];
                        }
                    }


                    $mois_dus = abs((date('Y', strtotime($date_consultation)) - date('Y', strtotime($date_affiliation)))*12 + (date('m', strtotime($date_consultation)) - date('m', strtotime($date_affiliation))));

                    $b = $this->bdd->prepare('SELECT COUNT(BENEFICIAIRE_NUM_SECU) AS NBRE_COTISATIONS FROM ECMU_COTISATION_VENTILATION WHERE BENEFICIAIRE_NUM_SECU = ?');
                    $b->execute(array($assure['NUM_SECU']));
                    $cotisations = $b->fetch();
                    $mois_cotises = intval($cotisations['NBRE_COTISATIONS']);



                    $c = $this->bdd->prepare('SELECT COUNT(FEUILLE) AS NBRE_FACTURES FROM FS');
                    $c->execute(array());
                    $num = $c->fetch();
                    $nouveau_num_transaction = $num['NBRE_FACTURES'];

                    $ecart = ($mois_dus - $mois_cotises);

                    if($mois_dus >= $delais) {
                        if($mois_cotises >= $delais) {
                            if($mois_cotises >= $mois_dus) {
                                $json = array(
                                    'success' => true,
                                    'codeRetour' => 0,
                                    'commentaireRetour' => 'LA RECHERCHE S\'EST BIEN DÉROULÉE',
                                    'pNumSecu' => $assure['NUM_SECU'],
                                    'codeCSP' => $assure['CATEGORIE_PROFESSIONNELLE'],
                                    'droitsOuverts' => true,
                                    'nb_cotises' => $mois_cotises,
                                    'nb_dus' => $mois_dus,
                                    'numTransaction' => $nouveau_num_transaction,
                                    'dateFinDroits' => null,
                                    'codeOgdPrestations' => array(
                                        'code' => $ogd['CODE'],
                                        'defautON' => false,
                                        'libelle' => $ogd['LIBELLE'],
                                        'tauxCouverture' => $taux_couverture
                                    ),
                                    'exeReferent' => array(
                                        'code' => $ets['CODE'],
                                        'defautON' => false,
                                        'libelle' => $ets['RAISON_SOCIALE']
                                    )
                                );
                            }else {

                                if($ecart >= $delais) {
                                    $json = array(
                                        'success' => true,
                                        'codeRetour' => 0,
                                        'commentaireRetour' => 'LA RECHERCHE S\'EST BIEN DÉROULÉE.',
                                        'pNumSecu' => $assure['NUM_SECU'],
                                        'codeCSP' => $assure['CATEGORIE_PROFESSIONNELLE'],
                                        'droitsOuverts' => false,
                                        'nb_cotises' => $mois_cotises,
                                        'nb_dus' => $mois_dus,
                                        'numTransaction' => $nouveau_num_transaction,
                                        'dateFinDroits' => null,
                                        'codeOgdPrestations' => array(
                                            'code' => '',
                                            'defautON' => false,
                                            'libelle' => '',
                                            'tauxCouverture' => ''
                                        ),
                                        'exeReferent' => array(
                                            'code' => '',
                                            'defautON' => false,
                                            'libelle' => ''
                                        )
                                    );
                                }else {
                                    $json = array(
                                        'success' => true,
                                        'codeRetour' => 0,
                                        'commentaireRetour' => 'LA RECHERCHE S\'EST BIEN DÉROULÉE.',
                                        'pNumSecu' => $assure['NUM_SECU'],
                                        'codeCSP' => $assure['CATEGORIE_PROFESSIONNELLE'],
                                        'droitsOuverts' => true,
                                        'nb_cotises' => $mois_cotises,
                                        'nb_dus' => $mois_dus,
                                        'numTransaction' => $nouveau_num_transaction,
                                        'dateFinDroits' => null,
                                        'codeOgdPrestations' => array(
                                            'code' => $ogd['CODE'],
                                            'defautON' => false,
                                            'libelle' => $ogd['LIBELLE'],
                                            'tauxCouverture' => $taux_couverture
                                        ),
                                        'exeReferent' => array(
                                            'code' => $ets['CODE'],
                                            'defautON' => false,
                                            'libelle' => $ets['RAISON_SOCIALE']
                                        )
                                    );
                                }
                            }
                        }else {
                            $json = array(
                                'success' => true,
                                'codeRetour' => 0,
                                'commentaireRetour' => 'LA RECHERCHE S\'EST BIEN DÉROULÉE',
                                'pNumSecu' => $assure['NUM_SECU'],
                                'codeCSP' => $assure['CATEGORIE_PROFESSIONNELLE'],
                                'droitsOuverts' => false,
                                'nb_cotises' => $mois_cotises,
                                'nb_dus' => $mois_dus,
                                'numTransaction' => $nouveau_num_transaction,
                                'dateFinDroits' => null,
                                'codeOgdPrestations' => array(
                                    'code' => '',
                                    'defautON' => false,
                                    'libelle' => '',
                                    'tauxCouverture' => ''
                                ),
                                'exeReferent' => array(
                                    'code' => '',
                                    'defautON' => false,
                                    'libelle' => ''
                                )
                            );
                        }
                    }
                    else {
                        $json = array(
                            'success' => true,
                            'codeRetour' => 0,
                            'commentaireRetour' => 'La recherche s\'est bien déroulée.',
                            'pNumSecu' => $assure['NUM_SECU'],
                            'codeCSP' => $assure['CATEGORIE_PROFESSIONNELLE'],
                            'droitsOuverts' => false,
                            'nb_cotises' => $mois_cotises,
                            'nb_dus' => $mois_dus,
                            'numTransaction' => $nouveau_num_transaction,
                            'dateFinDroits' => null,
                            'codeOgdPrestations' => array(
                                'code' => '',
                                'defautON' => false,
                                'libelle' => '',
                                'tauxCouverture' => ''
                            ),
                            'exeReferent' => array(
                                'code' => '',
                                'defautON' => false,
                                'libelle' => ''
                            )
                        );
                    }
                }
            }
        }
        return $json;
    }
     */
	
	public function consultation_droits_ecnam($num_secu, $date_consultation) {

        $delais_prise_en_charge = 3;
        $delais_presations = 3;
        $codes_ogd_prestations_refuses = array('05104000', '02104000');

        $a = $this->bdd->prepare('SELECT A.NUM_SECU, A.PAYEUR_NUM_SECU, A.NATIONALITE, A.CODE_OGD_PRESTATIONS_PROV, B.LIBELLE AS LIBELLE_OGD_PRESTATIONS_PROV, A.EXECUTANT_REFERENT, A.DATE_AFFILIATION, A.CODE_OGD_COTISATIONS, A.DATE_DELIVRANCE_CARTE, A.CATEGORIE_PROFESSIONNELLE FROM ECMU_ASSURES A JOIN REF_OGD B ON A.CODE_OGD_PRESTATIONS_PROV = B.CODE AND A.NUM_SECU = ?');
        $a->execute(array($num_secu));
        $assure = $a->fetch();
        if(empty($assure['NUM_SECU'])) {
            $json = array(
                'success' => false,
                'message' => 'N° SECU INTROUVABLE'
            );
        }else {
            $payeur = $this->trouver_assure($assure['PAYEUR_NUM_SECU']);
            if(!$payeur) {
                $json = array(
                    'success' => false,
                    'message' => "L'ASSURÉ PAYEUR DE CET ASSURÉ N'EST PAS DÉFINI. VEUILLER CONTACTER L'ADMINISTRATEUR."
                );
            }else {
                $assure_code_ogd_prestations = $assure['CODE_OGD_PRESTATIONS_PROV'];
                $assure_libelle_ogd_prestations = $assure['LIBELLE_OGD_PRESTATIONS_PROV'];
                $assure_code_ogd_cotisations = $assure['CODE_OGD_COTISATIONS'];
                if(in_array($assure_code_ogd_prestations, $codes_ogd_prestations_refuses)) {
                    $json = array(
                        'success' => false,
                        'message' => "LE CODE OGD ".$assure_code_ogd_prestations." N'EST PAS AUTORISE A EFFECTUER DES PRESTATIONS."
                    );
                } else {
                    if($assure_code_ogd_cotisations == '03016000') {$taux_couverture = '100%';}else {$taux_couverture = '70%';}

                    if(!empty($assure['EXECUTANT_REFERENT'])){
                        $et = $this->bdd->prepare('SELECT INP AS CODE, RAISON_SOCIALE FROM ECMU_REF_ETABLISSEMENT_SANTE WHERE INP = ?');
                        $et->execute(array($assure['EXECUTANT_REFERENT']));
                        $ets = $et->fetch();
                    }
                    else{
                        $ets = array(
                            'CODE' => '',
                            'RAISON_SOCIALE' => ''
                        );
                    }
                    if(in_array($assure_code_ogd_cotisations, array('03011000', '03012000', '03013000', '03014000', '03015000', '03017000'))) {
                        $date_generalisation = '2019-07-01';
                        if(strtotime($assure['DATE_AFFILIATION']) < strtotime($date_generalisation)) {
                            $date_affiliation = $date_generalisation;
                        }else {
                            $date_affiliation = $assure['DATE_AFFILIATION'];
                        }
                    }
                    else {
                        $date_affiliation = $assure['DATE_AFFILIATION'];
                    }

                    $pr = $this->bdd->prepare('SELECT MIN(DATE_DEBUT) AS DATE_DEBUT FROM ECMU_COTISATION_VENTILATION WHERE BENEFICIAIRE_NUM_SECU = ?');
                    $pr->execute(array($num_secu));
                    $premiere_cotisation = $pr->fetch();
                    if($premiere_cotisation) {
                        if(strtotime($premiere_cotisation['DATE_DEBUT']) < strtotime($date_affiliation)) {
                            $date_affiliation = $premiere_cotisation['DATE_DEBUT'];
                        }
                    }

                    if(strtotime($date_consultation) >= strtotime($date_affiliation)) {
                        $date_debut = date_create($date_affiliation);
                        $date_fin = date_create($date_consultation);

                        $nombre_annees = (int)date_diff($date_debut, $date_fin)->format('%y');
                        $nombre_mois = (int)date_diff($date_debut, $date_fin)->format('%m');

                        $mois_dus = ($nombre_annees * 12) + $nombre_mois;


                        $b = $this->bdd->prepare('SELECT COUNT(BENEFICIAIRE_NUM_SECU) AS NBRE_COTISATIONS FROM ECMU_COTISATION_VENTILATION WHERE BENEFICIAIRE_NUM_SECU = ?');
                        $b->execute(array($assure['NUM_SECU']));
                        $cotisations = $b->fetch();
                        $mois_cotises = (int)$cotisations['NBRE_COTISATIONS'];

                        $c = $this->bdd->prepare('SELECT COUNT(FEUILLE) AS NBRE_FACTURES FROM FS');
                        $c->execute(array());
                        $num = $c->fetch();
                        $nouveau_num_transaction = (int)$num['NBRE_FACTURES'];

                        $ecart = ($mois_dus - $mois_cotises);


                        if($assure_code_ogd_cotisations === '03011000') {
                            if($payeur['CATEGORIE_PROFESSIONNELLE'] === 'SAL') {
                                $delais_presations = 12;
                            }
                        }


                        if($mois_cotises < $delais_prise_en_charge) {
                            $json = array(
                                'success' => false,
                                'message' => "CET ASSURE OBSERVE ENCORE LE DELAI DE CARENCE. IL NE PEUT DONC BENEFICIER DES PRESTATIONS"
                            );
                        }
                        else {
                            if($ecart > $delais_presations) {
                                $json = array(
                                    'success' => true,
                                    'codeRetour' => 0,
                                    'commentaireRetour' => 'LA RECHERCHE S\'EST BIEN DÉROULÉE.',
                                    'pNumSecu' => $assure['NUM_SECU'],
                                    'codeCSP' => $assure['CATEGORIE_PROFESSIONNELLE'],
                                    'droitsOuverts' => false,
                                    'nb_cotises' => $mois_cotises,
                                    'nb_dus' => $mois_dus,
                                    'numTransaction' => $nouveau_num_transaction,
                                    'dateFinDroits' => null,
                                    'codeOgdPrestations' => array(
                                        'code' => '',
                                        'defautON' => false,
                                        'libelle' => '',
                                        'tauxCouverture' => ''
                                    ),
                                    'exeReferent' => array(
                                        'code' => '',
                                        'defautON' => false,
                                        'libelle' => ''
                                    )
                                );
                            }else {
                                $json = array(
                                    'success' => true,
                                    'codeRetour' => 0,
                                    'commentaireRetour' => 'LA RECHERCHE S\'EST BIEN DÉROULÉE',
                                    'pNumSecu' => $assure['NUM_SECU'],
                                    'codeCSP' => $assure['CATEGORIE_PROFESSIONNELLE'],
                                    'droitsOuverts' => true,
                                    'nb_cotises' => $mois_cotises,
                                    'nb_dus' => $mois_dus,
                                    'numTransaction' => $nouveau_num_transaction,
                                    'dateFinDroits' => null,
                                    'codeOgdPrestations' => array(
                                        'code' => $assure_code_ogd_prestations,
                                        'defautON' => false,
                                        'libelle' => $assure_libelle_ogd_prestations,
                                        'tauxCouverture' => $taux_couverture
                                    ),
                                    'exeReferent' => array(
                                        'code' => $ets['CODE'],
                                        'defautON' => false,
                                        'libelle' => $ets['RAISON_SOCIALE']
                                    )
                                );
                            }
                        }
                    }
                    else {
                        $json = array(
                            'success' => false,
                            'message' => "ASSURE INEXISTANT A LA DATE DE CONSULTATION."
                        );
                    }
                }
            }
        }
        return $json;
    }
	
	public function trouver_derniere_cotisations($num_secu) {
        $a = $this->bdd->prepare('SELECT MAX(DATE_DEBUT) AS DATE_DEBUT FROM ECMU_COTISATION_VENTILATION WHERE BENEFICIAIRE_NUM_SECU = ?');
        $a->execute(array($num_secu));
        $json = $a->fetch();
        return $json;
    }
    public function trouver_mois_cotisations($num_secu,$annee,$mois) {
        $a = $this->bdd->prepare('SELECT EXTRACT (MONTH FROM TO_DATE(DATE_DEBUT)) AS MOIS, EXTRACT (YEAR FROM TO_DATE(DATE_DEBUT)) AS ANNEE, STATUT FROM ECMU_COTISATION_VENTILATION WHERE BENEFICIAIRE_NUM_SECU = ? AND EXTRACT (YEAR FROM TO_DATE(DATE_DEBUT)) = ? AND EXTRACT (MONTH FROM TO_DATE(DATE_DEBUT)) = ?');
        $a->execute(array($num_secu,$annee,$mois));
        $json = $a->fetch();
        return $json;
    }
}