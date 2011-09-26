<?php
// generate a list of filter tabs
$group_guid = $vars['group_guid'];
$filter_context = $vars['filter'];
if ($group_guid) {
	$url_start = "event_calendar/group/{$group_guid}/{$vars['start_date']}/{$vars['mode']}";
} else {
	$url_start = "event_calendar/list/{$vars['start_date']}/{$vars['mode']}";
}

$tabs = array(
	'all' => array(
		'text' => elgg_echo('all'),
		'href' => "$url_start/all",
		'selected' => ($filter_context == 'all'),
		'priority' => 200,
	),
	'mine' => array(
		'text' => elgg_echo('mine'),
		'href' => "$url_start/mine",
		'selected' => ($filter_context == 'mine'),
		'priority' => 300,
	),
	'friend' => array(
		'text' => elgg_echo('friends'),
		'href' =>  "$url_start/friends",
		'selected' => ($filter_context == 'friends'),
		'priority' => 400,
	),
);

$event_calendar_spots_display = elgg_get_plugin_setting('spots_display', 'event_calendar');
if ($event_calendar_spots_display == "yes") {
	$tabs['open'] = array(
		'text' => elgg_echo('event_calendar:open'),
		'href' => "$url_start/open",
		'selected' => ($filter_context == 'open'),
		'priority' => 100,
	);
}

foreach ($tabs as $name => $tab) {
	$tab['name'] = $name;
	
	elgg_register_menu_item('filter', $tab);
}

echo elgg_view_menu('filter', array('sort_by' => 'priority', 'class' => 'elgg-menu-hz'));

$event_calendar_region_display = elgg_get_plugin_setting('region_display', 'event_calendar');
if ($event_calendar_region_display == 'yes') {
	elgg_load_js("elgg.event_calendar");
	$url_start .= "/$filter_context";
	echo elgg_view('event_calendar/region_select',array('url_start'=>$url_start,'region'=>$vars['region']));
}