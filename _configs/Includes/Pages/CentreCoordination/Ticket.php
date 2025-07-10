<?php
require_once '../../../Classes/UTILISATEURS.php';
require_once '../../../Classes/ASSURES.php';
require_once '../../../Classes/TICKETS.php';
require_once '../../../Classes/COORDINATIONS.php';


if(isset($_SESSION['ECMU_USER_ID']) || !empty($_SESSION['ECMU_USER_ID'])) {
    $UTILISATEURS = new UTILISATEURS();
    $user = $UTILISATEURS->trouver($_SESSION['ECMU_USER_ID'], NULL, NULL);
    if (empty($user['ID_UTILISATEUR'])) {
        session_destroy();
        echo '<script>window.location.href="' . URL . '"</script>';
    } else {
        $modules = array_diff(explode(';', stream_get_contents($user['PROFIL'], -1)), array(""));
        $nb_modules = count($modules);
        if ($nb_modules == 0) {
            session_destroy();
            echo '<script>window.location.href="' . URL . '"</script>';
        } else {
            if (in_array('COORD', $modules)) {
                //$ADMINISTRATEURS = new ADMINISTRATEURS();
                $TICKETS = new TICKETS();
                $ASSURES = new ASSURES();
                //$admin = $ADMINISTRATEURS->trouver($_SESSION['ecnam_user_id'], NULL, NULL);
                $COORDINATIONS = new COORDINATIONS();
                $centre = $UTILISATEURS->trouver_centre_coordination($user['ID_UTILISATEUR']);
                $id_ticket = $_POST['id_ticket'];
                $list_action = $TICKETS->lister_ticket_actions($id_ticket);
                $ticket = $TICKETS->moteur_recherche($id_ticket, '', '', '', '', false, $centre['CODE_CENTRE'],null);
                if (!empty($ticket)) {
                    $tick = $ticket[0];

                    if (trim($tick['NUM_SECU'])) {
                        $nom_prenoms_assure = $ASSURES->trouver($tick['NUM_SECU']);
                        if ($nom_prenoms_assure) {
                            $nom_prenoms_assure = "$nom_prenoms_assure[NOM] $nom_prenoms_assure[PRENOM]";
                        } else {
                            $nom_prenoms_assure = '';
                        }

                    } else {
                        $nom_prenoms_assure = "";
                    }
                }

                ?>
                <input id="centre_coordination" hidden value="<?=$centre['CODE_CENTRE'];?>"/>
                <div id="error"></div>
                <div class="two_frames">
                    <div class="card">
                        <h5 class="card-header bg-dark text-light"><span>TICKET N° <a
                                        id='id_ticket'><?php echo $tick['ID_TICKET'] ?></a></span></h5>
                        <div class="card-body">
                            <span class="">STATUT DU TICKET</span></small>
                            <p class="card-text h6 text-dark"><?php
                                echo $tick['LIBELLE_STATUT']; ?> <input id="priseenchargeticket"
                                                                        class="btn btn-xs btn-outline-danger"
                                                                        type="<?php if ($tick['CODE_STATUT'] == 'N') {
                                                                            echo 'button';
                                                                        } else {
                                                                            echo 'hidden';
                                                                        } ?>"
                                                                        value="Prendre en charge le ticket"/></p>
                            <span class="">DATE DE CREATION</span>
                            <p class="card-text h6 text-dark"><?php
                                //setlocale(LC_TIME, "fr_FR");
                                $date = new DateTime(date($tick['DATE_TICKET']));
                                echo $date->format('d-m-Y H:i:s');
                                ?> </p>
                            <span class="">ETABLISSEMENT DE SANTE</span>
                            <p class="card-text h6 text-dark"><?php echo $tick['NOM_ETS']; ?></p>
                            <span class="">TYPE DE REQUETE</span>
                            <p class="card-text h6 text-dark"><?php echo $tick['LIBELLE_TYPE']; ?></p>
                            <span class="">CATEGORIE DE LA REQUETE</span>
                            <p class="card-text h6 text-dark"><?php echo $tick['LIBELLE_CATEGORIE']; ?></p>
                            <span class="">REQUERANT</span>
                            <!--<a href="" class="card-link"></a>--><p
                                    class="card-text h6 text-dark"><?php echo $tick['NOM_USER'] . ' ' . $tick['PRENOM_USER']; ?></p>

                            <?php
                            if ($tick['NUM_SECU'] != NULL || !empty($tick['NUM_SECU'])) {
                                ?>
                                <span class="">NUM ASSURE</span>
                                <!--                        <a class="card-link" href=""></a>-->
                                <p class="card-text h6 text-dark"><?php echo $tick['NUM_SECU']; ?></p>

                                <span class="">ASSURE</span>
                                <p class="card-text h6 text-dark"><?php echo $nom_prenoms_assure; ?></p>
                                <?php
                            }
                            ?>
                            <span class="">TITRE</span>
                            <p class="card-text h6 text-dark"><?php echo $tick['TITRE']; ?></p>
                            <span class="">DESCRIPTION</span>
                            <p class="card-text h6 text-dark"><?php echo $tick['DESCRIPTION']; ?></p>
                        </div>
                    </div>
                    <div class="page-content page-container" id="page-content">
                        <div class="">
                            <div class="row d-flex justify-content-center">
                                <div class="col-md-12">
                                    <div class="card card-bordered">
                                        <div class="card-header">
                                            <h6 class="card-title"><strong>Chat/Actions </strong></h6>
                                            <?php
                                            //$admin['ID_USER'] ='';
                                            if ($tick['CODE_STATUT'] != 'F' && $tick['CODE_STATUT'] != 'A' && $tick['CODE_STATUT'] != 'N')
                                                if ($tick['CODE_STATUT'] == 'R') {
                                                    if ($tick['STATUT_USER_REG'] == $user['ID_UTILISATEUR']) {

                                                    } else {
                                                        echo "<button
                                            class='btn btn-xs btn-warning' data-abc='true' id='changer_statut'>j'ai résolu le ticket</button>";
                                                    }
                                                } else {
                                                    echo "<button
                                            class='btn btn-xs btn-warning' data-abc='true' id='changer_statut'>j'ai résolu le ticket</button>";
                                                }
                                            ?>
                                        </div>
                                        <div class="ps-container ps-theme-default ps-active-y" id="chat-content"
                                             style="overflow-y: scroll !important; height:400px !important;">
                                            <?php
                                            foreach ($list_action as $action) {
                                                ?>
                                                <div class="media media-chat <?php if ($action['USER_REG_ID'] == NULL || ($action['TYPE_USER']=='COORD')) {
                                                    echo 'media-chat-reverse';
                                                } ?>">
                                                    <?php if ($action['USER_REG_ID'] == NULL || ($action['TYPE_USER']=='COORD')) {
                                                        ?>
                                                        <img class="avatar"
                                                             src="https://ecmu.ipscnam.ci/_publics/images/logo_cnam.png"
                                                             alt="...">
                                                        <?php
                                                    } ?>
                                                    <div class="media-body">
                                                        <p><?php echo $action['DESCRIPTION']; ?></p>
                                                        <p class="meta text-dark">
                                                            <time datetime="2018">
                                                                <?php
                                                                $date = new DateTime($action['DATE_REG_A']);
                                                                echo $date->format('d-m-Y H:i:s');
                                                                echo (' ');
                                                                if ($action['USER_REG_ID'] == NULL ||$action['TYPE_USER'] =='COORD') {
                                                                    if($action['TYPE_USER']=='COORD'){
                                                                        echo $action['NOM_USER'] . ' ' . $action['PRENOM_USER'] .' ('.$action['TYPE_USER'].')' ;
                                                                    }
                                                                    else{
                                                                        echo $action['NOM_ADMIN'] . ' ' . $action['PRENOM_ADMIN'];
                                                                    }

                                                                } else {
                                                                    echo $action['NOM_USER'] . ' ' . $action['PRENOM_USER'] ;
                                                                }
                                                                ?></time>
                                                        </p>
                                                    </div>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                            <div class="ps-scrollbar-x-rail" style="left: 0px; bottom: 0px;">
                                                <div class="ps-scrollbar-x" tabindex="0"
                                                     style="left: 0px; width: 0px;"></div>
                                            </div>
                                            <div class="ps-scrollbar-y-rail" style="top: 0px; height: 0px; right: 2px;">
                                                <div class="ps-scrollbar-y" tabindex="0"
                                                     style="top: 0px; height: 2px;"></div>
                                            </div>
                                        </div>
                                        <?php
                                        if ($tick['CODE_STATUT'] != 'F' && $tick['CODE_STATUT'] != 'A' && $tick['CODE_STATUT'] != 'N') {
                                            echo '<div class="publisher bt-1 border-light"><img class="avatar avatar-xs"
                                                                              src="https://ecmu.ipscnam.ci/_publics/images/logo_cnam.png"
                                                                              alt="...">
                                    <input class="publisher-input" type="text" id="message" placeholder="Votre message">
                                    <span class="publisher-btn file-group"> <i class="fa fa-paperclip file-browser">
                            </i> <input type="file">
                        </span>
                                    <button class="publisher-btn text-info" id="add_action" data-abc="true">
                                        <i class="fa fa-paper-plane"></i></button>
                                </div>';
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <script type="text/javascript" src="<?= JS.'page_centre_coordination_tickets.js'?>"></script>

                <?php
            }
        }
    }
} else {
    session_destroy();
    echo '<script>window.location.href="' . URL . '"</script>';
}
?>
