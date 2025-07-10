<?php
/**
 * Created by PhpStorm.
 * User: mohamed.kone
 * Date: 20/04/2021
 * Time: 12:25
 */

require_once '../../Classes/DUPLICATA.php';
require_once '../../Classes/ASSURES.php';


$num_secu = $_POST['num_secu'];
$nom = strtoupper($_POST['nom']);
$prenom = strtoupper($_POST['prenom']);
$date_naissance = date("Y-m-d",strtotime(str_replace("/","-",$_POST['date_naissance'])));
$telephone = $_POST['telephone'];
$type_piece = $_POST["type_piece"];
$num_piece = $_POST["num_piece"];
$date_fin_validite = date("Y-m-d",strtotime(str_replace("/","-",$_POST["date_fin_validite"])));
$motif_demande = $_POST["motif_demande"];

$id_demande =  strtoupper(uniqid(str_replace('.','',date('dmy',time())).substr($num_secu,-6)));

$extensions = array("jpeg", "jpg", "png","pdf");
if(!empty($_FILES['file']['name'])){
    $extension_file_1 = pathinfo($_FILES['file']['name'],PATHINFO_EXTENSION);
    if(in_array($extension_file_1,$extensions)){
        $image_scan_piece_input = $id_demande.'_PIECE.'.$extension_file_1;
        $image_scan_piece_temp = $_FILES['file']['tmp_name'];
    }else{
        $a = array(
            'status' => 'failed',
            'message' => 'L\'EXTENSION DE LA PIECE EST INCORRECTE, VEUILLEZ CHOISIR UNE IMAGE AU FORMAT JPEG'
        );
    }

}else{
    $image_scan_piece_input = NULL;
    $image_scan_piece_temp = NULL;
}

if(!empty($_FILES['file1']['name'])){
    $extension_file_2 = pathinfo($_FILES['file1']['name'],PATHINFO_EXTENSION);
    if(in_array($extension_file_2,$extensions)){
        $image_declaration_perte = $id_demande.'_DECLARATION_PERTE.'.$extension_file_2;
        $image_declaration_temp = $_FILES['file1']['tmp_name'];
    }else{
        $a = array(
            'status' => 'failed',
            'message' => 'L\'EXTENSION DE LA DECLARATION DE PERTE EST INCORRECTE, VEUILLEZ CHOISIR UNE IMAGE AU FORMAT JPEG'
        );
    }

}else{
    $image_declaration_perte = NULL;
    $image_declaration_temp = NULL;
}

if(!empty($_FILES['file2']['name'])){
    $extension_file_3 = pathinfo($_FILES['file2']['name'],PATHINFO_EXTENSION);
    if(in_array($extension_file_3,$extensions)){
         $image_carte_abimee = $id_demande.'_CARTE_ABIMEE.'.$extension_file_3;
         $image_carte_abimee_temp = $_FILES['file2']['tmp_name'];
    }else{
        $a = array(
            'status' => 'failed',
            'message' => 'L\'EXTENSION DE LA CARTE ABIMEE EST INCORRECTE, VEUILLEZ CHOISIR UNE IMAGE AU FORMAT JPEG'
        );
    }
}else{
    $image_carte_abimee = NULL;
    $image_carte_abimee_temp = NULL;
}



$DUPLICATA = new DUPLICATA();
$ASSURE = new ASSURES();

$verif_num_secu = $ASSURE->trouver($num_secu);
if($verif_num_secu){
    $chemin = DIR.'IMPORTS/DUPLICATA/';
    $dossier = $num_secu.'/';
    /**
     *  VERIFICATION STATUT NUM_SECU DEMANDEUR
     */
    $verif_statut_carte = $DUPLICATA->verif_statut_carte($num_secu);
    if($verif_statut_carte) {
        if($verif_statut_carte['STATUT_VALIDATION'] == '0' || $verif_statut_carte['STATUT_VALIDATION'] == '1'){
            $verification = 0;
        }else {
            $verification = 1;
        }
    }else {
        $verification = 1;
    }

    if($verification === 1) {
        if(!file_exists($chemin)) {
            if (!mkdir($chemin, 0777, true) && !is_dir($chemin)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $chemin));
            }
        }
        if(!file_exists($chemin . $dossier)) {
            if (!mkdir($concurrentDirectory = $chemin . $dossier, 0777, true) && !is_dir($concurrentDirectory)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
            }
            if(!empty($image_scan_piece_temp)){
                move_uploaded_file($image_scan_piece_temp, $chemin . $dossier . $image_scan_piece_input);
            }
            if(!empty($image_declaration_temp)){
                move_uploaded_file($image_declaration_temp,$chemin.$dossier.$image_declaration_perte);
            }
            if(!empty($image_carte_abimee_temp)){
                move_uploaded_file($image_carte_abimee_temp,$chemin.$dossier.$image_carte_abimee);
            }
        }
        else{
            if(!empty($image_scan_piece_temp)){
                move_uploaded_file($image_scan_piece_temp, $chemin . $dossier . $image_scan_piece_input);
            }
            if(!empty($image_declaration_temp)){
                move_uploaded_file($image_declaration_temp,$chemin.$dossier.$image_declaration_perte);
            }
            if(!empty($image_carte_abimee_temp)){
                move_uploaded_file($image_carte_abimee_temp,$chemin.$dossier.$image_carte_abimee);
            }
        }
		$a = $DUPLICATA->editer_reedition_carte($id_demande,$num_secu,$nom,$prenom,$date_naissance,$telephone,$type_piece,$num_piece,$date_fin_validite,$image_scan_piece_input,$motif_demande,$image_carte_abimee,$image_declaration_perte);

		if($a['status'] == 'success'){
			$message = "CHER(E) {$num_secu}, VOTRE DEMANDE DE REEDITION DE CARTE A ETE PRISE EN COMPTE; LE NUMERO DE SUIVI EST: {$id_demande}.";

			$adresse_ip = '10.10.4.7/';
			//$adresse_ip = 'recette-ecnam.ipscnam.ci/';
			//$adresse_ip = 'developpement.ipscnam.ci/ecnam/';
			$url = "https://".$adresse_ip."webservices/envoi-sms.php";
			$parametres =[
				'type_envoi' => 'I',
				'numero' => $telephone,
				'message' => $message];
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLINFO_HEADER_OUT, true);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $parametres);


			$result = curl_exec($ch);

			curl_close($ch);

			$retour = json_decode($result);
			if($retour->success == true){
				$json = $a;
			}
		}
		else{
			$json = array(
				'status' => 'failed',
				'message' => 'VOTRE DEMANDE A ECHOUE. PRIERE CONTACTER LE SUPPORT.'
			);
		}
    }else {
        $json = array(
            'status' => 'failed',
            'message' => 'VOUS NE POUVEZ PAS EFFECTUER DE DEMANDE EN CE MOMENT, VOUS AVEZ UNE DEMANDE DEJA EN COURS DE TRAITEMENT'
        );
    }

}else{
    $json = array(
        'status' => 'failed',
        'message' => 'CE NUMERO DE SECURITE SOCIALE N\'EXISTE PAS'
    );
}
echo json_encode($json,NULL);