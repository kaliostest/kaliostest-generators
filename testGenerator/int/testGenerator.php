<?php
/**
 * Interface scriptGenerator
 * All new test generator files implement this interface
 * @author Simon PICCIOTTO (KALIOS)
 */

interface TestGenerator {
	
	
	
		/**
		 * 
		 * Generate only testcase file
		 */
		function generateTestCase ($array_lang);
		
		
		/**
		 * 
		 * Generate all testcases files
		 * 
		 */
		function generateAllTestcases($array_lang);
		
	
		/**
		 * 
		 * Generate testcases generation report file
		 */
		function generateTestcasesGenerationReport ($array_lang);
		
		
	
}

?>