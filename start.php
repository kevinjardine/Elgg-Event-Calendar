<?php

/**
 *  Event calendar plugin
 *
 * @package event_calendar
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Kevin Jardine <kevin@radagast.biz>
 * @copyright Radagast Solutions 2008-2011
 * @link http://radagast.biz/
 */


elgg_register_event_handler('init','system','event_calendar_init');

function event_calendar_init() {

	elgg_register_library('elgg:event_calendar', elgg_get_plugins_path() . 'event_calendar/models/model.php');
		
	// Register a page handler, so we can have nice URLs
	elgg_register_page_handler('event_calendar','event_calendar_page_handler');

	// Register URL handler
	elgg_register_entity_url_handler('object', 'event_calendar','event_calendar_url');

	// Register granular notification for this type
	register_notification_object('object', 'event_calendar', elgg_echo('event_calendar:new_event'));
		
	// Set up site menu
	$site_calendar = elgg_get_plugin_setting('site_calendar', 'event_calendar');
	if (!$site_calendar || $site_calendar != 'no') {
		// add a site navigation item
		$item = new ElggMenuItem('event_calendar', elgg_echo('item:object:event_calendar'), 'event_calendar/list/');
		elgg_register_menu_item('site', $item);
	}
	// make event calendar title and description searchable
	elgg_register_entity_type('object','event_calendar');

	// make legacy tags searchable
	if (function_exists('elgg_register_tag_metadata_name')) {
		elgg_register_tag_metadata_name('event_tags');
	}
	
	// register the plugin's JavaScript
	$plugin_js = elgg_get_simplecache_url('js', 'event_calendar/event_calendar');
	elgg_register_js('elgg.event_calendar', $plugin_js);

	//add to group profile page
	// TODO - are the left and right values still relevant for Elgg 1.8?
	$group_calendar = elgg_get_plugin_setting('group_calendar', 'event_calendar');
	if (!$group_calendar || $group_calendar != 'no') {
		// add blog link to
		elgg_register_plugin_hook_handler('register', 'menu:owner_block', 'event_calendar_owner_block_menu');
		$group_profile_display = elgg_get_plugin_setting('group_profile_display', 'event_calendar');
		if (!$group_profile_display || $group_profile_display == 'right') {
			//elgg_extend_view('groups/right_column', 'event_calendar/groupprofile_calendar');
			elgg_extend_view('groups/tool_latest', 'event_calendar/group_module');
		} else if ($group_profile_display == 'left') {
			elgg_extend_view('groups/tool_latest', 'event_calendar/group_module');
			//elgg_extend_view('groups/left_column', 'event_calendar/groupprofile_calendar');
		}
	}

	//add to the css
	elgg_extend_view('css/elgg', 'event_calendar/css');

	//add a widget
	elgg_register_widget_type('event_calendar',elgg_echo("event_calendar:widget_title"),elgg_echo('event_calendar:widget:description'));

	// add the event calendar group tool option
	$event_calendar_group_default = elgg_get_plugin_setting('group_default', 'event_calendar');
	if (!$event_calendar_group_default || ($event_calendar_group_default == 'yes')) {
		add_group_tool_option('event_calendar',elgg_echo('event_calendar:enable_event_calendar'),true);
	} else {
		add_group_tool_option('event_calendar',elgg_echo('event_calendar:enable_event_calendar'),false);
	}

	// if autogroup is set, listen and respond to join/leave events
	if (elgg_get_plugin_setting('autogroup', 'event_calendar') == 'yes') {
		elgg_register_event_handler('join', 'group', 'event_calendar_handle_join');
		elgg_register_event_handler('leave', 'group', 'event_calendar_handle_leave');
	}
	
	// entity menu
	elgg_register_plugin_hook_handler('register', 'menu:entity', 'event_calendar_entity_menu_setup');

	// register actions
	$action_path = elgg_get_plugins_path() . 'event_calendar/actions/event_calendar';

	elgg_register_action("event_calendar/edit","$action_path/edit.php");
	elgg_register_action("event_calendar/delete","$action_path/delete.php");
	elgg_register_action("event_calendar/add_personal","$action_path/add_personal.php");
	elgg_register_action("event_calendar/remove_personal","$action_path/remove_personal.php");
	elgg_register_action("event_calendar/request_personal_calendar","$action_path/request_personal_calendar.php");
	elgg_register_action("event_calendar/toggle_personal_calendar","$action_path/toggle_personal_calendar.php");
	elgg_register_action("event_calendar/killrequest","$action_path/killrequest.php");
	elgg_register_action("event_calendar/addtocalendar","$action_path/addtocalendar.php");
	elgg_register_action("event_calendar/add_to_group","$action_path/add_to_group.php");
	elgg_register_action("event_calendar/remove_from_group","$action_path/remove_from_group.php");

}

/**
 * Add a menu item to an ownerblock
 */
