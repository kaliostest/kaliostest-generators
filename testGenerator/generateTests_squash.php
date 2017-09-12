<?php
session_save_path('./../tmp');
session_start();
require 'int/testGenerator.php';
require_once 'Propel.php';
include 'PHPExcel.php';
include 'PHPExcel/Writer/Excel2007.php';
require_once './../inc/config.inc.php';
require_once './../inc/FlxZipArchive.inc.php';


/*Add your imports here*/
	
class generateTests_squash implements testGenerator {
	
	
	
	/* Si l'utilisateur à cliqué sur Générer les tests manuels d'1
	cas de test
	*/
	
	public function generateTestCase ($array_lang) {
	
		
				
		$application = unserialize(urldecode($_SESSION['application']));
		if (!file_exists ($application->getManualTestsPath())) {
			Throw new Exception($array_lang['ERROR_TESTGENERATIONERRORDIR']);
				
		}
		
				$objPHPExcel = $this->createXLSFileUpdate();
				$application = unserialize(urldecode($_SESSION['application']));
				
				
				//get different testcases!
				$exportTests = ExportTestsQuery::create()->find();
				$tests_name = array();
				$tests = array();
				foreach ($exportTests as $exportTest) {
					if (!in_array( $exportTest->getTestName(), $tests_name)) {
						$tests_name[] = $exportTest->getTestName();
						$tests[] = array('testcase'=>$exportTest->getTestName(),'folder'=>$exportTest->getFolderName(), 'criteria'=>$exportTest->getCriteria());
					}
				}
				
				$objPHPExcel = $this->createXLSFileUpdate();
				$j=1;
				foreach ($tests as $test) {
					++$j;
					$testcaseName  =	utf8_encode($test['testcase']);
					$testcaseCrits = 	utf8_encode($test['criteria']);
					$folderName    = 	utf8_encode($test['folder']);
						
					$tcpathvalue = '';
					if (strpos($testcaseCrits, 'TC_PATH')!=-1) {
						$critsArray = split('=', $testcaseCrits);
						for($i=0; $i < sizeof ($critsArray); $i++) {
							if ($critsArray[$i]=='TC_PATH') {
								$tcpathvalue = str_replace('|', '', $critsArray[$i+1]);
								break;
							}
						}
					}
						
					//if TC_PATH is in testcase criteria, use it, otherwise use the testcase folder value
					$tc_path = $tcpathvalue!='' ? $tcpathvalue :  utf8_encode($application->getLabel()).'/'.$folderName;
					$objPHPExcel->setActiveSheetIndex(0);
					$objPHPExcel->getActiveSheet()->SetCellValue('A'.$j, 'UPDATE');
					$objPHPExcel->getActiveSheet()->SetCellValue('C'.$j, $application->getLabel());
					$objPHPExcel->getActiveSheet()->SetCellValue('D'.$j, '/'.$tc_path.'/'.$testcaseName);
					$objPHPExcel->getActiveSheet()->SetCellValue('H'.$j, $testcaseName);
						
				}
				
				
				$exportTests = ExportTestsQuery::create()->find();
				$objPHPExcel->setActiveSheetIndex(1);
				$j=2;
				$k=1;
				$testcaseFileName = $exportTests[0]->getTestName() ;
				foreach ($exportTests as $exportTest) {
					$testcaseName = utf8_encode($exportTest->getTestName());
					$objPHPExcel->getActiveSheet()->SetCellValue('A'.$j, 'UPDATE');
					$objPHPExcel->getActiveSheet()->SetCellValue('B'.$j, '/'.$tc_path.'/'.$testcaseName);
					//if name is changed, means another testcase JV has started
					if ($testcaseName!=$testNameMem) $k=1;
					$objPHPExcel->getActiveSheet()->SetCellValue('E'.$j, $k);
					$objPHPExcel->getActiveSheet()->SetCellValue('H'.$j, utf8_encode ('<p>'.$exportTest->getStepName().'</p>'.$exportTest->getStepDescription()));
					$stepresults = $exportTest->getStepResults()!='' ? $exportTest->getStepResults() : '';
					$objPHPExcel->getActiveSheet()->SetCellValue('I'.$j, $stepresults);
					++$j;
					++$k;
					$testNameMem = $testcaseName;
				}
					
				$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
				$objWriter->save($application->getManualTestsPath().'/'.$testcaseFileName.'.xls');
				
	}

	
	
	/*
	Si l'utilisateur à cliqué sur Générer les tests manuels de
	l'ensemble des cas de test*/
	
