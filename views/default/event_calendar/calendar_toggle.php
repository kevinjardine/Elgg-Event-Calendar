<?php
$event = $vars['event_calendar_event'];
$user = $vars['entity'];
$container = get_entity($event->container_guid);

if ($container->canEdit()) {
	$link = '<p class="event-calendar-personal-calendar-toggle"><a href="javascript:void(0);" ';
	$link .= 'onclick="javascript:event_calendar_personal_toggle('.$event->guid.','.$vars['entity']->guid.'); return false;" ';
	$link .= ' >';
	$link .= '<span id="event_calendar_user_data_'.$vars['entity']->guid.'">'.elgg_echo('event_calendar:remove_from_the_calendar').'</span>';
	$link .= '</a></p>';
	
	$button = elgg_view('input/button',array(
		'id'=>'event_calendar_user_data_'.$event->guid.'_'.$user->guid,
		'class' => "event-calendar-personal-calendar-toggle",
		'value' => elgg_echo('event_calendar:remove_from_the_calendar_button'),
	));
}

echo '<div class="event-calendar-personal-calendar-toggle-wrapper">'.$button.'<div>';
