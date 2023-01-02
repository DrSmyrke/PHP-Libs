<?php
function bits_help()
{
	print "\$bits = new Bits;<br>\n";
	// print "\$res = \$sql->init( serverAddr, userName, password, dataBase ); //return true or error<br>\n";
	// print "\$res = \$sql->init( serverAddr, userName, password, dataBase ); //return true or error<br>\n";
}

class Bits
{
	private $success				= false;

	/**
	 * Checking bit in number
	 * 
	 * @param number value
	 * @param number bit number (0..7)
	 * @return true if bit as set
	 */
	static public function checkBit( $value, $bitNum )
	{
		return ( $value & ( 1 << $bitNum ) ) ? true : false;
	}
}
?>
