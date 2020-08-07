<?php


function vkAPI_help()
{
	print "\$vkAPI = new vkAPI();<br>\n";
	print "\$vkAPI->setConfirmationToken( CALLBACK_API_CONFIRMATION_TOKEN );<br>\n";
	print "\$vkAPI->setGroupID( CALLBACK_API_GROUP_ID )<br>\n";
	print "\$vkAPI->setAccessToken( VK_API_ACCESS_TOKEN )<br>\n";
	print "\$vkAPI->setCheckSSL( false )<br>\n";
	print "\$data = \$vkAPI->execute();<br>\n";
	print "\$color = \$vkAPI->replaceColor( \"blue\" );<br>\n";
}



class vkAPI
{
	private $confirmationToken		= "";
	private $groupID				= "";
	private $accessToken			= "";
	private $checkSSL				= true;
	
	public function __construct()
	{
		define('VK_API_VERSION', '5.69');
		define('VK_API_ENDPOINT', 'https://api.vk.com/method/');
	}
	
	public function execute()
	{
		$returnData = array();
		
		$requestData = $this->getData();
		
		
		$group_id = ( isset( $requestData["group_id"] ) ) ? $requestData["group_id"] : "";
		$type = ( isset( $requestData["type"] ) ) ? $requestData["type"] : "";
		$user_id = ( isset( $requestData["object"]["user_id"] ) ) ? $requestData["object"]["user_id"] : "";
		
		$returnData["type"] = $type;
		$returnData["user_id"] = $user_id;
		
		switch( $type ){
			case "confirmation":
				if( $group_id == $this->groupID ){
					print $this->confirmationToken;
				}else{
					print "ERROR";
				}
				return $returnData;
			break;
			case "message_new":
				if( isset( $requestData["object"]["body"] ) ){
					$returnData["mess"] = $requestData["object"]['body'];
				}
				if( isset( $requestData["object"]["payload"] ) ){
					$returnData["payload"] = $requestData["object"]['payload'];
				}
			break;
		}
		
		
		
		return $returnData;
	}
	
	public function sendMessage($targetID, $message, $attachments = array())
	{
		return $this->call('messages.send', array(
			'peer_id'		=> $targetID,
			'message'		=> $message,
			'attachment'	=> implode(',', $attachments)
		));
	}
	
	public function sendButtons($targetID, $message, $buttons = array(), $one_time = true)
	{
		$buttonsSend = array();
		$buttonsSend["buttons"] = $buttons;
		$buttonsSend["one_time"] = $one_time;
		
		return $this->call('messages.send', array(
			'peer_id'		=> $targetID,
			'message'		=> $message,
			'keyboard'		=> json_encode($buttonsSend, JSON_UNESCAPED_UNICODE)
		));
	}
	
	private function call($method, $params = array())
	{
		$params['access_token'] = $this->accessToken;
		$params['v'] = VK_API_VERSION;

		#$query = http_build_query( $params );
		#$url = VK_API_ENDPOINT.$method.'?'.$query;
		$url = VK_API_ENDPOINT.$method;

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
			if (!$response || !isset($response['response'])) return "Invalid response for '{$method}' request";
			return $response['response'];
		}else{
			return "Failed CURL init";
		}
	}

	public function setConfirmationToken( $value ){ $this->confirmationToken = $value; }
	public function setGroupID( $value ){ $this->groupID = $value; }
	public function setAccessToken( $value ){ $this->accessToken = $value; }
	public function setCheckSSL( $value = true ){ $this->checkSSL = $value; }
	
	private function getData(){ return json_decode(file_get_contents('php://input'), true); }
	
	public function replaceColor($color)
	{
		switch ($color) {
			case 'red':		$color = 'negative';	break;
			case 'green':	$color = 'positive';	break;
			case 'white':	$color = 'secondary';	break;
			case 'blue':	$color = 'primary';		break;

			default: break;
        }
        return $color;
    }
}

?>
