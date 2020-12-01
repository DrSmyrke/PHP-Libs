<?php
class MyEngine
{
	private $language			= "en";
	private $pageIco			= "";
	private $useCookie			= true;
	private $assetsPathUrl		= "/data/";
	private $assetsPath			= "/data";
	private $jsArray			= Array();
	private $cssArray			= Array();
	###### EDIT MANUAL ##################
	private $authorContent		= "Прокофьев Юрий (Prokofiev Jura)";
	private $authorKeywords		= "Прокофьев Юрий, портфолио, Мои работы, Мои проекты, Программы, Свободное программное обеспечение, Open source";
	private $authorDescription	= "Персональная страничка -= Dr.Smyrke =-";
	private $lngAll = array(
	"en" => array(
		"links"=>"My Links",
		"update"=>"Update",
		"file"=>"File",
		"size"=>"Size",
		"upload"=>"Upload",
		"cookieBanner" => "By using this website, you agree to our use of cookies. We use cookies to provide you with a great experience and to help our website run effectively.",
		),
	"ru" => array(
		"links"=>"Мои ссылки",
		"update"=>"Обновить",
		"file"=>"Файл",
		"size"=>"Размер",
		"upload"=>"Загружено",
		"cookieBanner" => "Используя этот сайт, вы соглашаетесь на использование нами файлов cookie. Мы используем куки, чтобы предоставить вам удобство использования и помочь нашему веб-сайту работать эффективно.",
		)
	);
	###### END EDIT MANUAL ##################

	###### DONT EDIT ########################

	private $cookieJS		= '
function setCookie(cname,cvalue)
{
	var d = new Date();
	d.setTime(d.getTime() + (365*24*60*60*1000));
	var expires = "expires="+d.toUTCString();
	document.cookie = cname + "=" + cvalue + "; " + expires;
}

function getCookie(name)
{
	/*
  let matches = document.cookie.match(new RegExp(
    "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, "\\$1") + "=([^;]*)"
  ));
  return matches ? decodeURIComponent(matches[1]) : undefined;
  */
  var match = document.cookie.match(new RegExp("(^| )" + name + "=([^;]+)"));
  if (match) return match[2];

  return undefined;
}

function chkCookie()
{
	if( getCookie( "acceptCookie" ) != "true" ){
		document.getElementById( "cookieBox" ).className = "bottomСookieBlock";
	}
}

function acceptCookie()
{
	setCookie( "acceptCookie", true );
	document.location.reload();
}
'."\n";

	public function __construct()
	{
		if( !isset( $_COOKIE["lang"] ) ){
			//setcookie("lang","en");
			$this->language = "en";
		}else{
			$this->language = $_COOKIE["lang"];
		}

		$this->assetsPath = $_SERVER['DOCUMENT_ROOT'].$this->assetsPath;
	}

	public function getLanguage(){ return $this->language; }
	public function setLanguage( $language = "en" ){ $this->language = $language; }

	public function addScriptFile( $url ){ array_push( $this->jsArray, $url ); }
	public function addCssFile( $url ){ array_push( $this->cssArray, $url ); }

	public function pageTop( $pagetitle )
	{
		print '<!DOCTYPE html>
	<html lang="'.$this->language.'">
		<head>
			<meta name="viewport" content="width=device-width, initial-scale=1.0">
			<meta charset="utf-8"/>
			<META http-equiv="Pragma" content="no-cache">'."\n";
		if( $this->authorContent != "" ){
			print '			<META NAME="Author" CONTENT="'.$this->authorContent.'"/>'."\n";
		}
		if( $this->authorKeywords != "" ){
			print '			<META NAME="keywords" CONTENT="'.$this->authorKeywords.'"/>'."\n";
		}
		if( $this->authorDescription != "" ){
			print '			<META NAME="description" CONTENT="'.$this->authorDescription.'"/>'."\n";
		}
		if( $this->pageIco != "" ){
			print '			<link rel="shortcut icon" href="'.$this->pageIco.'"/>'."\n";
		}
		foreach( $this->cssArray as $file ){
			print '			<link rel=stylesheet type="text/css" href="'.$file.'"/>'."\n";
		}
		foreach( $this->jsArray as $file ){
			print '			<script type="text/javascript" src="'.$file.'"></script>'."\n";
		}
		if( $this->useCookie ){
			print '			<script type="text/javascript">'.$this->cookieJS.'</script>'."\n";
		}
		print '			<title>-= '.$pagetitle.' =-</title>
		</head>';
		if( $this->useCookie ){
			print '		<body onLoad="chkCookie();">'."\n";
		}else{
			print '		<body>'."\n";
		}

		print '<style>.logo,.mainMenu{ margin: auto;text-align: center; }.bottomСookieBlock td{ padding: 10px; }.bottomСookieBlock{	position: fixed;	bottom: 0px;	left: 0px;	background-color: gray;	font-size: 14pt;}.cookieBlockAccept{	padding: 15px;	border: 1px solid orange;	color: orange;	font-weight: bold;	background-color: gray;	cursor: pointer;}</style>';

		if( is_file( $this->assetsPath."/img/logo.png" ) ){
			print '<div class="logo"> <a href="/"><img src="'.$this->assetsPathUrl.'img/logo.png"></a> </div>';
		}

		if( $this->useCookie ){
			print '			<table class="bottomСookieBlock hidden" id="cookieBox" width="100%">
			<tr>
				<td>'.$this->lngAll[$this->language]["cookieBanner"].'</td>
				<td>
					<button class="cookieBlockAccept" onClick="acceptCookie();">OK</button>
				</td>
			</tr>
			</table>'."\n";
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
		<!--<br><i>ООО "Шайтан технолоджи"</i><br>
		<i>при поддержке ЗАО "Мутные схемы" и ОАО "Любовь в займы"</i>!-->
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
