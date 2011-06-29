<?php

// this action allows an admin or event owner to approve a calendar request

// Load configuration
global $CONFIG;

gatekeeper();

$user_guid = get_input('user_guid', get_loggedin_userid());
$event_id = get_input('event_id');

$user = get_entity($user_guid);
$event = get_entity($event_id);

// If join request made
if (event_calendar_personal_can_manage($event,$user_guid) 
		&& check_entity_relationship($user_guid, 'event_calendar_request', $event_id))	{
	if (event_calendar_add_personal_event($event_id,$user_guid)) {
		remove_entity_relationship($user_guid, 'event_calendar_request', $event_id);
		notify_user($user_guid, $CONFIG->site->guid, elgg_echo('event_calendar:add_users_notify:subject'),
							sprintf(
							elgg_echo('event_calendar:add_users_notify:body'),
							$user->name,
							$event->title,
							$event->getURL()
							)
		);
		system_message(elgg_echo('event_calendar:request_approved'));
		
	}
} else {
	register_error(elgg_echo('event_calendar:review_requests_error'));
}
	
forward($_SERVER['HTTP_REFERER']);
