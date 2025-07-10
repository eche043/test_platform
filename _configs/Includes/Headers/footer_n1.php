
<script type="application/javascript" src="<?= NODE_MODULES.'jquery/dist/jquery.js';?>"></script>
<script type="application/javascript" src="<?= NODE_MODULES.'jqueryui/jquery-ui.js';?>"></script>
<script type="application/javascript" src="<?= NODE_MODULES.'jquery-form/dist/jquery.form.min.js';?>"></script>
<script type="application/javascript" src="<?= NODE_MODULES.'bootstrap/dist/js/bootstrap.js';?>"></script>
<script type="application/javascript" src="<?= NODE_MODULES.'bootstrap/dist/js/bootstrap.bundle.js';?>"></script>
<script type="application/javascript" src="<?= NODE_MODULES.'@fortawesome/fontawesome-free/js/all.js';?>"></script>
<script type="application/javascript" src="<?= NODE_MODULES.'@fortawesome/fontawesome-free/js/fontawesome.min.js';?>"></script>
<script type="application/javascript" src="<?= NODE_MODULES.'datatables.net/js/jquery.dataTables.js';?>"></script>
<script type="application/javascript" src="<?= NODE_MODULES.'datatables.net-bs4/js/dataTables.bootstrap4.js';?>"></script>
<script type="application/javascript" src="<?= JS.'functions.js?v=1.1.1';?>"></script>
<script type="application/javascript" src="<?= JS.'ecmu.js';?>"></script>
<?php
if(ACTIVE_URL == URL.'agent/') {
    echo "<script>afficher_page_agent_index();</script>";
}
if(isset($_GET['num']) && ACTIVE_URL == URL.'agent/facture-selection-type.php?num='.$_GET['num']) {
    echo "<script>afficher_page_agent_facture_selection_type(getUrlVars()['num']);</script>";
}
if(isset($_GET['type']) && isset($_GET['num']) && ACTIVE_URL == URL.'agent/facture-edition.php?type='.$_GET['type'].'&num='.$_GET['num']) {
    echo "<script>afficher_page_agent_facture_edition(getUrlVars()['type'],getUrlVars()['num']);</script>";
}
if(isset($_GET['num']) && ACTIVE_URL == URL.'agent/facture-annulation.php?num='.$_GET['num']) {
    echo "<script>afficher_page_agent_facture_annulation(getUrlVars()['num']);</script>";
}
if(isset($_GET['type']) && isset($_GET['num']) && ACTIVE_URL == URL.'agent/facture.php?type='.$_GET['type'].'&num='.$_GET['num']) {
    echo "<script>afficher_page_agent_facture(getUrlVars()['type'],getUrlVars()['num']);</script>";
}

