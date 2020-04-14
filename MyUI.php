<?php
class MyUI
{
	public function __construct()
	{
		
	}

	public function getSwitch1()
	{
		
	}

	public function printProgressBar( $prz, $text = "", $bradius = 0, $bg = "", $fg = "orange, orange", $space = true )
	{
		if( $text == "" ) $text = "{PRZ}%";
		$text = str_replace( "{PRZ}", $prz, $text );
		$text = str_replace( "{UNPRZ}", ( 100 - $prz ), $text );
		$background = ( $bg != "" ) ? 'linear-gradient(to bottom, '.$bg.')' : 'rgba( 0, 0, 0, 0 )';
		$pbarground = ( $fg != "" ) ? 'linear-gradient(to bottom, '.$fg.') no-repeat' : '';
		if( $space ) $pbarground .= " content-box";
		print '<div style="border-radius: '.$bradius.'px;border:1px solid silver;width:100%;padding:3px;font-size:9pt;background:'.$pbarground.', '.$background.';background-size:'.$prz.'% 100%, 100% 100%;">'.$text.'</div>';
	}
}
?>
