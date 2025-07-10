<?php
if(ACTIVE_URL == URL) {
    define('TITLE','ecmu');
}
if(ACTIVE_URL == URL.'profil.php') {
    define('TITLE','Profil utilisateur');
}
if(ACTIVE_URL == URL.'infos-utiles.php') {
    define('TITLE','Infos utiles');
}
if(ACTIVE_URL == URL.'panier-soins.php') {
    define('TITLE','Panier de soins');
}
if(ACTIVE_URL == URL.'panier-actes.php') {
    define('TITLE','Actes Médicaux');
}
if(ACTIVE_URL == URL.'panier-medicaments.php') {
    define('TITLE','Médicaments');
}
if(ACTIVE_URL == URL.'panier-pathologies.php') {
    define('TITLE','Pathologies');
}
if(ACTIVE_URL == URL.'reseau-soins.php') {
    define('TITLE','Réseau de soins');
}
if(ACTIVE_URL == URL.'connexion.php') {
    define('TITLE','Connexion ecmu');
}
if(ACTIVE_URL == URL.'agent/') {
    define('TITLE','Agent d\'accueil');
}
if(isset($_GET['num']) && ACTIVE_URL == URL.'agent/facture-selection-type.php?num='.$_GET['num']) {
    define('TITLE','Facture n° '.$_GET['num'].': type');
}
if(isset($_GET['type']) && isset($_GET['num']) && ACTIVE_URL == URL.'agent/facture-edition.php?type='.$_GET['type'].'&num='.$_GET['num']) {
    define('TITLE','Edition facture n° '.$_GET['num']);
}
if(isset($_GET['type']) && isset($_GET['num']) && ACTIVE_URL == URL.'agent/facture.php?type='.$_GET['type'].'&num='.$_GET['num']) {
    define('TITLE','Facture n° '.$_GET['num']);
}
if(isset($_GET['num']) && (ACTIVE_URL == URL.'agent/facture-annulation.php?num='.$_GET['num'] || ACTIVE_URL == URL.'centre-saisie/facture-annulation.php?num='.$_GET['num'])) {
    define('TITLE','Annulation facture n°: '.$_GET['num']);
}
if(ACTIVE_URL == URL.'agent/ententes-prealables.php' || ACTIVE_URL == URL.'centre-saisie/ententes-prealables.php') {
    define('TITLE','Ententes préalables');
}
if(ACTIVE_URL == URL.'agent/attestations-droits.php' || ACTIVE_URL == URL.'centre-saisie/attestations-droits.php') {
    define('TITLE','Attestation de droits');
}
if(isset($_GET['id']) && (ACTIVE_URL == URL.'agent/attestation-droits.php?id='.$_GET['id'] || ACTIVE_URL == URL.'centre-saisie/attestation-droits.php?id='.$_GET['id'])) {
    define('TITLE','Attestation N°'.$_GET['id']);
}
if(ACTIVE_URL == URL.'agent/cmr.php'|| ACTIVE_URL == URL.'centre-saisie/cmr.php') {
    define('TITLE','CMR');
}
if(ACTIVE_URL == URL.'agent/bordereaux.php') {
    define('TITLE','Bordereaux');
}
if(isset($_GET['num']) && ACTIVE_URL == URL.'agent/bordereau.php?num='.$_GET['num']) {
    define('TITLE','Bordereau N°'.$_GET['num']);
}
if(ACTIVE_URL == URL.'agent/historique.php') {
    define('TITLE','Historique');
}
if(ACTIVE_URL == URL.'agent/distribution_masques.php') {
    define('TITLE','Distribution de Masques');
}
if(ACTIVE_URL == URL.'professionnel-sante/') {
    define('TITLE','Professionnel de santé');
}
if(ACTIVE_URL == URL.'centre-sante/') {
    define('TITLE','Centre de santé');
}
if(ACTIVE_URL == URL.'centre-saisie/') {
    define('TITLE','Centre de saisie');
}
if(ACTIVE_URL == URL.'assurance/') {
    define('TITLE','Assurance');
}
if(isset($_GET['type']) && isset($_GET['num']) && ACTIVE_URL == URL.'centre-saisie/facture.php?type='.$_GET['type'].'&num='.$_GET['num']) {
    define('TITLE','Facture n° '.$_GET['num']);
}
if(isset($_GET['type']) && isset($_GET['num']) && ACTIVE_URL == URL.'centre-saisie/facture-edition.php?type='.$_GET['type'].'&num='.$_GET['num']) {
    define('TITLE','Facture n° '.$_GET['num']);
}
if(isset($_GET['num']) && ACTIVE_URL == URL.'centre-saisie/facture-selection-type.php?num='.$_GET['num']) {
    define('TITLE','Facture n° '.$_GET['num'].': type');
}
if(ACTIVE_URL == URL.'collectivite/' || ACTIVE_URL == URL.'collectivite/etats.php') {
    define('TITLE','Collectivité');
}
if(isset($_GET['num-secu']) && ACTIVE_URL == URL.'collectivite/assure.php?num-secu='.$_GET['num-secu']) {
    require_once '../_configs/Classes/UTILISATEURS.php';
    require '../_configs/Classes/ASSURES.php';
    $ASSURES = new ASSURES();
    $assure = $ASSURES->trouver($_GET['num-secu']);
    define('TITLE',$assure['NOM'].' '.$assure['PRENOM']);
}
if(ACTIVE_URL == URL.'assure/'  ) {
    require_once '../_configs/Classes/UTILISATEURS.php';
    $UTILISATEURS = new UTILISATEURS();
    $user = $UTILISATEURS->trouver($_SESSION['ECMU_USER_ID'],NULL,NULL);
    define('TITLE',$user['PRENOM'].' '.$user['NOM']);
}
if(ACTIVE_URL == URL.'collectivite/populations.php') {
    define('TITLE','Gestion des Populations');
}
if(isset($_GET['code-collectivite']) && ACTIVE_URL == URL.'collectivite/populations.php?code-collectivite='.$_GET['code-collectivite']) {
    define('TITLE',$_GET['code-collectivite'].': Gestion des Populations');
}
if(isset($_GET['code-collectivite']) && ACTIVE_URL == URL.'collectivite/retrait_populations.php?code-collectivite='.$_GET['code-collectivite']) {
    define('TITLE',$_GET['code-collectivite'].': Retrait de Populations');
}
if(isset($_GET['code-collectivite']) && (ACTIVE_URL == URL.'collectivite/cotisations.php?code-collectivite='.$_GET['code-collectivite'] || (isset($_GET['annee']) && ACTIVE_URL == URL.'collectivite/cotisations.php?code-collectivite='.$_GET['code-collectivite'].'&annee='.$_GET['annee']))) {
    define('TITLE',$_GET['code-collectivite'].': Gestion des Cotisations');
}
if(ACTIVE_URL == URL.'assure/cotisations.php'){
    define('TITLE','Cotisations');
}
if(ACTIVE_URL == URL.'assure/consommations.php'){
    define('TITLE','Consommation');
}
if(ACTIVE_URL == URL.'assure/mes-infos.php'){
    define('TITLE','Mes Infos');
}
if(isset($_GET['numero']) && isset($_GET['type']) && ACTIVE_URL == URL.'assure/facture.php?numero='.$_GET['numero'].'&type='.$_GET['type']){
    define('TITLE','Factures: '.$_GET['numero']);
}
if(ACTIVE_URL == URL.'assure/demandes.php'){
    define('TITLE','Demandes');
}
if(isset($_GET['id']) && ACTIVE_URL == URL.'assure/attestation.php?id='.$_GET['id']){
    define('TITLE','Attestation N°'.$_GET['id']);
}
if(ACTIVE_URL == URL.'ogd-prestations/'
    || ACTIVE_URL == URL.'ogd-prestations/ententes-prealables.php'
    || ACTIVE_URL == URL.'ogd-prestations/verification.php'
	|| ACTIVE_URL == URL.'ogd-prestations/liquidation.php') {
    define('TITLE','OGD Prestations');
}

