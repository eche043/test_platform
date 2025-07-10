<?php
header('Content-type: text/html; charset=UTF-8');

if(isset($_GET['num']) && isset($_GET['code_ets'])) {

    require_once '../_configs/Functions/chiffresEnLettres.php';
    require_once '../_configs/Classes/UTILISATEURS.php';
    require_once '../_configs/Classes/FACTURES.php';
    require_once '../_configs/Classes/BORDEREAUX.php';
    require_once '../_configs/Classes/CICMU.php';
    require_once '../_configs/Classes/ETABLISSEMENTSSANTE.php';
    require_once('../vendor/tecnickcom/tcpdf/config/tcpdf_config.php');
    require_once('../vendor/tecnickcom/tcpdf/tcpdf.php');


    $ETABLISSEMENTSANTE = new ETABLISSEMENTSSANTE();
    $FACTURES = new FACTURES();
    $BORDEREAUX = new BORDEREAUX();
    $CICMU = new CICMU();
    //$bordereau = $FACTURES->trouver_ets_bordereau($_GET['code_ets'],$_GET['num']);
    $bordereau = $BORDEREAUX->trouver($_GET['code_ets'],$_GET['num']);
    $ets = $ETABLISSEMENTSANTE->trouver_ets_valide($_GET['code_ets']);
    $ogd = $FACTURES->trouver_ogd($bordereau['NUM_OGD_BORDEREAU']);
    $type_facture = $FACTURES->trouver_type_facture($bordereau['TYPE_FEUILLE']);
    if(!empty($bordereau['DATE_REG'])) {
        if(!empty($bordereau['CODE_AC'])){
            $factures = $CICMU->lister_bordereaux_facture($bordereau['CODE_ETS_BORDEREAU'], $bordereau['NUMERO_BORDEREAU']);
        }
        else {
            $factures = $BORDEREAUX->lister_bordereaux_facture($_GET['code_ets'], $bordereau['NUMERO_BORDEREAU']);
        }
        $nbre_actes = 0;
        $part_cmu = 0;
        $part_ac = 0;
        foreach ($factures as $fact){
            if(!empty($bordereau['CODE_AC'])){
                $part_cmu = $part_cmu+(float)$fact['PART_CMU'];
                $part_ac = $part_ac+(float)$fact['PART_AC'];
                $nbre_actes = $nbre_actes + $fact['NOMBRE_ACTES'];
            }
            else {
                $trouver_facture = $FACTURES->trouver($fact['FEUILLE']);
                $nbre_actes = $nbre_actes + $fact['NOMBRE_ACTES'];
                if ($trouver_facture['CODE_OGD_AFFILIATION'] === '03016000') {
                    $part_ac = $fact['MONTANT'] - $fact['PART_CMU'];
                } else {
                    $part_ac = 0;
                }
                $part_cmu = $part_cmu + ((float)$fact['PART_CMU'] + $part_ac);
            }
        }

        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('IPSCNAM');
        $pdf->SetTitle('Bordereau n° '.$bordereau['NUMERO_BORDEREAU']);
        $pdf->SetSubject('Bordereau de transmission');
        $pdf->SetKeywords('CMU, Bordereau, transmission, Prestations, Santé, OGD');

        // set default header data
        //$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 005', PDF_HEADER_STRING);




        // set some language-dependent strings (optional)
        if (@file_exists(dirname(__FILE__).'/lang/fra.php')) {
            require_once(dirname(__FILE__).'/lang/fra.php');
            $pdf->setLanguageArray($l);
        }

        // Add a page
        // This method has several options, check the source code documentation for more information.
        $pdf->AddPage();

        $pdf->SetFont('helvetica', '', 15);
        $pdf->Cell(0, 0, 'BORDEREAU DE TRANSMISSION', 1, 1, 'C', 0, '', 1);
        $pdf->Write(0, '', '', 0, 'R', true, 0, false, false, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(0, 0, $ets['RAISON_SOCIALE'], 0, 1, 'C', 0, '', 1);
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(0, 0, 'N° BORDEREAU: '.$bordereau['NUMERO_BORDEREAU'], 0, 1, 'C', 0, '', 1);

        $pdf->SetFont('helvetica', '', 10);
        $pdf->Write(0, '', '', 0, 'R', true, 0, false, false, 0);
        $pdf->MultiCell(30, 0, 'Date Facture:', 0, 'L', 0, 0, '', '', true);
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->MultiCell(45, 0, date('d-m-Y',strtotime($bordereau['DATE_REG'])), 0, 'L', 0, 0, '', '', true);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(30, 0, 'Période:', 0, 'L', 0, 0, '', '', true);
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->MultiCell(80, 0, 'du '.date('d-m-Y',strtotime($bordereau['DATE_DEBUT_PERIODE'])).' au '.date('d-m-Y',strtotime($bordereau['DATE_FIN_PERIODE'])), 0, 'L', 0, 0, '', '', true);

        $pdf->SetFont('helvetica', '', 10);
        $pdf->Write(0, '', '', 0, 'R', true, 0, false, false, 0);
        $pdf->MultiCell(30, 0, 'N° de la Facture:', 0, 'L', 0, 0, '', '', true);
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->MultiCell(45, 0, $bordereau['NUMERO_BORDEREAU'], 0, 'L', 0, 0, '', '', true);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(30, 0, 'OGD:', 0, 'L', 0, 0, '', '', true);
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->MultiCell(80, 0, $ogd['LIBELLE'], 0, 'L', 0, 0, '', '', true);

        $pdf->SetFont('helvetica', '', 10);
        $pdf->Write(0, '', '', 0, 'R', true, 0, false, false, 0);
        $pdf->MultiCell(30, 0, 'Nbr. de factures:', 0, 'L', 0, 0, '', '', true);
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->MultiCell(45, 0, count($factures), 0, 'L', 0, 0, '', '', true);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(30, 0, 'Type de factures:', 0, 'L', 0, 0, '', '', true);
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->MultiCell(80, 0, $type_facture['LIBELLE'], 0, 'L', 0, 0, '', '', true);

        $pdf->SetFont('helvetica', '', 10);
        $pdf->Write(0, '', '', 0, 'R', true, 0, false, false, 0);
        $pdf->MultiCell(30, 0, 'Nbr. actes:', 0, 'L', 0, 0, '', '', true);
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->MultiCell(45, 0, $nbre_actes, 0, 'L', 0, 0, '', '', true);
        if(!empty($bordereau['CODE_AC'])){
            $pdf->SetFont('helvetica', '', 10);
            $pdf->MultiCell(30, 0, 'Assurance Com.:', 0, 'L', 0, 0, '', '', true);
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->MultiCell(80, 0, $bordereau['CODE_AC'], 0, 'L', 0, 0, '', '', true);
        }


        $pdf->Write(0, '', '', 0, 'R', true, 0, false, false, 0);
        $pdf->SetFillColor(179, 179, 179);
        $pdf->Write(0, '', '', 0, 'R', true, 0, false, false, 0);
        $pdf->SetFont('helvetica', 'B', 6.5);
        $pdf->MultiCell(10, 0, 'N°', 1, 'C', 1, 0, '', '', true);
        $pdf->MultiCell(25, 0, 'N° SECU', 1, 'C', 1, 0, '', '', true);
        $pdf->MultiCell(20, 0, 'N° FACTURE', 1, 'C', 1, 0, '', '', true);
        $pdf->MultiCell(15, 0, 'N° FS. INIT.', 1, 'C', 1, 0, '', '', true);
        $pdf->MultiCell(20, 0, 'DATE SOINS', 1, 'C', 1, 0, '', '', true);
        $pdf->MultiCell(20, 0, 'C. PRESTAT.', 1, 'C', 1, 0, '', '', true);
        $pdf->MultiCell(18, 0, 'AFFECTION', 1, 'C', 1, 0, '', '', true);
        $pdf->MultiCell(15, 0, 'MONTANT', 1, 'C', 1, 0, '', '', true);
        $pdf->MultiCell(17, 0, 'P. ASSURE', 1, 'C', 1, 0, '', '', true);
        $pdf->MultiCell(15, 0, 'PART AC', 1, 'C', 1, 0, '', '', true);
        $pdf->MultiCell(15, 0, 'PART CMU', 1, 'C', 1, 1, '', '', true);
        $pdf->SetFont('helvetica', '', 6.5);
        $ligne = 1;
        $total_montant = 0;
        $total_part_assure = 0;
        $total_part_cmu = 0;
        $total_part_ac = 0;
        //$factures = $FACTURES->trouver_liste_facture_par_bordereau($bordereau['NUM_BORDEREAU']);
        foreach ($factures as $facture) {
            if(!empty($bordereau['CODE_AC'])) {
                $part_cmu_facture = $facture['PART_CMU'];
                $part_ass_compl = $facture['PART_AC'];
                $part_assure = $facture['MONTANT'] - ((float)$facture['PART_CMU']+(float)$facture['PART_AC']);
                $total_part_ac = $total_part_ac+(float)$facture['PART_AC'];
                $total_part_cmu = $total_part_cmu+(float)$facture['PART_CMU'];
                $total_part_assure = $total_part_assure+(float)$part_assure;
                $code_affection = $facture['AFFECTION1'];
            }
            else {
                if ($fact['CODE_OGD_AFFILIATION'] === '03016000') {
                    $part_ass_compl = $facture['MONTANT'] - $facture['PART_CMU'];
                    $part_assure = 0;
                } else {
                    $part_ass_compl = 0;
                    $part_assure = $facture['MONTANT'] - round($facture['PART_CMU']);
                }
                $part_cmu_facture =round($part_cmu + $part_ass_compl);
                $total_part_cmu = $total_part_cmu + (round($facture['PART_CMU']) + $part_ass_compl);
                $total_part_assure = $total_part_assure + $part_assure;
                //$total_part_assure = $total_part_assure + ($facture['MONTANT'] - round($facture['PART_CMU']));
                $total_part_ac = 0;
                if($facture['TYPE_FACTURE'] == 'AMB') {
                    $code_affection = $facture['AFFECTION1'];
                }else {
                    $facture_initiale = $FACTURES->trouver_facture(null,$facture['NUM_FS_INITIALE']);
                    $code_affection = $facture_initiale['AFFECTION1'];
                }
            }
            $total_montant = $total_montant + $facture['MONTANT'];

            $pdf->MultiCell(10, 0, $ligne, 1, 'R', 1, 0, '', '', true);
            $pdf->MultiCell(25, 0, $facture['NUM_SECU'], 1, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(20, 0, $facture['FEUILLE'], 1, 'R', 0, 0, '', '', true);
            $pdf->MultiCell(15, 0, $facture['NUM_FS_INITIALE'], 1, 'R', 0, 0, '', '', true);
            $pdf->MultiCell(20, 0, date('d-m-Y',strtotime($facture['DATE_SOINS'])), 1, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(20, 0, $facture['CODE_PS'], 1, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(18, 0, $code_affection, 1, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(15, 0, number_format($facture['MONTANT'],'0','',' '), 1, 'R', 0, 0, '', '', true);
            $pdf->MultiCell(17, 0, number_format($part_assure,'0','',' '), 1, 'R', 0, 0, '', '', true);
            $pdf->MultiCell(15, 0, number_format($part_ass_compl,'0','',' '), 1, 'R', 0, 0, '', '', true);
            $pdf->MultiCell(15, 0, number_format($part_cmu_facture,'0','',' '), 1, 'R', 0, 1, '', '', true);
            $ligne++;
        }
        $pdf->SetFillColor(179, 179, 179);
        $pdf->SetFont('helvetica', 'B', 6.5);
        $pdf->MultiCell(128, 0, 'TOTAL', 1, 'L', 1, 0, '', '', true);
        $pdf->MultiCell(15, 0, number_format($total_montant,'0','',' '), 1, 'R', 1, 0, '', '', true);
        $pdf->MultiCell(17, 0, number_format($total_part_assure,'0','',' '), 1, 'R', 1, 0, '', '', true);
        $pdf->MultiCell(15, 0, number_format($total_part_ac,'0','',' '), 1, 'R', 1, 0, '', '', true);
        $pdf->MultiCell(15, 0, number_format($total_part_cmu,'0','',' '), 1, 'R', 1, 1, '', '', true);


        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Write(0, '', '', 0, 'R', true, 0, false, false, 0);
        $pdf->Cell(0, 0, 'Total Part CMU: '.number_format($total_part_cmu,'0','',' ').' F CFA', 0, 1, 'L', 0, '', 1);
        $pdf->Cell(0, 0, 'Total Part AC: '.number_format($total_part_ac,'0','',' ').' F CFA', 0, 1, 'L', 0, '', 1);
        $pdf->Cell(0, 0, 'Total à Rembourser: '.number_format($total_part_cmu+$total_part_ac,'0','',' ').' F CFA', 0, 1, 'L', 0, '', 1);
        $pdf->SetFont('helvetica', 'BI', 8);
        $pdf->Cell(0, 0, 'Arrété la presente facture la somme de: '.strtoupper(chiffresEnLettres($total_part_cmu+$total_part_ac)).' FRANCS CFA', 0, 1, 'L', 0, '', 1);
        $pdf->SetFont('helvetica', 'BU', 10);
        $pdf->Write(0, '', '', 0, 'R', true, 0, false, false, 0);
        $pdf->Cell(0, 0, 'SIGNATURE & CACHET', 0, 1, 'R', 0, '', 1);

        // This method has several options, check the source code documentation for more information.
        $js = 'print(true);';
        $pdf->IncludeJS($js);
        $pdf->Output('', 'I');
    }else {
        echo 'Une erreur est survenue lors de l\'impression. Prière contacter un support';
    }
}