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

// Load event calendar model
require_once(dirname(__FILE__) . "/models/model.php");
    
// Define context
set_context('event_calendar');

global $CONFIG;

$event_id = get_input('event_id',0);
if ($event_id && ($event = get_entity($event_id))) {
	$event_container = get_entity($event->container_guid);
	if ($event_container instanceOf ElggGroup) {
		// Re-define context
		set_context('groups');
		set_page_owner($event_container->getGUID());
	}
	$count = event_calendar_get_users_for_event($event_id,0,0,true);
	if ($count > 0) {
		add_submenu_item(sprintf(elgg_echo('event_calendar:personal_event_calendars_link'),$count), $CONFIG->url . "mod/event_calendar/display_event_users.php?event_id=".$event_id, '0eventnonadmin');
	}
	if (isloggedin()) {
		$user_id = get_loggedin_userid();
		if (event_calendar_personal_can_manage($event,$user_id)) {
			if (event_calendar_has_personal_event($event_id,$user_id)) {
				add_submenu_item(elgg_echo('event_calendar:remove_from_my_calendar'), $CONFIG->url . "action/event_calendar/manage?event_action=remove_personal&event_id=".$event_id.'&'.event_calendar_security_fields(), '0eventnonadmin');
			} else {
				if (!event_calendar_is_full($event_id) && !event_calendar_has_collision($event_id,$user_id)) {
					add_submenu_item(elgg_echo('event_calendar:add_to_my_calendar'), $CONFIG->url . "action/event_calendar/manage?event_action=add_personal&event_id=".$event_id.'&'.event_calendar_security_fields(), '0eventnonadmin');
				}
			}
		} else {
			if (!check_entity_relationship($user_id, 'event_calendar_request', $event_id)) {
				add_submenu_item(elgg_echo('event_calendar:make_request_title'), $CONFIG->url . "action/event_calendar/request_personal_calendar?event_id=".$event_id.'&'.event_calendar_security_fields(), '0eventnonadmin');
			}		
		}
	}
	$body = elgg_view('object/event_calendar',array('entity'=>$event,'full'=>true));
	$title = $event->title;
	page_draw($title,elgg_view_layout("two_column_left_sidebar", '', elgg_view_title($title) . $body));
} else {
	register_error('event_calendar:error_nosuchevent');
	forward();
}
?>