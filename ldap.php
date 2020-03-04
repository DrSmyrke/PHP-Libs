<?php
	######################### AD PARAMS #############################
	
	$domainAddr			= "example.net";
	$domainPort			= "389";
	$domainPortSSL		= "636";
	$domainGroupsDN		= "ou=USERS,dc=example,dc=net";
	$domainGroupsDN2	= "ou=USERS,dc=example,dc=net";
	$domainGroupsDN3	= "ou=ADBook,dc=example,dc=net";
	
	######################### GLOBAL VARS ###########################
	
	$userName			= "guest";
	$userPC				= "N/A";
	$userAD				= "N/A";
	$adUserData			= null;
	$adConnect			= null;
	$authMode			= null;
	$login				= null;
	$pass				= null;
	
	#################################################################
	
	if( isset( $_SERVER['REQUEST_METHOD'] ) ){
		if( $_SERVER['REQUEST_METHOD'] == 'POST' ){
			if( isset( $_POST["authMode"] ) ) $authMode = $_POST["authMode"];
			if( isset( $_POST["login"] ) ) $login = $_POST["login"];
			if( isset( $_POST["pass"] ) ) $pass = $_POST["pass"];
		}else{
			if( isset( $_GET["authMode"] ) ) $authMode = $_GET["authMode"];
			if( isset( $_GET["login"] ) ) $login = $_GET["login"];
			if( isset( $_GET["pass"] ) ) $pass = $_GET["pass"];
		}
	}
	
	#################################################################
	
	if( $authMode == "login" || $authMode == "login2" ){
		parsLogin( $authMode );
		exit;
	}
	if( $authMode == "logout" || $authMode == "logout2" ){
		parsLogout( $authMode );
		exit;
	}

function parsLogout( $mode )
{
	//global $_SERVER;
	setcookie("WebAccess","ACHTUNG",time()-60*60*24);
	//$conn=adAdminConnect();
	//$userAData=adGetData($conn,$userName);
	//$newData=array();
	//$newData["userPassword"]="	";
	//$result=saveAdData($userAData[0]["dn"],$newData);
	#$fs=fopen("users/$userName/auth","w");fwrite($fs,"	");fclose($fs);
	if( $mode == "logout" ){
		print "3>:Вы успешно вышли!>:";
		printLoginForm();
	}
	if( $mode == "logout2" ){
		pagetop("Страница выхода","");
		print '<script type="text/javascript">msg("Вы упешно вышли!");</script>';
		print '<script type="text/javascript">setTimeout(\'document.location.href="/";\',2000);</script>';
	}
}
function parsLogin( $mode )
{
	global $_SERVER, $login, $pass;
	list( $true, $conn ) = adConnect( $login, $pass );
	if( $true ){
		$userAData = adGetData( $conn, $login );
		if( !adUserStatus( $userAData[0]["useraccountcontrol"][0] ) ){
			if( $mode=="login2"){
				pagetop("Страница входа","");
				print '<script type="text/javascript">msg("Ваша учетная запись отключена! Обратитесь к системному администратору!");</script>';
			}
			if( $mode=="login") print "0>:Ваша учетная запись отключена! Обратитесь к системному администратору!";
		}else{
			$login = strtolower($login);
			//if(!is_dir("users/$ulogin")){mkdir("users/$ulogin");}
			$key = md5( uniqid( rand(), 1 ) );
			$lastLoginTime = time();
			setcookie( "WebAccess", $login.":".$pass.":".md5($key.":".$_SERVER["REMOTE_ADDR"].":".$lastLoginTime),time()+60*60*24);
			//$newData = array();
			//$newData["userPassword"] = "$key:$lastLoginTime";
			//$result = saveAdData( $userAData[0]["dn"], $newData );
			//if( $mode == "login2"){if(!$result){print '<script type="text/javascript">msg("'.$result.'");</script>';}}
			//if( $mode == "login"){if(!$result){print "0>:";var_dump($result);}}
			#$fs=fopen("users/$ulogin/auth","w");fwrite($fs,"$key	$lastLoginTime");fclose($fs);
			//$fs=fopen("users/$ulogin/pass","w");fwrite($fs,$pass);fclose($fs);
			if( $mode=="login2"){
				pagetop("Страница входа","");
				print '<script type="text/javascript">msg("Вход выполнен!");</script>';
			}
			if($mode=="login"){
				print "1>:Вход выполнен!>:";
				printMyPanel($login);
			}
		}
	}else{
		if( $mode == "login2" ){
			pagetop("Страница входа","");
			print '<script type="text/javascript">msg("Логин или пароль не верен!");</script>';
		}
		if( $mode == "login" ) print "0>:Логин или пароль не верен!";
	}
	if( $mode == "login2" ) print '<script type="text/javascript">setTimeout(\'document.location.href="'.$_SERVER["HTTP_REFERER"].'";\',2000);</script>';
}
function parsAuth()
{
	global $_COOKIE,$_SERVER, $auth, $userName, $adConnect, $adUserData;
	if(isset($_COOKIE["WebAccess"])){
		list( $lastLogin, $lastPass, $authKey ) = explode( ":", $_COOKIE["WebAccess"] );
		$lastLogin = strtolower($lastLogin);
		list( $true, $conn ) = adConnect( $lastLogin, $lastPass );
		//$userAData = adGetData( $conn, $user );
		//list( $key, $lastLoginTime ) = explode( ":", $userAData[0]["userpassword"][0] );
		//$verification_hash = md5( $authKey.":".$_SERVER["REMOTE_ADDR"].":".$lastLoginTime );
		//if( $coockies_hash == $verification_hash ){
		//	$auth = true;
		//	$userName = $lastLogin;
		//}
		if( $true ){
			$auth = true;
			$userName = $lastLogin;
			$adConnect = $conn;
			$adUserData = adGetData( $conn, $userName );
		}
	}
}

