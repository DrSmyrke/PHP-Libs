<?php
function DrString_help()
{
	// print "\$class = new DrString;<br>\n";
}

class DrString
{
	private $debug							= false;

	//----------------- --------------------------------------------
	public function __construct( $debug = false )
	{
		$this->debug						= $debug;
	}

	/**
	 * Packing value to unsigned 8 bit value
	 * @param integer value
	 * @param boolean value
	 * @return packed binary string
	 */
	static public function compare( &$string1, &$string2, $smart = true, $percent = true )
	{
		$similar_symbols					= Array(
			'n' => 'ñ', 'e' => 'é', 'a' => 'á', 'i' => 'í', 'u' => 'ú', '?' => '¿',
			'!' => '¡',
		);
		$s1_length							= strlen( $string1 );
		$s2_length							= strlen( $string2 );
		// Каждый символ строки s1 сравнивается со всеми символами в s2 на допустимом расстоянии d
		$delta								= (int)( max( $s1_length, $s2_length ) / 2 )- 1;
		//число совпадающих символов
		$m									= 0;
		// количество транспозиций
		$tr									= 0;
		// длина общего префикса от начала строки до максимума 4-х символов
		$preffics_length					= 0;
		//Порог усиления
		$bt									= 0.7;
		// постоянный коэффициент масштабирования, использующийся для того, чтобы скорректировать оценку в сторону повышения для выявления наличия общих префиксов. p не должен превышать 0,25, поскольку в противном случае расстояние может стать больше, чем 1. Стандартное значение этой константы в работе Винклера: p=0.1;
		$p									= 0.1;
		$result								= 0;
		$buffer								= Array();

		for( $i1 = 0; $i1 < $s1_length; $i1++ ){
			// $sym1 = $string1[ $i1 ];
			$sym1 = mb_substr( $string1, $i1, 1 );

			for( $i2 = 0; $i2 < $s2_length; $i2++ ){
				if( $i2 < ( $i1 - $delta ) ) continue;
				if( $i2 > ( $i1 + $delta ) ) break;
				// $sym2 = $string2[ $i2 ];
				$sym2 = mb_substr( $string2, $i2, 1 );

				// Добавляем проверку похожих символов
				if( $sym1 != $sym2 ){
					$ssym1 = ( isset( $similar_symbols[ $sym1 ] ) ) ? $similar_symbols[ $sym1 ] : '';
					$ssym2 = ( isset( $similar_symbols[ $sym2 ] ) ) ? $similar_symbols[ $sym2 ] : '';
					// print $sym1.'->'.$sym2.'/'.$ssym1.'->'.$ssym2.'<br>';
					if( $ssym1 == $sym2 ){
						$sym1 = $ssym1;
						// print $sym1.'->'.$sym2.'<br>';
					}else if( $ssym2 == $sym1 ){
						$sym2 = $ssym2;
						// print $sym1.'<-'.$sym2.'<br>';
					}
				}

				if( $sym1 == $sym2 ){
					if( isset( $buffer[ $i2 ] ) ){
						// на этой позиции текущий сивол строки s1 был найден ранее
						// продолжаем поиск в строке s2
						if( $buffer[ $i2 ] == $sym1 ) continue;
					}else{
						// фиксируем позицию найденного символа в строке s2
						$buffer[ $i2 ] = $sym1;
						$m++;
						// транспозиция
						if( $i1 != $i2 ){
							//--(не совпали порядковые номера
							$tr++;
						}
						// считаем префикс (до 4-х)
						if( $i1 == $i2 ){
							if( $i1 == 0 ){
								$preffics_length = 1;
							}else{
								if( $preffics_length < 4 && $i1 == $preffics_length ){
									$preffics_length++;
								}
							}
						}

						break;
					}
				}
			}
		}

		// расчет коэффициента
		if( $m > 0 ){
			// половина количества транспозиций
			$t = (int)( $tr / 2 );
			// Расстояние Джаро
			$dj = ( $m / $s1_length + $m / $s2_length + ( $m - $t ) / $m ) / 3;
			if( $dj >= $bt ){
				// Расстояние Джаро-Винклера
				$result = $dj + ( $preffics_length * $p * ( 1 - $dj ) );
			}else{
				// dj < bt - меньше "порога усиления" применения префиксного бонуса
				$result = $dj;
			}
		}

		return ( $percent ) ? $result * 100 : $result;
	}

	//-------------------------------------------------------------
	// static public function packArrayU16( $data, $to0xString = false )
	// {
	// 	$binarydata = NULL;

	// 	if( !is_array( $data ) ) return ( $to0xString ) ? '0x'.bin2hex( $binarydata ) : $binarydata;
	// 	foreach( $data as $value ){
	// 		if( $value == '' || $value == NULL ) continue;
	// 		$binarydata .= Binary::packToU16( $value );
	// 	}

	// 	return ( $to0xString ) ? '0x'.bin2hex( $binarydata ) : $binarydata;
	// }

	//-------------------------------------------------------------
}
?>
