<?php

/**
 * Show event
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
    
// Define context
set_context('event_calendar');

global $CONFIG;

$event_id = get_input('event_id',0);
if ($event_id && ($event = get_entity($event_id))) {
	set_page_owner($event->container_guid);
	if (page_owner_entity() instanceOf ElggGroup) {
		// Re-define context
		set_context('groups');		
	}
	$body = elgg_view('event_calendar/forms/delete_confirm',array('event_id'=>$event_id,'title'=>$event->title));
	$title = elgg_echo('event_calendar:delete_confirm_title');
	page_draw($title,elgg_view_layout("two_column_left_sidebar", '', elgg_view_title($title) . $body));
} else {
	register_error('event_calendar:error_nosuchevent');
	forward();
}
?>