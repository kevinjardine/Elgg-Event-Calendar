<?php
$event = $vars['event'];
$fd = $vars['form_data'];

$personal_manage_options = array(
	elgg_echo('event_calendar:personal_manage:by_event:open') => 'open',
	elgg_echo('event_calendar:personal_manage:by_event:closed') => 'closed',
	elgg_echo('event_calendar:personal_manage:by_event:private') => 'private',
);

$schedule_options = array(
	elgg_echo('event_calendar:schedule_type:poll')=>'poll',
	elgg_echo('event_calendar:schedule_type:fixed')=>'fixed',
);

$event_calendar_fewer_fields = elgg_get_plugin_setting('fewer_fields', 'event_calendar');
$event_calendar_repeating_events = elgg_get_plugin_setting('repeating_events', 'event_calendar');

$event_calendar_region_display = elgg_get_plugin_setting('region_display', 'event_calendar');
$event_calendar_type_display = elgg_get_plugin_setting('type_display', 'event_calendar');
$event_calendar_spots_display = elgg_get_plugin_setting('spots_display', 'event_calendar');
//$event_calendar_add_users = elgg_get_plugin_setting('add_users', 'event_calendar');
$event_calendar_hide_access = elgg_get_plugin_setting('hide_access', 'event_calendar');

$event_calendar_more_required = elgg_get_plugin_setting('more_required', 'event_calendar');
$event_calendar_personal_manage = elgg_get_plugin_setting('personal_manage', 'event_calendar');
$event_calendar_repeated_events = elgg_get_plugin_setting('repeated_events', 'event_calendar');
$event_calendar_reminders = elgg_get_plugin_setting('reminders', 'event_calendar');
$event_calendar_bbb_server_url = elgg_get_plugin_setting('bbb_server_url', 'event_calendar');

if ($event_calendar_more_required == 'yes') {
	$required_fields = array('title','venue','start_date','start_time',
		'brief_description','region','event_type','fees','contact','organiser',
		'event_tags','spots');
} else {
	$required_fields = array('title','venue','start_date');
}
$all_fields = array('title','venue','start_time','start_date','end_time','end_date',
	'brief_description','region','event_type','fees','contact','organiser','event_tags',
	'long_description','spots','personal_manage');

$prefix = array();
foreach ($all_fields as $fn) {
	if (in_array($fn,$required_fields)) {
		$prefix[$fn] = elgg_echo('event_calendar:required').' ';
	} else {
		$prefix[$fn] = elgg_echo('event_calendar:optional').' ';
	}
}

if ($event) {
	/*$title = $event->title;
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
	if ($event->tags) {
		$event_tags = $event->tags;
	} else {
		// old way
		$event_tags = $event->event_tags;
	}
	$reminder_number = $event->reminder_number;
	$reminder_interval = $event->reminder_interval;
	
	$long_description = $event->long_description;
	$access = $event->access_id;
	if ($event_calendar_times != 'no') {
		$start_time = $event->start_time;
		$end_time = $event->end_time;
	}
	if ($event_calendar_personal_manage == 'by_event') {
		$personal_manage = $event->personal_manage;
		if (!$personal_manage) {
			$personal_manage = 'open';
		}
	}*/
	$event_action = 'manage_event';
	$event_guid = $event->guid;
} else {	
	$event_action = 'add_event';
	$event_guid = 0;
}

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
$event_tags = $fd['tags'];
$all_day = $fd['all_day'];
$send_reminder = $fd['send_reminder'];
$reminder_number = $fd['reminder_number'];
$reminder_interval = $fd['reminder_interval'];
$schedule_type = $fd['schedule_type'];
$long_description = $fd['long_description'];
$access = $fd['access_id'];
if ($event_calendar_times != 'no') {
	$start_time = $fd['start_time'];
	$end_time = $fd['end_time'];
}
if ($event_calendar_personal_manage == 'by_event') {
	$personal_manage = $fd['personal_manage'];
}

$body = '<div class="event-calendar-edit-form">';

$body .= elgg_view('input/hidden',array('name'=>'event_action', 'value'=>$event_action));
$body .= elgg_view('input/hidden',array('name'=>'event_guid', 'value'=>$event_guid));
//$body .= elgg_view('input/hidden',array('name'=>'group_guid', 'value'=>$vars['group_guid']));

$body .= '<div class="event-calendar-edit-form-block event-calendar-edit-form-top-block">';

$body .= '<p><label>'.elgg_echo("event_calendar:title_label").'</label>';
$body .= elgg_view("input/text",array('name' => 'title','class'=>'event-calendar-medium-text','value'=>$title));
$body .= '</p>';
$body .= '<p class="event-calendar-description">'.$prefix['title'].elgg_echo('event_calendar:title_description').'</p>';

