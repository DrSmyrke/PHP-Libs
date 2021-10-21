<?php


function telegaAPI_help()
{
	print "\$telegaAPI = new telegaAPI();<br>\n";
	print "\$telegaAPI->setAccessToken( VK_API_ACCESS_TOKEN )<br>\n";
	print "\$telegaAPI->setCheckSSL( false )<br>\n";
	print "\$data = \$telegaAPI->execute();<br>\n";
}



class telegaAPI
{
	private $accessToken			= "";
	private $checkSSL				= true;
	private $parse_mode				= "HTML";
	private $debug					= false;
	
	public function __construct( $debug = false )
	{
		define('API_ENDPOINT', 'https://api.telegram.org/bot');

		$this->debug				= $debug;
	}
	
	// public function execute()
	// {
	// 	$returnData = array();
		
	// 	$requestData = $this->getData();
		
		
	// 	$group_id = ( isset( $requestData["group_id"] ) ) ? $requestData["group_id"] : "";
	// 	$type = ( isset( $requestData["type"] ) ) ? $requestData["type"] : "";
	// 	$user_id = ( isset( $requestData["object"]["user_id"] ) ) ? $requestData["object"]["user_id"] : "";
		
	// 	$returnData["type"] = $type;
	// 	$returnData["user_id"] = $user_id;
		
	// 	switch( $type ){
	// 		case "confirmation":
	// 			if( $group_id == $this->groupID ){
	// 				print $this->confirmationToken;
	// 			}else{
	// 				print "ERROR";
	// 			}
	// 			return $returnData;
	// 		break;
	// 		case "message_new":
	// 			if( isset( $requestData["object"]["body"] ) ){
	// 				$returnData["mess"] = $requestData["object"]['body'];
	// 			}
	// 			if( isset( $requestData["object"]["payload"] ) ){
	// 				$returnData["payload"] = $requestData["object"]['payload'];
	// 			}
	// 		break;
	// 	}
		
		
		
	// 	return $returnData;
	// }
	
	public function sendMessage( $chatID, $message )
	{
		return $this->call('sendMessage', array(
			'chat_id'		=> $chatID,
			'text'			=> substr( $message, 0, 4096 ),
			'parse_mode'	=> $this->parse_mode,
		));
	}
	
	private function call( $method, $params = array() )
	{
		$url = API_ENDPOINT.$this->accessToken.'/'.$method;

		if( $this->debug ) print ">:".$url."<br>";

		if( function_exists('curl_init') ){
			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_HTTPHEADER, array( "Content-Type:multipart/form-data" ));
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
			
			if( !$this->checkSSL ) curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			
			$json = curl_exec($curl);
			$error = curl_error($curl);
			
			if ($error) return "Failed '{$method}' request ['{$error}']";
			curl_close($curl);
			$response = json_decode($json, true);
			if( $this->debug ) print ">:".$json."<br>";
			return $response;
		}else{
			return "Failed CURL init";
		}
	}

	public function setAccessToken( $value ){ $this->accessToken = $value; }
	public function setCheckSSL( $value = true ){ $this->checkSSL = $value; }
}

?>
