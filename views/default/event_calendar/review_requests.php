<?php

foreach($vars['requests'] as $request) {
	if ($request instanceof ElggUser) {
		$icon = elgg_view("profile/icon", array(
			'entity' => $request,
			'size' => 'small'
		));
		$info = '<a href="' . $request->getURL() . '" >'.$request->name.'</a>';
		$info .= '<div style="margin-top: 5px;" ></div>';
		$info .=  elgg_view('output/confirmlink',
			array(
			'class' => "cancel_button",
			'href' => $vars['url'] . 'action/event_calendar/killrequest?user_guid='.$request->guid.'&event_id=' . $vars['entity']->guid,
			'confirm' => elgg_echo('event_calendar:request:remove:check'),
			'text' => elgg_echo('delete'),
		));
		$info .= '&nbsp;&nbsp;';
		$url = elgg_add_action_tokens_to_url("{$vars['url']}action/event_calendar/addtocalendar?user_guid={$request->guid}&event_id={$vars['entity']->guid}");
		$info .= '<a href="'.$url.'" class="add_topic_button">'.elgg_echo('accept').'</a>';
		echo elgg_view_listing($icon,$info);
	}
}

?>
