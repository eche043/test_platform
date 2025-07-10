<?php


class COLLECTIVITES extends BDD
{
    public function trouver($code_collectivite) {
        $a = $this->bdd->prepare('
        SELECT 
            CODE, 
            RAISON_SOCIALE, 
            COMMUNE, 
            ADRESSE_GEOGRAPHIQUE, 
            TELEPHONE, 
            EMAIL, 
            FAX, 
            CODE_OGD_COTISATIONS, 
            CODE_OGD_PRESTATIONS,
            LOGO 
        FROM 
            ECMU_COLLECTIVITES 
        WHERE 
            CODE = ?');
        $a->execute(array($code_collectivite));
        $json = $a->fetch();
        return $json;
    }

    public function trouver_populations_collectivite($code_collectivite){
        $a = $this->bdd->prepare('SELECT * FROM OGD_AFFILIATION_POPULATION WHERE CODE_COLLECTIVITE = ? ORDER BY DATE_REG');
        $a->execute(array($code_collectivite));
        $json = $a->fetchAll();
        return $json;

    }

    public function trouver_populations_collectivite_par_statut($code_collectivite,$statut){
        $a = $this->bdd->prepare('SELECT * FROM OGD_AFFILIATION_POPULATION WHERE CODE_COLLECTIVITE = ? AND STATUT_COLLECTIVITE = ? AND ROWNUM <= 10000 ORDER BY DATE_REG DESC, PAYEUR_NUM_MATRICULE DESC');
        $a->execute(array($code_collectivite,$statut));
        $json = $a->fetchAll();
        return $json;

    }
    public function total_populations_collectivite_par_statut($code_collectivite,$statut){
        $a = $this->bdd->prepare('SELECT count(ID) AS TOTAL FROM OGD_AFFILIATION_POPULATION WHERE CODE_COLLECTIVITE = ? AND STATUT_COLLECTIVITE = ?');
        $a->execute(array($code_collectivite,$statut));
        $json = $a->fetch();
        return $json;

    }

    public function trouver_population($id_population, $code_collectivite, $num_secu, $num_matricule_entreprise, $num_matricule_ogd, $code_ogd){
        if(!empty($id_population)){
            $a = $this->bdd->prepare('SELECT * FROM OGD_AFFILIATION_POPULATION WHERE ID = ?');
            $a->execute(array($id_population));
        }elseif(!empty($num_secu)){
            $a = $this->bdd->prepare('SELECT * FROM OGD_AFFILIATION_POPULATION WHERE BENEFICIAIRE_NUM_SECU = ?');
            $a->execute(array($num_secu));
        }else{
            if(!empty($num_matricule_ogd)){
            $a = $this->bdd->prepare('SELECT * FROM OGD_AFFILIATION_POPULATION WHERE CODE_OGD = ? AND BENEFICIAIRE_NUM_OGD = ?');
            $a->execute(array($code_ogd, $num_matricule_ogd));
            }else{
                $a = $this->bdd->prepare('SELECT * FROM OGD_AFFILIATION_POPULATION WHERE CODE_COLLECTIVITE = ? AND BENEFICIAIRE_NUM_MATRICULE = ?');
                $a->execute(array($code_collectivite, $num_matricule_entreprise));
            }
        }

        $json = $a->fetch();
        return $json;
    }

    public function ajouter_nouvelle_population($id_population, $code_ogd, $code_collectivite, $type_beneficiaire, $numero_secu_payeur, $numero_entreprise_payeur, $numero_matricule_payeur, $nom_payeur, $prenoms_payeur, $date_naissance_payeur, $numero_secu_benef, $numero_entreprise_benef, $numero_matricule_benef, $nom_benef, $prenoms_benef, $date_naissance_benef, $civile, $sexe, $lieu_naissance, $lieu_residence, $statut, $user_reg){
        $up = 0;
        $ins = 0;
        if(empty($id_population)){
            $trouver_population = $this->trouver_population(null, $code_collectivite, $numero_secu_benef, $numero_entreprise_benef, $numero_matricule_benef, $code_ogd);
            if(isset($trouver_population['ID'])){
                if($trouver_population['STATUT']==0){
                    if($trouver_population['CODE_COLLECTIVITE']!=$code_collectivite){
                        $ajout_mvt = $this->ajouter_mouvement_affiliation_population($trouver_population['ID'], null, $trouver_population['CODE_OGD'], $trouver_population['CODE_COLLECTIVITE'], $trouver_population['STATUT'], $user_reg);

                        $a = $this->bdd->prepare('UPDATE OGD_AFFILIATION_POPULATION SET CODE_OGD = ?, CODE_COLLECTIVITE = ?, TYPE = ?, PAYEUR_NUM_SECU = ?, PAYEUR_NUM_MATRICULE = ?, PAYEUR_NUM_OGD = ?, PAYEUR_NOM = ?, PAYEUR_PRENOMS = ?, PAYEUR_DATE_NAISSSANCE = ?, BENEFICIAIRE_NUM_SECU = ?, BENEFICIAIRE_NUM_MATRICULE = ?, BENEFICIAIRE_NUM_OGD = ?, BENEFICIAIRE_NOM = ?, BENEFICIAIRE_PRENOMS = ?, BENEFICIAIRE_DATE_NAISSANCE = ?, BENEFICIAIRE_CIVILITE = ?, BENEFICIAIRE_SEXE = ?, BENEFICIAIRE_LIEU_NAISSANCE = ?, BENEFICIAIRE_LIEU_RESIDENCE = ?, STATUT = ?, DATE_EDIT = SYSDATE, USER_EDIT = ? WHERE ID = ?');
                        $a->execute(array($code_ogd, $code_collectivite, $type_beneficiaire, $numero_secu_payeur, $numero_entreprise_payeur, $numero_matricule_payeur, $nom_payeur, $prenoms_payeur, $date_naissance_payeur, $numero_secu_benef, $numero_entreprise_benef, $numero_matricule_benef, $nom_benef, $prenoms_benef, $date_naissance_benef, $civile, $sexe, $lieu_naissance, $lieu_residence, $statut, $user_reg, $trouver_population['ID']));
                        $up++;
                        $last_id = $trouver_population['ID'];
                    }else{
                        $last_id = null;
                    }
                }else{
                    $a = $this->bdd->prepare('UPDATE OGD_AFFILIATION_POPULATION SET CODE_OGD = ?,CODE_COLLECTIVITE = ?, TYPE = ?, PAYEUR_NUM_SECU = ?, PAYEUR_NUM_MATRICULE = ?, PAYEUR_NUM_OGD = ?, PAYEUR_NOM = ?, PAYEUR_PRENOMS = ?, PAYEUR_DATE_NAISSSANCE = ?, BENEFICIAIRE_NUM_SECU = ?, BENEFICIAIRE_NUM_MATRICULE = ?, BENEFICIAIRE_NUM_OGD = ?, BENEFICIAIRE_NOM = ?, BENEFICIAIRE_PRENOMS = ?, BENEFICIAIRE_DATE_NAISSANCE = ?, BENEFICIAIRE_CIVILITE = ?, BENEFICIAIRE_SEXE = ?, BENEFICIAIRE_LIEU_NAISSANCE = ?, BENEFICIAIRE_LIEU_RESIDENCE = ?, STATUT = ?, DATE_EDIT = SYSDATE, USER_EDIT = ? WHERE ID = ?');
                    $a->execute(array($code_ogd, $code_collectivite, $type_beneficiaire, $numero_secu_payeur, $numero_entreprise_payeur, $numero_matricule_payeur, $nom_payeur, $prenoms_payeur, $date_naissance_payeur, $numero_secu_benef, $numero_entreprise_benef, $numero_matricule_benef, $nom_benef, $prenoms_benef, $date_naissance_benef, $civile, $sexe, $lieu_naissance, $lieu_residence, $statut, $user_reg, $trouver_population['ID']));
                    $up++;
                    $last_id = $trouver_population['ID'];
                }

            }else{
                $a = $this->bdd->prepare('INSERT INTO OGD_AFFILIATION_POPULATION(CODE_OGD, CODE_COLLECTIVITE, TYPE, PAYEUR_NUM_SECU, PAYEUR_NUM_MATRICULE, PAYEUR_NUM_OGD, PAYEUR_NOM, PAYEUR_PRENOMS, PAYEUR_DATE_NAISSSANCE, BENEFICIAIRE_NUM_SECU, BENEFICIAIRE_NUM_MATRICULE, BENEFICIAIRE_NUM_OGD, BENEFICIAIRE_NOM, BENEFICIAIRE_PRENOMS, BENEFICIAIRE_DATE_NAISSANCE, BENEFICIAIRE_CIVILITE, BENEFICIAIRE_SEXE, BENEFICIAIRE_LIEU_NAISSANCE, BENEFICIAIRE_LIEU_RESIDENCE, STATUT, USER_REG) VALUES (:CODE_OGD, :CODE_COLLECTIVITE, :TYPE, :PAYEUR_NUM_SECU, :PAYEUR_NUM_MATRICULE, :PAYEUR_NUM_OGD, :PAYEUR_NOM, :PAYEUR_PRENOMS, :PAYEUR_DATE_NAISSSANCE, :BENEFICIAIRE_NUM_SECU, :BENEFICIAIRE_NUM_MATRICULE, :BENEFICIAIRE_NUM_OGD, :BENEFICIAIRE_NOM, :BENEFICIAIRE_PRENOMS, :BENEFICIAIRE_DATE_NAISSANCE, :BENEFICIAIRE_CIVILITE, :BENEFICIAIRE_SEXE, :BENEFICIAIRE_LIEU_NAISSANCE, :BENEFICIAIRE_LIEU_RESIDENCE, :STATUT, :USER_REG)');
                $a->execute(array(
                    'CODE_OGD' => $code_ogd,
                    'CODE_COLLECTIVITE' => $code_collectivite,
                    'TYPE' => $type_beneficiaire,
                    'PAYEUR_NUM_SECU' => $numero_secu_payeur,
                    'PAYEUR_NUM_MATRICULE' => $numero_entreprise_payeur,
                    'PAYEUR_NUM_OGD' => $numero_matricule_payeur,
                    'PAYEUR_NOM' => $nom_payeur,
                    'PAYEUR_PRENOMS' => $prenoms_payeur,
                    'PAYEUR_DATE_NAISSSANCE' => $date_naissance_payeur,
                    'BENEFICIAIRE_NUM_SECU' => $numero_secu_benef,
                    'BENEFICIAIRE_NUM_MATRICULE' => $numero_entreprise_benef,
                    'BENEFICIAIRE_NUM_OGD' => $numero_matricule_benef,
                    'BENEFICIAIRE_NOM' => $nom_benef,
                    'BENEFICIAIRE_PRENOMS' => $prenoms_benef,
                    'BENEFICIAIRE_DATE_NAISSANCE' => $date_naissance_benef,
                    'BENEFICIAIRE_CIVILITE' => $civile,
                    'BENEFICIAIRE_SEXE' => $sexe,
                    'BENEFICIAIRE_LIEU_NAISSANCE' => $lieu_naissance,
                    'BENEFICIAIRE_LIEU_RESIDENCE' => $lieu_residence,
                    'STATUT' => $statut,
                    'USER_REG' =>$user_reg
                )) OR DIE (print_r(array('',$this->bdd->errorInfo())));
                $ins++;

                $b = $this->bdd->prepare('SELECT MAX(ID) AS "LAST_ID" FROM OGD_AFFILIATION_POPULATION');
                $b->execute(array());
                $last = $b->fetch();
                $last_id = $last['LAST_ID'];
            }
        }else{
            $a = $this->bdd->prepare('UPDATE OGD_AFFILIATION_POPULATION SET CODE_OGD = ?, TYPE = ?, PAYEUR_NUM_SECU = ?, PAYEUR_NUM_MATRICULE = ?, PAYEUR_NUM_OGD = ?, PAYEUR_NOM = ?, PAYEUR_PRENOMS = ?, PAYEUR_DATE_NAISSSANCE = ?, BENEFICIAIRE_NUM_SECU = ?, BENEFICIAIRE_NUM_MATRICULE = ?, BENEFICIAIRE_NUM_OGD = ?, BENEFICIAIRE_NOM = ?, BENEFICIAIRE_PRENOMS = ?, BENEFICIAIRE_DATE_NAISSANCE = ?, BENEFICIAIRE_CIVILITE = ?, BENEFICIAIRE_SEXE = ?, BENEFICIAIRE_LIEU_NAISSANCE = ?, BENEFICIAIRE_LIEU_RESIDENCE = ?,  STATUT = ?, DATE_EDIT = SYSDATE, USER_EDIT = ? WHERE ID = ?');
            $a->execute(array($code_ogd, $type_beneficiaire, $numero_secu_payeur, $numero_entreprise_payeur, $numero_matricule_payeur, $nom_payeur, $prenoms_payeur, $date_naissance_payeur, $numero_secu_benef, $numero_entreprise_benef, $numero_matricule_benef, $nom_benef, $prenoms_benef, $date_naissance_benef, $civile, $sexe, $lieu_naissance, $lieu_residence, $statut, $user_reg, $id_population));
            $up++;
            $last_id = $id_population;
        }
        $json = array(
            'status' => true,
            'message'=>array(
                'last_id'=>$last_id,
                'maj'=>$up,
                'insert'=>$ins
            )
        );
        return $json;
    }

    public function maj_statut_population($id_population, $statut,$user_reg){
        $a = $this->bdd->prepare('UPDATE OGD_AFFILIATION_POPULATION SET STATUT_COLLECTIVITE = ?, DATE_EDIT = SYSDATE, USER_EDIT = ? WHERE ID = ?');
        $a->execute(array($statut, $user_reg, $id_population));

        $json = array(
            'status' => true,
        );
        return $json;
    }

    public function ajouter_declaration_cotisation($id_population, $id_declaration, $annee, $mois, $code_collectivite, $code_ogd, $numero_secu_benef, $numero_secu_payeur, $user){
        $a = $this->bdd->prepare('INSERT INTO OGD_AFFILIATION_COTISATIONS(ID_POPULATION, ID_DECLARATION, CODE_OGD, CODE_COLLECTIVITE, ANNEE, MOIS, PAYEUR_NUM_SECU, BENEFICIAIRE_NUM_SECU, USER_REG) VALUES (:ID_POPULATION, :ID_DECLARATION, :CODE_OGD, :CODE_COLLECTIVITE, :ANNEE, :MOIS, :PAYEUR_NUM_SECU, :BENEFICIAIRE_NUM_SECU, :USER_REG)');
        $a->execute(array(
            'ID_POPULATION' => $id_population,
            'ID_DECLARATION' => $id_declaration,
            'CODE_OGD' => $code_ogd,
            'CODE_COLLECTIVITE' => $code_collectivite,
            'ANNEE' => $annee,
            'MOIS' => $mois,
            'PAYEUR_NUM_SECU' => $numero_secu_payeur,
            'BENEFICIAIRE_NUM_SECU' => $numero_secu_benef,
            'USER_REG' =>$user
        )) OR DIE (print_r(array($id_population,$this->bdd->errorInfo())));
        $json = array(
            'status' => true,
        );
        return $json;
    }

    public function ajouter_declaration_cotisation_collectivite($code_ogd, $code_collectivite, $occurrences, $annee, $mois, $user){
        $a = $this->bdd->prepare('INSERT INTO OGD_AFFILIATION_DECLARATION(CODE_OGD, CODE_COLLECTIVITE, OCCURRENCES, ANNEE, MOIS, USER_REG) VALUES (:CODE_OGD, :CODE_COLLECTIVITE, :OCCURRENCES, :ANNEE, :MOIS, :USER_REG)');
        $a->execute(array(
            'CODE_OGD' => $code_ogd,
            'CODE_COLLECTIVITE' => $code_collectivite,
            'OCCURRENCES' => $occurrences,
            'ANNEE' => $annee,
            'MOIS' => $mois,
            'USER_REG' =>$user
        )) OR DIE (print_r(array($code_ogd, $code_collectivite, $occurrences, $annee, $mois, $user,$this->bdd->errorInfo())));

        $json = array(
            'status' => true
        );
        return $json;
    }

    public function last_declaration(){
        $b = $this->bdd->prepare('SELECT MAX(TO_NUMBER(ID_DECLARATION))  AS "LAST_DECLARATION" FROM OGD_AFFILIATION_DECLARATION');
        $b->execute(array());
        $declaration = $b->fetch();
        $json = array(
            'status' => true,
            'LAST_DECLARATION' => $declaration['LAST_DECLARATION']
        );
        return $json;
    }
/*
    public function liste_des_declarations_cotisations($code_collectivite){
        $a = $this->bdd->prepare("SELECT ID_DECLARATION, CODE_OGD, CODE_COLLECTIVITE, OCCURRENCES, MOIS, ANNEE, TO_CHAR(DATE_REG,'dd/mm/yyyy HH24:mi') as DATE_REG, USER_REG FROM OGD_AFFILIATION_DECLARATION WHERE CODE_COLLECTIVITE = ? ORDER BY DATE_REG DESC");
        $a->execute(array($code_collectivite));
        $json = $a->fetchAll();
        return $json;
    }*/

    public function liste_des_declarations_cotisations($code_ogd, $code_collectivite){
        $a = $this->bdd->prepare("SELECT A.DATE_REG AS DATE_DECLARATION, A.ID_DECLARATION, A.CODE_OGD, A.CODE_COLLECTIVITE, A.ANNEE, A.MOIS, COUNT(B.ID_POPULATION) AS EFFECTIF, (COUNT(B.ID_POPULATION) * 1000) AS MONTANT FROM OGD_AFFILIATION_DECLARATION A JOIN OGD_AFFILIATION_COTISATIONS B ON A.CODE_OGD = B.CODE_OGD AND A.CODE_COLLECTIVITE = B.CODE_COLLECTIVITE AND A.ANNEE = B.ANNEE AND A.MOIS = B.MOIS AND A.CODE_OGD = ? AND A.CODE_COLLECTIVITE = ? GROUP BY A.DATE_REG, A.ID_DECLARATION, A.CODE_OGD, A.CODE_COLLECTIVITE, A.ANNEE, A.MOIS ORDER BY A.ANNEE DESC, MOIS DESC");
        $a->execute(array($code_ogd, $code_collectivite));
        $json = $a->fetchAll();
        return $json;
    }

    public function liste_des_declarations_cotisations_annee($code_ogd, $code_collectivite, $annee){
        $a = $this->bdd->prepare("SELECT A.DATE_REG AS DATE_DECLARATION, B.ID_DECLARATION, B.ID_PAIEMENT, A.CODE_OGD, A.CODE_COLLECTIVITE, A.ANNEE, A.MOIS, COUNT(B.ID_POPULATION) AS EFFECTIF, (COUNT(B.ID_POPULATION) * 1000) AS MONTANT FROM OGD_AFFILIATION_DECLARATION A JOIN OGD_AFFILIATION_COTISATIONS B ON A.CODE_OGD = B.CODE_OGD AND A.CODE_COLLECTIVITE = B.CODE_COLLECTIVITE AND A.ID_DECLARATION = B.ID_DECLARATION AND A.ANNEE = B.ANNEE AND A.MOIS = B.MOIS AND A.CODE_OGD = ? AND A.CODE_COLLECTIVITE = ? AND A.ANNEE = ? GROUP BY A.DATE_REG, B.ID_DECLARATION, B.ID_PAIEMENT, A.CODE_OGD, A.CODE_COLLECTIVITE, A.ANNEE, A.MOIS ORDER BY A.ANNEE DESC, MOIS DESC");
        $a->execute(array($code_ogd, $code_collectivite, $annee));
        $json = $a->fetchAll();
        return $json;
    }

    public function trouver_cotisation($mois, $annee, $id_population, $id_declaration){
        $a = $this->bdd->prepare('SELECT * FROM OGD_AFFILIATION_COTISATIONS WHERE ANNEE = ? AND MOIS = ? AND ID_POPULATION = ? AND ID_DECLARATION = ? ');
        $a->execute(array($annee,$mois,$id_population, $id_declaration));
        $json = $a->fetch();
        return $json;
    }

    public function trouver_population_pour_declarations($mois, $annee, $collectivite){
        $a = $this->bdd->prepare('SELECT * FROM OGD_AFFILIATION_POPULATION WHERE CODE_COLLECTIVITE = ? AND STATUT_COLLECTIVITE = ? AND ID NOT IN (SELECT ID_POPULATION FROM OGD_AFFILIATION_COTISATIONS oac WHERE MOIS= ? AND ANNEE = ? AND CODE_COLLECTIVITE = ?)');
        $a->execute(array($collectivite, 1, $mois, $annee, $collectivite));
        $json = $a->fetchAll();
        return $json;
    }

    public function ajouter_mouvement_affiliation_population($id_population, $id_fichier, $code_ogd, $code_collectivite, $statut, $user){
        $a = $this->bdd->prepare('INSERT INTO LOG_HISTORIQUE_MOUVEMENT_OGD_AFF_POPULATION(ID_POPULATION, ID_FICHIER, CODE_OGD, CODE_COLLECTIVITE, STATUT, USER_REG) VALUES (:ID_POPULATION, :ID_FICHIER, :CODE_OGD, :CODE_COLLECTIVITE, :STATUT, :USER_REG)');
        $a->execute(array(
            'ID_POPULATION' => $id_population,
            'ID_FICHIER' => $id_fichier,
            'CODE_OGD' => $code_ogd,
            'CODE_COLLECTIVITE' => $code_collectivite,
            'STATUT' => $statut,
            'USER_REG' =>$user
        )) OR DIE (print_r(array($id_population, $id_fichier, $code_ogd, $code_collectivite, $statut, $user,$this->bdd->errorInfo())));
        $json = array(
            'status' => true
        );
        return $json;
    }
/*
    public function trouver_declaration($id_declaration){
        $a = $this->bdd->prepare("SELECT ID_DECLARATION, CODE_OGD, CODE_COLLECTIVITE, OCCURRENCES, MOIS, ANNEE, TO_CHAR(DATE_REG,'dd/mm/yyyy HH24:mi') as DATE_REG, USER_REG FROM OGD_AFFILIATION_DECLARATION WHERE ID_DECLARATION = ?");
        $a->execute(array($id_declaration));
        $json = $a->fetch();
        return $json;
    }*/

    public function trouver_declaration($id_declaration){
        $a = $this->bdd->prepare("SELECT A.DATE_REG AS DATE_DECLARATION, A.ID_DECLARATION, A.CODE_OGD, A.CODE_COLLECTIVITE, A.ANNEE, A.MOIS, COUNT(B.ID_POPULATION) AS EFFECTIF, (COUNT(B.ID_POPULATION) * 1000) AS MONTANT FROM OGD_AFFILIATION_DECLARATION A JOIN OGD_AFFILIATION_COTISATIONS B ON A.CODE_OGD = B.CODE_OGD AND A.ID_DECLARATION = B.ID_DECLARATION AND A.CODE_COLLECTIVITE = B.CODE_COLLECTIVITE AND A.ANNEE = B.ANNEE AND A.MOIS = B.MOIS AND A.ID_DECLARATION = ? GROUP BY A.DATE_REG, A.ID_DECLARATION, A.CODE_OGD, A.CODE_COLLECTIVITE, A.ANNEE, A.MOIS ORDER BY A.ANNEE DESC, MOIS DESC");
        $a->execute(array($id_declaration));
        $json = $a->fetch();
        return $json;
    }

    public function verification_informations_population($ligne, $code_collectivite, $type_beneficiaire, $numero_secu_payeur, $numero_entreprise_payeur, $numero_matricule_payeur, $nom_payeur, $prenoms_payeur, $date_naissance_payeur, $numero_secu_benef, $numero_entreprise_benef, $numero_matricule_benef, $nom_benef, $prenoms_benef, $date_naissance_benef, $civile, $sexe, $lieu_naissance, $lieu_residence){
        $message = '';
        $erreur = 0;

        if(empty($type_beneficiaire)){
            $message = $message."Ligne N°".$ligne.": LE TYPE BENEFICIAIRE N'EST PAS DEFINI.\n";
            $erreur++;
        }else{
            if($type_beneficiaire !='T' && $type_beneficiaire !='E' && $type_beneficiaire !='C' && $type_beneficiaire !='A'){
                $message = $message. "Ligne N°".$ligne.": LE TYPE BENEFICIAIRE RENSEIGNE N'EST PAS CONFORME A CELUI ATTENDU.\n";
                $erreur++;
            }
        }

        if((empty($numero_secu_payeur) && empty($numero_entreprise_payeur) && empty($numero_matricule_payeur)) &&  (empty($numero_secu_benef) && empty($numero_entreprise_benef) && empty($numero_matricule_benef))){
            $message = $message. "Ligne N°".$ligne.": PRIERE RENSEIGNER AU MOINS UN NUMERO.\n";
            $erreur++;
        }

        if(empty($nom_benef) || empty($prenoms_benef) || empty($date_naissance_benef)){
            $message = $message. "Ligne N°".$ligne.": LE NOM, LE(S) PRENOM(S) ET LA DATE DE NAISSANCE DU BENEFICIAIRE SONT OBLIGATOIRES. PRIERE LES RENSEIGNER.\n";
            $erreur++;
        }
        if($civile != 'M' && $civile != 'MME' && $civile != 'MLE'){
            $message = $message. "Ligne N°".$ligne.": LA CIVILITE RENSEIGNEE N'EST PAS CONFORME A CELLE ATTENDUE. \n";
            $erreur++;
        }
        if($sexe != 'M' && $sexe != 'F'){
            $message = $message. "Ligne N°".$ligne.": LE GENRE RENSEIGNE N'EST PAS CONFORME A CELUI ATTENDU.\n";
            $erreur++;
        }

        if($type_beneficiaire=='T'){
            if(!empty($numero_secu_payeur) && !empty($numero_secu_benef) && $numero_secu_payeur!=$numero_secu_benef){
                $message = $message."Ligne N°".$ligne.": POUR LE TYPE BENENFICIAIRE TRAVAILLEUR(T), LE NUMERO SECU PAYEUR DOIT EST EGAL AU NUMERO SECU BENEFICIAIRE.\n";
                $erreur++;
            }
            if(!empty($numero_matricule_payeur) && !empty($numero_matricule_benef) && $numero_matricule_payeur!=$numero_matricule_benef){
                $message = $message."Ligne N°".$ligne.": POUR LE TYPE BENENFICIAIRE TRAVAILLEUR(T), LE MATRICULE PAYEUR OGD DOIT EST EGAL AU NUMERO MATRICULE BENEFICIAIRE OGD.\n";
                $erreur++;
            }
            if(!empty($numero_entreprise_payeur) && !empty($numero_entreprise_benef) && $numero_entreprise_payeur!=$numero_entreprise_benef){
                $message = $message."Ligne N°".$ligne.": POUR LE TYPE BENENFICIAIRE TRAVAILLEUR(T), LE MATRICULE ENTREPRISE PAYEUR DOIT EST EGAL AU NUMERO MATRICULE ENTREPRISE BENEFICIAIRE\n.";
                $erreur++;
            }
        }else{
            if(!empty($numero_secu_payeur) && !empty($numero_secu_benef) && $numero_secu_payeur==$numero_secu_benef){
                $message = $message."Ligne N°".$ligne.": POUR LE TYPE BENENFICIAIRE AYANT-DROIT(CONJOINT OU ENFANT), LE NUMERO SECU PAYEUR DOIT EST DIFFERENT DU NUMERO SECU BENEFICIAIRE. SI L'INFORMATION EST INCONNUE, PRIERE LAISSER LE CHAMP VIDE.\n";
                $erreur++;
            }
            if(!empty($numero_matricule_payeur) && !empty($numero_matricule_benef) && $numero_matricule_payeur==$numero_matricule_benef){
                $message = $message."Ligne N°".$ligne.": POUR LE TYPE BENENFICIAIRE AYANT-DROIT(CONJOINT OU ENFANT), LE MATRICULE PAYEUR OGD DOIT EST DIFFERENT DU NUMERO MATRICULE BENEFICIAIRE OGD. SI L'INFORMATION EST INCONNUE, PRIERE LAISSER LE CHAMP VIDE.\n";
                $erreur++;
            }
            if(!empty($numero_entreprise_payeur) && !empty($numero_entreprise_benef) && $numero_entreprise_payeur==$numero_entreprise_benef){
                $message = $message."Ligne N°".$ligne.": POUR LE TYPE BENENFICIAIRE AYANT-DROIT(CONJOINT OU ENFANT), LE MATRICULE ENTREPRISE PAYEUR DOIT EST DIFFERENT DU NUMERO MATRICULE ENTREPRISE BENEFICIAIRE. SI L'INFORMATION EST INCONNUE, PRIERE LAISSER LE CHAMP VIDE.\n";
                $erreur++;
            }
        }
        $a = $this->bdd->prepare('SELECT * FROM OGD_AFFILIATION_POPULATION WHERE BENEFICIAIRE_NUM_SECU = ?');
        $a->execute(array($numero_secu_benef));
        $trouver_pop_par_secu = $a->fetch();
        if(isset($trouver_pop_par_secu['ID'])){
            if($trouver_pop_par_secu['CODE_COLLECTIVITE']!=$code_collectivite AND $trouver_pop_par_secu['STATUT']==1){
                $message = $message."Ligne N°".$ligne.": CE NUMERO SECU A DEJA ETE UTILISE DANS UNE AUTRE COLLECTIVITE. PRIERE CONFIRMER AVEC LA PERSONNE CONCERNEE.\n";
                $erreur++;
            }
        }

        $json = array(
            'status' => true,
            'erreur' => $erreur,
            'message' => $message
        );
        return $json;
    }


    public function lister_annees_declarations($code_ogd, $code_collectivite) {
        //$a = $this->bdd->prepare("SELECT DISTINCT ANNEE FROM OGD_AFFILIATION_DECLARATION oad WHERE CODE_OGD = ? AND CODE_COLLECTIVITE = ? ORDER BY ANNEE DESC");
        $a = $this->bdd->prepare("SELECT A.ANNEE FROM OGD_AFFILIATION_DECLARATION A JOIN OGD_AFFILIATION_COTISATIONS B ON A.CODE_OGD = B.CODE_OGD AND A.CODE_COLLECTIVITE = B.CODE_COLLECTIVITE AND A.CODE_OGD = ? AND A.CODE_COLLECTIVITE = ? GROUP BY A.ANNEE ORDER BY A.ANNEE DESC");
        $a->execute(array($code_ogd,$code_collectivite));
        $json = $a->fetchAll();
        return $json;
    }

    public function moteur_recherche_population_collectivite($code_ogd, $code_collectivite, $num_matricule, $num_secu, $nom_prenom) {
        if(!empty($num_matricule) && !empty($num_secu) && !empty($num_secu) ){
            $a = $this->bdd->prepare("SELECT ID,TYPE, BENEFICIAIRE_NUM_SECU AS NUM_SECU, PAYEUR_NUM_SECU, PAYEUR_NUM_MATRICULE, BENEFICIAIRE_NUM_MATRICULE AS NUM_MATRICULE, BENEFICIAIRE_CIVILITE AS CIVILITE, BENEFICIAIRE_NOM AS NOM, BENEFICIAIRE_PRENOMS AS PRENOMS, BENEFICIAIRE_DATE_NAISSANCE AS DATE_NAISSANCE, BENEFICIAIRE_SEXE AS SEXE FROM OGD_AFFILIATION_POPULATION WHERE CODE_OGD = ? AND CODE_COLLECTIVITE = ? AND (BENEFICIAIRE_NUM_SECU LIKE ? OR BENEFICIAIRE_NUM_SECU IS NULL OR PAYEUR_NUM_SECU IS NULL OR PAYEUR_NUM_SECU LIKE ?) AND (PAYEUR_NUM_MATRICULE LIKE ? OR PAYEUR_NUM_OGD LIKE ?) AND UPPER((BENEFICIAIRE_NOM||' '||BENEFICIAIRE_PRENOMS)) LIKE UPPER(?) ORDER BY PAYEUR_NUM_MATRICULE, BENEFICIAIRE_NOM, BENEFICIAIRE_PRENOMS");
            $a->execute(array($code_ogd, $code_collectivite, '%'.$num_secu.'%', '%'.$num_secu.'%','%'.$num_matricule.'%','%'.$num_matricule.'%','%'.$nom_prenom.'%'));
        }
        elseif(empty($num_matricule) && !empty($num_secu) && !empty($nom_prenom) ){
            $a = $this->bdd->prepare("SELECT ID,TYPE, BENEFICIAIRE_NUM_SECU AS NUM_SECU, PAYEUR_NUM_SECU, PAYEUR_NUM_MATRICULE, BENEFICIAIRE_NUM_MATRICULE AS NUM_MATRICULE, BENEFICIAIRE_CIVILITE AS CIVILITE, BENEFICIAIRE_NOM AS NOM, BENEFICIAIRE_PRENOMS AS PRENOMS, BENEFICIAIRE_DATE_NAISSANCE AS DATE_NAISSANCE, BENEFICIAIRE_SEXE AS SEXE FROM OGD_AFFILIATION_POPULATION WHERE CODE_OGD = ? AND CODE_COLLECTIVITE = ? AND (BENEFICIAIRE_NUM_SECU LIKE ? OR BENEFICIAIRE_NUM_SECU IS NULL OR PAYEUR_NUM_SECU IS NULL OR PAYEUR_NUM_SECU LIKE ?) AND (PAYEUR_NUM_MATRICULE LIKE ? OR PAYEUR_NUM_OGD LIKE ?) AND UPPER((BENEFICIAIRE_NOM||' '||BENEFICIAIRE_PRENOMS)) LIKE UPPER(?) ORDER BY PAYEUR_NUM_MATRICULE, BENEFICIAIRE_NOM, BENEFICIAIRE_PRENOMS");
            $a->execute(array($code_ogd, $code_collectivite, '%'.$num_secu.'%', '%'.$num_secu.'%','%'.$num_matricule.'%','%'.$num_matricule.'%','%'.$nom_prenom.'%'));
        }
        elseif(!empty($num_matricule) && empty($num_secu) && !empty($nom_prenom) ){
            $a = $this->bdd->prepare("SELECT ID,TYPE, BENEFICIAIRE_NUM_SECU AS NUM_SECU, PAYEUR_NUM_SECU, PAYEUR_NUM_MATRICULE, BENEFICIAIRE_NUM_MATRICULE AS NUM_MATRICULE, BENEFICIAIRE_CIVILITE AS CIVILITE, BENEFICIAIRE_NOM AS NOM, BENEFICIAIRE_PRENOMS AS PRENOMS, BENEFICIAIRE_DATE_NAISSANCE AS DATE_NAISSANCE, BENEFICIAIRE_SEXE AS SEXE FROM OGD_AFFILIATION_POPULATION WHERE CODE_OGD = ? AND CODE_COLLECTIVITE = ? AND (BENEFICIAIRE_NUM_SECU LIKE ? OR BENEFICIAIRE_NUM_SECU IS NULL OR PAYEUR_NUM_SECU IS NULL OR PAYEUR_NUM_SECU LIKE ?) AND (PAYEUR_NUM_MATRICULE LIKE ? OR PAYEUR_NUM_OGD LIKE ?) AND UPPER((BENEFICIAIRE_NOM||' '||BENEFICIAIRE_PRENOMS)) LIKE UPPER(?) ORDER BY PAYEUR_NUM_MATRICULE, BENEFICIAIRE_NOM, BENEFICIAIRE_PRENOMS");
            $a->execute(array($code_ogd, $code_collectivite, '%'.$num_secu.'%', '%'.$num_secu.'%','%'.$num_matricule.'%','%'.$num_matricule.'%','%'.$nom_prenom.'%'));
        }
        elseif(!empty($num_matricule) && !empty($num_secu) && empty($nom_prenom) ){
            $a = $this->bdd->prepare("SELECT ID,TYPE, BENEFICIAIRE_NUM_SECU AS NUM_SECU, PAYEUR_NUM_SECU, PAYEUR_NUM_MATRICULE, BENEFICIAIRE_NUM_MATRICULE AS NUM_MATRICULE, BENEFICIAIRE_CIVILITE AS CIVILITE, BENEFICIAIRE_NOM AS NOM, BENEFICIAIRE_PRENOMS AS PRENOMS, BENEFICIAIRE_DATE_NAISSANCE AS DATE_NAISSANCE, BENEFICIAIRE_SEXE AS SEXE FROM OGD_AFFILIATION_POPULATION WHERE CODE_OGD = ? AND CODE_COLLECTIVITE = ? AND (BENEFICIAIRE_NUM_SECU LIKE ? OR BENEFICIAIRE_NUM_SECU IS NULL OR PAYEUR_NUM_SECU IS NULL OR PAYEUR_NUM_SECU LIKE ?) AND (PAYEUR_NUM_MATRICULE LIKE ? OR PAYEUR_NUM_OGD LIKE ?) AND UPPER((BENEFICIAIRE_NOM||' '||BENEFICIAIRE_PRENOMS)) LIKE UPPER(?) ORDER BY PAYEUR_NUM_MATRICULE, BENEFICIAIRE_NOM, BENEFICIAIRE_PRENOMS");
            $a->execute(array($code_ogd, $code_collectivite, '%'.$num_secu.'%', '%'.$num_secu.'%','%'.$num_matricule.'%','%'.$num_matricule.'%','%'.$nom_prenom.'%'));
        }
        elseif(empty($num_matricule) && empty($num_secu) && !empty($nom_prenom) ){
            $a = $this->bdd->prepare("SELECT ID,TYPE, BENEFICIAIRE_NUM_SECU AS NUM_SECU, PAYEUR_NUM_SECU, PAYEUR_NUM_MATRICULE, BENEFICIAIRE_NUM_MATRICULE AS NUM_MATRICULE, BENEFICIAIRE_CIVILITE AS CIVILITE, BENEFICIAIRE_NOM AS NOM, BENEFICIAIRE_PRENOMS AS PRENOMS, BENEFICIAIRE_DATE_NAISSANCE AS DATE_NAISSANCE, BENEFICIAIRE_SEXE AS SEXE FROM OGD_AFFILIATION_POPULATION WHERE CODE_OGD = ? AND CODE_COLLECTIVITE = ? AND (BENEFICIAIRE_NUM_SECU LIKE ? OR BENEFICIAIRE_NUM_SECU IS NULL OR PAYEUR_NUM_SECU IS NULL OR PAYEUR_NUM_SECU LIKE ?) AND (PAYEUR_NUM_MATRICULE LIKE ? OR PAYEUR_NUM_OGD LIKE ?) AND UPPER((BENEFICIAIRE_NOM||' '||BENEFICIAIRE_PRENOMS)) LIKE UPPER(?) ORDER BY PAYEUR_NUM_MATRICULE, BENEFICIAIRE_NOM, BENEFICIAIRE_PRENOMS");
            $a->execute(array($code_ogd, $code_collectivite, '%'.$num_secu.'%', '%'.$num_secu.'%','%'.$num_matricule.'%','%'.$num_matricule.'%','%'.$nom_prenom.'%'));
        }
        elseif(!empty($num_matricule) && empty($num_secu) && empty($nom_prenom) ){
            $a = $this->bdd->prepare("SELECT ID,TYPE, BENEFICIAIRE_NUM_SECU AS NUM_SECU, PAYEUR_NUM_SECU, PAYEUR_NUM_MATRICULE, BENEFICIAIRE_NUM_MATRICULE AS NUM_MATRICULE, BENEFICIAIRE_CIVILITE AS CIVILITE, BENEFICIAIRE_NOM AS NOM, BENEFICIAIRE_PRENOMS AS PRENOMS, BENEFICIAIRE_DATE_NAISSANCE AS DATE_NAISSANCE, BENEFICIAIRE_SEXE AS SEXE FROM OGD_AFFILIATION_POPULATION WHERE CODE_OGD = ? AND CODE_COLLECTIVITE = ? AND (PAYEUR_NUM_MATRICULE LIKE ? OR PAYEUR_NUM_OGD LIKE ?) ORDER BY PAYEUR_NUM_MATRICULE, BENEFICIAIRE_NOM, BENEFICIAIRE_PRENOMS");
            $a->execute(array($code_ogd, $code_collectivite,'%'.$num_matricule.'%','%'.$num_matricule.'%'));
        }
        elseif(empty($num_matricule) && !empty($num_secu) && empty($nom_prenom) ){
            $a = $this->bdd->prepare("SELECT ID,TYPE, BENEFICIAIRE_NUM_SECU AS NUM_SECU, PAYEUR_NUM_SECU, PAYEUR_NUM_MATRICULE, BENEFICIAIRE_NUM_MATRICULE AS NUM_MATRICULE, BENEFICIAIRE_CIVILITE AS CIVILITE, BENEFICIAIRE_NOM AS NOM, BENEFICIAIRE_PRENOMS AS PRENOMS, BENEFICIAIRE_DATE_NAISSANCE AS DATE_NAISSANCE, BENEFICIAIRE_SEXE AS SEXE FROM OGD_AFFILIATION_POPULATION WHERE CODE_OGD = ? AND CODE_COLLECTIVITE = ? AND (BENEFICIAIRE_NUM_SECU LIKE ?  OR PAYEUR_NUM_SECU LIKE ?) ORDER BY PAYEUR_NUM_MATRICULE, BENEFICIAIRE_NOM, BENEFICIAIRE_PRENOMS");
            $a->execute(array($code_ogd, $code_collectivite, '%'.$num_secu.'%', '%'.$num_secu.'%'));
        }
        $json = $a->fetchAll();
        return $json;
    }
}