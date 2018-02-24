<?php
	
	$dir = "/mnt/photo";
	
	$cmd = null;
	$target = null;
	if( $_POST["c"] != "" ) $cmd = $_POST["c"];
	if( $_POST["t"] != "" ) $target = $_POST["t"];
	if( $_GET["c"] != "" ) $cmd = $_GET["c"];
	if( $_GET["t"] != "" ) $target = $_GET["t"];

	

	if( $cmd == "open" ){
		$file = "$dir$target";
		if( is_file( $file ) ){
			list($name,$type)=explode(".",$file);
			$type = strtolower($type);
			$str = "File parsing error";
			if($type == "jpg"){
				$str = '<img src="?c=get&t='.$target.'">';
			}
			if($type == "mp4"){
				$str = '
<video controls width="400" height="300">
	<!-- MP4 для Safari, IE9, iPhone, iPad, Android, и Windows Phone 7 -->
	<source src="?c=get&t='.$target.'" type="video/mp4">
	<!-- добавляем видеоконтент для устаревших браузеров, в которых нет поддержки элемента video -->
	<object data="video.swf" type="application/x-shockwave-flash">
		<param name="movie" value="video.swf">
	</object>
</video>';
#	<source src="video.webm" type="video/webm"><!-- WebM/VP8 для Firefox4, Opera, и Chrome -->
#	<source src="video.ogv" type="video/ogg"><!-- Ogg/Vorbis для старых версий браузеров Firefox и Opera -->
			}
			print $str;
		}
		die;
	}
	if( $cmd == "get" ){
		$file = "$dir$target";
		if( is_file( $file ) ){
			$fname=array_pop(explode("/",$target));
			print "$fname,$file";
			file_download($file,$fname);
		}
		die;
	}

	foreach(glob("../../data/libs/*.php") as $str){include $str;}
	pagetop("Фотки","../../");

?>
<style>
body{
	overflow:hidden;
}
.elem{
	cursor:pointer;
	display:inline-block;
	text-align:center;
	/*
	border:1px solid black;
	*/
	width:100px;
	vertical-align:top;
}
#viewer{
	position:fixed;
	left:0px;
	top:0px;
	width:100%;
	height:100%;
	background:black;
	visibility:hidden;
}
#player{
	padding:2px;
	width:100%;
	height:100%;
	text-align:center;
	vertical-align:middle;
}
#player:after{
    content: '';
    display: inline-block;
    height: 100%;
    vertical-align: middle;
}
#player img{
	max-width: 100%;
	max-height: 100%;
	margin: auto;
	vertical-align: middle;
}
#player video{
	max-width: 100%;
	max-height: 100%;
	margin: auto;
	vertical-align: middle;
}
.panel{
	background: rgba(59, 59, 59, 0.45) !important;
	position:fixed;
	color:white;
	text-shadow:1px 1px 0px black;
	font-size: 16px;
	font-weight: bold;
	text-align: left;
}
.topPanel{
	width:100%;
	border-bottom: 1px solid rgba(67,67,67,.85);
	height: 43px !important;
	left:0px;
	top:0px;
}
.bottomPanel{
	width:100%;
	border-top: 1px solid rgba(67,67,67,.85);
	height: 43px !important;
	left:0px;
	bottom:0px;
}
.close_btn{
	display: inline-block;
	text-align: center;
	border: none;
	cursor: pointer;
	font-weight: 700;
	float: right;
	padding: 11px 14px 14px 14px;
}
.close_icon{
	background: url(img/icons.png?2) no-repeat;
	background-position: -108px;
	padding: 2px 18px 2px 0;
	margin: 0;
	font-weight: 700;
}
.topText{
	padding: 10px 14px 0px 12px;
}
</style>

<section class="border"></section>

<div onselectstart="return false" onmousedown="return false">
	<?php
	print "DIR: [$target]<br>";
	printDir( "$dir$target" );
	?>
</div>

<div id="viewer">
	<div class="panel topPanel">
		<a class="close_btn" onClick="closeViewer();">
			<i class="close_icon"></i>
		</a>
		<div class="topText">
			1 из <span id="totalFilesField"></span>
			<span style="padding-left:30px;" id="fileName">FILENAME</span>
		</div>
	</div>
	<div id="player" onselectstart="return false" onmousedown="return false"></div>
	<div class="panel bottomPanel"></div>
</div>










<script type="text/javascript">

function makeHttpObject() {
	try {return new XMLHttpRequest();}
	catch (error) {}
	try {return new ActiveXObject("Msxml2.XMLHTTP");}
	catch (error) {}
	try {return new ActiveXObject("Microsoft.XMLHTTP");}
	catch (error) {}
	throw new Error("Could not create HTTP request object.");
}

var request=makeHttpObject();
var viewer = document.getElementById("viewer");
var player = document.getElementById("player");
var fileNameField = document.getElementById("fileName");
var folder = <?php print "'$target'"; ?>;

request.onreadystatechange=function(){
	if (request.readyState==4 && request.status == 200) {
		player.innerHTML=request.responseText;
	}
}

