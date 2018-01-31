<?php
	sleep(1);
	foreach(glob("../data/libs/*.php") as $str){include $str;}
	pagetop("Статус сервера","../");
?>
<style>
.val{color: rgb(139, 191, 82);}
.sys{color: rgb(127, 4, 102);}
.pbar{
	border:1px solid silver;
	width:70%;
	height:15px;
	padding:1px;
}
.inBar{
	height:100%;
	background:orange;
	font-size:9pt;
	padding-left:1px;
	text-shadow:0px 0px 2px white;
	color:black;
}
</style>
<a href="<?php	print $_SERVER["PHP_SELF"];	?>">Обновить</a><br>
<section class="border"></section>


<span class="sys">+++++++++++++++++:</span> System Data <span class="sys">:+++++++++++++++++++</span><br>
<?php
	print '&#160;&#160;Hostname <span class="sys">=</span> <span class="val">'.`hostname`.'</span><br>';
	print '&#160;&#160;&#160;&#160;&#160;&#160;Address <span class="sys">=</span> <span class="val">'.`/sbin/ifconfig -a | grep "inet " | awk '{print $2}' | awk -F':' '{print $2}' | grep -v 127.0.0.1`.'</span><br>';
	print '&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;Kernel <span class="sys">=</span> <span class="val">'.`uname -r`.'</span><br>';
	print '&#160;&#160;&#160;&#160;&#160;&#160;&#160;Uptime <span class="sys">=</span> <span class="val">'.`uptime | sed 's/.*"up ([^,]*), .*/1/'`.'</span><br>';
	print '&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;CPU <span class="sys">=</span> <span class="val">'.`lscpu | grep "CPU(s):" | head -1 | awk '{print $2}'`.' x '.`cat /proc/cpuinfo | grep "model name" | head -1 | awk '{print $4" "$5" "$6" "$7" "$9}'`.'</span><br>';
	#$MEMKB=`cat /proc/meminfo | grep MemTotal | awk {'print $2'}`;
	#$MEMKB/=1024;
	$MEMMB=`echo "$(cat /proc/meminfo | grep MemTotal | awk {'print $2'}) / 1024" | bc `;
	print '&#160;&#160;&#160;&#160;&#160;Memory <span class="sys">=</span> <span class="val">'.$MEMMB.' Mb</span><br>';
	print '&#160;&#160;Proccount <span class="sys">=</span> <span class="val">'.`expr $(ps -Afl | wc -l) - 5`.'</span><br>';
	print '&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;Date <span class="sys">=</span> <span class="val">'.`date "+%A %d.%m.%Y [%H:%M:%S]"`.'</span><br>';
?>
<span class="sys">+++++++++++++++++:</span> Storage Data <span class="sys">:+++++++++++++++++++</span><br>
<?php
	foreach(explode("\n",`df -B 1 -x tmpfs`) as $str){
		for($i=0;$i<10;$i++){$str=str_replace("  "," ",$str);}
		list($dev,$size,$used,,,$mount)=explode(" ",$str);
		if($dev=="" or $dev=="Filesystem" or $dev=="none"){continue;}
		$prz=round($used/($size/100));
		$free=$size-$used;
		print "$mount<br>";
		print '<div class="pbar"><div style="width:'.$prz.'%;" class="inBar">'.$prz.'%</div></div>';
		print "<span style=\"color:gray;\"><b>Free:</b> [".getSize($free)." / ".getSize($size)."] <i>".(100-$prz)."%</i></span><br>";
	}
?>
<?php	pagebottom();	?>