$body .= '<p><label>'.elgg_echo("event_calendar:venue_label").'</label>';
$body .= elgg_view("input/text",array('name' => 'venue','class'=>'event-calendar-medium-text','value'=>$venue));
$body .= '</p>';
$body .= '<p class="event-calendar-description">'.$prefix['venue'].elgg_echo('event_calendar:venue_description').'</p>';

$body .= '<p><label>'.elgg_echo("event_calendar:brief_description_label").'</label>';
$body .= elgg_view("input/text",array('name' => 'description','class'=>'event-calendar-medium-text','value'=>$brief_description));
$body .= '</p>';
$body .= '<p class="event-calendar-description">'.$prefix['brief_description'].elgg_echo('event_calendar:brief_description_description').'</p>';

$body .= '<p><label>'.elgg_echo("event_calendar:event_tags_label").'</label>';
$body .= elgg_view("input/tags",array('name' => 'tags','class'=>'event-calendar-medium-text','value'=>$event_tags));
$body .= '</p>';
$body .= '<p class="event-calendar-description">'.$prefix['event_tags'].elgg_echo('event_calendar:event_tags_description').'</p>';

$body .= '<p><label>'.elgg_echo("event_calendar:calendar_label").'</label>';
$body .= elgg_view('event_calendar/container',array('container_guid'=>$vars['group_guid']));
$body .= '</p>';
$body .= '<p class="event-calendar-description">'.$prefix['calendar'].elgg_echo('event_calendar:calendar_description').'</p>';

if($event_calendar_bbb_server_url) {
	$body .= '<p>';
	if ($fd['web_conference']) {
		$body .= elgg_view('input/checkbox',array('name'=>'web_conference','value'=>1,'checked'=>'checked'));
	} else {
		$body .= elgg_view('input/checkbox',array('name'=>'web_conference','value'=>1));
	}
	$body .= elgg_echo('event_calendar:web_conference_label');
	$body .= '</p>';
}

$body .= '</div>';

$body .= '<div class="event-calendar-edit-form-block event-calendar-edit-form-schedule-block">';
$body .= '<h2>'.elgg_echo('event_calendar:schedule:header').'</h2>';
if ($all_day) {
	$body .= elgg_view('input/checkbox',array('name'=>'all_day','value'=>1,'checked'=>'checked'));
} else {
	$body .= elgg_view('input/checkbox',array('name'=>'all_day','value'=>1));
}
$body .= elgg_echo('event_calendar:all_day_label');
if(elgg_plugin_exists('event_poll')) {
	$body .= elgg_view('input/radio',array('name'=>'schedule_type','value'=>$schedule_type,'options'=>$schedule_options));
}
$body .= '<div class="event-calendar-edit-date-wrapper">';
$body .= elgg_view('event_calendar/datetime_edit', 
	array(
		'start_date' => $start_date,
		'end_date' => $end_date,
		'start_time' => $start_time,
		'end_time' => $end_time,
		'prefix' => $prefix,
));
if ($event_calendar_repeated_events == 'yes') {
	$body .= elgg_view('event_calendar/repeat_form_element',$vars);
}
if ($event_calendar_reminders == 'yes') {
	$body .= '<div class="event-calendar-edit-reminder-wrapper">';
	if ($send_reminder) {
		$body .= elgg_view('input/checkbox',array('name'=>'send_reminder','checked' => 'checked','value'=>1));
	} else {
		$body .= elgg_view('input/checkbox',array('name'=>'send_reminder','value'=>1));
	}
	$body .= elgg_echo('elgg_calendar:send_reminder_label'). ' ';
	$numbers = array();
	for ($i=1;$i<60;$i++) {
		$numbers[$i] = $i;
	}
	$intervals = array(
		1 => elgg_echo('event_calendar:interval:minute'),
		60 => elgg_echo('event_calendar:interval:hour'),
		60*24 => elgg_echo('event_calendar:interval:day'),
	);
	
	$body .= elgg_view('input/dropdown',array('name'=>'reminder_number','options_values'=>$numbers,'value'=>$reminder_number));
	$body .= elgg_view('input/dropdown',array('name'=>'reminder_interval','options_values'=>$intervals,'value'=>$reminder_interval));
	$body .= elgg_echo('elgg_calendar:send_reminder_before');
	$body .= '</div>';
}

if ($event_calendar_spots_display == 'yes') {
	$body .= '<p><label>'.elgg_echo("event_calendar:spots_label").'<br />';
	$body .= elgg_view("input/text",array('name' => 'spots','class'=>'event-calendar-medium-text','value'=>$spots));
	$body .= '</label></p>';
	$body .= '<p class="event-calendar-description">'.$prefix['spots'].elgg_echo('event_calendar:spots_description').'</p>';
}
$body .= '</div>';
$body .= '</div>';

// the following feature has been superceded by the manage subscribers feature