function event_calendar_owner_block_menu($hook, $type, $return, $params) {
	if (elgg_instanceof($params['entity'], 'group')) {
		if ($params['entity']->event_calendar_enable != "no") {
			$url = "event_calendar/group/{$params['entity']->guid}";
			$item = new ElggMenuItem('event_calendar', elgg_echo('event_calendar:group'), $url);
			$return[] = $item;
		}
	}

	return $return;
}

// TODO: delete this once everything is recoded

function event_calendar_pagesetup() {

	global $CONFIG;

	$page_owner = page_owner_entity();

	$context = get_context();

	// Group submenu option
	if ($page_owner instanceof ElggGroup && $context == 'groups') {
		if (event_calendar_activated_for_group($page_owner)) {
			add_submenu_item(elgg_echo("event_calendar:group"), $CONFIG->wwwroot . "pg/event_calendar/group/" . $page_owner->getGUID());
		}
	} else if ($context == 'event_calendar'){
		add_submenu_item(elgg_echo("event_calendar:site_wide_link"), $CONFIG->wwwroot . "pg/event_calendar/");
		$site_calendar = get_plugin_setting('site_calendar', 'event_calendar');
		if (!$site_calendar || $site_calendar == 'admin') {
			if (isadminloggedin()) {
				// only admins can post directly to the site-wide calendar
				add_submenu_item(elgg_echo('event_calendar:new'), $CONFIG->url . "pg/event_calendar/new/", 'eventcalendaractions');
			}
		} else if ($site_calendar == 'loggedin') {
			// any logged-in user can post to the site calendar
			if (isloggedin()) {
				add_submenu_item(elgg_echo('event_calendar:new'), $CONFIG->url . "pg/event_calendar/new/", 'eventcalendaractions');
			}
		}
	}

	if (($context == 'groups') || ($context == 'event_calendar')) {
		if (($event_id = get_input('event_id',0)) && ($event = get_entity($event_id))) {
			if (isadminloggedin() && (get_plugin_setting('allow_featured', 'event_calendar') == 'yes')) {
				if ($event->featured) {
					add_submenu_item(elgg_echo('event_calendar:unfeature'), $CONFIG->url . "action/event_calendar/unfeature?event_id=".$event_id.'&'.event_calendar_security_fields(), 'eventcalendaractions');
				} else {
					add_submenu_item(elgg_echo('event_calendar:feature'), $CONFIG->url . "action/event_calendar/feature?event_id=".$event_id.'&'.event_calendar_security_fields(), 'eventcalendaractions');
				}
			}
			add_submenu_item(elgg_echo("event_calendar:view_link"), $CONFIG->wwwroot . "pg/event_calendar/view/" . $event_id,'0eventcalendaradmin');
			if ($event->canEdit()) {
				add_submenu_item(elgg_echo("event_calendar:edit_link"), $CONFIG->wwwroot . "mod/event_calendar/manage_event.php?event_id=" . $event_id,'0eventcalendaradmin');
				add_submenu_item(elgg_echo("event_calendar:delete_link"), $CONFIG->wwwroot . "mod/event_calendar/delete_confirm.php?event_id=" . $event_id,'0eventcalendaradmin');
				$event_calendar_personal_manage = get_plugin_setting('personal_manage', 'event_calendar');
				if ($event_calendar_personal_manage == 'no') {
					add_submenu_item(elgg_echo('event_calendar:review_requests_title'), $CONFIG->wwwroot . "pg/event_calendar/review_requests/".$event_id, '0eventcalendaradmin');
				}
			}
		}
	}
}

function event_calendar_url($entity) {
	$friendly_title = elgg_get_friendly_title($entity->title);
	return "event_calendar/view/{$entity->guid}/$friendly_title";
}

/**
 * Dispatches event calendar pages.
 *
 * URLs take the form of
 *  Site event calendar:			event_calendar/list/<start_date>/<display_mode>/<filter_context>/<region>
 *  Single event:       			event_calendar/view/<event_guid>/<title>
 *  New event:        				event_calendar/add
 *  Edit event:       				event_calendar/edit/<event_guid>
 *  Group event calendar:  			event_calendar/group/<group_guid>/<start_date>/<display_mode>/<filter_context>/<region>
 *  Add group event:   				event_calendar/add/<group_guid>
 *  Review requests:				event_calendar/review_requests/<event_guid>
 *  Display event subscribers:		event_calendar/display_users/<event_guid>
 *
 * Title is ignored
 *
 * @param array $page
 * @return NULL
 */
