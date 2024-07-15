<?php
function Binary_help()
{
	// print "\$binary = new Binary;<br>\n";
}

class Binary
{
	private $success				= false;

	/**
	 * Packing value to unsigned 8 bit value
	 * 
	 * @param integer value
	 * @param boolean value
	 * @return packed binary string
	 */
	static public function packToU8( $value, $to0xString = false )
	{
		$binarydata = pack( 'C*', $value );
		return ( $to0xString ) ? Binary::packBinaryDataToString( $binarydata ) : $binarydata;
	}

	static public function packToU16( $value, $to0xString = false )
	{
		$binarydata = pack( 'n*', $value );
		return ( $to0xString ) ? Binary::packBinaryDataToString( $binarydata ) : $binarydata;
	}

	static public function packToU32( $value, $to0xString = false )
	{
		$binarydata = pack( 'N*', $value );
		return ( $to0xString ) ? Binary::packBinaryDataToString( $binarydata ) : $binarydata;
	}

	static public function unpackToU8Array( &$inData, &$outData )
	{
		$outData = unpack( 'C*', $inData );
	}

	static public function unpackToU16Array( &$inData, &$outData )
	{
		$outData = unpack( 'n*', $inData );
	}

	static public function unpackToU32Array( &$inData, &$outData )
	{
		$outData = unpack( 'N*', $inData );
	}

	static public function packArrayU8( $data, $to0xString = false )
	{
		$binarydata = NULL;

		if( !is_array( $data ) ) return ( $to0xString ) ? Binary::packBinaryDataToString( $binarydata ) : $binarydata;
		foreach( $data as $value ){
			if( $value == '' || $value == NULL ) continue;
			$binarydata .= Binary::packToU8( $value );
		}

		return ( $to0xString ) ? Binary::packBinaryDataToString( $binarydata ) : $binarydata;
	}

	//-------------------------------------------------------------
	static public function packArrayU16( $data, $to0xString = false )
	{
		$binarydata = NULL;

		if( !is_array( $data ) ) return ( $to0xString ) ? Binary::packBinaryDataToString( $binarydata ) : $binarydata;
		foreach( $data as $value ){
			if( $value == '' || $value == NULL ) continue;
			$binarydata .= Binary::packToU16( $value );
		}

		return ( $to0xString ) ? Binary::packBinaryDataToString( $binarydata ) : $binarydata;
	}

	//-------------------------------------------------------------
	static public function packArrayU32( $data, $to0xString = false )
	{
		$binarydata = NULL;

		if( !is_array( $data ) ) return ( $to0xString ) ? Binary::packBinaryDataToString( $binarydata ) : $binarydata;
		foreach( $data as $value ){
			if( $value == '' || $value == NULL ) continue;
			$binarydata .= Binary::packToU32( $value );
		}
		
		return ( $to0xString ) ? Binary::packBinaryDataToString( $binarydata ) : $binarydata;
	}
	
	//-------------------------------------------------------------
	static public function packBinaryDataToString( $binarydata = NULL )
	{
		if( $binarydata == NULL ) return '';
		return '0x'.bin2hex( $binarydata );
	}

	//-------------------------------------------------------------
}
?>
