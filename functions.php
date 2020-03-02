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
	
	rmdir( $dir );
}



?>
