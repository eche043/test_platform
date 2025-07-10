<?php
use GuzzleHttp\Pool;
use GuzzleHttp\Client;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\RequestException;

require_once '../../../Classes/UTILISATEURS.php';
require '../../../../vendor/autoload.php';
if(isset($_SESSION['ECMU_USER_ID'])){
    $session_user = $_SESSION['ECMU_USER_ID'];
    if(!empty($session_user)){
        $UTILISATEURS = new UTILISATEURS();
        $utilisateur = $UTILISATEURS->trouver($session_user,null,null);

        if(!empty($utilisateur['ID_UTILISATEUR'])){
            //$trouver_ets = $UTILISATEURS->trouver_ets_utilisateur($session_user);
            require_once '../../../Classes/ASSURES.php';
            $ASSURES = new ASSURES();
            if($utilisateur['ACTIF'] != 1){
                $json = array(
                    'status' => false,
                    'message' => 'VOTRE IDENTIFIANT A ETE DESACTIVE. VOUS NE POUVEZ PAS EFFECTUER CETTE ACTION. VEUILLEZ CONTACTER VOTRE ADMINISTRATEUR SYSTEME.'
                );
            }else{
                $type_facture = trim($_POST['type_facture']);
                $date_soins = strtoupper(date('Y-m-d',strtotime(str_replace('/','-',$_POST['date_soins']))));
                $date_sortie = strtoupper(date('Y-m-d',strtotime(str_replace('/','-',$_POST['date_sortie']))));
                $type_sortie = trim($_POST['type_sortie']);
                $num_fs_initiale = trim($_POST['num_fs_initiale']);
                $num_facture = trim($_POST['num_facture']);
                $num_ep_cnam = trim($_POST['num_ep_cnam']);
                $num_ep_ac = null;
                $num_ac = trim($_POST['num_ac']);
                $code_ac = trim($_POST['code_ac']);
                if(isset($_POST['type_ets'])){
                    $type_ets = trim($_POST['type_ets']);
                }else{
                    $type_ets = NULL;
                }
                $type_ets_autre = trim($_POST['type_ets_autre']);
                $info_complementaire = trim($_POST['info_complementaire']);
                $info_compl_autre = trim($_POST['info_compl_autre']);
                if(isset($_POST['date_accident']) && !empty($_POST['date_accident'])){$date_accident = strtoupper(date('Y-m-d',strtotime(str_replace('/','-',trim($_POST['date_accident'])))));}else{$date_accident= null;}
                if(isset($_POST['num_imm_vehicule'])){$num_imm_vehicule = trim($_POST['num_imm_vehicule']);}else{$num_imm_vehicule = null;}
                $code_programme = trim($_POST['code_programme']);
                $code_ps = trim($_POST['code_ps']);
                $code_specialite_ps = trim($_POST['code_specialite_ps']);
                if(isset($_POST['code_affection'])) {
                    $code_affection = explode(',',str_replace('[','',str_replace(']','',str_replace('"','',json_encode($_POST['code_affection'])))));
                    $pathologies = array_filter($_POST['code_affection']);
                }else {
                    $code_affection = array(NULL,NULL);
                    $pathologies =  array();
                }
                if(!isset($code_affection[1])) {
                    $code_affection[1] = NULL;
                }

                $code_actes = explode(',',str_replace('[','',str_replace(']','',str_replace('"','',json_encode($_POST['code_acte'])))));
                $quantite_presc = explode(',',str_replace('[','',str_replace(']','',str_replace('"','',json_encode($_POST['quantite_presc'])))));
                $quantite = explode(',',str_replace('[','',str_replace(']','',str_replace('"','',json_encode($_POST['quantite'])))));
                $num_dent = explode(',',str_replace('[','',str_replace(']','',str_replace('"','',json_encode($_POST['num_dent'])))));
                $prix_unitaire = explode(',',str_replace('[','',str_replace(']','',str_replace('"','',json_encode($_POST['prix_unitaire'])))));
                $date_debut_acte = explode(',',str_replace('[','',str_replace(']','',str_replace('"','',json_encode($_POST['date_debut'])))));
                $date_fin_acte = explode(',',str_replace('[','',str_replace(']','',str_replace('"','',json_encode($_POST['date_fin'])))));

                $montant_cmu = array();
                $montant_complementaire= array();
                $montant_assure= array();
                $taux_couverture= array();
                $taux_couverture_ac= array();
                $montant_depense= array();
                $base_remboursement= array();
                $base_remboursement_ac= array();

                if(isset($_POST['montant_cmu'])){$montant_cmu = explode(',',str_replace('[','',str_replace(']','',str_replace('"','',json_encode($_POST['montant_cmu'])))));}
                if(isset($_POST['montant_complementaire'])){$montant_complementaire = explode(',',str_replace('[','',str_replace(']','',str_replace('"','',json_encode($_POST['montant_complementaire'])))));}
                if(isset($_POST['montant_assure'])){$montant_assure = explode(',',str_replace('[','',str_replace(']','',str_replace('"','',json_encode($_POST['montant_assure'])))));}
                if(isset($_POST['taux_remboursement'])){$taux_couverture = explode(',',str_replace('[','',str_replace(']','',str_replace('"','',json_encode($_POST['taux_remboursement'])))));}
                if(isset($_POST['taux_remboursement_ac'])){$taux_couverture_ac = explode(',',str_replace('[','',str_replace(']','',str_replace('"','',json_encode($_POST['taux_remboursement_ac'])))));}
                if(isset($_POST['montant_depense'])){$montant_depense = explode(',',str_replace('[','',str_replace(']','',str_replace('"','',json_encode($_POST['montant_depense'])))));}
                if(isset($_POST['prix_unitaire_base'])){$base_remboursement = explode(',',str_replace('[','',str_replace(']','',str_replace('"','',json_encode($_POST['prix_unitaire_base'])))));}else{$base_remboursement = $prix_unitaire;}
                if(isset($_POST['prix_unitaire_base_ac'])){$base_remboursement_ac = explode(',',str_replace('[','',str_replace(']','',str_replace('"','',json_encode($_POST['prix_unitaire_base_ac'])))));}

                require_once '../../../Classes/FACTURES.php';
                $FACTURES = new FACTURES();
                $facture = $FACTURES->trouver($num_facture);
                if(!empty($facture['FEUILLE'])) {
                    $array_statut = array('','C','F','N');
                    $statut_mirka = false;
                    if(in_array($facture['STATUT'],$array_statut)) {
                        $array_statut_fs_initiale = array('A');
                        $nb_actes = count($code_actes);
                        if($code_ps){
                            if(!empty($num_fs_initiale)) {
                                $facture_initiale = $FACTURES->trouver_facture_initiale($num_fs_initiale);
                                if (!in_array($facture_initiale['STATUT'], $array_statut_fs_initiale)) {
                                    $statut_mirka = true;
                                }
                            }
                            else{
                                $statut_mirka = true;
                            }

                            if($statut_mirka==true){

                                $prestations = array();
                                if($facture['REGIME_CODE']=='RGB'){
                                    $taux_couverture_m = 0.7;
                                }
                                else{
                                    $taux_couverture_m = 1;
                                }
                                for ($i = 0; $i < $nb_actes; $i++) {
                                    if(isset($montant_depense[$i])){$montant_depense_m = $montant_depense[$i];}else{$montant_depense_m = $prix_unitaire[$i] * $quantite[$i];}
                                    if(isset($montant_cmu[$i])){$montant_cmu_m = $montant_cmu[$i];}else{$montant_cmu_m = $montant_depense_m*$taux_couverture_m;}
                                    if(isset($montant_assure[$i])){$montant_assure_m = $montant_assure[$i];}else{$montant_assure_m = $montant_depense_m-$montant_cmu_m;}

                                    $date_debut = strtoupper(date('Y-m-d',strtotime(str_replace('/','-',(str_replace('\/','-',trim($date_debut_acte[$i])))))));
                                    $date_fin = strtoupper(date('Y-m-d',strtotime(str_replace('/','-',(str_replace('\/','-',trim($date_fin_acte[$i])))))));
                                    $prestations[] = array(
                                        "code" => $code_actes[$i],
                                        "statut_remboursement" => 1,
                                        "motif_non_remboursement" =>  "",
                                        "base_remboursement" =>  $base_remboursement[$i],
                                        "taux_couverture" =>  $taux_couverture_m,
                                        "quantite_prescrite" =>  $quantite_presc[$i],
                                        "quantite_servie" =>  $quantite[$i],
                                        "prix_unitaire" =>  $prix_unitaire[$i],
                                        "montant_depense" =>  $montant_depense_m,
                                        "montant_cmu" => $montant_cmu_m,
                                        "montant_complementaire" =>  $montant_complementaire ?$montant_complementaire[$i]: 0,
                                        "montant_remise" =>  0,
                                        "montant_assure" =>  $montant_assure_m,
                                        "date_debut" =>  $date_debut,
                                        "date_fin" =>  $date_fin,
                                        "dents" =>  null
                                    );
                                }


                                $client = new Client([
                                    'timeout' => 60,
                                    'verify' => false
                                ]);

                                $headers = [
                                    //'Authorization' => 'Bearer 1|pC8jCa84ZSYMjg4fwKWluPKzIWOFRMuSB81dX9ci316eec2b',
                                    'Authorization' => 'Bearer 79|VdcGNkIF1YKKeqZeUuYN5TgjYqgOZdlGdQnDb6Fj0dc9a4b6',
                                    'accept' => 'application/json',
                                    'Content-Type' => 'application/json'
                                ];

                                if($type_facture!== 'AMB' || $type_facture!== 'DEN' ){
                                    $statut_m = "F";
                                    $method = 'PATCH';
                                    $url = 'https://10.10.4.85:3128/api/prestations/factures/'.$num_facture;
                                }else{
                                    if($facture['STATUT'] == '' || $facture['STATUT'] == 'N'){
                                        $statut_m = "N";
                                        $method = 'POST';
                                        $url ='https://10.10.4.85:3128/api/prestations/factures/'.$num_facture.'/prestations';
                                    }
                                    else{
                                        $statut_m = "F";
                                        $method = 'PATCH';
                                        $url = 'https://10.10.4.85:3128/api/prestations/factures/'.$num_facture;
                                    }
                                }

                                if($type_facture=='EXP'){
                                    if(empty($type_ets)){
                                        $type_ets = 'B';
                                    }
                                }

                                if($pathologies){
                                    $body = json_encode([
                                        "code_statut"=> $statut_m,
                                        "numero_initial"=> $num_fs_initiale,
                                        "numero_ep"=> "",
                                        "date_soins"=> $date_soins,
                                        "date_fin_soins"=> $date_sortie,
                                        "code_type_facture"=> $type_facture,
                                        "code_etablissement"=> $facture['ETABLISSEMENT'],
                                        "code_type_etablissement"=> $type_ets,
                                        "autre_type_etablissement"=> $type_ets_autre,
                                        "assure"=> [
                                            "numero_secu"=> $facture['NUM_SECU'],
                                            "numero_matricule"=> $num_ac,
                                            "infos_complementaires"=> [
                                                "code_type"=> $info_complementaire,
                                                "autre_type"=> $info_compl_autre,
                                                "date"=> $date_accident,
                                                "numero_vehicule"=> $num_imm_vehicule,
                                                "code_programme"=> $code_programme
                                            ]
                                        ],
                                        "code_organisme"=> $facture['NUM_OGD'],
                                        "professionnel_sante"=> [
                                            "code"=> "$code_ps",
                                            "code_specialite"=> "$code_specialite_ps"
                                        ],
                                        "pathologies"=> $pathologies,
                                        "prestations"=> $prestations
                                    ]);
                                }
                                else{
                                    $body = json_encode([
                                        "code_statut"=> $statut_m,
                                        "numero_initial"=> $num_fs_initiale,
                                        "numero_ep"=> "",
                                        "date_soins"=> $date_soins,
                                        "date_fin_soins"=> $date_sortie,
                                        "code_type_facture"=> $type_facture,
                                        "code_etablissement"=> $facture['ETABLISSEMENT'],
                                        "code_type_etablissement"=> $type_ets,
                                        "autre_type_etablissement"=> $type_ets_autre,
                                        "assure"=> [
                                            "numero_secu"=> $facture['NUM_SECU'],
                                            "numero_matricule"=> $num_ac,
                                            "infos_complementaires"=> [
                                                "code_type"=> $info_complementaire,
                                                "autre_type"=> $info_compl_autre,
                                                "date"=> $date_accident,
                                                "numero_vehicule"=> $num_imm_vehicule,
                                                "code_programme"=> $code_programme
                                            ]
                                        ],
                                        "code_organisme"=> $facture['NUM_OGD'],
                                        "professionnel_sante"=> [
                                            "code"=> "$code_ps",
                                            "code_specialite"=> "$code_specialite_ps"
                                        ],
                                        "prestations"=> $prestations
                                    ]);

                                }


                                $request = new Request($method, $url, $headers, $body);

                                try{
                                    $res = $client->send($request);

                                    $reponse = json_decode($res->getBody());
                                    if($reponse->success===true){

                                        if($type_facture == 'AMB' || $type_facture == 'DEN') {
                                            $nb_actes = count($code_actes);
                                            $ligne = 0;
                                            if($nb_actes != 0) {
                                                if($facture['STATUT'] == ''){
                                                    $statut_fse = 'C';
                                                    $validaton = 1;
                                                }elseif($facture['STATUT'] == 'C'){
                                                    $statut_fse = 'F';
                                                    if(!empty($code_ps) && !empty($code_affection[0]) && !empty($type_sortie) && !empty($date_sortie)) {
                                                        $validaton = 1;
                                                    }else {
                                                        $validaton = 0;
                                                    }
                                                }else {
                                                    $statut_fse = 'F';
                                                    if(!empty($code_ps) && !empty($code_affection[0]) && !empty($type_sortie) && !empty($date_sortie)) {
                                                        $validaton = 1;
                                                    }else {
                                                        $validaton = 0;
                                                    }
                                                }
                                                if($validaton == 1) {
                                                    $supression_acte = $FACTURES->supprimer_facture_actes($num_facture);
                                                    if($supression_acte['status'] == true) {
                                                        for ($i = 0; $i < $nb_actes; $i++) {
                                                            $ajouter_acte = $FACTURES->ajouter_facture_acte('a',$num_facture,$code_actes[$i],strtoupper(date('Y-m-d',strtotime($date_soins))),strtoupper(date('Y-m-d',strtotime($date_soins))),$quantite[$i],$quantite_presc[$i],$prix_unitaire[$i],0,NULL, $montant_cmu[$i]??0, $montant_complementaire[$i]??0, $montant_assure[$i]??0, $base_remboursement_ac[$i]??0, $base_remboursement[$i]??0, $taux_couverture[$i]??0, $taux_couverture_ac[$i]??0,$utilisateur['ID_UTILISATEUR']);
                                                            if($ajouter_acte['status'] == true) {
                                                                $ligne++;
                                                            }
                                                        }
                                                        if($ligne == $nb_actes) {
                                                            $maj_facture = $FACTURES->maj_facture($type_facture,$date_soins,$num_facture,$num_fs_initiale,$num_ep_cnam, $num_ep_ac, $num_ac,$type_ets,$type_ets_autre,$info_complementaire,$info_compl_autre,NULL,NULL,$code_programme,$code_affection[0],$code_affection[1],$date_sortie,$type_sortie,$code_ps,$code_specialite_ps,$statut_fse,$utilisateur['ID_UTILISATEUR']);
                                                            if($maj_facture['status'] == true) {
                                                                $json = array(
                                                                    'status' => true,
                                                                    'message' => $maj_facture['message']
                                                                );
                                                            }
                                                        }
                                                    }else {
                                                        $json = array(
                                                            'status' => false,
                                                            'message' => 'UNE ERREUR EST SURVENUE LORS DE LA MISE A JOUR DE LA FACTURE. VEUILLEZ REESAYER. SI LE PROBLEME PERSISTE, PRIERE DE CONTACTER VOTRE ADMINISTRATEUR.'
                                                        );
                                                    }
                                                }else {
                                                    if(empty($code_ps)) {
                                                        $json = array(
                                                            'status' => false,
                                                            'message' => 'VEUILLEZ RENSEIGNER LE PROFESSIONNEL DE SANTE.'
                                                        );
                                                    }elseif (empty($code_affection[0])) {
                                                        $json = array(
                                                            'status' => false,
                                                            'message' => 'VEUILLEZ RENSEIGNER LA PATHOLOGIE.'
                                                        );
                                                    }elseif (empty($type_sortie)) {
                                                        $json = array(
                                                            'status' => false,
                                                            'message' => 'VEUILLEZ RENSEIGNER LE TYPE DE SORTIE.'
                                                        );
                                                    }elseif (empty($type_sortie)) {
                                                        $json = array(
                                                            'status' => false,
                                                            'message' => 'VEUILLEZ RENSEIGNER LA DATE DE SORTIE.'
                                                        );
                                                    }else {
                                                        $json = array(
                                                            'status' => false,
                                                            'message' => $validaton
                                                        );
                                                    }
                                                }
                                            }else {
                                                $json = array(
                                                    'status' => false,
                                                    'message' => 'VEUILLEZ RENSEIGNER AU MOINS UN ACTE.'
                                                );
                                            }
                                        }
                                        elseif ($type_facture == 'HOS' || $type_facture == 'EXP'){
                                            if(!empty($num_fs_initiale)) {
                                                $facture_initiale = $FACTURES->trouver_facture_initiale($num_fs_initiale);
                                                if(!in_array($facture_initiale['STATUT'],$array_statut_fs_initiale)){
                                                    if(isset($code_affection[0]) || isset($code_affection[1])){
                                                        $type_acte = 'a';
                                                        $nb_actes = count($code_actes);
                                                        $ligne = 0;
                                                        if($nb_actes != 0) {
                                                            $supression_acte = $FACTURES->supprimer_facture_actes($num_facture);
                                                            if($supression_acte['status'] == true) {
                                                                for ($i = 0; $i < $nb_actes; $i++) {
                                                                    $ajouter_acte = $FACTURES->ajouter_facture_acte($type_acte,$num_facture,$code_actes[$i],strtoupper(date('Y-m-d',strtotime($date_soins))),strtoupper(date('Y-m-d',strtotime($date_soins))),$quantite[$i],$quantite_presc[$i],$prix_unitaire[$i],0,NULL, $montant_cmu[$i]??0, $montant_complementaire[$i]??0, $montant_assure[$i]??0, $base_remboursement_ac[$i]??0, $base_remboursement[$i]??0, $taux_couverture[$i]??0, $taux_couverture_ac[$i]??0,$utilisateur['ID_UTILISATEUR']);
                                                                    if($ajouter_acte['status'] == true) {
                                                                        $ligne++;
                                                                    }
                                                                }
                                                                if($ligne == $nb_actes) {
                                                                    if(isset($code_affection[0])){
                                                                        if(empty($code_affection[0])){
                                                                            $code_affection[0] = $facture_initiale['AFFECTION1'];
                                                                        }/*else{
                                                                            $code_affection[0] =$code_affection[0];
                                                                        }*/
                                                                    }else{
                                                                        $code_affection[0] = NULL;
                                                                    }
                                                                    if(isset($code_affection[1])){
                                                                        if(empty($code_affection[1])){
                                                                            $code_affection[1] = $facture_initiale['AFFECTION2'];
                                                                        }/*else {
                                                                            $code_affection[1] = $code_affection[1];
                                                                        }*/
                                                                    }else{
                                                                        $code_affection[1] = NULL;
                                                                    }
                                                                    $maj_facture = $FACTURES->maj_facture($type_facture,$date_soins,$num_facture,$facture_initiale['FEUILLE'], $num_ep_cnam, $num_ep_ac, $num_ac,$type_ets,$type_ets_autre,$info_complementaire,$info_compl_autre,$num_imm_vehicule,$date_accident,$code_programme,$code_affection[0],$code_affection[1],$date_sortie,$type_sortie,$code_ps,$code_specialite_ps,'F',$utilisateur['ID_UTILISATEUR']);
                                                                    if($maj_facture['status'] == true) {
                                                                        if(empty($facture_initiale['AFFECTION1'])) {
                                                                            $maj_affection = $FACTURES->maj_affection_facture($code_affection[0],$code_affection[1],$facture_initiale['FEUILLE']);
                                                                        }
                                                                        $json = array(
                                                                            'status' => true,
                                                                            'message' => $maj_facture['message']
                                                                        );
                                                                    }
                                                                }
                                                            }else {
                                                                $json = array(
                                                                    'status' => false,
                                                                    'message' => 'UNE ERREUR EST SURVENUE LORS DE LA MISE A JOUR DE LA FACTURE. VEUILLEZ REESAYER. SI LE PROBLEME PERSISTE, PRIERE DE CONTACTER VOTRE ADMINISTRATEUR.'
                                                                );
                                                            }
                                                        }else {
                                                            $json = array(
                                                                'status' => false,
                                                                'message' => 'VEUILLEZ RENSEIGNER AU MOINS UN ACTE.'
                                                            );
                                                        }
                                                    }else {
                                                        $json = array(
                                                            'status' => false,
                                                            'message' => 'VEUILLEZ RENSEIGNER AU MOINS UNE AFFECTION.'
                                                        );
                                                    }
                                                }else {
                                                    $json = array(
                                                        'status' => false,
                                                        'message' => 'LE N° DE FEUILLE DE SOINS INITIALE SAISI NE PEUT ETRE UTILISE POUR CETTE FACTURE.'
                                                    );
                                                }
                                            }else {
                                                $json = array(
                                                    'status' => false,
                                                    'message' => 'VEUILLEZ RENSEIGNER LE NUMERO DE LA FEUILLE DE SOINS INITIALE SVP.'
                                                );
                                            }
                                        }
                                        else {
                                            require_once '../../../Classes/MEDICAMENTS.php';
                                            $MEDICAMENTS = new MEDICAMENTS();
                                            if(!empty($num_fs_initiale)) {
                                                $facture_initiale = $FACTURES->trouver_facture_initiale($num_fs_initiale);
                                                if(!in_array($facture_initiale['STATUT'],$array_statut_fs_initiale)) {
                                                    if($type_facture == 'MED') {
                                                        $type_acte = 'm';
                                                        $ps = $FACTURES->verifier_facture_ps($code_ps,NULL,$facture['ETABLISSEMENT'],strtoupper(date('Y-m-d',strtotime($facture['DATE_SOINS']))));
                                                    }else {
                                                        $type_acte = 'a';
                                                        $ps = $FACTURES->verifier_facture_ps($facture['PS'],NULL,$facture['ETABLISSEMENT'],strtoupper(date('Y-m-d',strtotime($facture['DATE_SOINS']))));
                                                    }

                                                    if(isset($code_affection[0])){
                                                        $code_affection[0] =$code_affection[0];
                                                    }else{
                                                        $code_affection[0] = NULL;
                                                    }
                                                    if(isset($code_affection[1])){
                                                        $code_affection[1] =$code_affection[1];
                                                    }else{
                                                        $code_affection[1] = NULL;
                                                    }
                                                    if(!empty($code_affection[0]) || !empty($code_affection[1])){
                                                        if($ps['code_ps']) {
                                                            $nb_actes = count($code_actes);
                                                            $ligne = 0;
                                                            if($nb_actes != 0) {
                                                                $supression_acte = $FACTURES->supprimer_facture_actes($num_facture);
                                                                if($supression_acte['status'] == true) {
                                                                    for ($i = 0; $i < $nb_actes; $i++) {
                                                                        if($type_facture == 'MED') {
                                                                            $medicament = $MEDICAMENTS->trouver_medicament('NPSP',$code_actes[$i]);
                                                                            $code_acte = $medicament['CODE'];
                                                                        }else{
                                                                            $code_acte = $code_actes[$i];
                                                                        }
                                                                        $ajouter_acte = $FACTURES->ajouter_facture_acte($type_acte,$num_facture,$code_acte,strtoupper(date('Y-m-d',strtotime($date_soins))),strtoupper(date('Y-m-d',strtotime($date_soins))),$quantite[$i],$quantite_presc[$i],$prix_unitaire[$i],0,NULL, $montant_cmu[$i]??0, $montant_complementaire[$i]??0, $montant_assure[$i]??0, $base_remboursement_ac[$i]??0, $base_remboursement[$i]??0, $taux_couverture[$i]??0, $taux_couverture_ac[$i]??0, $utilisateur['ID_UTILISATEUR']);
                                                                        if($ajouter_acte['status'] == true) {
                                                                            $ligne++;
                                                                        }
                                                                    }
                                                                    if($ligne == $nb_actes) {
                                                                        $maj_facture = $FACTURES->maj_facture($type_facture,$date_soins,$num_facture,$facture_initiale['FEUILLE'],$num_ep_cnam, $num_ep_ac, $num_ac,$type_ets,$type_ets_autre,$info_complementaire,$info_compl_autre,NULL,NULL,$code_programme,$code_affection[0],$code_affection[1],$date_sortie,$type_sortie,$ps['code_ps'],$ps['code_specialite'],'F',$utilisateur['ID_UTILISATEUR']);
                                                                        if($maj_facture['status'] == true) {
                                                                            if(empty($facture_initiale['AFFECTION1'])) {
                                                                                $maj_affection = $FACTURES->maj_affection_facture($code_affection[0],$code_affection[1],$facture_initiale['FEUILLE']);
                                                                            }
                                                                            $json = array(
                                                                                'status' => true,
                                                                                'message' => $maj_facture['message']
                                                                            );
                                                                        }
                                                                    }
                                                                }else {
                                                                    $json = array(
                                                                        'status' => false,
                                                                        'message' => 'UNE ERREUR EST SURVENUE LORS DE LA MISE A JOUR DE LA FACTURE. VEUILLEZ REESAYER. SI LE PROBLEME PERSISTE, PRIERE DE CONTACTER VOTRE ADMINISTRATEUR.'
                                                                    );
                                                                }
                                                            }else {
                                                                $json = array(
                                                                    'status' => false,
                                                                    'message' => 'VEUILLEZ RENSEIGNER AU MOINS UN ACTE.'
                                                                );
                                                            }
                                                        }else{
                                                            $json = array(
                                                                'status' => false,
                                                                'message' => 'VEUILLEZ RENSEIGNER UN PROFESSIONNEL DE SANTE VALIDE.'
                                                            );
                                                        }
                                                    }else{
                                                        $json = array(
                                                            'status' => false,
                                                            'message' => 'VEUILLEZ RENSEIGNER AU MOINS UNE AFFECTION.'
                                                        );
                                                    }
                                                }else {
                                                    $json = array(
                                                        'status' => false,
                                                        'message' => 'LE N° DE FEUILLE DE SOINS INITIALE SAISI NE PEUT ETRE UTILISE POUR CETTE FACTURE.'
                                                    );
                                                }
                                            }else {
                                                $json = array(
                                                    'status' => false,
                                                    'message' => 'VEUILLEZ RENSEIGNER LE NUMERO DE LA FEUILLE DE SOINS INITIALE SVP.'
                                                );
                                            }
                                        }
                                    }
                                    else{
                                        $json = array(
                                            'status' => false,
                                            'message' => $reponse['success']
                                        );
                                    }
                                }catch (\Throwable $e){
                                    var_dump(array($e->getMessage(),$e->getLine()));
                                    $response = $e->getResponse();
                                    $json = array('status' => false, 'message'=>$response->getBody()->getContents());
                                }
                            }
                            else{
                                $json = array(
                                    'status' => false,
                                    'message' => 'LE N° DE FEUILLE DE SOINS INITIALE SAISI NE PEUT ETRE UTILISE POUR CETTE FACTURE.'
                                );
                            }
                            //
                        }else{
                            $json = array(
                                'status' => false,
                                'message' => 'LE PROFESSIONNEL DE SANTE N\'EST PAS VALIDE. PRIERE VERIFIER.'
                            );
                        }
                    }else {
                        $json = array(
                            'status' => false,
                            'message' => 'LE N° DE FACTURE SAISI EST NE PEUT EDITE ACTUELLEMENT AVEC LE STATUT: '.$facture['STATUT'].'. VEUILLEZ CONTACTER VOTRE ADMINISTRATEUR SYSTEME.'
                        );
                    }
                }else {
                    $json = array(
                        'status' => false,
                        'message' => 'LE N° DE FACTURE SAISI EST INCORRECT. VEUILLEZ CONTACTER VOTRE ADMINISTRATEUR SYSTEME.'
                    );
                }
            }
        }else{
            $json = array(
                'status' => false,
                'message' => 'VOTRE IDENTIFIANT EST INCORRECT. VOUS NE POUVEZ PAS EFFECTUER CETTE ACTION. VEUILLEZ CONTACTER VOTRE ADMINISTRATEUR SYSTEME.'
            );
        }
    }else{
        $json = array(
            'status' => false,
            'message' => 'VOTRE IDENTIFIANT EST INCORRECT. VOUS NE POUVEZ PAS EFFECTUER CETTE ACTION. VEUILLEZ CONTACTER VOTRE ADMINISTRATEUR SYSTEME.'
        );
    }
}else{
    $json = array(
        'status' => false,
        'message' => 'VOTRE IDENTIFIANT EST INCORRECT. VOUS NE POUVEZ PAS EFFECTUER CETTE ACTION. VEUILLEZ CONTACTER VOTRE ADMINISTRATEUR SYSTEME.'
    );
}

echo json_encode($json);
?>