if(isset($_GET['numero']) && ACTIVE_URL == URL.'ogd-prestations/details-entente-prealable.php?numero='.$_GET['numero']){
    define('TITLE','ENTENTE PREALABLE N°'.$_GET['numero']);
}

if(isset($_GET['numero']) && isset($_GET['type']) && ACTIVE_URL == URL.'ogd-prestations/facture.php?numero='.$_GET['numero'].'&type='.$_GET['type']){
    define('TITLE','FACTURE N°'.$_GET['numero']);
}

if(ACTIVE_URL == URL.'duplicata.php') {
    define('TITLE','Demande Duplicata');
}

if(ACTIVE_URL == URL.'partenaire/') {
    define('TITLE','Partenaire');
}

if(isset($_GET['id']) && ACTIVE_URL == URL.'partenaire/demande-duplicata.php?id='.$_GET['id']) {
    define('TITLE','Duplicata N°:'.$_GET['id']);
}

if(ACTIVE_URL == URL.'centre-coordination/') {
    $UTILISATEURS = new UTILISATEURS();
    $centre = $UTILISATEURS->trouver_centre_coordination($_SESSION['ECMU_USER_ID']);
    define('TITLE',$centre['LIBELLE_CENTRE']);
}
if(isset($_GET['code']) && ACTIVE_URL == URL.'centre-coordination/etablissement.php?code='.$_GET['code']) {
    /*$UTILISATEURS = new UTILISATEURS();
    $centre = $UTILISATEURS->trouver_centre_coordination($_SESSION['ECMU_USER_ID']);*/
    define('TITLE','Etablissement lié au centre');
}
if(isset($_GET['code-ets']) && ACTIVE_URL == URL.'centre-coordination/factures.php?code-ets='.$_GET['code-ets']) {
    define('TITLE','Factures');
}
if(isset($_GET['code-ets']) && ACTIVE_URL == URL.'centre-coordination/bordereaux.php?code-ets='.$_GET['code-ets']) {
    define('TITLE','Bordereaux');
}
if(isset($_GET['code-ets']) && ACTIVE_URL == URL.'centre-coordination/demandes.php?code-ets='.$_GET['code-ets']) {
    define('TITLE','Demandes');
}
if(isset($_GET['code-ets']) && ACTIVE_URL == URL.'centre-coordination/ententes-prealables.php?code-ets='.$_GET['code-ets']) {
    define('TITLE','Entente Préalable');
}
if(isset($_GET['code-ogd']) && isset($_GET['num-secu']) && ACTIVE_URL == URL.'centre-coordination/assure.php?code-ogd='.$_GET['code-ogd'].'&num-secu='.$_GET['num-secu']) {
    define('TITLE','INFORMATIONS ASSURÉ');
}
if(ACTIVE_URL == URL.'centre-coordination/bordereaux.php') {
    define('TITLE','Bordereaux par Etablissement');
}
if(ACTIVE_URL == URL.'centre-coordination/assures.php') {
    define('TITLE','Assurés');
}
if(ACTIVE_URL == URL.'centre-coordination/ententes-prealables.php') {
    define('TITLE','Ententes Préalables');
}
if(isset($_GET['num']) && isset($_GET['code-ets']) && ACTIVE_URL == URL.'centre-coordination/facture-selection-type.php?num='.$_GET['num'].'&code-ets='.$_GET['code-ets']) {
    define('TITLE','Facture n° '.$_GET['num'].': type');
}
if(isset($_GET['code-ets']) && ACTIVE_URL == URL.'centre-coordination/cmr.php?code-ets='.$_GET['code-ets']) {
    define('TITLE','CMR');
}
if(isset($_GET['code-ets']) && ACTIVE_URL == URL.'centre-coordination/professionnels-sante.php?code-ets='.$_GET['code-ets']) {
    define('TITLE','Professionnels de santé');
}
if(isset($_GET['code-ets']) && ACTIVE_URL == URL.'centre-coordination/terminaux-biometriques.php?code-ets='.$_GET['code-ets']) {
    define('TITLE','Terminaux Biométriques');
}
if(ACTIVE_URL == URL.'centre-coordination/terminaux-biometriques.php') {
    define('TITLE','Terminaux Biométriques');
}

