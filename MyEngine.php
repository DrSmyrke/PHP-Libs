<?php
class MyEngine
{
	private $language			= "en";
	private $useCookie			= false;
	private $staticMode			= false;
	private $jsArray			= Array();
	private $cssArray			= Array( "/data/css/index.css" );
	private $onLoadArray		= Array();
	private $mainMenu			= Array();
	private $staticHost			= "";
	###### EDIT MANUAL ##################
	private $authorContent		= "Прокофьев Юрий (Prokofiev Jura)";
	private $authorKeywords		= "Прокофьев Юрий, портфолио, Мои работы, Мои проекты, Программы, Свободное программное обеспечение, Open source";
	private $authorDescription	= "Персональная страничка -= Dr.Smyrke =-";
	###### END EDIT MANUAL ##################

	private $lng = array(
		"en" => array(
			"opis1"=>"Hello! My name is Yura.",
			"opis2"=>"But here you can find some of my personal projects, download my projects, or find out a bit more about me.",
		),
		"ru" => array(
			"opis1"=>"Привет! Меня зовут Юра.",
			"opis2"=>"Здесь вы можете найти некоторые из моих личных проектов, скачать мои проекты, или узнать немного больше обо мне.",
		)
	);

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
	public function setMainMenu( $mainMenu = array() ){ $this->mainMenu = $mainMenu; }

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
			print '			<link rel=stylesheet type="text/css" href="'.$this->staticHost.'/css/fonts.css"/>'."\n";
			print '			<script type="text/javascript" src="'.$this->staticHost.'/js/index.js"></script>'."\n";
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

		print '			<div style="display: flex;justify-content: space-between;flex-wrap: wrap;">
				<div style="display: flex;">
					<img src="/data/img/avatar.jpg" style="border-radius: 64px;">
					<div class="mySay">'."\n";
		print '<b>'.$this->lng[$this->language]["opis1"].'</b><br>';
		print $this->lng[$this->language]["opis2"];
		print '			</div></div>'."\n";
		$this->drawMenu( $this->mainMenu );
		print '			</div>';
	}

	public function pageBottom( $preContent ="" )
	{
		print $preContent;
		print '
	<hr>
	<center>
		Created by <a href="http://drsmyrke.ru" target="_blank">Dr.Smyrke</a>
	</center>
</body></html>';
	}

	public function drawMenu( $mainMenu )
	{
		$tmp = explode( "?", $_SERVER["REQUEST_URI"] );
		$openUrl = $tmp[0];
		print '<div class="mainMenu">';
			foreach( $mainMenu as $url => $data ){
				if( !is_array( $data ) ) continue;

				if( $url == "languages" ){
					if( count( $data ) > 0 ){
						print '<div class="languageButtons">';
						foreach( $data as $lang ){
							$selected	= ( $lang == $this->language ) ? ' class="selected"' : '';
							$url		= ( $this->staticMode ) ? $this->staticHost.'/images/languages/'.$lang.'.gif' : '/data/img/languages/'.$lang.'.gif';
							print '<img src="'.$url.'"'.$selected.' onClick="setCookie( \'lang\', \''.$lang.'\' );document.location.reload();">';
						}
						print '</div>';
					}
					continue;
				}

				$text	= ( isset( $data[$this->language] ) ) ? $data[$this->language] : '';
				$ico = ( isset($data["ico"]) ) ? '<img src="'.$data["ico"].'">' : "";

				if( $text == "" && $ico == "" ) continue;

				$class = ( $openUrl == $url )?' selected':'';
				print '<a href="'.$url.'" class="a'.$class.'" style="padding: 5px;">'.$ico.$text.'</a>'."\n";
			}
		print '</div>';
	}
}
?>