function event_calendar_page_handler($page) {

	elgg_load_library('elgg:event_calendar');
	$page_type = $page[0];
	switch ($page_type) {
		case 'list':
			if (isset($page[1])) {
				$start_date = $page[1];
				if (isset($page[2])) {
					$display_mode = $page[2];
					if (isset($page[3])) {
						$filter_mode = $page[3];
						if (isset($page[4])) {
							$region = $page[4];
						} else {
							$region = '';
						}
					} else {
						$filter_mode = '';
					}
				} else {
					$display_mode = '';
				}
			} else {
				$start_date = 0;
			}
			echo event_calendar_get_page_content_list($page_type,0,$start_date,$display_mode,$filter_mode,$region);
			break;
		case 'view':
			echo event_calendar_get_page_content_view($page[1]);
			break;
		case 'display_users':
			echo event_calendar_get_page_content_display_users($page[1]);
			break;
		case 'add':
			if (isset($page[1])) {
				group_gatekeeper();
				$group_guid = $page[1];
			} else {
				gatekeeper();
				$group_guid = 0;
			}
			echo event_calendar_get_page_content_edit($page_type,$group_guid);
			break;
		case 'edit':
			gatekeeper();
			echo event_calendar_get_page_content_edit($page_type, $page[1]);
			break;
		case 'group':
			group_gatekeeper();
			if (isset($page[1])) {
				$group_guid = $page[1];
				if (isset($page[2])) {
					$start_date = $page[2];
					if (isset($page[3])) {
					$display_mode = $page[2];
					if (isset($page[3])) {
						$filter_mode = $page[3];
						if (isset($page[4])) {
							$region = $page[4];
						} else {
							$region = '';
						}
					} else {
						$filter_mode = '';
					}
					} else {
						$display_mode = '';
					}
				} else {
					$start_date = '';
				}
			} else {
				$group_guid = 0;
			}
			echo event_calendar_get_page_content_list($page_type,$group_guid,$start_date,$display_mode,$filter_mode,$region);
			break;
		case 'review_requests':
			gatekeeper();
			echo event_calendar_get_page_content_review_requests($page[1]);
			break;
	}
}

/**
 * Add particular event calendar links/info to entity menu
 */
function event_calendar_entity_menu_setup($hook, $type, $return, $params) {
	if (elgg_in_context('widgets')) {
		return $return;
	}

	$entity = $params['entity'];
	$handler = elgg_extract('handler', $params, false);
	if ($handler != 'event_calendar') {
		return $return;
	}
	$user_guid = elgg_get_logged_in_user_guid();
	if (event_calendar_personal_can_manage($entity,$user_guid)) {
		if (event_calendar_has_personal_event($entity->guid,$user_guid)) {
			$options = array(
				'name' => 'personal_calendar',
				'text' => elgg_echo('event_calendar:remove_from_the_calendar'),
				'title' => elgg_echo('event_calendar:remove_from_my_calendar'),
				'href' => elgg_add_action_tokens_to_url("action/event_calendar/remove_personal?guid={$entity->guid}"),
				'priority' => 150,
			);
			$return[] = ElggMenuItem::factory($options);
		} else {
			if (!event_calendar_is_full($entity->guid) && !event_calendar_has_collision($entity->guid,$user_guid)) {
				$options = array(
					'name' => 'personal_calendar',
					'text' => elgg_echo('event_calendar:add_to_the_calendar'),
					'title' => elgg_echo('event_calendar:add_to_my_calendar'),
					'href' => elgg_add_action_tokens_to_url("action/event_calendar/add_personal?guid={$entity->guid}"),
					'priority' => 150,
				);
				$return[] = ElggMenuItem::factory($options);			}
		}
	} else {
		if (!event_calendar_has_personal_event($entity->guid,$user_guid) && !check_entity_relationship($user_guid, 'event_calendar_request', $entity->guid)) {
			$options = array(
				'name' => 'personal_calendar',
				'text' => elgg_echo('event_calendar:make_request_title'),
				'title' => elgg_echo('event_calendar:make_request_title'),
				'href' => elgg_add_action_tokens_to_url("action/event_calendar/request_personal_calendar?guid={$entity->guid}"),
				'priority' => 150,
			);
			$return[] = ElggMenuItem::factory($options);
		}		
	}
	
	$options = array(
		'name' => 'calendar_listing',
		'text' => elgg_echo('event_calendar:personal_event_calendars_link',array(event_calendar_get_users_for_event($entity->guid,0,0,true))),
		'title' => elgg_echo('event_calendar:users_for_event_menu_title'),
		'href' => "event_calendar/display_users/{$entity->guid}",
		'priority' => 150,
	);
	$return[] = ElggMenuItem::factory($options);
	
	/*if (elgg_is_admin_logged_in() && (elgg_get_plugin_setting('allow_featured', 'event_calendar') == 'yes')) {
		if ($event->featured) {
			add_submenu_item(elgg_echo('event_calendar:unfeature'), $CONFIG->url . "action/event_calendar/unfeature?event_id=".$event_id.'&'.event_calendar_security_fields(), 'eventcalendaractions');
		} else {
			add_submenu_item(elgg_echo('event_calendar:feature'), $CONFIG->url . "action/event_calendar/feature?event_id=".$event_id.'&'.event_calendar_security_fields(), 'eventcalendaractions');
		}
	}*/

	return $return;
}
