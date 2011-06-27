<?php
/**
 * Display users who have added a given event to their personal calendars
 * 
 * @package event_calendar
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Kevin Jardine <kevin@radagast.biz>
 * @copyright Radagast Solutions 2008
 * @link http://radagast.biz/
 * 
 */
 
// Load Elgg engine
require_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");

// Load event calendar model
require_once(dirname(__FILE__) . "/models/model.php");

set_context('event_calendar');
$limit = get_input('limit', 12);
$offset = get_input('offset', 0);
if (($event_id = get_input('event_id', 0)) && $event = get_entity($event_id)) {
	$event_container = get_entity($event->container_guid);
	if ($event_container instanceOf ElggGroup) {
		// Re-define context
		set_context('groups');
		set_page_owner($event_container->getGUID());
	}
	set_input('search_viewtype','gallery');
	$count = event_calendar_get_users_for_event($event_id,$limit,$offset,true);
	$users = event_calendar_get_users_for_event($event_id,$limit,$offset,false);
	$body = event_calendar_view_entity_list($users, $count, $offset, $limit, true, false);
	
	$body .= elgg_view('event_calendar/personal_toggle_js');
	
	$title = sprintf(elgg_echo('event_calendar:users_for_event_title'),$event->title);
	page_draw($title,elgg_view_layout("two_column_left_sidebar", '', elgg_view_title($title) . $body));
} else {
	register_error('event_calendar:error_nosuchevent');
	forward();
}
?>