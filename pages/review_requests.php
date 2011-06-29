<?php

gatekeeper();

$event_id = get_input('event_id',0);

$title = elgg_echo('event_calendar:review_requests_title');

$event = get_entity($event_id);
$user_id = get_loggedin_userid();

if (event_calendar_personal_can_manage($event,$user_id)) {
	$requests = elgg_get_entities_from_relationship(
		array(
			'relationship' => 'event_calendar_request', 
			'relationship_guid' => $event_id, 
			'inverse_relationship' => TRUE, 
			'limit' => 9999)
	);
	if ($requests) {
		$body = elgg_view('event_calendar/review_requests',array('requests' => $requests, 'entity' => $event));
		//$body = elgg_view('page_elements/contentwrapper',array('body'=>$body));
	} else {
		$body = elgg_view('page_elements/contentwrapper',array('body'=>elgg_echo('event_calendar:review_requests_request_none')));
	}
} else {
	$body = elgg_view('page_elements/contentwrapper',array('body'=>elgg_echo('event_calendar:review_requests_error')));
}

$body = elgg_view_layout('two_column_left_sidebar', '', elgg_view_title($title).$body);
	
page_draw($title, $body);
?>