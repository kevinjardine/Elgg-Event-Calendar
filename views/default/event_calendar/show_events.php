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
	if (get_plugin_setting('agenda_view', 'event_calendar') == 'yes') {
		$event_list = elgg_view('event_calendar/agenda_view',$vars);
	} else {
		if ($listing_format == 'paged') {
			$event_list = elgg_view('event_calendar/paged_view',$vars);
		} else {
			$event_list = elgg_view_entity_list($vars['events'], $vars['count'], $vars['offset'], $vars['limit'], false, false);
		}
	}
} else {
	$event_list = '<p>'.elgg_echo('event_calendar:no_events_found').'</p>';
}
if ($listing_format == 'paged') {
	echo $event_list;
} else {
	if (isloggedin()) {
		$nav = elgg_view('event_calendar/nav',$vars);
	} else {
		$nav = '';
	}
?>
<table width="100%">
<tr><td>
<div id="event_list">
<?php
echo $nav.'<br />'.$event_list;
?>
</div>
</td>
<td align="right">
<?php
echo elgg_view('event_calendar/calendar',$vars);
?>
</td></tr>
</table>
<?php
}
?>