	public function generateAllTestcases($array_lang) {
	
		
		
		$application = unserialize(urldecode($_SESSION['application']));
		if (!file_exists ($application->getManualTestsPath())) {
			Throw new Exception($array_lang['ERROR_TESTGENERATIONERRORDIR']);
				
		}
	
			
		//get different testcases!
		$exportTests = ExportTestsQuery::create()->find();
		$tests_name = array();
		$tests = array();
		foreach ($exportTests as $exportTest) {
				if (!in_array( $exportTest->getTestName(), $tests_name)) {
						$tests_name[] = $exportTest->getTestName();
						$tests[] = array('testcase'=>$exportTest->getTestName(),'folder'=>$exportTest->getFolderName(), 'criteria'=>$exportTest->getCriteria());
				}
		}
		
		$objPHPExcel = $this->createXLSFileUpdate();
		$j=1;
		foreach ($tests as $test) {
			++$j;
			$testcaseName  =	utf8_encode($test['testcase']);
			$testcaseCrits = 	utf8_encode($test['criteria']);
			$folderName    = 	utf8_encode($test['folder']);
			
			$tcpathvalue = '';
			if (strpos($testcaseCrits, 'TC_PATH')!=-1) {
				$critsArray = split('=', $testcaseCrits);
				for($i=0; $i < sizeof ($critsArray); $i++) {
					if ($critsArray[$i]=='TC_PATH') {
						$tcpathvalue = str_replace('|', '', $critsArray[$i+1]);
						break;
					}
				}
			}
			
			//if TC_PATH is in testcase criteria, use it, otherwise use the testcase folder value
			$tc_path = $tcpathvalue!='' ? $tcpathvalue :  utf8_encode($application->getLabel()).'/'.$folderName;
			$objPHPExcel->setActiveSheetIndex(0);
			$objPHPExcel->getActiveSheet()->SetCellValue('A'.$j, 'UPDATE');
			$objPHPExcel->getActiveSheet()->SetCellValue('C'.$j, $application->getLabel());
			$objPHPExcel->getActiveSheet()->SetCellValue('D'.$j, '/'.$tc_path.'/'.$testcaseName);
			$objPHPExcel->getActiveSheet()->SetCellValue('H'.$j, $testcaseName);
			
		}	
	
		
		
		$exportTests = ExportTestsQuery::create()->find();
		$objPHPExcel->setActiveSheetIndex(1);
		$j=2;
		$k=1;
		foreach ($exportTests as $exportTest) {
			
			$testcaseName = utf8_encode($exportTest->getTestName());
			$folderName = utf8_encode($exportTest->getFolderName());
			$objPHPExcel->getActiveSheet()->SetCellValue('A'.$j, 'UPDATE');
			
			$testcaseCrits = 	$exportTest->getCriteria();
			$tcpathvalue = '';
			if (strpos($testcaseCrits, 'TC_PATH')!=-1) {
				$critsArray = split('=', $testcaseCrits);
				for($i=0; $i < sizeof ($critsArray); $i++) {
					if ($critsArray[$i]=='TC_PATH') {
						$tcpathvalue = str_replace('|', '', $critsArray[$i+1]);
						break;
					}
				}
			}
			$tc_path = $tcpathvalue!='' ? $tcpathvalue :  utf8_encode($application->getLabel()).'/'.$folderName;	
			
			$objPHPExcel->getActiveSheet()->SetCellValue('B'.$j, '/'.$tc_path.'/'.$testcaseName);
			//if name is changed, means another testcase JV has started
			if ($testcaseName!=$testNameMem) $k=1;
			$objPHPExcel->getActiveSheet()->SetCellValue('E'.$j, $k);
			$objPHPExcel->getActiveSheet()->SetCellValue('H'.$j, utf8_encode('<p>'.$exportTest->getStepName().'</p>'.$exportTest->getStepDescription()));
			$stepresults = $exportTest->getStepResults()!='' ? $exportTest->getStepResults() :  '';
			$objPHPExcel->getActiveSheet()->SetCellValue('I'.$j, $stepresults);
			++$j;
			++$k;
			$testNameMem = $testcaseName;		
		}
					
		$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
		$objWriter->save($application->getManualTestsPath().'/Cas_de_test.xls');

	}
	
	
	
	/*Si l'utilisateur à cliqué sur Générer les tests manuels du
	rapport de génération*/
	
