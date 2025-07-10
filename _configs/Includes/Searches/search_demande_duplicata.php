<?php
/**
 * Created by PhpStorm.
 * User: mohamed.kone
 * Date: 22/04/2021
 * Time: 08:23
 */

require_once '../../Classes/DUPLICATA.php';
require_once '../../Classes/ASSURES.php';
$DUPLICATA = new DUPLICATA();
$ASSURES = new ASSURES();

if(isset($_POST["numero_suivi"])) {$numero_suivi = $_POST["numero_suivi"];} else {$numero_suivi = null;}
if(isset($_POST["numero_secu"])) {$numero_secu = $_POST["numero_secu"];} else {$numero_secu = null;}
$demande = $DUPLICATA->trouver_carte($numero_suivi,$numero_secu);
if($demande){
?>
    <div class="row justify-content-sm-center">
        <div class="col-sm-12">

            <?php
            if($demande['STATUT_PRODUCTION'] == 1 && $demande['STATUT_ACHEMINEMENT'] == 0 && $demande['STATUT_RETRAIT'] == 0 ) {
                echo '<p class="text-success h5 text-center"><b>Votre carte a été produite et est en cours d\'acheminement.</b></p><hr>';
            }elseif($demande['STATUT_PRODUCTION'] == 1 && $demande['STATUT_ACHEMINEMENT'] == 1 && $demande['STATUT_RETRAIT'] == 0 ){
                echo '<p class="text-success h5 text-center"><b>Votre carte est en cours de délivrance.</b></p><hr>';
            } elseif($demande['STATUT_PRODUCTION'] == 1 && $demande['STATUT_ACHEMINEMENT'] == 1 && $demande['STATUT_RETRAIT'] == 1 ){
                echo '<p class="text-success h5 text-center"><b>Votre carte a été retirée.</b></p><hr>';
            }elseif($demande['STATUT_VALIDATION'] == 2 ) {
                echo '<p class="text-danger h5 text-center"><b>VOTRE DEMANDE DE DUPLICATA A ETE REFUSEE.</b></p><hr>';
            }
            else{
                echo '<p class="text-info h5 text-center"><b>Votre demande de duplicata a été transmise, nous vous contacterons par sms pour la suite du processus.</b></p><hr>';
            }
            ?>
        </div>

        <div class="col-sm-11 alert alert-secondary">
            <?php
            $assure = $ASSURES->trouver($demande['NUM_SECU']);
            if($assure) {
                ?>
                    <p class="h5">
                        N° de suivi : <strong id="strong_num_suivi"><?= $demande['ID_DEMANDE'];?></strong><br />
                        N° sécu : <strong><?= $demande['NUM_SECU'];?></strong><br />
                        Nom et prénom(s) : <strong><?= $demande['NOM'] . ' ' . $demande['PRENOMS']; ?></strong><br />
                        Date de naissance : <strong><?= date('d/m/Y', strtotime($demande['DATE_NAISSANCE'])); ?></strong><br />
                        N° téléphone : <strong><?= $demande['NUM_TELEPHONE'];?></strong>
                    </p>
                <?php
            }
            ?>
        </div>
    </div><hr />
    <div class="row justify-content-sm-center">
        <div class="col-sm-3 mr-1 border border-primary bg-light rounded">
            <div class="row" style="padding: 5px">
                <div class="col-sm-9 text-left">
                    <div class="row" style="margin-left: 10px">
                        <h6 class="text-primary"><b>  PRODUCTION</b></h6>
                    </div>
                    <br>
                    <div class="row" style="margin-left: 10px"><b>
                            <?php if(!empty(($demande['DATE_PRODUCTION']))){ echo date('d/m/Y',strtotime($demande["DATE_PRODUCTION"]));}?>
                        </b>
                    </div>
                    <br>
                </div>
                <div class="col-sm-3 text-success">
                    <br>
                    <?php if($demande["STATUT_PRODUCTION"] == 1){
                        echo '<i class="fa fa-check fa-4x" aria-hidden="true"></i>';
                    }else{
                        echo '<i class="fa fa-times fa-4x text-danger" aria-hidden="true"></i>';
                    } ?>


                </div>
            </div>
        </div>
        <div class="col-sm-3 mr-1 border border-primary bg-light rounded">
            <div class="row" style="padding: 5px">
                <div class="col-sm-9 text-left">
                    <div class="row" style="margin-left: 10px">
                        <h6 class="text-primary"><b>ACHEMINEMENT</b></h6>
                    </div>
                    <br>
                    <div class="row" style="margin-left: 10px">
                        <div class="row" style="margin-left: 0px"><b><?php if(!empty(($demande['DATE_ACHEMINEMENT']))){ echo date('d/m/Y',strtotime($demande["DATE_ACHEMINEMENT"]));}?></b></div>
                    </div>
                    <div class="row" style="margin-left: 10px"><b><?= $demande["LIEU_ACHEMINEMENT"] ?></b></div>
                    <div class="row" style="margin-left: 10px">NUMERO DE RANGEMENT : <b> <?= $demande["NUMERO_RANGEMENT"] ?></b></div>
                    <br>
                </div>
                <div class="col-sm-3 text-success">
                    <br>
                    <?php if($demande["STATUT_ACHEMINEMENT"] == 1){
                        echo '<i class="fa fa-check fa-4x" aria-hidden="true"></i>';
                    }else{
                        echo '<i class="fa fa-times fa-4x text-danger" aria-hidden="true"></i>';
                    } ?>
                </div>
            </div>
        </div>
        <div class="col-sm-3 mr-1 border border-primary bg-light rounded">
            <div class="row" style="padding: 5px">
                <div class="col-sm-9 text-left">
                    <div class="row" style="margin-left: 10px">
                        <h6 class="text-primary"><b>  DELIVRANCE </b></h6>
                    </div>
                    <br>
                    <div class="row" style="margin-left: 10px"><b><?php if(!empty(($demande['DATE_RETRAIT']))){ echo date('d/m/Y',strtotime($demande["DATE_RETRAIT"]));}?></b></div>
                    <div class="row" style="margin-left: 10px"><b><?= $demande["LIEU_RETRAIT"] ?></b></div>
                    <br>
                </div>
                <div class="col-sm-3 text-success">
                    <br>
                    <?php if($demande["STATUT_RETRAIT"] == 1){
                        echo '<i class="fa fa-check fa-4x" aria-hidden="true"></i>';
                    }else{
                        echo '<i class="fa fa-times fa-4x text-danger" aria-hidden="true"></i>';
                    } ?>
                </div>
            </div>
        </div>
    </div><hr />
    <p class="text-center">
        <a class="btn btn-primary btn-sm" href="<?= URL.'duplicata.php';?>"><i class="fa fa-arrow-circle-left"></i> Retourner</a>
        <?php
            if($demande['STATUT_VALIDATION'] != 2){
               echo '<button type="button" class="btn btn-secondary btn-sm" id="button_imprimer"><i class="fa fa-print"></i> Imprimer</button>';
            }
        ?>

    </p>
<?php
}else{
    echo '<p class="alert alert-danger text-center"><strong>Aucune demande n\'a été trouvée pour les données renseignées.</strong></p><br /><p class="text-center"><a class="btn btn-primary btn-sm" href="'.URL.'duplicata.php"><i class="fa fa-arrow-circle-left"></i> Réessayer</a></p>';
}
?>
<script>
    $("#button_imprimer").click(function () {
        let num_suivi = $("#strong_num_suivi").html();
        window.open("impression-duplicata.php?num="+num_suivi,"nom_popup","menubar=no, status=no, scrollbars=no, menubar=no, width=800, height=700");
    });
</script>
