<?php
$event = $vars['event'];
$time_bit = '';
if (is_numeric($event->start_time)) {
	$time_bit = event_calendar_convert_time($event->start_time);
}

$date_bit = event_calendar_get_formatted_date($event->start_date);

if (event_calendar_has_personal_event($event->guid,get_loggedin_userid())) {
	$calendar_bit = 'checked = "checked"';
} else {
	$calendar_bit = '';
}

$calendar_bit .= ' onclick="javascript:event_calendar_personal_toggle('.$event->guid.'); return true;" ';

$info = '<tr>';
$info .= '<td class="event_calendar_paged_date">'.$date_bit.'</td>';
$info .= '<td class="event_calendar_paged_time">'.$time_bit.'</td>';
$info .= '<td class="event_calendar_paged_title"><a href="'.$event->getUrl().'">'.$event->title.'</a></td>';
$info .= '<td class="event_calendar_paged_venue">'.$event->venue.'</td>';
if ($vars['personal_manage'] != 'no') {
	$info .= '<td class="event_calendar_paged_calendar"><input id="event_calendar_paged_checkbox_'.$event->guid.'" type="checkbox" '.$calendar_bit.' /></td>';
}
$info .= '</tr>';

echo $info;
?>