	public function generateTestcasesGenerationReport($array_lang) {
		
	
		$application = unserialize(urldecode($_SESSION['application']));
		if (!file_exists ($application->getManualTestsPath())) {
			Throw new Exception($array_lang['ERROR_TESTGENERATIONERRORDIR']);
				
		}
			
		//get different testcases!
		$exportTests = ExportTestsQuery::create()->find();
		$tests_name = array();
		$tests = array();
		foreach ($exportTests as $exportTest) {
			if (!in_array( $exportTest->getTestName(), $tests_name)) {
				$tests_name[] = $exportTest->getTestName();
				$tests[] = array('testcase'=>$exportTest->getTestName(),'folder'=>$exportTest->getFolderName(), 'criteria'=>$exportTest->getCriteria());
			}
		}
	
		
		$zip_file_name = $application->getManualTestsPath().'/'.$tests[0]['folder'].'.zip';
		if (file_exists ($zip_file_name)) unlink ($zip_file_name);

		$zip = new ZipArchive();
		$filename = $application->getManualTestsPath().'/'.$tests[0]['folder'].'.zip';
		
		if ($zip->open($filename, ZipArchive::CREATE)!==TRUE) {
			exit("Impossible d'ouvrir le fichier <$filename>\n");
		}
		
		foreach ($tests as $test) {

				$objPHPExcel = new PHPExcel();
				$objPHPExcel->setActiveSheetIndex(0);
				$objPHPExcel->getActiveSheet()->setTitle('Exemple');
				
				$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'DESCRIPTION');
				$objPHPExcel->getActiveSheet()->SetCellValue('B1', utf8_encode($test['testcase']));
				$objPHPExcel->getActiveSheet()->SetCellValue('A2', 'IMPORTANCE');
				$objPHPExcel->getActiveSheet()->SetCellValue('A3', 'CREATED_BY');
				$objPHPExcel->getActiveSheet()->SetCellValue('A4', 'CREATED_ON');
				
				$exportTestsLines = ExportTestsQuery::create()->filterByTestName($test['testcase'])->find();
				$i=5;
				foreach ($exportTestsLines as $exportTestsLine) {
					
					$objPHPExcel->getActiveSheet()->SetCellValue('A'.$i, 'ACTION_STEP');
					$objPHPExcel->getActiveSheet()->SetCellValue('B'.$i,  utf8_encode('<p>'.$exportTestsLine->getStepName().'</p>'.$exportTestsLine->getStepDescription()));
					$stepresults = $exportTestsLine->getStepResults()!='' ? $exportTestsLine->getStepResults(): '';
					$objPHPExcel->getActiveSheet()->SetCellValue('C'.$i,  $stepresults);
							
				$i++;	
				}
				$objPHPExcel->getActiveSheet()->SetCellValue('A'.$i, 'PREREQUISITE');
		
				$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
				$objWriter->save($application->getManualTestsPath().'/'.$test['testcase'].'.xls');
		
				//zip creation
				$zip->addFile($application->getManualTestsPath().'/'. $test['testcase'].'.xls', iconv("cp1251", "cp866", $test['testcase']).'.xls');
				
		}
		
