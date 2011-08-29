<?php
$event = $vars['event'];
$fd = $vars['form_data'];

$event_calendar_times = elgg_get_plugin_setting('times', 'event_calendar');
$event_calendar_region_display = elgg_get_plugin_setting('region_display', 'event_calendar');
$event_calendar_type_display = elgg_get_plugin_setting('type_display', 'event_calendar');
$event_calendar_spots_display = elgg_get_plugin_setting('spots_display', 'event_calendar');
$event_calendar_add_users = elgg_get_plugin_setting('add_users', 'event_calendar');
$event_calendar_hide_access = elgg_get_plugin_setting('hide_access', 'event_calendar');
$event_calendar_hide_end = elgg_get_plugin_setting('hide_end', 'event_calendar');
$event_calendar_more_required = elgg_get_plugin_setting('more_required', 'event_calendar');

if ($event_calendar_more_required == 'yes') {
	$required_fields = array('title','venue','start_date','start_time',
		'brief_description','region','event_type','fees','contact','organiser',
		'event_tags','spots');
} else {
	$required_fields = array('title','venue','start_date');
}
$all_fields = array('title','venue','start_time','start_date','end_time','end_date',
	'brief_description','region','event_type','fees','contact','organiser','event_tags',
	'long_description','spots');
$prefix = array();
foreach ($all_fields as $fn) {
	if (in_array($fn,$required_fields)) {
		$prefix[$fn] = elgg_echo('event_calendar:required').' ';
	} else {
		$prefix[$fn] = elgg_echo('event_calendar:optional').' ';
	}
}

if ($event) {
	$title = $event->title;
	$brief_description = $event->description;
	$venue = $event->venue;
	// this is a form redisplay, so take the values as submitted
	$start_date = $event->start_date;
	$end_date = $event->end_date;
	
	if ($event_calendar_region_display) {
		$region = $event->region;
		if (!$region) {
			$region = '-';
		}
	}
	
	if ($event_calendar_spots_display) {
		$spots = trim($event->spots);
	}
	if ($event_calendar_type_display) {
		$event_type = $event->event_type;
		if (!$event_type) {
			$event_type = '-';
		}
	}
	$fees = $event->fees;
	$contact = $event->contact;
	$organiser = $event->organiser;
	$event_tags = $event->event_tags;
	$long_description = $event->long_description;
	$access = $event->access_id;
	if ($event_calendar_times == 'yes') {
		$start_time = $event->start_time;
		$end_time = $event->end_time;
	}
	$event_action = 'manage_event';
	$event_guid = $event->guid;
} else {
	$title = $fd['title'];
	$brief_description = $fd['description'];
	$venue = $fd['venue'];
	$start_date = $fd['start_date'];
	$end_date = $fd['end_date'];
	$fees = $fd['fees'];
	if ($event_calendar_spots_display) {
		$spots = $fd['spots'];
	}
	if ($event_calendar_region_display) {
		$region = $fd['region'];
	}
	if ($event_calendar_type_display) {
		$event_type = $fd['event_type'];
	}
	$contact = $fd['contact'];
	$organiser = $fd['organiser'];
	$event_tags = $fd['event_tags'];
	$long_description = $fd['long_description'];
	$access = $fd['access_id'];
	if ($event_calendar_times == 'yes') {
		$start_time = $fd['start_time'];
		$end_time = $fd['end_time'];
	}
	$event_action = 'add_event';
	$event_guid = 0;
}
$body = '';

$body .= elgg_view('input/hidden',array('name'=>'event_action', 'value'=>$event_action));
$body .= elgg_view('input/hidden',array('name'=>'event_guid', 'value'=>$event_guid));
$body .= elgg_view('input/hidden',array('name'=>'group_guid', 'value'=>$vars['group_guid']));

$body .= '<p><label>'.elgg_echo("event_calendar:title_label").'<br />';
$body .= elgg_view("input/text",array('name' => 'title','value'=>$title));
$body .= '</label></p>';
$body .= '<p class="description">'.$prefix['title'].elgg_echo('event_calendar:title_description').'</p>';

