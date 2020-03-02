<?php
class MyFunctions
{
	public function printDir( $path, $asTable = true, $className = "table" )
	{
		if( !is_dir( $path ) ) return;
		if( $asTable ){
			print '<table class="'.$className.'">'."\n";
			foreach( MyFunctions::readDirToArray( $path ) as $key => $value ){
				print '<tr><td colspan="2"><h3>'.basename($key).'</h3></td></tr>';
				foreach( $value as $pkt ){
					print '<tr><td><a href="'.$pkt.'">'.basename($pkt).'</a></td><td class="size">'.getSize(filesize($pkt)).'</td></tr>';
				}
			}
			print '</table>'."\n";
		}
	}

	public function readDirToArray( $dir )
	{
		$data = array();
		foreach( glob("$dir/*") as $elem ){
			if( is_dir( $elem ) ){
				$data["$elem"] = MyFunctions::readDirToArray( $elem );
			}else{
				array_push( $data, $elem );
			}
		}
		return $data;
	}
}
?>
