<?php


class PROFESSIONNELSANTE extends BDD
{
    public function lister_ets_ps($code_ets){
        $a = $this->bdd->prepare('
            SELECT DISTINCT(FS.PS) AS CODE_PS,
            ECMU_REF_PROFESSIONNEL_SANTE.NOM AS NOM,
            ECMU_REF_PROFESSIONNEL_SANTE.PRENOM AS PRENOM 
            FROM 
            ECMU_REF_PROFESSIONNEL_SANTE,
            FS WHERE FS.PS = ECMU_REF_PROFESSIONNEL_SANTE.INP 
            AND FS.ETABLISSEMENT = ?');
        $a->execute(array($code_ets));
        $json = $a->fetchAll();
        return $json;
    }

    public function trouver_ps_a_date_soins($code_ets,$code_ps,$date_soins) {

        $query = '
        SELECT 
            A.INP AS CODE_PS,
            A.NOM,
            A.PRENOM,
            A.LIBELLE_SPECIALITE AS CODE_SPECIALITE 
        FROM 
            ecmu_ref_professionnel_sante A,
            ps_ets B 
        WHERE 
        B.CODE_ETS = ?  
        AND B.PS = A.INP 
        AND B.STATUT = ? 
        AND A.INP = ? 
        AND (A.DATE_DEBUT_VALIDITE <= TO_DATE(?) 
        AND A.DATE_FIN_VALIDITE 
        IS NULL OR A.DATE_FIN_VALIDITE >= TO_DATE(?))
        ';

        $a = $this->bdd->prepare($query);
        $a->execute(array($code_ets,1,$code_ps,$date_soins,$date_soins));
        $ps_existe = count($a->fetchAll());
        if($ps_existe != 0) {
            $ps = $a->fetch();

            $b = $this->bdd->prepare('SELECT LIBELLE FROM ECMU_REF_SPECIALITES_MEDICALES WHERE CODE = ?');
            $b->execute(array($ps['CODE_SPECIALITE']));
            $specialite = $b->fetch();

            $json = array(
                'status' => true,
                'nom_prenom' => $ps['NOM'].' '.$ps['PRENOM'],
                'code_specialite' => $ps['CODE_SPECIALITE'],
                'libelle_specialite' => $specialite['LIBELLE']
            );

        }else {
            $json = array(
                'status' => false,
                'message' => 'Le code PS: '.$code_ps.' est incorrect pour la date et l\'établissement sélectionnés, veuillez reéssayer SVP.'
            );
        }

        return $json;
    }
}