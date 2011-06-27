<?php

/**
	 * Manage event
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
    
// Define context
set_context('event_calendar');

global $CONFIG;

gatekeeper();

$event = '';

$group_guid = (int) get_input('group_guid',0);

if ($event_id = get_input('event_id',0)) {
	$event = event_calendar_get_event_for_edit($event_id);
	if (!$event) {
		register_error(elgg_echo('event_calendar:no_such_event_edit_error'));
		forward();
	} else {
		set_page_owner($event->container_guid);
		if (page_owner_entity() instanceof ElggGroup) {
			set_context('groups');
		}
	}
	
	$title = elgg_echo('event_calendar:manage_event_title');
} else {
	$title = elgg_echo('event_calendar:add_event_title');
	if ($group_guid && $group = get_entity($group_guid)) {
		// redefine context
		set_context('groups');
		set_page_owner($group_guid);
	}
}

$body = elgg_view('event_calendar/forms/manage_event', array('event'=>$event,'event_id'=>$event_id,'group_guid'=>$group_guid));

page_draw($title,elgg_view_layout("two_column_left_sidebar", '', elgg_view_title($title) . $body));

?>