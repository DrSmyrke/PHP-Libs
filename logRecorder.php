<?php
############################################################################

function LogRecorder_help()
{
	// print "\$telegaAPI = new telegaAPI();<br>\n";
	// print "\$telegaAPI->setAccessToken( VK_API_ACCESS_TOKEN );<br>\n";
	// print "\$telegaAPI->setCheckSSL( false );<br>\n";
	// print "\$telegaAPI->about(); //Get bot information<br>\n";
	// print "\$telegaAPI->getUpdates(); //Get for bot messages<br>\n";
	// print "\$data = \$telegaAPI->execute();<br>\n";
}

############################################################################

enum LogsOutputType {
	case Telegram;
	case Discord;
}

############################################################################
class LogRecorder
{
	private $debug					= false;
	private $logLevel				= 3;
	private $logType				= 0;
	private $target					= '';
	private $param					= '';
	
	public function __construct( $debug = false )
	{
		$this->debug				= $debug;
	}
	
	public function setLogLevel( $level )
	{
		$this->logLevel				= (int) $level;
	}

	public function setOutput( $type, $target, $param )
	{
		$this->logType				= $type;
		$this->target				= $target;
		$this->param				= $param;
	}

	public function toLog( $level, $message, $file = '', $line = -1 )
	{
		if( $level > $this->logLevel ) return;

		$dateTime					= date( 'Y-m-d H:i:s' );

		if( $file != '' ){
			if( $line >= 0 ) $file .= ':'.$line;
			$message = $file.' '.$message;
		}

		switch( $this->logType ){
			case LogsOutputType::Telegram:
				$logAPI = new telegaAPI();
				$logAPI->setAccessToken( $this->param );
				$logAPI->setCheckSSL( false );
				$logAPI->sendMessage( $this->target, '['.$dateTime.'] <b>'.$this->getLogLevelString( $level ).'</b> '.$message );
			break;
			case LogsOutputType::Discord:
				$logAPI = new discordAPI();
				$logAPI->setAccessToken( $this->param );
				$logAPI->setCheckSSL( false );
				$logAPI->sendMessage( '['.$dateTime.'] <b>'.$this->getLogLevelString( $level ).'</b> '.$message, true );
			break;
		}
	}

	private function getLogLevelString( $type )
	{
		$string = 'N/A';

		switch( $type ){
			case 0:		$string = 'CORE';	break;
			case 1:		$string = 'ERROR';	break;
			case 2:		$string = 'WARN';	break;
			case 3:		$string = 'INFO';	break;
			case 4:		$string = 'DEBUG';	break;
		}

		return $string;
	}

	public static function getLogLevelFromString( $string )
	{
		$level = -1;

		switch( strtoupper( $string ) ){
			case 'CORE':	$level = 0;		break;
			case 'ERROR':	$level = 1;		break;
			case 'WARN':	$level = 2;		break;
			case 'INFO':	$level = 3;		break;
			case 'DEBUG':	$level = 4;		break;
		}

		return $level;
	}
}

?>