if(ACTIVE_URL == URL.'agent/ententes-prealables.php') {
    echo "<script>afficher_page_agent_ententes_prealables();</script>";
}
if(ACTIVE_URL == URL.'agent/attestations-droits.php') {
    echo "<script>afficher_page_agent_attestations_droits();</script>";
}
if(isset($_GET['id']) && ACTIVE_URL == URL.'agent/attestation-droits.php?id='.$_GET['id']) {
    echo "<script>afficher_page_agent_attestation_droits(getUrlVars()['id']);</script>";
}
if(isset($_GET['id']) && ACTIVE_URL == URL.'centre-saisie/attestation-droits.php?id='.$_GET['id']) {
    echo "<script>afficher_page_centre_saisie_attestation_droits(getUrlVars()['id']);</script>";
}
if(isset($_GET['code-ets']) && ACTIVE_URL == URL.'centre-coordination/attestation-droits.php?code-ets='.$_GET['code-ets']) {
    echo "<script>afficher_page_centre_coordination_attestation_droits(getUrlVars()['code-ets']);</script>";
}
if(isset($_GET['code-ets']) && ACTIVE_URL == URL.'centre-coordination/professionnels-sante.php?code-ets='.$_GET['code-ets']) {
    echo "<script>afficher_page_centre_coordination_professionnes_sante(getUrlVars()['code-ets']);</script>";
}
if(isset($_GET['code-ets']) && ACTIVE_URL == URL.'centre-coordination/terminaux-biometriques.php?code-ets='.$_GET['code-ets']) {
    echo "<script>afficher_page_centre_coordination_terminaux_biometriques(getUrlVars()['code-ets']);</script>";
}
if(isset($_GET['code-ets']) && ACTIVE_URL == URL.'centre-coordination/terminaux-biometriques-historique.php?code-ets='.$_GET['code-ets']) {
    echo "<script>afficher_page_centre_coordination_terminaux_biometriques_historique(getUrlVars()['code-ets']);</script>";
}
if(isset($_GET['code-ets']) && ACTIVE_URL == URL.'centre-coordination/terminaux-biometriques-edition.php?code-ets='.$_GET['code-ets']) {
    echo "<script>afficher_page_centre_coordination_terminaux_biometriques_edition(getUrlVars()['code-ets']);</script>";
}
if(ACTIVE_URL == URL.'centre-coordination/terminaux-biometriques.php') {
    echo "<script>afficher_page_centre_coordination_terminaux_biometriques();</script>";
}
if(ACTIVE_URL == URL.'agent/cmr.php') {
    echo "<script>afficher_page_agent_cmr();</script>";
}
if(ACTIVE_URL == URL.'agent/bordereaux.php') {
    echo "<script>afficher_page_agent_bordereaux();</script>";
}
if(ACTIVE_URL == URL.'centre-coordination/bordereaux.php') {
    echo "<script>afficher_page_centre_coordination_bordereaux();</script>";
}
if(ACTIVE_URL == URL.'centre-coordination/assures.php') {
    echo "<script>afficher_page_centre_coordination_assures();</script>";
}
if(isset($_GET['code-ets'])  && ACTIVE_URL == URL.'centre-coordination/bordereaux.php?code-ets='.$_GET['code-ets']) {
    echo "<script>afficher_page_centre_coordination_bordereaux(getUrlVars()['code-ets']);</script>";
}
if(isset($_GET['code-ets'])  && ACTIVE_URL == URL.'centre-coordination/demandes.php?code-ets='.$_GET['code-ets']) {
    echo "<script>afficher_page_centre_coordination_demandes(getUrlVars()['code-ets']);</script>";
}
if(isset($_GET['num']) && isset($_GET['code-ets'])  && ACTIVE_URL == URL.'centre-coordination/bordereau.php?num='.$_GET['num'].'&code-ets='.$_GET['code-ets']) {
    echo "<script>afficher_page_centre_coordination_bordereau(getUrlVars()['num'],getUrlVars()['code-ets']);</script>";
}
if(isset($_GET['code-ogd']) && isset($_GET['num-secu'])  && ACTIVE_URL == URL.'centre-coordination/assure.php?code-ogd='.$_GET['code-ogd'].'&num-secu='.$_GET['num-secu']) {
    echo "<script>afficher_page_centre_coordination_assure(getUrlVars()['code-ogd'],getUrlVars()['num-secu']);</script>";
}
if(isset($_GET['num']) && ACTIVE_URL == URL.'agent/bordereau.php?num='.$_GET['num']) {
    echo "<script>afficher_page_agent_bordereau(getUrlVars()['num']);</script>";
}
if(ACTIVE_URL == URL.'agent/historique.php') {
    echo "<script>afficher_page_agent_historique();</script>";
}
if(ACTIVE_URL == URL.'agent/distribution_masques.php') {
    echo "<script>afficher_page_agent_distribution_masques();</script>";
}

if(ACTIVE_URL == URL.'professionnel-sante/') {
    echo "<script>afficher_page_professionnel_sante_index();</script>";
}

if(ACTIVE_URL == URL.'centre-sante/') {
    echo "<script>afficher_page_centre_sante_index();</script>";
}

if(ACTIVE_URL == URL.'centre-saisie/') {
    echo "<script>afficher_page_centre_saisie_index();</script>";
}

