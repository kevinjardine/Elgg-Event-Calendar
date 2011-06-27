<?php

$region_list = trim(get_plugin_setting('region_list', 'event_calendar'));
// make sure that we are using Unix line endings
$region_list = str_replace("\r\n","\n",$region_list);
$region_list = str_replace("\r","\n",$region_list);
if ($region_list) {
	$body = '';
	$options_values = array('-' =>elgg_echo('event_calendar:all'));
	foreach(explode("\n",$region_list) as $region_item) {
		$region_item = trim($region_item);
		$options_values[$region_item] = $region_item;
	}
	$js = "onchange=\"javascript:$('#event_list').load('".$vars['url_start']
		."&amp;callback=true&region='+escape($('#region').val() ));\""; 
	//$js = "onchange=\"javascript:$('#event_list').load('".$vars['url_start']."&amp;callback=true&region='+$('#region').val());\"";
	$body .= elgg_echo('event_calendar:region_filter_by_label');
	$body .= elgg_view("input/pulldown",array('internalid' => 'region','js'=>$js,'value'=>$vars['region'],'options_values'=>$options_values));
	$body .= '<br />';
}

echo $body;
?>