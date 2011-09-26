//<script type="text/javascript">
elgg.provide('elgg.event_calendar');

elgg.event_calendar.init = function () {
	$('.event_calendar_paged_checkbox').click(elgg.event_calendar.handlePagedPersonalCalendarToggle);
	$('.event-calendar-personal-calendar-toggle').click(elgg.event_calendar.handleDisplayPagePersonalCalendarToggle);
	$('#event-calendar-region').change(elgg.event_calendar.handleRegionChange);
}

elgg.event_calendar.handleRegionChange = function(e) {
	url = $('#event-calendar-region-url-start').val()+"/"+escape($('#event-calendar-region').val());
	elgg.forward(url);
}

elgg.event_calendar.handlePagedPersonalCalendarToggle = function() {
	guid = parseInt($(this).attr('id').substring('event_calendar_paged_checkbox_'.length));
	elgg.event_calendar.togglePagedPersonalCalendar(guid);
}
elgg.event_calendar.togglePagedPersonalCalendar = function(guid) {
	elgg.action('event_calendar/toggle_personal_calendar',
			{
				data: {event_guid: guid},
				success: function (res) {
							var success = res.success;
							var msg = res.message;
							if (success) {
								elgg.system_message(msg,2000);
							} else {
								elgg.register_error(msg,2000);
							}
							//$('#event_calendar_paged_messages').html(msg);
							if (!success) {
								// action failed so toggle checkbox
								$("#event_calendar_paged_checkbox_"+guid).attr('checked',!$("#event_calendar_paged_checkbox_"+guid).attr('checked'));
							}
					    }
			}
	);
}

elgg.event_calendar.handleDisplayPagePersonalCalendarToggle = function() {
	var guidBit = $(this).attr('id').substring('event_calendar_user_data_'.length);
	var guids = guidBit.split('_');
	var event_guid = parseInt(guids[0]);
	var user_guid = parseInt(guids[1]);
	elgg.event_calendar.toggleDisplayPagePersonalCalendar(event_guid,user_guid);
}

elgg.event_calendar.toggleDisplayPagePersonalCalendar = function(event_guid,user_guid) {
	elgg.action('event_calendar/toggle_personal_calendar',
			{
				data: {event_guid: event_guid,user_guid: user_guid, other: 'yes'},
				success: function (res) {
							var success = res.success;
							var msg = res.message;
							if (success) {
								var button_text = res.button_text;
								$('#event_calendar_user_data_'+event_guid+'_'+user_guid).val(button_text);
								//elgg.system_message(msg,2000);
							} else {
								elgg.register_error(msg,2000);
							}
					    }
			}
	);
}

elgg.register_hook_handler('init', 'system', elgg.event_calendar.init);
//</script>