		$zip->close();
	}
	
	
	
	
	/**
	 * create file structure for Squash update
	 */
	public function createXLSFileUpdate() {
		
		
		$objPHPExcel = new PHPExcel();	
		
		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->getActiveSheet()->setTitle('TEST_CASES');
		$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'ACTION');
		$objPHPExcel->getActiveSheet()->SetCellValue('B1', 'PROJECT_ID');
		$objPHPExcel->getActiveSheet()->SetCellValue('C1', 'PROJECT_NAME');
		$objPHPExcel->getActiveSheet()->SetCellValue('D1', 'TC_PATH');
		$objPHPExcel->getActiveSheet()->SetCellValue('E1', 'TC_NUM');
		$objPHPExcel->getActiveSheet()->SetCellValue('F1', 'TC_ID');
		$objPHPExcel->getActiveSheet()->SetCellValue('G1', 'TC_REFERENCE');
		$objPHPExcel->getActiveSheet()->SetCellValue('H1', 'TC_NAME');
		$objPHPExcel->getActiveSheet()->SetCellValue('I1', 'TC_WEIGHT_AUTO');
		$objPHPExcel->getActiveSheet()->SetCellValue('J1', 'TC_WEIGHT');
		$objPHPExcel->getActiveSheet()->SetCellValue('K1', 'TC_NATURE');
		$objPHPExcel->getActiveSheet()->SetCellValue('L1', 'TC_TYPE');
		$objPHPExcel->getActiveSheet()->SetCellValue('M1', 'TC_STATUS');
		$objPHPExcel->getActiveSheet()->SetCellValue('N1', 'TC_DESCRIPTION');
		$objPHPExcel->getActiveSheet()->SetCellValue('O1', 'TC_PRE_REQUISITE');
		$objPHPExcel->getActiveSheet()->SetCellValue('P1', 'TC_#_CALLED_BY');
		$objPHPExcel->getActiveSheet()->SetCellValue('Q1', 'TC_#_ATTACHMENT');
		$objPHPExcel->getActiveSheet()->SetCellValue('R1', 'TC_CREATED_ON');
		$objPHPExcel->getActiveSheet()->SetCellValue('S1', 'TC_CREATED_BY');
		$objPHPExcel->getActiveSheet()->SetCellValue('T1', 'TC_LAST_MODIFIED_ON');
		$objPHPExcel->getActiveSheet()->SetCellValue('U1', 'TC_LAST_MODIFIED_BY');
		
			
			
		//FIN onglet TEST_CASES
			
		$objPHPExcel->createSheet();
		$sheet = $objPHPExcel->setActiveSheetIndex(1);
		$sheet->setTitle('STEPS');
		$objPHPExcel->setActiveSheetIndex(1);
		$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'ACTION');
		$objPHPExcel->getActiveSheet()->SetCellValue('B1', 'TC_OWNER_PATH');
		$objPHPExcel->getActiveSheet()->SetCellValue('C1', 'TC_OWNER_ID');
		$objPHPExcel->getActiveSheet()->SetCellValue('D1', 'TC_STEP_ID');
		$objPHPExcel->getActiveSheet()->SetCellValue('E1', 'TC_STEP_NUM');
		$objPHPExcel->getActiveSheet()->SetCellValue('F1', 'TC_STEP_IS_CALL_STEP');
		$objPHPExcel->getActiveSheet()->SetCellValue('G1', 'TC_STEP_CALL_DATASET');
		$objPHPExcel->getActiveSheet()->SetCellValue('H1', 'TC_STEP_ACTION');
		$objPHPExcel->getActiveSheet()->SetCellValue('I1', 'TC_STEP_EXPECTED_RESULT');
		$objPHPExcel->getActiveSheet()->SetCellValue('J1', 'TC_STEP_#_REQ');
		$objPHPExcel->getActiveSheet()->SetCellValue('K1', 'TC_STEP_#_ATTACHMENT');
			
			
			
		$objPHPExcel->createSheet();
		$sheet = $objPHPExcel->setActiveSheetIndex(2);
		$sheet->setTitle('PARAMETERS');
		
		$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'ACTION');
		$objPHPExcel->getActiveSheet()->SetCellValue('B1', 'TC_OWNER_PATH');
		$objPHPExcel->getActiveSheet()->SetCellValue('C1', 'TC_OWNER_ID');
		$objPHPExcel->getActiveSheet()->SetCellValue('D1', 'TC_PARAM_ID');
		$objPHPExcel->getActiveSheet()->SetCellValue('E1', 'TC_PARAM_NAME');
		$objPHPExcel->getActiveSheet()->SetCellValue('F1', 'TC_PARAM_DESCRIPTION');
		
			
		$objPHPExcel->createSheet();
		$sheet = $objPHPExcel->setActiveSheetIndex(3);
		$sheet->setTitle('DATASETS');
		$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'ACTION');
		$objPHPExcel->getActiveSheet()->SetCellValue('B1', 'TC_OWNER_PATH');
		$objPHPExcel->getActiveSheet()->SetCellValue('C1', 'TC_OWNER_ID');
		$objPHPExcel->getActiveSheet()->SetCellValue('D1', 'TC_DATASET_ID');
		$objPHPExcel->getActiveSheet()->SetCellValue('E1', 'TC_DATASET_NAME');
		$objPHPExcel->getActiveSheet()->SetCellValue('F1', 'TC_PARAM_OWNER_PATH');
		$objPHPExcel->getActiveSheet()->SetCellValue('G1', 'TC_PARAM_OWNER_ID');
		$objPHPExcel->getActiveSheet()->SetCellValue('H1', 'TC_DATASET_PARAM_NAME');
		$objPHPExcel->getActiveSheet()->SetCellValue('I1', 'TC_DATASET_PARAM_VALUE');
			
			
		$objPHPExcel->createSheet();
		$sheet = $objPHPExcel->setActiveSheetIndex(4);
		$sheet->setTitle('LINK_REQ_TC');
		
		$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'ACTION');
		$objPHPExcel->getActiveSheet()->SetCellValue('B1', 'REQ_PATH');
		$objPHPExcel->getActiveSheet()->SetCellValue('C1', 'REQ_VERSION_NUM');
		$objPHPExcel->getActiveSheet()->SetCellValue('D1', 'TC_PATH');
		
		
		return $objPHPExcel;
		
		
		
	}
	

	
	
	
}
	
	
?>