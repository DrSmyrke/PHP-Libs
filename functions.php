<?php
function resizeImage($filename, $newWidth = 600, $newHeight = 400, $newFile)
{
	//Открытие изображения
	
	$info   = getimagesize($filename);
	$width  = $info[0];
	$height = $info[1];
	$type   = $info[2];
	
	switch ($type) { 
		case 1: 
			$img = imageCreateFromGif($filename);
			imageSaveAlpha($img, true);
		break;					
		case 2: 
			$img = imageCreateFromJpeg($filename);
		break;
		case 3: 
			$img = imageCreateFromPng($filename); 
			imageSaveAlpha($img, true);
		break;
	}
	
	//Изменение размера изображения
	
	if (empty($newWidth)) $newWidth = ceil($newHeight / ($height / $width));
	if (empty($newHeight)) $newHeight = ceil($newWidth / ($width / $height));

	$tmp = imageCreateTrueColor($newWidth, $newHeight);

	if ($type == 1 || $type == 3) {
		imagealphablending($tmp, true); 
		imageSaveAlpha($tmp, true);
		$transparent = imagecolorallocatealpha($tmp, 0, 0, 0, 127); 
		imagefill($tmp, 0, 0, $transparent); 
		imagecolortransparent($tmp, $transparent);    
	}   

	$tw = ceil($newHeight / ($height / $width));
	$th = ceil($newWidth / ($width / $height));

	if ($tw < $newWidth) {
		imageCopyResampled($tmp, $img, ceil(($newWidth - $tw) / 2), 0, 0, 0, $tw, $newHeight, $width, $height);        
	}else{
		imageCopyResampled($tmp, $img, 0, ceil(($newHeight - $th) / 2), 0, 0, $newWidth, $th, $width, $height);    
	}            

	$img = $tmp;
	
	// сохранение или вывод
	
	if( $newFile == "" ){
		switch ($type) {
			case 1: 
				header('Content-Type: image/gif'); 
				imageGif($img);
			break;			
			case 2: 
				header('Content-Type: image/jpeg');
				imageJpeg($img, null, 100);
			break;			
			case 3: 
				header('Content-Type: image/x-png');
				imagePng($img);
			break;
		}
	}else{
		switch ($type) {
			case 1: 
				imageGif($img, $newFile);
			break;			
			case 2: 
				imageJpeg($img, $newFile, 100);
			break;			
			case 3: 
				imagePng($img, $newFile);
			break;
		}
	}
	
	imagedestroy($img);
}

function fileUploadCodeToMessage($code)
{
	$message = "N/A";
	switch ($code) {
		case UPLOAD_ERR_INI_SIZE:
			$message = "The uploaded file exceeds the upload_max_filesize directive in php.ini";
		break;
		case UPLOAD_ERR_FORM_SIZE:
			$message = "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
		break;
		case UPLOAD_ERR_PARTIAL:
			$message = "The uploaded file was only partially uploaded";
		break;
		case UPLOAD_ERR_NO_FILE:
			$message = "No file was uploaded";
		break;
		case UPLOAD_ERR_NO_TMP_DIR:
			$message = "Missing a temporary folder";
		break;
		case UPLOAD_ERR_CANT_WRITE:
			$message = "Failed to write file to disk";
		break;
		case UPLOAD_ERR_EXTENSION:
			$message = "File upload stopped by extension";
		break;
		default:
			$message = "Unknown upload error";
		break;
	}
	return $message;
}

function getMonName($mon, $language = "en"){
	switch( $mon ){
		case 1: return ( $language == "ru" ) ? "Январь" : "January" ;
		case 2: return ( $language == "ru" ) ? "Февраль" : "February" ;
		case 3: return ( $language == "ru" ) ? "Март" : "March" ;
		case 4: return ( $language == "ru" ) ? "Апрель" : "April" ;
		case 5: return ( $language == "ru" ) ? "Май" : "May" ;
		case 6: return ( $language == "ru" ) ? "Июнь" : "June" ;
		case 7: return ( $language == "ru" ) ? "Июль" : "July" ;
		case 8: return ( $language == "ru" ) ? "Август" : "August" ;
		case 9: return ( $language == "ru" ) ? "Сентябрь" : "September" ;
		case 10: return ( $language == "ru" ) ? "Октябрь" : "October" ;
		case 11: return ( $language == "ru" ) ? "Ноябрь" : "November" ;
		case 12: return ( $language == "ru" ) ? "Декабрь" : "December" ;
		default: return "---";
	}
}


function chis($val){$dot=false;if(strpos($val,".")){list($val,$dot)=explode(".",$val);}$string=$val;$stmp=strlen($val);if($stmp>3 and $stmp<=6){$t1=substr($val,-3);$t2=substr($val,0,-3);$string="$t2 $t1";}if($stmp>6 and $stmp<=9){$t1=substr($val,-3);$t2=substr($val,-6,3);$t3=substr($val,0,-6);$string="$t3 $t2 $t1";}if($stmp>9 and $stmp<=12){$t1=substr($val,-3);$t2=substr($val,-6,3);$t3=substr($val,-9,3);$t4=substr($val,0,3);$string="$t4 $t3 $t2 $t1";}if($stmp>12 and $stmp<=15){$t1=substr($val,-3);$t2=substr($val,-6,3);$t3=substr($val,-9,3);$t4=substr($val,-12,3);$t5=substr($val,0,3);$string="$t5 $t4 $t3 $t2 $t1";}if($dot){$string.=".".$dot;}return $string;}

function getSize($sz){global $language;$mass=array("bytes","Kb","Mb","Gb");if($language=="ru"){$mass=array("байт(а)","Кб","Мб","Гб");}if($sz<1024){$st="$sz  $mass[0]";}else{if($sz>1024 and $sz<1024000){$sz=substr($sz/1024,0,5);$st="$sz $mass[1]";}else{if($sz>1024000 and  $sz<1024000000){$sz=substr($sz/1048576,0,5);$st="$sz $mass[2]";}else{if($sz>1024000000){$sz=substr($sz/1048576000,0,5);$st="$sz $mass[3]";}}}}return $st;}

function drawData( $data, $spc = "| &#160;&#160;&#160;", $recursion = 0 )
{
	if( !is_array( $data ) ){
		var_dump($data);
		return;
	}
	foreach($data as $key => $val){
		for($i=0;$i<$recursion;$i++){print "$spc ";}
		print "[$key=>";
		if(gettype($val)=="array"){
			print "Array] (".count($val).")<br>";
			drawData($val,$spc,$recursion+1);
		}else{print "$val]<br>";}
	}
}

function hex_dump($str)
{
  return substr(preg_replace('#.#se','sprintf("%02x ",ord("$0"))',$str),0,-1);
}

function setLog($mess)
{
	global $userName,$adConnect;
	return date("d.m.Y [H:i:s]")." ".$mess." ".getNameAsLogin( $adConnect, $userName )."\n";
}

function getAgeFromBirthday($birthday)
{
	if( $birthday == "" ) return 0;
	$years = intval( (strtotime('now') -  strtotime($birthday)) / 60 / 60 / 24 / 365 );
	return $years;
}



function removeDirectory($dir)
{
	if( !is_dir( $dir ) ) return;
	
	foreach( glob( "$dir/*" ) as $path){
		if( is_dir( $path ) ){
			removeDirectory( $path );
		}else{
			unlink( (string)$path );
		}
	}
	
	rmdir( $path );
}



?>