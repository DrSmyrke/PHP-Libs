<?php
/* Example
	include "libs/rcon.class.php";
	$rcon=new Rcon;
	list($server,$constat)=$rcon->Connect($servers[$serv][0],$servers[$serv][3],$servers[$serv][4]);
	if($server){
		$ver=$rcon->Command("/version");
		$players=$rcon->Command("/players");
		$time=$rcon->Command("/time");
		print '<table class="table" cellspacing="0">';
		print '<tr><td>VERSION</td><td>'.$ver.'</td></tr>';
		print '<tr><td>TIME</td><td>'.$time.'</td></tr>';
		print '<tr><td>ONLINE</td><td>'.join("<br>",$players).'</td></tr>';
		print "</table>";
	}

*/
class RconException extends Exception
{
	// Exception thrown by Rcon class
}

class Rcon
{

	// Sending
	const SERVERDATA_EXECCOMMAND    = 2;
	const SERVERDATA_AUTH           = 3;
	
	// Receiving
	const SERVERDATA_RESPONSE_VALUE = 0;
	const SERVERDATA_AUTH_RESPONSE  = 2;
	
	private $Socket;
	private $RequestId;
	
	public function __destruct( )
	{
		$this->Disconnect( );
	}
	
	public function Connect( $Ip, $Port = 80, $Password = "", $Timeout = 3 )
	{
		$this->RequestId = 0;
		
		if( $this->Socket = @FSockOpen( $Ip, (int)$Port, $errno, $errstr, $Timeout ) )
		{
			Socket_Set_TimeOut( $this->Socket, $Timeout );
			
			if( !$this->Auth( $Password ) )
			{
				$this->Disconnect( );
				
				#throw new RconException( "Authorization failed." );
				return array(false,"Authorization failed.");
			}
		}
		else
		{
			#throw new RconException( "Server offline" );
			return array(false,"Server offline");
		}
		return array(true,"OK");
	}
	
	public function Disconnect( )
	{
		if( $this->Socket )
		{
			FClose( $this->Socket );
			
			$this->Socket = null;
		}
	}
	
	public function Command( $String )
	{
		if( !$this->WriteData( self :: SERVERDATA_EXECCOMMAND, $String ) )
		{
			return false;
		}
		
		$Data = $this->ReadData( );
		
		if( $Data[ 'RequestId' ] < 1 || $Data[ 'Response' ] != self :: SERVERDATA_RESPONSE_VALUE )
		{
			return false;
		}
		
		return $Data[ 'String' ];
	}
	
	private function Auth( $Password )
	{
		if( !$this->WriteData( self :: SERVERDATA_AUTH, $Password ) )
		{
			return false;
		}
		
		$Data = $this->ReadData( );
		
		return $Data[ 'RequestId' ] > -1 && $Data[ 'Response' ] == self :: SERVERDATA_AUTH_RESPONSE;
	}
	
	private function ReadData( )
	{
		$Packet = Array( );
		
		$Size = FRead( $this->Socket, 4 );
		$Size = UnPack( 'V1Size', $Size );
		$Size = $Size[ 'Size' ];
		
		// TODO: Add multiple packets (Source)
		
		$Packet = FRead( $this->Socket, $Size );
		$Packet = UnPack( 'V1RequestId/V1Response/a*String/a*String2', $Packet );
		
		return $Packet;
	}
	
	private function WriteData( $Command, $String = "" )
	{
		// Pack the packet together
		$Data = Pack( 'VV', $this->RequestId++, $Command ) . $String .chr(0).''.chr(0); 
		
		// Prepend packet length
		$Data = Pack( 'V', StrLen( $Data ) ) . $Data;
		
		$Length = StrLen( $Data );
		
		return $Length === FWrite( $this->Socket, $Data, $Length );
	}
}
