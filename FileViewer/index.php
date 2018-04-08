<?php
	
	$dir = "files";
	
	$cmd = null;
	$target = null;
	$file = null;
	if( $_POST["c"] != "" ) $cmd = $_POST["c"];
	if( $_POST["t"] != "" ) $target = $_POST["t"];
	if( $_POST["f"] != "" ) $file = $_POST["f"];
	if( $_GET["t"] != "" ) $target = $_GET["t"];

	
/*

';
#	<source src="video.webm" type="video/webm"><!-- WebM/VP8 для Firefox4, Opera, и Chrome -->
#	<source src="video.ogv" type="video/ogg"><!-- Ogg/Vorbis для старых версий браузеров Firefox и Opera -->

*/
		$name = substr($nameOrig, 0, $countSymName);
		if( strlen( $name ) != strlen( $elem ) ){
			$name = substr($nameOrig, 0, $countSymName - 3 )."...";
		}
		
		$ico = "file";
	if( $cmd == "back" ){
		$dir = "$dir$target";
		$count = 0;
		$fileNum = 0;
		$type = "";
		$path = "";
		$write = true;
		if( is_dir( $dir ) ){
			if( is_file( "$dir/$file" ) ){
				foreach(glob("$dir/*") as $elem){
					if(is_file($elem)){
						$count++;
						if( $elem == "$dir/$file" ) $write = false;
						if( $write ){
							$fileNum = $count;
							$type = "image";
							$tmp = explode(".",$elem);
							$rashir = array_pop( $tmp );
							$rashir = strtolower($rashir);
							if($rashir == "mp4") $type = "video";
							if($rashir == "avi") $type = "video";
							$path = $elem;
						}
					}
				}
			}
		}
		if($path && $type) print 'open:'.$fileNum.':'.$count.':'.$type.':'.$path;
		die;
	}
	if( $cmd == "next" ){
		$dir = "$dir$target";
		$count = 0;
		$fileNum = 0;
		$type = "";
		$path = "";
		$next = false;
		if( is_dir( $dir ) ){
			if( is_file( "$dir/$file" ) ){
				foreach(glob("$dir/*") as $elem){
					if(is_file($elem)){
						$count++;
						if( $elem == "$dir/$file" ){
							$next = true;
							continue;
						}
						if( $next ){
							$next = false;
							$fileNum = $count;
							$type = "image";
							$tmp = explode(".",$elem);
							$rashir = array_pop( $tmp );
							$rashir = strtolower($rashir);
							if($rashir == "mp4") $type = "video";
							if($rashir == "avi") $type = "video";
							$path = $elem;
						}
					}
				}
			}
		}
		if($path && $type) print 'open:'.$fileNum.':'.$count.':'.$type.':'.$path;
		die;
	}
	if( $cmd == "open" ){
		$dir = "$dir$target";
		$count = 0;
		$fileNum = 0;
		$type = "";
		$path = "";
		if( is_dir( $dir ) ){
			if( is_file( "$dir/$file" ) ){
				foreach(glob("$dir/*") as $elem){
					if(is_file($elem)){
						$count++;
						if( $elem == "$dir/$file" ){
							$fileNum = $count;
							$type = "image";
							$tmp = explode(".",$elem);
							$rashir = array_pop( $tmp );
							$rashir = strtolower($rashir);
							if($rashir == "mp4") $type = "video";
							if($rashir == "avi") $type = "video";
							$path = "$dir/$file";
						}
					}
				}
			}
		}
		print 'open:'.$fileNum.':'.$count.':'.$type.':'.$path;
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
	cursor:default;
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
.download_btn{
	display: inline-block;
	text-align: center;
	border: none;
	cursor: pointer;
	font-weight: 700;
	float: left;
	padding: 11px 14px 14px 14px;
}
.icon:hover{
	-moz-transition: 0.4s;
	-ms-transition: 0.4s;
	-webkit-transition: 0.4s;
	-o-transition: 0.4s;
	transition: 0.4s;
	-moz-transform: scale( 1.15 ); /* Для Firefox */
	-ms-transform: scale( 1.15 ); /* Для IE */
	-webkit-transform: scale( 1.15 ); /* Для Safari, Chrome, iOS */
	-o-transform: scale( 1.15 ); /* Для Opera */
	transform: scale( 1.15 );
}
.close_icon{
	background: url(img/close.png) no-repeat;
	padding: 0px 18px 2px 0;
	margin: 0;
	font-weight: 700;
}
.download_icon{
	background: url(img/download.png) no-repeat;
	padding: 0px 18px 2px 0;
	margin: 0;
}
.topText{
	padding: 10px 14px 0px 12px;
}
.fileViewer{
	width:100%;
	height:300px;
	overflow:auto;
}
</style>

<section class="border"></section>

<div class="fileViewer">
	<?php
	print "<br>";
	printPath($target);
	print "<br><br>";
	printDir( "$dir$target" );
	?>
</div>

<div id="viewer" onselectstart="return false" onmousedown="return false">
	<div id="player" unselectable="on"  onselectstart="return false;" onMouseDown="moveState=true;initMove(this, event);return false;" onmouseup="moveCancel(this, event);" onmousemove="moveHandler(this, event);" onMouseOut="moveCancel(this, event);"></div>
	
	<div class="panel topPanel">
		<a class="close_btn icon" onClick="closeViewer();">
			<i class="close_icon"></i>
		</a>
		<div class="topText">
			<span id="currentFileNumField"></span> из <span id="totalFilesField"></span>
			<span style="padding-left:30px;" id="fileName">FILENAME</span>
		</div>
	</div>
	<div class="panel bottomPanel">
		<a class="download_btn icon" onClick="alert();">
			<i class="download_icon"></i>
		</a>
	</div>
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
var script = <?php $tmp = explode("/", $_SERVER["SCRIPT_NAME"]); print "'".array_pop($tmp)."'"; ?>;
var moveState = false;
var x0, y0;
var moveTo = "";
var openViewerFlag = false;

request.onreadystatechange=function(){
	if (request.readyState==4 && request.status == 200) {
		var tmp = request.responseText.split(":");
		if( tmp[0] == "open" ){
			document.getElementById("currentFileNumField").innerHTML = tmp[1];
			document.getElementById("totalFilesField").innerHTML = tmp[2];
			if( tmp[3] == "image" ){
				document.getElementById("player").innerHTML = '<img src="'+ tmp[4] +'">';
			}
			if( tmp[3] == "video" ){
				document.getElementById("player").innerHTML = '<video controls width="400" height="300"><!-- MP4 для Safari, IE9, iPhone, iPad, Android, и Windows Phone 7 --><source src="?c=get&t='+ tmp[4] +'" type="video/mp4"><!-- добавляем видеоконтент для устаревших браузеров, в которых нет поддержки элемента video --><object data="flvplayer.swf?url='+ tmp[4] +'&width=400&height=300" type="application/x-shockwave-flash"><param name="movie" value="flvplayer.swf?url='+ tmp[4] +'&width=400&height=300"></object></video>';
			}
			document.getElementById("fileName").innerHTML = tmp[4].split("/").pop();
		}
	}
}
document.onkeydown=function(event){
	if( !openViewerFlag ) return;
	switch( event.keyCode ){
		case 37: prewFile( 0 ); break;			//LEFT
		case 39: nextFile( 0 ); break;			//RIGHT
		case 38: closeViewerUP(); break;		//UP
		case 40: closeViewerDOWN(); break;		//DOWN
		case 27: closeViewer(); break;			//ESC
	}
}

function openViewer( file )
{
	openViewerFlag = true;
	fileNameField.innerHTML = file;
	viewer.style.visibility = "visible";
	viewer.style.opacity = 1;

	request.open('POST', script,true);
	request.setRequestHeader('Content-type','application/x-www-form-urlencoded');
	str="c=open&t=" + folder+"&f="+file;
	request.send(str);
}
function closeViewerUP()
{
	setYMove(0,-1000);
	closeViewer();
}
function closeViewerDOWN()
{
	setYMove(0,1000);
	closeViewer();
}
function closeViewer()
{
	setTimeout("viewer.style.opacity = .75;",50);
	setTimeout("viewer.style.opacity = .50;",100);
	setTimeout("viewer.style.opacity = .25;",150);
	setTimeout("viewer.style.visibility = \"hidden\";player.innerHTML = \"\";fileNameField.innerHTML = \"\";openViewerFlag = false;",250);
}
function nextFile( x )
{
	if( document.getElementById("currentFileNumField").innerHTML == document.getElementById("totalFilesField").innerHTML ) return;
	setXMove(x,-1500);
	setTimeout("setXMove(1500,0);",500);
	request.open('POST', script,true);
	request.setRequestHeader('Content-type','application/x-www-form-urlencoded');
	str="c=next&t=" + folder+"&f="+document.getElementById("fileName").innerHTML;
	request.send(str);
}
function prewFile(x)
{
	if( document.getElementById("currentFileNumField").innerHTML == 1 ) return;
	setXMove(x,1500);
	setTimeout("setXMove(-1500,0);",500);
	request.open('POST', script,true);
	request.setRequestHeader('Content-type','application/x-www-form-urlencoded');
	str="c=back&t=" + folder+"&f="+document.getElementById("fileName").innerHTML;
	request.send(str);
}
function setXMove(x,target)
{
	var steep = 200;
	if( x < target ) x += steep;
	if( x > target ) x -= steep;
	player.style = "overflow: visible; transform: rotate(0deg) translate3d(" + x + "px, 0px, 0px); transition: none 0s ease 0s;";
	if( Math.abs( x - target ) < steep ) x = target;
	if( x != target ) setTimeout("setXMove("+x+","+target+");",1);
}
function setYMove(y,offset)
{
	var steep = 100;
	if( y < offset ) y += steep;
	if( y > offset ) y -= steep;
	player.style = "overflow: visible; transform: rotate(0deg) translate3d(0px, " + y + "px, 0px); transition: none 0s ease 0s;";
	if( y != offset ){
		setTimeout("setYMove("+y+","+offset+");",10);
	}else{
		player.style = "overflow: visible; transform: rotate(0deg) translate3d(0px, 0px, 0px); transition: none 0s ease 0s;";
	}
}
function initMove(div, event) {
    var event = event || window.event;
	x0 = event.clientX;
	y0 = event.clientY;
    moveState = true;
}
function moveHandler(div, event) {
    var event = event || window.event;
	if (moveState) {
		rx = Math.abs(event.clientX-x0);
		ry = Math.abs(event.clientY-y0);
		chkMoveTo(rx, ry);
		rx = (event.clientX-x0);
		ry = (event.clientY-y0);
		moveAnimation(rx, ry);
    }
}
function moveCancel(div, event) {
    var event = event || window.event;
	var rx = event.clientX-x0;
	var ry = event.clientY-y0;
	if (moveState) moveEnd(rx, ry);
}
function chkMoveTo(absRX, absRY)
{
	if(moveTo == "" && (absRX > 30 || absRY > 30)){
		if(absRX > absRY){
			moveTo = "x";
		}else{
			moveTo = "y";
		}
	}
}
function moveAnimation(rx, ry)
{
	if(moveTo == "x"){
		player.style = "overflow: visible; transform: rotate(0deg) translate3d("+rx+"px, 0px, 0px); transition: none 0s ease 0s;";
	}else{
		player.style = "overflow: visible; transform: rotate(0deg) translate3d(0px, "+ry+"px, 0px); transition: none 0s ease 0s;";
	}
}
function moveEnd(rx, ry)
{
	if(moveTo == "y" && Math.abs(ry) >= 200) closeViewer();
	if(moveTo == "x" && (rx) <= -300) nextFile(rx);
	if(moveTo == "x" && (rx) >= 300) prewFile(rx);
	player.style = "transform: rotate(0deg) translate3d(0px, 0px, 0px); transition: none 0s ease 0s;";
	moveTo = "";
	moveState = false;
}
/****************************************/
/********** Touch screen mode  **********/
/****************************************/
var initialPoint;
var currentPoint;

player.addEventListener('touchstart', function(event) {
	event.preventDefault();		//исключить возникновение стандартных реакций на действия курсора мыши со стороны браузера
	//event.stopPropagation();
	initialPoint=event.changedTouches[0];
	moveState = true;
}, false);

player.addEventListener('touchmove', function(event) {
	currentPoint=event.changedTouches[0];
	if( moveState ){
		var rx = Math.abs(currentPoint.pageX - initialPoint.pageX);
		var ry = Math.abs(currentPoint.pageY - initialPoint.pageY);
		chkMoveTo(rx, ry);
		rx = (currentPoint.pageX - initialPoint.pageX);
		ry = (currentPoint.pageY - initialPoint.pageY);
		moveAnimation(rx, ry);
	}
}, false);

player.addEventListener('touchend', function(event) {
	currentPoint=event.changedTouches[0];
	var rx = (currentPoint.pageX - initialPoint.pageX);
	var ry = (currentPoint.pageY - initialPoint.pageY);
	if (moveState) moveEnd(rx, ry);
}, false);

</script>
<?php	pageBottom();	?>



<?php
function printPath( $dir )
{
	$array = explode( "/", $dir );
	$tmp = null;
	foreach( $array as $elem ){
		if( $elem == "" ){
			print '<input type="button" value="/" onClick="document.location.href=\'?\';"> '."\n";
			continue;
		}
		print ' <input type="button" value="'.$elem.'" onClick="document.location.href=\'?t=/'.$tmp.$elem.'\';">'."\n";
		$tmp .= $elem."/";
	}
}
function printDir( $dir )
{
	global $target;
	$dirs = array();
	$files = array();
	$countSymName = 12;

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
		$name = substr($elem, 0, $countSymName);
		if( strlen( $name ) != strlen( $elem ) ){
			$name = substr($name, 0, $countSymName - 3 )."...";
		}
		print '<div class="elem"><a href="?t='.$target.'/'.$elem.'" title="'.$elem.'"><img src="img/folder.png" onMouseOver="this.src=\'img/folder-open.png\';" onMouseOut="this.src=\'img/folder.png\';"><br>'.$name.'</a></div>'."\n";
	}
	foreach($files as $elem){
		$tmp = explode("/", $elem);
		$file = array_pop($tmp);
		$tmp = explode(".",$file);
		$type = array_pop($tmp);
		$nameOrig = join(".",$tmp);
		$name = substr($nameOrig, 0, $countSymName);
		if( strlen( $name ) != strlen( $nameOrig ) ){
			$name = substr($nameOrig, 0, $countSymName - 3 )."...";
		}
		$type = strtolower($type);
		$ico = "file";
		if($type == "mp4") $ico = "video";
		if($type == "avi") $ico = "video";
		print '<div class="elem" onClick="openViewer(\''.$elem.'\');" title="'.$elem.'"><img src="img/'.$ico.'.png"><br>'.$name.'</div>'."\n";
	}
}
/*
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
*/
?>
