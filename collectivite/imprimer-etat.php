<?php
require_once '../_configs/Classes/UTILISATEURS.php';
require_once '../_configs/Functions/chiffresEnLettres.php';

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
            require_once '../_configs/Classes/COLLECTIVITES.php';
            require_once '../_configs/Classes/COTISATIONS.php';
            require_once '../_configs/Classes/ASSURES.php';
            $COLLECTIVITES = new COLLECTIVITES();
            $COTISATIONS = new COTISATIONS();
            $ASSURES = new ASSURES();
            $user_collectivite = $COLLECTIVITES->trouver($user['CODE_COLLECTIVITE']);
            $date_debut = date('Y-m-d',strtotime($_GET['date_debut'])).' 00:00:00';
            $date_fin = date('Y-m-d',strtotime($_GET['date_fin'])).' 23:59:59';
            if(empty($user_collectivite['CODE'])) {
                session_destroy();
                echo "<script>self.close();</script>";
            }else {
                require_once('../vendor/tecnickcom/tcpdf/config/tcpdf_config.php');
                require_once('../vendor/tecnickcom/tcpdf/tcpdf.php');

                $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
                // set document information
                $pdf->SetCreator(PDF_CREATOR);
                $pdf->SetAuthor('IPSCNAM');
                $pdf->SetTitle('Bordereau de transmission de la collectivité: '.$user_collectivite['CODE']);
                $pdf->SetSubject('Paiements à la CMU');
                $pdf->SetKeywords('CMU, Bordereau, paiements, Codtisations, Santé, OGD');

                if (@file_exists(dirname(__FILE__).'/lang/fra.php')) {
                    require_once(dirname(__FILE__).'/lang/fra.php');
                    $pdf->setLanguageArray($l);
                }

                $pdf->AddPage();

                $pdf->SetFont('helvetica', '', 15);
                $pdf->Cell(0, 0, 'BORDEREAU DE TRANSMISSION', 1, 1, 'C', 0, '', 1);
                $pdf->Write(0, '', '', 0, 'R', true, 0, false, false, 0);
                $pdf->SetFont('helvetica', '', 10);
                $pdf->Cell(0, 0, $user_collectivite['RAISON_SOCIALE'], 0, 1, 'C', 0, '', 1);
                $pdf->SetFont('helvetica', 'B', 10);
                $pdf->Cell(0, 0, 'BORDEREAU DU: '.$_GET['date_debut'].' AU '.$_GET['date_fin'], 0, 1, 'C', 0, '', 1);

                $paiements_req2 = $COTISATIONS->trouver_cotisation_web($user_collectivite['CODE'],$date_debut,$date_fin);
                 $nb_paiements = count($paiements_req2);


                $pdf->Write(0, '', '', 0, 'R', true, 0, false, false, 0);
                $pdf->SetFillColor(179, 179, 179);
                $pdf->Write(0, '', '', 0, 'R', true, 0, false, false, 0);
                $pdf->SetFont('helvetica', 'B', 6.5);
                $pdf->MultiCell(10, 0, 'N°', 1, 'C', 1, 0, '', '', true);
                $pdf->MultiCell(20, 0, 'DATE', 1, 'C', 1, 0, '', '', true);
                $pdf->MultiCell(25, 0, 'N° TRANSACTION', 1, 'C', 1, 0, '', '', true);
                $pdf->MultiCell(15, 0, 'TYPE.', 1, 'C', 1, 0, '', '', true);
                $pdf->MultiCell(20, 0, 'N° SECU', 1, 'C', 1, 0, '', '', true);
                $pdf->MultiCell(30, 0, 'NOM.', 1, 'C', 1, 0, '', '', true);
                $pdf->MultiCell(50, 0, 'PRENOMS', 1, 'C', 1, 0, '', '', true);
                $pdf->MultiCell(20, 0, 'MONTANT', 1, 'C', 1, 1, '', '', true);
                $pdf->SetFont('helvetica', '', 6.5);


                $ligne = 1;
                $montant = 0;
                foreach ($paiements_req2 as $paiement) {
                    $assure = $ASSURES->trouver_assure($paiement['NUM_SECU']);
                    $pdf->MultiCell(10, 0, $ligne, 1, 'R', 1, 0, '', '', true);
                    $pdf->MultiCell(20, 0, date('d-m-Y',strtotime($paiement['DATE_REG'])), 1, 'C', 0, 0, '', '', true);
                    $pdf->MultiCell(25, 0, $paiement['NUM_TRANSACTION'], 1, 'R', 0, 0, '', '', true);
                    $pdf->MultiCell(15, 0, str_replace('I','INDIVIDUEL',str_replace('F','FAMILIAL',$paiement['PAYMENT_TYPE'])), 1, 'R', 0, 0, '', '', true);
                    $pdf->MultiCell(20, 0, $assure['NUM_SECU'], 1, 'C', 0, 0, '', '', true);
                    $pdf->MultiCell(30, 0, $assure['NOM'], 1, 'L', 0, 0, '', '', true);
                    $pdf->MultiCell(50, 0, $assure['PRENOM'], 1, 'L', 0, 0, '', '', true);
                    $pdf->MultiCell(20, 0, number_format($paiement['PAID_TRANSACTION_AMOUNT'],'0','',' '), 1, 'R', 0, 1, '', '', true);

                    $montant = $montant + $paiement['PAID_TRANSACTION_AMOUNT'];
                    $ligne++;
                }
                $pdf->SetFont('helvetica', 'B', 6.5);
                $pdf->MultiCell(170, 0, 'TOTAL', 1, 'L', 1, 0, '', '', true);
                $pdf->MultiCell(20, 0, number_format($montant,'0','',' '), 1, 'R', 1, 1, '', '', true);


                $pdf->SetFont('helvetica', 'B', 10);
                $pdf->Write(0, '', '', 0, 'R', true, 0, false, false, 0);
                $pdf->Cell(0, 0, 'Montant total: '.number_format($montant,'0','',' ').' F CFA', 0, 1, 'L', 0, '', 1);
                $pdf->SetFont('helvetica', 'BI', 8);
                $pdf->Cell(0, 0, 'Arrété la somme de: '.strtoupper(chiffresEnLettres($montant)).' FRANCS CFA', 0, 1, 'L', 0, '', 1);
                $pdf->SetFont('helvetica', 'BU', 10);
                $pdf->Write(0, '', '', 0, 'R', true, 0, false, false, 0);
                $pdf->Cell(0, 0, 'SIGNATURE & CACHET', 0, 1, 'R', 0, '', 1);


                $js = 'print(true);';
                $pdf->IncludeJS($js);
                $pdf->Output('', 'I');
            }
        }
    }
}else{
    session_destroy();
    echo '<script>window.location.href="'.URL.'"</script>';
}
?>
<script>
    $(function () {
        $('#dataTable').DataTable();
    });



</script>

