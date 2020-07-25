<?php

 // SQLImporter
 // Version 1.1
 // V1.0 Author: Rubén Crespo Álvarez - rumailster@gmail.com
 // Updated by: Yannick Luescher, hivemail.com

 /******************************************************************************************
 * Possibility to select dbase when creating an object instance:
 * -------------------------------------------------------------
 * $db = new sqlImport('dump.sql', false, 'localhost', 'testuser', 'testpass', 'testdbase');
 * $db->import();
 * if ($db->error) exit($db->error);
 * else echo "<b>Data written successfully</b>";
 * -------------------------------------------------------------
 * Now working with both /r/n resp. /n line endings (to make it work with /r see php.net)
 * Now working when using ; inside SQL statements
 * Check parameter added to output what would be written into dbase.
 * If host isn't set the active connection will be used (if any) as always.
 /******************************************************************************************/

 class sqlImport  extends engine{

 	// param $check bool: echo the sql statements instead of writing them into dbase

 	// Constructor
 	function __construct($SqlArchive, $check = false, $database = false) {
 		$this->database = $database;
 		$this->SqlArchive = $SqlArchive;
 		$this->check = $check;
 	}


 	// Import Data
 	function import() {
$CONN = self::db();

 			// To avoid problems we're reading line by line ...
 			$lines = file($this->SqlArchive, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
 			$buffer = '';
 			foreach ($lines as $line) {
 				// Skip lines containing EOL only
 				if (($line = trim($line)) == '')
 					continue;

 				// skipping SQL comments
 				if (substr(ltrim($line), 0, 2) == '--')
 					continue;

 				// An SQL statement could span over multiple lines ...
 				if (substr($line, -1) != ';') {
 					// Add to buffer
 					$buffer .= $line;
 					// Next line
 					continue;
 				} else
 					if ($buffer) {
 						$line = $buffer . $line;
 						// Ok, reset the buffer
 						$buffer = '';
 					}

 				// strip the trailing ;
 				$line = substr($line, 0, -1);

 				// Write the data
 					$result = $CONN->prepare($line);
                    $result->execute(array());

 			}
 		
 	}
 }

?>