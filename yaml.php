<?php
function saveYaml($file,$data){
	$str=parsDataYaml($data,null,0);
	$fs=fopen($file,"w");
	if(!fwrite($fs,$str)){fclose($fs);return false;}
	fclose($fs);
	return true;
}
function parsDataYaml($data,$spc,$recursion){
	$string="";
	if($spc==null){$spc=" ";}
	if($recursion==null){$recursion=0;}
	foreach($data as $key=>$val){
		if(is_int($key) and substr($val,0,1)=="#"){
			for($i=0;$i<$recursion;$i++){$string.="$spc ";}
			$string.="$val\n";continue;
		}
		if(is_int($key)){
			for($i=0;$i<$recursion-1;$i++){$string.="$spc ";}
			$string.="- $val\n";continue;
		}
		if(gettype($val)=="string" or gettype($val)=="integer"){
			for($i=0;$i<$recursion;$i++){$string.="$spc ";}
			$string.="$key: $val\n";continue;
		}
		if(gettype($val)=="array"){
			for($i=0;$i<$recursion;$i++){$string.="$spc ";}
			$string.="$key:\n";
			$string.=parsDataYaml($val,$spc,$recursion+1);
		}
	}
	return $string;
}
function parsYaml($file){
	$fs=file($file);
	$data=array();
	$prew=null;
	foreach($fs as $str){
		$str=str_replace(array("\n","\r"),"",$str);
		$str=str_replace("  "," ",$str);
		if(substr($str,0,1)=="#" or $str==""){setComentYaml($data,$str);continue;}
		$level=0;while(substr($str,$level,1)==" "){$level++;}
		$str=substr($str,$level);
		if(substr($str,-1,1)==":"){setArrayYaml($data,$prew,$str,$level);continue;}
		list($p,$v)=explode(": ",$str);
		if($p!="" and $v!=""){setPeremYaml($data,$prew,$p,$v,$level);continue;}
		if(substr($str,0,2)=="- "){setListYaml($data,$prew,$str,$level);continue;}
	}
	return $data;
}
function setListYaml(&$dt,$pr,$string,$level){
	$string=substr($string,2);
	$string=str_replace("\"","'",$string);
	parsUrlYaml($dt,$pr,$string,"",$level+1,"list");
}
function setPeremYaml(&$dt,&$pr,$p,$v,$level){
	$tmp=null;for($i=0;$i<$level;$i++){$tmp[]=$pr[$i];}$pr=$tmp;
	$v=str_replace("\"","'",$v);
	parsUrlYaml($dt,$pr,$p,$v,$level,"perem");
}
function setArrayYaml(&$dt,&$pr,$string,$level){
	$string=substr($string,0,-1);
	$tmp=null;for($i=0;$i<$level;$i++){$tmp[]=$pr[$i];}$pr=$tmp;
	$pr[]=$string;
}
function setComentYaml(&$dt,$string){$dt[]=$string;}
function parsUrlYaml(&$dt,$pr,$per,$val,$level,$type){
	$tmp="";for($i=0;$i<$level;$i++){$tmp.='["'.$pr[$i].'"]';}
	switch($type){
		case "list":
			eval("\$dt$tmp"."[]=\"$per\";");
			#print("L [".join("/",$pr)."] \$dt[".($dindex-1)."]$tmp"."[]='$per';<br>");
			break;
		case "perem":
			eval("\$dt$tmp"."[\"$per\"]=\"$val\";");
			#print("P [".join("/",$pr)."] \$dt$tmp"."[\"$per\"]=\"$val\";<br>");
			break;
	}
}
?>
