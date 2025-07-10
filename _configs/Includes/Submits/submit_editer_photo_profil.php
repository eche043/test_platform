<?php

require_once '../../../_configs/Classes/UTILISATEURS.php';

if(isset($_SESSION['ECMU_USER_ID']) || !empty($_SESSION['ECMU_USER_ID'])) {
    $UTILISATEURS = new UTILISATEURS();
    $user = $UTILISATEURS->trouver($_SESSION['ECMU_USER_ID'],NULL,NULL);
//    var_dump($user);
    if(empty($user['ID_UTILISATEUR'])) {
        session_destroy();
        echo '<script>window.location.href="'.URL.'connexion.php"</script>';
    }else {
        $modules = array_diff(explode(';',stream_get_contents($user['PROFIL'],-1)),array(""));
        $nb_modules = count($modules);
        if($nb_modules == 0) {
            session_destroy();
            echo '<script>window.location.href="'.URL.'connexion.php"</script>';
        }else{
            $_FILES['file']['name'];
            $Time = @date('Y-m-d H:i:s',time());
            if(!empty($_FILES['file']['name'])){
                $nouveau_nom = substr(md5($Time) , 0 , 20 ) . ".jpg" ;
                $chemin = DIR.'_publics/images/photos_profils/';
                $dossier = $user['NOM'].$user['ID_UTILISATEUR'].'/';
                if(!file_exists($chemin)) {
                    mkdir($chemin,0777,true);
                    if(!file_exists($chemin.$dossier)){
                        if(mkdir($chemin.$dossier,0777,true)){
                            move_uploaded_file($_FILES['file']['tmp_name'], $chemin.$dossier.$nouveau_nom);
                        }
                    }
                }else{
                    if(!file_exists($chemin.$dossier)){
                        if(mkdir($chemin.$dossier,0777,true)){
                            move_uploaded_file($_FILES['file']['tmp_name'], $chemin.$dossier.$nouveau_nom);
                        }
                    }else{
                        move_uploaded_file($_FILES['file']['tmp_name'], $chemin.$dossier.$nouveau_nom);
                    }
                }

            }
            else{
                $nouveau_nom = '';
            }
echo $chemin;
            $photo_modi = $UTILISATEURS->editer_photo_profil($nouveau_nom,$user['ID_UTILISATEUR'],$user['ID_UTILISATEUR']);
            echo json_encode($photo_modi);

        }

    }

}else{
    session_destroy();
    echo '<script>window.location.href="'.URL.'connexion.php"</script>';
}


