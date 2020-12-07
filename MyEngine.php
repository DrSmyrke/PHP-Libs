<?php
class MyEngine
{
	private $language			= "en";
	private $useCookie			= false;
	private $staticMode			= false;
	private $jsArray			= Array();
	private $cssArray			= Array( "/data/css/index.css" );
	private $onLoadArray		= Array();
	private $staticHost			= "";
	###### EDIT MANUAL ##################
	private $authorContent		= "Прокофьев Юрий (Prokofiev Jura)";
	private $authorKeywords		= "Прокофьев Юрий, портфолио, Мои работы, Мои проекты, Программы, Свободное программное обеспечение, Open source";
	private $authorDescription	= "Персональная страничка -= Dr.Smyrke =-";
	###### END EDIT MANUAL ##################

	###### DONT EDIT ########################

	public function __construct( $staticMode = false )
	{
		if( !isset( $_COOKIE["lang"] ) ){
			//setcookie("lang","en");
			$this->language = "en";
		}else{
			$this->language = $_COOKIE["lang"];
		}

		$this->staticMode = $staticMode;

		if( !$this->staticMode ) return;

		$tmp = explode( ".", $_SERVER['SERVER_NAME'] );
		if( count( $tmp ) > 1 ){
			$this->staticHost = array_pop( $tmp );
			$this->staticHost = 'http://static.'.array_pop( $tmp ).'.'.$this->staticHost;
		}
	}

	public function getLanguage(){ return $this->language; }
	public function setLanguage( $language = "en" ){ $this->language = $language; }

	public function addScriptFile( $url ){ array_push( $this->jsArray, $url ); }
	public function addCssFile( $url ){ array_push( $this->cssArray, $url ); }

	public function setUseCookie( $use = false ){ $this->useCookie = $use; }

	public function pageTop( $pagetitle )
	{
		print '<!DOCTYPE html>
	<html lang="'.$this->language.'">
		<head lang="'.$this->language.'">
			<meta name="viewport" content="width=device-width, initial-scale=1.0">
			<meta charset="utf-8"/>
			<META http-equiv="Pragma" content="no-cache">
			<link rel="shortcut icon" href="/data/img/siteIco.png"/>'."\n";
		if( $this->authorContent != "" ){
			print '			<META NAME="Author" CONTENT="'.$this->authorContent.'"/>'."\n";
		}
		if( $this->authorKeywords != "" ){
			print '			<META NAME="keywords" CONTENT="'.$this->authorKeywords.'"/>'."\n";
		}
		if( $this->authorDescription != "" ){
			print '			<META NAME="description" CONTENT="'.$this->authorDescription.'"/>'."\n";
		}

		if( $this->staticMode ){
			print '			<link rel=stylesheet type="text/css" href="'.$this->staticHost.'/css/index.css"/>'."\n";
			print '			<link rel=stylesheet type="text/css" href="'.$this->staticHost.'/js/index.js"/>'."\n";
		}
		foreach( $this->cssArray as $file ){
			print '			<link rel=stylesheet type="text/css" href="'.$file.'"/>'."\n";
		}
		foreach( $this->jsArray as $file ){
			print '			<script type="text/javascript" src="'.$file.'"></script>'."\n";
		}
		if( $this->useCookie ){
			array_push( $this->onLoadArray, "chkCookie()" );
			if( $this->staticMode ){
				print '			<script type="text/javascript" src="'.$this->staticHost.'/js/cookie.js"></script>'."\n";
			}else{
				print '			<script type="text/javascript" src="/data/js/cookie.js"></script>'."\n";
			}
		}
		print '			<title>-= '.$pagetitle.' =-</title>
		</head>';

		if( count( $this->onLoadArray ) == 0 ){
			print '		<body>'."\n";
		}else{
			print '		<body onLoad="'.join( ";", $this->onLoadArray ).'">'."\n";
		}
	}

	public function pageBottom( $preContent ="" )
	{
		print '<style>.madeBy i{ color: gray; font-style: italic; font-size:8pt; }.madeBy{ text-align: center; }</style>';
		print $preContent;
		print '
	<hr>
	<div class="madeBy">
		Created by <a href="http://drsmyrke.ru" target="_blank">Dr.Smyrke</a>
	</div>
</body></html>';
	}

	public function drawMenu( $mainMenu )
	{
		$tmp = explode( "?", $_SERVER["REQUEST_URI"] );
		$openUrl = $tmp[0];
		print '<div class="mainMenu">';
			foreach( $mainMenu as $url => $data ){
				if( !is_array( $data ) ) continue;

				if( $url == "switcher" ){
					$onClick	= ( isset( $data["onClick"] ) ) ? ' onClick="'.$data["onClick"].'"' : '';
					$first		= ( isset( $data["first"] ) ) ? $data["first"] : '';
					$second		= ( isset( $data["second"] ) ) ? $data["second"] : '';
					$checked	= ( isset( $data["checked"] ) ) ? ' checked' : '';
					print '<label for="langSwitch"> <input type="checkbox" id="langSwitch"'.$onClick.$checked.'> <div class="switcher1"> <div class="rail"> <div class="state1">'.$second.'</div> <div class="slider"></div> <div class="state2">'.$first.'</div> </div> </div> </label>';
					continue;
				}

				$text	= ( isset( $data[$this->language] ) ) ? $data[$this->language] : '';
				if( $text == "" ) continue;

				$class = ( $openUrl == $url )?' selected':'';
				$img = ( isset($data["ico"]) ) ? '<img src="'.$data["ico"].'">' : "";
				print '<a href="'.$url.'" class="button3'.$class.'" style="padding: 5px;">'.$data["ru"].'</a>'."\n";
			}
		print '</div>';
		print '<hr>';
	}
}
?>