if(ACTIVE_URL == URL.'centre-coordination/') {
    echo "<script>afficher_page_centre_coordination_index();</script>";
}
if(ACTIVE_URL == URL.'centre-coordination/factures.php') {
    echo "<script>afficher_page_centre_coordination_factures();</script>";
}
if(isset($_GET['code'])  && ACTIVE_URL == URL.'centre-coordination/etablissement.php?code='.$_GET['code']) {
    echo "<script>afficher_page_centre_coordination_etablissement(getUrlVars()['code'],getUrlVars()['code']);</script>";
}
if(isset($_GET['num']) && isset($_GET['code-ets'])  && ACTIVE_URL == URL.'centre-coordination/facture-selection-type.php?num='.$_GET['num'].'&code-ets='.$_GET['code-ets']) {
    echo "<script>afficher_page_centre_coordination_facture_selection_type(getUrlVars()['num'],getUrlVars()['code-ets']);</script>";
}
if(isset($_GET['code-ets'])  && ACTIVE_URL == URL.'centre-coordination/factures.php?code-ets='.$_GET['code-ets']) {
    echo "<script>afficher_page_centre_coordination_factures(getUrlVars()['code-ets'],getUrlVars()['code-ets']);</script>";
}
if(isset($_GET['type']) && isset($_GET['num']) && isset($_GET['code-ets']) && ACTIVE_URL == URL.'centre-coordination/facture-edition.php?type='.$_GET['type'].'&num='.$_GET['num'].'&code-ets='.$_GET['code-ets']) {
    echo "<script>afficher_page_centre_coordination_facture_edition(getUrlVars()['type'],getUrlVars()['num'],getUrlVars()['code-ets']);</script>";
}
if(isset($_GET['type']) && isset($_GET['num']) && isset($_GET['code-ets']) && ACTIVE_URL == URL.'centre-coordination/facture.php?type='.$_GET['type'].'&num='.$_GET['num'].'&code-ets='.$_GET['code-ets']) {
    echo "<script>afficher_page_centre_coordination_facture(getUrlVars()['type'],getUrlVars()['num'],getUrlVars()['code-ets']);</script>";
}
if(isset($_GET['num']) && isset($_GET['code-ets']) && ACTIVE_URL == URL.'centre-coordination/facture-annulation.php?num='.$_GET['num'].'&code-ets='.$_GET['code-ets']) {
    echo "<script>afficher_page_centre_coordination_facture_annulation(getUrlVars()['num'],getUrlVars()['code-ets']);</script>";
}


if(isset($_GET['type']) && isset($_GET['num']) && ACTIVE_URL == URL.'centre-saisie/facture.php?type='.$_GET['type'].'&num='.$_GET['num']) {
    echo "<script>afficher_page_centre_saisie_facture(getUrlVars()['type'],getUrlVars()['num']);</script>";
}

if(isset($_GET['type']) && isset($_GET['num']) && ACTIVE_URL == URL.'centre-saisie/facture-edition.php?type='.$_GET['type'].'&num='.$_GET['num']) {
    echo "<script>afficher_page_centre_saisie_facture_edition(getUrlVars()['type'],getUrlVars()['num']);</script>";
}

if(isset($_GET['num']) && ACTIVE_URL == URL.'centre-saisie/facture-selection-type.php?num='.$_GET['num']) {
    echo "<script>afficher_page_centre_saisie_facture_selection_type(getUrlVars()['num']);</script>";
}

if(isset($_GET['num']) && ACTIVE_URL == URL.'centre-saisie/facture-annulation.php?num='.$_GET['num']) {
    echo "<script>afficher_page_centre_saisie_facture_annulation(getUrlVars()['num']);</script>";
}

if(ACTIVE_URL == URL.'centre-coordination/ententes-prealables.php') {
    echo "<script>afficher_page_centre_coordination_ententes_prealables();</script>";
}
if(isset($_GET['code-ets']) && ACTIVE_URL == URL.'centre-coordination/ententes-prealables.php?code-ets='.$_GET['code-ets']) {
    echo "<script>afficher_page_centre_coordination_ententes_prealables(getUrlVars()['code-ets']);</script>";
}
if(ACTIVE_URL == URL.'centre-coordination/attestations-droits.php') {
    echo "<script>afficher_page_centre_coordination_attestations_droits();</script>";
}
if(isset($_GET['code-ets']) && ACTIVE_URL == URL.'centre-coordination/attestations-droits.php?code-ets='.$_GET['code-ets']) {
    echo "<script>afficher_page_centre_coordination_attestations_droits(getUrlVars()['code-ets']);</script>";
}
if(isset($_GET['id']) && isset($_GET['code-ets']) && ACTIVE_URL == URL.'centre-coordination/attestation-droits.php?id='.$_GET['id'].'&code-ets='.$_GET['code-ets']) {
    echo "<script>afficher_page_centre_coordination_attestation_droits(getUrlVars()['id'],getUrlVars()['code-ets']);</script>";
}