if(isset($_GET['id']) && isset($_GET['code-ets']) && ACTIVE_URL == URL.'centre-coordination/attestation-droits.php?id='.$_GET['id'].'&code-ets='.$_GET['code-ets']) {
    define('TITLE','Attestation de Droits n° '.$_GET['id']);
}

if(isset($_GET['code-ets']) && ACTIVE_URL == URL.'centre-coordination/attestations-droits.php?code-ets='.$_GET['code-ets']) {
    define('TITLE','Attestation de Droits ');
}

if(isset($_GET['code-ets']) && ACTIVE_URL == URL.'centre-coordination/terminaux-biometriques-edition.php?code-ets='.$_GET['code-ets']) {
    define('TITLE','Nouveau Terminal');
}
if(isset($_GET['code-ets']) && ACTIVE_URL == URL.'centre-coordination/terminaux-biometriques-historique.php?code-ets='.$_GET['code-ets']) {
    define('TITLE','Historique des Terminaux du centre :' .$_GET['code-ets']);
}

if(ACTIVE_URL == URL.'centre-coordination/attestations-droits.php') {
    define('TITLE','Attestation de Droits ');
}

if(isset($_GET['type']) && isset($_GET['num']) && isset($_GET['code-ets']) && ACTIVE_URL == URL.'centre-coordination/facture-edition.php?type='.$_GET['type'].'&num='.$_GET['num'].'&code-ets='.$_GET['code-ets']) {
    define('TITLE','Edition facture n° '.$_GET['num']);
}

