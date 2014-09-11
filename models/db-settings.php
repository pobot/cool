<?php
/*
UserCake Version: 2.0.2
http://usercake.com
*/

//Database Information

/*
$db_host = "127.0.0.1"; //Host address (most likely localhost)
$db_name = "skem"; //Name of Database
$db_user = "root"; //Name of database user
$db_pass = "felix"; //Password for database user
$db_table_prefix = "sk_";
*/

$db_host = "localhost"; //Host address (most likely localhost)
$db_name = "skem"; //Name of Database
$db_user = "augier"; //Name of database user
$db_pass = "m@rc013"; //Password for database user
$db_table_prefix = "sk_";

GLOBAL $errors;
GLOBAL $successes;

$errors = array();
$successes = array();

/* Create a new mysqli object with database connection parameters */
$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
GLOBAL $mysqli;

if(mysqli_connect_errno()) {
	echo "Connection Failed: " . mysqli_connect_errno();
	exit();
}

?>