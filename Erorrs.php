<?php
	ini_set('display_errors',1);
	ini_set('error_reporting',-1);

	############################################################################

	

	############################################################################

	// мы будем сами обрабатывать ВСЕ ошибки
	error_reporting(E_ALL);

	/*
	// определенная пользователем функция обработки ошибок
	function userErrorHandler( $errno, $errmsg, $filename, $linenum, $vars )
	{
		// дата и время для записи об ошибке
		$dt = date( "Y-m-d H:i:s (T)" );

		// определение ассоциативного массива строк ошибок
		// на самом деле следует рассматривать только элементы 2,8,256,512 и 1024
		$errortype = array (
			1   =>  "Ошибка",
			2   =>  "Предупреждение",
			4   =>  "Ошибка синтаксического анализа",
			8   =>  "Замечание",
			16  =>  "Ошибка ядра",
			32  =>  "Предупреждение ядра",
			64  =>  "Ошибка компиляции",
			128 =>  "Предупреждение компиляции",
			256 =>  "Ошибка пользователя",
			512 =>  "Предупреждение пользователя",
			1024=>  "Замечание пользователя"
		);
		// набор ошибок, для которого будут сохраняться значения переменных
		$user_errors = array( E_USER_ERROR, E_USER_WARNING, E_USER_NOTICE );

		$err  = $dt." ".$errortype[$errno]." № ".$errno."\n";
		$err .= $errmsg."\n";
		$err .= "Вызов из ".$filename." строка № ".$linenum."\n";

		ob_end_clean();
		ob_start();
		print_r($vars);
		$v = "Переменные:".ob_get_contents()."\n";
		ob_end_clean();
		ob_start("error_callback");

		// сохранить протокол ошибок и отправить его мылом
		// mail('error@htmlweb.ru', 'PHP error report', $err.$v, "Content-Type: text/plain; charset=windows-1251" ) or die("Ошибка при отправке сообщения об ошибке");
		// error_log($err."\n", 3, dirname(__FILE__) . "/log/error.log") or die("Ошибка записи сообщения об ошибке в файл");
	}
*/

	function userErrorHandler( $errno, $msg, $file, $linenum, $vars = "" )
	{
		print "{ error: { 'code': $errno, 'msg': '$msg', 'file': '$file', 'line': $linenum } }";
	}

	######################################
	// Определяем новую функцию-обработчик fatal error.
	function myShutdownHandler()
	{
		if (@is_array($e = @error_get_last())) {
			$code = isset($e['type']) ? $e['type'] : 0;
			$msg = isset($e['message']) ? $e['message'] : '';
			$file = isset($e['file']) ? $e['file'] : '';
			$line = isset($e['line']) ? $e['line'] : '';
			if( $code > 0 ){
				$tmp = explode( "\n", $msg );
				$msg = $tmp[0];
				userErrorHandler( $code, $msg, $file, $line, "" );
			}
		}
	}
	
	######################################

	// Перехват вывода на экран
	//ob_start("myShutdownHandler");
	// Перехват обработки ошибок
	$old_error_handler = set_error_handler( "userErrorHandler" );
	
	register_shutdown_function('myShutdownHandler');
?>
