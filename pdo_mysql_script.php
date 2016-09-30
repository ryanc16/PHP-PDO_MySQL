<?php
//////////////////////////////////////////////////////////////////////////////////////////////
/*
*	Author: 			Ryan Conklin
*	Date:				9/14/2016
*	Email:				rwc4@pct.edu
*	Repository:			https://github.com/ryanc16/PHP-PDO_MySQL
*	Original filename:	pdo_mysql_script.php
*	Version: 			1.0.1
*	Description: This is a PDO based, easy to use, nearly drop-in replacement for mysqli_* 
*	functions that allows for a more secure interaction with a MySQL database in a website
*	or web application that runs PHP. This will allow anyone to develop applications
*	that utilize a MySQL database to have access to prepared statements and the accompanying
*	functionality that is needed in order to build their application with a
*	security-oriented approach.
*	Note: This is a procedural style implementation of this library and its contained functions.
*/
//////////////////////////////////////////////////////////////////////////////////////////////
	$USER_SET_ERR_MODE = false;	
	$ERR_MODE=null;
	$ERR_MODES = 
	[
		"SILENT"=>PDO::ERRMODE_SILENT,
		"WARNING"=>PDO::ERRMODE_WARNING,
		"EXCEPTION"=>PDO::ERRMODE_EXCEPTION,
		0=>PDO::ERRMODE_SILENT,
		1=>PDO::ERRMODE_WARNING,
		2=>PDO::ERRMODE_EXCEPTION
	];
	$TESTING=false;//set to false as fail-safe;
	$DB=null;
	$HOSTNAME="";
	$USERNAME="";
	$PASSWORD="";
	$DATABASE="";
/*
///////////////
// IMPORTANT //
///////////////
Use this for setting wether this is being used in a testing environment
or a live environment.
If testing is set to true, warning and error messages will be shown in the browser.
Otherwise, when set to false, as would be desired of a live environment, no warning or error messages will be shown.
$istesting = true for testing or false for live environment
*/
	function PDO_set_testing($istesting){
		global $TESTING;
		$TESTING = $istesting;
	}
/*
Manually overrides the the error mode to one of the predefined error modes.
//// NOTE /////
Will do this regardless if testing has been set to true or false for testing or 
live environment. This means the user will have to try/catch their own exceptions!
Returns true if a valid mode is used and throws exception if previous error mode is not silent.
If a database connection is already present, it will set the connection to the new mode immediately.
Otherwise it will set it upon first successful connection.
$mode = an int or string representation of the error mode to be used from
the $ERR_MODES array. Valid values are "SILENT" or 0, "WARNING" or 1, "EXCEPTION" or 2.
*/
	function PDO_set_err_mode($mode){
		try{
			global $DB, $ERR_MODE, $ERR_MODES, $USER_SET_ERR_MODE;
			$mode = strtoupper($mode);
		if(array_key_exists($mode,$ERR_MODES)){
			$ERR_MODE = $ERR_MODES[$mode];
			if(is_connected())
				$DB->setAttribute(PDO::ATTR_ERRMODE, $ERR_MODE);
			$USER_SET_ERR_MODE = true;
			return true;
		}
		else{ throw new Exception("Unknown err_mode given.");}
		}
		catch(Exception $e){PDO_handle_exception($e);}
		return false;
	}
/*
Resumes normal error mode determination, displaying, and exception throwing.
*/
	function PDO_set_err_mode_default(){
		global $USER_SET_ERR_MODE;
		$USER_SET_ERR_MODE = false;
		reconnect();
	}
/*
Use this for creating your connection.
Returns true if connection is successful and false if it is not.
$host = url/address of database server.
$user = username of database user.
$pass = password for the user.
$database = name of the database.
*/
	function PDO_connect($host,$user,$pass,$database){
		global $DB, $HOSTNAME, $USERNAME, $PASSWORD, $DATABASE, $TESTING, $ERR_MODES, $ERR_MODE;
		if(isset($DB)) unset($DB);
		$HOSTNAME = $host;
		$USERNAME = $user;
		$PASSWORD = $pass;
		$DATABASE = $database;
		if($TESTING){
			if($ERR_MODE == null)
				$ERR_MODE = $ERR_MODES['EXCEPTION'];
		}
		else
			$ERR_MODE = $ERR_MODES['SILENT'];
		try{
			$DB = new PDO('mysql:host='.$HOSTNAME.';dbname='.$DATABASE.';charset=utf8', $USERNAME, $PASSWORD);
			$DB->setAttribute(PDO::ATTR_ERRMODE, $ERR_MODE);
			$DB->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			return true;
		}
		catch(Exception $e){PDO_handle_exception($e);}
		return false;
	}
/*
Returns the PDO class itself for if/when more methods and functionality is desired
*/
	function PDO_pdo_self(){
		gloabl $DB;
		return $DB;
	}
/*
Returns a boolean which determines whether there is a current connection to a database.
*/
	function PDO_is_connected(){
		global $DB;
		if(isset($DB))
			return true;
		else return false;
	}
/*
Sets the host to the new value given to the function.
Disconnects the current session.
$hostname = url/address of database server.
*/
	function PDO_change_host($hostname){
		global $DB, $HOSTNAME;
		if(isset($DB)) unset($DB);
		if(!isset($DB)){
			$HOSTNAME = $hostname;
			return true;
		}
		else return false;
	}
