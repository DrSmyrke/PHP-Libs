<?php
	ini_set('display_errors',1);
	ini_set('error_reporting',-1);
	
	######################################################
	
	$cmd				= null;
	$data				= null;
	$dateTime			= date("Y-m-d H:i:s");
	$currentYear		= date("Y");
	$currentMonth		= date("m");
	$currentWeekDay		= date("N");
	$thisPage			= baseName( $_SERVER["SCRIPT_NAME"] );
	$staticHost			= "";
	
	######################################################

	$tmp = explode( ".", $_SERVER['SERVER_NAME'] );

	if( count( $tmp ) > 1 ){
		$staticHost = array_pop( $tmp );
		if( $staticHost != "localhost" ){
			$staticHost = 'http://static.'.array_pop( $tmp ).'.'.$staticHost;
		}else{
			$staticHost = 'http://static.'.$staticHost;
		}
	}

	######################################################
	
	if( isset( $_SERVER['REQUEST_METHOD'] ) ){
		if( $_SERVER['REQUEST_METHOD'] == 'POST' ){
			if( isset( $_POST["cmd"] ) ) $cmd = $_POST["cmd"];
			if( isset( $_POST["data"] ) ) $data = $_POST["data"];
		}
		if( $_SERVER['REQUEST_METHOD'] == 'GET' ){
			if( isset( $_GET["cmd"] ) ) $cmd = $_GET["cmd"];
			if( isset( $_GET["data"] ) ) $data = $_GET["data"];
		}
	}
	
	######################################################

	if( !function_exists('apache_request_headers') ){ 
        function apache_request_headers(){ 
            foreach( $_SERVER as $key => $value ){ 
                if( substr($key,0,5)=="HTTP_" ){ 
					$key = str_replace(" ","-",ucwords(strtolower(str_replace("_"," ",substr($key,5))))); 
					$out[$key] = $value; 
                }else{
					$out[$key] = $value; 
				}
            } 
            return $out; 
        } 
	}
?>
