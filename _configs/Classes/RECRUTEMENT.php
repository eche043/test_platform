<?php

    class RECRUTEMENT extends BDD
    {
        public function editer_fichier_agac_comptes($telephone,$nom,$prenoms,$statut){
            $a = $this->bdd->prepare('
                INSERT INTO TB_RECRUTEMENT_AGAC_COMPTES (NUMERO_TELEPHONE,NOM,PRENOMS,STATUT) VALUES (:NUMERO_TELEPHONE,:NOM,:PRENOMS,:STATUT)
            ');
            $a->execute(array(
                'NUMERO_TELEPHONE'=>$telephone,
                'NOM'=>$nom,
                'PRENOMS'=>$prenoms,
                'STATUT'=>$statut))OR DIE(print_r($a->errorInfo()[2]));
            $json = array(
                'status' => true,
                'message' => 'Insertion effectuée avec succès...'
            );
            return $json;

        }
        public function editer_fichier_agac_centre($nom,$prenoms,$localite,$centre,$telephone,$centre_coordination_rattache,$nom_prenoms_coordinateurs,$numero_telephone_coordinateurs){
            $a = $this->bdd->prepare('
                INSERT INTO TB_RECRUTEMENT_AGAC_CENTRES (NOM,PRENOMS,LOCALITE,STRUCTURE_SANITAIRE,NUMERO_TELEPHONE,CENTRE_COORDINATION_RATTACHE,NOM_PRENOMS_COORDINATEUR,NUMERO_TELEPHONE_COORDINATEUR) 
                VALUES (:NOM,:PRENOMS,:LOCALITE,:STRUCTURE_SANITAIRE,:NUMERO_TELEPHONE,:CENTRE_COORDINATION_RATTACHE,:NOM_PRENOMS_COORDINATEUR,:NUMERO_TELEPHONE_COORDINATEUR)
            ');
            $a->execute(array(
                'NOM'=>$nom,
                'PRENOMS'=>$prenoms,
                'LOCALITE'=>$localite,
                'STRUCTURE_SANITAIRE'=>$centre,
                'NUMERO_TELEPHONE'=>$telephone,
                'CENTRE_COORDINATION_RATTACHE'=>$centre_coordination_rattache,
                'NOM_PRENOMS_COORDINATEUR'=>$nom_prenoms_coordinateurs,
                'NUMERO_TELEPHONE_COORDINATEUR'=>$numero_telephone_coordinateurs
            ))OR DIE('Error Insertion fichier agac - Recrutement ');
            $json = array(
                'status' => true,
                'message' => 'Insertion effectuée avec succès...'
            );
            return $json;

        }
        public function trouver_code_otp($id_compte){
            $a = $this->bdd->prepare('SELECT * FROM TB_RECRUTEMENT_AGAC_OTP WHERE ID_COMPTE= ? AND DATE_FIN IS NULL');
            $a->execute(array($id_compte));
            return $a->fetch();

        }
        public function editer_code_otp($id_compte,$code_otp,$date_debut,$date_fin){
            $trouver_code_otp = $this->trouver_code_otp($id_compte);
            if($trouver_code_otp){
                $a = $this->bdd->prepare("
                UPDATE TB_RECRUTEMENT_AGAC_OTP SET DATE_FIN = ? WHERE ID_COMPTE = ? AND DATE_FIN IS NULL
                ");
                $a->execute(array($date_fin,$id_compte));
                $json = array(
                    'status' => 'success',
                    'message' => 'Modification effectuée avec succès.'
                );
            }else{
                $a = $this->bdd->prepare("
                INSERT INTO TB_RECRUTEMENT_AGAC_OTP (ID_COMPTE, CODE_OTP,DATE_DEBUT) 
                VALUES (:ID_COMPTE, :CODE_OTP,:DATE_DEBUT)
                ");
                $a->execute(array(
                    'ID_COMPTE'=>$id_compte,
                    'CODE_OTP'=>$code_otp,
                    'DATE_DEBUT'=>$date_debut
                ))OR DIE('Error Insertion code_otp - Recrutement ');
                $json = array(
                    'status' => "success",
                    'message' => 'Insertion effectuée avec succès...'
                );
            }

            return $json;

        }

        public function editer_photo($id_compte, $photo_base64,$type_photo) {
            try {
                $sql = "INSERT INTO TB_RECRUTEMENT_AGAC_PHOTOS (ID_COMPTE, PHOTO,TYPE_PHOTO) 
                VALUES (:ID_COMPTE,:PHOTO,:TYPE_PHOTO)";

                // Préparer la requête
                $a = $this->bdd->prepare($sql);

                // Lier les paramètres
                $a->bindParam(':ID_COMPTE', $id_compte, PDO::PARAM_INT);
                $a->bindParam(':PHOTO', $photo_base64, PDO::PARAM_STR, strlen($photo_base64));  // Paramètre pour données BLOB
                    $a->bindParam(':TYPE_PHOTO', $type_photo, PDO::PARAM_STR,strlen($type_photo));  // Paramètre pour données BLOB

                //var_dump($photo_base64);
                    // Exécuter la requête
                $a->execute();

                // Fermer le flux
                //fclose($photo_base64);


                $json = array(
                    'status' => "success",
                    'message' => 'Insertion effectuée avec succès...'
                );
                return $json;
            } catch (PDOException $e) {
                // En cas d'erreur, capturer l'exception et afficher l'erreur
                die('Error Insertion photo AGAC - Recrutement: ' . $e->getMessage());
            }
        }

        public function modifier_photo($photo_base64,$type_photo,$id_compte) {
            try {
                $sql = "UPDATE TB_RECRUTEMENT_AGAC_PHOTOS 
        SET PHOTO = :photo, TYPE_PHOTO = :type_photo,DATE_MODIFICATION = SYSDATE
        WHERE ID_COMPTE = :id_compte";
                // Préparer la requête
                $a = $this->bdd->prepare($sql);

                // Lier les paramètres
                $a->bindParam(':PHOTO', $photo_base64, PDO::PARAM_STR, strlen($photo_base64));  // Paramètre pour données BLOB
                $a->bindParam(':TYPE_PHOTO', $type_photo, PDO::PARAM_STR,strlen($type_photo));  // Paramètre pour données BLOB
                $a->bindParam(':ID_COMPTE', $id_compte, PDO::PARAM_INT);
                $a->execute();
                $json = array(
                    'status' => "success",
                    'message' => 'Insertion effectuée avec succès...'
                );
                return $json;
            } catch (PDOException $e) {
                // En cas d'erreur, capturer l'exception et afficher l'erreur
                die('Error Insertion photo AGAC - Recrutement: ' . $e->getMessage());
            }
        }

        public function verif_situation_matrimoniale($idCompte){
            $a = $this->bdd->prepare(
                'SELECT * FROM TB_RECRUTEMENT_AGAC_INFOS_BIOGRAPHIQUES WHERE ID_COMPTE = ? AND SITUATION_MATRIMONIALE IN (?, ?)'
            );
            $a->execute(array($idCompte, 'CEL', 'DIV'));
            $json = $a->fetch();
            return $json;
        }

        public function connection_otp($id_compte,$code_otp){
            $a = $this->bdd->prepare(
                'SELECT * FROM TB_RECRUTEMENT_AGAC_OTP where ID_COMPTE = ? AND CODE_OTP = ? AND DATE_FIN IS NULL'
            );
            $a->execute(array($id_compte,$code_otp));
            $json = $a->fetch();
            return $json;
        }

        public function update_date_fin_otp($date_fin,$id_compte){
            $a = $this->bdd->prepare(
                'UPDATE TB_RECRUTEMENT_AGAC_OTP SET DATE_FIN = ? WHERE ID_COMPTE = ? AND DATE_FIN IS NULL'
            );
            $a->execute(array($date_fin,$id_compte));
            $json = array(
                'status' => "success",
            );
            return $json;
        }
        public function update_statut_compte_agac($statut,$telephone){
            $a = $this->bdd->prepare(
                'UPDATE TB_RECRUTEMENT_AGAC_COMPTES SET STATUT = ? where NUMERO_TELEPHONE = ?'
            );
            $a->execute(array($statut,$telephone));
            $json = array(
                'status' => "success",
            );
            return $json;
        }
        public function update_date_debut_otp($statut,$telephone){
            $a = $this->bdd->prepare(
                'UPDATE TB_RECRUTEMENT_AGAC_COMPTES SET STATUT = ? where NUMERO_TELEPHONE = ?'
            );
            $a->execute(array($statut,$telephone));
            $json = array(
                'status' => "success",
            );
            return $json;
        }

        public function trouver_agac($telephone_agac){
            $a = $this->bdd->prepare(
                'SELECT * FROM TB_RECRUTEMENT_AGAC_COMPTES where NUMERO_TELEPHONE = ?'
            );
            $a->execute(array($telephone_agac));
            $json = $a->fetch();
            return $json;
        }

        public function editer_infos_biometrique($id_compte,$nom,$prenoms,$date_naissance,$lieu_naissance,$nationalite,$sexe,$numero_telephone,$numero_secu,$adresse_mail,$type_piece,$numero_piece,$situation_matrimoniale,$nombre_enfants,$civilite,$lieu_residence,$nom_banque,$code_banque,$code_guichet,$numero_compte,$cle_rib,$numero_cnps){
            $a = $this->bdd->prepare("
                INSERT INTO TB_RECRUTEMENT_AGAC_INFOS_BIOGRAPHIQUES (id_compte,nom,prenoms,date_naissance,lieu_naissance,nationalite,sexe,numero_telephone,numero_secu,adresse_mail,type_de_piece,numero_piece,situation_matrimoniale,nombre_enfants,civilite,lieu_residence,nom_banque,code_banque,code_guichet,numero_compte,cle_rib,numero_cnps) 
                VALUES (:id_compte,:nom,:prenoms,TO_DATE(:date_naissance, 'YYYY-MM-DD'),:lieu_naissance,:nationalite,:sexe,:numero_telephone,:numero_secu,:adresse_mail,:type_de_piece,:numero_piece,:situation_matrimoniale,:nombre_enfants,:civilite,:lieu_residence,:nom_banque,:code_banque,:code_guichet,:numero_compte,:cle_rib,:numero_cnps)
            ");
            $a->execute(array(
                "id_compte" => $id_compte,
                "nom" => $nom,
                "prenoms" => $prenoms,
                "date_naissance" => $date_naissance,
                "lieu_naissance" => $lieu_naissance,
                "nationalite" => $nationalite,
                "sexe" => $sexe,
                "numero_telephone" => $numero_telephone,
                "numero_secu" => $numero_secu,
                "adresse_mail" => $adresse_mail,
                "type_de_piece" => $type_piece,
                "numero_piece" => $numero_piece,
                "situation_matrimoniale" => $situation_matrimoniale,
                "nombre_enfants" => $nombre_enfants,
                "civilite" => $civilite,
                "lieu_residence" => $lieu_residence,
                "nom_banque" => $nom_banque,
                "code_banque" => $code_banque,
                "code_guichet" => $code_guichet,
                "numero_compte" => $numero_compte,
                "cle_rib" => $cle_rib,
                "numero_cnps" => $numero_cnps,
            ));

            $json = array(
                'status' => "success",
                'message' => 'Insertion effectuée avec succès...'
            );
            return $json;

        }
        public function  modifier_info_biographique($nom,$prenoms,$date_naissance,$lieu_naissance,$nationalite,$sexe,$numero_telephone,$numero_secu,$adresse_mail,$type_piece,$numero_piece,$situation_matrimoniale,$nombre_enfants,$code_banque,$nom_banque,$code_guichet,$numero_compte,$cle_rib,$numero_cnps,$id_compte){

            $sql = $this->bdd->prepare("
            UPDATE TB_RECRUTEMENT_AGAC_INFOS_BIOGRAPHIQUES SET 
NOM = ?,
PRENOMS = ?, 
DATE_NAISSANCE = ?, 
LIEU_NAISSANCE = ?,
NATIONALITE = ?,
SEXE = ?,
NUMERO_TELEPHONE = ?,
NUMERO_SECU = ?,
ADRESSE_MAIL = ?,
TYPE_DE_PIECE = ?,
NUMERO_PIECE = ?,
SITUATION_MATRIMONIALE = ?,
DATE_MODIFICATION = SYSDATE,
NOMBRE_ENFANTS = ?,
CODE_BANQUE = ?,NOM_BANQUE = ?,
CODE_GUICHET = ?,NUMERO_COMPTE = ?,CLE_RIB = ?,NUMERO_CNPS = ? WHERE ID_COMPTE = ?");

            $sql->execute(array($nom,$prenoms,$date_naissance,$lieu_naissance,$nationalite,$sexe,$numero_telephone,$numero_secu,$adresse_mail,$type_piece,$numero_piece,$situation_matrimoniale,$nombre_enfants,$code_banque,$nom_banque,$code_guichet,$numero_compte,$cle_rib,$numero_cnps,$id_compte
            ));

            $json = array(
                'status' => "success",
                'message' => 'Modification effectuée avec succès...'
            );
            return $json;

        }

        public function modifier_enfant($nom, $prenoms, $date_naissance, $lieu_naissance, $sexe, $numero_secu, $id_compte,$_id_enfant) {
            $sql = $this->bdd->prepare("
        UPDATE TB_RECRUTEMENT_AGAC_INFOS_ENFANTS 
        SET 
            NOM = :NOM,
            PRENOMS = :PRENOMS,
            DATE_NAISSANCE = TO_DATE(:DATE_NAISSANCE, 'YYYY-MM-DD'),
            LIEU_NAISSANCE = :LIEU_NAISSANCE,
            SEXE = :SEXE,
            NUMERO_SECU = :NUMERO_SECU,
            DATE_MODIFICATION = SYSDATE
        WHERE ID_COMPTE = :ID_COMPTE AND ID_ENFANT = :ID_ENFANT
    ");

            // Exécution de la requête avec les paramètres liés
            $sql->execute(array(
                ':NOM' => $nom,
                ':PRENOMS' => $prenoms,
                ':DATE_NAISSANCE' => $date_naissance,
                ':LIEU_NAISSANCE' => $lieu_naissance,
                ':SEXE' => $sexe,
                ':NUMERO_SECU' => $numero_secu,
                ':ID_COMPTE' => $id_compte,
                ':ID_ENFANT' => $_id_enfant,
            ));

            $json = array(
                'status' => "success",
                'message' => 'Modification effectuée avec succès...'
            );

            return $json;
        }

        public function fermer_all_infos_enfant($id_compte) {
            $sql = $this->bdd->prepare("
        UPDATE TB_RECRUTEMENT_AGAC_INFOS_ENFANTS 
        SET DATE_FIN = SYSDATE
        WHERE ID_COMPTE = :ID_COMPTE AND DATE_FIN IS NULL
    ");
            // Exécution de la requête avec les paramètres liés
            $sql->execute(array(
                ':ID_COMPTE' => $id_compte
            ));

            $json = array(
                'status' => "success",
                'message' => 'Modification effectuée avec succès...'
            );

            return $json;
        }

        public function fermer_infos_enfant($id_compte,$id_enfant) {
            $sql = $this->bdd->prepare("
        UPDATE TB_RECRUTEMENT_AGAC_INFOS_ENFANTS 
        SET DATE_FIN = SYSDATE
        WHERE ID_COMPTE = :ID_COMPTE AND ID_ENFANT = :ID_ENFANT
    ");

            // Exécution de la requête avec les paramètres liés
            $sql->execute(array(
                ':ID_COMPTE' => $id_compte,
                ':ID_ENFANT' => $id_enfant,
            ));

            $json = array(
                'status' => "success",
                'message' => 'Modification effectuée avec succès...'
            );

            return $json;
        }

        public function modifier_info_famille(
            $nom_pere,
            $prenoms_pere,
            $nom_mere,
            $prenoms_mere,
            $date_naissance_conjoint,
            $nom_conjoint,
            $prenoms_conjoint,
            $profession_conjoint,
            $num_secu_conjoint,
            $nom_pers_urgence,
            $telephone_personne_urgence,
            $date_naissance_pere,
            $date_naissance_mere,
            $nom_pers_urgence2,
            $telephone_personne_urgence2,
            $nom_pers_urgence3,
            $telephone_personne_urgence3,
            $id_compte
        ) {
            try {
                // Vérification et formatage des dates
                if (!$date_naissance_conjoint || !$date_naissance_pere || !$date_naissance_mere) {
                    throw new Exception("Les dates de naissance ne peuvent pas être nulles.");
                }

                $date_naissance_conjoint = date('Y-m-d', strtotime($date_naissance_conjoint));
                $date_naissance_pere = date('Y-m-d', strtotime($date_naissance_pere));
                $date_naissance_mere = date('Y-m-d', strtotime($date_naissance_mere));

                $sql = $this->bdd->prepare("
            UPDATE TB_RECRUTEMENT_AGAC_FAMILLE SET 
                NOM_PERE = :NOM_PERE,
                PRENOMS_PERE = :PRENOMS_PERE,
                NOM_MERE = :NOM_MERE,
                PRENOMS_MERE = :PRENOMS_MERE,
                DATE_NAISSANCE_CONJOINT = TO_DATE(:DATE_NAISSANCE_CONJOINT, 'YYYY-MM-DD'),
                NOM_CONJOINT = :NOM_CONJOINT,
                PRENOMS_CONJOINT = :PRENOMS_CONJOINT,
                PROFESSION_CONJOINT = :PROFESSION_CONJOINT,
                NUMERO_SECU_CONJOINT = :NUMERO_SECU_CONJOINT,
                NOM_PERSONNE_URGENCE = :NOM_PERSONNE_URGENCE,
                TELEPHONE_PERSONNE_URGENCE = :TELEPHONE_PERSONNE_URGENCE,
                DATE_MODIFICATION = SYSDATE,
                DATE_NAISSANCE_PERE = TO_DATE(:DATE_NAISSANCE_PERE, 'YYYY-MM-DD'),
                DATE_NAISSANCE_MERE = TO_DATE(:DATE_NAISSANCE_MERE, 'YYYY-MM-DD'),
                NOM_PERSONNE_URGENCE_DEUX = :NOM_PERSONNE_URGENCE_DEUX,
                TELEPHONE_PERSONNE_URGENCE_DEUX = :TELEPHONE_PERSONNE_URGENCE_DEUX,
                NOM_PERSONNE_URGENCE_TROIS = :NOM_PERSONNE_URGENCE_TROIS,
                TELEPHONE_PERSONNE_URGENCE_TROIS = :TELEPHONE_PERSONNE_URGENCE_TROIS
            WHERE ID_COMPTE = :ID_COMPTE
        ");

                $sql->execute(array(
                    ":NOM_PERE" => $nom_pere,
                    ":PRENOMS_PERE" => $prenoms_pere,
                    ":NOM_MERE" => $nom_mere,
                    ":PRENOMS_MERE" => $prenoms_mere,
                    ":DATE_NAISSANCE_CONJOINT" => $date_naissance_conjoint,
                    ":NOM_CONJOINT" => $nom_conjoint,
                    ":PRENOMS_CONJOINT" => $prenoms_conjoint,
                    ":PROFESSION_CONJOINT" => $profession_conjoint,
                    ":NUMERO_SECU_CONJOINT" => $num_secu_conjoint,
                    ":NOM_PERSONNE_URGENCE" => $nom_pers_urgence,
                    ":TELEPHONE_PERSONNE_URGENCE" => $telephone_personne_urgence,
                    ":DATE_NAISSANCE_PERE" => $date_naissance_pere,
                    ":DATE_NAISSANCE_MERE" => $date_naissance_mere,
                    ":NOM_PERSONNE_URGENCE_DEUX" => $nom_pers_urgence2,
                    ":TELEPHONE_PERSONNE_URGENCE_DEUX" => $telephone_personne_urgence2,
                    ":NOM_PERSONNE_URGENCE_TROIS" => $nom_pers_urgence3,
                    ":TELEPHONE_PERSONNE_URGENCE_TROIS" => $telephone_personne_urgence3,
                    ":ID_COMPTE" => $id_compte
                ));

                $json = array(
                    'status' => "success",
                    'message' => 'Modification effectuée avec succès...'
                );
                return $json;

            } catch (Exception $e) {
                // Gestion des erreurs
                return array(
                    'status' => "error",
                    'message' => 'Erreur lors de la modification: ' . $e->getMessage()
                );
            }
        }


        public function editer_infos_famille($id_compte, $nom_pere,$prenoms_pere,$nom_mere,$prenoms_mere,$date_naissance_conjoint,
                                             $nom_conjoint, $prenoms_conjoint, $profession_conjoint, $num_secu_conjoint,
                                             $nom_personne_urgence, $telephone_personne_urgence, $date_naissance_pere, $date_naissance_mere,$nom_personne_urgence2,$telephone_personne_urgence2,$nom_personne_urgence3,$telephone_personne_urgence3) {
            // Préparation de la requête SQL
            $a = $this->bdd->prepare("
        INSERT INTO TB_RECRUTEMENT_AGAC_FAMILLE(ID_COMPTE,NOM_PERE,PRENOMS_PERE,NOM_MERE,PRENOMS_MERE,DATE_NAISSANCE_CONJOINT,NOM_CONJOINT,PRENOMS_CONJOINT,
                                                PROFESSION_CONJOINT,NUMERO_SECU_CONJOINT,NOM_PERSONNE_URGENCE,TELEPHONE_PERSONNE_URGENCE,
                                                DATE_NAISSANCE_PERE,DATE_NAISSANCE_MERE,NOM_PERSONNE_URGENCE_DEUX,TELEPHONE_PERSONNE_URGENCE_DEUX,
                                                NOM_PERSONNE_URGENCE_TROIS,TELEPHONE_PERSONNE_URGENCE_TROIS)
        VALUES(:ID_COMPTE,:NOM_PERE,:PRENOMS_PERE,:NOM_MERE,:PRENOMS_MERE,:DATE_NAISSANCE_CONJOINT,:NOM_CONJOINT,:PRENOMS_CONJOINT,:PROFESSION_CONJOINT,
               :NUMERO_SECU_CONJOINT,:NOM_PERSONNE_URGENCE,:TELEPHONE_PERSONNE_URGENCE,:DATE_NAISSANCE_PERE,:DATE_NAISSANCE_MERE,
               :NOM_PERSONNE_URGENCE_DEUX,:TELEPHONE_PERSONNE_URGENCE_DEUX,:NOM_PERSONNE_URGENCE_TROIS,:TELEPHONE_PERSONNE_URGENCE_TROIS) ");

            // Exécution de la requête avec les valeurs passées en paramètres
            $a->execute(array(
               'ID_COMPTE' =>$id_compte,
                'NOM_PERE'=> $nom_pere,
                'PRENOMS_PERE'=> $prenoms_pere,
                'NOM_MERE'=>$nom_mere,
                'PRENOMS_MERE'=> $prenoms_mere,
                'DATE_NAISSANCE_CONJOINT'=> date('Y-m-d', strtotime($date_naissance_conjoint)),
                'NOM_CONJOINT'=> $nom_conjoint,
                'PRENOMS_CONJOINT'=> $prenoms_conjoint,
                'PROFESSION_CONJOINT'=> $profession_conjoint,
                'NUMERO_SECU_CONJOINT'=> $num_secu_conjoint,
                'NOM_PERSONNE_URGENCE'=> $nom_personne_urgence,
                'TELEPHONE_PERSONNE_URGENCE'=> $telephone_personne_urgence,
                'DATE_NAISSANCE_PERE'=> date('Y-m-d', strtotime($date_naissance_pere)),
                'DATE_NAISSANCE_MERE'=> date('Y-m-d', strtotime($date_naissance_mere)),
                'NOM_PERSONNE_URGENCE_DEUX'=> $nom_personne_urgence2,
                'TELEPHONE_PERSONNE_URGENCE_DEUX'=> $telephone_personne_urgence2,
                'NOM_PERSONNE_URGENCE_TROIS'=> $nom_personne_urgence3,
                'TELEPHONE_PERSONNE_URGENCE_TROIS'=> $telephone_personne_urgence3
            ));

            // Retourner un message de succès
            $json = array(
                'status' => "success",
                'message' => 'Insertion effectuée avec succès...'
            );
            return $json;
        }


        public function editer_agac_enfant($id_compte, $enfants) {

            $a = $this->bdd->prepare("
                INSERT INTO TB_RECRUTEMENT_AGAC_INFOS_ENFANTS (id_compte, nom, prenoms, date_naissance,lieu_naissance,sexe,numero_secu,id_enfant,date_debut) 
                VALUES (:id_compte, :nom, :prenoms, TO_DATE(:date_naissance, 'YYYY-MM-DD'),:lieu_naissance,:sexe,:numero_secu,:id_enfant,:date_debut)
            ");

            foreach ($enfants as $enfant) {
                $date_naissance = date('Y-m-d', strtotime($enfant['date_naissance']));
                $date_debut = date('Y-m-d',time());
                $a->execute(array(
                    'id_compte' => $id_compte,
                    'nom' => $enfant['nom'],
                    'prenoms' => $enfant['prenoms'],
                    'date_naissance' => $date_naissance,
                    'lieu_naissance' => $enfant["lieu_naissance"],
                    'sexe' => $enfant["sexe"],
                    'numero_secu' => $enfant["num_secu_enfant"],
                    'id_enfant' => $enfant["id_enfant"],
                    'date_debut' => $date_debut,
                ));
            }
            $json = array(
                'status' => "success",
                'message' => 'Informations des enfants insérées avec succès.'
            );
            return $json;
        }

        public function inserer_agac_enfant($nom_enfant, $prenoms_enfant, $date_naissance_enfant, $lieu_naissance_enfant, $sexe_enfant, $num_secu_enfant, $id_compte, $id_enfant) {

            $a = $this->bdd->prepare("
                INSERT INTO TB_RECRUTEMENT_AGAC_INFOS_ENFANTS (id_compte, nom, prenoms, date_naissance,lieu_naissance,sexe,numero_secu,id_enfant,date_debut) 
                VALUES (:id_compte, :nom, :prenoms, TO_DATE(:date_naissance, 'YYYY-MM-DD'),:lieu_naissance,:sexe,:numero_secu,:id_enfant,:date_debut)
            ");
                $date_debut = date('Y-m-d',time());
                $a->execute(array(
                    'id_compte' => $id_compte,
                    'nom' => $nom_enfant,
                    'prenoms' => $prenoms_enfant,
                    'date_naissance' => $date_naissance_enfant,
                    'lieu_naissance' => $lieu_naissance_enfant,
                    'sexe' => $sexe_enfant,
                    'numero_secu' => $num_secu_enfant,
                    'date_debut' => $date_debut,
                    'id_enfant' => $id_enfant
                ));

            $json = array(
                'status' => "success",
                'message' => 'Informations des enfants insérées avec succès.'
            );
            return $json;
        }

        public function editer_affectation($id_compte,$code_centre_sante,$date_embauche) {

            $a = $this->bdd->prepare("
                INSERT INTO TB_RECRUTEMENT_AGAC_AFFECTATION (id_compte, code_centre_sante, date_embauche) 
                VALUES (:id_compte, :code_centre_sante,TO_DATE(:date_embauche, 'YYYY-MM-DD'))
            ");

            $a->execute(array(
                'id_compte' => $id_compte,
                'code_centre_sante' => $code_centre_sante,
                'date_embauche' => $date_embauche
            ));

            $json = array(
                'status' => "success",
                'message' => 'Les Informations ont été enregistrées avec succès...Veuillez trouver ci-dessous le résumé de vos informations saisies.'
            );
            return $json;
        }
        public function modifier_affectation($code_centre_sante,$date_embauche,$id_compte) {

            $a = $this->bdd->prepare("
                UPDATE TB_RECRUTEMENT_AGAC_AFFECTATION SET CODE_CENTRE_SANTE = :CODE_CENTRE_SANTE, DATE_EMBAUCHE = TO_DATE(:DATE_EMBAUCHE, 'YYYY-MM-DD'), DATE_MODIFICATION AS SYSYDATE WHERE ID_COMPTE = :ID_COMPTE) 
            ");

            $a->execute(array(
                'CODE_CENTRE_SANTE' => $code_centre_sante,
                'DATE_EMBAUCHE' => $date_embauche,
                'ID_COMPTE' => $id_compte
            ));

            $json = array(
                'status' => "success",
                'message' => 'Les Informations ont été enregistrées avec succès...Veuillez trouver ci-dessous le résumé de vos informations saisies.'
            );
            return $json;
        }

        public function liste_nationalite(){
            $a = $this->bdd->prepare('SELECT * FROM REF_NATIONALITE where FIN_VALIDITE IS NULL');
            $a->execute(array());
            $json = $a->fetchAll();
            return $json;
        }
        public function trouver_telephone_compte($id_compte){
            $a = $this->bdd->prepare('SELECT * FROM TB_RECRUTEMENT_AGAC_COMPTES where ID_COMPTE = ?');
            $a->execute(array($id_compte));
            $json = $a->fetch();
            return $json;
        }
        public function trouver_telephone_agac($telephone){
            $a = $this->bdd->prepare('SELECT * FROM TB_RECRUTEMENT_AGAC_COMPTES where NUMERO_TELEPHONE = ?');
            $a->execute(array($telephone));
            $json = $a->fetch();
            return $json;
        }
        public function trouver_centre_agac($telephone){
            $a = $this->bdd->prepare('SELECT * FROM TB_RECRUTEMENT_AGAC_CENTRES where NUMERO_TELEPHONE = ?');
            $a->execute(array($telephone));
            $json = $a->fetch();
            return $json;
        }
        public function trouver_infos_biographique($id_compte){
            $a = $this->bdd->prepare('SELECT * FROM TB_RECRUTEMENT_AGAC_INFOS_BIOGRAPHIQUES where ID_COMPTE = ?');
            $a->execute(array($id_compte));
            $json = $a->fetch();
            return $json;
        }
        public function trouver_infos_identification_visuelle($id_compte){
            $a = $this->bdd->prepare('SELECT * FROM TB_RECRUTEMENT_AGAC_PHOTOS where ID_COMPTE = ?');
            $a->execute(array($id_compte));
            $json = $a->fetch();
            return $json;
        }
        public function trouver_infos_famille($id_compte){
            $a = $this->bdd->prepare('SELECT * FROM TB_RECRUTEMENT_AGAC_FAMILLE where ID_COMPTE = ?');
            $a->execute(array($id_compte));
            $json = $a->fetch();
            return $json;
        }
        public function trouver_infos_enfants($id_compte){
            $a = $this->bdd->prepare('SELECT * FROM TB_RECRUTEMENT_AGAC_INFOS_ENFANTS where ID_COMPTE = ? AND DATE_FIN IS NULL');
            $a->execute(array($id_compte));
            $json = $a->fetchAll();
            return $json;
        }
        public function trouver_infos_affectation($id_compte){
            $a = $this->bdd->prepare('SELECT * FROM TB_RECRUTEMENT_AGAC_AFFECTATION where ID_COMPTE = ?');
            $a->execute(array($id_compte));
            $json = $a->fetch();
            return $json;
        }
        public function liste_profession(){
            $a = $this->bdd->prepare('SELECT * FROM REF_PROFESSION where FIN_VALIDITE IS NULL');
            $a->execute(array());
            $json = $a->fetchAll();
            return $json;
        }
        public function trouver_libelle_profession($code){
            $a = $this->bdd->prepare('SELECT LIBELLE FROM REF_PROFESSION where FIN_VALIDITE IS NULL AND CODE= ?');
            $a->execute(array($code));
            $json = $a->fetch();
            return $json;
        }
        public function trouver_libelle_sexe($code){
            $a = $this->bdd->prepare('SELECT LIBELLE FROM REF_SEXE where FIN_VALIDITE IS NULL AND CODE= ?');
            $a->execute(array($code));
            $json = $a->fetch();
            return $json;
        }
        public function trouver_libelle_situation_matrimoniale($code){
            $a = $this->bdd->prepare('SELECT LIBELLE FROM REF_SITUATION_FAMILIALE where FIN_VALIDITE IS NULL AND CODE= ?');
            $a->execute(array($code));
            $json = $a->fetch();
            return $json;
        }
        public function trouver_libelle_centre_sante($code_inp){
            $a = $this->bdd->prepare('SELECT RAISON_SOCIALE FROM ECMU_REF_ETABLISSEMENT_SANTE where INP= ?');
            $a->execute(array($code_inp));
            $json = $a->fetch();
            return $json;
        }
        public function trouver_libelle_type_piece($code){
            $a = $this->bdd->prepare('SELECT LIBELLE FROM REF_TYPE_DOCUMENT where CODE= ?');
            $a->execute(array($code));
            $json = $a->fetch();
            return $json;
        }
        public function trouver_libelle_nationalite($code){
            $a = $this->bdd->prepare('SELECT LIBELLE FROM REF_NATIONALITE where CODE= ?');
            $a->execute(array($code));
            $json = $a->fetch();
            return $json;
        }
        public function lister_sexe(){
            $a = $this->bdd->prepare('SELECT * FROM REF_SEXE where FIN_VALIDITE IS NULL');
            $a->execute(array());
            $json = $a->fetchAll();
            return $json;
        }
        public function lister_sexe_enfant(){
            $a = $this->bdd->prepare('SELECT * FROM REF_SEXE where FIN_VALIDITE IS NULL');
            $a->execute(array());
            $json = $a->fetchAll();
            return $json;
        }

        public function trouver_sexe($code){
            $a = $this->bdd->prepare('SELECT * FROM REF_SEXE where CODE = ? AND FIN_VALIDITE IS NULL');
            $a->execute(array($code));
            $json = $a->fetch();
            return $json;
        }
        public function trouver_civilite($code){
            $a = $this->bdd->prepare('SELECT * FROM REF_CIVILITE where CODE = ? AND FIN_VALIDITE IS NULL');
            $a->execute(array($code));
            $json = $a->fetch();
            return $json;
        }
        public function trouver_sexe_enfant($id_compte){
            $a = $this->bdd->prepare('SELECT *  FROM TB_RECRUTEMENT_AGAC_INFOS_ENFANTS where ID_COMPTE = ?');
            $a->execute(array($id_compte));
            $json = $a->fetch();
            return $json;
        }
        public function lister_civilite(){
            $a = $this->bdd->prepare('SELECT * FROM REF_CIVILITE where FIN_VALIDITE IS NULL');
            $a->execute(array());
            $json = $a->fetchAll();
            return $json;
        }
        public function lister_type_piece(){
            $a = $this->bdd->prepare('SELECT * FROM REF_TYPE_DOCUMENT where FIN_VALIDITE = ?');
            $a->execute(array(0));
            $json = $a->fetchAll();
            return $json;
        }
        public function lister_situation_familiale(){
            $a = $this->bdd->prepare('SELECT * FROM REF_SITUATION_FAMILIALE where FIN_VALIDITE IS NULL');
            $a->execute(array());
            $json = $a->fetchAll();
            return $json;
        }

        public function envoi_sms($compte_sms,$type_envoi,$numero,$message){
            //$compte_sms = $_POST['compte_sms'];
            //$type_envoi = $_POST['type_envoi'];
            //$numero = $_POST['numero'];
            //$message = $_POST['message'];
            if(isset($compte_sms)){
                $username = trim($compte_sms);
                if($username=='auth'){
                    $compte = 'CMPTO20230530190517';
                    $username = 'auth';
                    $password = 'Auth@23';
                    $sender = 'IPS CNAM';
                }
                elseif($username=='m2m'){
                    $compte = 'CMPTO20200320203736';
                    $username = 'm2m';
                    $password = 'mtomcnam@2023';
                    $sender = 'IPS CNAM';
                }
                elseif($username=='test'){
                    $compte = 'CMPTO20230530185146';
                    $username = 'test';
                    $password = 'SMStest@23';
                    $sender = 'IPS CNAM';
                }
                elseif($username=='affi'){
                    $compte = 'CMPTO20230530190225';
                    $username = 'test';
                    $password = 'SMSaffi@23';
                    $sender = 'IPS CNAM';
                }
                elseif($username=='M2mPartenaire'){
                    $compte = 'CMPTO20200414143530';
                    $username = 'M2mPartenaire';
                    $password = 'Partmtom@2023';
                    $sender = 'IPS CNAM';
                }
                elseif($username=='camp'){
                    $compte = 'CMPTO20200320203823';
                    $username = 'camp';
                    $password = 'C@mpcnam2023';
                    $sender = 'IPS CNAM';
                }
                elseif($username=='serv_client'){
                    $compte = 'CMPTO20240430132027';
                    $username = 'customer';
                    $password = 'Serv@Custom';
                    $sender = 'IPS CNAM';
                }
                else{
                    $compte = '';
                    $username = '';
                    $password = '';
                    $sender = '';
                }
                if (isset($type_envoi)) {
                    $type_envoi = trim($type_envoi);
                    $types_reconnus = array('G', 'I');
                    if (in_array($type_envoi, $types_reconnus)) {
                        if (isset($message)) {
                            $message = trim($message);
                            if (isset($numero)) {
                                if ($type_envoi == 'I') {
                                    $url_hyperSms = "https://smspro.hyperaccesss.com:8443/api/addOneSms";

                                    $numero_telephone = substr(trim($numero), -10);

                                    $contents = array(
                                        'Code' => $compte,
                                        'Username' => $username,
                                        'Password' => $password,
                                        'Sender' => $sender,
                                        'Sms' => $this->conversionCaractere($message),
                                        'Dest' => '225' . $numero_telephone
                                    );
                                }
                                if ($type_envoi == 'G') {
                                    $url_hyperSms = "https://smspro.hyperaccesss.com:8443/api/addFullSms";

                                    $destinataires = preg_split('/;/', trim($_POST['numero']), -1, PREG_SPLIT_NO_EMPTY);
                                    $nb_destinataires = count($destinataires);
                                    if ($nb_destinataires != 0) {
                                        foreach ($destinataires as $destinataire) {
                                            $numero_telephone[] = array(
                                                'Dest' => '225' . substr(trim($destinataire), -10)
                                            );
                                        }
                                        $contents = array(
                                            'Code' => $compte,
                                            'Username' => $username,
                                            'Password' => $password,
                                            'Sender' => $sender,
                                            'Sms' => strtoupper(conversionCaractere($message)),
                                            'Contact' => $numero_telephone
                                        );
                                        $json = $contents;
                                    }
                                }

                                $curl = curl_init();
                                curl_setopt_array($curl, array(
                                    CURLOPT_URL => $url_hyperSms,
                                    CURLOPT_RETURNTRANSFER => true,
                                    CURLOPT_ENCODING => "",
                                    CURLOPT_MAXREDIRS => 10,
                                    CURLOPT_TIMEOUT => 30,
                                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                    CURLOPT_CUSTOMREQUEST => "POST",
                                    CURLOPT_POSTFIELDS => json_encode($contents),
                                    CURLOPT_HTTPHEADER => array(
                                        "Content-Type: application/json",
                                        "cache-control: no-cache"
                                    ),
                                ));

                                $retour = json_decode(curl_exec($curl));
                                //var_dump($retour);
                                $err = curl_error($curl);
                                curl_close($curl);

                                if (@$retour->Code) {
                                    $reponses = $retour->Rep;
                                    foreach ($reponses as $reponse) {
                                        $reference = $reponse->Ref;
                                        $statut = $reponse->Statut;
                                    }

                                    if ($statut == 'sms_received') {
                                        $json = array(
                                            'success' => true,
                                            'reference_numero' => $reference
                                        );
                                    } else {
                                        $json = array(
                                            'success' => false,
                                            'statut' => $statut,
                                            'reference_numero' => $reference
                                        );
                                    }
                                } else {
                                    $json = array(
                                        'success' => false,
                                        'message' => "Une erreur est survenue lors de l'envoi du message."
                                    );
                                }
                            } else {
                                $json = array(
                                    'success' => false,
                                    'message' => "Aucun numéro de téléphone n'a été défini pour cet envoi."
                                );
                            }
                        } else {
                            $json = array(
                                'success' => false,
                                'message' => "Aucun message n'a été défini pour cet envoi."
                            );
                        }

                    } else {
                        $json = array(
                            'success' => false,
                            'message' => "Le type défini est incorrect pour cet envoi."
                        );
                    }
                } else {
                    $json = array(
                        'success' => false,
                        'message' => "Veuillez définir le type d'envoi de ce message."
                    );
                }
            }
            else{
                $json = array(
                    'success' => false,
                    'message' => "Veuillez définir le compte d'envoi."
                );
            }
            return $json;
        }

        public function conversionCaractere($txt)
        {
            $transliterationTable = array('á' => 'a', 'Á' => 'A', 'à' => 'a', 'À' => 'A', 'ă' => 'a', 'Ă' => 'A', 'â' => 'a', 'Â' => 'A', 'å' => 'a', 'Å' => 'A', 'ã' => 'a', 'Ã' => 'A', 'ą' => 'a', 'Ą' => 'A', 'ā' => 'a', 'Ā' => 'A', 'ä' => 'ae', 'Ä' => 'AE', 'æ' => 'ae', 'Æ' => 'AE', 'ḃ' => 'b', 'Ḃ' => 'B', 'ć' => 'c', 'Ć' => 'C', 'ĉ' => 'c', 'Ĉ' => 'C', 'č' => 'c', 'Č' => 'C', 'ċ' => 'c', 'Ċ' => 'C', 'ç' => 'c', 'Ç' => 'C', 'I§' => 'C', 'ď' => 'd', 'Ď' => 'D', 'ḋ' => 'd', 'Ḋ' => 'D', 'đ' => 'd', 'Đ' => 'D', 'ð' => 'dh', 'Ð' => 'Dh', 'é' => 'e', 'É' => 'E', 'è' => 'e', 'È' => 'E', 'ĕ' => 'e', 'Ĕ' => 'E', 'ê' => 'e', 'Ê' => 'E', 'ě' => 'e', 'Ě' => 'E', 'ë' => 'e', 'Ë' => 'E', 'ė' => 'e', 'Ė' => 'E', 'ę' => 'e', 'Ę' => 'E', 'ē' => 'e', 'Ē' => 'E', 'ḟ' => 'f', 'Ḟ' => 'F', 'ƒ' => 'f', 'Ƒ' => 'F', 'ğ' => 'g', 'Ğ' => 'G', 'ĝ' => 'g', 'Ĝ' => 'G', 'ġ' => 'g', 'Ġ' => 'G', 'ģ' => 'g', 'Ģ' => 'G', 'ĥ' => 'h', 'Ĥ' => 'H', 'ħ' => 'h', 'Ħ' => 'H', 'í' => 'i', 'Í' => 'I', 'ì' => 'i', 'Ì' => 'I', 'î' => 'i', 'Î' => 'I', 'ï' => 'i', 'Ï' => 'I', 'ĩ' => 'i', 'Ĩ' => 'I', 'į' => 'i', 'Į' => 'I', 'ī' => 'i', 'Ī' => 'I', 'I¯' => 'I', 'ĵ' => 'j', 'Ĵ' => 'J', 'ķ' => 'k', 'Ķ' => 'K', 'ĺ' => 'l', 'Ĺ' => 'L', 'ľ' => 'l', 'Ľ' => 'L', 'ļ' => 'l', 'Ļ' => 'L', 'ł' => 'l', 'Ł' => 'L', 'ṁ' => 'm', 'Ṁ' => 'M', 'ń' => 'n', 'Ń' => 'N', 'ň' => 'n', 'Ň' => 'N', 'ñ' => 'n', 'Ñ' => 'N', 'ņ' => 'n', 'Ņ' => 'N', 'ó' => 'o', 'Ó' => 'O', 'ò' => 'o', 'Ò' => 'O', 'ô' => 'o', 'Ô' => 'O', 'ő' => 'o', 'Ő' => 'O', 'õ' => 'o', 'Õ' => 'O', 'ø' => 'oe', 'Ø' => 'OE', 'ō' => 'o', 'Ō' => 'O', 'ơ' => 'o', 'Ơ' => 'O', 'ö' => 'oe', 'Ö' => 'OE', 'ṗ' => 'p', 'Ṗ' => 'P', 'ŕ' => 'r', 'Ŕ' => 'R', 'ř' => 'r', 'Ř' => 'R', 'ŗ' => 'r', 'Ŗ' => 'R', 'ś' => 's', 'Ś' => 'S', 'ŝ' => 's', 'Ŝ' => 'S', 'š' => 's', 'Š' => 'S', 'ṡ' => 's', 'Ṡ' => 'S', 'ş' => 's', 'Ş' => 'S', 'ș' => 's', 'Ș' => 'S', 'ß' => 'SS', 'ť' => 't', 'Ť' => 'T', 'ṫ' => 't', 'Ṫ' => 'T', 'ţ' => 't', 'Ţ' => 'T', 'ț' => 't', 'Ț' => 'T', 'ŧ' => 't', 'Ŧ' => 'T', 'ú' => 'u', 'Ú' => 'U', 'ù' => 'u', 'Ù' => 'U', 'ŭ' => 'u', 'Ŭ' => 'U', 'û' => 'u', 'Û' => 'U', 'ů' => 'u', 'Ů' => 'U', 'ű' => 'u', 'Ű' => 'U', 'ũ' => 'u', 'Ũ' => 'U', 'ų' => 'u', 'Ų' => 'U', 'ū' => 'u', 'Ū' => 'U', 'ư' => 'u', 'Ư' => 'U', 'ü' => 'ue', 'Ü' => 'UE', 'ẃ' => 'w', 'Ẃ' => 'W', 'ẁ' => 'w', 'Ẁ' => 'W', 'ŵ' => 'w', 'Ŵ' => 'W', 'ẅ' => 'w', 'Ẅ' => 'W', 'ý' => 'y', 'Ý' => 'Y', 'ỳ' => 'y', 'Ỳ' => 'Y', 'ŷ' => 'y', 'Ŷ' => 'Y', 'ÿ' => 'y', 'Ÿ' => 'Y', 'ź' => 'z', 'Ź' => 'Z', 'ž' => 'z', 'Ž' => 'Z', 'ż' => 'z', 'Ż' => 'Z', 'þ' => 'th', 'Þ' => 'Th', 'µ' => 'u', 'а' => 'a', 'А' => 'a', 'б' => 'b', 'Б' => 'b', 'в' => 'v', 'В' => 'v', 'г' => 'g', 'Г' => 'g', 'д' => 'd', 'Д' => 'd', 'е' => 'e', 'Е' => 'E', 'ё' => 'e', 'Ё' => 'E', 'ж' => 'zh', 'Ж' => 'zh', 'з' => 'z', 'З' => 'z', 'и' => 'i', 'И' => 'i', 'й' => 'j', 'Й' => 'j', 'к' => 'k', 'К' => 'k', 'л' => 'l', 'Л' => 'l', 'м' => 'm', 'М' => 'm', 'н' => 'n', 'Н' => 'n', 'о' => 'o', 'О' => 'o', 'п' => 'p', 'П' => 'p', 'р' => 'r', 'Р' => 'r', 'с' => 's', 'С' => 's', 'т' => 't', 'Т' => 't', 'у' => 'u', 'У' => 'u', 'ф' => 'f', 'Ф' => 'f', 'х' => 'h', 'Х' => 'h', 'ц' => 'c', 'Ц' => 'c', 'ч' => 'ch', 'Ч' => 'ch', 'ш' => 'sh', 'Ш' => 'sh', 'щ' => 'sch', 'Щ' => 'sch', 'ъ' => '', 'Ъ' => '', 'ы' => 'y', 'Ы' => 'y', 'ь' => '', 'Ь' => '', 'э' => 'e', 'Э' => 'e', 'ю' => 'ju', 'Ю' => 'ju', 'я' => 'ja', 'Я' => 'ja');
            return str_replace(array_keys($transliterationTable), array_values($transliterationTable), $txt);
        }

        public function trouver_ets_raison_sociale($raison_sociale) {
            $a = $this->bdd->prepare('SELECT DISTINCT(A.INP) AS CODE_ETS, A.RAISON_SOCIALE FROM 
ECMU_REF_ETABLISSEMENT_SANTE A JOIN ECMU_RESEAUX_ETS B ON A.INP = B.CODE_ETS 
AND B.RESEAU_ID != ?
AND  RAISON_SOCIALE LIKE ?');
            $a->execute(array(12, '%'.$raison_sociale.'%'));
            $json = $a->fetchAll();
            return $json;
        }

        public function verif_numero_secu(){
            $a = $this->bdd->prepare('SELECT * FROM REF_NATIONALITE where FIN_VALIDITE IS NULL');
            $a->execute(array());
            $json = $a->fetchAll();
            return $json;
        }
        public function trouver_numero_secu($num_secu){
            $a = $this->bdd->prepare('SELECT * FROM ECMU_ASSURES where NUM_SECU = ?');
            $a->execute(array($num_secu));
            $json = $a->fetch();
            return $json;
        }
        public function trouver_enfants($id_compte, $nombre_enfant) {
            // Requête SQL avec une sous-requête pour éviter les problèmes avec ROWNUM
            $sql = "
        SELECT nom, prenoms, date_naissance, lieu_naissance, sexe, numero_secu
        FROM (
            SELECT nom, prenoms, date_naissance, lieu_naissance, sexe, numero_secu
            FROM TB_RECRUTEMENT_AGAC_INFOS_ENFANTS
            WHERE id_compte = :id_compte AND DATE_FIN IS NULL
            ORDER BY id_enfant
        )
        WHERE ROWNUM <= :nombreEnfants
    ";

            // Préparation de la requête
            $a = $this->bdd->prepare($sql);

            // Lier les paramètres :id_compte et :nombreEnfants
            $a->bindParam(':id_compte', $id_compte, PDO::PARAM_STR);
            $a->bindValue(':nombreEnfants', (int)$nombre_enfant, PDO::PARAM_INT);

            // Exécuter la requête
            $a->execute();

            // Récupérer les résultats sous forme de tableau associatif
            $json = $a->fetchAll(PDO::FETCH_ASSOC);

            return $json;
        }

        public function clean_data($data) {
            $data = trim($data);  // Supprimer les espaces avant et après
            $data = stripslashes($data);  // Supprimer les antislashes
            $data = htmlspecialchars($data);  // Convertir les caractères spéciaux en entités HTML
            return $data;
        }





}