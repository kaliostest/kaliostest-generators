<?php
/**
 * Interface scriptGenerator
 * All new test generator files implement this interface
 * @author Simon PICCIOTTO (KALIOS)
 */

 
interface automatedTestsGenerator {
	
	
	
		/**
		 * 
		 * Generate only testcase file
		 */
		function generateTestCase ();
		
		
		/**
		 * 
		 * Generate component
		 * 
		 */
		function generateComponent();
		
	
		/**
		 * 
		 * Generate testplan
		 */
		function generateTestPlan ();
		
		
	
}

?>