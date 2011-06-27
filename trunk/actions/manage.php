<?php

/**
 * Manage actions
 * 
 * @package event_calendar
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Kevin Jardine <kevin@radagast.biz>
 * @copyright Radagast Solutions 2008
 * @link http://radagast.biz/
 * 
 */

// Load RIBA event model
require_once(dirname(dirname(__FILE__)) . "/models/model.php");

gatekeeper();
        
$event_action = get_input('event_action','');
if ($event_action == 'add_event' || $event_action == 'manage_event') {
    $result = event_calendar_set_event_from_form();
    if ($result->success) {
    	if ($event_action == 'manage_event') {
    		add_to_river('river/object/event_calendar/update','update',$_SESSION['user']->guid,$result->event->guid);
    		system_message(elgg_echo('event_calendar:manage_event_response'));
    	} else {
    		$event_calendar_autopersonal = get_plugin_setting('autopersonal', 'event_calendar');
    		if (!$event_calendar_autopersonal || ($event_calendar_autopersonal == 'yes')) {
    			event_calendar_add_personal_event($result->event->guid,$_SESSION['user']->guid);
    		}
    		add_to_river('river/object/event_calendar/create','create',$_SESSION['user']->guid,$result->event->guid);
    		system_message(elgg_echo('event_calendar:add_event_response'));
    	}
    	
    	forward($result->event->getURL());
    } else {
    	// redisplay form with error message
    	register_error(elgg_echo('event_calendar:manage_event_error'));
    	$group_guid = (int) get_input('group_guid',0);

		if ($result->form_data->event_id) {
			$event = get_entity($result->form_data->event_id);
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
    	$body = elgg_view('event_calendar/forms/manage_event', array('event'=>$result->form_data,'event_id'=>$result->form_data->event_id,'group_guid'=>$group_guid));
		
		page_draw($title,elgg_view_layout("two_column_left_sidebar", '', elgg_view_title($title) . $body));
    }
} else if ($event_action == 'delete_event') {
	$event_id = get_input('event_id',0);
	if (($event_id = get_input('event_id',0)) && ($event = get_entity($event_id)) && $event->canEdit()) {
		if (get_input('cancel','')) {
			system_message(elgg_echo('event_calendar:delete_cancel_response'));
			forward($event->getUrl());
		} else {
			$container = get_entity($event->container_guid);
			$event->delete();
			system_message(elgg_echo('event_calendar:delete_response'));
			forward($container->getUrl());
		}
	} else {
		register_error(elgg_echo('event_calendar:error_delete'));
	}
} else if ($event_action == 'add_personal') {
	$event_id = get_input('event_id',0);
	if (($event_id = get_input('event_id',0)) && ($event = get_entity($event_id))) {
		$user_id = $_SESSION['user']->getGUID();
		if (!event_calendar_has_personal_event($event_id,$user_id)) {
			if (event_calendar_add_personal_event($event_id,$user_id)) {
				system_message(elgg_echo('event_calendar:add_to_my_calendar_response'));		
			} else {
				register_error(elgg_echo('event_calendar:add_to_my_calendar_error'));
			}
			forward($event->getUrl());
		}
	}	
} else if ($event_action == 'remove_personal') {
	$event_id = get_input('event_id',0);
	if (($event_id = get_input('event_id',0)) && ($event = get_entity($event_id))) {
		event_calendar_remove_personal_event($event_id,$_SESSION['user']->getGUID());		
		system_message(elgg_echo('event_calendar:remove_from_my_calendar_response'));
		forward($event->getUrl());
	}	
}

forward();

?>