/*
Disconnects the current user from the database connection and sets
the user to a new one that will be used to create a new connection.
$username = username of database user.
$password = password for the user.
*/
	function PDO_change_user($username,$password){
		global $DB, $USERNAME, $PASSWORD;
		if(isset($DB)) unset($DB);
		if(!isset($DB)){
			$USERNAME = $username;
			$PASSWORD = $password;
			return true;
		}
			
		else return false;
	}
/*
Sets the database name to the new value given to the function.
Disconnects the current session.
$database = name of the database.
*/
	function PDO_change_database($database){
		global $DB, $DATABASE;
		if(isset($DB)) unset($DB);
		if(!isset($DB)){
		 	$DATABASE = $database;
			return true;
		}
		else return false;
	}
/*
Creates a new PDO object and attempts a connection using the current
values of HOSTNAME, USERNAME, PASSWORD, and DATABASE.
Returns true if connection is successful and false if it is not.
Useful for reconnecting after using switch_host(), switch_user(), or switch_database().
Disconnects current connection if already connected.
*/
	function PDO_reconnect(){
		global $HOSTNAME, $USERNAME, $PASSWORD, $DATABASE;
		return PDO_connect($HOSTNAME,$USERNAME,$PASSWORD,$DATABASE);
	}

/*
Uses current connection to prepare a SQL query, then queries the database.
Returns a multi-dimensional array as a result set to be used with other functions that use result sets.
$sql is the sql string to be prepared and queried.
$valuesIN is an array containing values to be queried in place of the values in the prepared statement.
$array_type = NUM, ASSOC or BOTH. Defaults to ASSOC
*/
	function PDO_query_prepared($sql,$valuesIN=array(),$array_type='ASSOC'){
		try{
		global $DB,$TESTING;
		if(!PDO_is_connected()){throw new Exception("No current connection to database!");}
		if($array_type=='ASSOC')
			$array_type = PDO::FETCH_ASSOC;
		else if($array_type=='NUM')
			$array_type = PDO::FETCH_NUM;
		else $array_type = PDO::FETCH_BOTH;
		$stmt = $DB->prepare($sql);
		$stmt->execute($valuesIN);
		if(!$stmt) return false;
		$valuesOUT = array();
		$i = 0;
		while($row = $stmt->fetch($array_type)){
			$valuesOUT[$i] = $row;
			$i++;
		}
		return $valuesOUT;
		}
		catch(Exception $e){PDO_handle_exception($e);}
		return false;
	}
/*
Uses current connection and sends a SQL query to it.

NOTICE THIS CANNOT UTILIZE PREPARED STATEMENTS.

Returns a multi-dimensional array as a result set to be used with other functions
that use result sets.

$sql = the sql string to be prepared
$array_type = NUM, ASSOC or BOTH. Defaults to ASSOC
*/
	function PDO_query($sql,$array_type='ASSOC'){
		try{
		global $DB, $TESTING;
		if(!PDO_is_connected()){throw new Exception("No current connection to database!");}
		if($array_type=='ASSOC')
			$array_type = PDO::FETCH_ASSOC;
		else if($array_type=='NUM')
			$array_type = PDO::FETCH_NUM;
		else $array_type = PDO::FETCH_BOTH;
		$stmt = $DB->query($sql);
		if(!$stmt) return false;
		$valuesOUT = array();
		$i = 0;
		while($row = $stmt->fetch($array_type)){
			$valuesOUT[$i] = $row;
			$i++;
		}
		return $valuesOUT;
		}
		catch(Exception $e){PDO_handle_exception($e);}
		return false;
	}
/*
Takes a result set returned by either PDO_query_prepared() or PDO_query() functions.
Returns either true or false depending on whether or not the result set 
contained any rows.
*/
	function PDO_has_rows($result_set){
		if($PDO_num_rows($result_set) > 0)
			return true;
		else return false;
	}
/*
Takes a result set returned by either PDO_query_prepared() or PDO_query() functions.
Returns the number of rows contained in the result set.
*/
	function PDO_num_rows($result_set){
		return count($result_set);
	}
/*
Takes a result set returned by either PDO_query_prepared() or PDO_query() functions passed by reference.
Returns the next row in the result set and removes it from the set.
If no rows are left in the result set, it returns false.
Can be used in a while loop to iterate over all rows until result set is empty.
$result_set = a result set returned by either PDO_query_prepared() or PDO_query() functions passed by reference.
*/
	function PDO_fetch_row(&$result_set){
		if(!$result_set)
			return false;
		if(count($result_set) < 1)
			return false;
		else
		return array_shift($result_set);
	}
/*
Takes a result set returned by either PDO_query_prepared() or PDO_query() functions.
Returns an array of the field names present in the result set.
$result_set = a result set returned by either PDO_query_prepared() or PDO_query() functions.
*/
	function PDO_field_names($result_set){
		if(PDO_has_rows($result_set))
			return array_keys($result_set[0]);
		else return array();
	}
/*
For internal use.
Handles exceptions based on the ERR_MODE.
*/
	function PDO_handle_exception($e){
		global $TESTING, $ERR_MODE, $USER_SET_ERR_MODE;
		if($TESTING) echo $e;
		else if($USER_SET_ERR_MODE && $ERR_MODE > 0) throw $e;
		return false;
	}
?>