$body .= '<p><label>'.elgg_echo("event_calendar:venue_label").'<br />';
$body .= elgg_view("input/text",array('name' => 'venue','value'=>$venue));
$body .= '</label></p>';
$body .= '<p class="description">'.$prefix['venue'].elgg_echo('event_calendar:venue_description').'</p>';

if ($event_calendar_times == 'yes') {
	$body .= '<p><label>'.elgg_echo("event_calendar:start_time_label").'</label><br />';
	$body .= elgg_view("input/timepicker",array('name' => 'start_time','value'=>$start_time));
	$body .= '</p>';
	$body .= '<p class="description">'.$prefix['start_time'].elgg_echo('event_calendar:start_time_description').'</p>';
}

$body .= '<p><label>'.elgg_echo("event_calendar:start_date_label").'<br />';
$body .= elgg_view("input/date",array('timestamp'=>TRUE, 'autocomplete'=>'off','name' => 'start_date','value'=>$start_date));
$body .= '</label></p>';
$body .= '<p class="description">'.$prefix['start_date'].elgg_echo('event_calendar:start_date_description').'</p>';

if ($event_calendar_hide_end != 'yes') {
	if ($event_calendar_times == 'yes') {
		$body .= '<p><label>'.elgg_echo("event_calendar:end_time_label").'</label><br />';
		$body .= elgg_view("input/timepicker",array('name' => 'end_time','value'=>$end_time));
		$body .= '</p>';
		$body .= '<p class="description">'.$prefix['end_time'].elgg_echo('event_calendar:end_time_description').'</p>';
	}
	
	$body .= '<p><label>'.elgg_echo("event_calendar:end_date_label").'<br />';
	$body .= elgg_view("input/date",array('timestamp'=>TRUE,'autocomplete'=>'off','name' => 'end_date','value'=>$end_date));
	$body .= '</label></p>';
	$body .= '<p class="description">'.$prefix['end_date'].elgg_echo('event_calendar:end_date_description').'</p>';
}

if ($event_calendar_spots_display == 'yes') {
	$body .= '<p><label>'.elgg_echo("event_calendar:spots_label").'<br />';
	$body .= elgg_view("input/text",array('name' => 'spots','value'=>$spots));
	$body .= '</label></p>';
	$body .= '<p class="description">'.$prefix['spots'].elgg_echo('event_calendar:spots_description').'</p>';
}

if ($event_calendar_add_users == 'yes') {
	$body .= '<p><label>'.elgg_echo("event_calendar:add_user_label").'<br />';
	$body .= elgg_view("input/adduser",array('name' => 'adduser','internalid' => 'do_adduser','width'=> 200, 'minChars'=>2));
	$body .= '</label></p><br /><br />';
	$body .= '<p class="description">'.elgg_echo('event_calendar:add_user_description').'</p>';
}

$body .= '<p><label>'.elgg_echo("event_calendar:brief_description_label").'<br />';
$body .= elgg_view("input/text",array('name' => 'description','value'=>$brief_description));
$body .= '</label></p>';
$body .= '<p class="description">'.$prefix['brief_description'].elgg_echo('event_calendar:brief_description_description').'</p>';

if ($event_calendar_region_display == 'yes') {
	$region_list = trim(elgg_get_plugin_setting('region_list', 'event_calendar'));
	$region_list_handles = elgg_get_plugin_setting('region_list_handles', 'event_calendar');
	// make sure that we are using Unix line endings
	$region_list = str_replace("\r\n","\n",$region_list);
	$region_list = str_replace("\r","\n",$region_list);
	if ($region_list) {
		$options = array();
		$options[] = '-';
		foreach(explode("\n",$region_list) as $region_item) {
			$region_item = trim($region_item);
			if ($region_list_handles == 'yes') {
				$options[$region_item] = elgg_echo('event_calendar:region:'.$region_item);
			} else {
				$options[$region_item] = $region_item;
			}
		}
		$body .= '<p><label>'.elgg_echo("event_calendar:region_label").'<br />';
		$body .= elgg_view("input/pulldown",array('name' => 'region','value'=>$region,'options_values'=>$options));
		$body .= '</label></p>';
		$body .= '<p class="description">'.$prefix['region'].elgg_echo('event_calendar:region_description').'</p>';
	}
}

