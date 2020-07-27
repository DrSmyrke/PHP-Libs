<?php
$NBT_types=array("TAG_END","TAG_BYTE","TAG_SHORT","TAG_INT","TAG_LONG","TAG_FLOAT",
"TAG_DOUBLE","TAG_BYTE_ARRAY","TAG_STRING","TAG_LIST","TAG_COMPOUND",
"TAG_INT_ARRAY");
function LoadNBTFile($filename){
	$nbtdata=array();
	$fp=fopen("compress.zlib://{$filename}","rb");
	readTag($fp,$nbtdata);
	fclose($fp);
	return $nbtdata;
}
function WriteNBTFile($filename,$nbtdata){
	$fp=fopen("compress.zlib://{$filename}","wb");
	$tmp=writeTag($fp,$nbtdata[0]);
	fclose($fp);
	return $tmp;
}
function writeTag($fp,$tag)
{
	$tagType = ( isset( $tag["type"] ) ) ? $tag["type"] : "";
	$tagName = ( isset( $tag["name"] ) ) ? $tag["name"] : "";
	$tagValue = ( isset( $tag["value"] ) ) ? $tag["value"] : "";
	#print "[$tagType,$tagName,$tagValue]<br>";
	writeType($fp,1,$tagType);
	writeType($fp,8,$tagName);
	return writeType($fp,$tagType,$tagValue);
#	return writeType($fp,1,$tag["type"]) && writeType($fp,8,$tag["name"]) && writeType($fp,$tag["type"],$tag["value"]);
}
function writeType($fp,$typeid,$value){
	global $NBT_types;
	$tagType=$NBT_types[$typeid];
	#print "Write [type=>$tagType [$typeid],value=>$value]<br>";
	switch($tagType){
		case "TAG_LONG":
			$data =_intstring2signedlong($value);
			return fwrite($fp,$data);
		case "TAG_LIST":
			if(!(writeType($fp,1,$value["type"]) && writeType($fp,3,count($value["value"])))) return false;
			foreach($value["value"] as $listItem) if(!writeType($fp,$value["type"],$listItem)) return false;
				return true;
		case "TAG_DOUBLE":
			return fwrite($fp,(pack('d',1) == "\77\360\0\0\0\0\0\0")?pack('d',$value):strrev(pack('d',$value)));
		case "TAG_FLOAT":
			return fwrite($fp,(pack('d',1) == "\77\360\0\0\0\0\0\0")?pack('f',$value):strrev(pack('f',$value)));
		case "TAG_BYTE_ARRAY":
			return writeType($fp,3,count($value)) && fwrite($fp,call_user_func_array("pack",array_merge(array("c".count($value)),$value)));
		case "TAG_INT":
			if($value<0) $value+=pow(2,32);
			return fwrite($fp,pack("N",$value));
		case "TAG_COMPOUND":
			foreach($value as $listItem) if(!writeTag($fp,$listItem)) return false;
			if(!fwrite($fp,"\0")) return false;
			return true;
		case "TAG_SHORT":
			if($value<0) $value+=pow(2,16);
			return fwrite($fp,pack("n",$value));
		case "TAG_STRING":
			$value=utf8_encode($value);
			return writeType($fp,2,strlen($value)) && fwrite($fp,$value);
		case "TAG_BYTE":
			return fwrite($fp,pack("c",$value));
		case "TAG_INT_ARRAY":
                	return writeType($fp,3,count( $value )) && is_int( fwrite( $fp,call_user_func_array( "pack",array_merge( array( "N" . count( $value )),$value ))));
	}
}

