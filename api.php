<?php

class Api
{
	private $classList			= array();
	private $objectList			= array();
	private $apiName			= "";
	private $authF				= false;
	protected $method			= ''; //GET|POST|PUT|DELETE
	public $requestUri			= [];
	public $requestParams		= [];
	protected $action			= ''; //Name Method for execute
	protected $incommingData	= [];
	protected $incommingRawData	= "";

	public function __construct()
	{
		header("Access-Control-Allow-Orgin: *");
		header("Access-Control-Allow-Methods: *");
		header("Content-Type: application/json");

		$this->requestUri		= explode('/', trim($_SERVER['REQUEST_URI'],'/'));
		$this->requestParams	= $_REQUEST;
		$this->incommingRawData	= file_get_contents( 'php://input' );
		$this->incommingData	= json_decode( $this->incommingRawData, true );
		if( $this->incommingData === NULL ){
			$this->incommingData = parse_ini_string( $this->incommingRawData, true );
		}
		$this->method			= $_SERVER['REQUEST_METHOD'];
		
		if( count( $this->requestUri ) > 0 ) $this->apiName = array_shift( $this->requestUri );
		
		if ($this->method == 'POST' && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER)) {
			if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'DELETE') {
				$this->method = 'DELETE';
			} else if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'PUT') {
				$this->method = 'PUT';
			} else {
				throw new Exception("Unexpected Header");
			}
		}
		
		if( $this->apiName != "" && is_array( $this->incommingData ) ){
			if( array_key_exists( "key", $this->incommingData ) ){
				$this->authF = $this->checkAuth( $this->apiName, $this->incommingData["key"] );
				sleep( 1 );
			}
		}

		foreach( glob("*.php") as $str ){
			require_once  $str;
			$file = basename( $str );
			$tmp = explode( ".", $file );
			$name = array_shift( $tmp );
			
			if( $file == "index.php" ) continue;

			$file = "http://".$_SERVER['SERVER_NAME']."/".$name;
			
			$this->objectList[ $name ] = new $name( $this->requestUri, $this->incommingData, $this->authF );

			if( method_exists( $this->objectList[$name], "getHelp" ) ) {
               $file .=  $this->objectList[$name]->{"getHelp"}();
			}

			array_push( $this->classList, $file );
		}

	}

	public function run()
	{
		if( $this->apiName == "" ){
			return $this->getHelp();
		}
		
		//Первые 2 элемента массива URI должны быть "api" и название таблицы
		//if(array_shift($this->requestUri) !== 'api' || array_shift($this->requestUri) !== $this->apiName){
		//if(array_shift($this->requestUri) !== $this->apiName){
		if( array_key_exists( $this->apiName, $this->objectList ) ){
			//Определение действия для обработки
			$this->action = $this->getAction();
			//Если метод(действие) определен в дочернем классе API
			if( method_exists( $this->objectList[$this->apiName], $this->action ) ) {
				$data = $this->objectList[$this->apiName]->{$this->action}();
				$data["data"]["method"]=$this->method;
				return $this->response( $data["data"], $data["code"] );
			} else {
				throw new RuntimeException('Invalid Method ['.$this->action.']', 405);
			}
		}else{
			throw new RuntimeException('API Not Found', 404);
		}
	}

	protected function response($data, $status = 500)
	{
		$statusString = $this->requestStatus( $status );
		header("HTTP/1.1 " . $status . " " . $statusString);
		if( !array_key_exists( "message", $data ) )	$data["message"] = $statusString;
		return json_encode($data);
	}

	private function requestStatus($code)
	{
		$status = "";
		
		switch( $code ){
			case 200:	$status = "OK";								break;
			case 206:	$status = "Partial Content";				break;
			case 400:	$status = "Bad Request";					break;
			case 401:	$status = "Unauthorized";					break;
			case 404:	$status = "Not Found";						break;
			case 405:	$status = "Method Not Allowed";				break;
			case 500:	$status = "Internal API Error";				break;
			default:	$status = "UNDEFINED";						break;
		}
		
		return $status;
	}

	private function getHelp()
	{
		return $this->response( $this->classList, 200 );
	}

	protected function getAction()
	{
		$method = $this->method;
		switch ($method) {
			case 'GET':
				if($this->requestUri){
					return 'viewAction';
				} else {
					return 'indexAction';
				}
				break;
			case 'POST':
				return 'createAction';
				break;
			case 'PUT':
				return 'updateAction';
				break;
			case 'DELETE':
				return 'deleteAction';
				break;
			default:
				return null;
		}
	}
	
	private function checkAuth( $apiName = "", $apiKey = "" )
	{
		global $_SERVER, $ApiAuthDB;
		
		$apiAuthSQL		= new Sql;
		$key			= "";
		$my_ip			= explode( ".", $_SERVER["REMOTE_ADDR"] );
		$access_ip		= [];
		$find			= false;
		
		if( $apiName == "" ) return false;
		if( !$apiAuthSQL->init( $ApiAuthDB["server"], $ApiAuthDB["user"], $ApiAuthDB["pass"], $ApiAuthDB["db"] ) ) return false;
		if( !$apiAuthSQL->connect() ) return false;
		
		$data			= $apiAuthSQL->getData( $ApiAuthDB["keyTable"], array( "*", "name" => $apiName ) );
		$apiAuthSQL->disconnect();
		
		if( !is_array( $data ) ) return false;
		if( count( $data ) == 0 ) return false;
		
		if( array_key_exists( "key", $data[0] ) )			$key			= $data[0]["key"];
		if( array_key_exists( "access_ip", $data[0] ) )		$access_ip		= explode( ",", $data[0]["access_ip"] );
		
		if( $key != $apiKey ) return false;
		
		foreach( $access_ip as $ip ){
			if( $ip == "*" ){
				$find = true;
				break;
			}
			$tmp = explode( ".", $ip );
			if( count( $tmp ) != 4 ) continue;
			if( $tmp[0] == $my_ip[0] && $tmp[1] == $my_ip[1] && $tmp[2] == $my_ip[2] && $tmp[3] == 0 ){
				$find = true;
				break;
			}
			if( $tmp[0] == $my_ip[0] && $tmp[1] == $my_ip[1] && $tmp[2] == $my_ip[2] && $tmp[3] == $my_ip[3] ){
				$find = true;
				break;
			}
		}
	
		if( !$find ) return false;
		
		return true;
	}
}
?>
