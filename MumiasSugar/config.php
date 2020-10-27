<?php
	ini_set( "display_errors", true );
	date_default_timezone_set( "Africa/Nairobi" );
	$as_site_url = $_SERVER['HTTP_HOST'].strtr(dirname($_SERVER['SCRIPT_NAME']), '\\', '/');
	
	define( "DB_DSN", "mysql:host=localhost;dbname=mumias" );
	define( "DB_USER", "root" );
	define( "DB_PASS", ""  );
	define( "CORE", "core/" );
	define( "PAGES", "pages/" );
	define( "TEMPLATE", "template/" );
	define( "SITEURL", "http://".$as_site_url."/" );
	define( "SITENAME", "Mumias Sugarcane Harvest Management System"  );
	
	function handleException( $exception ) {
		$error_message = $exception->getMessage();
		if (strpos($error_message, 'Access denied for user')) {
			$as_err['errno'] = 1;
			$as_err['errtitle'] = 'Start Setting Up Things';
			$as_err['errsumm'] = 'Set a few options to start you off... on: '.SITEURL;			
		} else if (strpos($error_message, 'Unknown database')) {
			$db_name = explode('dbname=', DB_DSN);	
			$as_err['errno'] = 1;
			$as_err['errtitle'] = 'Unable to connect to the database';
			$as_err['errsumm'] = 'The database <b>'.$db_name[1] . '</b> is unknown or missing. Connect to another or recreate the database with that name and refresh this page';	
		} else {
			$as_err['errno'] = 0;
			$as_err['errtitle'] = 'Need to fix something';
			$as_err['errsumm'] = $error_message;
		}
		require_once CORE .  'error.php';
	}
	set_exception_handler( 'handleException' );
	
	function as_new_option($title, $content) 
	{
		$conn = new PDO( DB_DSN, DB_USER, DB_PASS );
		$sql = "INSERT INTO options ( title, content, created ) VALUES ( :title, :content, :created )";
		$st = $conn->prepare ( $sql );
		$st->bindValue( ":title", $title, PDO::PARAM_STR );
		$st->bindValue( ":content", $content, PDO::PARAM_STR );
		$st->bindValue( ":created", date('Y-m-d H:i:s'), PDO::PARAM_STR );
		$st->execute();
		$last_id = $conn->lastInsertId();
		$conn = null;
		if ($last_id == 0) errCreateTables();
	}

	function as_update_option($title, $content) 
	{
		$conn = new PDO( DB_DSN, DB_USER, DB_PASS );
		$sql = "UPDATE options SET content=:content, updated=:updated WHERE title = :title";		
		$st = $conn->prepare ( $sql );
		$st->bindValue( ":title", $title, PDO::PARAM_STR );
		$st->bindValue( ":content", $content, PDO::PARAM_STR );
		$st->bindValue( ":updated", date('Y-m-d H:i:s'), PDO::PARAM_STR );
		$st->execute();
		$conn = null;
	}

	function as_check_option($title, $content) 
	{
		if (!strlen(as_option( $title ))) as_new_option($title, $content);
	}

	function errMissingTables()
	{
		$as_err['errno'] = 5;
		$as_err['errtitle'] = 'Missing database tables';
		$as_err['errsumm'] = 'Your database is missing some tables';
		$as_err['errsumm'] = 'We found your database to be missing some tables. When you click the Create Button below missing tables in your database will be created';
		require_once CORE .  'error.php';
		exit();
	}
	
	function errCreateTables()
	{
		$as_err['errno'] = 4;
		$as_err['errtitle'] = 'Setup your database';
		$as_err['errsumm'] = 'It is time to setup your database';
		$as_err['errfull'] = 'When click the Create Button below database will be setup with all the tables';
		require_once CORE .  'error.php';
		exit();
	}
	
	function as_option( $title ) 
	{
		$conn = new PDO( DB_DSN, DB_USER, DB_PASS );
		$sql = "SELECT content FROM options WHERE title=:title";
		$st = $conn->prepare( $sql );
		$st->bindValue( ":title", $title, PDO::PARAM_INT );
		$st->execute();
		$row = $st->fetch();
		$conn = null;
		if ( $row ) return $row['content'];
		else return '';
	}
	
    function as_check_db_value($valueid, $column, $value, $table)
	{
		$conn = new PDO( DB_DSN, DB_USER, DB_PASS );
		$sql = "SELECT * FROM " . $table . " WHERE " . $column . "=".$value;
		$st = $conn->prepare( $sql );
		$st->execute();
		$row = $st->fetch();
		$conn = null;
		if ( $row ) return true;
    }
	
	as_check_option('sitename', SITENAME);
	if (!as_check_db_value('managerid', 'level', 5, 'managers'))  {
		$as_err = array();
		$as_err['errno'] = 2;
		$as_err['errtitle'] = 'Create Your Own Account First';
		$as_err['errsumm'] = 'There are no managers yet! That means you need to set up your own account to proceed';
		require_once CORE .  'error.php';
		exit(); 
	}