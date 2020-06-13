<?php

class Api
{
	private $classList			= array();
	private $objectList			= array();
	private $apiName			= "";
	private $auth				= false;
	protected $method			= ''; //GET|POST|PUT|DELETE
	public $requestUri			= [];
	public $requestParams		= [];
	protected $action			= ''; //Name Method for execute
	protected $incommingData	= [];

	public function __construct()
	{
		header("Access-Control-Allow-Orgin: *");
		header("Access-Control-Allow-Methods: *");
		header("Content-Type: application/json");

		$this->requestUri		= explode('/', trim($_SERVER['REQUEST_URI'],'/'));
		$this->requestParams	= $_REQUEST;
		$this->incommingData	= json_decode( file_get_contents( 'php://input' ), true );
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
				$this->auth = ApiAuth( $this->apiName, $this->incommingData["key"] );
			}
		}

		foreach( glob("*.php") as $str ){
			require_once  $str;
			$file = basename( $str );
			$tmp = explode( ".", $file );
			$name = array_shift( $tmp );
			
			if( $file == "index.php" ) continue;

			$file = "http://".$_SERVER['SERVER_NAME']."/".$name;
			
			$this->objectList[ $name ] = new $name( $this->requestUri, $this->incommingData, $this->auth );

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
		header("HTTP/1.1 " . $status . " " . $this->requestStatus($status));
		return json_encode($data);
	}

	private function requestStatus($code)
	{
		$status = array(
			200 => 'OK',
			400 => 'Bad Request',
			401 => 'Unauthorized',
			404 => 'Not Found',
			405 => 'Method Not Allowed',
			500 => 'Internal Server Error',
		);
		return ($status[$code])?$status[$code]:$status[500];
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
}
?>
