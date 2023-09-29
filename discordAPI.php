<?php


// function telegaAPI_help()
// {
// 	print "\$telegaAPI = new telegaAPI();<br>\n";
// 	print "\$telegaAPI->setAccessToken( VK_API_ACCESS_TOKEN );<br>\n";
// 	print "\$telegaAPI->setCheckSSL( false );<br>\n";
// 	print "\$telegaAPI->about(); //Get bot information<br>\n";
// 	print "\$telegaAPI->getUpdates(); //Get for bot messages<br>\n";
// 	print "\$data = \$telegaAPI->execute();<br>\n";
// }



class discordAPI
{
	private $accessToken					= "";
	private $api_endpoint					= "https://discord.com/api/";
	private $webhook_endpoint				= "webhooks/";
	private $debug							= false;
	private $checkSSL						= true;
	private $acl							= Array();
	
	public function __construct( $debug = false )
	{
		$this->debug						= $debug;
	}

	public function setCheckSSL( $value = true ){ $this->checkSSL = $value; }
	public function setAccessToken( $value ){ $this->accessToken = $value; }
	public function enableDebug(){ $this->debug = true; }
	public function disableDebug(){ $this->debug = false; }
	
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
	
	public function sendMessage( $message, $webHook = false, $data = Array() )
	{
		$message = $this->messageReplace( $message );

		// $method = ( $webHook ) ? $this->webhook_endpoint : 'sendMessage';
		// return $this->call( $method, json_encode( Array(
		// 	'content'		=> substr( $message, 0, 4096 ),
			
		// 	'reactions' => ( isset( $data[ 'reactions' ] ) ) ? $data[ 'reactions' ] : Array(),
			
		// 	'embeds' => ( isset( $data[ 'embeds' ] ) ) ? $data[ 'embeds' ] : Array(),
		// 	'attachments' => ( isset( $data[ 'attachments' ] ) ) ? $data[ 'attachments' ] : Array(),
		// 	'payload_json' => ( isset( $data[ 'payload_json' ] ) ) ? $data[ 'payload_json' ] : Array(),
		// ) ) );
		//
	}

	public function sendWebhook( $message, $data = Array() )
	{
		$message = $this->messageReplace( $message );

		$boundary = uniqid();
		$type = ( isset( $data[ 'attachments' ] ) ) ? 'multipart/form-data' : 'application/json';
		$post_data = Array(
			'content'				=> substr( $message, 0, 4096 ),
		);
		if( isset( $data[ 'allowed_mentions' ] ) ) $post_data[ 'allowed_mentions' ] = $data[ 'allowed_mentions' ];
		if( isset( $data[ 'embeds' ] ) ) $post_data[ 'embeds' ] = $data[ 'embeds' ];

		if( isset( $data[ 'attachments' ] ) ){
			$post_data = $this->addMultipartData( json_encode( $post_data ), 'payload_json', 'application/json', $boundary );

			foreach( $data[ 'attachments' ] as $indx => $attach ){
				$post_data .= $this->addMultipartData( $attach[ 'data' ], $attach[ 'name' ], $attach[ 'type' ], $boundary, $attach[ 'filename' ], $attach[ 'encoding' ] );
			}

			$eol = "\r\n";
			$delimiter = '-------------' . $boundary;
			$post_data .= "--" . $delimiter . "--".$eol;
			$type .='; boundary=' . $delimiter;
		}else{
			$post_data = json_encode( $post_data );
		}
		
		return $this->call( $this->webhook_endpoint, $type, $post_data );
	}

	private function addMultipartData( $content, $name, $type, $boundary, $fileName = '', $encoding = 'binary' )
	{
		$data = '';
		$eol = "\r\n";
		$delimiter = '-------------' . $boundary;

		$DispositionAdd = ( $fileName != '' ) ? '; filename="' . $fileName . '"' : '';

		$data .= "--" . $delimiter . $eol
			  . 'Content-Disposition: form-data; name="' . $name . '"' . $DispositionAdd . $eol
			  . 'Content-Type: '.$type.$eol;
		if( $fileName != '' ) $data .= 'Content-Transfer-Encoding: '.$encoding.$eol;
		$data .= $eol;
		$data .= $content . $eol;

		return $data;
	}

	private function messageReplace( $message )
	{
		$message = str_replace( '<b>', '**', $message );
		$message = str_replace( '</b>', '**', $message );
		$message = str_replace( '<i>', '*', $message );
		$message = str_replace( '</i>', '*', $message );
		$message = str_replace( '<u>', '__', $message );
		$message = str_replace( '</u>', '__', $message );
		$message = str_replace( '<a>', '[', $message );
		$message = str_replace( '</a>', ']', $message );

		return $message;
	}

	// public function about()
	// {
	// 	return $this->call( 'getMe' );
	// }
	// public function getUpdates()
	// {
	// 	return $this->call( 'getUpdates' );
	// }
	
	private function call( $method, $type = 'multipart/form-data', $post_data = '' )
	{

		$url = $this->api_endpoint.$method.$this->accessToken;

		if( $this->debug ){
			print '>:'.$url.'['.$type.']<br>';
			print '>:'.$post_data.'<br>';
		}

		if( function_exists('curl_init') ){
			$curl = curl_init( $url );
			curl_setopt( $curl, CURLOPT_HTTPHEADER, Array(
				'Content-Type: '.$type,
			) );
			curl_setopt( $curl, CURLOPT_URL, $url );
			curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $curl, CURLOPT_POST, true );
			curl_setopt( $curl, CURLOPT_POSTFIELDS, $post_data );
			curl_setopt( $curl, CURLOPT_FOLLOWLOCATION, true );
			curl_setopt( $curl, CURLOPT_HEADER, false );
			
			if( !$this->checkSSL ) curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			
			$json = curl_exec($curl);
			$error = curl_error($curl);
			
			if ($error) return "Failed '{$method}' request ['{$error}']";
			curl_close($curl);
			$response = json_decode( $json, true );
			if( $this->debug ) print "<:".$response."<br>";
			return $response;
		}else{
			return "Failed CURL init";
		}
	}
}

?>
