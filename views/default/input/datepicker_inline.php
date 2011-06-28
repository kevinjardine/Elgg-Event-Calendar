<?php

/**
 * JQuery data picker(inline version)
 * 
 * @package event_calendar
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Kevin Jardine <kevin@radagast.biz>
 * @copyright Radagast Solutions 2008
 * @link http://radagast.biz/
 * 
 */
?>

<script language="javascript">
$(document).ready(function(){
var done_loading = false;
$("#<?php echo $vars['internalname']; ?>").datepicker({ 
	onChangeMonthYear: function(year, month, inst) {
 		if(inst.onChangeToday){
 			day=inst.selectedDay;
 		}else{
 			day=1;
 		}
		if (done_loading) {
			// in this case the mode is forced to month
			document.location.href = "<?php echo $vars['url'].'mod/event_calendar/show_events.php?mode=month&group_guid='.$vars['group_guid'].'&start_date='; ?>" + year+'-'+month+'-1';
		}
	},
    onSelect: function(date) {
		// jump to the new page
        document.location.href = "<?php echo $vars['url'].'mod/event_calendar/show_events.php?mode='.$vars['mode'].'&group_guid='.$vars['group_guid'].'&start_date='; ?>" + date.substring(0,10);
    },
    dateFormat: "yy-mm-dd",
    <?php echo $vars['range_bit']; ?>
    hideIfNoPrevNext: true,
    defaultDate: "<?php echo $vars['start_date'] .' - '.$vars['end_date']; ?>",
    <?php if ($vars['mode'] == 'week') echo 'highlightWeek: true,'; ?>
    rangeSelect: true
});
var start_date = $.datepicker.parseDate("yy-mm-dd", "<?php echo $vars['start_date']; ?>");
var end_date = $.datepicker.parseDate("yy-mm-dd", "<?php echo $vars['end_date']; ?>");
// not sure why this is necessary, but it seems to be
if ("<?php echo $vars['mode'] ?>" == "month") {
	end_date += 1;
}
$("#<?php echo $vars['internalname']; ?>").datepicker("setDate", start_date, end_date);
var done_loading = true;
});
</script>
<div id="<?php echo $vars['internalname']; ?>" ></div>
<p style="clear: both;"><!-- See day-by-day example for highlighting days code --></p>