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
            echo '<script>window.location.href="'.URL.'"</script>';
        }else{
            if(in_array('APA',$modules)) {
                $user_apa = $UTILISATEURS->trouver_utilisateur_partenaire($user['ID_UTILISATEUR']);
                if(count($user_apa)!=0) {
                    require_once '../../../Classes/PARTENAIRES.php';
                    $PARTENAIRES = new PARTENAIRES();
                    if(!empty($user_apa['CODE_PARTENAIRE'])){
                        $partenaire = $PARTENAIRES->trouver($user_apa['CODE_PARTENAIRE']);
                        $user_hab = explode(';',$user['FSE']);
                        if(in_array('DUPLICATA', $user_hab)) {
                            $demandes_duplicata = $PARTENAIRES->lister_demandes_en_attente_traitement(1);
                            $liste_motifs_demande = $PARTENAIRES->lister_motifs();
                            ?>
                            <div class="col">
                                <p class="titres_p"><i class="fa fa-handshake"></i> Partenaires: <b class="text-danger"><?=$partenaire['LIBELLE_PARTENAIRE'];?></b></p>
                                <hr />
                                <div>
                                    <form id="search_demandes_duplicata_form">

                                        <div class="form-group row">
                                            <div class="col-sm-2">
                                                <input type="text" class="form-control form-control-sm" id="numero_secu_demande_duplicata_input" name="numero_secu_demande_duplicata_input" autocomplete="off"  placeholder="Numero Sécu Demande" />
                                            </div>
                                            <div class="col-sm-2">
                                                <input type="text" class="form-control form-control-sm" id="numero_suivi_demande_duplicata_input" name="numero_suivi_demande_duplicata_input" autocomplete="off"  placeholder="Numero Suivi Demande" />
                                            </div>
                                            <div class="col-sm-2">
                                                <input type="text" class="form-control form-control-sm datepicker_moteur_rech" id="date_debut_periode_demande_input" name="date_debut_periode_demande_input" autocomplete="off" placeholder="Date début" value="<?= date('d/m/Y',strtotime('-1 week',time())); ?>" readonly />
                                            </div>
                                            <div class="col-sm-2">
                                                <input type="text" class="form-control form-control-sm datepicker_moteur_rech" id="date_fin_periode_demande_input" name="date_fin_periode_demande_input" autocomplete="off" placeholder="Date fin" value="<?= date('d/m/Y',time()); ?>" readonly />
                                            </div>
                                            <div class="col">
                                                <select name="" id="motif_demande_select" class="form-control form-control-sm">
                                                    <option value="">-- Motif Demande --</option>
                                                    <?php
                                                     foreach ($liste_motifs_demande as $motif){
                                                         ?>
                                                         <option value="<?=$motif['MOTIF_CODE'];?>"><?=$motif['MOTIF_LIBELLE'];?></option>
                                                        <?php
                                                     }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col">
                                                <select name="" id="statut_demande_select" class="form-control form-control-sm">
                                                    <option value="">-- Statut Demande --</option>
                                                    <option value="CAT">CARTE EN ATTENTE DE TRAITEMENT</option>
                                                    <option value="CAP">CARTE EN ATTENTE DE PRODUCTION</option>
                                                    <option value="CAA">CARTE EN ATTENTE D'ACHEMINEMENT</option>
                                                    <option value="CAR">CARTE EN ATTENTE DE RETRAIT</option>
                                                </select>
                                            </div>
                                            <div class="col-sm-1">
                                                <button type="submit" class="btn btn-primary btn-sm btn-block" id="search_demandes_duplicata_btn"><i class="fa fa-search"></i></button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div id="resultats_div">
                                    <div class="row">
                                        <div id="div_afficher_liste_populations_collectivite" class="col">
                                            <?php
                                            $total_demande = count($demandes_duplicata);
                                            if($total_demande==0){
                                                ?>
                                                <p class="alert alert-danger" align="center">AUCUNE DEMANDE EN ATTENTE.</p>
                                                <?php
                                            }
                                            else{
                                                $ver_pai = $total_demande;
                                                foreach ($demandes_duplicata as $demande) {
                                                    $motif = $PARTENAIRES->trouver_motif($demande['MOTIF_DEMANDE']);
                                                    if($motif['STATUT_PAIEMENT'] == 1 ){
                                                        if(empty($demande['NUM_TRANSACTION_PAIEMENT'])){
                                                            $ver_pai --;
                                                        }
                                                    }
                                                }

                                                if($ver_pai==0){
                                                    echo '<p class="alert alert-danger" align="center">AUCUNE DEMANDE EN ATTENTE.</p>';
                                                }
                                                else{
                                                    ?>
                                                    <table class="table table-bordered table-hover table-sm table-responsive-sm dataTable">
                                                        <thead class="bg-secondary text-white">
                                                        <tr align="center">
                                                            <td width="5"></td>
                                                            <td>N° SECU </td>
                                                            <td>NOM</td>
                                                            <td>PRENOMS</td>
                                                            <td>DATE NAISSANCE</td>
                                                            <td>MOTIF DEMANDE</td>
                                                            <td width="5"></td>
                                                        </tr>
                                                        </thead>

                                                        <tbody>
                                                        <?php
                                                        $i=1;
                                                        foreach ($demandes_duplicata as $demande) {
                                                            $pai = 1;
                                                            $motif = $PARTENAIRES->trouver_motif($demande['MOTIF_DEMANDE']);
                                                            if($motif['STATUT_PAIEMENT'] == 1 ){
                                                                if(empty($demande['NUM_TRANSACTION_PAIEMENT'])){
                                                                    $pai = 0;
                                                                }
                                                            }
                                                            if($pai==1){
                                                            ?>
                                                            <tr>
                                                                <td width="5" align="center"><?=$i;?></td>
                                                                <td align="center"><?=$demande['NUM_SECU'];?></td>
                                                                <td align="center"><?=$demande['NOM'];?></td>
                                                                <td align="center"><?=$demande['PRENOMS'];?></td>
                                                                <td align="center"><?=date('d/m/Y',strtotime($demande['DATE_NAISSANCE']));?></td>
                                                                <td align="center"><?=$motif['MOTIF_LIBELLE'];?></td>
                                                                <td><a href="<?=URL.'partenaire/demande-duplicata.php?id='.$demande['ID_DEMANDE'];?>" class="badge badge-info details_population" id="<?=$demande['ID_DEMANDE'];?>"><i class="fa fa-eye"></i></a></td>
                                                            </tr>
                                                            <?php
                                                            }elseif($pai==0){ ?>
                                                                <tr>
                                                                <td width="5" align="center"><?=$i;?></td>
                                                                <td align="center"><?=$demande['NUM_SECU'];?></td>
                                                                <td align="center"><?=$demande['NOM'];?></td>
                                                                <td align="center"><?=$demande['PRENOMS'];?></td>
                                                                <td align="center"><?=date('d/m/Y',strtotime($demande['DATE_NAISSANCE']));?></td>
                                                                <td align="center"><?=$motif['MOTIF_LIBELLE'];?></td>
                                                                <td><a href="<?=URL.'partenaire/demande-duplicata.php?id='.$demande['ID_DEMANDE'];?>" class="badge badge-info details_population" id="<?=$demande['ID_DEMANDE'];?>"><i class="fa fa-eye"></i></a></td>
                                                            </tr>
                                                            <?php }
                                                            $i++;
                                                        }
                                                        ?>
                                                        </tbody>
                                                    </table>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <script type="text/javascript" src="<?= JS.'page_partenaire.js'?>"></script>
                            <script>
                                $(".datepicker_moteur_rech").datepicker({
                                    changeMonth: false,
                                    changeYear: false,
                                    maxDate: 0
                                });
                            </script>
                        <?php
                        }else {
                            echo '<script>window.location.href="'.URL.'"</script>';
                        }
                    }else{
                        echo '<script>window.location.href="'.URL.'"</script>';
                    }
                }else {
                    echo '<script>window.location.href="'.URL.'"</script>';
                }
            }
        }
    }
}else{
    session_destroy();
    echo '<script>window.location.href="'.URL.'"</script>';
}
?>