<?php
	ini_set('display_errors',1);
	ini_set('error_reporting',-1);
	
	######################################################
	
	$cmd			= null;
	$data			= null;
	$dateTime		= date("Y-m-d H:i:s");
	$currentYear	= date("Y");
	$thisPage		= baseName( $_SERVER["SCRIPT_NAME"] );
	
	######################################################

	if( $_SERVER['REQUEST_METHOD'] == 'POST' ){
		if( isset( $_POST["cmd"] ) ) $cmd = $_POST["cmd"];
		if( isset( $_POST["data"] ) ) $data = $_POST["data"];
	}
	if( $_SERVER['REQUEST_METHOD'] == 'GET' ){
		if( isset( $_GET["cmd"] ) ) $cmd = $_GET["cmd"];
		if( isset( $_GET["data"] ) ) $data = $_GET["data"];
	}
	
	######################################################
?>
