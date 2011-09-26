<?php
/**
 * Elgg show events view
 * 
 * @package event_calendar
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Kevin Jardine <kevin@radagast.biz>
 * @copyright Radagast Solutions 2008
 * @link http://radagast.biz/
 * 
 */

$listing_format = $vars['listing_format'];

if ($vars['events']) {
	if ($listing_format == 'agenda') {
		$event_list = elgg_view('event_calendar/agenda_view',$vars);
	} else if ($listing_format == 'paged') {
		$event_list = elgg_view('event_calendar/paged_view',$vars);
	} else {
		$options = array(
			'list_class' => 'elgg-list-entity',
			'full_view' => FALSE,
			'pagination' => TRUE,
			'list_type' => 'listing',
			'list_type_toggle' => FALSE,
			'offset' => $vars['offset'],
			'limit' => $vars['limit'],
		);
		$event_list = elgg_view_entity_list($vars['events'], $options);
	}
} else {
	$event_list = '<p>'.elgg_echo('event_calendar:no_events_found').'</p>';
}
if ($listing_format == 'paged') {
	echo $event_list;
} else {
?>
<div style="width:100%">
<div id="event_list" style="float:left;">
<?php
echo $event_list;
?>
</div>
<div style="float:right;">
<?php
echo elgg_view('event_calendar/calendar',$vars);
?>
</div>
</div>
<?php
}
