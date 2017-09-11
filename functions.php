<?php
function getNameMon($mon){
	global $language;
	$ru=array("Январь","Февраль","Март","Апрель","Май","Июнь","Июль","Август","Сентябрь",
"Октябрь","Ноябрь","Декабрь");
	$en=array("January","February","March","April","May","June","July","August","September",
"October","November","December");
	$tmp=($language=="ru")?$ru[$mon-1]:$en[$mon-1];
	return $tmp;
}
# обработка чисел
function chis($val){$dot=false;if(strpos($val,".")){list($val,$dot)=explode(".",$val);}$string=$val;$stmp=strlen($val);if($stmp>3 and $stmp<=6){$t1=substr($val,-3);$t2=substr($val,0,-3);$string="$t2 $t1";}if($stmp>6 and $stmp<=9){$t1=substr($val,-3);$t2=substr($val,-6,3);$t3=substr($val,0,-6);$string="$t3 $t2 $t1";}if($stmp>9 and $stmp<=12){$t1=substr($val,-3);$t2=substr($val,-6,3);$t3=substr($val,-9,3);$t4=substr($val,0,3);$string="$t4 $t3 $t2 $t1";}if($stmp>12 and $stmp<=15){$t1=substr($val,-3);$t2=substr($val,-6,3);$t3=substr($val,-9,3);$t4=substr($val,-12,3);$t5=substr($val,0,3);$string="$t5 $t4 $t3 $t2 $t1";}if($dot){$string.=".".$dot;}return $string;}
# обработка размера
function getSize($sz){global $language;$mass=array("bytes","Kb","Mb","Gb");if($language=="ru"){$mass=array("байт(а)","Кб","Мб","Гб");}if($sz<1024){$st="$sz  $mass[0]";}else{if($sz>1024 and $sz<1024000){$sz=substr($sz/1024,0,5);$st="$sz $mass[1]";}else{if($sz>1024000 and  $sz<1024000000){$sz=substr($sz/1048576,0,5);$st="$sz $mass[2]";}else{if($sz>1024000000){$sz=substr($sz/1048576000,0,5);$st="$sz $mass[3]";}}}}return $st;}
function drawData($data,$spc,$recursion){
	if($spc==null){$spc="| &#160;&#160;&#160;";}
	if($recursion==null){$recursion=0;}
	foreach($data as $key=>$val){
		for($i=0;$i<$recursion;$i++){print "$spc ";}
		print "[$key=>$val]";
		if(gettype($val)=="array"){
			print " (".count($val).")<br>";
			drawData($val,$spc,$recursion+1);
		}else{print "<br>";}
	}
}
?>
