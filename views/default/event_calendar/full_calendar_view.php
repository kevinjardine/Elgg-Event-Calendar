<?php
elgg_load_js('elgg.full_calendar');

$events = $vars['events'];

$event_array = array();

foreach($events as $e) {
	$event_array[] = array(
		'id' => $e->guid,
		'title' => $e->title,
		'start_date' => $e->start_date,
		'end_date' => $e->real_end_time,
	);
}

$json_events_string = json_encode($event_array);

// TODO: is there an easy way to avoid embedding JS?
?>
<script>
$(document).ready(function() {
	var events = <?php echo $json_events_string; ?>;
	var cal_events = [];
	for (var i = 0; i < events.length; i++) {
		cal_events.push({
			id: events[i].id,
			title : events[i].title,
			start : new Date(1000*events[i].start_date),
			end : new Date(1000*events[i].end_date),
			allDay: false
		});
	}
	
	$('#calendar').fullCalendar({
		header: {
			left: 'prev,next today',
			center: 'title',
			right: 'month,agendaWeek,agendaDay'
		},
		editable: true,
		events: cal_events
	});
});
</script>
<div id='calendar'></div>