<?php
if(ACTIVE_URL != URL && ACTIVE_URL != URL.'connexion.php') {

if(isset($_SESSION['ECMU_USER_ID']) || !empty($_SESSION['ECMU_USER_ID'])) {
    $UTILISATEURS = new UTILISATEURS();
    $user = $UTILISATEURS->trouver($_SESSION['ECMU_USER_ID'], NULL, NULL);
    $user_profil = explode(';',$user['FSE']);
    ?>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <a class="navbar-brand" href="<?= URL; ?>">ecmu</a>
        <a class="navbar-toggler" href="#" data-toggle="collapse" data-target="#navbarSupportedContent"
           aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </a>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav mr-auto">
                <?php
                    if(
                        ACTIVE_URL == URL.'agent/' ||
                        ACTIVE_URL == URL.'agent/ententes-prealables.php' ||
                        ACTIVE_URL == URL.'agent/attestations-droits.php' ||
                        (isset($_GET['id']) && ACTIVE_URL == URL.'agent/attestation-droits.php?id='.$_GET['id']) ||
                        ACTIVE_URL == URL.'agent/cmr.php' ||
                        ACTIVE_URL == URL.'agent/bordereaux.php' ||
                        (isset($_GET['num']) && ACTIVE_URL == URL.'agent/bordereaux.php?num='.$_GET['num']) ||
                        ACTIVE_URL == URL.'agent/historique.php' ||
                        ACTIVE_URL == URL.'agent/distribution_masques.php' ||
                        (isset($_GET['num']) && ACTIVE_URL == URL.'agent/facture-selection-type.php?num='.$_GET['num']) ||
                        (isset($_GET['num']) && ACTIVE_URL == URL.'agent/facture-annulation.php?num='.$_GET['num']) ||
                        (isset($_GET['type']) && isset($_GET['num']) && ACTIVE_URL == URL.'agent/facture-edition.php?type='.$_GET['type'].'&num='.$_GET['num']) ||
                        (isset($_GET['type']) && isset($_GET['num']) && ACTIVE_URL == URL.'agent/facture.php?type='.$_GET['type'].'&num='.$_GET['num'])
                    ) {
                    ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= URL . 'agent/'; ?>">Factures</a>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Demandes
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="<?= URL . 'agent/ententes-prealables.php'; ?>">Entente
                                préalable</a>
                            <a class="dropdown-item" href="<?= URL . 'agent/attestations-droits.php'; ?>">Attestation de
                                droits</a>
                            <a class="dropdown-item" href="<?= URL . 'agent/cmr.php'; ?>">Centre médical référent</a>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= URL . 'agent/bordereaux.php'; ?>">Bordereaux</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= URL . 'agent/historique.php'; ?>">Historique</a>
                    </li>
                    <?php
                        if(in_array('MED',$user_profil)) {
                            ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= URL . 'agent/distribution_masques.php'; ?>">Distribution
                                    Masques</a>
                            </li>
                            <?php
                        }
                    }
                if(
                    ACTIVE_URL == URL.'centre-saisie/' ||
                    ACTIVE_URL == URL.'centre-saisie/ententes-prealables.php' ||
                    ACTIVE_URL == URL.'centre-saisie/attestations-droits.php' ||
                    ACTIVE_URL == URL.'centre-saisie/cmr.php' ||
                    (isset($_GET['type']) && isset($_GET['num']) && ACTIVE_URL == URL.'centre-saisie/facture-edition.php?type='.$_GET['type'].'&num='.$_GET['num'])||
                    (isset($_GET['type']) && isset($_GET['num']) && ACTIVE_URL == URL.'centre-saisie/facture.php?type='.$_GET['type'].'&num='.$_GET['num']) ||
                    (isset($_GET['num']) && ACTIVE_URL == URL.'centre-saisie/facture-annulation.php?num='.$_GET['num'])
                ){
                ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= URL . 'centre-saisie/'; ?>">Factures</a>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Demandes
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="<?= URL . 'centre-saisie/ententes-prealables.php'; ?>">Entente
                                préalable</a>
                            <a class="dropdown-item" href="<?= URL . 'centre-saisie/attestations-droits.php'; ?>">Attestation de
                                droits</a>
                            <a class="dropdown-item" href="<?= URL . 'centre-saisie/cmr.php'; ?>">Centre médical référent</a>
                        </div>
                    </li>
                <?php
                }if(
                    ACTIVE_URL == URL.'assure/' ||
                    ACTIVE_URL == URL.'assure/cotisations.php' ||
                    ACTIVE_URL == URL.'assure/consommations.php' ||
                    ACTIVE_URL == URL.'assure/demandes.php' ||
                    ACTIVE_URL == URL.'assure/mes-infos.php' ||
                    (isset($_GET['numero']) && isset($_GET['type']) && ACTIVE_URL == URL.'assure/facture.php?numero='.$_GET['numero'].'&type='.$_GET['type']) ||
                    (isset($_GET['id']) && ACTIVE_URL == URL.'assure/attestation.php?id='.$_GET['id'])
                ) {
                    ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= URL . 'assure/'; ?>">Accueil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= URL . 'assure/cotisations.php'; ?>">Cotisations</a>
                    </li>
<!--                    <li class="nav-item">-->
<!--                        <a class="nav-link" href="--><?//= URL . 'assure/consommations.php'; ?><!--">Consommations</a>-->
<!--                    </li>-->
<!--                    <li class="nav-item">-->
<!--                        <a class="nav-link" href="--><?//= URL . 'assure/mes-infos.php'; ?><!--">Mes Infos</a>-->
<!--                    </li>-->
<!--                    <li class="nav-item">-->
<!--                        <a class="nav-link" href="--><?//= URL . 'assure/demandes.php';?><!--">-->
<!--                            Demandes-->
<!--                        </a>-->
<!--                    </li>-->
                    <?php
                 }

                if(
                    ACTIVE_URL == URL.'ogd-prestations/'
                    || ACTIVE_URL == URL.'ogd-prestations/ententes-prealables.php'
                    || ACTIVE_URL == URL.'ogd-prestations/verification.php'
                    || ACTIVE_URL == URL.'ogd-prestations/liquidation.php'
                    || (isset($_GET['numero']) && ACTIVE_URL == URL.'ogd-prestations/details-entente-prealable.php?numero='.$_GET['numero'])
                    || (isset($_GET['numero']) && isset($_GET['type']) && ACTIVE_URL == URL.'ogd-prestations/facture.php?numero='.$_GET['numero'].'&type='.$_GET['type'])
                ) {
                    ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= URL . 'ogd-prestations/'; ?>">Accueil</a>
                    </li>
                    <?php
                    if(in_array('EP',$user_profil)) {
                        ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= URL . 'ogd-prestations/ententes-prealables.php'; ?>">Ententes
                                Préalables</a>
                        </li>
                        <?php
                    }
                    if(in_array('RPT',$user_profil)) {
                        ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= URL . 'ogd-prestations/verification.php'; ?>">Vérification</a>
                        </li>
                        <?php
                    }
                    if(in_array('LIQ',$user_profil)) {
                    ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= URL . 'ogd-prestations/liquidation.php'; ?>">Liquidation</a>
                        </li>

                    <?php
                    }
                }
                if(ACTIVE_URL == URL.'collectivite/etats.php'
                    || ACTIVE_URL == URL.'collectivite/populations.php'
                    || isset($_GET['code-collectivite']) && ACTIVE_URL == URL.'collectivite/populations.php?code-collectivite='.$_GET['code-collectivite']
                    || isset($_GET['code-collectivite']) && ACTIVE_URL == URL.'collectivite/cotisations.php?code-collectivite='.$_GET['code-collectivite']
                    || (isset($_GET['code-collectivite']) && isset($_GET['annee']) && ACTIVE_URL == URL.'collectivite/cotisations.php?code-collectivite='.$_GET['code-collectivite'].'&annee='.$_GET['annee'])
                    || isset($_GET['code-collectivite']) && ACTIVE_URL == URL.'collectivite/retrait_populations.php?code-collectivite='.$_GET['code-collectivite']
                ) {
                    ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= URL . 'collectivite/'; ?>">Accueil</a>
                    </li>
                    <li class="nav-item">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= URL . 'collectivite/populations.php?code-collectivite='.$_GET['code-collectivite']; ?>">Gestion des Populations</a>
                    </li>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= URL . 'collectivite/cotisations.php?code-collectivite='.$_GET['code-collectivite']; ?>">Gestion des Cotisations</a>
                    </li>
                <?php    }
                        if(ACTIVE_URL == URL.'centre-coordination/'
                            || ACTIVE_URL == URL.'centre-coordination/ententes-prealables.php'
							|| ACTIVE_URL == URL.'centre-coordination/historique.php'
							|| ACTIVE_URL == URL.'centre-coordination/bordereaux.php'
							|| ACTIVE_URL == URL.'centre-coordination/attestations-droits.php'
							|| ACTIVE_URL == URL.'centre-coordination/cmr.php'
							|| ACTIVE_URL == URL.'centre-coordination/terminaux-biometriques.php'
							|| ACTIVE_URL == URL.'centre-coordination/assures.php'
							|| ACTIVE_URL == URL.'centre-coordination/tickets.php'
							|| isset($_GET['code']) && ACTIVE_URL == URL.'centre-coordination/etablissement.php?code='.$_GET['code']
							|| isset($_GET['code-ets']) && ACTIVE_URL == URL.'centre-coordination/demandes.php?code-ets='.$_GET['code-ets']
							|| isset($_GET['code-ets']) && ACTIVE_URL == URL.'centre-coordination/terminaux-biometriques-edition.php?code-ets='.$_GET['code-ets']
							|| isset($_GET['code-ets']) && ACTIVE_URL == URL.'centre-coordination/ententes-prealables.php?code-ets='.$_GET['code-ets']
							|| isset($_GET['id']) && isset($_GET['code-ets']) && ACTIVE_URL == URL.'centre-coordination/attestation-droits.php?id='.$_GET['id'].'&code-ets='.$_GET['code-ets']
							|| isset($_GET['code-ets']) && ACTIVE_URL == URL.'centre-coordination/attestations-droits.php?code-ets='.$_GET['code-ets']
							|| isset($_GET['code-ets']) && ACTIVE_URL == URL.'centre-coordination/professionnels-sante.php?code-ets='.$_GET['code-ets']
							|| isset($_GET['code-ets']) && ACTIVE_URL == URL.'centre-coordination/terminaux-biometriques.php?code-ets='.$_GET['code-ets']
							|| isset($_GET['code-ets']) && ACTIVE_URL == URL.'centre-coordination/terminaux-biometriques-historique.php?code-ets='.$_GET['code-ets']
							|| isset($_GET['code-ets']) && ACTIVE_URL == URL.'centre-coordination/factures.php?code-ets='.$_GET['code-ets']
							|| isset($_GET['code-ets']) && ACTIVE_URL == URL.'centre-coordination/cmr.php?code-ets='.$_GET['code-ets']
							|| isset($_GET['type']) && isset($_GET['num']) && isset($_GET['code-ets']) && ACTIVE_URL == URL.'centre-coordination/facture.php?type='.$_GET['type'].'&num='.$_GET['num'].'&code-ets='.$_GET['code-ets']
							|| isset($_GET['code-ets']) && ACTIVE_URL == URL.'centre-coordination/historique.php?code-ets='.$_GET['code-ets']
							|| isset($_GET['num']) && isset($_GET['code-ets']) && ACTIVE_URL == URL.'centre-coordination/facture-selection-type.php?num='.$_GET['num'].'&code-ets='.$_GET['code-ets']
							|| isset($_GET['type']) && isset($_GET['num']) && isset($_GET['code-ets']) && ACTIVE_URL == URL.'centre-coordination/facture-edition.php?type='.$_GET['type'].'&num='.$_GET['num'].'&code-ets='.$_GET['code-ets']
							|| isset($_GET['num']) && isset($_GET['code-ets']) && ACTIVE_URL == URL.'centre-coordination/bordereau.php?num='.$_GET['num'].'&code-ets='.$_GET['code-ets']
							|| isset($_GET['code-ogd']) && isset($_GET['num-secu']) && ACTIVE_URL == URL.'centre-coordination/assure.php?code-ogd='.$_GET['code-ogd'].'&num-secu='.$_GET['num-secu']
							|| isset($_GET['code-ets']) && ACTIVE_URL == URL.'centre-coordination/bordereaux.php?code-ets='.$_GET['code-ets']
							|| isset($_GET['id']) && ACTIVE_URL == URL.'centre-coordination/ticket.php?id='.$_GET['id']
                        ) { 
							$centres_coordinations = $UTILISATEURS->trouver_centre_coordination($_SESSION['ECMU_USER_ID']);
							if($centres_coordinations){
						?>

                            <li class="nav-item">
                                <a class="nav-link" href="<?= URL.'centre-coordination/'?>">Etablissements</a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Demandes
                                </a>
                                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="<?= URL . 'centre-coordination/ententes-prealables.php'; ?>">Entente Préalable</a>
                                    <a class="dropdown-item" href="<?= URL . 'centre-coordination/attestations-droits.php'; ?>">Attestation de droits</a>
                                    <a class="dropdown-item" href="<?= URL . 'centre-coordination/cmr.php'; ?>">Centre médical réferent</a>
                                </div>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= URL.'centre-coordination/bordereaux.php'?>">Bordereaux</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= URL.'centre-coordination/historique.php'?>">Historique</a>
                            </li>
							<li class="nav-item">
								<a class="nav-link" href="<?= URL.'centre-coordination/assures.php'?>">Assurés</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="<?= URL.'centre-coordination/tickets.php'?>">Tickets</a>
							</li>

                       <?php 
							}
					   }
        ?>

                </ul>
                <ul class="navbar-nav justify-content-end">
                    <li class="nav-item">
                        <a href="<?= URL.'infos-utiles.php';?>" target="_blank" class="nav-link"><i class="fa fa-info-circle"></i> Info utiles</a>
                    </li>
                </ul>
                <form class="form-inline my-2 my-lg-0">
                    <button class="btn btn-sm btn-danger my-2 my-sm-0" id="deconnexion_link_n1" type="button"><i
                            class="fa fa-power-off"></i></button>
                </form>
            </div>

    </nav>
    <div class="col" id="div_profil_link">
        <a href="<?= URL.'profil.php';?>"><i class="fa fa-user"></i> <b><?= $user['NOM'].' '.$user['PRENOM'] ?></b></a>
    </div>
    <?php
}
}
?>
