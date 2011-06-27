<?php

	/**
	 *  Event calendar plugin
	 * 
	 * @package RIBA event
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Kevin Jardine <kevin@radagast.biz>
	 * @copyright Radagast Solutions 2008
	 * @link http://radagast.biz/
	 */
	// Load event calendar model
	require_once(dirname(__FILE__) . "/models/model.php");
	/**
	 * event calendar initialisation
	 *
	 * These parameters are required for the event API, but we won't use them:
	 * 
	 * @param unknown_type $event
	 * @param unknown_type $object_type
	 * @param unknown_type $object
	 */

	function event_calendar_init() {
		
		// Load system configuration
		global $CONFIG;			
					
		// Register a page handler, so we can have nice URLs
		register_page_handler('event_calendar','event_calendar_page_handler');
		
		// Register URL handler
		register_entity_url_handler('event_calendar_url','object', 'event_calendar');
		
		// Register granular notification for this type
		if (is_callable('register_notification_object'))
			register_notification_object('object', 'event_calendar', elgg_echo('event_calendar:new_event'));
			
		// Set up menu for users
		if (isloggedin()) {
			$site_calendar = get_plugin_setting('site_calendar', 'event_calendar');
			if (!$site_calendar || $site_calendar != 'no') {			
				add_menu(elgg_echo('item:object:event_calendar'), $CONFIG->wwwroot . "pg/event_calendar/");
			}
		}
		// make tags searchable for Elgg 1.7.4
		if (function_exists('elgg_register_tag_metadata_name')) {
			elgg_register_tag_metadata_name('event_tags');
		}
		
		//add to group profile page
		$group_calendar = get_plugin_setting('group_calendar', 'event_calendar');
		if (!$group_calendar || $group_calendar != 'no') {
			$group_profile_display = get_plugin_setting('group_profile_display', 'event_calendar');
			if (!$group_profile_display || $group_profile_display == 'right') {
				extend_view('groups/right_column', 'event_calendar/groupprofile_calendar');
			} else if ($group_profile_display == 'left') {
				extend_view('groups/left_column', 'event_calendar/groupprofile_calendar');
			}
		}
		
		//add to the css
		extend_view('css', 'event_calendar/css');
		
		//add a widget
		add_widget_type('event_calendar',elgg_echo("event_calendar:widget_title"),elgg_echo('event_calendar:widget:description'));
		
		// add the event calendar group tool option
		if (function_exists('add_group_tool_option')) {
			$event_calendar_group_default = get_plugin_setting('group_default', 'event_calendar');
			if (!$event_calendar_group_default || ($event_calendar_group_default == 'yes')) {
				add_group_tool_option('event_calendar',elgg_echo('event_calendar:enable_event_calendar'),true);
			} else {
				add_group_tool_option('event_calendar',elgg_echo('event_calendar:enable_event_calendar'),false);
			}
		}
		
		// if autogroup is set, listen and respond to join/leave events
		if (get_plugin_setting('autogroup', 'event_calendar') == 'yes') {
			register_elgg_event_handler('join', 'group', 'event_calendar_handle_join');
			register_elgg_event_handler('leave', 'group', 'event_calendar_handle_leave');
		}

		register_entity_type('object','event_calendar');
	}
	
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
		global $CONFIG;		
		return $CONFIG->url . 'pg/event_calendar/view/'.$entity->getGUID();		
	}
	
	/**
	 * Page handler; allows the use of fancy URLs
	 *
	 * @param array $page From the page_handler function
	 * @return true|false Depending on success
	 */
	function event_calendar_page_handler($page) {
		if (isset($page[0]) && $page[0]) {
			if (($page[0] == 'group') && isset($page[1])) {
				set_input('group_guid',$page[1]);
				if (isset($page[2])) {
					set_input('filter',$page[2]);
				}
				@include(dirname(__FILE__) . "/show_events.php");
			} else if (($page[0] == 'view') && isset($page[1])) {
				set_input('event_id',$page[1]);
				@include(dirname(__FILE__) . "/show_event.php");
			} else if ($page[0] == 'new') {
				@include(dirname(__FILE__) . "/manage_event.php");
			} else if ($page[0] == 'review_requests' && isset($page[1])) {
				set_input('event_id',$page[1]);
				@include(dirname(__FILE__) . "/pages/review_requests.php");
			} else if (in_array($page[0],array('all','friends','mine'))) {
				set_input('filter',$page[0]);
				@include(dirname(__FILE__) . "/show_events.php");
			}
		} else {
			@include(dirname(__FILE__) . "/show_events.php");
		}
		return true;		
	}
	
// Make sure the event calendar functions are called
	register_elgg_event_handler('init','system','event_calendar_init');
	register_elgg_event_handler('pagesetup','system','event_calendar_pagesetup');
	
// Register actions

	global $CONFIG;
	register_action("event_calendar/manage",false,$CONFIG->pluginspath . "event_calendar/actions/manage.php");
	register_action("event_calendar/request_personal_calendar",false,$CONFIG->pluginspath . "event_calendar/actions/request_personal_calendar.php");
	register_action("event_calendar/toggle_personal_calendar",false,$CONFIG->pluginspath . "event_calendar/actions/toggle_personal_calendar.php");
	register_action("event_calendar/killrequest",false,$CONFIG->pluginspath . "event_calendar/actions/killrequest.php");
	register_action("event_calendar/addtocalendar",false,$CONFIG->pluginspath . "event_calendar/actions/addtocalendar.php");
	register_action("event_calendar/add_to_group",false,$CONFIG->pluginspath . "event_calendar/actions/add_to_group.php");
	register_action("event_calendar/remove_from_group",false,$CONFIG->pluginspath . "event_calendar/actions/remove_from_group.php");

?>