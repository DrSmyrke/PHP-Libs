<?php
	######################### GLOBAL VARS ###########################
	
	$auth				= false;
	$accessDeniedStr	= "По вопросам доступа обращаться по телефону: 02";
	
	#################################################################
	
function parsAuthHTTP()
{
	global $_SERVER, $auth, $userName, $adConnect, $adUserData, $userPC, $userAD, $accessDeniedStr;
	
	$headers = apache_request_headers();
	
	if( !isset($headers['Authorization']) ){
		header('HTTP/1.0 401 Unauthorized');
		header('WWW-Authenticate: Basic realm="My Intranet"');
		if( strpos( $_SERVER["HTTP_USER_AGENT"], "AppleWebKit" ) === false ){
			header('WWW-Authenticate: NTLM');
			return;
		}
		sleep(1);
		//drawData( $headers );
		//drawData( $_SERVER );
		//print $accessDeniedStr;
		return;
	}
	
	$chain = base64_decode(substr($headers['Authorization'],5));
	
	if( isset($headers['Authorization']) ){
		$tmp = explode( " ", $headers['Authorization'] );
		if( count( $tmp ) == 2 ){
			$authType = strtolower($tmp[0]);
			$authKey = $tmp[1];
			//print "[$authType]";
			
			switch( $authType ){
				case "basic":
					$authTmpBase64 = explode( ":", base64_decode($authKey) );
					if( count( $authTmpBase64 ) == 2 ){
						$authLogin = strtolower($authTmpBase64[0]);
						list( $userAD, $authLogin ) = explode( "\\", $authLogin );
						$authPass = $authTmpBase64[1];
						if( $authLogin && $userAD && $authPass ){
							list( $true, $conn ) = adConnect( $authLogin, $authPass );
							$userName = $authLogin;
							$adConnect = $conn;
							$adUserData = adGetData( $conn, $userName );
							if( $adUserData["count"] > 0 ) $auth = true;
						}
					}
				break;
				case "ntlm":
					$chain = base64_decode(substr($headers['Authorization'],5));
					switch (ord($chain{8})) { // смотрим номер этапа процесса идентификации
						case 3: // этап 5 - приём сообщения type-3
							foreach (array('LM_resp','NT_resp','domain','user','host') as $k=>$v) {
								extract(unpack('vlength/voffset',substr($chain,$k*8+14,4)));
								$val = substr($chain,$offset,$length);
								$val = ($k<2 ? hex_dump($val) : iconv('UTF-16LE','CP1251',$val));
								if( $v == "host" ) $userPC = $val;
								if( $v == "domain" ) $userAD = $val;
								if( $v == "user" ) $userName = $val;
							}
							if( $userName && $userAD ){
								list( $true, $adConnect ) = adConnect2( "rsi.net", 389, "rsi.net", "Ldapviewer", "uKtyvQmrRf" );
								$adUserData = adGetData( $adConnect, $userName );
								if( $adUserData["count"] > 0 ) $auth = true;
							}
						break;
						case 1:
							// этап 3 (тут было == 0xB2, я исправил на 130). 178 -> B2 или 130 -> 82
							// 0x82 возвращают мозилла и опера при обычном вводе руками, а 0xB2 возвращает IE при параметре "исользовать текущие логин и пароль"
							if (ord($chain{13}) == 0x82||ord($chain{13}) == 0xB2) {
								// проверяем признак NTLM 0x82 по смещению 13 в сообщении type-1:
								$chain = "NTLMSSP\x00".// протокол
								"\x02" /* номер этапа */ ."\x00\x00\x00\x00\x00\x00\x00".
								"\x28\x00" /* общая длина сообщения */ ."\x00\x00".
								"\x01\x82" /* признак */ ."\x00\x00".
								"\x00\x02\x02\x02\x00\x00\x00\x00". // nonce
								"\x00\x00\x00\x00\x00\x00\x00\x00";
								header('HTTP/1.1 401 Unauthorized');
								header('WWW-Authenticate: NTLM '.base64_encode($chain)); // отправляем сообщение type-2
								exit;
							}
						break;
					}
				break;
			}
			
		}
	}
}

parsAuthHTTP();
if( !$auth ){
	print $accessDeniedStr;
	exit;
}
?>