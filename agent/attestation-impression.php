<?php
header('Content-type: text/html; charset=UTF-8');
include '../_configs/Classes/BDD.php';
include '../_configs/Classes/ATTESTATIONSDROITS.php';
include '../_configs/Classes/ASSURES.php';

$ATTESTATIONSDROITS = new ATTESTATIONSDROITS();
$ASSURES = new ASSURES();
$attestations = $ATTESTATIONSDROITS->trouver($_GET['id'],null);
$attestation = $attestations[0];

if(empty($attestation['ID'])) {
    echo '<script>window.location.href="'.URL.'agent/attestations-droits.php"</script>';
}

$assure = $ASSURES->trouver($attestation['NUM_SECU']);
$genre = $ASSURES->trouver_assure_genre($assure['SEXE']);


$json = array(
    "ASSURE" => array(
        'num_secu' => $attestation['NUM_SECU'],
        'nom' => $assure['NOM'],
        'prenom' => $assure['PRENOM'],
        'date_naissance' => date('d-m-Y',strtotime($assure['DATE_NAISSANCE'])),
        'genre' => $assure['SEXE']
    ),
    "MOTIF" => $attestation['MOTIF_DEMANDE'],
    'debut_validite_validite' => date('d-m-Y',strtotime($attestation['DATE_DEBUT_VALIDITE'])),
    'fin_validite_validite' => date('d-m-Y',strtotime($attestation['DATE_FIN_VALIDITE']))
);

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
        $img_file = IMAGES.'logo-cnam_opacity.jpg';

        // Render the image
        $this->Image($img_file, 0, 0, 223, 280, '', '', '', false, 300, '', false, false, 0);

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
$pdf->SetTitle('Attestation N '.$attestation['ID']);
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

$pdf->AddPage();

//$pdf->Image(IMAGES.'logo-cnam_opacity.jpg', 5, 30, 200, 200);

// set style for barcode
$style = array(
    'border' => 0
);
$tbl = <<<EOD
<table cellspacing="0" cellpadding="1" border="1" style="font-size: 14px">
    <tr align="center">
        <td rowspan="3" width="100"><img src="../_publics/images/logo_cnam.png" width="48" alt="Logo CNAM"></td>
        <td colspan="3" width="580" style="height: 25px; line-height: 25px; font-size: 25px" bgcolor="#87CEFA"><b>ATTESTATION DE DROITS CMU</b></td>
    </tr>
    <tr align="center" style="height: 25px; line-height: 25px">
        <td><b>Ref. ER-24-AFL</b></td>
        <td>Version 1.0 du 29/06/2017</td>
        <td>Page 1 sur 1</td>
    </tr>

</table>
EOD;
$pdf->writeHTML($tbl, true, false, false, false, 'C');
$pdf->Ln(10);
$pdf->Write(0, 'ATTESTATION N '.$attestation['ID'], '', 0, 'C', true, 0, false, false, 0);
$pdf->Ln(5);
$pdf->SetFillColor(135, 206, 250);
$pdf->Cell(0, 0, 'ASSURE', 1, 1, 'C', 1, '', 0);

$pdf->MultiCell(60, 0, 'N° SECU', 1, 'L', 0, 0, '', '', true);
$pdf->MultiCell(0, 0, $assure['NUM_SECU'], 1, 'L', 0, 1, '', '', true);
$pdf->MultiCell(60, 0, 'NOM & PRENOM(S)', 1, 'L', 0, 0, '', '', true);
$pdf->MultiCell(0, 0, $assure['NOM'].' '.$assure['PRENOM'], 1, 'L', 0, 1, '', '', true);
$pdf->MultiCell(60, 0, 'DATE DE NAISSANCE', 1, 'L', 0, 0, '', '', true);
$pdf->MultiCell(0, 0, date('d/m/Y',strtotime($assure['DATE_NAISSANCE'])), 1, 'L', 0, 1, '', '', true);
$pdf->MultiCell(60, 0, 'GENRE', 1, 'L', 0, 0, '', '', true);
$pdf->MultiCell(0, 0, $genre['LIBELLE'], 1, 'L', 0, 1, '', '', true);
$pdf->Cell(0, 0, 'MOTIF DE LA DEMANDE', 1, 1, 'C', 0, '', 0);
$pdf->MultiCell(190, 0, $attestation['MOTIF_DEMANDE'], 1, 'C', 0, 1, '', '', true, 0, false, true, '', 'M', true);
$pdf->MultiCell(60, 0, 'VALIDITE', 1, 'L', 0, 0, '', '', true);
$pdf->MultiCell(0, 0, 'DU '.date('d/m/Y',strtotime($attestation['DATE_DEBUT_VALIDITE'])).' AU '.date('d/m/Y',strtotime($attestation['DATE_FIN_VALIDITE'])), 1, 'L', 0, 1, '', '', true);

// QRCODE,L : QR-CODE Low error correction
$pdf->write2DBarcode(json_encode($json), 'QRCODE,L', 65, 150, 75, 75, $style, 'C');



//Close and output PDF document
$pdf->Output('', 'I');

$bdd = null;
?>