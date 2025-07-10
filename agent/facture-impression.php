<?php
header('Content-type: text/html; charset=UTF-8');

if(isset($_GET['num']) && isset($_GET['type'])) {
    require_once '../_configs/Functions/chiffresEnLettres.php';
    require_once '../_configs/Functions/function_convert_special_characters_to_normal.php';
    require_once '../_configs/Classes/UTILISATEURS.php';
    require_once '../_configs/Classes/FACTURES.php';
    require_once '../_configs/Classes/PROFESSIONNELSANTE.php';
    require_once '../_configs/Classes/ETABLISSEMENTSSANTE.php';
    require_once '../_configs/Classes/MEDICAMENTS.php';
    require_once '../_configs/Classes/ACTESMEDICAUX.php';
    require_once '../_configs/Classes/COLLECTIVITES.php';
    require_once('../vendor/tecnickcom/tcpdf/config/tcpdf_config.php');
    require_once('../vendor/tecnickcom/tcpdf/tcpdf.php');

    $FACTURES = new FACTURES();
    $PROFESSIONNELSANTE = new PROFESSIONNELSANTE();
    $MEDICAMENTS = new MEDICAMENTS();
    $ETABLISSEMENTSSANTE = new ETABLISSEMENTSSANTE();
    $ACTESMEDICAUX = new ACTESMEDICAUX();
    $facture = $FACTURES->trouver_facture(NULL,$_GET['num']);

    if(!empty($facture['FEUILLE'])) {
        if(!empty($facture['PS'])){
            $ps = $FACTURES->verifier_facture_ps($facture['PS'],NULL,$facture['ETABLISSEMENT'],strtoupper(date('Y-m-d',strtotime($facture['DATE_SOINS']))));
        }else{
            $ps['nom_prenom'] = NULL;
            $ps['libelle_specialite'] = NULL;
        }

        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('IPSCNAM');
        $pdf->SetTitle('Facture n° '.$facture['FEUILLE']);
        $pdf->SetSubject('Feuille de soins éléctronique');
        $pdf->SetKeywords('CMU, Facture, Feuille de soins, Prestations, Santé, OGD');


        $pdf->SetMargins(6, 9.3, 5, true);
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 70);
        $pdf->Write(0, '', '', 0, 'R', true, 0, false, false, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(40, 0, date('d/m/Y',strtotime($facture['DATE_SOINS'])), 0, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(45, 0, $facture['NUM_OGD'].' / '.$facture['NOM_OGD'], 0, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(15, 0, '', 0, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(95, 0, $facture['NOM'].' '.$facture['PRENOM'], 0, 'C', 0, 1, '', '', true);

        $pdf->Write(0, '', '', 0, 'R', true, 0, false, false, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(40, 0, $facture['NUM_FS_INITIALE'], 0, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(45, 0, $facture['FEUILLE'], 0, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(15, 0, '', 0, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(38, 0, $facture['NUM_SECU'], 0, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(41, 0, date('d/m/Y',strtotime($facture['DATE_NAISSANCE'])), 0, 'C', 0, 0, '', '', true);
        if($facture['GENRE'] == 'M') {
            $pdf->MultiCell(10, 0, 'X', 0, 'C', 0, 1, '', '', true);
        }else {
            $pdf->MultiCell(17, 0, 'X', 0, 'R', 0, 1, '', '', true);
        }

        $pdf->Write(0, '', '', 0, 'R', true, 0, false, false, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(40, 0, $facture['NUM_EP_CNAM'], 0, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(45, 0, $facture['NUM_EP_AC'], 0, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(15, 0, '', 0, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(35, 0, '', 0, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(41, 0, '', 0, 'C', 0, 1, '', '', true);


        $pdf->MultiCell(41, 0, '', 0, 'C', 0, 1, '', '', true);
        $pdf->MultiCell(41, 0, '', 0, 'C', 0, 1, '', '', true);

        $pdf->Write(0, '', '', 0, 'R', true, 0, false, false, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(40, 0, $facture['ETABLISSEMENT'], 0, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(160, 0, $facture['NOM_ETS'], 0, 'C', 0, 1, '', '', true);

        $pdf->MultiCell(41, 0, '', 0, 'C', 0, 1, '', '', true);
        $pdf->MultiCell(41, 0, '', 0, 'C', 0, 1, '', '', true);
        $pdf->MultiCell(41, 0, '', 0, 'C', 0, 1, '', '', true);

        $pdf->Write(0, '', '', 0, 'R', true, 0, false, false, 0);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(20, 0, $facture['PS'], 0, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(100, 0, $ps['nom_prenom'], 0, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(80, 0, $ps['libelle_specialite'], 0, 'C', 0, 1, '', '', true);


        $pdf->MultiCell(41, 0, '', 0, 'C', 0, 1, '', '', true);
        $pdf->MultiCell(41, 0, '', 0, 'C', 0, 1, '', '', true);
        $pdf->MultiCell(41, 0, '', 0, 'C', 0, 1, '', '', true);
        $pdf->MultiCell(41, 0, '', 0, 'C', 0, 1, '', '', true);
        $pdf->MultiCell(41, 0, '', 0, 'C', 0, 1, '', '', true);
        $pdf->MultiCell(41, 0, '', 0, 'C', 0, 1, '', '', true);
        $pdf->MultiCell(41, 0, '', 0, 'C', 0, 1, '', '', true);
        $pdf->MultiCell(41, 0, '', 0, 'C', 0, 1, '', '', true);
        $pdf->MultiCell(41, 0, '', 0, 'C', 0, 1, '', '', true);
        $pdf->MultiCell(41, 0, '', 0, 'C', 0, 1, '', '', true);

        if ($facture['TYPE_FEUILLE'] == 'AMB') {
            $pdf->MultiCell(41, 0, '', 0, 'C', 0, 1, '', '', true);
            $actes = $FACTURES->trouver_facture_liste_actes($facture['FEUILLE']);
            $total_montant = 0;
            $total_part_assure = 0;
            $total_part_cmu = 0;
            $total_part_ac = 0;
            foreach ($actes as $acte) {
                /*if($acte['TYPE'] == 'm') {
                    if(substr($acte['CODE'],0,5)=='06188' || substr($acte['CODE'],0,5)=='22500'){
                        $type_code = 'EAN13';
                    }else{
                        $type_code = 'CEGEDIM';
                    }
                    $medicament = $MEDICAMENTS->trouver_medicament($type_code,$acte['code']);
                    $designation = $medicament['LIBELLE'];
                }else {
                    $ngap = $ACTESMEDICAUX->trouver_un_acte($acte['CODE']);
                    $designation = $ngap['LIBELLE'];
                }
                $montant = $acte['MONTANT'] * $acte['QUANTITE'];
                $part_cmu = round($montant * 0.7);
                if($facture['CODE_CSP'] == 'IND') {
                    $part_ac = $montant - $part_cmu;
                }else {
                    $part_ac = 0;
                }
                $part_assure = ($montant - ($part_cmu + $part_ac));*/

                $details_acte = $FACTURES->trouver_facture_acte($facture['FEUILLE'],$acte['CODE']);
                $acte_code = $details_acte['CODE'];
                $designation = strtoupper($details_acte['LIBELLE']);
                $montant = $acte['MONTANT'] * $acte['QUANTITE'];
                $part_cmu = $acte['PART_RO'] * $acte['QUANTITE'];
                /*if($acte['MONTANT'] > $details_acte['MONTANT_BASE']){
                    $montant_base = $details_acte['MONTANT_BASE'] * $acte['QUANTITE'];
                    $part_cmu = round($montant_base * 0.7);
                }
                else{
                    $part_cmu = round($montant * 0.7);
                }*/
                /* $montant = $acte['MONTANT'] * $acte['QUANTITE'];
                $part_cmu = round($montant * 0.7); */
                if($facture['CODE_OGD_AFFILIATION'] == '03016000') {
                    $part_ac = $montant - $part_cmu;
                    $part_assure = ($montant - ($part_cmu + $part_ac));
                }else {
                    $part_ac = $acte['PART_RC'] * $acte['QUANTITE'];
                    $part_assure = $acte['PART_ASSURE'] * $acte['QUANTITE'];
                }


                $pdf->Write(0, '', '', 0, 'R', true, 0, false, false, 0);
                $pdf->SetFont('helvetica', '', 8);

                $pdf->MultiCell(20, 0, $details_acte['CODE'], 0, 'C', 0, 0, '', '', true);
                $pdf->MultiCell(50, 0, strtoupper(conversionCaractere($details_acte['LIBELLE'])), 0, 'L', 0, 0, '', '', true);
                $pdf->MultiCell(20, 0, date('d/m/Y',strtotime($acte['DEBUT'])), 0, 'R', 0, 0, '', '', true);
                $pdf->MultiCell(20, 0, date('d/m/Y',strtotime($acte['FIN'])), 0, 'R', 0, 0, '', '', true);
                $pdf->MultiCell(10, 0, $acte['QUANTITE'], 0, 'R', 0, 0, '', '', true);
                $pdf->MultiCell(18, 0, $montant, 0, 'R', 0, 0, '', '', true);
                $pdf->MultiCell(18, 0, $part_cmu, 0, 'R', 0, 0, '', '', true);
                $pdf->MultiCell(18, 0, $part_ac, 0, 'R', 0, 0, '', '', true);
                $pdf->MultiCell(18, 0, $part_assure, 0, 'R', 0, 1, '', '', true);
                $pdf->SetFont('helvetica', '', 8);
                $pdf->MultiCell(41, 0, '', 0, 'C', 0, 1, '', '', true);

                $total_montant = $total_montant + $montant;
                $total_part_cmu = $total_part_cmu + $part_cmu;
                $total_part_ac = $total_part_ac + $part_ac;
                $total_part_assure = $total_part_assure + $part_assure;
            }

            if(count($actes) == 1) {
                $pdf->MultiCell(41, 0, '', 0, 'C', 0, 1, '', '', true);
                $pdf->MultiCell(41, 0, '', 0, 'C', 0, 1, '', '', true);
                $pdf->MultiCell(41, 0, '', 0, 'C', 0, 1, '', '', true);
                $pdf->MultiCell(41, 0, '', 0, 'C', 0, 1, '', '', true);
                $pdf->MultiCell(41, 0, '', 0, 'C', 0, 1, '', '', true);
            }if(count($actes) == 2) {
                $pdf->MultiCell(41, 0, '', 0, 'C', 0, 1, '', '', true);
                $pdf->MultiCell(41, 0, '', 0, 'C', 0, 1, '', '', true);
            }

            $pdf->Write(0, '', '', 0, 'R', true, 0, false, false, 0);
            $pdf->SetFont('helvetica', '', 8);

            $pdf->MultiCell(120, 0, '', 0, 'L', 0, 0, '', '', true);
            $pdf->MultiCell(18, 0, $total_montant, 0, 'R', 0, 0, '', '', true);
            $pdf->MultiCell(18, 0, $total_part_cmu, 0, 'R', 0, 0, '', '', true);
            $pdf->MultiCell(18, 0, $total_part_ac, 0, 'R', 0, 0, '', '', true);
            $pdf->MultiCell(18, 0, $total_part_assure, 0, 'R', 0, 1, '', '', true);
        }

        if ($facture['TYPE_FEUILLE'] == 'DEN') {

            $actes = $FACTURES->trouver_facture_liste_actes($facture['FEUILLE']);
            $total_montant = 0;
            $total_part_assure = 0;
            $total_part_cmu = 0;
            $total_part_ac = 0;
            foreach ($actes as $acte) {
                if($acte['TYPE'] == 'm') {
                    if(substr($acte['CODE'],0,5)=='06188' || substr($acte['CODE'],0,5)=='22500'){
                        $type_code = 'EAN13';
                    }else{
                        $type_code = 'CEGEDIM';
                    }
                    $medicament = $MEDICAMENTS->trouver_medicament($type_code,$acte['CODE']);
                    $designation = $medicament['LIBELLE'];
                }else {
                    $ngap = $ACTESMEDICAUX->trouver_un_acte($acte['CODE']);
                    $designation = $ngap['LIBELLE'];
                }
                $montant = $acte['MONTANT'] * $acte['QUANTITE'];
                $part_cmu = $acte['PART_RO'] * $acte['QUANTITE'];
                //$part_cmu = round($montant * 0.7);
                if($facture['CODE_CSP'] == 'IND') {
                    $part_ac = $montant - $part_cmu;$part_assure = ($montant - ($part_cmu + $part_ac));
                }else {
                    $part_ac = $acte['PART_RC'] * $acte['QUANTITE'];
                    $part_assure = $acte['PART_ASSURE'] * $acte['QUANTITE'];
                }


                $pdf->Write(0, '', '', 0, 'R', true, 0, false, false, 0);
                $pdf->SetFont('helvetica', '', 10);

                $pdf->MultiCell(20, 0, $acte['CODE'], 0, 'C', 0, 0, '', '', true);
                $pdf->MultiCell(50, 0, substr($designation,0,55).'...', 0, 'L', 0, 0, '', '', true);
                $pdf->MultiCell(30, 0, date('d/m/Y',strtotime($acte['DEBUT'])), 0, 'L', 0, 0, '', '', true);
                $pdf->MultiCell(10, 0, $acte['NUM_DENT'], 0, 'R', 0, 0, '', '', true);
                $pdf->MultiCell(18, 0, $montant, 0, 'R', 0, 0, '', '', true);
                $pdf->MultiCell(18, 0, $part_cmu, 0, 'R', 0, 0, '', '', true);
                $pdf->MultiCell(18, 0, $part_ac, 0, 'R', 0, 0, '', '', true);
                $pdf->MultiCell(18, 0, $part_assure, 0, 'R', 0, 1, '', '', true);
                $pdf->SetFont('helvetica', '', 8);
                $pdf->MultiCell(41, 0, '', 0, 'C', 0, 1, '', '', true);

                $total_montant = $total_montant + $montant;
                $total_part_cmu = $total_part_cmu + $part_cmu;
                $total_part_ac = $total_part_ac + $part_ac;
                $total_part_assure = $total_part_assure + $part_assure;

            }
        }

        if($facture['TYPE_FEUILLE'] == 'EXP' || $facture['TYPE_FEUILLE'] == 'HOS') {

            $actes = $FACTURES->trouver_facture_liste_actes($facture['FEUILLE']);
            $total_montant = 0;
            $total_part_assure = 0;
            $total_part_cmu = 0;
            $total_part_ac = 0;
            foreach ($actes as $acte) {
                /*if($acte['TYPE'] == 'm') {
                    if(substr($acte['CODE'],0,5)=='06188' || substr($acte['CODE'],0,5)=='22500'){
                        $type_code = 'EAN13';
                    }else{
                        $type_code = 'CEGEDIM';
                    }
                    $medicament = $MEDICAMENTS->trouver_medicament($type_code,$acte['code']);
                    $designation = $medicament['LIBELLE'];
                }else {
                    $ngap = $ACTESMEDICAUX->trouver_un_acte($acte['CODE']);
                    $designation = $ngap['LIBELLE'];
                }
                $montant = $acte['MONTANT'] * $acte['QUANTITE'];
                $part_cmu = round($montant * 0.7);
                if($facture['CODE_CSP'] == 'IND') {
                    $part_ac = $montant - $part_cmu;
                }else {
                    $part_ac = 0;
                }
                $part_assure = ($montant - ($part_cmu + $part_ac));*/

                $details_acte = $FACTURES->trouver_facture_acte($facture['FEUILLE'],$acte['CODE']);
                $acte_code = $details_acte['CODE'];
                $designation = strtoupper($details_acte['LIBELLE']);
                $montant = $acte['MONTANT'] * $acte['QUANTITE'];
                $part_cmu = $acte['PART_RO'] * $acte['QUANTITE'];
                /*if($acte['MONTANT'] > $details_acte['MONTANT_BASE']){
                    $montant_base = $details_acte['MONTANT_BASE'] * $acte['QUANTITE'];
                    $part_cmu = round($montant_base * 0.7);
                }
                else{
                    $part_cmu = round($montant * 0.7);
                }*/
                /* $montant = $acte['MONTANT'] * $acte['QUANTITE'];
                $part_cmu = round($montant * 0.7); */
                if($facture['CODE_OGD_AFFILIATION'] == '03016000') {
                    $part_ac = $montant - $part_cmu;
                    $part_assure = ($montant - ($part_cmu + $part_ac));
                }else {
                    $part_ac = $acte['PART_RC'] * $acte['QUANTITE'];
                    $part_assure = $acte['PART_ASSURE'] * $acte['QUANTITE'];
                }


                $pdf->Write(0, '', '', 0, 'R', true, 0, false, false, 0);
                $pdf->SetFont('helvetica', '', 10);

                $pdf->MultiCell(20, 0, $acte['CODE'], 0, 'C', 0, 0, '', '', true);
                $pdf->MultiCell(100, 0, $designation, 0, 'L', 0, 0, '', '', true);
                $pdf->MultiCell(18, 0, $montant, 0, 'R', 0, 0, '', '', true);
                $pdf->MultiCell(18, 0, $part_cmu, 0, 'R', 0, 0, '', '', true);
                $pdf->MultiCell(18, 0, $part_ac, 0, 'R', 0, 0, '', '', true);
                $pdf->MultiCell(18, 0, $part_assure, 0, 'R', 0, 1, '', '', true);
                $pdf->SetFont('helvetica', '', 8);
                $pdf->MultiCell(41, 0, '', 0, 'C', 0, 1, '', '', true);

                $total_montant = $total_montant + $montant;
                $total_part_cmu = $total_part_cmu + $part_cmu;
                $total_part_ac = $total_part_ac + $part_ac;
                $total_part_assure = $total_part_assure + $part_assure;

            }

            if(count($actes) == 1) {
                $pdf->MultiCell(41, 0, '', 0, 'C', 0, 1, '', '', true);
                $pdf->MultiCell(41, 0, '', 0, 'C', 0, 1, '', '', true);
                $pdf->MultiCell(41, 0, '', 0, 'C', 0, 1, '', '', true);
                $pdf->MultiCell(41, 0, '', 0, 'C', 0, 1, '', '', true);
                $pdf->MultiCell(41, 0, '', 0, 'C', 0, 1, '', '', true);
                $pdf->MultiCell(41, 0, '', 0, 'C', 0, 1, '', '', true);
                $pdf->MultiCell(41, 0, '', 0, 'C', 0, 1, '', '', true);
                $pdf->MultiCell(41, 0, '', 0, 'C', 0, 1, '', '', true);
                $pdf->MultiCell(41, 0, '', 0, 'C', 0, 1, '', '', true);
                $pdf->MultiCell(41, 0, '', 0, 'C', 0, 1, '', '', true);
                $pdf->MultiCell(41, 0, '', 0, 'C', 0, 1, '', '', true);
                $pdf->MultiCell(41, 0, '', 0, 'C', 0, 1, '', '', true);
                $pdf->MultiCell(41, 0, '', 0, 'C', 0, 1, '', '', true);
                $pdf->MultiCell(41, 0, '', 0, 'C', 0, 1, '', '', true);
                $pdf->MultiCell(41, 0, '', 0, 'C', 0, 1, '', '', true);
                $pdf->MultiCell(41, 0, '', 0, 'C', 0, 1, '', '', true);
                $pdf->MultiCell(41, 0, '', 0, 'C', 0, 1, '', '', true);
                $pdf->MultiCell(41, 0, '', 0, 'C', 0, 1, '', '', true);
                $pdf->MultiCell(41, 0, '', 0, 'C', 0, 1, '', '', true);
                $pdf->MultiCell(41, 0, '', 0, 'C', 0, 1, '', '', true);
                $pdf->MultiCell(41, 0, '', 0, 'C', 0, 1, '', '', true);
                $pdf->MultiCell(41, 0, '', 0, 'C', 0, 1, '', '', true);
            }if(count($actes) == 2) {
                $pdf->MultiCell(41, 0, '', 0, 'C', 0, 1, '', '', true);
                $pdf->MultiCell(41, 0, '', 0, 'C', 0, 1, '', '', true);
                $pdf->MultiCell(41, 0, '', 0, 'C', 0, 1, '', '', true);
                $pdf->MultiCell(41, 0, '', 0, 'C', 0, 1, '', '', true);
                $pdf->MultiCell(41, 0, '', 0, 'C', 0, 1, '', '', true);
                $pdf->MultiCell(41, 0, '', 0, 'C', 0, 1, '', '', true);
                $pdf->MultiCell(41, 0, '', 0, 'C', 0, 1, '', '', true);
                $pdf->MultiCell(41, 0, '', 0, 'C', 0, 1, '', '', true);
                $pdf->MultiCell(41, 0, '', 0, 'C', 0, 1, '', '', true);
                $pdf->MultiCell(41, 0, '', 0, 'C', 0, 1, '', '', true);
                $pdf->MultiCell(41, 0, '', 0, 'C', 0, 1, '', '', true);
                $pdf->MultiCell(41, 0, '', 0, 'C', 0, 1, '', '', true);
                $pdf->MultiCell(41, 0, '', 0, 'C', 0, 1, '', '', true);
                $pdf->MultiCell(41, 0, '', 0, 'C', 0, 1, '', '', true);
                $pdf->MultiCell(41, 0, '', 0, 'C', 0, 1, '', '', true);
                $pdf->MultiCell(41, 0, '', 0, 'C', 0, 1, '', '', true);
                $pdf->MultiCell(41, 0, '', 0, 'C', 0, 1, '', '', true);
                $pdf->MultiCell(41, 0, '', 0, 'C', 0, 1, '', '', true);
                $pdf->MultiCell(41, 0, '', 0, 'C', 0, 1, '', '', true);
            }if(count($actes) == 3) {
                $pdf->MultiCell(41, 0, '', 0, 'C', 0, 1, '', '', true);
                $pdf->MultiCell(41, 0, '', 0, 'C', 0, 1, '', '', true);
                $pdf->MultiCell(41, 0, '', 0, 'C', 0, 1, '', '', true);
                $pdf->MultiCell(41, 0, '', 0, 'C', 0, 1, '', '', true);
                $pdf->MultiCell(41, 0, '', 0, 'C', 0, 1, '', '', true);
                $pdf->MultiCell(41, 0, '', 0, 'C', 0, 1, '', '', true);
                $pdf->MultiCell(41, 0, '', 0, 'C', 0, 1, '', '', true);
                $pdf->MultiCell(41, 0, '', 0, 'C', 0, 1, '', '', true);
                $pdf->MultiCell(41, 0, '', 0, 'C', 0, 1, '', '', true);
                $pdf->MultiCell(41, 0, '', 0, 'C', 0, 1, '', '', true);
                $pdf->MultiCell(41, 0, '', 0, 'C', 0, 1, '', '', true);
                $pdf->MultiCell(41, 0, '', 0, 'C', 0, 1, '', '', true);
                $pdf->MultiCell(41, 0, '', 0, 'C', 0, 1, '', '', true);
                $pdf->MultiCell(41, 0, '', 0, 'C', 0, 1, '', '', true);
                $pdf->MultiCell(41, 0, '', 0, 'C', 0, 1, '', '', true);
            }

            $pdf->Write(0, '', '', 0, 'R', true, 0, false, false, 0);
            $pdf->SetFont('helvetica', '', 10);

            $pdf->MultiCell(120, 0, '', 0, 'L', 0, 0, '', '', true);
            $pdf->MultiCell(18, 0, $total_montant, 0, 'R', 0, 0, '', '', true);
            $pdf->MultiCell(18, 0, $total_part_cmu, 0, 'R', 0, 0, '', '', true);
            $pdf->MultiCell(18, 0, $total_part_ac, 0, 'R', 0, 0, '', '', true);
            $pdf->MultiCell(18, 0, $total_part_assure, 0, 'R', 0, 1, '', '', true);
        }




        $js = 'print(true);';
        $pdf->IncludeJS($js);
        $pdf->Output('', 'I');

    }
}