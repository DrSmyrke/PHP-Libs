<?php
    $lng=array(
		"en"=>array(
			"title"=>"Achievements",
			"complete"=>"Done:"
		),
		"ru"=>array(
			"title"=>"Достижения",
			"complete"=>"Выполнено:"
		)
	);
	foreach(glob("data/libs/*.php") as $str){include $str;}
	pageTop($lng[$language]["title"],"");
	getAchievements("my/Achievements.yml",$language,"light");
?>




<style>
.achievements{
	width:100%;
}
.achievements b, .achievements .b{
	text-shadow:0px 0px 5px #FFCF7D;
	color:white;
}
.achiv{
	padding:0px;
	font-size:11pt;
	-moz-border-radius:5px;
	-webkit-border-radius:5px;
	-o-border-radius:5px;
	border-radius:5px;
	border-color:gray;
	border-width:1px;
	border-style:solid;
}
legend,.legend{
	color:#F07746;
	font-weight:bold;
	font-size:10pt;
	text-shadow: 0px 1px 0px #000;
}
</style>

<?php
function getAchievements($file,$lang,$style="dark"){	#style=dark/light
	global $lng;
	$data=parsYaml($file); 100%
	drawData($data);
	print '<table class="achievements">';
	$imgBG=($style=="dark")?"my/pixelb.png":"my/pixelg.png";
	$imgBar=($style=="dark")?"my/pixelg.png":"my/pixelb.png";
	foreach($data as $str){
		$prz=$str["value"]/($str["max"]/100);
		if($prz>100){ $prz = 100; }
		$imgStyle=($prz<100)?"opacity:0.2;-moz-opacity:0.2;filter: alpha(opacity=20);":"";
		print '<tr><td width="64px" style="padding-top:5px;"><img src="'.$str["image"].'" style="'.$imgStyle.'"></td><td class="achiv" style="background:url('.$imgBG.');"><p style="-moz-border-radius:3px;-webkit-border-radius:3px;-o-border-radius:3px;border-radius:3px;margin:0px;padding-left:3px;background:url('.$imgBar.') no-repeat;background-size:'.$prz.'% 100%;-moz-background-size:'.$prz.'% 100%;-o-background-size:'.$prz.'% 100%;-webkit-background-size:'.$prz.'% 100%;margin:3px;"><b>'.$str["name"][$lang].'</b><br><text class="legend">'.$str["info"][$lang].'</text><br>'.$str["status"][$lang].' [';
		$classGreen=($style=="dark")?"valgreen":"b";
		$classAlert=($style=="dark")?"valorange":"valorange";
		$classPrz=($style=="dark")?"valyellow":"legend";
		if($prz<100){
			print '<text class="'.$classAlert.'">'.chis($str["value"]).'</text> / <text class="'.$classGreen.'">'.chis($str["max"]).'</text>';
		}else{
			print '<text class="'.$classGreen.'">'.chis($str["value"]).'</text>';
		}
		print ']<br>'.$lng[$lang]["complete"].' <text class="'.$classPrz.'">'.$prz.'%</text>';
		if($str["reward"] != -1){
			print "&nbsp;&nbsp;&nbsp;";
		}
		print '</p></td></tr>';
	}
	print '</table>';
}
?>


<?php	pageBottom();	?>
