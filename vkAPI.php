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
	print "\$res = \$vkAPI->sendMessage( targetID, message, attachments = array() );<br>\n";
	print "\$res = \$vkAPI->sendSticker( targetID, sticker_id = 0 );<br>\n";
	print "\$res = \$vkAPI->sendButtons( targetID, message, buttons = array(), inline = true, one_time = false );<br>\n";
}



class vkAPI
{
	private $confirmationToken		= "";
	private $groupID				= "";
	private $accessToken			= "";
	private $checkSSL				= true;
	
	public function __construct()
	{
		define('VK_API_VERSION', '5.131');
		define('VK_API_ENDPOINT', 'https://api.vk.com/method/');
	}
	
	public function execute()
	{
		$returnData				= array();
		$requestData			= $this->getData();
		$group_id				= ( isset( $requestData["group_id"] ) ) ? $requestData["group_id"] : "";
		$type					= ( isset( $requestData["type"] ) ) ? $requestData["type"] : "";
		
		if( $group_id != $this->groupID ){
			print "error";
			return $returnData;
		}

		$returnData["type"]		= $type;
		
		switch( $type ){
			case "confirmation":
				print $this->confirmationToken;
			break;
			case "message_new":
				if( isset( $requestData[ "object" ] ) ){
					$object = $requestData["object"];


					if( isset( $object["message"] ) ){
						$returnData["message"] = $object["message"];

						if( isset( $object["message"]["from_id"] ) ){
							$returnData["user_id"] = $object["message"]["from_id"];
						}
						if( isset( $object["message"]["text"] ) ){
							$returnData["text"] = $object["message"]["text"];
						}
						if( isset( $object["message"]["payload"] ) ){
							$returnData["payload"] = $object["message"]["payload"];
						}
					}
				}
				print "ok";
			break;
		}
		
		
		
		return $returnData;
	}
	
	public function sendMessage( $targetID, $message, $attachments = array() )
	{
		return $this->call('messages.send', array(
			'peer_id'		=> $targetID,
			'random_id'		=> time(),
			'message'		=> $message,
			'attachment'	=> implode(',', $attachments)
		));
	}

	public function sendSticker( $targetID, $sticker_id = 0 )
	{
		return $this->call('messages.send', array(
			'peer_id'		=> $targetID,
			'random_id'		=> time(),
			'message'		=> "",
			'sticker_id'	=> $sticker_id
		));
	}
	
	public function sendButtons( $targetID, $message, $buttons = array(), $inline = false, $one_time = false )
	{
		$buttonsSend = array();
		$buttonsSend["buttons"] = $buttons;
		$buttonsSend["inline"] = $inline;
		if( $inline ) $buttonsSend["one_time"] = $one_time;
		
		return $this->call('messages.send', array(
			'peer_id'		=> $targetID,
			'random_id'		=> time(),
			'message'		=> $message,
			'keyboard'		=> json_encode( $buttonsSend, JSON_UNESCAPED_UNICODE )
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
			
			$response = curl_exec($curl);
			$error = curl_error($curl);
			
			if ($error) return "Failed '{$method}' request";
			curl_close($curl);
			$data = json_decode( $response, true);
			if( is_array( $data ) ){
				if( isset( $data["error"] ) ){
					$errorNo	= ( isset( $data["error"]["error_code"] ) ) ? $data["error"]["error_code"] : "N/A";
					$errorMsg	= ( isset( $data["error"]["error_msg"] ) ) ? $data["error"]["error_msg"] : "-";

					return "Invalid request for '{$method}' error [".$errorNo.":".$errorMsg."]";
				}else if( !isset( $data['response'] ) ){
					return "Invalid response for '{$method}'";
				}else{
					return $data['response'];
				}
			}
			return "Error";
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