if(ACTIVE_URL == URL.'assurance/') {
    echo "<script>afficher_page_assurance_index();</script>";
}

if(ACTIVE_URL == URL.'centre-saisie/ententes-prealables.php') {
    echo "<script>afficher_page_centre_saisie_ententes_prealables();</script>";
}

if(ACTIVE_URL == URL.'centre-coordination/cmr.php') {
    echo "<script>afficher_page_centre_coordination_cmr();</script>";
}


if(isset($_GET['code-ets']) && ACTIVE_URL == URL.'centre-coordination/cmr.php?code-ets='.$_GET['code-ets']) {
    echo "<script>afficher_page_centre_coordination_cmr(getUrlVars()['code-ets']);</script>";
}

if(ACTIVE_URL == URL.'centre-saisie/attestations-droits.php') {
    echo "<script>afficher_page_centre_saisie_attestations_droits();</script>";
}

if(ACTIVE_URL == URL.'centre-saisie/cmr.php') {
    echo "<script>afficher_page_centre_saisie_cmr();</script>";
}

if(ACTIVE_URL == URL.'assurance/') {
    echo "<script>afficher_page_assurance_index();</script>";
}

if(ACTIVE_URL == URL.'collectivite/') {
    echo "<script>afficher_page_collectivite_index();</script>";
}
if(ACTIVE_URL == URL.'collectivite/etats.php') {
    echo "<script>afficher_page_collectivite_etats();</script>";
}
if(isset($_GET['num-secu']) && ACTIVE_URL == URL.'collectivite/assure.php?num-secu='.$_GET['num-secu']) {
    echo "<script>afficher_page_collectivite_assure(getUrlVars()['num-secu']);</script>";
}
if(isset($_GET['code-collectivite']) && ACTIVE_URL == URL.'collectivite/populations.php?code-collectivite='.$_GET['code-collectivite']) {
    echo "<script>afficher_page_collectivite_populations(getUrlVars()['code-collectivite']);</script>";
}
if(isset($_GET['code-collectivite']) && ACTIVE_URL == URL.'collectivite/cotisations.php?code-collectivite='.$_GET['code-collectivite']) {
    echo "<script>afficher_page_collectivite_cotisations(getUrlVars()['code-collectivite']);</script>";
}
if(isset($_GET['annee']) && isset($_GET['code-collectivite'])  && ACTIVE_URL == URL.'collectivite/cotisations.php?code-collectivite='.$_GET['code-collectivite'].'&annee='.$_GET['annee']) {
    echo "<script>afficher_page_collectivite_cotisations(getUrlVars()['code-collectivite'],getUrlVars()['annee']);</script>";
}
if(isset($_GET['code-collectivite']) && ACTIVE_URL == URL.'collectivite/retrait_populations.php?code-collectivite='.$_GET['code-collectivite']) {
    echo "<script>afficher_page_collectivite_retrait_populations(getUrlVars()['code-collectivite']);</script>";
}

if(ACTIVE_URL == URL.'assure/') {
    echo "<script>afficher_page_assure_index();</script>";
}
if(ACTIVE_URL == URL.'assure/cotisations.php') {
    echo "<script>afficher_page_assure_cotisations();</script>";
}
if(ACTIVE_URL == URL.'assure/consommations.php') {
    echo "<script>afficher_page_assure_consommations();</script>";
}
if(ACTIVE_URL == URL.'assure/mes-infos.php') {
    echo "<script>afficher_page_assure_mes_infos();</script>";
}
if(ACTIVE_URL == URL.'assure/demandes.php') {
    echo "<script>afficher_page_assure_demandes();</script>";
}
if(isset($_GET['numero']) && isset($_GET['type']) && ACTIVE_URL == URL.'assure/facture.php?numero='.$_GET['numero'].'&type='.$_GET['type']) {
    echo "<script>afficher_page_assure_facture(getUrlVars()['type'],getUrlVars()['numero']);</script>";
}

