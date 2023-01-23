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

enum LogType{
	case CORE;
	case ERROR;
	case WARN;
	case INFO;
	case DEBUG;
}

enum LogOutputType{
	case TELEGRAM;
	case FILE;
}


############################################################################
class LogRecorder
{
	private $debug					= false;
	private $logLevel				= LogType::INFO;
	private $logType				= LogOutputType::FILE;
	private $target					= '';
	private $param					= '';
	
	public function __construct( $debug = false )
	{
		$this->debug				= $debug;
	}
	
	public function setLogLevel( LogType $level )
	{
		$this->logLevel				= $level;
	}

	public function setOutput( LogOutputType $type, $target, $param )
	{
		$this->logType				= $type;
		$this->target				= $target;
		$this->param				= $param;
	}

	public function toLog( LogType $type, $message, $file = '', $line = -1 )
	{
		$dateTime				= date( 'Y-m-d H:i:s' );

		if( $file != '' ){
			if( $line >= 0 ) $file .= ':'.$line;
			$message = $file.' '.$message;
		}

		switch( $this->logType ){
			case LogOutputType::TELEGRAM:
				$logAPI = new telegaAPI();
				$logAPI->setAccessToken( $this->param );
				$logAPI->setCheckSSL( false );
				$logAPI->sendMessage( $this->target, '['.$dateTime.'] <b>'.$this->getLogTypeString( $type ).'</b> '.$message );
			break;
		}
	}

	private function getLogTypeString( LogType $type )
	{
		$string = 'N/A';

		switch( $this->logType ){
			case LogType::CORE:		$string = 'CORE';	break;
			case LogType::ERROR:	$string = 'ERROR';	break;
			case LogType::WARN:		$string = 'WARN';	break;
			case LogType::INFO:		$string = 'INFO';	break;
			case LogType::DEBUG:	$string = 'DEBUG';	break;
		}

		return $string;
	}
}

?>