if ($event_calendar_type_display == 'yes') {
	$type_list = trim(elgg_get_plugin_setting('type_list', 'event_calendar'));
	$type_list_handles = elgg_get_plugin_setting('type_list_handles', 'event_calendar');
	// make sure that we are using Unix line endings
	$type_list = str_replace("\r\n","\n",$type_list);
	$type_list = str_replace("\r","\n",$type_list);
	if ($type_list) {
		$options = array();
		$options[] = '-';
		foreach(explode("\n",$type_list) as $type_item) {
			$type_item = trim($type_item);
			if ($type_list_handles == 'yes') {
				$options[$type_item] = elgg_echo('event_calendar:type:'.$type_item);
			} else {
				$options[$type_item] = $type_item;
			}			
		}
		$body .= '<p><label>'.elgg_echo("event_calendar:type_label").'<br />';
		$body .= elgg_view("input/pulldown",array('name' => 'event_type','value'=>$event_type,'options_values'=>$options));
		$body .= '</label></p>';
		$body .= '<p class="description">'.$prefix['event_type'].elgg_echo('event_calendar:type_description').'</p>';
	}
}

$body .= '<p><label>'.elgg_echo("event_calendar:fees_label").'<br />';
$body .= elgg_view("input/text",array('name' => 'fees','value'=>$fees));
$body .= '</label></p>';
$body .= '<p class="description">'.$prefix['fees'].elgg_echo('event_calendar:fees_description').'</p>';

$body .= '<p><label>'.elgg_echo("event_calendar:contact_label").'<br />';
$body .= elgg_view("input/text",array('name' => 'contact','value'=>$contact));
$body .= '</label></p>';
$body .= '<p class="description">'.$prefix['contact'].elgg_echo('event_calendar:contact_description').'</p>';

$body .= '<p><label>'.elgg_echo("event_calendar:organiser_label").'<br />';
$body .= elgg_view("input/text",array('name' => 'organiser','value'=>$organiser));
$body .= '</label></p>';
$body .= '<p class="description">'.$prefix['organiser'].elgg_echo('event_calendar:organiser_description').'</p>';

$body .= '<p><label>'.elgg_echo("event_calendar:event_tags_label").'<br />';
$body .= elgg_view("input/tags",array('name' => 'tags','value'=>$event_tags));
$body .= '</label></p>';
$body .= '<p class="description">'.$prefix['event_tags'].elgg_echo('event_calendar:event_tags_description').'</p>';

$body .= '<p><label>'.elgg_echo("event_calendar:long_description_label").'<br />';
$body .= elgg_view("input/longtext",array('name' => 'long_description','value'=>$long_description));
$body .= '</label></p>';
$body .= '<p class="description">'.$prefix['long_description'].elgg_echo('event_calendar:long_description_description').'</p>';

if($event_calendar_hide_access == 'yes') {
	$event_calendar_default_access = elgg_get_plugin_setting('default_access', 'event_calendar');
	if($event_calendar_default_access) {
		$body .= elgg_view("input/hidden",array('name' => 'access_id','value'=>$event_calendar_default_access));
	} else {
		$body .= elgg_view("input/hidden",array('name' => 'access_id','value'=>ACCESS_PRIVATE));
	}
} else {
	$body .= '<p><label>'.elgg_echo("access").'<br />';
	$body .= elgg_view("input/access",array('name' => 'access_id','value'=>$access));
	$body .= '</label></p>';
}

$body .= elgg_view('input/submit', array('name'=>'submit','value'=>elgg_echo('event_calendar:submit')));

echo $body;
