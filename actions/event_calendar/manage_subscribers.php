<?php
$event_guid = get_input('event_guid');
$event = get_entity($event_guid);
if (elgg_instanceof($event,'object','event_calendar') && $event->canEdit()) {
	$members = get_input('members');
	// clear the event from all personal calendars
	remove_entity_relationships($event_guid, 'personal_event', TRUE);
	// add event to personal calendars
	foreach ($members as $user_guid) {
		add_entity_relationship($user_guid,'personal_event',$event_guid);
	}
	system_message(elgg_echo('event_calendar:manage_subscribers:success'));
	forward($event->getURL());
} else {
	register_error(elgg_echo('event_calendar:manage_subscribers:error'));
	forward();
}
