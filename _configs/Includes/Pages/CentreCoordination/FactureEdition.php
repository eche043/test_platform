<?php
require_once '../../../Classes/UTILISATEURS.php';

if(isset($_SESSION['ECMU_USER_ID']) || !empty($_SESSION['ECMU_USER_ID'])) {
    $UTILISATEURS = new UTILISATEURS();
    $user = $UTILISATEURS->trouver($_SESSION['ECMU_USER_ID'],NULL,NULL);
    if(empty($user['ID_UTILISATEUR'])) {
        session_destroy();
        echo '<script>window.location.href="'.URL.'"</script>';
    }else {
        $modules = array_diff(explode(';',stream_get_contents($user['PROFIL'],-1)),array(""));
        $nb_modules = count($modules);
        if($nb_modules == 0) {
            session_destroy();
            //echo '<script>window.location.href="'.URL.'"</script>';
        }else{
            if(in_array('COORD',$modules)) {
                require_once '../../../Classes/FACTURES.php';
                require_once '../../../Classes/ETABLISSEMENTSSANTE.php';
                require_once '../../../Classes/COORDINATIONS.php';
                $FACTURES = new FACTURES();
                $ETABLISSEMENTSSANTE = new ETABLISSEMENTSSANTE();
                $COORDINATIONS = new COORDINATIONS();
                $centre = $UTILISATEURS->trouver_centre_coordination($user['ID_UTILISATEUR']);
                if($centre){
                    $ets = $COORDINATIONS->trouver_ets($centre['CODE_CENTRE'],$_POST['code_ets']);

                    if($ets)
                    {
                        if(isset($_POST['type']) && !empty($_POST['type']) && isset($_POST['num']) && !empty($_POST['num']))
                        {
                            require_once '../../../Classes/FACTURES.php';
                            $FACTURES = new FACTURES();
                            $facture = $FACTURES->trouver($_POST['num']);
                            if(!empty($facture['FEUILLE'])) {
                                if($ets['CODE_ETS'] == $facture['ETABLISSEMENT'])
                                {
                                    require_once '../../../Classes/ASSURES.php';
                                    $ASSURES = new ASSURES();
                                    $assure = $ASSURES->trouver($facture['NUM_SECU']);
                                    if(!empty($assure['NUM_SECU'])) {
                                        if(!empty($facture['PS']))
                                        {
                                            $ps = $FACTURES->verifier_facture_ps($facture['PS'],NULL,$facture['ETABLISSEMENT'],date('Y-m-d',strtotime($facture['DATE_SOINS'])));
                                        }else{
                                            $ps = array(
                                                'code_ps' => NULL,
                                                'nom_prenom' => NULL,
                                                'code_specialite' => NULL,
                                                'libelle_specialite' => NULL
                                            );
                                        }
                                        $facture_initiale = $FACTURES->trouver($facture['NUM_FS_INITIALE']);
                                        if(!empty($facture_initiale['PS'])){
                                            $ps_initiale = $FACTURES->verifier_facture_ps($facture_initiale['PS'],NULL,$facture_initiale['ETABLISSEMENT'],strtoupper(date('Y-m-d',strtotime($facture_initiale['DATE_SOINS']))));
                                        }else{
                                            $ps_initiale = array(
                                                'code_ps' => NULL,
                                                'nom_prenom' => NULL,
                                                'code_specialite' => NULL,
                                                'libelle_specialite' => NULL
                                            );
                                        }
                                        $genre = $ASSURES->trouver_genre($facture['GENRE']);
                                        $type = $FACTURES->trouver_type_facture($_POST['type']);
                                        if(!empty($type['CODE'])) {
                                            $affichage = 0;
                                            if(empty($facture['TYPE_FEUILLE'])) {
                                                if($type['CODE'] == 'MED') {$affichage = 0;}else {$affichage++;}
                                            }else {
                                                if($facture['TYPE_FEUILLE'] == $type['CODE']) {$affichage++;}else {$affichage = 0;}
                                            }
                                            if($affichage != 0) {
                                                ?>
                                                <div id="messages_erreur"></div>
                                                <div class="container" id="div_facture">
                                                    <p class="titres_p"><small>Bon de prise en charge médicale</small> <?= $type['LIBELLE'];?> (<b id="type_facture_b"><?= $type['CODE'];?></b>)</p>
                                                    <p class="align_center" id="p_resultats"></p>
                                                    <?php include "../Forms/form_facture.php";?>
<!--                                                    <script type="application/javascript" src="--><?//= JS.'page_centre_saisie_facture.js';?><!--"></script>-->
                                                        <script type="application/javascript" src="<?= JS.'page_centre_coordination_facture.js?v=1';?>"></script>
                                                </div>
                                                <?php
                                            }else {echo 'test';
                                                //echo '<script>window.location.href="'.URL.'centre-coordination/"</script>';
                                            }

                                        }else {echo 'test';
                                            //echo '<script>window.location.href="'.URL.'centre-coordination/"</script>';
                                        }
                                    }else {echo 'test';
                                        //echo '<script>window.location.href="'.URL.'centre-coordination/"</script>';
                                    }
                                }else {echo 'test';
                                    //echo '<script>window.location.href="'.URL.'centre-coordination/"</script>';
                                }
                            }else{echo 'test';
                                //echo '<script>window.location.href="'.URL.'centre-coordination/"</script>';
                            }
                        }else {echo 'test';
                            //echo '<script>window.location.href="'.URL.'centre-coordination/"</script>';
                        }
                        ?>



                        <script type="text/javascript" src="<?= JS.'page_centre_coordination.js'?>"></script>
                        <?php
                    }else{echo 'test';
                        //echo '<script>window.location.href="'.URL.'centre-coordination/"</script>';
                    }
                }
                else{echo 'test';
                    //echo '<script>window.location.href="'.URL.'"</script>';
                }
            }
        }
    }
}else{
    session_destroy();
    echo '<script>window.location.href="'.URL.'"</script>';
}
?>
<script>
    $(function () {
        $('.dataTable').DataTable();
    });

</script>