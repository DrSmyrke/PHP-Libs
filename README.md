# PHP-Libs
My library for PHP


#### My IMAGE Library Example

```
$mi = new MyImage;
$mi->resizeImage( $filename, $newWidth = 600, $newHeight = 400, $newFile );
$mi->getThumbnail( $origImage, $targetImage = '' );
// if $targetImage == "" thumbnail image sending to browser whith image headers
```
