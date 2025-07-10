<?php
/**
 * Created by PhpStorm.
 * User: mohamed.kone
 * Date: 22/04/2021
 * Time: 08:23
 */

require_once '../../Classes/DUPLICATA.php';
$DUPLICATA = new DUPLICATA();
$demande = $DUPLICATA->trouver_num_suivi_carte($_POST["numero_suivi"]);

if($demande){
?>
<table class="table table-bordered table-hover table-sm">
    <thead class="bg-dark text-center text-light">
        <tr>
            <td colspan="8"><b>INFORMATIONS PERSONNELLES</b></td>
        </tr>
    </thead>
    <thead class="bg-primary">
    <tr>
        <th>NUM SECU</th>
        <th>NOM</th>
        <th>PRENOMS</th>
        <th>DATE NAISSANCE</th>
        <th>TELEPHONE</th>
        <th>TYPE PIECE</th>
        <th>NUMERO PIECE</th>
        <th>DATE FIN VALIDITE</th>
    </tr>
    </thead>
    <tbody class="bg-light">
    <tr>
        <td><?= $demande["NUM_SECU"];?></td>
        <td><b><?= $demande['NOM'];?></b></td>
        <td><?= $demande['PRENOMS'];?></td>
        <td><?= date("d/m/Y",strtotime($demande['DATE_NAISSANCE']));?></td>
        <td><?= $demande['NUM_TELEPHONE'];?></td>
        <td><?= $demande['TYPE_PIECE'];?></td>
        <td><?= $demande['NUMERO_PIECE'];?></td>
        <td><?= date("d/m/Y",strtotime($demande['DATE_FIN_VALIDITE_PIECE']));?></td>
    </tr>
    </tbody>
</table>

<table class="table table-bordered table-hover table-sm">
        <thead class="bg-dark text-center text-light">
        <tr>
            <td colspan="7"><b>INFORMATIONS RELATIVES A LA DEMANDE</b></td>
        </tr>
        </thead>
        <thead>
        <tr class="bg-primary">
            <th>MOTIF DEMANDE</th>
            <th>DATE TRANSMISSION</th>
            <th>STATUT PRODUCTION</th>
            <th>DATE PRODUCTION</th>
            <th>STATUT RETRAIT</th>
            <th>DATE RETRAIT</th>
            <th>LIEU RETRAIT</th>
        </tr>
        </thead>
        <tbody>
        <tr class="bg-light">
            <td><?= $demande["MOTIF_DEMANDE"];?></td>
            <td><?= date("d/m/Y",strtotime($demande['DATE_TRANSMISSION']));?></td>
            <td><?= $demande['STATUT_PRODUCTION'];?></td>
            <td><b><?= date("d/m/Y",strtotime($demande['DATE_PRODUCTION']));?></b></td>
            <td><?= $demande['STATUT_RETRAIT'];?></td>
            <td><b><?= date("d/m/Y",strtotime($demande['DATE_RETRAIT']));?></b></td>
            <td><b><?= $demande['LIEU_RETRAIT'];?></b></td>
        </tr>
        </tbody>
    </table>

<?php }else{ ?>
    <div class="row">
        <div class="col-sm-12 text-danger" align="center">
            <b>Ce numéro de suivi de correspond à aucune demande  de duplicata en attente de validation.</b>
        </div>
    </div>
<?php } ?>