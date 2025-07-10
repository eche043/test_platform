<?php

require_once '../../../Classes/UTILISATEURS.php';
if(isset($_SESSION['ECMU_USER_ID'])){
    $session_user = $_SESSION['ECMU_USER_ID'];
    if(!empty($session_user)){
        $UTILISATEURS = new UTILISATEURS();
        $utilisateur_existe = $UTILISATEURS->trouver($session_user,null,null);
        if(!empty($utilisateur_existe['ID_UTILISATEUR'])){
            $trouver_ets = $UTILISATEURS->trouver_ets_utilisateur($session_user);
            if($utilisateur_existe['ACTIF'] != 1){
                $json = array(
                    'status' => false,
                    'message' => 'VOTRE IDENTIFIANT A ETE DESACTIVE. VOUS NE POUVEZ PAS EFFECTUER CETTE ACTION. VEUILLEZ CONTACTER VOTRE ADMINISTRATEUR SYSTEME.'
                );
            }else{
                if(!($_POST['code_collectivite'])){
                    $json = array(
                        'status' => false,
                        'message' => 'VOTRE COLLECTIVITE N\'A PAS ETE DEFINIE. VOUS NE POUVEZ PAS EFFECTUER CETTE ACTION. VEUILLEZ CONTACTER VOTRE ADMINISTRATEUR SYSTEME.'
                    );
                }else{
                    require_once '../../../Classes/ASSURES.php';
                    require_once '../../../Classes/COLLECTIVITES.php';
                    $ASSURES = new ASSURES();
                    $COLLECTIVITES = new COLLECTIVITES();
                    $code_collectivite = $_POST['code_collectivite'];
                    $trouver_ogd_collectivite = $COLLECTIVITES->trouver($code_collectivite);
                    if(isset($trouver_ogd_collectivite['CODE_OGD_COTISATIONS']) && !empty($trouver_ogd_collectivite['CODE_OGD_COTISATIONS'])) {
                        $fichier = DIR. 'FORMAT FICHIER.xlsx';
                        $path_exports = DIR.'EXPORTS/POPULATIONS_COLLECTIVITES/'.$code_collectivite.'/'.date('d_m_Y',time()).'/';
                        if(!file_exists($path_exports)) {
                            mkdir($path_exports,0777, true);
                        }

                        if (file_exists($fichier)) {
                            $extension = strrchr($fichier,'.');
                            $mode = 'r';
                            $handle = fopen($fichier, $mode);
                            $i=2;
                            if(!empty($fichier)) {
                                $liste_population = $COLLECTIVITES->trouver_populations_collectivite_par_statut($code_collectivite,1);
                                if(count($liste_population)!=0){

                                    require '../../../../vendor/autoload.php';
                                    $allowedFileType = [
                                        'application/vnd.ms-excel',
                                        'text/xls',
                                        'text/xlsx',
                                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                                    ];

                                    $Reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();

                                    $spreadSheet = $Reader->load($fichier);

                                    $excelSheet = $spreadSheet->getActiveSheet();
                                    foreach ($liste_population as $pop){
                                        $excelSheet->setCellValue('A'.$i, $pop['TYPE']);
                                        $excelSheet->setCellValue('B'.$i, $pop['PAYEUR_NUM_SECU']);
                                        $excelSheet->setCellValue('C'.$i, $pop['PAYEUR_NUM_MATRICULE']);
                                        $excelSheet->setCellValue('D'.$i, $pop['PAYEUR_NUM_OGD']);
                                        $excelSheet->setCellValue('E'.$i, $pop['PAYEUR_NOM']);
                                        $excelSheet->setCellValue('F'.$i, $pop['PAYEUR_PRENOMS']);
                                        $excelSheet->setCellValue('G'.$i, date('d/m/Y',strtotime($pop['PAYEUR_DATE_NAISSSANCE'])));
                                        $excelSheet->setCellValue('H'.$i, $pop['BENEFICIAIRE_NUM_SECU']);
                                        $excelSheet->setCellValue('I'.$i, $pop['BENEFICIAIRE_NUM_MATRICULE']);
                                        $excelSheet->setCellValue('J'.$i, $pop['BENEFICIAIRE_NUM_OGD']);
                                        $excelSheet->setCellValue('K'.$i, $pop['BENEFICIAIRE_NOM']);
                                        $excelSheet->setCellValue('L'.$i, $pop['BENEFICIAIRE_PRENOMS']);
                                        $excelSheet->setCellValue('M'.$i, date('d/m/Y',strtotime($pop['BENEFICIAIRE_DATE_NAISSANCE'])));
                                        $excelSheet->setCellValue('N'.$i, $pop['BENEFICIAIRE_CIVILITE']);
                                        $excelSheet->setCellValue('O'.$i, $pop['BENEFICIAIRE_SEXE']);
                                        $excelSheet->setCellValue('P'.$i, $pop['BENEFICIAIRE_LIEU_NAISSANCE']);
                                        $excelSheet->setCellValue('Q'.$i, $pop['BENEFICIAIRE_LIEU_RESIDENCE']);
                                        $i++;
                                    }

                                    $Writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadSheet);
                                    $newfilename = 'liste_population_'.$code_collectivite.'_'.date('dmYHis',time()).'.xlsx';
                                    $Writer->save($path_exports.$newfilename);//$sheetCount = count($spreadSheetAry);
                                    $json = array(
                                        'status' => true,
                                        'message'=> URL.'EXPORTS/POPULATIONS_COLLECTIVITES/'.$code_collectivite.'/'.date('d_m_Y',time()).'/'.$newfilename
                                    );
                                }else{
                                    $json = array(
                                        'status' => false,
                                        'message' => '...'
                                    );
                                }

                            }else{

                            }
                        }else{

                        }
                    }else{
                        $json = array(
                            'status' => false,
                            'message' => 'L\'OGD DE VOTRE COLLECTIVITE N\'A PAS ETE DEFINI. VEUILLEZ CONTACTER VOTRE ADMINISTRATEUR SYSTEME.'
                        );
                    }
                }
            }
        }else{
            $json = array(
                'status' => false,
                'message' => 'VOTRE IDENTIFIANT EST INCORRECT. VOUS NE POUVEZ PAS EFFECTUER CETTE ACTION. VEUILLEZ CONTACTER VOTRE ADMINISTRATEUR SYSTEME.'
            );
        }
    }else{
        $json = array(
            'status' => false,
            'message' => 'VOTRE SESSION EST INACTIVE. PRIERE VOUS RECONNECTER POUR POURSUIVRE CETTE ACTION.'
        );
    }
}else{
    $json = array(
        'status' => false,
        'message' => 'VOTRE SESSION EST INACTIVE. PRIERE VOUS RECONNECTER POUR POURSUIVRE CETTE ACTION.'
    );
}
echo json_encode($json);
?>

