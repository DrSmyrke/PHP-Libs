<?php
function bits_help()
{
	print "\$bits = new Bits;<br>\n";
	print "\$res = Bits::checkBit( value, bitNum ); //return true or false<br>\n";
	print "\$res = Bits::setBit( &value, bitNum );<br>\n";
	print "\$res = Bits::resetBit( &value, bitNum );<br>\n";
	print "\$res = Bits::invertBit( &value, bitNum );<br>\n";
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

	/**
	 * Set bit in number
	 * 
	 * @param number value
	 * @param number bit number (0..7)
	 * @return notching
	 */
	static public function setBit( &$value, $bitNum )
	{
		$value |= ( 1 << $bitNum );
	}

	/**
	 * Reset bit in number
	 * 
	 * @param number value
	 * @param number bit number (0..7)
	 * @return notching
	 */
	static public function resetBit( &$value, $bitNum )
	{
		$value &= ~( 1 << $bitNum );
	}

	/**
	 * Invert bit in number
	 * 
	 * @param number value
	 * @param number bit number (0..7)
	 * @return notching
	 */
	static public function invertBit( &$value, $bitNum )
	{
		$value ^= ( 1 << $bitNum );
	}
}
?>
