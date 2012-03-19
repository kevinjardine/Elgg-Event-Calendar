<?php
elgg_load_js('elgg.full_calendar');

$events = $vars['events'];

$event_array = array();
$times_supported = elgg_get_plugin_setting('times','event_calendar') != 'no';

foreach($events as $e) {
	$event_item = array(
		'guid' => $e->guid,
		//'title' => '<a href="'.$e->url.'">'.$e->title.'</a>',
		'title' => $e->title,
		'url' => $e->getURL(),
		'start_date' => $e->start_date,
		'end_date' => $e->real_end_time,
	);
	if ($times_supported) {
		$event_item['allDay'] = FALSE;
	} else {
		$event_item['allDay'] = TRUE;
	}

	$event_array[] = $event_item;
}

$json_events_string = json_encode($event_array);

// TODO: is there an easy way to avoid embedding JS?
?>
<script>

handleEventClick = function(event) {
    if (event.url) {
        window.location.href = event.url;
        return false;
    }
};

handleEventDrop = function(event,dayDelta,minuteDelta,allDay,revertFunc) {

    if (!confirm("Are you sure about this change?")) {
        revertFunc();
    } else {
    	elgg.action('event_calendar/modify_full_calendar',
    		{
    			data: {event_guid: event.guid,dayDelta: dayDelta, minuteDelta: minuteDelta},
    			success: function (res) {
    				var success = res.success;
    				var msg = res.message;
    				if (!success) {
    					elgg.register_error(msg,2000);
    					revertFunc()
    				}
    			}
    		}
    	);
    }
};

$(document).ready(function() {
	var events = <?php echo $json_events_string; ?>;
	var cal_events = [];
	for (var i = 0; i < events.length; i++) {
		cal_events.push({
			guid: events[i].guid,
			title : events[i].title,
			url: events[i].url,
			start : new Date(1000*events[i].start_date),
			end : new Date(1000*events[i].end_date),
			allDay: events[i].allDay
		});
	}
	
	$('#calendar').fullCalendar({
		header: {
			left: 'prev,next today',
			center: 'title',
			right: 'month,agendaWeek,agendaDay'
		},
		editable: true,
		slotMinutes: 15,
		eventDrop: handleEventDrop,
		eventClick: handleEventClick,
		events: cal_events
	});
});
</script>
<div id='calendar'></div>
