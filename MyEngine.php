<?php
class MyEngine
{
	private $language			= "en";
	private $pageIco			= "";
	private $useCookie			= false;
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
}
document.body.onload=function()
{
	chkCookie();
};';

	public function __construct()
	{
		/*
		$language="en";
		if(!isset( $_COOKIE["lang"] )){
			setcookie("lang","en");
			$language = "en";
		}else{
			$language = $_COOKIE["lang"];
		}
		*/
	}

	public function getLanguage(){ return $this->language; }

	public function init( $language = "en", $pageIco = "", $useCookie = false )
	{
		$this->language			= $language;
		$this->pageIco			= $pageIco;
		$this->useCookie		= $useCookie;
	}

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
		</head>
		<body>'."\n";
		
		MyEngine::printMyCSS();
		
		if( $this->useCookie ){
			print '			<table class="bottomСookieBlock hidden" id="cookieBox" width="100%">
			<tr>
				<td>'.$this->lngAll[$this->language]["cookieBanner"].'</td>
				<td>
					<button class="cookieBlockAccept" onClick="acceptCookie();">OK</button>
				</td>
			</tr>
			</table>';
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

	private function printMyCSS()
	{
		print '<style>\n';

		print '*, *:before, *:after { transition: .25s ease-in-out; }';
		print '.madeBy i{ color: gray; fint-style: italic; font-size:8pt; }';
		print '.madeBy{ text-align: center; }';
		print '@media only screen and (max-width: 950px){ .block { width: 95%; margin: 25px auto; display: block; max-height: none; overflow: none; } }';
		print '@media only screen and (max-width: 1170px){ .content, .logo {width: 95%;} }';
		print '/* @media only screen and (orientation:portrait){ .menu .a {display: block; width: 95%; margin: auto; margin-bottom: 5px;} } */';
		print '.hidden{ display: none; }';
		print 'hr{ border: 0px dashed; height: 1px; background-image: -webkit-linear-gradient(left, #fff, #000, #fff); background-image: -moz-linear-gradient(left, #fff, #000, #fff); background-image: -ms-linear-gradient(left, #fff, #000, #fff); background-image: -o-linear-gradient(left, #fff, #000, #fff); }';

		print '</style>\n';
	}
}
?>
