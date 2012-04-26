<?php
// A non-admin / non-event-creator only sees the button if they have the event on his/her personal calendar 
// and it is at most 15 minutes before the conference starts.

// The button is removed for everyone (even admins) one day after the conference ends.

$event = $vars['event'];

if ($event) {
	elgg_load_library('elgg:event_calendar');
	$user_guid = elgg_get_logged_in_user_guid();
	$termination_time = $event->real_end_time + 60*60*24;
	if ($termination_time < time()) {
		$in_time_window = FALSE;
	} else if ($event->canEdit()) {
		$in_time_window = TRUE;
	} else if (event_calendar_has_personal_event($event->guid, $user_guid) && ($event->start_date - 15*60) >= time()) {
		$in_time_window = TRUE;
	} else {
		$in_time_window = FALSE;
	}
	if ( $in_time_window ) {
		$button = elgg_view('output/url', array(
			'href' => event_calendar_get_join_bbb_url($event),
			'text' => elgg_echo('event_calendar:join_conf_button'),
			'class' => 'elgg-button elgg-button-action',
			'target' => '_blank',
		));
	
		echo '<div class="event-calendar-conf-join-button">'.$button.'</div>';
	}
}
