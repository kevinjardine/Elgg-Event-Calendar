<?php
$event_calendar_restricted_times = elgg_get_plugin_setting('restricted_times', 'event_calendar');
$time_format = elgg_get_plugin_setting('timeformat', 'event_calendar');
if (!$time_format) {
	$time_format = '24';
}

$value = $vars['value'];
if (is_numeric($value)) {
	$hour = floor($value/60);
	$minute = ($value -60*$hour);	
	$time = $hour*60+$minute;
} else {
	$time = '-';
}

$dates = array();
$dates['-'] = '-';

if ($time_format == '12') {
	if ($event_calendar_restricted_times == 'yes') {
		$h1 = 6;
		$h2 = 9;
	} else {
		$h1 = 0;
		$h2 = 11;
	}
	for($h=$h1;$h<=12;$h++) {
		$ht = sprintf("%02d",$h);
		for($m=0;$m<60;$m=$m+15) {
			$mt = sprintf("%02d",$m);
			$t = $h*60+$m;
			if ($h < 12) {
				$dates[$t] = "$ht:$mt am";
			} else {
				$dates[$t] = "$ht:$mt pm";
			}
		}
	}
	for($h=1;$h<$h2;$h++) {
		$ht = sprintf("%02d",$h);
		for($m=0;$m<60;$m=$m+15) {
			$mt = sprintf("%02d",$m);
			$t = 12*60+$h*60+$m;
			$dates[$t] = "$ht:$mt pm";
		}
	}
	if ($event_calendar_restricted_times == 'yes') {
		$m = 0;
		$h = 9;
		$ht = sprintf("%02d",$h);
		$mt = sprintf("%02d",$m);
		$t = 12*60+$h*60+$m;
		$dates[$t] = "$ht:$mt pm";
	}
} else {
	if ($event_calendar_restricted_times == 'yes') {
		$h1 = 6;
		$h2 = 21;
	} else {
		$h1 = 0;
		$h2 = 24;
	}
	for($h=$h1;$h<$h2;$h++) {
		$ht = sprintf("%02d",$h);
		for($m=0;$m<60;$m=$m+15) {
			$mt = sprintf("%02d",$m);
			$t = $h*60+$m;
			$dates[$t] = "$ht:$mt";
		}
	}
	if ($event_calendar_restricted_times == 'yes') {
		$m = 0;
		$h = 21;
		$ht = sprintf("%02d",$h);
		$mt = sprintf("%02d",$m);
		$t = 12*60+$h*60+$m;
		$dates[$t] = "$ht:$mt pm";
	}
}

echo elgg_view('input/dropdown',array('name'=>$vars['name'],'value'=>$time,'options_values'=>$dates));

