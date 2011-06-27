<?php
// asks the event owner to add you to the event
$event_id = get_input('event_id',0);
$user_id = get_loggedin_userid();
$event = get_entity($event_id);
if ($event && ($event->getSubtype() == 'event_calendar')) {
	if (event_calendar_send_event_request($event,$user_id)) {
		system_message(elgg_echo('event_calendar:request_event_response'));
	} else {
		register_error(elgg_echo('event_calendar:request_event_error'));
	}
	
	forward($event->getUrl());
	
	exit;
}

forward();
