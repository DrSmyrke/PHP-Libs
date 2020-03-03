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
					print '<tr><td><a href="'.$pkt.'">'.basename($pkt).'</a></td><td class="size">'.MyFunctions::getSize(filesize($pkt)).'</td></tr>';
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

	public function hex_dump( $str )
	{
		return substr(preg_replace('#.#se','sprintf("%02x ",ord("$0"))',$str),0,-1);
	}

	public function drawData( $data, $spc = "| &#160;&#160;&#160;", $recursion = 0 )
	{
		if( !is_array( $data ) ){
			var_dump($data);
			return;
		}
		foreach( $data as $key => $val ){
			for( $i = 0; $i < $recursion; $i++ ){
				print "$spc ";
			}
			print "[$key=>";
			if( gettype( $val ) == "array" ){
				print "Array] (".count( $val ).")<br>";
				MyFunctions::drawData( $val, $spc, $recursion+1 );
			}else{
				print "$val]<br>";
			}
		}
	}

	public function chis( $val )
	{
		$dot = false;
		if( strpos( $val, "." ) ){
			list( $val, $dot ) = explode( ".", $val );
		}
		$string = "";
		$len = strlen( $val );
		$counter = 0;
		for( $i = $len - 1; $i > 0; $i-- ){
			if( $i % 3 ) $string = " ".$string;
			$string = $val[$i].$string;
		}
		/*
		if( $stmp > 3 and $stmp <= 6 ){
			$t1 = substr( $val, -3 );
			$t2 = substr( $val, 0, -3 );
			$string = "$t2 $t1";
		}
		if( $stmp > 6 and $stmp <= 9){
			$t1 = substr( $val, -3 );
			$t2 = substr( $val, -6, 3 );
			$t3 = substr( $val, 0, -6 );
			$string = "$t3 $t2 $t1";
		}
		if($stmp>9 and $stmp<=12){$t1=substr($val,-3);$t2=substr($val,-6,3);$t3=substr($val,-9,3);$t4=substr($val,0,3);$string="$t4 $t3 $t2 $t1";}if($stmp>12 and $stmp<=15){$t1=substr($val,-3);$t2=substr($val,-6,3);$t3=substr($val,-9,3);$t4=substr($val,-12,3);$t5=substr($val,0,3);$string="$t5 $t4 $t3 $t2 $t1";}
		*/
		if( $dot ) $string .= ".".$dot;
		return $string;
	}

	public function getSize( $size = 0 )
	{
		global $language;
		$mass = array( "bytes", "Kb", "Mb", "Gb" );
		if( $language == "ru" ){
			$mass = array( "байт(а)", "Кб", "Мб", "Гб" );
		}

		$index = 0;
		
		if( $size < 1024 ){
			return "$size  $mass[0]";
		}else{
			do{
				$count = round( $size / 1024 );
				$size -= $count * 1024;
				$index++;
			}while( $count > 0 );
			return $st = "$size  ".$mass[$index];
		}
		
		/*
		if( $sz < 1024 ){
			$st = "$sz  $mass[0]";
		}else{
			if( $sz > 1024 and $sz < 1024000){
				$sz = substr( $sz/1024, 0, 5 );
				$st = "$sz $mass[1]";
			}else{
				if( $sz > 1024000 and  $sz < 1024000000 ){
					$sz = substr( $sz/1048576, 0, 5 );
					$st = "$sz $mass[2]";
				}else{
					if( $sz > 1024000000 ){
						$sz = substr( $sz/1048576000, 0, 5 );
						$st = "$sz $mass[3]";
					}
				}
			}
		}
		*/
		return $size;
	}
}
?>