if(isset($_GET['type']) && isset($_GET['num']) && isset($_GET['code-ets']) && ACTIVE_URL == URL.'centre-coordination/facture.php?type='.$_GET['type'].'&num='.$_GET['num'].'&code-ets='.$_GET['code-ets']) {
    define('TITLE','Facture n° '.$_GET['num']);
}
if(isset($_GET['num']) && isset($_GET['code-ets']) && (ACTIVE_URL == URL.'centre-coordination/facture-annulation.php?num='.$_GET['num'].'&code-ets='.$_GET['code-ets'])) {
    define('TITLE','Annulation facture n°: '.$_GET['num']);
}
if(isset($_GET['code-ets']) && ACTIVE_URL == URL.'centre-coordination/historique.php?code-ets='.$_GET['code-ets']) {
    define('TITLE','Historique');
}

if(ACTIVE_URL == URL.'centre-coordination/cmr.php') {
    define('TITLE','CMR');
}

if(ACTIVE_URL == URL.'centre-coordination/historique.php') {
    define('TITLE','Historique');
}
if(ACTIVE_URL==URL."centre-coordination/tickets.php"){
    define('TITLE','Liste tickets');
}
if (isset($_GET['id']) && ACTIVE_URL == URL.'centre-coordination/ticket.php?id='.$_GET['id'])
{
    define('TITLE','Détails Ticket : '.$_GET['id']);
}
if(ACTIVE_URL==URL."recrutement/"){
    define('TITLE','Recrutement des AGAC');
}
if (isset($_GET['id-agac']) && ACTIVE_URL == URL.'recrutement/recrutement.php?id-agac='.$_GET['id-agac'])
{
    define('TITLE','Recrutement AGAC : '.$_GET['id-agac']);
}
if (isset($_GET['id-agac']) && ACTIVE_URL == URL.'recrutement/resume_recrutement.php?id-agac='.$_GET['id-agac'])
{
    define('TITLE','Recrutement AGAC : '.$_GET['id-agac']);
}