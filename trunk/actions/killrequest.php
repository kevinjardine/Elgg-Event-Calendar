<?php

	// Load configuration
	global $CONFIG;
	
	gatekeeper();
	
	$user_guid = get_input('user_guid', get_loggedin_userid());
	$event_id = get_input('event_id');
	
	$user = get_entity($user_guid);
	$event = get_entity($event_id);
	
	// If join request made
	if (event_calendar_personal_can_manage($event,$user_guid) && check_entity_relationship($user->guid, 'event_calendar_request', $event->guid))
	{
		remove_entity_relationship($user->guid, 'event_calendar_request', $event->guid);
		system_message(elgg_echo('event_calendar:requestkilled'));
	}
	
	forward($_SERVER['HTTP_REFERER']);
	
?>