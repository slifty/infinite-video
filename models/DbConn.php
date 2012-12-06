<?php
###
# Info:
#  Last Updated 2011
#  Daniel Schultz
#
# Comments:
###
include_once("config.php");
class DbConn {
	
	# Constants
	// Clean types
	const CLEAN_INPUT = "input";
	const CLEAN_VALIDATION = "validation";
	
	
	# Static Variables
	private static $dbConnection = null;
	
	
	# Static Methods
	public static function connect() {
		global $MYSQL_HOST, $MYSQL_USER, $MYSQL_PASS, $MYSQL_DB;
		
		// If a connection exists, return it
		if(DbConn::$dbConnection != null)
			return DbConn::$dbConnection;
		
		// Create a connection
		DbConn::$dbConnection = new mysqli($MYSQL_HOST,$MYSQL_USER,$MYSQL_PASS,$MYSQL_DB);
		DbConn::$dbConnection->query("SET CHARACTER SET 'utf8'") or print($mysqli->error); 
		
		return DbConn::$dbConnection;
	}
	
	public static function clean($data, $cleanType = DbConn::CLEAN_INPUT) {
		if(is_string($data)) {
			$data = ltrim($data);
			$data = rtrim($data);
			
			// Get rid of backslashes
			$data = str_replace('\\','\\\\',$data);
			
			// Damn pesky carriage returns...
			$data = str_replace("\r\n", "\n", $data);
			$data = str_replace("\r", "\n", $data);
			
			// Json requires some characters be escaped
			$data = str_replace("\n", "\\n", $data);
			$data = str_replace("\t", "\\t", $data);
			$data = str_replace('"','\\"',$data);
			
			if($cleanType == DbConn::CLEAN_VALIDATION)
				return $data;
			else
				return '"'.$data.'"';
		} elseif (is_array($data)) {
			foreach($data as $key => $val)
				$data[$key] = DbConn::clean($val, $cleanType);
			return $data;
		} else {
			return (int)$data;
		}
	}
}
?>