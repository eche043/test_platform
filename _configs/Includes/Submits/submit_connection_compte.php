<?php
/**
 * Created by PhpStorm.
 * User: mohamed.kone
 * Date: 05/02/2020
 * Time: 08:19
 */

$username = trim($_POST['username']);
$password = trim($_POST['password']);

if(empty($username) || empty($password)){
    $json = array(
        'status' => true,
        'message' => 'L\'<strong>Identifiant / Email</strong> et le <strong>Mot de passe </strong> sont obligatoires'
    );
}else{
    require_once '../../Classes/UTILISATEURS.php';
    $UTILISATEUR = new UTILISATEURS();
    $connexion = $UTILISATEUR->connexion($username,$password);
    if($connexion['status'] == true){
//        session_start();
        $_SESSION['ECMU_USER_ID'] = $connexion['user_id'];
		$maj_derniere_connexion = $UTILISATEUR->mise_a_jour_derniere_connexion($connexion['user_id']);
        $json = array(
            'status' => true
        );
    }else {
        $json = $connexion;
    }
}
echo json_encode($json,NULL);