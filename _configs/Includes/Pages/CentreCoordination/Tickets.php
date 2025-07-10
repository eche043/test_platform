<?php
require_once '../../../Classes/UTILISATEURS.php';

if(isset($_SESSION['ECMU_USER_ID']) || !empty($_SESSION['ECMU_USER_ID'])) {
    $UTILISATEURS = new UTILISATEURS();
    $user = $UTILISATEURS->trouver($_SESSION['ECMU_USER_ID'],NULL,NULL);
    if(empty($user['ID_UTILISATEUR'])) {
        session_destroy();
        echo '<script>window.location.href="'.URL.'"</script>';
    }
    else
    {
        $modules = array_diff(explode(';',stream_get_contents($user['PROFIL'],-1)),array(""));
        $nb_modules = count($modules);
        if($nb_modules == 0) {
            session_destroy();
            echo '<script>window.location.href="'.URL.'"</script>';
        }else{
            if(in_array('COORD',$modules)) {
                require_once '../../../Classes/COORDINATIONS.php';
                require_once '../../../Classes/TICKETS.php';
                require_once '../../../Classes/ASSURES.php';


                $ASSURES = new ASSURES();
                $COORDINATIONS = new COORDINATIONS();
                $centre = $UTILISATEURS->trouver_centre_coordination($user['ID_UTILISATEUR']);

                $TICKETS = new TICKETS();

                $categories = $TICKETS->lister_ticket_categorie();
                $types = $TICKETS->lister_ticket_type();
                $list_status_ticket =$TICKETS->afficher_liste_status_ticket();
                $tickets_list = $TICKETS->moteur_recherche('', '', '', '', 'N;C;R',true,$centre['CODE_CENTRE']);
                if($centre){
                    $etablissements = $COORDINATIONS->lister_ets($centre['CODE_CENTRE']);
                    $nb_ets = count($etablissements);
                    ?>
                    <p class="titres_p"><i class="fa fa-newspaper"></i> Liste des tickets</p>
                    <form id="form_recherche_ticket">
                        <div class="form-row align-items-center">
                            <div class="col-sm-3 my-1">
                                <label class="sr-only" for="code_ets">ETS</label>
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control form-control-sm" maxlength="" id="nom_ets" placeholder="ETS" autocomplete="off" />
                                </div>
                                <div class="col-md-12" style="position: absolute; z-index: 10">
                                    <table class="list-group table table-bordered table-hover" id="show-list">
                                    </table>
                                </div>
                            </div>

                            <div class="col-sm-2 my-1">
                                <label class="sr-only" for="statut">Statut</label>
                                <div class="input-group input-group-sm">
                                    <select class="form-control form-control-sm custom-select" id="statut" aria-label="Statut">
                                        <option value="">Tous Statut</option>
                                        <?php
                                        //var_dump($list_status_ticket);

                                        foreach($list_status_ticket as $statut) {
                                            echo '<option value="'.$statut['CODE_STATUT'].'">'.$statut['LIBELLE'].'</option>';
                                        }
                                        ?>

                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-2 my-1">
                                <label class="sr-only" for="categorie_input">Categorie</label>
                                <div class="input-group input-group-sm">
                                    <select class="form-control form-control-sm custom-select" id="categorie_input" aria-label="Statut">
                                        <option value="">Toutes Categories</option>
                                        <?php
                                        //var_dump($categories);
                                        foreach($categories as $categorie) {
                                            echo '<option value="'.$categorie['id'].'">'.$categorie['libelle'].'</option>';
                                        }
                                        ?>

                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-2 my-1">
                                <label class="sr-only" for="type_input">Type</label>
                                <div class="input-group input-group-sm">
                                    <select class="form-control form-control-sm custom-select" id="type_input" aria-label="Statut">
                                        <option value="">Tous Types</option>
                                        <?php
                                        foreach($types as $type) {
                                            echo '<option value="'.$type['id'].'">'.$type['libelle'].'</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div  class="col-sm my-1"><input id="centre_coordination" hidden value="<?=$centre['CODE_CENTRE'];?>"/></div>
                            <div class="col-sm-1 my-1">
                                <button type="submit" class="btn btn-sm btn-dark btn-block" id="button_recherche"><i class="fa fa-search"></i></button>
                            </div>
                        </div>
                    </form><hr />
                    <div id="div_resultats">
                        <div class="row">
                            <div class="col-sm-12">
                                <table class="table table-bordered rounded-lg table-sm table-hover" id="tickets_table">
                                    <thead class="bg-info">
                                    <tr>
                                        <th style="width: 5px">N°</th>
                                        <th>DATE CREATION</th>
                                        <th>N° SECU</th>
                                        <th>ASSURE</th>
                                        <th>TYPE</th>
                                        <th>CATEGORIE</th>
                                        <th>ETABLISSEMENT</th>
                                        <th>CENTRE DE COORDINATION</th>
                                        <th style="width: 110px">STATUT</th>
                                        <th style="width: 5px"></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $ligne = 1;
                                    foreach ($tickets_list as $tick) {
                                        if(trim($tick['NUM_SECU'])){
                                            $nom_prenoms_assure=$ASSURES->trouver($tick['NUM_SECU']);
                                            if($nom_prenoms_assure){
                                                $nom_prenoms_assure = "{$nom_prenoms_assure['NOM']} {$nom_prenoms_assure['PRENOM']}";
                                            }
                                            else{
                                                $nom_prenoms_assure = '';
                                            }

                                        }
                                        else{
                                            $nom_prenoms_assure="";
                                        }
                                        $date = new DateTime(date($tick['DATE_TICKET']));

                                        ?>
                                        <tr>
                                            <td><b><?= $tick['ID_TICKET']; ?></b></td>
                                            <td><b><?= $date->format('d-m-Y H:i:s'); ?></b></td>
                                            <td><b><?= $tick['NUM_SECU']; ?></b></td>
                                            <td><b><?= $nom_prenoms_assure; ?></b></td>
                                            <td><b><?= $tick['LIBELLE_TYPE']; ?></b></td>
                                            <td><b><?= $tick['LIBELLE_CATEGORIE']; ?></b></td>
                                            <td><?= $tick['NOM_ETS']; ?></td>
                                            <td><?= $tick['LIBELLE_CENTRE']; ?></td>
                                            <td><?= $tick['LIBELLE_STATUT']; ?></td>
                                            <td>
                                                <a href="<?= URL . 'centre-coordination/ticket.php?id=' . $tick['ID_TICKET']; ?>"
                                                   class="badge badge-info"><i class="fa fa-eye"></i></a></td>
                                        </tr>
                                        <?php
                                        $ligne++;
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <script type="text/javascript" src="<?= JS.'page_centre_coordination_tickets.js'?>"></script>
                    <?php
                }
                else{
                    echo '<br><p class="align_center alert alert-danger">AUCUN CENTRE DE COORDINATION DEFINI. PRIERE CONTACTER UN ADMINISTRATEUR.</p>';
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
        $('#dataTable').DataTable();
    });

</script>
