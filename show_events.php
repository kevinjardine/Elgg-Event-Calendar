<?php

/**
 * Show events
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

// Load event model
require_once(dirname(__FILE__) . "/models/model.php");
    
// Define context
set_context('event_calendar');

global $CONFIG;

global $autofeed;
$autofeed = true;

$event = '';

$event_calendar_listing_format = get_plugin_setting('listing_format', 'event_calendar');
$event_calendar_spots_display = trim(get_plugin_setting('spots_display', 'event_calendar'));
$event_calendar_first_date = trim(get_plugin_setting('first_date', 'event_calendar'));
$event_calendar_last_date = trim(get_plugin_setting('last_date', 'event_calendar'));

$original_start_date = get_input('start_date',date('Y-m-d'));
if ( $event_calendar_first_date && ($original_start_date < $event_calendar_first_date) ) {
	$original_start_date = $event_calendar_first_date;
}
if ( $event_calendar_last_date && ($original_start_date > $event_calendar_last_date) ) {
	$original_start_date = $event_calendar_first_date;
}

if ($event_calendar_listing_format == 'paged') {
	$start_ts = strtotime($original_start_date);
	$start_date = $original_start_date;
	if ($event_calendar_last_date) {
		$end_ts = strtotime($event_calendar_last_date);
	} else {
		// set to a large number
		$end_ts = 2000000000;
	}
} else {

	// the default interval is one month
	$day = 60*60*24;
	$week = 7*$day;
	$month = 31*$day;
	
	$mode = trim(get_input('mode',''));
	
	if ($mode == "day") {
		$start_date = $original_start_date;
		$end_date = $start_date;
		$start_ts = strtotime($start_date);
		$end_ts = strtotime($end_date)+$day-1;
	} else if ($mode == "week") {
		// need to adjust start_date to be the beginning of the week
		$start_ts = strtotime($original_start_date);
		$start_ts -= date("w",$start_ts)*$day;
		$end_ts = $start_ts + 6*$day;
		
		$start_date = date('Y-m-d',$start_ts);
		$end_date = date('Y-m-d',$end_ts);
	} else {
		$start_ts = strtotime($original_start_date);
		$month = date('m',$start_ts);
		$year = date('Y',$start_ts);
		$start_date = $year.'-'.$month.'-1';
		$end_date = $year.'-'.$month.'-'.getLastDayOfMonth($month,$year);
		//$subtitle = elgg_echo('event_calendar:month_label').': '.date('F Y',$start_ts);
		// RIBA wants month prefix removed
	}
	
	if ($event_calendar_first_date && ($start_date < $event_calendar_first_date)) {
		$start_date = $event_calendar_first_date;
	}
	
	if ($event_calendar_last_date && ($end_date > $event_calendar_last_date)) {
		$end_date = $event_calendar_last_date;
	}
	
	$start_ts = strtotime($start_date);
	
	if ($mode == "day") {
		$end_ts = strtotime($end_date)+$day-1;
		$subtitle = elgg_echo('event_calendar:day_label').': '.date('j F Y',strtotime($start_date));
	} else if ($mode == "week") {
		$end_ts = $start_ts + 6*$day;
		$subtitle = elgg_echo('event_calendar:week_label').': '.date('j F',$start_ts) . ' - '.date('j F Y',$end_ts);
	} else {
		//$subtitle = elgg_echo('event_calendar:month_label').': '.date('F Y',$start_ts);
		// RIBA wants month prefix removed
		$end_ts = strtotime($end_date);
		$subtitle = date('F Y',$start_ts);
	}
}

$group_guid = (int) get_input('group_guid',0);
if ($group_guid && $group = get_entity($group_guid)) {
	// redefine context
	set_context('groups');
	set_page_owner($group_guid);
	$group_calendar = get_plugin_setting('group_calendar', 'event_calendar');
	if (!$group_calendar || $group_calendar == 'members') {
		if (page_owner_entity()->canWriteToContainer($_SESSION['user'])){
			add_submenu_item(elgg_echo('event_calendar:new'), $CONFIG->url . "pg/event_calendar/new/?group_guid=" . page_owner(), '1eventcalendaradmin');
		}
	} else if ($group_calendar == 'admin') {
		if (isadminloggedin() || ($group->owner_guid == get_loggedin_userid())) {				
			add_submenu_item(elgg_echo('event_calendar:new'), $CONFIG->url . "pg/event_calendar/new/?group_guid=" . page_owner(), '1eventcalendaradmin');
		}
	}
}

$offset = get_input('offset');

if ($offset !=  NULL) {
	// don't allow ajax magic during pagination
	$offset = (int) $offset;
	$callback='';
} else {
	$offset = 0;
	$callback = get_input('callback','');
}

$limit = 15;
if ($event_calendar_spots_display == 'yes') {
	$filter = get_input('filter','open');
} else {
	$filter = get_input('filter','all');
}
$region = get_input('region','-');
if ($filter == 'all') {
	$count = event_calendar_get_events_between($start_ts,$end_ts,true,$limit,$offset,$group_guid,$region);
	$events = event_calendar_get_events_between($start_ts,$end_ts,false,$limit,$offset,$group_guid,$region);
} else if ($filter == 'open') {
	$count = event_calendar_get_open_events_between($start_ts,$end_ts,true,$limit,$offset,$group_guid,$region);
	$events = event_calendar_get_open_events_between($start_ts,$end_ts,false,$limit,$offset,$group_guid,$region);
} else if ($filter == 'friends') {
	$user_guid = get_loggedin_userid();
	$count = event_calendar_get_events_for_friends_between($start_ts,$end_ts,true,$limit,$offset,$user_guid,$group_guid,$region);
	$events = event_calendar_get_events_for_friends_between($start_ts,$end_ts,false,$limit,$offset,$user_guid,$group_guid,$region);	
} else if ($filter == 'mine') {
	$user_guid = get_loggedin_userid();
	$count = event_calendar_get_events_for_user_between($start_ts,$end_ts,true,$limit,$offset,$user_guid,$group_guid,$region);
	$events = event_calendar_get_events_for_user_between($start_ts,$end_ts,false,$limit,$offset,$user_guid,$group_guid,$region);	
}

elgg_extend_view('metatags','event_calendar/metatags');

$vars = array(	'original_start_date' => $original_start_date,
			'start_date'	=> $start_date,
			'end_date'		=> $end_date,
			'first_date'	=> $event_calendar_first_date,
			'last_date'		=> $event_calendar_last_date,
			'mode'			=> $mode,
			'events'		=> $events,
			'count'			=> $count,
			'offset'		=> $offset,
			'limit'			=> $limit,
			'group_guid'	=> $group_guid,
			'filter'		=> $filter,
			'region'		=> $region,
			'listing_format' => $event_calendar_listing_format,
);

if ($callback) {
	if (isloggedin()) {
		$nav = elgg_view('event_calendar/nav',$vars);
	} else {
		$nav = '';
	}
	if ($events) {
		if (get_plugin_setting('agenda_view', 'event_calendar') == 'yes') {
			$event_list = elgg_view('event_calendar/agenda_view',$vars);
		} else {
			$event_list = elgg_view_entity_list($events, $count, $offset, $limit, false, false);
		}
	} else {
		$event_list = '<p>'.elgg_echo('event_calendar:no_events_found').'</p>';
	}
	echo $nav.'<br />'.$event_list;
} else {

	$body = elgg_view('event_calendar/show_events', $vars);
	
	if ($event_calendar_listing_format == 'paged') {
		$title = elgg_echo('event_calendar:upcoming_events_title');		
	} else {
		$title = elgg_echo('event_calendar:show_events_title'). ' ('.$subtitle.')';
	}
	
	$body = elgg_view('page_elements/contentwrapper',array('body' =>$body, 'subclass' => 'events'));
		
	page_draw($title,elgg_view_layout("two_column_left_sidebar", '', elgg_view_title($title).$body));
}

function getLastDayOfMonth($month,$year) {
	return idate('d', mktime(0, 0, 0, ($month + 1), 0, $year));
}


?>