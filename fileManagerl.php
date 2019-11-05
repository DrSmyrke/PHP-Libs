<?php
function fileManager_help()
{
	//print "$yaml = new Yaml;<br>\n";
}



class FileManager
{
	private $fileIco		= "";
	private $folderIco		= "";
	private $folderOpenIco	= "";
	
	public function setFileIco( $path ){ $this->fileIco = $path; }
	public function setFolderIco( $path ){ $this->folderIco = $path; }
	public function setFolderOpenIco( $path ){ $this->folderOpenIco = $path; }
	
	public function printAssets()
	{
		global $thisPage;
		print '<style>.filemanager_fileIco{ height: 32px; vertical-align:middle; }</style>';
		print '<script>
			var FileManager_updateBox;
			var requestFileManager = makeHttpObject();
			requestFileManager.onreadystatechange=function(){
				if (requestFileManager.readyState==4 && requestFileManager.status == 200) {
					var tmp = requestFileManager.responseText.split(":>:");
					if( tmp[0] == "content") FileManager_updateBox.innerHTML = tmp[1];
				}
			}
			function FileManager_openDir(dir, updaterBox){
				var updBox = document.getElementById( updaterBox );
				FileManager_updateBox = updBox;
				updBox.innerHTML = \'<span class="valorange">Processing...</span>\';
					
				str = "fileManagerData[cmd]=showDir&fileManagerData[dir]=" + dir;
				
				requestFileManager.open( "POST", \''.$thisPage.'\', true );
				requestFileManager.setRequestHeader(\'Content-type\',\'application/x-www-form-urlencoded\');
				requestFileManager.send( str );
			}
			function FileManager_newDir(form, updaterBox){
				var x = prompt(\'Please enter name directory\', \'\');
				if( x ){
					var e = document.createElement("INPUT");
					e.type = "hidden";
					e.name = "fileManagerData[newDir]";
					e.value = x;
					form .appendChild(e);
					
					changeParam( form, updaterBox );
				}
				
				return false;
			}
		</script>';
	}
	
	public function showDir( $dir, $pathHide, $updateScript = "?", $updateBox = "" )
	{
		if( !is_dir( $dir ) ) return;
		
		global $thisPage;
		
		print '<table class="table" cellspacing="0"><tr><th>Name</th><th>Size</th><th>Upload</th><th></th></tr>';
		
		$rootDir = str_replace( $pathHide, "", $dir );
		if( $rootDir == "/" ) $rootDir = "";
		
		foreach( glob( $dir."/*" ) as $elem ){
			$tmp = explode( "/", $elem );
			$name = array_pop( $tmp );
			$size = filesize( $elem );
			$time = filectime( $elem );
			$action = "";
			
			$add = ( is_dir( $elem ) ) ? '<a href="javascript:void(0);" onClick="FileManager_openDir( \''.$rootDir.'/'.$name.'\', \''.$updateBox.'\' );"><img class="filemanager_fileIco" src="'.$this->folderIco.'"> '.$name.'</a>' : '<a href="'.$elem.'" target="_blank"><img class="filemanager_fileIco" src="'.$this->fileIco.'"> '.$name.'</a>';
			if( is_file( $elem ) && filesize( $elem ) > 0 ){
				$info   = getimagesize( $elem );
				$width  = $info[0];
				$height = $info[1];
				$type   = $info[2];
				switch ($type) { 
					case 2|3: $add = '<a href="'.$elem.'" target="_blank"><img class="filemanager_fileIco" src="'.$elem.'"> '.$name.'</a>'; break;
				}
			}
			
			if( is_writable( $elem ) ){
				$action = '<form class="form" action="'.$thisPage.'" onSubmit="return changeParam( this, \''.$updateBox.'\' );"><input type="hidden" name="fileManagerData[cmd]" value="removeElem"> <input type="hidden" name="fileManagerData[dir]" value="'.$rootDir.'"> <input type="hidden" name="fileManagerData[elem]" value="'.$name.'"> <input type="submit" value="REMOVE" onClick="return confirm(\'Continue delete?\');">  </form>';
			}
			
			print '<tr><td>'.$add.'</td><td>'.getSize($size).'</td><td>'.date('d.m.Y',$time).'</td><td>'.$action.'</td></tr>';
		}
		print '</table>';
	}
	
	public function printPath( $path, $updateBox = "", $change = false )
	{
		global $thisPage;
		
		$list = explode( "/", $path );
		if( count( $list ) > 0 ){
			if( $list[0] == ".." ) $list[0] = "";
			if( $list[0] == "" ) $list[0] = "/";
		}
	
		if( count( $list ) == 2 ){
			if( $list[0] == "/" && $list[1] == "" ) return;
		}
		
		print '<table class="table" cellspacing="0"><tr><td>';
		
		$first = true;
		$path = "";
		
		foreach( $list as $elem ){
			if( $elem == "" ) continue;
			if( $first ){
				print '<a href="javascript:void(0);" onClick="FileManager_openDir( \''.$elem.'\', \''.$updateBox.'\' );"><img class="filemanager_fileIco" src="'.$this->folderOpenIco.'"></a> ';
				$path = "/";
				$first = false;
				continue;
			}
			
			
			print ' / ';
			
			print '<a href="javascript:void(0);" onClick="FileManager_openDir( \''.$path.$elem.'\', \''.$updateBox.'\' );"><img class="filemanager_fileIco" src="'.$this->folderOpenIco.'">'.$elem.'</a> ';
			
			$path .= $elem."/";
		}
		
		if( $change ){
			print '</td><td align="right">';
				print '<form class="form" action="'.$thisPage.'" onSubmit="return FileManager_newDir( this, \''.$updateBox.'\' );">';
					print '<input type="hidden" name="fileManagerData[cmd]" value="createDir">';
					print '<input type="hidden" name="fileManagerData[dir]" value="'.$path.'">';
					print '<input type="submit" value="NEW DIR"> ';
				print '</form>';
				print '<form class="form" action="'.$thisPage.'" onSubmit="return changeParam( this, \''.$updateBox.'\' );">';
					print '<input type="hidden" name="fileManagerData[cmd]" value="uploadFiles">';
					print '<input type="hidden" name="fileManagerData[dir]" value="'.$path.'">';
					print '<input type="file" id="FileManager_uploadB" name="userfile[]" accept="image/png, .txt, .ini" onChange="uploadForm( this.form, \''.$updateBox.'\' );" multiple>';
					print '<label style="order: 1;" for="FileManager_uploadB" class="valorange">UPLOAD</label>';
				print '</form>';
		}
		
		print '</td></tr></table>';
	}
}
?>