function openViewer( file )
{
	fileNameField.innerHTML = file;
	totalFilesField.innerHTML = totalFiles;
	viewer.style.visibility = "visible";
	request.open('POST', "index.php",true);
	request.setRequestHeader('Content-type','application/x-www-form-urlencoded');
	str="c=open&t=" + folder + "/" + file;
	request.send(str);
}
function closeViewer()
{
	viewer.style.visibility = "hidden";
	player.innerHTML = "";
	fileNameField.innerHTML = "";
}
//function resize()
//{
	//var w = window.innerWidth;
	//var h = window.innerHeight;
//	var w = parseInt(getComputedStyle(player).width.replace("px",""));
//	var h = parseInt(getComputedStyle(player).height.replace("px",""));
//	var imgW = parseInt(getComputedStyle(player.firstChild).width.replace("px",""));
//	var imgH = parseInt(getComputedStyle(player.firstChild).height.replace("px",""));
	//alert(w + "X" + h + " [" + imgW + "/" + imgH + "]");
	//if( imgW > w ){
	//	player.firstChild.style.width = w +'px';
	//	alert(getComputedStyle(player).width + "X" + getComputedStyle(player).height + " [" + getComputedStyle(player.firstChild).width + "/" + getComputedStyle(player.firstChild).height + "]");
	//}else{
	//alert("12e3");
//}
/*
	var imgR = imgW / imgH;
	
	if( imgW > w ){
		player.firstChild.style.width = w +'px';
		if( imgH > window.innerHeight ){
			player.firstChild.style.width = w +'px';
			player.style.height = h + 'px';
			//alert( getComputedStyle(player.firstChild).width + "X" + getComputedStyle(player.firstChild).height );
		}
	}
*/
	//if( getComputedStyle(player.firstChild).height.replace("px","") > window.innerHeight ){
	//	player.firstChild.style.height = '100%';
	//	alert( getComputedStyle(player.firstChild).width + "x" + getComputedStyle(player.firstChild).height );
	//}
	//if( getComputedStyle(player).width >= getComputedStyle(player).height ){
	//	player.firstChild.style.width = "100%";
	//}else{
	//	player.firstChild.style.height = "100%";
	//}
	//alert( getComputedStyle(player.firstChild).width + "x" + getComputedStyle(player.firstChild).height );
	//player.firstChild.style.width = "10%";
	//alert( getComputedStyle(player).width );
	//alert( player.firstChild.tagName );
//}
/*
var str;
var infoVal;
var request=makeHttpObject();
request.onreadystatechange=function(){
	if (request.readyState==4 && request.status == 200) {
		infoVal.innerHTML=request.responseText;
		//delete  request;
	}
}
function getInfo(elem,id){
	request.open('POST', "index.php",true);
	request.setRequestHeader('Content-type','application/x-www-form-urlencoded');
	str="cmd=getData&data="+elem;
	request.send(str);
	var infoBox=document.getElementById("info"+id);
	infoVal=infoBox;
	infoVal.style.visibility="visible";
	infoVal.innerHTML="UPDATE...";
}
function out(id){
	var obj=document.getElementById("info"+id);
	obj.style.visibility="hidden";
}
*/
</script>
<?php	pageBottom();	?>



<?php
function printDir( $dir ){

	$dirs = array();
	$files = array();

	foreach(glob("$dir/*") as $elem){
		if(array_pop(explode("/",$elem))=="index.php") continue;
		$fname=array_pop(explode("/",$elem));
		if(is_dir($elem)){
			array_push($dirs,$fname);
			continue;
		}
		if(is_file($elem)) array_push($files,$fname);
	}

	foreach($dirs as $elem){
		print '<div class="elem"><img src="img/folder.png" onMouseOver="this.src=\'img/folder-open.png\';" onMouseOut="this.src=\'img/folder.png\';"><br>'.$elem.'</div>';
	}
	foreach($files as $elem){
		list($name,$type)=explode(".",$elem);
		$type = strtolower($type);
		$ico = "file";
		if($type == "mp4") $ico = "video";
		print '<div class="elem" onClick="openViewer(\''.$elem.'\');"><img src="img/'.$ico.'.png"><br>'.$name.'</div>';
	}
	
	print '<script>';
	print 'var totalFiles = '.count($files).';';
	print '</script>';
}
function file_download($file,$fileName)
{
	if(!file_exists($file)) return;
	// сбрасываем буфер вывода PHP, чтобы избежать переполнения памяти выделенной под скрипт
	// если этого не сделать файл будет читаться в память полностью!
	if (ob_get_level()) ob_end_clean();
	// заставляем браузер показать окно сохранения файла
	header('Content-Description: File Transfer');
	header('Content-Type: application/octet-stream');
	if($fileName == ""){
		header('Content-Disposition: attachment; filename=' . basename($file));
	}else{
		header('Content-Disposition: attachment; filename=' . $fileName);
	}
	header('Content-Transfer-Encoding: binary');
	header('Expires: 0');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');
	header('Content-Length: ' . filesize($file));
	// читаем файл и отправляем его пользователю
	readfile($file);
}
?>
