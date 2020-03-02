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

	public function getMonName( $mon, $language = "en" )
	{
		$ru = array( "Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь" );
		$en = array( "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December" );

		if( $mon == 0 ){
			return "----";
		}
		
		if( $mon > 12 || $mon < 1 ) $mon = 1;

		$mon--;
		
		switch ($language){
			case "en":	return $en[$mon];	break;
			case "ru":	return $ru[$mon];	break;
			default:	return "----"; 		break;
		}
	}
}
?>
