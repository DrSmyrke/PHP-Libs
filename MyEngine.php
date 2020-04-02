<?php
class MyEngine
{
	private $language			= "en";
	private $pageIco			= "";
	private $useCookie			= true;
	private $assetsPath			= "/my/www/ASSETS";
	###### EDIT MANUAL ##################
	private $authorContent		= "Прокофьев Юрий (Prokofiev Jura)";
	private $authorKeywords		= "Прокофьев Юрий,портфолио,Мои работы,Мои проекты, Программы, Свободное программное обеспечение";
	private $authorDescription	= "Персональная страничка -= Dr.Smyrke =-";
	private $authorGenerator	= "Gedit";
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
		
		if( !is_dir( "data" ) ) mkdir( "data", 775, true );
		if( !is_dir( "data/img" ) ) mkdir( "data/img", 775, true );
		if( !is_dir( "data/img/sites_icons" ) ) mkdir( "data/img/sites_icons", 775, true );
		
		if( is_dir( "data" ) ){
			if( !is_file( "data/buttons.css" ) ) copy( $this->assetsPath."/buttons.css", "data/buttons.css" );
			if( !is_file( "data/switchers.css" ) ) copy( $this->assetsPath."/switchers.css", "data/switchers.css" );
			if( !is_file( "data/drsmyrke.css" ) ) copy( $this->assetsPath."/drsmyrke.css", "data/drsmyrke.css" );
			if( !is_file( "data/color.css" ) ) copy( $this->assetsPath."/color.css", "data/color.css" );
			if( !is_file( "data/animate.css" ) ) copy( $this->assetsPath."/animate.css", "data/animate.css" );
		}
		if( is_dir( "data/img" ) ){
			if( !is_file( "data/img/my.png" ) ) copy( $this->assetsPath."/img/my.png", "data/img/my.png" );
			if( !is_file( "data/img/fon.png" ) ) copy( $this->assetsPath."/data/img/fon.png", "data/img/fon.png" );
			if( !is_file( "data/img/line.png" ) ) copy( $this->assetsPath."/data/img/line.png", "data/img/line.png" );
		}
		if( is_dir( "data/img/sites_icons" ) ){
			$icons = array( "hh_ru.svg", "icq.png", "skype.png", "stihi.svg", "SuperJob.png", "thingiverse.png", "vk.png", "youtube.png", "cults-3d.svg", "Patreon.png" );
			foreach( $icons as $ico ){
				if( !is_file( $this->assetsPath."/img/sites_icons/$ico" ) ) continue;
				if( !is_file( "data/img/sites_icons/$ico" ) ) copy( $this->assetsPath."/img/sites_icons/$ico", "data/img/sites_icons/$ico" );
			}
		}
	}

	public function getLanguage(){ return $this->language; }
	public function setLanguage( $language = "en" ){ $this->language = $language; }

	public function pageTop( $pagetitle, $assetsPath )
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
		if( $this->authorGenerator != "" ){
			print '			<META NAME="Generator" CONTENT="'.$this->authorGenerator.'"/>'."\n";
		}
		foreach( glob( $assetsPath."data/*.css" ) as $file ){
			print '			<link rel=stylesheet type="text/css" href="'.$file.'"/>'."\n";
		}
		if( $this->pageIco != "" ){
			print '			<link rel="shortcut icon" href="'.$this->pageIco.'"/>'."\n";
		}
		foreach( glob( $assetsPath."data/*.js" ) as $file ){
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
		
		print '<style>.logo,.mainMenu{ margin: auto;text-align: center; }</style>';
		print '<div class="logo"> <a href="/"><img src="/data/img/my.png"></a> </div>';
		
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

	public function pageBottom()
	{
		print '
	<hr>
	<div class="block mylinks">
		<div class="legend">'.$this->lngAll[$this->language]["links"].'</div>
		<div class="content">
			<a href="https://trudvsem.ru/cv/card/print/ef130d60-a2e2-11e5-b24a-833b590698f7/b9de76c0-a300-11e5-9ca9-0163a9ae3d01" target="_blank" class="button"><img src="https://trudvsem.ru/assets/img/header-text.png" title="Труд всем"></a>
			<a href="https://www.superjob.ru/resume/inzhener-8175571.html" target="_blank" class="button"><img src="/data/img/sites_icons/SuperJob.png" title="Super Job.ru"></a>
			<a href="https://hh.ru/resume/544bce82ff00c8582c0039ed1f447369476b73" target="_blank" class="button"><img src="/data/img/sites_icons/hh_ru.svg" title="Head Hunter"></a>
			<a href="http://www.icq.com/people/238114708" target="_blank" class="button"><img src="/data/img/sites_icons/icq.png" title="ICQ: 238114708"></a>
			<a href="http://vk.com/drsmyrke" target="_blank" class="button"><img src="/data/img/sites_icons/vk.png" title="VK: drsmyrke"></a>
			<a href="callto:drsmyrke" class="button"><img src="/data/img/sites_icons/skype.png" title="Skype: drsmyrke"></a>
			<a href="http://www.youtube.com/channel/UCkxJ1_cSxlUMPkZ3GB400Wg" target="_blank" class="button"><img src="/data/img/sites_icons/youtube.png" title="YouTube"></a>
			<!-- #style:     fill: currentColor; / для темного фона	!-->
			<a href="https://github.com/DrSmyrke" target="_blank" class="button"><svg aria-hidden="true" title="GitHub" height="32" version="1.1" viewBox="0 0 16 16" width="32"><path fill-rule="evenodd" d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27.68 0 1.36.09 2 .27 1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.013 8.013 0 0 0 16 8c0-4.42-3.58-8-8-8z"></path></svg></a>
			<a href="https://www.stihi.ru/avtor/drsmyrke" target="_blank" class="button"><img src="/data/img/sites_icons/stihi.svg" title="Стихи.ру"></a>
			<a href="https://www.thingiverse.com/DrSmyrke" target="_blank" class="button"><img src="/data/img/sites_icons/thingiverse.png" title="Thingiverse"></a>
			<a href="https://cults3d.com/en/users/DrSmyrke/creations" target="_blank" class="button"><img src="/data/img/sites_icons/cults-3d.svg" title="Cults 3d"></a>
			<a href="https://www.patreon.com/DrSmyrke" target="_blank" class="button"><img src="/data/img/sites_icons/Patreon.png" title="Patreon"></a>
		</div>
	</div>
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
