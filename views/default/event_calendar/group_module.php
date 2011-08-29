<?php
/**
 * Group event calendar module
 */

$group = elgg_get_page_owner_entity();

if ($group->event_calendar_enable == "no") {
	return true;
}

$all_link = elgg_view('output/url', array(
	'href' => "event_calendar/group/$group->guid/all",
	'text' => elgg_echo('link:view:all'),
));

elgg_push_context('widgets');
$options = array(
	'type' => 'object',
	'subtype' => 'event_calendar',
	'container_guid' => elgg_get_page_owner_guid(),
	'limit' => 6,
	'full_view' => false,
	'pagination' => false,
);
$content = elgg_list_entities($options);
elgg_pop_context();

if (!$content) {
	$content = '<p>' . elgg_echo('event_calendar:no_events_found') . '</p>';
}

$new_link = elgg_view('output/url', array(
	'href' => "event_calendar/add/$group->guid",
	'text' => elgg_echo('event_calendar:new'),
));

echo elgg_view('groups/profile/module', array(
	'title' => elgg_echo('event_calendar:group'),
	'content' => $content,
	'all_link' => $all_link,
	'add_link' => $new_link,
));
