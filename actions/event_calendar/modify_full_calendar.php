<?php
elgg_load_library('elgg:event_calendar');
$event_guid = get_input('event_guid',0);
$day_delta = get_input('dayDelta');
$minute_delta = get_input('minuteDelta','');

if (event_calendar_modify_full_calendar($event_guid,$day_delta,$minute_delta)) {
	$response = array('success'=>TRUE);
} else {	
	$response = array('success'=>FALSE, 'message' =>elgg_echo('event_calendar:modify_full_calendar:error'));
}

echo json_encode($response);

exit;