function readTag($fp,&$pdata){
	global $NBT_types;
	$typeid=readType($fp,1);
	if($typeid==0){
		return false;
	}else{
		$string=readType($fp,8);
		$value=readType($fp,$typeid);
		#$tagType=$NBT_types[$typeid];
		$tagType=$typeid;
		$tagName=$string;
		$tagData=$value;
		#print "[$tagType,$tagName,$tagData]<br>";
		$pdata[]=array("type"=>$tagType,"name"=>$tagName,"value"=>$tagData);
		return true;
	}
}
function readType($fp,$typeid){
	global $NBT_types;
	$tagType=$NBT_types[$typeid];
	#print "Read [type=>$tagType [$typeid],value=>$value]<br>";
	switch($tagType){
		case "TAG_LONG":
			$bin=fread($fp,8);
			if(strlen($bin) != 8){return(false);}
			list(,$firstHalf)=unpack("N",substr($bin,0,4));
			list(,$secondHalf)=unpack("N",substr($bin,4,4));
			$value=bcadd($secondHalf,bcmul($firstHalf,"4294967296"));
			if(bccomp($value,bcpow(2,63)) >= 0) $value=bcsub($value,bcpow(2,64));
			return($value);
		case "TAG_LIST":
			$tagID=readType($fp,1);
			$listLength=readType($fp,3);
			$list=array("type"=>$tagID,"value"=>array());
			for($i=0; $i < $listLength; $i++) {
				if(feof($fp)) break;
				$list["value"][]=readType($fp,$tagID);
			}
			return $list;
		case "TAG_DOUBLE":
			list(,$value)=(pack('d',1) == "\77\360\0\0\0\0\0\0")?unpack('d',fread($fp,8)):unpack('d',strrev(fread($fp,8)));
			return $value;
		case "TAG_FLOAT":
			list(,$value)=(pack('d',1) == "\77\360\0\0\0\0\0\0")?unpack('f',fread($fp,4)):unpack('f',strrev(fread($fp,4)));
			return $value;
		case "TAG_BYTE_ARRAY":
			$arrayLength=readType($fp,3);
			$array=array();
			for($i=0; $i < $arrayLength; $i++) $array[]=readType($fp,1);
			return $array;
		case "TAG_INT":
			#Знаковое целое (32 бита,big endian)
			list(,$unpacked)=unpack("N",fread($fp,4));
			#Конвертация unsigned int signed int
			if($unpacked>=pow(2,31)) $unpacked-=pow(2,32);
			return $unpacked;
		case "TAG_COMPOUND":
			$tree=array();
			while(readTag($fp,$tree));
			return $tree;
		case "TAG_SHORT":
			list(,$unpacked)=unpack("n",fread($fp,2));
			#Конвертация unsigned short в signed short.
			if($unpacked>=pow(2,15)) $unpacked-=pow(2,16); 
			return $unpacked;
		case "TAG_STRING":
			if(!$stringLength=readType($fp,2)) return "";
			#Читаем число байт,заданное строкой длины,и декодирования из utf8.
			$string=utf8_decode(fread($fp,$stringLength));
			return $string;
		case "TAG_BYTE":
			list(,$unpacked)=unpack("c",fread($fp,1));
			return $unpacked;
		case "TAG_INT_ARRAY":
			$arrayLength=readType( $fp,3);
			$array= array_values( unpack( "N*",fread( $fp,$arrayLength * 4 )));
			return $array;
	}
}
function _intstring2signedlong($string){
	$sbt=array();
	$sby=array();
	$ret='';
	$c_remaining=$string;
	$c_mod=0;
	for($i=0;$i<64;$i++){$sbt[$i]=0;}
	for($mp=63,$i=0;$mp>=0,$i<64;$mp--,$i++){
		$c_mod=bcdiv($c_remaining,bcpow(2,$mp));
		$c_remaining=bcmod($c_remaining,bcpow(2,$mp));
		if(bccomp($c_mod,"1")>=0){$sbt[$i]=1;	}
		if(bccomp($c_remaining,"0")==0){break;}
	}
	for($i=0;$i<8;$i++){
		$bin='';
		for($j=0;$j<8;$j++){
			$os=($i*8)+$j;
			$bin.= $sbt[$os];
		}
		$sby[$i]=chr(bindec($bin));
	}
	$ret=implode($sby);
	if(bccomp($ret,bcpow(2,63))>=0){
		$ret=bcsub($ret,bcpow(2,64));
	}
	return($ret);
}
function getSectionsIndex($data){
	$index=0;
	foreach($data[0]["value"][0]["value"] as $str){
		if($str["name"]=="Sections"){break;}
		$index++;
	}
	return $index;
}
function getChunkPos($data){
	foreach($data[0]["value"][0]["value"] as $str){
		if($str["name"]=="xPos"){$cx=$str["value"]*16;}
		if($str["name"]=="zPos"){$cz=$str["value"]*16;}
	}
	return array($cx,$cz);
}
function readTagToString(&$fp,&$pdata){
	global $NBT_types;
	$typeid=readTypeToString($fp,1);
	if($typeid==0){
		return false;
	}else{
		$string=readTypeToString($fp,8);
		$value=readTypeToString($fp,$typeid);
		#$tagType=$NBT_types[$typeid];
		$tagType=$typeid;
		$tagName=$string;
		$tagData=$value;
		#print "[$tagType,$tagName,$tagData]<br>";
		$pdata[]=array("type"=>$tagType,"name"=>$tagName,"value"=>$tagData);
		return true;
	}
}
function readTypeToString(&$fp,$typeid){
	global $NBT_types;
	$tagType=$NBT_types[$typeid];
	switch($tagType){
		case "TAG_LONG":
			$bin=substr($fp,0,8);$fp=substr($fp,8);
			if(strlen($bin) != 8){return(false);}
			list(,$firstHalf)=unpack("N",substr($bin,0,4));
			list(,$secondHalf)=unpack("N",substr($bin,4,4));
			$value=bcadd($secondHalf,bcmul($firstHalf,"4294967296"));
			if(bccomp($value,bcpow(2,63)) >= 0) $value=bcsub($value,bcpow(2,64));
			return($value);
		case "TAG_LIST":
			$tagID=readTypeToString($fp,1);
			$listLength=readTypeToString($fp,3);
			$list=array("type"=>$tagID,"value"=>array());
			for($i=0; $i < $listLength; $i++) {
				if(feof($fp)) break;
				$list["value"][]=readTypeToString($fp,$tagID);
			}
			return $list;
		case "TAG_DOUBLE":
			list(,$value)=(pack('d',1) == "\77\360\0\0\0\0\0\0")?unpack('d',substr($fp,0,8)):unpack('d',strrev(substr($fp,0,8)));
			$fp=substr($fp,8);
			return $value;
		case "TAG_FLOAT":
			list(,$value)=(pack('d',1) == "\77\360\0\0\0\0\0\0")?unpack('f',substr($fp,0,4)):unpack('f',strrev(substr($fp,0,4)));
			$fp=substr($fp,4);
			return $value;
		case "TAG_BYTE_ARRAY":
			$arrayLength=readTypeToString($fp,3);
			$array=array();
			for($i=0; $i < $arrayLength; $i++) $array[]=readTypeToString($fp,1);
			return $array;
		case "TAG_INT":
			#Знаковое целое (32 бита,big endian)
			list(,$unpacked)=unpack("N",substr($fp,0,4));$fp=substr($fp,4);
			#Конвертация unsigned int signed int
			if($unpacked>=pow(2,31)) $unpacked-=pow(2,32);
			return $unpacked;
		case "TAG_COMPOUND":
			$tree=array();
			while(readTagToString($fp,$tree));
			return $tree;
		case "TAG_SHORT":
			list(,$unpacked)=unpack("n",substr($fp,0,2));$fp=substr($fp,2);
			#Конвертация unsigned short в signed short.
			if($unpacked>=pow(2,15)) $unpacked-=pow(2,16); 
			return $unpacked;
		case "TAG_STRING":
			if(!$stringLength=readTypeToString($fp,2)) return "";
			#Читаем число байт,заданное строкой длины,и декодирования из utf8.
			$string=utf8_decode(substr($fp,0,$stringLength));$fp=substr($fp,$stringLength);
			return $string;
		case "TAG_BYTE":
			$unpacked=null;
			if($fp){
				list(,$unpacked)=unpack("c",substr($fp,0,1));
				$fp=substr($fp,1);
			}
			return $unpacked;
		case "TAG_INT_ARRAY":
			$arrayLength=readTypeToString( $fp,3);
			$array= array_values( unpack( "N*",substr( $fp,0,$arrayLength * 4 )));
			$fp=substr($fp,$arrayLength*4);
			return $array;
	}
}
function getNBTDataUrl($url){
	foreach(explode("/",substr($url,1)) as $str){
		$str=($str=="type" or $str=="value" or $str=="name")?"{\"$str\"}":"[$str]";
		$tmp.=$str;
	}
	return $tmp;
}
function getNBTParam($data,$param,$url,$find){
	$value=null;
	$find=false;
	foreach($data as $key=>$val){
		if(gettype($val)=="array"){
			list($tmp,$uri,$find)=getNBTParam($val,$param,"$url/$key",$find);
			if($find){$value=$tmp;$url=$uri;break;}
		}else{
			if($key==="name"){
				if($val==="$param"){
					$url="$url/value";
					$value=$data["value"];
					$find=true;
					break;
				}
			}
		}
	}
	return array($value,$url,$find);
}
function getNBTAttributes($data,$name){
	$value=$url=null;
	$find=false;
	list($data,$url,$tmp)=getNBTParam($data,"Attributes");
	if(!$tmp){return array(null,null,$find);}
	foreach($data["value"] as $key=>$val){
		if($val[0]["value"]==="generic.$name"){
			$value=$val[1]["value"];
			$url="$url/value/$key/1/value";
			$find=true;
			break;
		}
		if($val[1]["value"]==="generic.$name"){
			$value=$val[0]["value"];
			$url="$url/$key/value/$key/0/value";
			$find=true;
			break;
		}
	}
	return array($value,$url,$find);
}
function createNewInventory($data){
	$data[0]["value"][]=array("type"=>9,"name"=>"Inventory","value"=>array("type"=>10,"value"=>array()));
	return $data;
}
?>
