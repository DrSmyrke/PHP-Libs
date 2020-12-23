<?php
function myimage_help()
{
	print "\$mi = new MyImage;<br>\n";
	print "\$mf->resizeImage( filename, newWidth = 600, newHeight = 400, newFile );<br>\n";
}

class MyImage
{
	public function resizeImage( $filename, $newWidth = 600, $newHeight = 400, $newFile = "" )
	{
		//Open image
	
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
	
		//Change size image
	
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
	
		// Save or view image
	
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
}
?>