if(isset($_GET['id']) && ACTIVE_URL == URL.'assure/attestation.php?id='.$_GET['id']) {
    echo "<script>afficher_page_assure_attestation(getUrlVars()['id']);</script>";
}


if(ACTIVE_URL == URL.'ogd-prestations/') {
    echo "<script>afficher_page_ogd_prestation_index();</script>";
}

if(ACTIVE_URL == URL.'ogd-prestations/ententes-prealables.php') {
    echo "<script>afficher_page_ogd_prestation_entente_prealable_index();</script>";
}

if(isset($_GET['numero']) && ACTIVE_URL == URL.'ogd-prestations/details-entente-prealable.php?numero='.$_GET['numero']) {
    echo "<script>afficher_page_ogd_prestation_details_entente_prealable(getUrlVars()['numero']);</script>";
}

if(ACTIVE_URL == URL.'ogd-prestations/verification.php') {
    echo "<script>afficher_page_ogd_prestation_verification();</script>";
}

if(ACTIVE_URL == URL.'ogd-prestations/liquidation.php') {
    echo "<script>afficher_page_ogd_prestation_liquidation();</script>";
}

if(isset($_GET['numero']) && isset($_GET['type']) && ACTIVE_URL == URL.'ogd-prestations/facture.php?numero='.$_GET['numero'].'&type='.$_GET['type']) {
    echo "<script>afficher_page_ogd_prestation_facture(getUrlVars()['numero'],getUrlVars()['type']);</script>";
}

if(ACTIVE_URL == URL.'partenaire/') {
    echo "<script>afficher_page_partenaire_index();</script>";
}

if(isset($_GET['id']) &&ACTIVE_URL == URL.'partenaire/demande-duplicata.php?id='.$_GET['id']) {
    echo "<script>afficher_page_partenaire_demande_duplicata(getUrlVars()['id']);</script>";
}

if(ACTIVE_URL == URL.'centre-coordination/historique.php') {
    echo "<script>afficher_page_centre_coordination_historique();</script>";
}
if(isset($_GET['code-ets']) && ACTIVE_URL == URL.'centre-coordination/historique.php?code-ets='.$_GET['code-ets']) {
    echo "<script>afficher_page_centre_coordination_historique(getUrlVars()['code-ets']);</script>";
}
if (ACTIVE_URL == URL.'centre-coordination/tickets.php' ||
    (isset($_GET['id']) && ACTIVE_URL == URL.'centre-coordination/ticket.php?id='.$_GET['id'])
)
{
    echo '<script type="application/javascript" src="'.JS.'page_centre_coordination_tickets.js"></script>';
    if (ACTIVE_URL == URL.'centre-coordination/tickets.php'){ echo '<script>afficher_tickets_index()</script>';}
    elseif (isset($_GET['id']) && ACTIVE_URL == URL.'centre-coordination/ticket.php?id='.$_GET['id']){
        echo "<script>afficher_ticket_details(getUrlVars()['id'])</script>";
    }
}
if(ACTIVE_URL == URL.'recrutement/') {
    echo "<script>afficher_page_recrutement();</script>";
}
if(isset($_GET['id-agac']) && ACTIVE_URL == URL.'recrutement/recrutement.php?id-agac='.$_GET['id-agac']) {
    echo "<script>afficher_page_recrutement_formulaire(getUrlVars()['id-agac']);</script>";
}
if(isset($_GET['id-agac']) && ACTIVE_URL == URL.'recrutement/resume_recrutement.php?id-agac='.$_GET['id-agac']) {
    echo "<script>afficher_page_resume_recrutement_formulaire(getUrlVars()['id-agac']);</script>";
}
?>


<footer>&copy; <a href="https://www.ipscnam.ci" target="_blank">CNAM</a> - 2017 - <?= date('Y',time());?></footer>
</body>
</html>