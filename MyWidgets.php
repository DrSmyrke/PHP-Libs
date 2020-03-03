<?php
class MyWidgets
{
	public function printSystemInfo()
	{
		print '<style>.systemInfoValue{color: rgb(139, 191, 82);}.systemInfoDecor{color: rgb(127, 4, 102);}.systemInfoProgressBar{border:1px solid silver;width:70%;height:15px;	padding:1px;}.systemInfoInsideBar{height:100%;background:orange;font-size:9pt;padding-left:1px;text-shadow:0px 0px 2px white;color:black;}</style>';

		print '<span class="systemInfoDecor">+++++++++++++++++:</span> System Data <span class="systemInfoDecor">:+++++++++++++++++++</span><br>';
		print '&#160;&#160;Hostname <span class="systemInfoDecor">=</span> <span class="systemInfoValue">'.`hostname`.'</span><br>';
		print '&#160;&#160;&#160;&#160;&#160;&#160;Address <span class="systemInfoDecor">=</span> <span class="systemInfoValue">'.`/sbin/ifconfig -a | grep "inet " | awk '{print $2}' | awk -F':' '{print $2}' | grep -v 127.0.0.1`.'</span><br>';
		print '&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;Kernel <span class="systemInfoDecor">=</span> <span class="systemInfoValue">'.`uname -r`.'</span><br>';
		print '&#160;&#160;&#160;&#160;&#160;&#160;&#160;Uptime <span class="systemInfoDecor">=</span> <span class="systemInfoValue">'.`uptime | sed 's/.*"up ([^,]*), .*/1/'`.'</span><br>';
		print '&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;CPU <span class="systemInfoDecor">=</span> <span class="systemInfoValue">'.`lscpu | grep "CPU(s):" | head -1 | awk '{print $2}'`.' x '.`cat /proc/cpuinfo | grep "model name" | head -1 | awk '{print $4" "$5" "$6" "$7" "$9}'`.'</span><br>';
		#$MEMKB=`cat /proc/meminfo | grep MemTotal | awk {'print $2'}`;
		#$MEMKB/=1024;
		$MEMMB = `echo "$(cat /proc/meminfo | grep MemTotal | awk {'print $2'}) / 1024" | bc `;
		print '&#160;&#160;&#160;&#160;&#160;Memory <span class="systemInfoDecor">=</span> <span class="systemInfoValue">'.$MEMMB.' Mb</span><br>';
		print '&#160;&#160;Proccount <span class="systemInfoDecor">=</span> <span class="systemInfoValue">'.`expr $(ps -Afl | wc -l) - 5`.'</span><br>';
		print '&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;Date <span class="systemInfoDecor">=</span> <span class="systemInfoValue">'.`date "+%A %d.%m.%Y [%H:%M:%S]"`.'</span><br>';
		print '<span class="systemInfoDecor">+++++++++++++++++:</span> Storage Data <span class="systemInfoDecor">:+++++++++++++++++++</span><br>';
		foreach( explode( "\n", `df -B 1 -x tmpfs` ) as $str ){
			for( $i = 0; $i < 10; $i++ ) $str = str_replace( "  ", " ", $str );
			list( $dev, $size, $used,,,$mount ) = explode( " ", $str );
			if( $dev == "" or $dev == "Filesystem" or $dev == "none" ) continue;
			$prz = round( $used / ( $size / 100 ) );
			$free = $size - $used;
			print "$mount<br>";
			print '<div class="systemInfoProgressBar"><div style="width:'.$prz.'%;" class="systemInfoInsideBar">'.$prz.'%</div></div>';
			print "<span style=\"color:gray;\"><b>Free:</b> [".MyFunctions::getSize($free)." / ".MyFunctions::getSize($size)."] <i>".(100-$prz)."%</i></span><br>";
		}
	}
}
