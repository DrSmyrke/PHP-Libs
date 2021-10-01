# PHP-Libs
My library for PHP


#### My Functions Library Example

```
$mf = new MyFunctions;;
$mf->printDir( $path, $asTable = true, $className = "table" );
$res = $mf->readDirToArray( $dir );
$res = $mf->getMonName( $mon, $language = "en" ); // print month name
$res = $mf->hex_dump( $str ); // print string to hex format
$res = $mf->drawData( $data, $spc = "| &#160;&#160;&#160;", $recursion = 0 ); // draw data (fork var_dump)
$res = $mf->chis( $number ); // get human format to number ex: 1000130 -> 1 000 130
$res = $mf->getSize( $size ); // get human format to bytes
$res = $mf->getAgeFromBirthday( $birthday ); // get full years from birthday date
$mf->removeDirectory( $dir ); //recursive remove directory
$res = $mf->setLog( $mess ); //added date time before message
$res = $mf->xorString( $str, $key ); //encode XOR string from key
$res = $mf->scanDirParam( $dir, $true ); //scan dir recursive param
$mf->dirParamToRepoList( &$string, $data ); //print to string repo file list
```

#### My IMAGE Library Example

```
$mi = new MyImage;
$mi->resizeImage( $filename, $newWidth = 600, $newHeight = 400, $newFile );
$mi->getThumbnail( $origImage, $targetImage = '' ); // if $targetImage == "" thumbnail image sending to browser whith image headers
```
