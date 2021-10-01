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
	
		imagedestroy( $img );
	}

	public function getThumbnail( $origImage, $targetImage = '' )
	{
		$info		= GetImageSize( $origImage 	);
		$exif		= exif_read_data( $origImage );
		$width		= $info[0];
		$height		= $info[1];
		$r			= $width / $height;
		$img		= null;

		$newWidth	= 640;
		$newHeight	= $newWidth / $r;

		switch( $info[2] ){
			case IMAGETYPE_GIF:
				//header('content-type: image/gif');
				$img = imagecreatefromgif( $origImage );
				//imagegif($dst);
			break;
			case IMAGETYPE_JPEG:
				//header('content-type: image/jpeg');
				$img = imagecreatefromjpeg( $origImage );
				//imagejpeg($dst);
			break;
			case IMAGETYPE_PNG:
				//header('content-type: image/png');
				$img = imagecreatefrompng( $origImage );
				//imagepng($dst);
			break;
		}

		if( $img == null ){
			return;
		}

		//Pictures that are rotated using EXIF, will show up in the original orientation
		if( $exif && isset( $exif['Orientation'] ) ){
			$ort = $exif['Orientation'];
			if ($ort == 6 || $ort == 5){
				$img = imagerotate( $img, 270, null );
				$tmp = $newWidth;
				$newWidth = $newHeight;
				$newHeight = $tmp;
				$tmp = $width;
				$width = $height;
				$height = $tmp;
			}else if( $ort == 3 || $ort == 4 ){
				$img = imagerotate( $img, 180, null );
			}else if( $ort == 8 || $ort == 7 ){
				$img = imagerotate( $img, 90, null );
				$tmp = $newWidth;
				$newWidth = $newHeight;
				$newHeight = $tmp;
				$tmp = $width;
				$width = $height;
				$height = $tmp;
			}
                
			if( $ort == 5 || $ort == 4 || $ort == 7 ) imageflip( $img, IMG_FLIP_HORIZONTAL );
		}

		$dst = imagecreatetruecolor( $newWidth, $newHeight );

		if( $targetImage == "" ) header('content-type: image/jpeg');

		imagecopyresampled( $dst, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height );

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

		imagedestroy( $img );
		imagedestroy( $dst );
	}
}
?>
