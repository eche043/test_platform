<?php
include "BDD.php";
class UTILISATEURS extends BDD {
    public function trouver($user_id, $email, $pseudo){
        if(!empty($user_id)){
            $a =$this->bdd->prepare('SELECT * FROM UTILISATEURS WHERE ID_UTILISATEUR = ?');
            $a->execute(array($user_id));
        }else{
            if(!empty($pseudo)){
                $a =$this->bdd->prepare('SELECT * FROM UTILISATEURS WHERE LOGIN = ?');
                $a->execute(array($pseudo));
            }else{
                $a =$this->bdd->prepare('SELECT * FROM UTILISATEURS WHERE EMAIL = ?');
                $a->execute(array($email));
            }
        }
        $json = $a->fetch();
        return $json;
    }

    public function creer($num_secu, $email, $num_telepehone){
        $a = $this->bdd->prepare('SELECT NUM_SECU, NOM, PRENOM FROM ecmu_assures WHERE NUM_SECU = ?');
        $a->execute(array($num_secu));
        $assure_existe = $a->fetch();

        if(!empty($assure_existe['NUM_SECU'])){
            $b =$this->bdd->prepare('SELECT NUM_SECU FROM utilisateurs WHERE NUM_SECU = ?');
            $b->execute(array($num_secu));
            $user_num_secu_existe = $b->fetch();

            if(empty($user_num_secu_existe['NUM_SECU'])){
                $verifier_email = $this->trouver(null,$email,null);
                if(empty($verifier_email['EMAIL'])){
                    $date = date('Y-m-d H:i:s',time());
                    $password = strtoupper(substr(sha1($num_secu.$date),0,10));

                    $c = $this->bdd->prepare('INSERT INTO utilisateurs (NUM_SECU, TYPE_UTILISATEUR, LOGIN, MOT_DE_PASSE, MDP_ACTIF, NOM, PRENOM, EMAIL, TELEPHONE, ACTIF, PROFIL, USER_REG) 
                                                      VALUES(:NUM_SECU, :TYPE_UTILISATEUR, :LOGIN, :MOT_DE_PASSE, :MDP_ACTIF, :NOM, :PRENOM, :EMAIL, :TELEPHONE, :ACTIF, :PROFIL, :USER_REG)');
                    $c->execute(array(
                        'NUM_SECU' => $num_secu,
                        'TYPE_UTILISATEUR' => 'ASSU',
                        'LOGIN' => $num_secu,
                        'MOT_DE_PASSE' => sha1($password),
                        'MDP_ACTIF' => 0,
                        'NOM' => $assure_existe['NOM'],
                        'PRENOM' => $assure_existe['PRENOM'],
                        'EMAIL' => $email,
                        'TELEPHONE' => $num_telepehone,
                        'ACTIF' => 1,
                        'PROFIL' => 'ASSU;',
                        'USER_REG' => 1
                    ))OR DIE('Error User'.$num_secu.'->'.$email.'->'.$num_telepehone.'->'.$password.'->'.$assure_existe['NOM'].'->'.$assure_existe['PRENOM']);

                    $json = array(
                        'status' => true,
                        'message' => 'VOTRE COMPTE A ETE CREE AVEC SUCCES. UN MESSAGE CONTENANT VOS IDENTIFIANTS DE CONNEXION A ETE ENVOYE PAR EMAIL A L\'ADRESSE '.$email.'. N\'HESITEZ PAS A CONSULTER VOS SPAMS.',
                        'prenom' => $assure_existe['PRENOM'],
                        'mot_de_passe' => $password
                    );
                }else{
                    $json = array(
                        'status' => false,
                        'message' => 'CET EMAIL A DEJA ETE UTILISE POUR LA CREATION D\'UN AUTRE COMPTE.1'
                    );
                }
            }else{
                $json = array(
                    'status' => false,
                    'message' => 'CE NUMERO SECU A DEJA ETE UTILISE POUR LA CREATION D\'UN AUTRE COMPTE.2'
                );
            }
        }else{
            $json = array(
                'status' => false,
                'message' => 'CE NUMERO SECU EST ERRONE. PRIERE ENTRER UN NUMERO CORRECT.'
            );
        }

        return $json;
    }

    public function reinitialiser_mot_de_passe ($email){
        $verifier_email = $this->trouver(null,$email,null);
        if(!empty($verifier_email['EMAIL'])){
            $date = date('Y-m-d H:i:s',time());
            $password = strtoupper(substr(sha1($verifier_email['NUM_SECU'].$date),0,10));
            $a = $this->bdd->prepare('UPDATE utilisateurs SET MDP_ACTIF = ?, MOT_DE_PASSE = ?, DATE_EDIT = ? WHERE ID_UTILISATEUR = ?');
            $a->execute(array(0, sha1($password), date('Y-m-d H:i:s',time()),$verifier_email['ID_UTILISATEUR']));

            $json = array(
                'status' => true,
                'message' => 'UN EMAIL VOUS A ETE ENVOYE A L\'ADRESSE <b>'.$email.'</b>. PRIERE LA CONSULTER.',
                'mot_de_passe' => $password,
                'prenom' => $verifier_email['PRENOM']
            );
        }else{
            $json = array(
                'status' => false,
                'message' => 'CET EMAIL NE CORRESPOND A AUCUN COMPTE. PRIERE SAISIR UNE AUTRE ADRESSE EMAIL.'
            );
        }
        return $json;
    }

    public function connexion($identifiant,$mot_de_passe){
        $a = $this->bdd->prepare('SELECT ID_UTILISATEUR, LOGIN, NOM, PRENOM, ACTIF, MDP_ACTIF  FROM utilisateurs WHERE LOGIN = ? AND MOT_DE_PASSE = ?');
        $a->execute(array($identifiant,sha1($mot_de_passe)));
        $utilisateur = $a->fetch();
        if (!isset($utilisateur['ID_UTILISATEUR']) && empty($utilisateur['ID_UTILISATEUR'])) {
            $json = array(
                'status' => false,
                'message' => "IDENTIFIANT ET/OU MOT DE PASSE INCORRECT."
            );
        } else {

            if ($utilisateur['ACTIF'] == 1) {
                $json = array(
                    'status' => true,
                    'user_id' => $utilisateur['ID_UTILISATEUR']
                );

            }else {
                $json = array(
                    'status' => false,
                    'message' => "CE COMPTE A ÉTÉ DÉSACTIVÉ. VEUILLEZ CONTACTER L'ADMINISTRATEUR."
                );
            }
        }

        return $json;
    }

    public function mise_a_jour_mot_de_passe($ancien,$nouveau,$user){
        $date = date('Y-m-d H:i:s',time());
        $utilisateur = $this->trouver($user,NULL,NULL);
        if(!empty($utilisateur['ID_UTILISATEUR'])) {
            if($utilisateur['MOT_DE_PASSE'] == sha1($ancien)) {
                $a = $this->bdd->prepare('UPDATE utilisateurs SET MOT_DE_PASSE = ?, MDP_ACTIF = ?, DATE_EDIT = ? WHERE ID_UTILISATEUR = ?');
                $a->execute(array(sha1($nouveau),1,$date,$user));
                $json = array(
                    'status' => true,
                    'message' => 'VOTRE MOT DE PASSE A ETE MODIFIE AVEC SUCCES.'
                );
            }else {
                $json = array(
                    'status' => false,
                    'message' => 'L\'ANCIEN MOT DE PASSE EST INCORRECT. VEUILLEZ SAISIR LE MOT DE PASSE VALIDE.'
                );
            }
        }else {
            $json = array(
                'status' => false,
                'message' => 'CET UTILISATEUR EST INCONNU.'
            );
        }
        return $json;
    }

    public function editer_piste_audit($adresse_ip, $action, $contenu, $user){
        $a = $this->bdd->prepare('INSERT INTO log_ecmu(LOG_ADRESSE_IP, LOG_ACTION, LOG_CONTENU, USER_REG) VALUES(:LOG_ADRESSE_IP, :LOG_ACTION, :LOG_CONTENU, :USER_REG)');
        $a->execute(array($adresse_ip,$action,$contenu,$user)) OR DIE('echec');
        $json = array(
            'status' => true
        );
        return $json;
    }

    public function lister_profils(){
        $a = $this->bdd->query('SELECT PROFIL_CODE AS CODE, PROFIL_LIBELLE AS LIBELLE FROM utilisateurs_profils');
        $json = $a;
        return $json;
    }

    public function trouver_profil($code){
        $a = $this->bdd->prepare('SELECT PROFIL_CODE AS CODE, PROFIL_LIBELLE AS LIBELLE FROM utilisateurs_profils WHERE PROFIL_CODE = ?');
        $a->execute(array($code));
        $json = $a->fetch();
        return $json;
    }

    public function trouver_ets_utilisateur($user){
        $trouver_user = $this->trouver($user, null,null);
        $profil = array_diff(explode(';',stream_get_contents($trouver_user['PROFIL'],-1)),array(""));
        $nb_profils = count($profil);
        if($nb_profils != 0) {
            if(in_array('AGAC',$profil)) {
                $a = $this->bdd->prepare('SELECT CODE_AGENT, CODE_ETS, INP_RESPONSABLE AS CODE_PS FROM agent_ets WHERE CODE_AGENT = ? AND DATE_FIN_VALIDITE IS NULL');
                $a->execute(array($trouver_user['CODE_AGENT']));
                $json = $a->fetch();
            }elseif(in_array('PS',$profil)){
                $a = $this->bdd->prepare('SELECT CODE_ETS, PS AS CODE_PS, STATUT FROM ps_ets WHERE PS = ? AND STATUT = ?');
                $a->execute(array($trouver_user['CODE_PS'],1));
                $json = $a->fetchAll();
            }elseif(in_array('DCS',$profil)){
                $a = $this->bdd->prepare('SELECT CODE_DCS, CODE_ETS, STATUT FROM DIRECTION_CENTRE_SANTE_ETS WHERE CODE_DCS = ? AND STATUT = ?');
                $a->execute(array($trouver_user['CODE_DCS'],1));
                $json = $a->fetchAll();
            }else {
                $json = null;
            }
        }
        return $json;
    }

    public function trouver_utilisateur_ogd($code_ogd_p) {
        $a =$this->bdd->prepare('SELECT * FROM utilisateurs WHERE CODE_OGD_P = ?');
        $a->execute(array($code_ogd_p));
        $json = $a->fetchAll();
        return $json;
    }

    public function trouver_ets_ps($code_ps,$statut) {
        $a = $this->bdd->prepare('SELECT PS,CODE_ETS FROM PS_ETS WHERE PS LIKE ? AND STATUT LIKE ? ORDER BY CODE_ETS ASC');
        $a->execute(array($code_ps,'%'.$statut.'%'));
        $json = $a->fetchAll();
        return $json;
    }

    public function trouver_directeur_ets($login,$statut) {
        $a = $this->bdd->prepare('
SELECT CODE_DCS,CODE_ETS 
FROM DIRECTION_CENTRE_SANTE_ETS 
WHERE CODE_DCS LIKE ? 
AND STATUT LIKE ? 
ORDER BY CODE_ETS ASC
');
        $a->execute(array($login,'%'.$statut.'%'));
        $json = $a->fetchAll();
        return $json;
    }

    public function lister_ps_etablissements($code_ps) {
        $a = $this->bdd->prepare('SELECT CODE_ETS, STATUT, DATE_EDIT, DATE_REG, USER_EDIT, USER_REG FROM PS_ETS WHERE PS = ?');
        $a->execute(array($code_ps));
        $json = $a->fetchAll();
        return $json;
    }

    public function generer_code_agent() {
        $a = $this->bdd->prepare('SELECT COUNT(CODE_AGENT) AS EFFECTIF FROM UTILISATEURS WHERE CODE_AGENT = ? OR  CODE_AGENT IS NULL');
        $a->execute(array(''));
        $nb_agents = $a->fetch();
        $nouveau_code = 'AGAC'.str_pad(($nb_agents['EFFECTIF'] + 1),'5','0',STR_PAD_LEFT);
        $json = array(
            'CODE_AGENT' => $nouveau_code
        );
        return $json;
    }

    public function trouver_agent_ets($code_agent) {
        $utilisateur = $this->trouver($code_agent,null,null);
        if(!empty($utilisateur['LOGIN'])) {
            $a = $this->bdd->prepare('SELECT * FROM AGENT_ETS WHERE CODE_AGENT = ? WHERE DATE_FIN_VALIDITE IS NULL ORDER BY DATE_DEBUT_VALIDITE DESC');
            $a->execute(array($utilisateur['CODE_AGENT']));
            $json = $a->fetch();

        }else {
            $json = array(
                'status' => false,
                'message' => 'UTILISATEUR INCONNU'
            );
        }
        return $json;

    }



        public function reinitialisation_mot_de_passe($id_utilisateur,$mot_de_passe,$user){

            $date = date('Y-m-d H:i:s',time());
            $passe = sha1($mot_de_passe);
            $a = $this->bdd->prepare("UPDATE UTILISATEURS SET MOT_DE_PASSE = ?, DATE_EDIT = SYSDATE, USER_EDIT = ? WHERE ID_UTILISATEUR = ?");
            $a->execute(array($passe,$user,$id_utilisateur))OR DIE ('ECHEC REINITIALISATION DU MOT DE PASSE');
            $b = $this->bdd->prepare("COMMIT");
            $b->execute(array());
            $json = array(
                'status' => true,
                'message' => 'le mot de passe de '.$id_utilisateur. 'est :'.$passe
            );
            return $json;
        }

        public function editer_photo_profil($photo,$user,$id_utilisateur){
            $a = $this->bdd->prepare('UPDATE UTILISATEURS SET IMAGE = ?,DATE_EDIT = SYSDATE, USER_EDIT = ? WHERE ID_UTILISATEUR = ?');
            $a->execute(array($photo,$user,$id_utilisateur));
            $json = array(
                'status' => true,
                'message' => 'MODIFICATION DE LA PHOTO DE PROFIL EFFECTUE AVEC SUCCES'
            );
            return $json;
        }


    public function trouver_utilisateur_collectivite($id_utilisateur, $code_collectivite){
        $a = $this->bdd->prepare('SELECT * FROM UTILISATEURS_COLLECTIVITES WHERE ID_UTILISATEUR = ? AND CODE_COLLECTIVITE = ? AND DATE_FIN_VALIDITE IS NULL');
        $a->execute(array($id_utilisateur, $code_collectivite));
        $utilisateur = $a->fetch();

        if(!isset($utilisateur['ID'])) {
            $json = array(
                'status' => 'failed',
                'message' => 'Cet utilisateur n\'est pas lié à cette collectivité.'
            );
        }else {
            $json = array(
                'status' => 'success',
                'ID' => $utilisateur['ID'],
                'ID_UTILISATEUR' => $utilisateur['ID_UTILISATEUR'],
                'CODE_COLLECTIVITE' => $utilisateur['CODE_COLLECTIVITE']
            );
        }
        return $json;
    }


    public function liste_collectivites_utilisateur($id_utilisateur){
        $a = $this->bdd->prepare('SELECT * FROM UTILISATEURS_COLLECTIVITES WHERE ID_UTILISATEUR = ? AND DATE_FIN_VALIDITE IS NULL');
        $a->execute(array($id_utilisateur));
        $json = $a->fetchAll();

        return $json;
    }

    public function trouver_utilisateur_partenaire($id_utilisateur){
        $a = $this->bdd->prepare('SELECT * FROM UTILISATEURS_PARTENAIRES WHERE ID_UTILISATEUR = ? AND DATE_FIN_VALIDITE IS NULL');
        $a->execute(array($id_utilisateur));
        $utilisateur = $a->fetch();

        if(!isset($utilisateur['ID'])) {
            $json = array(
                'status' => 'failed',
                'message' => 'Cet utilisateur n\'est pas lié à ce partenaire.'
            );
        }else {
            $json = array(
                'status' => 'success',
                'ID' => $utilisateur['ID'],
                'ID_UTILISATEUR' => $utilisateur['ID_UTILISATEUR'],
                'CODE_PARTENAIRE' => $utilisateur['CODE_PARTENAIRE']
            );
        }
        return $json;
    }

    public function trouver_centre_coordination($user){
            $a = $this->bdd->prepare("SELECT A.ID_UTILISATEUR, C.CODE_AGENT AS CODE_AGENT, A.CODE_CENTRE,B.LIBELLE_CENTRE  FROM UTILISATEUR_CENTRE_COORDINATION A JOIN CENTRE_COORDINATION B ON A.CODE_CENTRE = B.CODE_CENTRE_COORDINATION JOIN UTILISATEURS C ON A.ID_UTILISATEUR = C.ID_UTILISATEUR AND A.DATE_FIN IS NULL AND C.ID_UTILISATEUR = ?");
            $a->execute(array($user));
            $json = $a->fetch();
            return $json;
    }
	
	public function mise_a_jour_derniere_connexion($id_utilisateur){
        $a = $this->bdd->prepare('UPDATE UTILISATEURS SET DERNIERE_CONNEXION = SYSDATE WHERE ID_UTILISATEUR = ?');
        $a->execute(array($id_utilisateur));
        $json = array(
            'status' => true,
            'message' => 'DATE DERNIERE CONNEXION MISE A JOUR AVEC SUCCES'
        );
        return $json;
    }
}