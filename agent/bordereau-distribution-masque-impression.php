<?php
header('Content-type: text/html; charset=UTF-8');

if(isset($_GET['numero_secu']) && isset($_GET['code_ets']) && isset($_GET['date_debut']) && isset($_GET['date_fin'])) {

    require_once '../_configs/Functions/chiffresEnLettres.php';
    require_once '../_configs/Classes/UTILISATEURS.php';
    require_once '../_configs/Classes/DISTRIBUTIONMASQUES.php';
    require_once '../_configs/Classes/ASSURES.php';
    require_once '../_configs/Classes/ETABLISSEMENTSSANTE.php';
    require_once('../vendor/tecnickcom/tcpdf/config/tcpdf_config.php');
    require_once('../vendor/tecnickcom/tcpdf/tcpdf.php');


    $ETABLISSEMENTSANTE = new ETABLISSEMENTSSANTE();
    $UTILISATEURS = new UTILISATEURS();
    $DISTRIBUTIONMASQUES = new DISTRIBUTIONMASQUES();
    $ASSURES = new ASSURES();

    $numero_secu = trim($_GET['numero_secu']);
    $code_ets = trim($_GET['code_ets']);
    $date_debut = strtoupper(date('Y-m-d',strtotime(str_replace('/','-',$_GET['date_debut']))));
    $date_fin = strtoupper(date('Y-m-d',strtotime(str_replace('/','-',$_GET['date_fin']))));

    $distributions = $DISTRIBUTIONMASQUES->moteur_recherche_par_centre($numero_secu, $date_debut, $date_fin, $code_ets);
    $nb_masques_distribues = count($distributions);
    $ets = $ETABLISSEMENTSANTE->trouver_etablissement_sante($_GET['code_ets']);
    if($nb_masques_distribues != 0) {
        /*$nbre_actes = 0;
        $part_cmu = 0;
        foreach ($factures as $fact){
            $nbre_actes = $nbre_actes + $fact['NOMBRE_ACTES'];
            $part_cmu = $part_cmu + (float)$fact['PART_CMU'];
        }*/

        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('IPSCNAM');
        $pdf->SetTitle('Bordereau du '.date('d/m/Y',strtotime($date_debut)).' au '.date('d/m/Y',strtotime($date_fin)));
        $pdf->SetSubject('Bordereau de Distribution de Masques - CNAM');
        $pdf->SetKeywords('CMU, Bordereau, transmission, Masques, Santé');

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
        $pdf->Cell(0, 0, 'BORDEREAU DE DISTRIBUTION DE MASQUES', 1, 1, 'C', 0, '', 1);
        $pdf->Write(0, '', '', 0, 'R', true, 0, false, false, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(0, 0, $ets['RAISON_SOCIALE'], 0, 1, 'C', 0, '', 1);

        $pdf->SetFont('helvetica', '', 10);
        $pdf->Write(0, '', '', 0, 'R', true, 0, false, false, 0);
        $pdf->MultiCell(30, 0, 'Date :', 0, 'L', 0, 0, '', '', true);
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->MultiCell(45, 0, date('d/m/Y',time()), 0, 'L', 0, 0, '', '', true);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(30, 0, 'Période:', 0, 'L', 0, 0, '', '', true);
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->MultiCell(80, 0, 'du '.date('d/m/Y',strtotime($date_debut)).' au '.date('d/m/Y',strtotime($date_fin)), 0, 'L', 0, 0, '', '', true);

        $pdf->Write(0, '', '', 0, 'R', true, 0, false, false, 0);
        $pdf->SetFillColor(179, 179, 179);
        $pdf->Write(0, '', '', 0, 'R', true, 0, false, false, 0);
        $pdf->SetFont('helvetica', 'B', 6.5);
        $pdf->MultiCell(10, 0, 'N°', 1, 'C', 1, 0, '', '', true);
        $pdf->MultiCell(25, 0, 'N° SECU', 1, 'C', 1, 0, '', '', true);
        $pdf->MultiCell(55, 0, 'NOM & PRENOM(S) DES ASSURES', 1, 'C', 1, 0, '', '', true);
        $pdf->MultiCell(45, 0, 'DISTRIBUE PAR', 1, 'C', 1, 0, '', '', true);
        $pdf->MultiCell(20, 0, 'DATE', 1, 'C', 1, 0, '', '', true);
        $pdf->MultiCell(15, 0, 'HEURE', 1, 'C', 1, 0, '', '', true);
        $pdf->MultiCell(20, 0, 'DATE FIN', 1, 'C', 1, 1, '', '', true);
        $pdf->SetFont('helvetica', '', 6.5);
        $ligne = 1;
        $total = 0;
        foreach ($distributions as $dst_masq) {
            $assure = $ASSURES->trouver($dst_masq['NUM_SECU']);
            $agent = $UTILISATEURS->trouver($dst_masq['USER_REG'],null,null);

            $pdf->MultiCell(10, 0, $ligne, 1, 'R', 1, 0, '', '', true);
            $pdf->MultiCell(25, 0, $dst_masq['NUM_SECU'], 1, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(55, 0, $assure['NOM'].' '.$assure['PRENOM'], 1, 'L', 0, 0, '', '', true);
            $pdf->MultiCell(45, 0, $agent['NOM'].' '.$agent['PRENOM'], 1, 'L', 0, 0, '', '', true);
            $pdf->MultiCell(20, 0, date('d/m/Y',strtotime($dst_masq['M_DATE_DEBUT'])), 1, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(15, 0, date('H:i',strtotime($dst_masq['DATE_REG'])), 1, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(20, 0, date('d/m/Y',strtotime($dst_masq['M_DATE_FIN'])), 1, 'C', 0, 1, '', '', true);
            $ligne++;
            $total ++;
        }
        $pdf->SetFillColor(179, 179, 179);
        $pdf->SetFont('helvetica', 'B', 6.5);
        $pdf->MultiCell(170, 0, 'TOTAL', 1, 'L', 1, 0, '', '', true);
        $pdf->MultiCell(20, 0, $total, 1, 'C', 1, 1, '', '', true);

        // This method has several options, check the source code documentation for more information.
        $js = 'print(true);';
        $pdf->IncludeJS($js);
        $pdf->Output('', 'I');
    }else {
        echo 'test';
    }
}