<?php
$event_id = get_input('event_id',0);
$user_id = get_input('user_id',get_loggedin_userid());
$other = get_input('other','');
$success = '@s@';
$failure = '@f@';
if ($other) {
	$remove_response = elgg_echo('event_calendar:add_to_the_calendar');
	$add_response = elgg_echo('event_calendar:remove_from_the_calendar');
	$add_error = elgg_echo('event_calendar:add_to_the_calendar_error');
} else {
	$remove_response = $success.elgg_echo('event_calendar:remove_from_my_calendar_response');
	$add_response = $success.elgg_echo('event_calendar:add_to_my_calendar_response');
	$add_error = $failure.elgg_echo('event_calendar:add_to_my_calendar_error');
}
// three character prefix to indicate success or failure

if (event_calendar_has_personal_event($event_id,$user_id)) {
	event_calendar_remove_personal_event($event_id,$user_id);
	echo $remove_response;
} else {
	if (event_calendar_add_personal_event($event_id,$user_id)) {
		echo $success.$add_response;
	} else {
		echo $failure.$add_error;
	}
}

exit;
?>