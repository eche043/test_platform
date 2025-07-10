<?php
$order_id = $_GET['order_num'];
$transaction_id = $_GET['transaction_id'];
$code_caisse = 'UPU000002';

header('Content-type: text/html; charset=UTF-8');
require_once '../_configs/Classes/UTILISATEURS.php';
require_once '../_configs/Classes/ASSURES.php';
require_once '../_configs/Classes/COLLECTIVITES.php';
$ASSURES = new ASSURES();
$COLLECTIVITES = new COLLECTIVITES();

$paiement = $ASSURES->verifier_paiement($order_id,$transaction_id);
$assure = $ASSURES->trouver($paiement['NUM_SECU']);
$caisse = $COLLECTIVITES->trouver($code_caisse);
//var_dump($paiement);
require_once('../vendor/tecnickcom/tcpdf/config/tcpdf_config.php');
require_once('../vendor/tecnickcom/tcpdf/tcpdf.php');

class MYPDF extends TCPDF {
    public function Header() {
        // Get the current page break margin
        $bMargin = $this->getBreakMargin();

        // Get current auto-page-break mode
        $auto_page_break = $this->AutoPageBreak;

        // Disable auto-page-break
        $this->SetAutoPageBreak(false, 0);

        // Define the path to the image that you want to use as watermark.
        $img_file = IMAGES.'logo_cnam.png';

        // Render the image
        $this->Image($img_file, 10, 15, 12, 12, '', '', '', false, 400, '', false, false, 0);

        // Restore the auto-page-break status
        $this->SetAutoPageBreak($auto_page_break, $bMargin);

        // Set the starting point for the page content
        $this->setPageMark();
    }
}

// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('IPSCNAM');
$pdf->SetTitle('RECU N° '.$transaction_id);
$pdf->SetSubject('Attestation de droits');

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set font
$pdf->SetFont('helvetica', '', 15);

// add a page
// set color for background
$pdf->SetFillColor(0, 136, 204);
$pdf->AddPage();

$style = array(
    'border' => 0
);
$logo_collectivite = IMAGES.'logos_collectivites/'.$caisse['LOGO'];
$pdf->Image($logo_collectivite, 190, 15, 12, 12, '', '', '', false, 400, '', false, false, 0);

$pdf->SetFont('helvetica', '', 7);
$pdf->Write(0, '', '', 0, 'R', true, 0, false, false, 0);
$pdf->SetFont('helvetica', 'B', 20);
$pdf->MultiCell(150, 0, "DECLARATION DE COTISATIONS", 1, 'C', 1, 1, 30, '', true);
$pdf->SetFont('helvetica', '', 12);
$pdf->MultiCell(35, 0, 'Ref: XX.XX.XX', 1, 'C', 0, 0, 30, '', true);
$pdf->MultiCell(75, 0, 'Version 1.0 du 16/02/2018', 1, 'C', 0, 0, '', '', true);
$pdf->MultiCell(40, 0, 'Page 1 sur 1', 1, 'C', 0, 1, '', '', true);

$pdf->SetFont('helvetica', '', 15);
$pdf->Write(0, '', '', 0, 'R', true, 0, false, false, 0);
$pdf->Cell(0, 0, 'RECU N°: '.$paiement['NUM_TRANSACTION'], 0, 1, 'C', 0, '', 1);
$pdf->Write(0, '', '', 0, 'R', true, 0, false, false, 0);

$pdf->SetFont('helvetica', '', 12);
$pdf->MultiCell(50, 0, 'Date de paiement', 0, 'L', 0, 0, '', '', true);
$pdf->MultiCell(0, 0, date('d/m/Y',strtotime($paiement['DATE_REG'])), 0, 'L', 0, 1, '', '', true);
$pdf->MultiCell(50, 0, 'N° Transaction', 0, 'L', 0, 0, '', '', true);
$pdf->MultiCell(0, 0, $paiement['NUM_TRANSACTION'], 0, 'L', 0, 1, '', '', true);

$pdf->MultiCell(50, 0, 'N° Sécu', 0, 'L', 0, 0, '', '', true);
$pdf->MultiCell(0, 0, $assure['NUM_SECU'], 0, 'L', 0, 1, '', '', true);

$pdf->MultiCell(50, 0, 'Nom & Prénom(s)', 0, 'L', 0, 0, '', '', true);
$pdf->MultiCell(0, 0, $assure['NOM'].' '.$assure['PRENOM'], 0, 'L', 0, 1, '', '', true);

$pdf->MultiCell(50, 0, 'Montant', 0, 'L', 0, 0, '', '', true);
$pdf->MultiCell(0, 0, number_format($paiement['TRANSACTION_MONTANT'],'0','',' ').' '.$paiement['CURRENCY'], 0, 'L', 0, 1, '', '', true);

$pdf->MultiCell(50, 0, 'Porte feuille', 0, 'L', 0, 0, '', '', true);
$pdf->MultiCell(0, 0, $paiement['WALLET'], 0, 'L', 0, 1, '', '', true);


$pdf->MultiCell(0, 0, '', 0, 'R', 0, 1, '', '', true);
$pdf->SetFont('', 'I', 9, '');
$pdf->MultiCell(0, 0, '(*) Prière de conserver précieusement ce reçu de paiement car il vous sera demandé en cas de réclamation', 0, 'L', 0, 1, '', '', true);


$js = 'print(true);';
$pdf->IncludeJS($js);


//Close and output PDF document
$pdf->Output('', 'I');
?>