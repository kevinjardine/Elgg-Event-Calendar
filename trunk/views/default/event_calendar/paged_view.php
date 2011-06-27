<?php
$nav = elgg_view('navigation/pagination',array(
			
//												'baseurl' => $_SERVER['REQUEST_URI'],
												'baseurl' => $_SERVER['SCRIPT_NAME'].'/?'.$_SERVER['QUERY_STRING'],
												'offset' => $vars['offset'],
												'count' => $vars['count'],
												'limit' => $vars['limit'],
			
														));
$event_calendar_times = get_plugin_setting('times', 'event_calendar');
$event_calendar_personal_manage = get_plugin_setting('personal_manage', 'event_calendar');
$events = $vars['events'];
$html = '';
$date_format = 'F Y';
$current_month = '';
if ($events) {
	foreach($events as $event) {
		$month = date($date_format,$event->start_date);
		if ($month != $current_month) {
			if ($html) {
				$html .= elgg_view('event_calendar/paged_footer');
			}
			$html .= elgg_view('event_calendar/paged_header',array('date'=>$month,'personal_manage'=>$event_calendar_personal_manage));
			
			$current_month = $month;
		}
		$html .= elgg_view('event_calendar/paged_item_view',array('event'=>$event,'times'=>$event_calendar_times,'personal_manage'=>$event_calendar_personal_manage));
	}
	$html .= elgg_view('event_calendar/paged_footer');
}
$msgs = '<div id="event_calendar_paged_messages"></div>';
$html = $msgs.$nav.'<div class="event_calendar_paged">'.$html.'</div>'.$nav;

echo $html;
?>
<script type="text/javascript">
function event_calendar_personal_toggle(guid) {
	$.get("<?php echo $vars['url'].'action/event_calendar/toggle_personal_calendar?'.event_calendar_security_fields().'&event_id='; ?>"+guid,
		function (res) {
			var flag = res.substring(0,3);
			var msg = res.substring(3);
			$('#event_calendar_paged_messages').html(msg);
			if (flag == '@f@') {
				// action failed so toggle checkbox
				$("#event_calendar_paged_checkbox_"+guid).attr('checked',!$("#event_calendar_paged_checkbox_"+guid).attr('checked'));
			}
	    }
	);
}
</script>