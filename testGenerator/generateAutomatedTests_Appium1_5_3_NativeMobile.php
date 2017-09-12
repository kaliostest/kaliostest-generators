<?php
require 'int/automatedTestsGenerator.php';
require 'generateAutomatedTests_Appium1_4_8_NativeMobile.conf.php';
require_once 'Propel.php';
		
		

/*Add your imports here*/

class generateAutomatedTests_Appium1_5_3_NativeMobile implements automatedTestsGenerator {


	/* Si l'utilisateur à cliqué sur Générer les tests manuels d'1
	cas de test
	*/

	public function generateTestCase () {
		
		
	
		$timestamp = time();
	
		//get testcase infos 
		$testcase 			   = ExportAutomatedTestsQuery::create()->findOne();
		$testcase_class_name   = ucfirst(traiterAccents ($testcase->getTestCaseName()));
		$testcase_name  	   =  traiterAccents ($testcase->getTestCaseName());
		
		//get config 
		$exportConf = ExportConfQuery::create()->findOne();
		
		//get script export path 
		$exportPath = $exportConf->getScriptsFolder();
		
		//delete old java files
		$exportPath_lect = opendir ($exportPath);
		while ($file_name = readdir($exportPath_lect)) {
			//echo 
			if(strpos ($file_name, '.java')){
				$file = $exportPath.'/'.$file_name;
				unlink($file);
			}
		}
		
		//create main testcase java file with upper first class, and delete if already exists
		$testcaseFile = $exportPath.'/'.$testcase_class_name.'.java';
		if (file_exists($testcaseFile))	{
			unlink ($testcaseFile);
		}
		$id_testcaseFile = fopen ($testcaseFile, 'a+');
		$today = date("Y-m-d H:i:s", time());
	 	self::writeLine ($id_testcaseFile, "//kaliostest Testcase Export File");
	    self::writeLine($id_testcaseFile, "//Export date : ".$today);
	    self::writeLine($id_testcaseFile, '//Testcase '.$testcase_name);
	    self::writeLine($id_testcaseFile, "package scripts_metiers;");
	    
	 
	    self::writeLine($id_testcaseFile, "import scripts_techniques.*;");
	    //imports are fined in conf file
	    self::writeLine($id_testcaseFile,  IMPORT_MAINFILE);
	    self::writeLine($id_testcaseFile, 'public class '.$testcase_class_name.' extends TestCase {');
	    self::writeLine($id_testcaseFile, "	//drivers declaration");
	    self::writeLine($id_testcaseFile, "	public static WebDriver driverInstance;");
	    self::writeLine($id_testcaseFile, "	public static boolean JunitLaunch;");
	    self::writeLine($id_testcaseFile, "	public static WebDriver selfdriver;");


	    self::writeLine($id_testcaseFile, '	public '.$testcase_class_name.'(String name) {');
	    self::writeLine($id_testcaseFile, "		super(name);");
	    self::writeLine($id_testcaseFile, "	}");
	    self::writeLine($id_testcaseFile, "	//methode appel�e uniquement en mode lancement Junit, au lancement");
	    self::writeLine($id_testcaseFile, "	protected void setUp() throws Exception {");
	    self::writeLine($id_testcaseFile, "		JunitLaunch=true;");
	    self::writeLine($id_testcaseFile, "		super.setUp();");
	    self::writeLine($id_testcaseFile, "	}");
	    self::writeLine($id_testcaseFile, "	//methode appelée uniquement en mode lancement Junit, à la fermeture");
	    self::writeLine($id_testcaseFile, "	protected void tearDown() throws Exception {");
	    self::writeLine($id_testcaseFile, "		System.out.println (\"Passage teardown\");");
	    self::writeLine($id_testcaseFile, "	}");
	    self::writeLine($id_testcaseFile, "	//main method");
	    self::writeLine($id_testcaseFile, "	public static boolean launch() throws Exception {");
	   
	    
	    //find apk in conf
	    $array_apkvalue = split (' = ', self::findStrInConf($exportConf, 'apk'));
	    $apk = $array_apkvalue[1];
	    
		$array_osvalue = split (' = ', self::findStrInConf($exportConf, 'exploitation'));
	    $osvalue= $array_osvalue[1];
	  
	  	self::writeLine($id_testcaseFile, '		final String appium_url = Config.appium_url;
        final String deviceId = Config.deviceId;
        final String deviceOsVersion = Config.deviceOsVersion;');
	  	
	  	
	  	switch ($osvalue) {
	  		case 'Android' :
	  		self::writeLine($id_testcaseFile,'File app = new File ("'.$apk.'");
			if(!app.exists()) {
				throw new RuntimeException ("File not found : "+ app.getAbsolutePath());
			}');	break;
	  	}
	  	
	  		
	  			
      	 self::writeLine($id_testcaseFile, '		DesiredCapabilities capabilities = new DesiredCapabilities();
        //browser name comes from kaliostest
        capabilities.setCapability(MobileCapabilityType.DEVICE_NAME, deviceId);');
   
	    switch ($osvalue) {
			case 'Android' : 
			   self::writeLine($id_testcaseFile, '		capabilities.setCapability(MobileCapabilityType.PLATFORM_NAME, MobilePlatform.ANDROID);');
			   self::writeLine($id_testcaseFile, '		capabilities.setCapability(MobileCapabilityType.APP, app.getAbsolutePath());');
			   
			   break;
			case 'IOS': 
			   self::writeLine($id_testcaseFile, '		capabilities.setCapability(MobileCapabilityType.PLATFORM_NAME, MobilePlatform.IOS);');
			break;
			
		}
         self::writeLine($id_testcaseFile, '		capabilities.setCapability(MobileCapabilityType.PLATFORM_VERSION, deviceOsVersion);');
		 self::writeLine($id_testcaseFile, '		capabilities.setCapability(MobileCapabilityType.LAUNCH_TIMEOUT, Integer.parseInt (Config.window_attach_timeout));');
  
		//driver IOS or Android
	   switch ($osvalue) {
		case 'Android' : 
			
			 self::writeLine($id_testcaseFile, "		 AndroidDriver driver = new AndroidDriver(new URL(appium_url), capabilities);");
		break;
		
		case 'IOS': 
			 self::writeLine($id_testcaseFile, "		  IOSDriver driver = new IOSDriver(new URL(appium_url), capabilities);");	
		break;   	
	   }
	   
	   
	   self::writeLine($id_testcaseFile, '		'.$testcase_class_name.'.selfdriver = driver;');
	   
	    //call to scenarios
	    self::writeLine($id_testcaseFile, "		//call scenarios");
	    self::writeLine($id_testcaseFile, "		boolean status = true;");
	     
	    //find all different scenarios from export_automated_tests table
	  	$tests = ExportAutomatedTestsQuery::create()->orderById()->groupByScenarioId()->find();
	  	$index_scenario=0;
		foreach ($tests as $test) {
	 		++$index_scenario;
	 		if ($index_scenario>$index_start) {
		 		 self::writeLine($id_testcaseFile, '		//call scenario '.$test->getScenarioName());
		 	
		 		 $line_call = '';
		 		 $line_call.='    	status = Scenarios.'.traiterAccents($test->getScenarioName()).'_'.$test->getScenarioId().'(driver, ';
		 		 $line_call.=  $test->getTestCaseId().', "'.traiterAccents ($test->getTestcaseName()).'",'.$test->getScenarioId().',"'.traiterAccents ($test->getScenarioName()).'",'.$index_scenario.');';
		 	
		 		self::writeLine($id_testcaseFile, $line_call);
		 		self::writeLine($id_testcaseFile, "    	if (status == false) {");
		 		self::writeLine($id_testcaseFile, "			return status;");
		 		self::writeLine($id_testcaseFile, "		}");
	 		}
		 }
		
		 self::writeLine($id_testcaseFile, "			return status;");
		 self::writeLine($id_testcaseFile, "	}");
		 self::writeLine($id_testcaseFile, "	public void testPlanGo() {");
		 self::writeLine($id_testcaseFile, "		try {");
		 self::writeLine($id_testcaseFile, "		main(null);");
		 self::writeLine($id_testcaseFile, "		} catch (InterruptedException e) {");
		 self::writeLine($id_testcaseFile, "		e.printStackTrace();");
		 self::writeLine($id_testcaseFile, "		}");
		 self::writeLine($id_testcaseFile, "	}");
		 self::writeLine($id_testcaseFile, "	//FONCTION MAIN");
		 self::writeLine($id_testcaseFile, "	public static void main(String[] args) throws InterruptedException {");

		 self::writeLine($id_testcaseFile, '  		Fonctions.cleanTestcaseLog ("'.$testcase_name.'");'); 
		 self::writeLine($id_testcaseFile, "		boolean result = false;");		 
		 self::writeLine($id_testcaseFile, "		try {");
		 self::writeLine($id_testcaseFile, "			System.out.println (\"Démarrage du cas de test\");");  
		 self::writeLine($id_testcaseFile, '			int num_instances = Fonctions.get_lines_parameters("'.$testcase_name.'");');
		 self::writeLine($id_testcaseFile, "			Config.compteur_instance = 2;");
		 self::writeLine($id_testcaseFile, "			while (Config.compteur_instance<num_instances) {");	 
		 self::writeLine($id_testcaseFile, "				Config.compteur_params=1;");
		 self::writeLine($id_testcaseFile, '				Fonctions.createLogFile("'.$testcase_name.'");');
		 self::writeLine($id_testcaseFile, '				result = launch();'); 
		 self::writeLine($id_testcaseFile, "				Config.compteur_instance = Config.compteur_instance+1;");
		 self::writeLine($id_testcaseFile, "			}");
		 self::writeLine($id_testcaseFile, "			if (JunitLaunch) assertEquals (result, true);");
	 	 self::writeLine($id_testcaseFile, "			//Copy logs to history");
	 	 self::writeLine($id_testcaseFile, '  			Fonctions.histoTestcaseLog ("'.$testcase_name.'", "'.$timestamp.'");');
	 	 self::writeLine($id_testcaseFile, "			System.exit(0);");
		 self::writeLine($id_testcaseFile, "		} catch (Exception e) {");
	 	 self::writeLine($id_testcaseFile, '  		Fonctions.histoTestcaseLog ("'.$testcase_name.'", "'.$timestamp.'");');
	 	 self::writeLine($id_testcaseFile, "		e.printStackTrace();");
		 self::writeLine($id_testcaseFile, "		}");
		 self::writeLine($id_testcaseFile, "	}");
		 self::writeLine($id_testcaseFile, "}");
		 
	    fclose ($id_testcaseFile);
	    
	    //generate scenarios 
	    self::generateScenariosFile();
	  
	    self::generateComponentsFile('testcase');
	    
	     //find in conf if tech script must be generated
	     if ($exportConf->getHasTechScriptGen()) {
	     	self::generateTechScriptFile();
	  
		}
		
		//create bat file for command line execution
		if (strpos ($exportConf->getRawScriptsFolder(), '[user]')) {
			$bat_file_name =  traiterAccents($testcase_name).'.bat';
			$bat_file = 	$exportPath.'/../'.$bat_file_name;
		
		}
		else if ($exportConf->getUserFolder()!='') {
			$bat_file_name = $testcase_name.'_'.$exportConf->getUserFolder().'.bat';
			$bat_file = 	$exportPath.'/../../'.$bat_file_name;
		}
		else {
			$bat_file_name = $testcase_name.'.bat';
			$bat_file = 	$exportPath.'/../'.$bat_file_name;		
			
		}
		
		if (file_exists($bat_file)) {
			unlink($bat_file);
		}
		
		$id_batfile = fopen($bat_file,"a+") ;		
		self::writeLine($id_batfile, "@echo off");
		$testcase_str =  "scripts_metiers.".$testcase_class_name;
			
		if (strpos ($exportConf->getRawScriptsFolder(), '[user]')) {
			self::writeLine($id_batfile, "call ant -DUSER_DIR=. clean") ;
			self::writeLine($id_batfile, "call ant -DUSER_DIR=. build");
			self::writeLine($id_batfile, "call ant -DNOM_CLASS_MAIN=".$testcase_str." run");
		}
		else if ($exportConf->getUserFolder()!='') {
			self::writeLine($id_batfile, "call ant -DREP_BASE_EXPORT=EXPORT".$exportConf->getUserFolder()." -DUSER_DIR=".$exportConf->getUserFolder()." clean") ;
			self::writeLine($id_batfile, "call ant -DREP_BASE_EXPORT=EXPORT".$exportConf->getUserFolder()." -DUSER_DIR=".$exportConf->getUserFolder()." build");
			self::writeLine($id_batfile, "call ant -DREP_BASE_EXPORT=EXPORT".$exportConf->getUserFolder()." -DUSER_DIR=".$exportConf->getUserFolder()." -DNOM_CLASS_MAIN=".$testcase_str." run");
		
		}
		else {
			self::writeLine($id_batfile, "call ant -DUSER_DIR=. clean") ;
			self::writeLine($id_batfile, "call ant -DUSER_DIR=. build");
			self::writeLine($id_batfile, "call ant -DNOM_CLASS_MAIN=".$testcase_str." run");
				
		}
		fclose ($id_batfile);
	}
	
	
	
	/**
	 * If the the user has clicked on generate component
	 * {@inheritDoc}
	 * @see automatedTestsGenerator::generateComponent()*/

	public function generateComponent() {
		$timestamp = time();
		
		//get testcase infos
		$testcase 			   = ExportAutomatedTestsQuery::create()->findOne();
		$testcase_class_name   = ucfirst(traiterAccents ($testcase->getTestCaseName()));
		$testcase_name  	   =  traiterAccents ($testcase->getTestCaseName());
		
		//get config
		$exportConf = ExportConfQuery::create()->findOne();
		
		//get script export path
		$exportPath = $exportConf->getScriptsFolder();
		
		//delete old java files
		$exportPath_lect = opendir ($exportPath);
		while ($file_name = readdir($exportPath_lect)) {
			//echo
			if(strpos ($file_name, '.java')){
				$file = $exportPath.'/'.$file_name;
				unlink($file);
			}
		}
		
		//create main testcase java file with upper first class, and delete if already exists
		$testcaseFile = $exportPath.'/'.$testcase_class_name.'.java';
		if (file_exists($testcaseFile))	{
			unlink ($testcaseFile);
		}
		$id_testcaseFile = fopen ($testcaseFile, 'a+');
		$today = date("Y-m-d H:i:s", time());
		self::writeLine ($id_testcaseFile, "//kaliostest Testcase Export File");
		self::writeLine($id_testcaseFile, "//Export date : ".$today);
		self::writeLine($id_testcaseFile, '//Testcase '.$testcase_name);
		self::writeLine($id_testcaseFile, "package scripts_metiers;");
		 
		
		self::writeLine($id_testcaseFile, "import scripts_techniques.*;");
		//imports are fined in conf file
		self::writeLine($id_testcaseFile,  IMPORT_MAINFILE);
		self::writeLine($id_testcaseFile, 'public class '.$testcase_class_name.' extends TestCase {');
		self::writeLine($id_testcaseFile, "	//drivers declaration");
		self::writeLine($id_testcaseFile, "	public static WebDriver driverInstance;");
		self::writeLine($id_testcaseFile, "	public static boolean JunitLaunch;");
		self::writeLine($id_testcaseFile, "	public static WebDriver selfdriver;");
		
		
		self::writeLine($id_testcaseFile, '	public '.$testcase_class_name.'(String name) {');
		self::writeLine($id_testcaseFile, "		super(name);");
		self::writeLine($id_testcaseFile, "	}");
		self::writeLine($id_testcaseFile, "	//methode appelée uniquement en mode lancement Junit, au lancement");
		self::writeLine($id_testcaseFile, "	protected void setUp() throws Exception {");
		self::writeLine($id_testcaseFile, "		JunitLaunch=true;");
		self::writeLine($id_testcaseFile, "		super.setUp();");
		self::writeLine($id_testcaseFile, "	}");
		self::writeLine($id_testcaseFile, "	//methode appelée uniquement en mode lancement Junit, à la fermeture");
		self::writeLine($id_testcaseFile, "	protected void tearDown() throws Exception {");
		self::writeLine($id_testcaseFile, "		System.out.println (\"Passage teardown\");");
		self::writeLine($id_testcaseFile, "	}");
		self::writeLine($id_testcaseFile, "	//main method");
		self::writeLine($id_testcaseFile, "	public static boolean launch() throws Exception {");
		
		 
		//find browser and url in conf
		$array_browservalue = split (' = ', self::findStrInConf($exportConf, 'Navigateur'));
		$browser = $array_browservalue[1];
		 
		$array_urlvalue = split (' = ', self::findStrInConf($exportConf, 'URL'));
		$urlvalue= $array_urlvalue[1];
		 
		$array_osvalue = split (' = ', self::findStrInConf($exportConf, 'exploitation'));
	    $osvalue= $array_osvalue[1];
	  
	  	
	  	switch ($osvalue) {
	  		case 'Android' :
	  		self::writeLine($id_testcaseFile,'File app = new File ("'.$apk.'");
			if(!app.exists()) {
				throw new RuntimeException ("File not found : "+ app.getAbsolutePath());
			}');	break;
	  	}
	  	
	  		
	  			
      	 self::writeLine($id_testcaseFile, '		DesiredCapabilities capabilities = new DesiredCapabilities();
        //browser name comes from kaliostest
        capabilities.setCapability(MobileCapabilityType.DEVICE_NAME, deviceId);');
   
	    switch ($osvalue) {
			case 'Android' : 
			   self::writeLine($id_testcaseFile, '		capabilities.setCapability(MobileCapabilityType.PLATFORM_NAME, MobilePlatform.ANDROID);');
			   self::writeLine($id_testcaseFile, '		capabilities.setCapability(MobileCapabilityType.APP, app.getAbsolutePath());');
			   
			   break;
			case 'IOS': 
			   self::writeLine($id_testcaseFile, '		capabilities.setCapability(MobileCapabilityType.PLATFORM_NAME, MobilePlatform.IOS);');
			break;
			
		}
         self::writeLine($id_testcaseFile, '		capabilities.setCapability(MobileCapabilityType.PLATFORM_VERSION, deviceOsVersion);');
			 self::writeLine($id_testcaseFile, '		capabilities.setCapability(MobileCapabilityType.LAUNCH_TIMEOUT, Integer.parseInt (Config.window_attach_timeout));');
  
		//driver IOS or Android
	   switch ($osvalue) {
		case 'Android' : 
			
			 self::writeLine($id_testcaseFile, "		 AndroidDriver driver = new AndroidDriver(new URL(appium_url), capabilities);");
		break;
		
		case 'IOS': 
			 self::writeLine($id_testcaseFile, "		  IOSDriver driver = new IOSDriver(new URL(appium_url), capabilities);");	
		break;   	
	   }
	   
	   
	   self::writeLine($id_testcaseFile, '		'.$testcase_class_name.'.selfdriver = driver;');
	   
		 
		//call to scenarios
		self::writeLine($id_testcaseFile, "		//call scenarios");
		self::writeLine($id_testcaseFile, "		boolean status = true;");
		
		//find all different scenarios from export_automated_tests table
		$tests = ExportAutomatedTestsQuery::create()->orderById()->groupByScenarioId()->find();
		$index_scenario=0;
		foreach ($tests as $test) {
			++$index_scenario;
			if ($index_scenario>$index_start) {
				self::writeLine($id_testcaseFile, '		//call scenario '.$test->getScenarioName());
		
				$line_call = '';
				$line_call.='    	status = Scenarios.'.traiterAccents($test->getScenarioName()).'_'.$test->getScenarioId().'(selenium, ';
				$line_call.=  $test->getTestCaseId().', "'.traiterAccents ($test->getTestcaseName()).'",'.$test->getScenarioId().',"'.traiterAccents ($test->getScenarioName()).'",'.$index_scenario.');';
		
				self::writeLine($id_testcaseFile, $line_call);
				self::writeLine($id_testcaseFile, "    	if (status == false) {");
				self::writeLine($id_testcaseFile, "			return status;");
				self::writeLine($id_testcaseFile, "		}");
			}
		}
		
		self::writeLine($id_testcaseFile, "			return status;");
		self::writeLine($id_testcaseFile, "	}");
		self::writeLine($id_testcaseFile, "	public void testPlanGo() {");
		self::writeLine($id_testcaseFile, "		try {");
		self::writeLine($id_testcaseFile, "		main(null);");
		self::writeLine($id_testcaseFile, "		} catch (InterruptedException e) {");
		self::writeLine($id_testcaseFile, "		e.printStackTrace();");
		self::writeLine($id_testcaseFile, "		}");
		self::writeLine($id_testcaseFile, "	}");
		self::writeLine($id_testcaseFile, "	//FONCTION MAIN");
		self::writeLine($id_testcaseFile, "	public static void main(String[] args) throws InterruptedException {");
		
		self::writeLine($id_testcaseFile, '  		Fonctions.cleanTestcaseLog ("'.$testcase_name.'");');
		self::writeLine($id_testcaseFile, "		boolean result = false;");
		self::writeLine($id_testcaseFile, "		try {");
		self::writeLine($id_testcaseFile, "			System.out.println (\"Démarrage du cas de test\");");
		self::writeLine($id_testcaseFile, '			int num_instances = Fonctions.get_lines_parameters("'.$testcase_name.'");');
		self::writeLine($id_testcaseFile, "			Config.compteur_instance = 2;");
		self::writeLine($id_testcaseFile, "			while (Config.compteur_instance<num_instances) {");
		self::writeLine($id_testcaseFile, "				Config.compteur_params=1;");
		self::writeLine($id_testcaseFile, '				Fonctions.createLogFile("'.$testcase_name.'");');
		self::writeLine($id_testcaseFile, '				result = launch();');
		self::writeLine($id_testcaseFile, "				Config.compteur_instance = Config.compteur_instance+1;");
		self::writeLine($id_testcaseFile, "			}");
		self::writeLine($id_testcaseFile, "			if (JunitLaunch) ");
		self::writeLine($id_testcaseFile, "	assertEquals (result, true);");
		self::writeLine($id_testcaseFile, "			//Copy logs to history");
		self::writeLine($id_testcaseFile, '  			Fonctions.histoTestcaseLog ("'.$testcase_name.'", "'.$timestamp.'");');
		self::writeLine($id_testcaseFile, "			System.exit(0);");
		self::writeLine($id_testcaseFile, "		} catch (Exception e) {");
		self::writeLine($id_testcaseFile, '  		Fonctions.histoTestcaseLog ("'.$testcase_name.'", "'.$timestamp.'");');
		self::writeLine($id_testcaseFile, "		e.printStackTrace();");
		self::writeLine($id_testcaseFile, "		}");
		self::writeLine($id_testcaseFile, "	}");
		self::writeLine($id_testcaseFile, "}");
			
		fclose ($id_testcaseFile);
		 
		//generate scenarios
		self::generateScenariosFile();
		 
		self::generateComponentsFile('component');
		 
		//find in conf if tech script must be generated
		if ($exportConf->getHasTechScriptGen()) {
			self::generateTechScriptFile();
			 
		}
		 

			
		

	}



	/*Si l'utilisateur à cliqué sur Générerun plan de tests*/

	public function generateTestPlan() {

		
		//generate testplan file
		//get testcase infos
		$testplan 			   = ExportTestplanQuery::create()->findOne();
		$testplan_class_name   = ucfirst(traiterAccents ($testplan->getTestPlanName()));
		$testplan_name  	   =  traiterAccents ($testplan->getTestPlanName());
		$testplan_id			= $testplan->getTestPlanId();
		//get config
		$exportConf = ExportConfQuery::create()->findOne();
		
		//get script export path
		$exportPath = $exportConf->getScriptsFolder();
	
		//delete old java files
		$exportPath_lect = opendir ($exportPath);
		while ($file_name = readdir($exportPath_lect)) {
			//echo
			if(strpos ($file_name, '.java')){
				$file = $exportPath.'/'.$file_name;
				unlink($file);
			}
		}
		
		//create main testplan java file with upper first class, and delete if already exists
		$testplanFile = $exportPath.'/'.$testplan_class_name.'.java';
		if (file_exists($testplanFile))	{
			unlink ($testplanFile);
		}
		$id_testplanFile = fopen ($testplanFile, 'a+');
		
	
		$dir_results = 'planTests/'.$testplan_name.'___'.$testplan_id.'___'.$testplan->getTimeStampGeneration();
		
		$today = date("Y-m-d H:i:s", time());
		self::writeLine($id_testplanFile, "//kaliostest Testplan Export File");
		self::writeLine($id_testplanFile, "//Export date : ".$today);
		self::writeLine($id_testplanFile, "package scripts_metiers;");
		self::writeLine($id_testplanFile, IMPORT_TESTPLANFILE);
		self::writeLine($id_testplanFile, "import scripts_metiers.*;");
		self::writeLine($id_testplanFile, "import scripts_techniques.*;");
		self::writeLine($id_testplanFile, 'public class '.$testplan_class_name.'  {');
		self::writeLine($id_testplanFile, "public static Test suite() {");
		self::writeLine($id_testplanFile, '	Config.dir_export = Config.dir_export + "/'. $dir_results . '";');
		self::writeLine($id_testplanFile, '	TestSuite suite = new TestSuite("'. $testplan_class_name.'");');
		
		//find testcases for testplan
		$tests		   = ExportTestplanQuery::create()->find();
		foreach ($tests as $test) {
			self::writeLine($id_testplanFile, '//call test case  '.$test->getTestCaseName());
			self::writeLine($id_testplanFile, '	suite.addTestSuite('.ucfirst(traiterAccents($test->getTestCaseName())).'.class);');

		}
		
		self::writeLine($id_testplanFile, "	return suite;");
		self::writeLine($id_testplanFile, "}");
		//get timestamp generation from database 
		
		
		self::writeLine	($id_testplanFile, 'public static void  Runtestsuite(){ ');
		self::writeLine	($id_testplanFile, "TestSuite suite =  new TestSuite();");
		self::writeLine($id_testplanFile, "TestResult result = new TestResult();");
		self::writeLine($id_testplanFile, "suite.run(result);");
		self::writeLine($id_testplanFile, "}");
		self::writeLine($id_testplanFile, "public static void main(String[] args) throws InterruptedException {");
		self::writeLine($id_testplanFile, "	try {");
		self::writeLine($id_testplanFile, "		Runtestsuite();");
		self::writeLine($id_testplanFile, "	} catch (Exception e) {");
		self::writeLine($id_testplanFile, "		e.printStackTrace();");
		self::writeLine($id_testplanFile, "	}");
		self::writeLine($id_testplanFile, "}");
		self::writeLine($id_testplanFile, "}");
		fclose ($id_testplanFile);
		
		//generate testcases files
		
		reset($tests);
		foreach ($tests as $test) {
			//get testcase infos
	
			$testcase_class_name   = ucfirst(traiterAccents ($test->getTestCaseName()));
			$testcase_name  	   =  traiterAccents ($test->getTestCaseName());
			$testcase_id 			= $test->getTestCaseId();
			//get config
			$exportConf = ExportConfQuery::create()->findOne();
			
			//get script export path
			$exportPath = $exportConf->getScriptsFolder();
			
			//create main testcase java file with upper first class, and delete if already exists
			$testcaseFile = $exportPath.'/'.$testcase_class_name.'.java';
			if (file_exists($testcaseFile))	{
				unlink ($testcaseFile);
			}
			$id_testcaseFile = fopen ($testcaseFile, 'a+');
			$today = date("Y-m-d H:i:s", time());
			self::writeLine ($id_testcaseFile, "//kaliostest Testcase Export File");
			self::writeLine($id_testcaseFile, "//Export date : ".$today);
			self::writeLine($id_testcaseFile, '//Testcase '.$testcase_name);
			self::writeLine($id_testcaseFile, "package scripts_metiers;");
			 
			
			self::writeLine($id_testcaseFile, "import scripts_techniques.*;");
			//imports are fined in conf file
			self::writeLine($id_testcaseFile,  IMPORT_MAINFILE);
			self::writeLine($id_testcaseFile, 'public class '.$testcase_class_name.' extends TestCase {');
			self::writeLine($id_testcaseFile, "	//drivers declaration");
			self::writeLine($id_testcaseFile, "	public static WebDriver driverInstance;");
			self::writeLine($id_testcaseFile, "	public static boolean JunitLaunch;");
			self::writeLine($id_testcaseFile, "	public static WebDriver selfdriver;");
			
			
			self::writeLine($id_testcaseFile, '	public '.$testcase_class_name.'(String name) {');
			self::writeLine($id_testcaseFile, "		super(name);");
			self::writeLine($id_testcaseFile, "	}");
			self::writeLine($id_testcaseFile, "	//methode appelée uniquement en mode lancement Junit, au lancement");
			self::writeLine($id_testcaseFile, "	protected void setUp() throws Exception {");
			self::writeLine($id_testcaseFile, "		JunitLaunch=true;");
			self::writeLine($id_testcaseFile, "		super.setUp();");
			self::writeLine($id_testcaseFile, "	}");
			self::writeLine($id_testcaseFile, "	//methode appelée uniquement en mode lancement Junit, à la fermeture");
			self::writeLine($id_testcaseFile, "	protected void tearDown() throws Exception {");
			self::writeLine($id_testcaseFile, "		System.out.println (\"Passage teardown\");");
			self::writeLine($id_testcaseFile, "	}");
			self::writeLine($id_testcaseFile, "	//main method");
			self::writeLine($id_testcaseFile, "	public static boolean launch() throws Exception {");
			
			 
			   //find apk in conf
		    $array_apkvalue = split (' = ', self::findStrInConf($exportConf, 'apk'));
		    $apk = $array_apkvalue[1];
		  
			 	$array_osvalue = split (' = ', self::findStrInConf($exportConf, 'exploitation'));
			$osvalue= $array_osvalue[1];
		  
				
	  	switch ($osvalue) {
	  		case 'Android' :
	  		self::writeLine($id_testcaseFile,'File app = new File ("'.$apk.'");
			if(!app.exists()) {
				throw new RuntimeException ("File not found : "+ app.getAbsolutePath());
			}');	break;
	  	}
	  	
	  	self::writeLine($id_testcaseFile, '		final String appium_url = Config.appium_url;
        final String deviceId = Config.deviceId;
        final String deviceOsVersion = Config.deviceOsVersion;');
	  	
	  			
      	 self::writeLine($id_testcaseFile, '		DesiredCapabilities capabilities = new DesiredCapabilities();
        //browser name comes from kaliostest
        capabilities.setCapability(MobileCapabilityType.DEVICE_NAME, deviceId);');
   
	    switch ($osvalue) {
			case 'Android' : 
			   self::writeLine($id_testcaseFile, '		capabilities.setCapability(MobileCapabilityType.PLATFORM_NAME, MobilePlatform.ANDROID);');
			   self::writeLine($id_testcaseFile, '		capabilities.setCapability(MobileCapabilityType.APP, app.getAbsolutePath());');
			   
			   break;
			case 'IOS': 
			   self::writeLine($id_testcaseFile, '		capabilities.setCapability(MobileCapabilityType.PLATFORM_NAME, MobilePlatform.IOS);');
			break;
			
		}
         self::writeLine($id_testcaseFile, '		capabilities.setCapability(MobileCapabilityType.PLATFORM_VERSION, deviceOsVersion);');
		 self::writeLine($id_testcaseFile, '		capabilities.setCapability(MobileCapabilityType.LAUNCH_TIMEOUT, Integer.parseInt (Config.window_attach_timeout));');
  
		//driver IOS or Android
	   switch ($osvalue) {
		case 'Android' : 
			
			 self::writeLine($id_testcaseFile, "		 AndroidDriver driver = new AndroidDriver(new URL(appium_url), capabilities);");
		break;
		
		case 'IOS': 
			 self::writeLine($id_testcaseFile, "		  IOSDriver driver = new IOSDriver(new URL(appium_url), capabilities);");	
		break;   	
	   }
	   
	   
	   self::writeLine($id_testcaseFile, '		'.$testcase_class_name.'.selfdriver = driver;');
	   	    
			//call to scenarios
			self::writeLine($id_testcaseFile, "		//call scenarios");
			self::writeLine($id_testcaseFile, "		boolean status = true;");
			
			//find all different scenarios from export_automated_tests table
			$tests = ExportAutomatedTestsQuery::create()->filterByTestcaseId ($testcase_id)->orderById()->groupByScenarioId()->find();
		
			$index_scenario=0;
			foreach ($tests as $test) {
				++$index_scenario;
				if ($index_scenario>$index_start) {
					self::writeLine($id_testcaseFile, '		//call scenario '.$test->getScenarioName());
			
					$line_call = '';
					$line_call.='    	status = Scenarios.'.traiterAccents($test->getScenarioName()).'_'.$test->getScenarioId().'(driver, ';
					$line_call.=  $testcase_id.', "'.$testcase_name.'",'.$test->getScenarioId().',"'.traiterAccents ($test->getScenarioName()).'",'.$index_scenario.');';
			
					self::writeLine($id_testcaseFile, $line_call);
					self::writeLine($id_testcaseFile, "    	if (status == false) {");
					self::writeLine($id_testcaseFile, '		 driver.quit();');
				   
					self::writeLine($id_testcaseFile, "			return status;");
					self::writeLine($id_testcaseFile, "		}");
				}
			}
			
			self::writeLine($id_testcaseFile, "			return status;");
			self::writeLine($id_testcaseFile, "	}");
			self::writeLine($id_testcaseFile, "	public void testPlanGo() {");
			self::writeLine($id_testcaseFile, "		try {");
			self::writeLine($id_testcaseFile, "		main(null);");
			self::writeLine($id_testcaseFile, "		} catch (InterruptedException e) {");
			self::writeLine($id_testcaseFile, "		e.printStackTrace();");
			self::writeLine($id_testcaseFile, "		}");
			self::writeLine($id_testcaseFile, "	}");
			self::writeLine($id_testcaseFile, "	//FONCTION MAIN");
			self::writeLine($id_testcaseFile, "	public static void main(String[] args) throws InterruptedException {");
			
			self::writeLine($id_testcaseFile, '  		Fonctions.cleanTestcaseLog ("'.$testcase_name.'");');
			self::writeLine($id_testcaseFile, "		boolean result = false;");
			self::writeLine($id_testcaseFile, "		try {");
			self::writeLine($id_testcaseFile, "			System.out.println (\"Démarrage du cas de test\");");
			self::writeLine($id_testcaseFile, '			int num_instances = Fonctions.get_lines_parameters("'.$testcase_name.'");');
			self::writeLine($id_testcaseFile, "			Config.compteur_instance = 2;");
			self::writeLine($id_testcaseFile, "			while (Config.compteur_instance<num_instances) {");
			self::writeLine($id_testcaseFile, "				Config.compteur_params=1;");
			self::writeLine($id_testcaseFile, '				Fonctions.createLogFile("'.$testcase_name.'");');
			self::writeLine($id_testcaseFile, '				result = launch();');
			
			//testplan generation : if result ko, launch stoprestart procedure
			self::writeLine($id_testcaseFile, '				if (result==false) {');
			self::writeLine($id_testcaseFile, '					Fonctions.stopRestart('.$testcase_class_name .'.selfdriver);');
			self::writeLine($id_testcaseFile, "				}");
			self::writeLine($id_testcaseFile, "				Config.compteur_instance = Config.compteur_instance+1;");
			self::writeLine($id_testcaseFile, "			}");
			self::writeLine($id_testcaseFile, "			if (JunitLaunch) ");
			self::writeLine($id_testcaseFile, "		assertEquals (result, true);");
			self::writeLine($id_testcaseFile,  '     '.$testcase_class_name.'.selfdriver.quit();');
			self::writeLine($id_testcaseFile, "		} catch (Exception e) {");
			self::writeLine($id_testcaseFile, "		e.printStackTrace();");
			self::writeLine($id_testcaseFile, "		}");
			self::writeLine($id_testcaseFile, "	}");
			self::writeLine($id_testcaseFile, "}");
				
			fclose ($id_testcaseFile);		
		}
		
		//generate scenarios
		self::generateScenariosFile();
		 
		
		//generate components
		self::generateComponentsFile('testplan');
		 
		//find in conf if tech script must be generated
		if ($exportConf->getHasTechScriptGen()) {
			self::generateTechScriptFile();
			 
		}
		
	
		//create bat file for command line execution
		if (strpos ($exportConf->getRawScriptsFolder(), '[user]')) {
			$bat_file_name =  traiterAccents($testplan_name).'.bat';
			$bat_file = 	$exportPath.'/../'.$bat_file_name;
		
		}
		else if ($exportConf->getUserFolder()!='') {
			$bat_file_name = $testplan_name.'_'.$exportConf->getUserFolder().'.bat';
			$bat_file = 	$exportPath.'/../../'.$bat_file_name;
		}
		else {
			$bat_file_name = $testplan_name.'.bat';
			$bat_file = 	$exportPath.'/../'.$bat_file_name;
		}
			
		if (file_exists($bat_file)) {
			unlink($bat_file);
		}
		
		
		
		$id_batfile = fopen($bat_file,"a+") ;
		self::writeLine($id_batfile, "@echo off");
		$testplan_str =  "scripts_metiers.".$testplan_class_name;
	
			
		if (strpos ($exportConf->getRawScriptsFolder(), '[user]')) {
			self::writeLine($id_batfile, "call ant -DUSER_DIR=. clean") ;
			self::writeLine($id_batfile, "call ant -DUSER_DIR=. build");
			self::writeLine($id_batfile, "call ant -DNOM_CLASS_MAIN=".$testplan_str." run");
		}
		else if ($exportConf->getUserFolder()!='') {
			self::writeLine($id_batfile, "call ant -DREP_BASE_EXPORT=EXPORT".$exportConf->getUserFolder()." -DUSER_DIR=".$exportConf->getUserFolder()." clean") ;
			self::writeLine($id_batfile, "call ant -DREP_BASE_EXPORT=EXPORT".$exportConf->getUserFolder()." -DUSER_DIR=".$exportConf->getUserFolder()." build");
			self::writeLine($id_batfile, "call ant -DREP_BASE_EXPORT=EXPORT".$exportConf->getUserFolder()." -DUSER_DIR=".$exportConf->getUserFolder()." -DNOM_CLASS_MAIN=".$testplan_str." run");
		
		}
		else {
			self::writeLine($id_batfile, "call ant -DUSER_DIR=. clean") ;
			self::writeLine($id_batfile, "call ant -DUSER_DIR=. build");
			self::writeLine($id_batfile, "call ant -DNOM_CLASS_MAIN=".$testplan_str." run");
		
		}
		fclose ($id_batfile);
		
		
		
		
		
	}
	
	
	
	
	/**
	 * generate scenarios file
	 */

	public function generateScenariosFile() {
		
		//get config
		$exportConf = ExportConfQuery::create()->findOne();
		
		//get script export path
		$exportPath = $exportConf->getScriptsFolder();
		
		$tests = ExportAutomatedTestsQuery::create()->orderById()->groupByScenarioId()->find();
		
		
		//create scenarios file (delete if already exists)
		$scenariosFile = $exportPath.'/Scenarios.java';
		if (file_exists($scenariosFile))	{
			unlink ($scenariosFile);
		}
		$id_scenariosFile = fopen ($scenariosFile, 'a+');
		$today = date("Y-m-d H:i:s", time());
		self::writeLine ($id_scenariosFile, "//kaliostest Scenarios Export File");
		self::writeLine($id_scenariosFile, "//Export date : ".$today);
		self::writeLine($id_scenariosFile, "package scripts_metiers;");
		//imports are fined in conf file
		self::writeLine( $id_scenariosFile, IMPORT_SCENARIOSFILE);
		self::writeLine( $id_scenariosFile, "public class Scenarios {");
		
		 
		foreach ($tests as $test) {
			self::writeLine($id_scenariosFile, 'public static boolean '.traiterAccents ($test->getScenarioName()).'_'.$test->getScenarioId().' (WebDriver selenium, int testcase_id, String testcase_label, int scenario_id, String scenario_label, int scenario_index) throws Exception {');
			self::writeLine($id_scenariosFile, 'boolean status = true;');
		
			//find scenario components
			$testscomps = ExportAutomatedTestsQuery::create()->orderById()->filterByScenarioId($test->getScenarioId())->groupByComponentId()->find();
			foreach ($testscomps as $testscomp) {
				++$index_module;
				self::writeLine($id_scenariosFile, '    //Module Call '.$testscomp->getComponentName());
				$line ='';
				$line.='    status = Modules.'.traiterAccents ($testscomp->getComponentName()).'_'.$testscomp->getComponentId().'(selenium, ';
				$line.='testcase_id,testcase_label,';
				$line.='scenario_id,scenario_label, scenario_index, ';
				$line.=$testscomp->getComponentId().',"'.traiterAccents ($testscomp->getComponentName()).'",'.$index_module.');';
				self::writeLine($id_scenariosFile, $line);
				self::writeLine($id_scenariosFile, "if (!status) return false;");
			}
			self::writeLine($id_scenariosFile, "return status;");
			self::writeLine($id_scenariosFile, "}");
		}
		 
		//end of scenarios file
		self::writeLine($id_scenariosFile, "}");
		fclose ($id_scenariosFile);

		
	}
	
	
	
	
	/**
	 * generate components file
	 */

	public function generateComponentsFile($source) {
	
		
		//info for kaliostest log file
		switch ($source) {
			case 'testcase' : 
				$perspect=3;
				$structype=4;
				break;
			case 'testplan' : 
				$perspect=0;
				$structype=5;
				break;
			case 'component' : 
				$perspect=1;
				$structype=2;
				break;
		}
		
		//get config
		$exportConf = ExportConfQuery::create()->findOne();
		
		//get script export path
		$exportPath = $exportConf->getScriptsFolder();
		
		//create components file (delete if already exists)
		$componentsFile = $exportPath.'/Modules.java';
		if (file_exists($componentsFile))	{
			unlink ($componentsFile);
		}
		$id_componentsFile = fopen ($componentsFile, 'a+');
		$today = date("Y-m-d H:i:s", time());
		self::writeLine ($id_componentsFile, "//kaliostest Components Export File");
		self::writeLine($id_componentsFile, "//Export date : ".$today);
		
		self::writeLine($id_componentsFile, "package scripts_metiers;");
		
		//imports are fined in conf file
		self::writeLine($id_componentsFile, IMPORT_COMPONENTSFILE);
		self::writeLine($id_componentsFile, "import scripts_techniques.*;");
		self::writeLine($id_componentsFile, "public class Modules {");
		$testscomps = ExportAutomatedTestsQuery::create()->orderById()->groupByComponentId()->find();
		
		
		foreach ($testscomps as $testscomp) {
			
			//component function declaration
			self::writeLine($id_componentsFile, 'public static boolean '.traiterAccents ($testscomp->getComponentName()).'_'.$testscomp->getComponentId().' (WebDriver selenium, int testcase_id, String testcase_label, int scenario_id, String scenario_label, int scenario_index,int module_id, String module_label, int module_index) throws IOException {');
			self::writeLine($id_componentsFile, "	boolean status = true;");
			self::writeLine($id_componentsFile, '	String file_params = Config.dir_params +  File.separator + testcase_label + ".csv";');
			//find module steps
			$testscompsteps = ExportAutomatedTestsQuery::create()->orderById()->filterByComponentId($testscomp->getComponentId())->groupByStepIndex()->find();
			$index=0;
			foreach ($testscompsteps as $step) {
				++$index;
				
				self::writeLine($id_componentsFile, '    //testStep Attributes');
				
				$ligne = '    Teststep teststep_'.$index.' = new Teststep('.$perspect.','.$structype.','.$step->getProjectId().',"'.traiterAccents ($step->getProjectName()).'",';
				$ligne.= 'testcase_id,testcase_label,';
				$ligne.= 'scenario_id,scenario_label,scenario_index,';
				$ligne.= 'module_id,module_label, module_index,';
				$ligne.= $index.',';
				$ligne.= $step->getScreenId().',"'.$step->getScreenName().'","'.$step->getTechScreenName().'",';
				$ligne.= $step->getObjectId().',"'.$step->getObjectName().'","'.$step->getTechObjectName().'",';
				$ligne.= '"'.$step->getObjTypeName().'","'.$step->getActionName().'","'.$step->getActionName().'"';
				$ligne.= ');';
				self::writeLine($id_componentsFile, $ligne);
		
				self::writeLine($id_componentsFile, '    //param call');
				self::writeLine($id_componentsFile, ' 	teststep_'.$index.'.param  = Fonctions.getParameter(file_params,Config.compteur_instance, Config.compteur_params);');
				 
				//action parametrable?
				$exportAction= ExportActionQuery::create()->filterByActionId($step->getActionId())->findOne();
				if ($exportAction->getActionIsParametrable()) {
					if (BOOL_DONOTOPTIMIZE_ACTIONS) {
		
						self::writeLine($this->id_modulesFile, '   	 //exec script call');
						$ligne_callscript = '		status = Scripts_techniques.'.$exportAction->getActionScript().'(selenium, teststep_'.$index.');';
						self::writeLine($this->id_modulesFile, $ligne_callscript);
					}
					else {
						self::writeLine ($id_componentsFile, '	if (!teststep_'.$index.'.param.equals("")) {');
						self::writeLine($id_componentsFile, '   	 //exec script call');
						$ligne_callscript = '		status = Scripts_techniques.'.$exportAction->getActionScript().'(selenium, teststep_'.$index.');';
						self::writeLine($id_componentsFile, $ligne_callscript);
						self::writeLine($id_componentsFile, "	}");
					}
				}
				else {
					self::writeLine($id_componentsFile, '   	 //exec script call');
					$ligne_callscript = '		status = Scripts_techniques.'.$exportAction->getActionScript().'(selenium, teststep_'.$index.');';
					self::writeLine($id_componentsFile, $ligne_callscript);
				}
				self::writeLine($id_componentsFile, '    //parametrer count increase');
				$ligne_incrcompteur = ' 	Config.compteur_params    = Config.compteur_params +1;';
				self::writeLine($id_componentsFile, $ligne_incrcompteur);
				self::writeLine($id_componentsFile, "	if (!status)  return false;");
			}
			self::writeLine($id_componentsFile, "	return true;");
			self::writeLine($id_componentsFile, "}");
		}
		self::writeLine($id_componentsFile, "}");
		 
		fclose ($id_componentsFile);
	
	}
	
	
	
	
	/**
	 * utility function to write a line with break
	 * @param int $id_file
	 * @param String $line
	 */
	
	public function writeLine($id_file, $line) {
		fwrite($id_file, $line);
		fwrite($id_file, "\r\n");
	}


	
	
	/**
	 * utility function to find a string the conf (useful for browser or URL)
	 * @param ExportConf $exportConf
	 * @param unknown $str
	 */
	
	public function findStrInConf (ExportConf $exportConf, $str) {
	
		for ($i=0; $i < 15; $i++) {
			$res = $exportConf->getByPosition($i);
			if (strpos($res, $str)!==false) {
					return $res;
			}
		}
		return '';
	}
	
	
	

	/**
	 * return string between 2 strings
	 * @param unknown $string
	 * @param unknown $start
	 * @param unknown $end
	 * @return string
	 */
	
	function get_string_between($string, $start, $end){
		$string = ' ' . $string;
		$ini = strpos($string, $start);
		if ($ini == 0) return '';
		$ini += strlen($start);
		$len = strpos($string, $end, $ini) - $ini;
		return substr($string, $ini, $len);
	}
	
	
/**
 * 
 * @param ExportConf $exportConf
 */
	function generateTechScriptFile() {
		
		//get config
		$exportConf = ExportConfQuery::create()->findOne();
		
		$techPath = $exportConf->getTechScriptsFolder();
		$techFile = $techPath.'/Scripts_techniques.java';
		 
		//handle actions to code
		$file_contents =  file_get_contents($techFile);
		
		 
		//get actions to code
		//if action to code is in tech file, don't delete it : memorize code and regenerate file with old code for these actions
		$testactions_tocode = ExportActionQuery::create()->filterByActionToCode(true)->find();
		$actions_tocodemem_ids = array();
		$actions_tocodemem = array();
		foreach ($testactions_tocode as $action_tocode) {
		
			//action already generated once
			$script_name=   $action_tocode->getActionScript();
			$script_code = get_string_between ($file_contents, '//Action '.$script_name, '//Fin action '.$script_name);
			if ($script_code!='') {
				$actions_tocodemem_ids[] = $action_tocode->getActionId();
				$actions_tocodemem[$action_tocode->getActionId()] ='//Action '.$script_name.$script_code.'//Fin action '.$script_name."\r\n";
			}
		}
		
		
		if (file_exists ($techFile)) unlink ($techFile);
		$str_file = '';
		$str_file.="\r\n";
		$str_file.='package scripts_techniques;';
		$str_file.="\r\n";
		$str_file.= IMPORT_TECHFILE;
		$str_file.="\r\n";
		$str_file.='public class Scripts_techniques {';
		$str_file.="\r\n";
		 
		//get all actions
		$testactions = ExportActionQuery::create()->find();
		foreach ($testactions as $testaction) {
			//if action is to code, write old code (from previous techfile)
			if (in_array ($testaction->getActionId(), $actions_tocodemem_ids)) {
				$str_file.=$actions_tocodemem[$testaction->getActionId()];
			}
			//if not, write code from table column
			else {
				//	echo 'action non a coder : '.$a->getScript();
				//	echo ('<br>');
				$str_file.= $testaction->getActionScriptCode();
			}
			$str_file.="\r\n";
		}
		$str_file.="\r\n";
		$str_file .='}';
		$id_techFile = fopen($techFile, "a+");
		fputs ($id_techFile, $str_file);
		fclose ($id_techFile);
	}
	

	/**
	 * 
	 * @param unknown $libelle
	 * @return mixed
	 */
	
	
function traiterAccents($libelle) {
		$libelle = str_replace(' ', '_', $libelle);
		$libelle = ereg_replace('�', '_', $libelle);
		$libelle = ereg_replace('�', 'e', $libelle);
		$libelle = ereg_replace('�', 'e', $libelle);
		$libelle = ereg_replace('�', 'a', $libelle);
		$libelle = ereg_replace('�', 'c', $libelle);
		$libelle = ereg_replace('�', 'e', $libelle);
		$libelle = ereg_replace('-', '_', $libelle);
		$libelle = str_replace('+', 'plus', $libelle);
		$libelle = str_replace('(', '_', $libelle);
		$libelle = str_replace(')', '_', $libelle);
		$libelle = str_replace(',', '_', $libelle);
		$libelle = str_replace('.', '_', $libelle);
		$libelle = str_replace(':', '_', $libelle);
		$libelle = str_replace('�', '_', $libelle);
		$libelle = str_replace('=', '_', $libelle);
		$libelle = str_replace('&', '_', $libelle);
		$libelle = str_replace('[', '_', $libelle);
		$libelle = str_replace(']', '_', $libelle);
		$libelle = str_replace('<', '_', $libelle);
		$libelle = str_replace('>', '_', $libelle);
		$libelle = str_replace('/', '_', $libelle);
		$libelle = str_replace('\\', '_', $libelle);
		$libelle = str_replace('"', '_', $libelle);
		return $libelle;
	}
}


?>