function adConnect( $login, $pass )
{
	global $domainAddr, $domainPort;
	$conn = ldap_connect($domainAddr,$domainPort) or die('Не могу подключиться к LDAP-серверу: ' .$domainAddr. ', порт ' .$domainPort);
	ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, 3);
	//ldap_set_option($conn, LDAP_OPT_REFFERALS,0);
	return array( ldap_bind( $conn, $login."@".$domainAddr, $pass ),$conn);
}
function adConnect2( $domainAddr, $domainPort, $domainID, $login, $pass )
{
	$conn = ldap_connect($domainAddr,$domainPort) or die('Не могу подключиться к LDAP-серверу: ' .$domainAddr. ', порт ' .$domainPort);
	ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, 3);
	//ldap_set_option($conn, LDAP_OPT_REFFERALS,0);
	return array(ldap_bind( $conn, $login."@".$domainID, $pass ),$conn);
}

function adGetData( $conn, $user )
{
	global $domainGroupsDN, $domainGroupsDN2;
	$search = ldap_search( $conn, $domainGroupsDN, "(sAMAccountName=$user)" );
	$userData = ldap_get_entries( $conn, $search );
	if( $userData["count"] == 0 ){
		$search = ldap_search( $conn, $domainGroupsDN2, "(sAMAccountName=$user)" );
		$userData = ldap_get_entries( $conn, $search );
	}
	return $userData;
}

function adUserStatus($code)
{
	if($code=="66050"){return false;}#Отключена, пароль вечен, не может менять пароль
	if($code=="66082"){return false;}#Отключена, пароль вечен, может менять пароль
	if($code=="66048"){return true;}#Включена, пароль вечен, не может менять пароль
	if($code=="66080"){return true;}#Включена, пароль вечен, может менять пароль
	if($code=="544"){return true;}#Включена, не может менять пароль
	if($code=="546" or $code=="514"){return false;}#Отключена, без других галок
	if($code=="512"){return true;}#Включена, может менять пароль
	if($code=="514"){return false;}#Отключена, может менять пароль
	return true;
}

function parsDN($string)
{
	$tmp=array();
	foreach(explode(",",$string) as $str){
		$type=$value=null;
		$tmp2=explode("=",$str);
		if(isset($tmp2[0])){$type=$tmp2[0];}
		if(isset($tmp2[1])){$value=$tmp2[1];}
		if($type=="OU"){$tmp[]=$value;}
	}
	return $tmp;
}

function parsCN( $stringCN )
{
	$tmp=array();
	foreach(explode(",",$stringCN) as $str){
		list($type,$value)=explode("=",$str);
		if($type=="CN"){$tmp[]=$value;}
	}
	return $tmp;
}

function adGetLoginFromCN( $conn, $user )
{
	global $domainGroupsDN;
	$search = ldap_search($conn,$domainGroupsDN,"(cn=$user)");
	$userData = ldap_get_entries($conn,$search);
	$userLogin = (isset($userData[0]["samaccountname"][0]))?$userData[0]["samaccountname"][0]:null;
	return $userLogin;
}

function objAsOrganizationUnit( $objectclassData )
{
	$res = false;
	if( !is_array( $objectclassData ) ) return $res;
	foreach( $objectclassData as $val ){
		if( $val == "organizationalUnit" ){
			$res = true;
			break;
		}
	}
	return $res;
}

function objAsGroup( $objectclassData )
{
	$res = false;
	if( !is_array( $objectclassData ) ) return $res;
	foreach( $objectclassData as $val ){
		if( $val == "group" ){
			$res = true;
			break;
		}
	}
	return $res;
}

function getNameAsLogin( $conn, $login, $param = false )
{
	$data = adGetData( $conn, $login );
	$name = ( isset( $data[0]["cn"][0] ) ) ? $data[0]["cn"][0] : $login;
	if( $param ) $name = '<a href="/profile.php?user='.$login.'" target="_blank">'.$name.'</a>';
	return $name;
}

function getADUsersBox( $conn, $ou, $value = null )
{
	$str = null;
	
	$searchOU	= ldap_list( $conn, $ou,"(ou=*)" );
	$dataOU		= ldap_get_entries( $conn, $searchOU );
	$searchCN	= ldap_list( $conn, $ou,"(cn=*)" );
	$dataCN		= ldap_get_entries( $conn, $searchCN );
	
	$str .= '<input name="data[user]" list="users_id" autocomplete="off" size="40">';
		$str .= '<datalist id="users_id">';
		
		foreach( $dataCN as $elem ){
			if( objAsGroup( $elem["objectclass"] ) ) continue;
			if( !is_array( $elem ) ) continue;
			if( isset($elem["msexchhidefromaddresslists"][0]) ){
				if( $elem["msexchhidefromaddresslists"][0] == "TRUE" ) continue;
			}
		
			$login = ( isset($elem["samaccountname"][0]) )? $elem["samaccountname"][0]:"";
			$displayname = ( isset($elem["displayname"][0]) )? $elem["displayname"][0]:"";
			$useraccountcontrol = ( isset($elem["useraccountcontrol"][0]) )? $elem["useraccountcontrol"][0]:"";
			if( !adUserStatus( $useraccountcontrol ) ) continue;
		
			$str .= '<option label="'.$displayname.'" value="'.$login.'">'.$displayname.'</option>';
		}
		foreach( $dataOU as $elem ){
			if( objAsOrganizationUnit( $elem["objectclass"] ) ) getADUsersBox( $conn, $elem["dn"] );
		}
		
		$str .= '</datalist>';	
	return $str;
}
?>