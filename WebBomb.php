<?php
function webbomb_help()
{
	print "\$bomb = new WebBomb;<br>\n";
	print "\$bomb->activate();<br>\n";
	print "\$bomb->sendBomb(); //Manual send bomb!!!! Attention! May result in a complete loss of RAM<br>\n";
}

class WebBomb
{
	private $userAgent		= "";//$_SERVER['HTTP_USER_AGENT'];
	private $requestURL		= "";//$_SERVER['REQUEST_URI'];
	private $bombFile		= "/www/data/10G.gzip";
	private $findUA			= array( "nikto", "sqlmap" );
	private $findUrls		= array( "/?timetodie", "/phpmyadmin", "/admin" );
	
	public function activate()
	{
		foreach( $this->findUrls as $str ){
			if( strpos( $this->requestURL, $str ) == 0 ){
				WebBomb::sendBomb();
				break;
			}
		}
		foreach( $this->findUA as $str ){
			if( strpos( $this->userAgent, $str ) >= 0 ){
				WebBomb::sendBomb();
				break;
			}
		}
	}

	public function sendBomb()
	{
		header("Content-Encoding: gzip");
		header("Content-Length: ".filesize( $bombFile ));
		//Turn off output buffering
        if (ob_get_level()) ob_end_clean();
		//send the gzipped file to the client
        readfile( $bombFile );
		exit();
	}
}
?>
