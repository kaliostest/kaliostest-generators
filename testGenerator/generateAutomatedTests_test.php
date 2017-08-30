<?php
require 'int/automatedTestsGenerator.php';
require_once 'Propel.php';
		
		

/*Add your imports here*/

class generateAutomatedTests_test implements automatedTestsGenerator {


	/* Si l'utilisateur à cliqué sur Générer les tests manuels d'1
	cas de test
	*/

	public function generateTestCase () {

		Propel::init('./../model/build/conf/synopsis-conf.php');
		$con = Propel::getConnection(LinkPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		$sql = 'SELECT * from Export_testplan';
			
		try {
			$stmt = $con->prepare($sql);
			$stmt->execute();
		} catch (Exception $e) {
			if ($stmt) $stmt = null; // close
			Propel::log($e->getMessage(), Propel::LOG_ERR);
			throw new PropelException($e);
		}

		while ($row = $stmt->fetch()) {
		
			/* add your code here */

		}
	
			
			$sql = 'SELECT * from Export_automated_tests';
			
		try {
			$stmt = $con->prepare($sql);
			$stmt->execute();
		} catch (Exception $e) {
			if ($stmt) $stmt = null; // close
			Propel::log($e->getMessage(), Propel::LOG_ERR);
			throw new PropelException($e);
		}

		while ($row = $stmt->fetch()) {
		
			/* add your code here */

		}
			
		$sql = 'SELECT * from Export_actions';
		try {
			$stmt = $con->prepare($sql);
			$stmt->execute();
		} catch (Exception $e) {
			if ($stmt) $stmt = null; // close
			Propel::log($e->getMessage(), Propel::LOG_ERR);
			throw new PropelException($e);
		}
		while ($row = $stmt->fetch()) {
		
			/* add your code here */

		}
			
		$sql = 'SELECT * from Export_configuration';
		try {
			$stmt = $con->prepare($sql);
			$stmt->execute();
		} catch (Exception $e) {
			if ($stmt) $stmt = null; // close
			Propel::log($e->getMessage(), Propel::LOG_ERR);
			throw new PropelException($e);
		}
		while ($row = $stmt->fetch()) {
		
			/* add your code here */

		}
			
			
	

	}


	/*
	Si l'utilisateur à cliqué sur Générer un composant*/

	public function generateComponent() {

		
	Propel::init('./../model/build/conf/synopsis-conf.php');
	$con = Propel::getConnection(LinkPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
			$sql = 'SELECT * from Export_testplan';
			
		try {
			$stmt = $con->prepare($sql);
			$stmt->execute();
		} catch (Exception $e) {
			if ($stmt) $stmt = null; // close
			Propel::log($e->getMessage(), Propel::LOG_ERR);
			throw new PropelException($e);
		}

		while ($row = $stmt->fetch()) {
		
			/* add your code here */

		}
	
			
			$sql = 'SELECT * from Export_automated_tests';
			
		try {
			$stmt = $con->prepare($sql);
			$stmt->execute();
		} catch (Exception $e) {
			if ($stmt) $stmt = null; // close
			Propel::log($e->getMessage(), Propel::LOG_ERR);
			throw new PropelException($e);
		}

		while ($row = $stmt->fetch()) {
		
			/* add your code here */

		}
			
		$sql = 'SELECT * from Export_actions';
		try {
			$stmt = $con->prepare($sql);
			$stmt->execute();
		} catch (Exception $e) {
			if ($stmt) $stmt = null; // close
			Propel::log($e->getMessage(), Propel::LOG_ERR);
			throw new PropelException($e);
		}
		while ($row = $stmt->fetch()) {
		
			/* add your code here */

		}
			
		$sql = 'SELECT * from Export_configuration';
		try {
			$stmt = $con->prepare($sql);
			$stmt->execute();
		} catch (Exception $e) {
			if ($stmt) $stmt = null; // close
			Propel::log($e->getMessage(), Propel::LOG_ERR);
			throw new PropelException($e);
		}
		while ($row = $stmt->fetch()) {
		
			/* add your code here */

		}
			
		

	}



	/*Si l'utilisateur à cliqué sur Générerun plan de tests*/

	public function generateTestPlan() {

	Propel::init('./../model/build/conf/synopsis-conf.php');
	$con = Propel::getConnection(LinkPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
				$sql = 'SELECT * from Export_testplan';
			
		try {
			$stmt = $con->prepare($sql);
			$stmt->execute();
		} catch (Exception $e) {
			if ($stmt) $stmt = null; // close
			Propel::log($e->getMessage(), Propel::LOG_ERR);
			throw new PropelException($e);
		}

		while ($row = $stmt->fetch()) {
		
			/* add your code here */

		}
	
			
			$sql = 'SELECT * from Export_automated_tests';
			
		try {
			$stmt = $con->prepare($sql);
			$stmt->execute();
		} catch (Exception $e) {
			if ($stmt) $stmt = null; // close
			Propel::log($e->getMessage(), Propel::LOG_ERR);
			throw new PropelException($e);
		}

		while ($row = $stmt->fetch()) {
		
			/* add your code here */

		}
			
		$sql = 'SELECT * from Export_actions';
		try {
			$stmt = $con->prepare($sql);
			$stmt->execute();
		} catch (Exception $e) {
			if ($stmt) $stmt = null; // close
			Propel::log($e->getMessage(), Propel::LOG_ERR);
			throw new PropelException($e);
		}
		while ($row = $stmt->fetch()) {
		
			/* add your code here */

		}
			
		$sql = 'SELECT * from Export_configuration';
		try {
			$stmt = $con->prepare($sql);
			$stmt->execute();
		} catch (Exception $e) {
			if ($stmt) $stmt = null; // close
			Propel::log($e->getMessage(), Propel::LOG_ERR);
			throw new PropelException($e);
		}
		while ($row = $stmt->fetch()) {
		
			/* add your code here */

		}
		
	}


}


?>