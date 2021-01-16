<?php

function yaml_help()
{
	print "\$yaml = new Yaml;<br>\n";
	print "\$yaml->saveToFile( \"1.yml\", dataArray );<br>\n";
	print "\$dataArray = \$yaml->loadFile( \"1.yml\" );<br>\n";
}



class Yaml
{
	public function loadFile( $fileName )
	{
		$data = array();
		$prew = &$data;
		$prewLevel = 0;
		if( !is_file( $fileName ) ) return $data;
		$fs = file( $fileName );

		$keys = array();

		foreach( $fs as $str ){
			$str = str_replace( array( "\n", "\r" ), "", $str );

			$level = 0;
			$firstSym = substr( $str, 0, 1 );
			if( $firstSym == " " || $firstSym == "	" ){
				if( $firstSym == " " ){
					while( substr( $str, $level, 1 ) == " " ) $level++;
					$str = substr( $str, $level );
					$level /= 2;
				}
				if( $firstSym == "	" ){
					while( substr( $str, $level, 1 ) == "	" ) $level++;
					$str = substr( $str, $level );
				}
			}



			if( strpos( $str, ":" ) !== false ){
				$tmp = explode( ":", $str );
				$key = array_shift( $tmp );
				if( count( $tmp ) > 1 ){
					$value = join( ":", $tmp );
				}else{
					$value = array_shift( $tmp );
				}
				if( strlen( $value ) > 1 && substr( $value, 0, 1 ) == " " ) $value = substr( $value, 1 );

				if( $value == " " || $value == "" ) $value = array();

				if( $prewLevel > $level ){
					$r = $prewLevel - $level;
					for( $i = 0; $i < $r; $i++ ) array_pop( $keys );
					if( is_array( $value ) ) array_push( $keys, $key );
				}

				if( $level > $prewLevel || $level == $prewLevel ){
					if( is_array( $value ) ) array_push( $keys, $key );
				}

				$prew = &$data;
				foreach( $keys as $k ){
					if( isset( $prew[$k] ) ) $prew = &$prew[$k];
				}

				if( !is_array( $value ) ) $value = str_replace( "\"", "", $value );
				if( is_string( $value ) ){
					$lowerValue = strtolower( $value );
					if( $lowerValue == "true" ) $value = true;
					if( $lowerValue == "false" ) $value = false;
				}

				if( is_float( $value ) )	$value = Float( $value );
				if( is_int( $value ) )		$value = Int( $value );

				if( is_string( $value ) ){
					if( substr( $value, 0, 1 ) == "[" && substr( $value,-1 ) == "]" ){
						$value = substr( $value, 1, -1 );
						$value = $this->constructArray( explode( ", ", $value ) );
					}
				}

				$prew[$key] = $value;

				if( $level != $prewLevel ) $prewLevel = $level;
				continue;
			}

			if( substr( $str, 0, 2 ) == "- " ){
				array_push( $prew[$key], substr( $str, 2 ) );
				if( $level != $prewLevel ) $prewLevel = $level;
			}
		}


		return $data;
	}

	private function constructArray( $dataArray = array() )
	{
		$res		= array();

		foreach( $dataArray as $value ){
			if( is_numeric( $value )){
				$value += 1;
				$value -= 1;
				array_push( $res, $value );
			}else{
				if( is_bool( $value ) ){
					array_push( $res, Bool( $value ) );
					continue;
				}
				if( is_string( $value ) ){
					array_push( $res, $value );
					continue;
				}
			}
		}

		return $res;
	}

	public function saveToFile( $fileName, $data = array(), $separator = "  " )
	{
		if( !is_array( $data ) ) return;
		$fs = fopen( $fileName, "w" );
		if( $fs ){
			fwrite( $fs, Yaml::serializeData( $data, $separator ) );
			fclose( $fs );
		}
	}

	public function serializeData( $data, $spc = "  ", $recursion = 0 )
	{
		$res = "";
		if( !is_array( $data ) ) return $res;

		foreach($data as $key => $val){
			for( $i = 0; $i < $recursion; $i++ ) $res .= "$spc";
			$res .= "$key: ";
			if(gettype($val)=="array"){
				$res .= "\n";
				$res .= Yaml::serializeData( $val, $spc, $recursion+1 );
			}else{
				if(  is_numeric( $val ) ){
					$res .= "$val\n";
				}else{
					if( is_bool( $val ) ){
						if( $val ){
							$res .= "true\n";
						}else{
							$res .= "false\n";
						}
					}else{
						$res .= "\"$val\"\n";
					}
				}
			}
		}

		return $res;
	}
}

?>
