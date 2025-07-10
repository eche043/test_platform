<?php
    require_once '../../../Classes/UTILISATEURS.php';
    if(isset($_SESSION['ECMU_USER_ID'])) {
    echo '<script>window.location.href="'.URL.'"</script>';
}else {
    require_once '../../../Classes/RECRUTEMENT.php';
    $RECRUTEMENT = new RECRUTEMENT();
    $id_compte = $_POST['id_agac'];
    $trouver_compte = $RECRUTEMENT->trouver_telephone_compte($id_compte);
    $telephone = $trouver_compte['NUMERO_TELEPHONE'];
    $info_biographique = $RECRUTEMENT->trouver_infos_biographique($id_compte);
    $info_identification_visuelle = $RECRUTEMENT->trouver_infos_identification_visuelle($id_compte);
    $info_familiale = $RECRUTEMENT->trouver_infos_famille($id_compte);
    $info_affectation = $RECRUTEMENT->trouver_infos_affectation($id_compte);
    $nationalites  = $RECRUTEMENT->liste_nationalite();
    $sexes = $RECRUTEMENT->lister_sexe();
    $documents = $RECRUTEMENT->lister_type_piece();
    $statut_matrimoniales = $RECRUTEMENT->lister_situation_familiale();
    $professions = $RECRUTEMENT->liste_profession();
?>
<div class="col">
    <div class="row justify-content-md-center">
        <div class="col col-sm-10" id="div_login">
            <div class="col-sm-12 mb-4">
                <div class="row" id="div_resume_infos_renseigne">
                    <div class="col">
                        <div class="row">
                            <div class="col-sm-12 text-center">
                                <h2 class="">PROCESSUS TERMINÉ</h2>
                                <a href="<?=URL."recrutement/";?>" id="btn_deconnexion_recrutement" class="btn btn-danger">
                                    <i class="fa fa-window-close" aria-hidden="true"></i> QUITTER</a>
                                <hr>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <table class="table table-bordered table-striped table-hover table-sm">
                                    <thead class="bg-info">
                                    <tr class="text-white">
                                        <th colspan="2"><b> IDENTIFICATION VISUELLE</b></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td class="text-center">
                                            <?php   $image_mime_type = "image/png"; // Type MIME de l'image
                                                $image_base64 = stream_get_contents($info_identification_visuelle['PHOTO']);
                                                echo '<img src="data:'.$info_identification_visuelle["TYPE_PHOTO"].';base64,' . $image_base64 . '" alt="Image" width="100" height="100">';
                                            ?>
                                        </td>
                                    </tr>


                                    </tbody>
                                    <tbody>
                                </table>

                                <table class="table table-bordered table-striped table-hover table-sm mb-4">
                                    <thead class="bg-info">
                                    <tr class="text-white">
                                        <th colspan="2"><b>AFFECTATION</b></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>CENTRE DE SANTÉ</td>
                                        <td><b class="text-danger">
                                                <?php
                                                    $trouver_centre = $RECRUTEMENT->trouver_centre_agac($telephone);
                                                    echo $trouver_centre["STRUCTURE_SANITAIRE"]
                                                ?>
                                            </b>
                                        </td>
                                    </tr>


                                    </tbody>
                                    <thead class="bg-info">
                                    <tr class="text-white">
                                        <th colspan="2"><b>EN CAS D'URGENCE CONTACTER</b></th>
                                    </tr>
                                    </thead>
                                    <tr>
                                        <td>NOM & PRENOM(S)</td>
                                        <td><b><?= $info_familiale['NOM_PERSONNE_URGENCE'] ?></b></td>
                                    </tr>
                                    <tr>
                                        <td>CONTACT</td>
                                        <td><b><?= $info_familiale['TELEPHONE_PERSONNE_URGENCE'] ?></b></td>
                                    </tr>
                                    <tr>
                                        <td>NOM & PRENOM(S)</td>
                                        <td><b><?= $info_familiale['NOM_PERSONNE_URGENCE_DEUX'] ?></b></td>
                                    </tr>
                                    <tr>
                                        <td>CONTACT</td>
                                        <td><b><?= $info_familiale['TELEPHONE_PERSONNE_URGENCE_DEUX'] ?></b></td>
                                    </tr>
                                    <tr>
                                        <td>NOM & PRENOM(S)</td>
                                        <td><b><?= $info_familiale['NOM_PERSONNE_URGENCE_TROIS'] ?></b></td>
                                    </tr>
                                    <tr>
                                        <td>CONTACT</td>
                                        <td><b><?= $info_familiale['TELEPHONE_PERSONNE_URGENCE_TROIS'] ?></b></td>
                                    </tr>
                                    <tbody>

                                    </tbody>
                                    <thead class="bg-info">
                                    <tr class="text-white">
                                        <th colspan="2"><b>INFORMATIONS BANCAIRES</b></th>
                                    </tr>
                                    </thead>
                                    <tr>
                                        <td>CODE BANQUE</td>
                                        <td><b><?= $info_biographique['CODE_BANQUE'] ?></b></td>
                                    </tr>
                                    <tr>
                                        <td>NOM BANQUE</td>
                                        <td><b><?= $info_biographique['NOM_BANQUE'] ?></b></td>
                                    </tr>
                                    <tr>
                                        <td>CODE GUICHET</td>
                                        <td><b><?= $info_biographique['CODE_GUICHET'] ?></b></td>
                                    </tr>
                                    <tr>
                                        <td>NUMERO COMPTE</td>
                                        <td><b><?= $info_biographique['NUMERO_COMPTE'] ?></b></td>
                                    </tr>
                                    <tr>
                                        <td>CLE RIB</td>
                                        <td><b><?= $info_biographique['CLE_RIB'] ?></b></td>
                                    </tr>
                                    <tbody>
                                </table>
                            </div>
                            <div class="col-sm-4">
                                <table class="table table-bordered table-striped table-hover table-sm">
                                    <thead class="bg-info">
                                    <tr class="text-white">
                                        <th colspan="2"><b> INFORMATIONS BIOGRAPHIQUES</b></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td width="120">NOM</td>
                                        <td><b class="text-danger"><?= $info_biographique['NOM'] ?></b></td>
                                    </tr>
                                    <tr>
                                        <td>PRENOM(S)</td>
                                        <td><b><?= $info_biographique['PRENOMS'] ?></b></td>
                                    </tr>
                                    <tr>
                                        <td>DATE DE NAISSANCE</td>
                                        <td><b><?= date('d-m-Y',strtotime($info_biographique['DATE_NAISSANCE'])) ?></b></td>
                                    </tr>
                                    <tr>
                                        <td>LIEU DE NAISSANCE</td>
                                        <td><b><?= $info_biographique['LIEU_NAISSANCE'] ?></b></td>
                                    </tr>
                                    <tr>
                                        <td>NATIONALITE</td>
                                        <td><b><?php
                                                    $lib_nationalite = $RECRUTEMENT->trouver_libelle_nationalite($info_biographique['NATIONALITE']);
                                                    echo $lib_nationalite['LIBELLE'] ?></b></td>
                                    </tr>
                                    <tr>
                                        <td>SEXE</td>
                                        <td><b><?php if($info_biographique['SEXE'] == 'M'){echo "HOMME"; }else{ echo "FEMME"; } ?></b></td>
                                    </tr>
                                    <tr>
                                        <td>NUMERO SECU</td>
                                        <td><b><?= $info_biographique['NUMERO_SECU'] ?></b></td>
                                    </tr>
                                    <tr>
                                        <td>ADRESSE EMAIL</td>
                                        <td><b><?= $info_biographique['ADRESSE_MAIL'] ?></b></td>
                                    </tr>
                                    <tr>
                                        <td>TYPE PIECE</td>
                                        <td><b><?php
                                                    $lib_type_piece = $RECRUTEMENT->trouver_libelle_type_piece($info_biographique['TYPE_DE_PIECE']);
                                                    echo $lib_type_piece['LIBELLE'] ?></b></td>
                                    </tr>
                                    <tr>
                                        <td>NUMERO DE LA PIECE</td>
                                        <td><b><?= $info_biographique['NUMERO_PIECE'] ?></b></td>
                                    </tr>
                                    <tr>
                                        <td>SITUATION MATRIMONIALE</td>
                                        <td><b><?php
                                                    $lib_situation_matrimoniale = $RECRUTEMENT->trouver_libelle_situation_matrimoniale($info_biographique['SITUATION_MATRIMONIALE'] );
                                                    echo $lib_situation_matrimoniale["LIBELLE"];
                                                ?>
                                            </b>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>NOMBRE D'ENFANTS</td>
                                        <td><b><?= $info_biographique['NOMBRE_ENFANTS'] ?></b></td>
                                    </tr>
                                    </tbody>
                                    <!-- <thead class="bg-info">
                                     <tr class="text-white">
                                         <th colspan="2"><b>INFO NAISSANCE</b></th>
                                     </tr>
                                     </thead>-->
                                    <tbody>


                                    </tbody>
                                </table>
                            </div>
                            <div class="col-sm-4">
                                <table class="table table-bordered table-striped table-hover table-sm">
                                    <thead class="bg-info">
                                        <tr class="text-white">
                                            <th colspan="2"><b> INFORMATIONS FAMILIALE</b></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <thead class="bg-light">
                                        <tr class="text-dark">
                                            <th colspan="2">PÈRE</th>
                                        </tr>
                                    </thead>
                                    <tr>
                                        <td width="120">NOM</td>
                                        <td><b class="text-danger"><?= $info_familiale['NOM_PERE'] ?></b></td>
                                    </tr>
                                    <tr>
                                        <td>PRENOM(S)</td>
                                        <td><b><?= $info_familiale['PRENOMS_PERE'] ?></b></td>
                                    </tr>
                                    <thead class="bg-light">
                                    <tr class="text-dark text-dark">
                                        <th colspan="2">MÈRE</th>
                                    </tr>
                                    </thead>
                                    <tr>
                                        <td width="120">NOM</td>
                                        <td><b class="text-danger"><?= $info_familiale['NOM_MERE'] ?></b></td>
                                    </tr>
                                    <tr>
                                        <td>PRENOM(S)</td>
                                        <td><b><?= $info_familiale['PRENOMS_MERE'] ?></b></td>
                                    </tr>
                                    </tbody>
                                    <?php if(!empty( $info_familiale['NOM_CONJOINT'])){ ?>
                                        <thead class="bg-info">
                                         <tr class="text-white">
                                             <th colspan="2"><b>INFORMATION CONJOINT</b></th>
                                         </tr>
                                         </thead>
                                        <tr>
                                            <td>NOM</td>
                                            <td><b><?= $info_familiale['NOM_CONJOINT'] ?></b></td>
                                        </tr>
                                        <tr>
                                            <td>PRENOMS</td>
                                            <td><b><?= $info_familiale['PRENOMS_CONJOINT'] ?></b></td>
                                        </tr>
                                    <?php if(!empty($info_familiale['PROFESSION_CONJOINT'])){?>
                                        <tr>
                                            <td>PROFESSION</td>
                                            <td><b><?php
                                                        $libelle_profesion = $RECRUTEMENT->trouver_libelle_profession($info_familiale['PROFESSION_CONJOINT']);
                                                        echo $profesion_conjoint = strtoupper($libelle_profesion["LIBELLE"]);
                                                   ?>
                                                </b>
                                            </td>
                                        </tr>
                                    <tbody>
                                    <?php } } ?>


                                    </tbody>
                                </table>
                                <?php
                                    if($info_biographique['NOMBRE_ENFANTS'] > 0){
                                    $enfants = $RECRUTEMENT->trouver_infos_enfants($_SESSION['id_compte']);
                                ?>
                                <table class="table table-bordered table-striped table-hover table-sm">
                                    <thead class="bg-info">
                                    <tr class="text-white">
                                        <th colspan="2"><b> ENFANT(S)</b></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach($enfants AS $enfant) { ?>
                                        <tr>
                                            <td width="120">NOM</td>
                                            <td><b class="text-danger"><?= $enfant['NOM'] ?></b></td>
                                        </tr>
                                        <tr>
                                            <td>PRENOM(S)</td>
                                            <td><b><?= $enfant['PRENOMS'] ?></b></td>
                                        </tr>
                                        <tr>
                                            <td>DATE DE NAISSANCE</td>
                                            <td><b><?= date('d-m-Y',strtotime($enfant['DATE_NAISSANCE'])) ?></b></td>
                                        </tr>
                                        <tr>
                                            <td>LIEU DE NAISSANCE</td>
                                            <td><b><?= $enfant['LIEU_NAISSANCE'] ?></b></td>
                                        </tr>
                                        <tr>
                                            <td>SEXE</td>
                                            <td><b><?php if($enfant['SEXE'] == 'M'){echo "HOMME"; }else{ echo "FEMME"; } ?></b></td>
                                        </tr>
                                        <tr >
                                            <td>NUMERO SECU</td>
                                            <td><b><?= $enfant['NUMERO_SECU'] ?></b></td>
                                        </tr>
                                        <tr class="bg-dark">
                                            <td colspan="2"><br></td>
                                        </tr>
                                    <?php } ?>
                                    </tbody>
                                    <!-- <thead class="bg-info">
                                     <tr class="text-white">
                                         <th colspan="2"><b>INFO NAISSANCE</b></th>
                                     </tr>
                                     </thead>-->
                                    <tbody>


                                    </tbody>
                                </table>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php } ?>

<script type="text/javascript">
 /*   $("#btn_deconnexion_recrutement").click(function(){
        $.ajax({
            url: '../_configs/Includes/Submits/Recrutement/submit_deconnexion_recrutement.php',
            type: 'POST',
            dataType: 'json',
            success: function (data) {
                if(data["status"] === "success"){
                    setTimeout(function(){
                        window.location.href='/ecmu/recrutement/recrutement.php';
                    }, 3000);
                }
            }
        })
        return false;
    })*/
</script>
