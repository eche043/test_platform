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

$demande = $DUPLICATA->trouver_num_suivi_num_secucarte($_POST["numero_suivi"]);


if($demande){
    ?>
    <div class="row">
        <div class="col-sm-12" align="center">
            <div class="row">
                <div class="col-sm-12">
                    <?php if($demande['STATUT_PRODUCTION'] == 1 && $demande['STATUT_ACHEMINEMENT'] == 0 && $demande['STATUT_RETRAIT'] == 0 ) { ?>
                        <h5 class="text-success"><b>Votre carte est en cours de production.</b></h5>
                    <?php }elseif($demande['STATUT_PRODUCTION'] == 1 && $demande['STATUT_ACHEMINEMENT'] == 1 && $demande['STATUT_RETRAIT'] == 0 ){ ?>
                        <h5 class="text-success"><b>Votre carte est en cours d'acheminement.</b></h5>
                    <?php } elseif($demande['STATUT_PRODUCTION'] == 1 && $demande['STATUT_ACHEMINEMENT'] == 1 && $demande['STATUT_RETRAIT'] == 1 ){ ?>
                        <h5 class="text-success"><b>Votre carte est diponible.</b></h5>
                    <?php }else{ ?>
                        <h5 class="text-danger"><b>Votre demande de duplicata a été transmise, nous vous contacterons par sms pour la suite du processus.</b></h5>
                        <hr>
                    <?php } ?>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-sm-1" align="left"></div>
                <div class="col-sm-11" align="left">
                    <?php
                        $nom_prenom = $ASSURES->trouver($demande['NUM_SECU']);
                    ?>
                    <h5>Nom et prénom(s) : <?= $nom_prenom['NOM'] . ' ' . $nom_prenom['PRENOM']; ?> <i class="text-danger">(<?= $demande['NOM'].' '.$demande['PRENOMS']; ?>)</i></h5>
                    <h5>Date de naissance : <?= date('d/m/Y',strtotime($nom_prenom["DATE_NAISSANCE"])) ?></h5>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-sm-12">
                    <div class="row" align="center">
                        <div class="col-sm-1"></div>
                        <div class="col-sm-3 mr-1 border border-primary bg-light rounded">
                            <div class="row" style="padding: 5px">
                                <div class="col-sm-9 text-left">
                                    <div class="row" style="margin-left: 10px">
                                        <h6 class="text-primary"><b>  PRODUCTION</b></h6>
                                    </div>
                                    <br>
                                    <div class="row" style="margin-left: 10px"><b>
                                            <?php if(!empty(($demande['DATE_PRODUCTION']))){ echo date('d-m-Y',strtotime($demande["DATE_PRODUCTION"]));}?>
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
                                        <div class="row" style="margin-left: 0px"><b><?php if(!empty(($demande['DATE_ACHEMINEMENT']))){ echo date('d-m-Y',strtotime($demande["DATE_ACHEMINEMENT"]));}?></b></div>
                                    </div>
                                    <div class="row" style="margin-left: 10px"><b><?= $demande["LIEU_ACHEMINEMENT"] ?></b></div>
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
                                    <div class="row" style="margin-left: 10px"><b><?php if(!empty(($demande['DATE_RETRAIT']))){ echo date('d-m-Y',strtotime($demande["DATE_RETRAIT"]));}?></b></div>
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
                    </div>
                </div>
            </div>
        </div>
    </div>


<?php }else{ ?>
    <div class="row">
        <div class="col-sm-12 text-danger" align="center">
            <div class="row">
                <div class="col-sm-12">
                    <h4 class="text-danger"><b>Aucune demande  de duplicata en attente de validation pour cet assuré.</b></h4>
                </div>
            </div>
        </div>
    </div>
<?php } ?>