/*if ($event_calendar_add_users == 'yes') {
	$body .= '<p><label>'.elgg_echo("event_calendar:add_user_label").'<br />';
	$body .= elgg_view("input/adduser",array('name' => 'adduser','internalid' => 'do_adduser','width'=> 200, 'minChars'=>2));
	$body .= '</label></p><br /><br />';
	$body .= '<p class="description">'.elgg_echo('event_calendar:add_user_description').'</p>';
}*/

if ($event_calendar_personal_manage == 'by_event') {
	$body .= '<div class="event-calendar-edit-form-block event-calendar-edit-form-membership-block">';
	$body .= '<h2>'.elgg_echo('event_calendar:personal_manage:label').'</h2>';
	$body .= elgg_view("input/radio",array('name' => 'personal_manage','value'=>$personal_manage,'options'=>$personal_manage_options));
	//$body .= '<p class="event-calendar-description">'.$prefix['personal_manage'].elgg_echo('event_calendar:personal_manage:description').'</p>';
	$body .= '<br clear="both" />';
	$body .= '</div>';
}

$body .= '<div class="event-calendar-edit-form-block event-calendar-edit-form-share-block">';
$body .= '<h2>'.elgg_echo('event_calendar:permissions:header').'</h2>';
if($event_calendar_hide_access == 'yes') {
	$event_calendar_default_access = elgg_get_plugin_setting('default_access', 'event_calendar');
	if($event_calendar_default_access) {
		$body .= elgg_view("input/hidden",array('name' => 'access_id','value'=>$event_calendar_default_access));
	} else {
		$body .= elgg_view("input/hidden",array('name' => 'access_id','value'=>ACCESS_PRIVATE));
	}
} else {
	$body .= '<p><label>'.elgg_echo('event_calendar:read_access').'</label>';
	$body .= elgg_view("input/access",array('name' => 'access_id','value'=>$access));
	$body .= '</p>';
}
if (elgg_plugin_exists('entity_admins')) {
	$body .= elgg_echo('event_calendar:share_ownership:label');
	$body .= '<br />';
	$body .= elgg_echo('event_calendar:share_ownership:description');
	$body .= elgg_view('input/entity_admins_dropdown',array('entity'=>$event));
}
$body .= '</div>';

if ($event_calendar_region_display == 'yes' || $event_calendar_type_display == 'yes' || $event_calendar_fewer_fields != 'yes') {
	$body .= '<div class="event-calendar-edit-form-block event-calendar-edit-form-other-block">';
	
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
			$body .= '<p><label>'.elgg_echo("event_calendar:region_label").'</label>';
			$body .= elgg_view("input/dropdown",array('name' => 'region','value'=>$region,'options_values'=>$options));
			$body .= '</p>';
			$body .= '<p class="event-calendar-description">'.$prefix['region'].elgg_echo('event_calendar:region_description').'</p>';
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
			$body .= '<p><label>'.elgg_echo("event_calendar:type_label").'</label>';
			$body .= elgg_view("input/dropdown",array('name' => 'event_type','value'=>$event_type,'options_values'=>$options));
			$body .= '</p>';
			$body .= '<p class="event-calendar-description">'.$prefix['event_type'].elgg_echo('event_calendar:type_description').'</p>';
		}
	}
	
	if ($event_calendar_fewer_fields != 'yes') {
	
		$body .= '<p><label>'.elgg_echo("event_calendar:fees_label").'</label>';
		$body .= elgg_view("input/text",array('name' => 'fees','class'=>'event-calendar-medium-text','value'=>$fees));
		$body .= '</p>';
		$body .= '<p class="event-calendar-description">'.$prefix['fees'].elgg_echo('event_calendar:fees_description').'</p>';
		
		$body .= '<p><label>'.elgg_echo("event_calendar:contact_label").'</label>';
		$body .= elgg_view("input/text",array('name' => 'contact','class'=>'event-calendar-medium-text','value'=>$contact));
		$body .= '</p>';
		$body .= '<p class="event-calendar-description">'.$prefix['contact'].elgg_echo('event_calendar:contact_description').'</p>';
		
		$body .= '<p><label>'.elgg_echo("event_calendar:organiser_label").'</label>';
		$body .= elgg_view("input/text",array('name' => 'organiser','class'=>'event-calendar-medium-text','value'=>$organiser));
		$body .= '</p>';
		$body .= '<p class="event-calendar-description">'.$prefix['organiser'].elgg_echo('event_calendar:organiser_description').'</p>';
		
		$body .= '<p><label>'.elgg_echo("event_calendar:long_description_label").'</label>';
		$body .= elgg_view("input/longtext",array('name' => 'long_description','class'=>'event-calendar-long-text','value'=>$long_description));
		$body .= '</p>';
		$body .= '<p class="event-calendar-description">'.$prefix['long_description'].elgg_echo('event_calendar:long_description_description').'</p>';
	}
	
	$body .= '</div>';
}

$body .= elgg_view('input/submit', array('name'=>'submit','value'=>elgg_echo('event_calendar:submit')));

$body .= '</div>';

echo $body;
