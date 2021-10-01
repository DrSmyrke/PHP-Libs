<?php
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

	public function getThumbnail( $origImage, $targetImage = '' )
	{
		$info = GetImageSize( $origImage 	);
		$width = $info[0];
		$height = $info[1];
		if( $width > $height ){
			$r = $height / $width;
		}else{
			$r = $width / $height;
		}

		$nw = 640;
		$nh = $nw * $r;

		$dst = imagecreatetruecolor( $nw, $nh );


		switch($info[2]){
			case IMAGETYPE_GIF:
				//header('content-type: image/gif');
				$src = imagecreatefromgif( $origImage );
				//imagegif($dst);
			break;
			case IMAGETYPE_JPEG:
				//header('content-type: image/jpeg');
				$src = imagecreatefromjpeg( $origImage );
				//imagejpeg($dst);
			break;
			case IMAGETYPE_PNG:
				//header('content-type: image/png');
				$src = imagecreatefrompng( $origImage );
				//imagepng($dst);
			break;
		}

		if( $targetImage == "" ) header('content-type: image/jpeg');

		imagecopyresampled( $dst, $src, 0, 0, 0, 0, $nw,$nh,$width,$height );

		if( $targetImage == "" ){
			imagejpeg( $dst );
		}else{
			switch( $info[ 2 ] ){
				case IMAGETYPE_GIF:
					imagegif( $dst, $targetImage );
				break;
				case IMAGETYPE_JPEG:
					imagejpeg( $dst, $targetImage );
				break;
				case IMAGETYPE_PNG:
					imagepng( $dst, $targetImage );
				break;
			}
		}

		imagedestroy( $src );
		imagedestroy( $dst );
	}
}
?>
