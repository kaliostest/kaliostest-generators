<?php
session_save_path('./../tmp');
session_start();
require 'int/testGenerator.php';
require_once 'Propel.php';
include 'PHPExcel.php';
include 'PHPExcel/Writer/Excel2007.php';
require_once './../inc/config.inc.php';


/*Add your imports here*/
	
class generateTests_QC implements testGenerator {
	
	
	
	/* Si l'utilisateur à cliqué sur Générer les tests manuels d'1
	cas de test
	*/
	
	public function generateTestCase ($array_lang) {
	
		
		
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
							$tests[] = array('testcase'=>$exportTest->getTestName(),'folder'=>$exportTest->getFolderName(), 'criteria'=>$exportTest->getCriteria(), 'description'=>$exportTest->getTestDescription());
						}
					}
					$objPHPExcel = $this->createXLSFileForQC();
					$cnt_tc=1;
					$j=1;
					$testcaseFile = $tests[0]['testcase'];
					foreach ($tests as $test) {
					
						$cnt_tc = $j+1;
						
						$testcaseName  			=	utf8_encode($test['testcase']);
						$testcaseCrits 			= 	utf8_encode($test['criteria']);
						$folderName    			= 	utf8_encode($test['folder']);
						$testcaseDescription 	= 	utf8_encode($test['description']);			
						
						$exportTestsLines = ExportTestsQuery::create()->filterByTestName($test['testcase'])->find();
						foreach ($exportTestsLines as $exportTestsLine) {
							++$j;
						
							$objPHPExcel->setActiveSheetIndex(0);
							$objPHPExcel->getActiveSheet()->SetCellValue('A'.$j, $folderName);
							$objPHPExcel->getActiveSheet()->SetCellValue('B'.$j, $testcaseName);
							$objPHPExcel->getActiveSheet()->SetCellValue('C'.$j, $testcaseDescription);
							$objPHPExcel->getActiveSheet()->SetCellValue('D'.$j, utf8_encode($exportTestsLine->getStepName()));
							$objPHPExcel->getActiveSheet()->SetCellValue('E'.$j, $this->handleStepDescriptionAndResults($exportTestsLine->getStepDescription()));
							$stepresults = $exportTestsLine->getStepResults()!='' ?  $this->handleStepDescriptionAndResults ($exportTestsLine->getStepResults()): '';
							$objPHPExcel->getActiveSheet()->SetCellValue('F'.$j,  $stepresults);		
								
						}
					
						$this->handleCrits($objPHPExcel,$test['testcase'],$testcaseCrits, $cnt_tc);
									
					}
					$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
					$objWriter->save($application->getManualTestsPath().'/'.$testcaseFile.'.xls');
			
		}

	
	
	/*
	Si l'utilisateur à cliqué sur Générer les tests manuels de
	l'ensemble des cas de test*/
	
	public function generateAllTestcases($array_lang) {
	
		
	$application = unserialize(urldecode($_SESSION['application']));
	
	if (!file_exists ($application->getManualTestsPath())) {
		Throw new Exception($array_lang['ERROR_TESTGENERATIONERRORDIR']);
			
	}
	//check criteria before export
	$crits = array();
	$exportTests = ExportTestsQuery::create()->find();
	$numligne =0;
	$index = 0;
	foreach ($exportTests as $exportTest) {
		$criteria = $exportTest->getCriteria();
		$critsvalues = split ('\|', $criteria);
		$critsnames = array();
		//var_dump ($critsvalues);
		foreach ($critsvalues as $critsvalue) {
			$critvalue = split ('=', $critsvalue);
		//	if ($critvalue[0]!='') {
				$critsnames[]= $critvalue[0];
				++$index;
		//	}
		}
		++$numligne;
		$crits[] = $critsnames;
	}
	
	//	var_dump ($crits);
	$bool_samecrits = true;
	//size of first crit arrays
	for ($i=1; $i < sizeof ($crits); $i++) {
		$diff = array_diff_assoc($crits[$i], $crits[$i-1]);
		if (sizeof ($diff)!=0) {
	/*		echo 'pass : différence = ';
			echo ('<br>');
			var_dump ($diff);
			echo ('<br>');*/
			$bool_samecrits = false;
	
		}
	}

	if (!$bool_samecrits) {
		Throw new Exception($array_lang['ERROR_TESTGENERATIONCRITERIA']);		
	}
	
		//get different testcases!
		$exportTests = ExportTestsQuery::create()->find();
		$tests_name = array();
		$tests = array();
		foreach ($exportTests as $exportTest) {
			if (!in_array( $exportTest->getTestName(), $tests_name)) {
				$tests_name[] = $exportTest->getTestName();
				$tests[] = array('testcase'=>$exportTest->getTestName(),'folder'=>$exportTest->getFolderName(), 'criteria'=>$exportTest->getCriteria(), 'description'=>$exportTest->getTestDescription());
			}
		}
		
		$objPHPExcel = $this->createXLSFileForQC();
		//testcase line counter
		$cnt_tc =1;
		$j=1;
		foreach ($tests as $test) {
			
		
			$testcaseName  			=	utf8_encode($test['testcase']);
			$testcaseCrits 			= 	utf8_encode($test['criteria']);
			$folderName    			= 	utf8_encode($test['folder']);
			$testcaseDescription 	= 	utf8_encode($test['description']);			
			
			$exportTestsLines = ExportTestsQuery::create()->filterByTestName($test['testcase'])->find();
			
			$cnt_tc = $j+1;
			foreach ($exportTestsLines as $exportTestsLine) {
			++$j;
			
				$objPHPExcel->setActiveSheetIndex(0);
				$objPHPExcel->getActiveSheet()->SetCellValue('A'.$j, $folderName);
				$objPHPExcel->getActiveSheet()->SetCellValue('B'.$j, $testcaseName);
				$objPHPExcel->getActiveSheet()->SetCellValue('C'.$j, $testcaseDescription);
				$objPHPExcel->getActiveSheet()->SetCellValue('D'.$j, utf8_encode($exportTestsLine->getStepName()));
				$objPHPExcel->getActiveSheet()->SetCellValue('E'.$j, $this->handleStepDescriptionAndResults($exportTestsLine->getStepDescription()));
				$stepresults = $exportTestsLine->getStepResults()!='' ?  $this->handleStepDescriptionAndResults ($exportTestsLine->getStepResults()): '';
				$objPHPExcel->getActiveSheet()->SetCellValue('F'.$j,  $stepresults);		
					
			}
			$this->handleCrits($objPHPExcel,$test['testcase'],$testcaseCrits, $cnt_tc);
				
		}
		$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
		$objWriter->save($application->getManualTestsPath().'/Testcases.xls');	
	}
	
	
	
	/*Si l'utilisateur à cliqué sur Générer les tests manuels du
	rapport de génération*/
	
	public function generateTestcasesGenerationReport($array_lang) {
		
		
		$application = unserialize(urldecode($_SESSION['application']));
		if (!file_exists ($application->getManualTestsPath())) {
			Throw new Exception($array_lang['ERROR_TESTGENERATIONERRORDIR']);
				
		}
		//check criteria before export
		$crits = array();
		$exportTests = ExportTestsQuery::create()->find();
		$numligne =0;
		$index = 0;
		foreach ($exportTests as $exportTest) {
			$criteria = $exportTest->getCriteria();
			$critsvalues = split ('\|', $criteria);
			$critsnames = array();
			//var_dump ($critsvalues);
			foreach ($critsvalues as $critsvalue) {
				$critvalue = split ('=', $critsvalue);
				//	if ($critvalue[0]!='') {
				$critsnames[]= $critvalue[0];
				++$index;
				//	}
			}
			++$numligne;
			$crits[] = $critsnames;
		}
		
		//	var_dump ($crits);
		$bool_samecrits = true;
		//size of first crit arrays
		for ($i=1; $i < sizeof ($crits); $i++) {
			$diff = array_diff_assoc($crits[$i], $crits[$i-1]);
			if (sizeof ($diff)!=0) {
				/*		echo 'pass : différence = ';
					echo ('<br>');
					var_dump ($diff);
					echo ('<br>');*/
				$bool_samecrits = false;
		
			}
		}
		
		if (!$bool_samecrits) {
			Throw new Exception($array_lang['ERROR_TESTGENERATIONCRITERIA']);
		}
		
				//get different testcases!
				$exportTests = ExportTestsQuery::create()->find();
				$tests_name = array();
				$tests = array();
				foreach ($exportTests as $exportTest) {
					if (!in_array( $exportTest->getTestName(), $tests_name)) {
						$tests_name[] = $exportTest->getTestName();
						$tests[] = array('testcase'=>$exportTest->getTestName(),'folder'=>$exportTest->getFolderName(), 'criteria'=>$exportTest->getCriteria(), 'description'=>$exportTest->getTestDescription());
					}
				}
				
				$objPHPExcel = $this->createXLSFileForQC();
				$j=1;
				$cnt_tc =1;
				foreach ($tests as $test) {
					
				
					$testcaseName  			=	utf8_encode($test['testcase']);
					$testcaseCrits 			= 	utf8_encode($test['criteria']);
					$folderName    			= 	utf8_encode($test['folder']);
					$testcaseDescription 	= 	utf8_encode($test['description']);			
					
					$exportTestsLines = ExportTestsQuery::create()->filterByTestName($test['testcase'])->find();
					$cnt_tc = $j+1;
					foreach ($exportTestsLines as $exportTestsLine) {
						++$j;
					
						$objPHPExcel->setActiveSheetIndex(0);
						$objPHPExcel->getActiveSheet()->SetCellValue('A'.$j, $folderName);
						$objPHPExcel->getActiveSheet()->SetCellValue('B'.$j, $testcaseName);
						$objPHPExcel->getActiveSheet()->SetCellValue('C'.$j, $testcaseDescription);
						$objPHPExcel->getActiveSheet()->SetCellValue('D'.$j, utf8_encode($exportTestsLine->getStepName()));
						$objPHPExcel->getActiveSheet()->SetCellValue('E'.$j, $this->handleStepDescriptionAndResults($exportTestsLine->getStepDescription()));
						$stepresults = $exportTestsLine->getStepResults()!='' ?  $this->handleStepDescriptionAndResults ($exportTestsLine->getStepResults()): '';
						$objPHPExcel->getActiveSheet()->SetCellValue('F'.$j,  $stepresults);		
							
					}
					$this->handleCrits($objPHPExcel,$test['testcase'],$testcaseCrits, $cnt_tc);
					
				}
				$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
				$objWriter->save($application->getManualTestsPath().'/'.$test['folder'].'.xls');
					
		}
	
	
	
	
	/**
	 * create file structure for Squash update
	 */
	public function createXLSFileForQC() {
		
		
		$objPHPExcel = new PHPExcel();	
		
		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Folder Name');
		$objPHPExcel->getActiveSheet()->SetCellValue('B1', 'Test Name');
		$objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Test Description');
		$objPHPExcel->getActiveSheet()->SetCellValue('D1', 'Step Name');
		$objPHPExcel->getActiveSheet()->SetCellValue('E1', 'Step Description');
		$objPHPExcel->getActiveSheet()->SetCellValue('F1', 'Expected Result');
				
		//Criteria...
		return $objPHPExcel;
		
		
		
	}
	
	/**
	 * Handle test description
	 * @param string $txt
	 * @return string
	 */
	
	function handleStepDescriptionAndResults ($txt) {
		
		$txt =  str_replace ("</p>", "\r\n",  $txt);
		$txt =  str_replace ("<p>", '- ',$txt);
		$txt = rtrim($txt, "\r\n");
		return  utf8_encode($txt);
	}
	
	
	/**
	 * handle criteria
	 * @param string $objExcel
	 * @param unknown $crits
	 */
	function handleCrits ($objPHPExcel, $testcaseName, $crits, $cnt_tc)  {
		
/*		$id_file = fopen ("c:/temp/toto.txt", "a+");
		fwrite ($id_file, 'test name : '.$testcaseName);
		fwrite ($id_file, "\r\n");
		*/
		$letters = array ('G', 'H', 'I', 'J', 'K', 'L', 'M','N','O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
		$objPHPExcel->setActiveSheetIndex(0);
		$critsArray = explode ("|", $crits);
		$l=0;
		$exportTestsLinesCrits = ExportTestsQuery::create()->filterByTestName($testcaseName)->find();
	//	fwrite ($id_file, 'count :'.sizeof($exportTestsLinesCrits));
	//	fwrite ($id_file, "\r\n");
			

		foreach ($critsArray as $critNameValue) {
		
			$critNameValueArr = split ("=", $critNameValue);
			
/*			fwrite ($id_file, 'letters[j1]'.$letters[$l].'1');
			fwrite ($id_file, "\r\n");
			fwrite ($id_file, '----------------->crit : '.$critNameValueArr[0]);
			fwrite ($id_file, "\r\n");
*/			
			$objPHPExcel->getActiveSheet()->SetCellValue($letters[$l].'1', $critNameValueArr[0]);
			$m=$cnt_tc;
		//	fwrite ($id_file, 'Compteur de lignes  '.$m);
			//fwrite ($id_file, "\r\n");
			
		
			foreach ($exportTestsLinesCrits as $exportTestsLine) {
			/*	fwrite ($id_file, 'letters[jm]'.$letters[$l].$m);
				fwrite ($id_file, "\r\n");
				fwrite ($id_file, '------------->value : '.$critNameValueArr[1]);
				fwrite ($id_file, "\r\n");*/
				$objPHPExcel->getActiveSheet()->SetCellValue($letters[$l].$m, $critNameValueArr[1]);
				++$m;
			}
			++$l;
		}
		
		fclose ($id_file);
		return $objPHPExcel;
	}
	
	
	
	
}
	
	
?>