<?php
ini_set("display_errors", true);
date_default_timezone_set("Africa/Nairobi");
$as_site_url = $_SERVER['HTTP_HOST'] . strtr(dirname($_SERVER['SCRIPT_NAME']), '\\', '/');

//Database domain name server
define( "DB_DSN", "mysql:host=localhost;dbname=mumias" );

//Database user
define( "DB_USER", "root" );

//Database password
define( "DB_PASS", ""  );

//Core module where main system funstions are defined
define("CORE", "core/");

//Page module where different page types are handled
define("PAGES", "pages/");

// Site template for css and javascript
define("TEMPLATE", "template/");

// Site address
define("SITEURL", "http://" . $as_site_url . "/");

// Name of the system/Site
define( "SITENAME", "Mumias Sugarcane Harvest Management System"  );

// run the exception handler
set_exception_handler('handleException');

/**
 * Exception handler for basic functioning like when system fails to connect with the database
 */
function handleException($exception)
{
	$error_message = $exception->getMessage();

	// When there is no admin user in the system

	if (strpos($error_message, 'Access denied for user')) {
		$as_err['errno'] = 1;
		$as_err['errtitle'] = 'Start Setting Up Things';
		$as_err['errsumm'] = 'Set a few options to start you off... on: ' . SITEURL;
	}

	// When there is no connection to the database
	else if (strpos($error_message, 'Unknown database')) {
		$db_name = explode('dbname=', DB_DSN);
		$as_err['errno'] = 1;
		$as_err['errtitle'] = 'Unable to connect to the database';
		$as_err['errsumm'] = 'The database <b>' . $db_name[1] . '</b> is unknown or missing. Connect to another or recreate the database with that name and refresh this page';
	} else {
		$as_err['errno'] = 0;
		$as_err['errtitle'] = 'Need to fix something';
		$as_err['errsumm'] = $error_message;
	}

	// The error page will based on this file
	require_once CORE .  'error.php';
}

/**
 * Create a new option for site settings
 */
function as_new_option($title, $content)
{
	$conn = new PDO(DB_DSN, DB_USER, DB_PASS);
	$sql = "INSERT INTO options ( title, content, created ) VALUES ( :title, :content, :created )";
	$st = $conn->prepare($sql);
	$st->bindValue(":title", $title, PDO::PARAM_STR);
	$st->bindValue(":content", $content, PDO::PARAM_STR);
	$st->bindValue(":created", date('Y-m-d H:i:s'), PDO::PARAM_STR);
	$st->execute();
	$last_id = $conn->lastInsertId();
	$conn = null;
	if ($last_id == 0) errCreateTables();
}

/**
 * Update an option for site settings
 */
function as_update_option($title, $content)
{
	$conn = new PDO(DB_DSN, DB_USER, DB_PASS);
	$sql = "UPDATE options SET content=:content, updated=:updated WHERE title = :title";
	$st = $conn->prepare($sql);
	$st->bindValue(":title", $title, PDO::PARAM_STR);
	$st->bindValue(":content", $content, PDO::PARAM_STR);
	$st->bindValue(":updated", date('Y-m-d H:i:s'), PDO::PARAM_STR);
	$st->execute();
	$conn = null;
}

/**
 * check if an option for site settings exists and if it doesn't create it
 */
function as_check_option($title, $content)
{
	if (!strlen(as_option($title))) as_new_option($title, $content);
}

/**
 * Showing the error of missing tables
 */
function errMissingTables()
{
	$as_err['errno'] = 5;
	$as_err['errtitle'] = 'Missing database tables';
	$as_err['errsumm'] = 'Your database is missing some tables';
	$as_err['errsumm'] = 'We found your database to be missing some tables. When you click the Create Button below missing tables in your database will be created';
	require_once CORE .  'error.php';
	exit();
}

/**
 * Showing the error to setup the database name, user name and password
 */
function errCreateTables()
{
	$as_err['errno'] = 4;
	$as_err['errtitle'] = 'Setup your database';
	$as_err['errsumm'] = 'It is time to setup your database';
	$as_err['errfull'] = 'When click the Create Button below database will be setup with all the tables';
	require_once CORE .  'error.php';
	exit();
}

/**
 * get the value of option for site settings
 */
function as_option($title)
{
	$conn = new PDO(DB_DSN, DB_USER, DB_PASS);
	$sql = "SELECT content FROM options WHERE title=:title";
	$st = $conn->prepare($sql);
	$st->bindValue(":title", $title, PDO::PARAM_INT);
	$st->execute();
	$row = $st->fetch();
	$conn = null;
	if ($row) return $row['content'];
	else return '';
}

/**
 * check if a value in a row exists
 */
function as_check_db_value($valueid, $column, $value, $table)
{
	$conn = new PDO(DB_DSN, DB_USER, DB_PASS);
	$sql = "SELECT * FROM " . $table . " WHERE " . $column . "=" . $value;
	$st = $conn->prepare($sql);
	$st->execute();
	$row = $st->fetch();
	$conn = null;
	if ($row) return true;
}

as_check_option('sitename', SITENAME);
if (!as_check_db_value('managerid', 'level', 5, 'managers')) {
	$as_err = array();
	$as_err['errno'] = 2;
	$as_err['errtitle'] = 'Create Your Own Account First';
	$as_err['errsumm'] = 'There are no managers yet! That means you need to set up your own account to proceed';
	require_once CORE .  'error.php';
	exit();
}