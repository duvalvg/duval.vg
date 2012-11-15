<?php
// Version: 2.0 RC4; Good Post/Bad Post manage area, and XML features.

function template_main()
{
	global $context, $modSettings, $scripturl, $settings, $txt;
	
	echo '
<form action="', $scripturl, '?action=admin;area=gpbp;sa=savebasic" method="post" accept-charset="', $context['character_set'], '">
	<table border="0" width="100%" cellspacing="0" cellpadding="4" class="tborder" align="center">
		<tr class="titlebg">
			<td colspan="2">', $txt['gpbp_settings'], '</td>
		</tr>
		<tr class="catbg">
			<td colspan="2">', $txt['gpbp_managearea_basic'], '</td>
		</tr>
		<tr class="windowbg2" valign="top">
			<td width="60%">
				<a href="', $scripturl, '?action=helpadmin;help=gpbp_threshold" onclick="return reqWin(this.href);" class="help"><img src="', $settings['images_url'], '/helptopics.gif" alt="', $txt['help'], '" align="top" /></a> 
				<strong>', $txt['gpbp_bad_threshold'], ':</strong><br />
				<span class="smalltext">', $txt['gpbp_bad_threshold_desc'], '</span>
			</td>
			<td>
				<input type="text" size="6" maxlength="3" id="gpbp_hide_threshold" name="gpbp_hide_threshold" value="', $modSettings['gpbp_hide_threshold'],'" />
			</td>
		</tr>
		<tr class="windowbg2" valign="top">
			<td>
				<a href="', $scripturl, '?action=helpadmin;help=gpbp_display_respect" onclick="return reqWin(this.href);" class="help"><img src="', $settings['images_url'], '/helptopics.gif" alt="', $txt['help'], '" align="top" /></a> 
				<label for="gpbp_display_respect"><strong>', $txt['gpbp_show_respect'], ':</strong></label><br />
				<span class="smalltext">', $txt['gpbp_show_respect_desc'], '</span>
			</td>
			<td>
				<input type="checkbox" id="gpbp_display_respect" name="gpbp_display_respect"', $modSettings['gpbp_display_respect'] ? ' checked="checked"' : '',' />
			</td>
		</tr>
		<tr class="windowbg2" valign="top">
			<td>
				<label for="gpbp_display_stats"><strong>', $txt['gpbp_display_stats'], ':</strong></label><br />
				<span class="smalltext">', $txt['gpbp_display_stats_desc'], '</span>
			</td>
			<td>
				<input type="checkbox" id="gpbp_display_stats" name="gpbp_display_stats"', $modSettings['gpbp_display_stats'] ? ' checked="checked"' : '',' />
			</td>
		</tr>
		<tr class="windowbg2" valign="top">
			<td>
				<strong>', $txt['gpbp_show_best_topics'], '</strong><br />
				<span class="smalltext">', $txt['gpbp_show_best_topics_desc'], '</span>
			</td>
			<td>
				<input type="text" size="4" id="gpbp_show_best_topics" name="gpbp_show_best_topics" value="', $modSettings['gpbp_show_best_topics'], '" /> 
			</td>
		</tr>
		<tr valign="top" class="windowbg2">
			<td width="60%">
				<strong>', $txt['gpbp_button_set'], '</strong><br />
				<span class="smalltext">', $txt['gpbp_button_set_desc'], '</span><br />
			</td>
			<td valign="top" align="left">
				<div>
					<input type="radio" id="gpbp_button_set_1" name="gpbp_button_set"', empty($modSettings['gpbp_button_set']) ? ' checked="checked"' : '' ,' value="" />
					<label for="gpbp_button_set_1">
					<img src="', $settings['default_images_url'], '/gpbp_arrow_down.gif" alt="', $txt['gpbp_vote_down'], '" />
					<img src="', $settings['default_images_url'], '/gpbp_arrow_down_lit.gif" alt="', $txt['gpbp_voted_down_alt'], '" />
					<img src="', $settings['default_images_url'], '/gpbp_arrow_up.gif" alt="', $txt['gpbp_vote_up'], '" />
					<img src="', $settings['default_images_url'], '/gpbp_arrow_up_lit.gif" alt="', $txt['gpbp_voted_up_alt'], '" />
					</label>
				</div>';
	for ($i = 2; $i <= 4; $i++)
		echo '
				<div>
					<input type="radio" id="gpbp_button_set_', $i,'" name="gpbp_button_set"', $modSettings['gpbp_button_set'] == '_'.$i ? ' checked="checked"' : '' ,' value="_', $i, '" />
					<label for="gpbp_button_set_', $i,'">
					<img src="', $settings['default_images_url'], '/gpbp_', $i, '_arrow_down.png" alt="', $txt['gpbp_vote_down'], '" />
					<img src="', $settings['default_images_url'], '/gpbp_', $i, '_arrow_down_lit.png" alt="', $txt['gpbp_voted_down_alt'], '" />
					<img src="', $settings['default_images_url'], '/gpbp_', $i, '_arrow_up.png" alt="', $txt['gpbp_vote_up'], '" />
					<img src="', $settings['default_images_url'], '/gpbp_', $i, '_arrow_up_lit.png" alt="', $txt['gpbp_voted_up_alt'], '" />
					</label>
				</div>';
	echo '
			</td>
		</tr>
		<tr class="windowbg2">
			<td colspan="2">
				<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
				<input type="submit" value="', $txt['save'], '" />
			</td>
		</tr>
	</table>
</form>
<form action="', $scripturl, '?action=admin;area=gpbp;sa=saveboards" method="post" accept-charset="', $context['character_set'], '">
	<table border="0" width="100%" cellspacing="0" cellpadding="4" class="tborder" align="center">
		<tr class="catbg">
			<td colspan="2">', $txt['gpbp_affected_boards'], '</td>
		</tr>
		<tr valign="top" class="windowbg2">
			<td width="60%">
				<strong>', $txt['gpbp_enable_boards'], ':</strong><br />
				<span class="smalltext">', $txt['gpbp_enable_boards_desc'], '</span><br />
			</td>
			<td valign="top" align="left">
				<fieldset style="width: 90%;" id="boardlist">
					<legend>', $txt['gpbp_choose_boards'], '</legend>';

	// List all the boards available, and their GPBP setting.
	foreach ($context['boardlist'] as $board)
		echo '
					<label for="boards_', $board['id'], '"><input type="checkbox" name="boards[]" value="', $board['id'], '" id="boards_', $board['id'], '"', $board['gpbp'] ? ' checked="checked"' : '', ' />', $board['name'], '</label><br />';

	echo '
				</fieldset>
				<label for="checkallboards"><em>', $txt['check_all'], '</em></label> <input id="checkallboards" type="checkbox" onclick="invertAll(this, this.form, \'boards[]\');" /><br />
				<br />
			</td>
		</tr>
		<tr class="windowbg2">
			<td colspan="2">
				<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
				<input type="submit" value="', $txt['save'], '" />
			</td>
		</tr>
	</table>
</form>';
}

function template_advanced()
{
	global $context, $modSettings, $scripturl, $settings, $txt;
	
	echo '
<form action="', $scripturl, '?action=admin;area=gpbp;sa=saveadvanced" method="post" accept-charset="', $context['character_set'], '">

	<table border="0" width="100%" cellspacing="0" cellpadding="4" class="tborder" align="center">
		<tr class="titlebg">
			<td colspan="2">', $txt['gpbp_settings'], '</td>
		</tr>
		<tr class="windowbg2" valign="top">
			<td width="65%">
				<label for="gpbp_disable_negative_voting"><strong>', $txt['gpbp_disable_negative_voting'], ':</strong></label><br />
				<span class="smalltext">', $txt['gpbp_disable_negative_voting_desc'], '</span>
			</td>
			<td>
				<input type="checkbox" id="gpbp_disable_negative_voting" name="gpbp_disable_negative_voting"', $modSettings['gpbp_disable_negative_voting'] ? ' checked="checked"' : '',' />
			</td>
		</tr>
		<tr class="windowbg2" valign="top">
			<td width="65%">
				<label for="gpbp_disable_disrespect"><strong>', $txt['gpbp_disable_disrespect'], ':</strong></label><br />
				<span class="smalltext">', $txt['gpbp_disable_disrespect_desc'], '</span>
			</td>
			<td>
				<input type="checkbox" id="gpbp_disable_disrespect" name="gpbp_disable_disrespect"', $modSettings['gpbp_disable_disrespect'] ? ' checked="checked"' : '',' />
			</td>
		</tr>
		<tr class="windowbg2">
			<td colspan="2">
				<hr />
			</td>
		</tr>
		<tr class="windowbg2" valign="top">
			<td>
				<strong>', $txt['gpbp_post_count_limit'], '</strong><br />
				<span class="smalltext">', $txt['gpbp_post_count_limit_desc'], '</span>
			</td>
			<td>
				<input type="text" size="6" maxlength="3" id="gpbp_post_count_limit" name="gpbp_post_count_limit" value="', $modSettings['gpbp_post_count_limit'],'" />
			</td>
		</tr>
		<tr class="windowbg2">
			<td colspan="2">
				<hr />
			</td>
		</tr>
		<tr class="windowbg2" valign="top">
			<td>
				<label for="gpbp_users_choice"><strong>', $txt['gpbp_users_choice'], ':</strong></label><br />
				<span class="smalltext">', $txt['gpbp_users_choice_desc'], '</span>
			</td>
			<td>
				<input type="checkbox" id="gpbp_users_choice" name="gpbp_users_choice"', $modSettings['gpbp_users_choice'] ? ' checked="checked"' : '',' />
			</td>
		</tr>
		<tr class="windowbg2" valign="top">
			<td>
				<label for="gpbp_users_choice_none"><strong>', $txt['gpbp_users_choice_none'], '</strong></label><br />
				<span class="smalltext">', $txt['gpbp_users_choice_none_desc'], '</span>
			</td>
			<td>
				<input type="checkbox" id="gpbp_users_choice_none" name="gpbp_users_choice_none"', $modSettings['gpbp_users_choice_none'] ? ' checked="checked"' : '',' />
			</td>
		</tr>
		<tr class="windowbg2" valign="top">
			<td>
				<label for="gpbp_users_hide_again"><strong>', $txt['gpbp_users_hide_again'], '</strong></label><br />
				<span class="smalltext">', $txt['gpbp_users_hide_again_desc'], '</span>
			</td>
			<td>
				<input type="checkbox" id="gpbp_users_hide_again" name="gpbp_users_hide_again"', $modSettings['gpbp_users_hide_again'] ? ' checked="checked"' : '',' />
			</td>
		</tr>
		<tr class="windowbg2">
			<td colspan="2">
				<hr />
			</td>
		</tr>
		<tr class="windowbg2" valign="top">
			<td>
				<strong>', $txt['gpbp_voters_list_limit'], '</strong><br />
				<span class="smalltext">', $txt['gpbp_voters_list_limit_desc'], '</span>
			</td>
			<td>
				<input type="text" size="6" maxlength="3" id="gpbp_voters_list_limit" name="gpbp_voters_list_limit" value="', $modSettings['gpbp_voters_list_limit'],'" />
			</td>
		</tr>
		<tr class="windowbg2">
			<td colspan="2">
				<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
				<input type="submit" value="', $txt['save'], '" />
			</td>
		</tr>
	</table>
</form>';
}

function template_tools()
{
	global $context, $scripturl, $settings, $txt;
	
	echo '
<form action="', $scripturl, '?action=admin;area=gpbp;sa=tools;ssa=search#searchtable" method="post" accept-charset="', $context['character_set'], '">
	<table id="searchtable" border="0" width="100%" cellspacing="0" cellpadding="4" class="tborder" align="center">
		<tr class="titlebg">
			<td colspan="5">', $txt['gpbp_vote_search'], '</td>
		</tr>
		<tr class="windowbg">
			<td colspan="5">', $txt['gpbp_vote_search_filters'],'</td>
		</tr>
		<tr class="windowbg2" valign="top">
			<td colspan="5">', $txt['gpbp_vote_search_filter_by'], '</td>
		</tr>';
	if (!empty($context['gpbp_names_error']))
		echo '
		<tr class="windowbg2" valign="top">
			<td colspan="5">
				<div class="errorbox">', $context['gpbp_names_error'], '
				</div>
			</td>
		</tr>';
	echo '
		<tr class="windowbg2" valign="top">
			<td>', $txt['gpbp_vote_search_topic_id'], ' <input type="text" size="6" name="topic" value="', !empty($_REQUEST['topic']) ? $_REQUEST['topic'] : '', '" />
			</td>
			<td>', $txt['gpbp_vote_search_msg_id'], ' <input type="text" size="6" name="message" value="', !empty($_REQUEST['message']) ? $_REQUEST['message'] : '', '" />
			</td>
			<td>', $txt['gpbp_vote_search_poster_name'], ' <input type="text" size="20" name="poster" value="', !empty($_REQUEST['poster']) ? $_REQUEST['poster'] : '', '" />
			</td>
			<td>', $txt['gpbp_vote_search_voter_name'], ' <input type="text" size="20" name="voter" value="', !empty($_REQUEST['voter']) ? $_REQUEST['voter'] : '', '" />
			</td>
			<td>', $txt['gpbp_vote_search_score'], ' <select name="score"><option value="0">', $txt['gpbp_vote_search_any_score'], '</option><option value="1"', !empty($_REQUEST['score']) && $_REQUEST['score'] == 1 ? ' selected="selected"' : '' ,'>', $txt['gpbp_voted_up_alt'], '</option><option value="-1"', !empty($_REQUEST['score']) && $_REQUEST['score'] == "-1" ? ' selected="selected"' : '' ,'>', $txt['gpbp_voted_down_alt'], '</option></select>
			</td>
		</tr>
		<tr class="windowbg2">
			<td colspan="5">
				<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
				<input type="submit" value="', $txt['search'], '" style="font-weight:bold;margin: 0 20px;" />
			</td>
		</tr>
	</table>
</form>';
	if (!empty($context['gpbp_search_results']) || !empty($context['gpbp_votes_edited']) || !empty($context['gpbp_votes_edited_filter']))
	{
		echo '
<form action="', $scripturl, '?action=admin;area=gpbp;sa=tools#searchtable" method="post" accept-charset="', $context['character_set'], '">
	<table id="votestable" border="0" width="100%" cellspacing="0" cellpadding="4" class="table_grid" align="center">
		<tr class="titlebg">
			<td colspan="7">', $txt['gpbp_vote_list'], !empty($context['gpbp_vote_list_showing']) ? ' - ' . $context['gpbp_vote_list_showing'] : '', '
			<div class="floatright">', !empty($context['page_index']) ? $context['page_index'] : '', '</div>
			</td>
		</tr>';
		if (!empty($context['gpbp_votes_edited']) || !empty($context['gpbp_votes_edited_filter']))
			echo '
		<tr class="windowbg2" valign="top">
			<td colspan="7">
				<div class="information">', !empty($context['gpbp_votes_edited']) ? $context['gpbp_votes_edited'] : $context['gpbp_votes_edited_filter'], '
				</div>
			</td>
		</tr>';
		echo '
		<tr class="catbg">
			<th width="14%">', $txt['topic'], '
			</th>
			<th>', $txt['message'], '
			</th>
			<th>', $txt['gpbp_vote_list_poster'], '
			</th>
			<th>', $txt['gpbp_vote_list_voter'], '
			</th>
			<th>', $txt['gpbp_vote_list_vote'], '
			</th>
			<th>', $txt['gpbp_vote_list_when'], '
			</th>
			<th><input type="checkbox" onclick="invertAll(this, this.form)" class="input_check" title="', $txt['gpbp_vote_list_check'], '" />
			</th>
		</tr>';
		$i = 1;
		$gpbp_ext = empty($context['gpbp_button_set']) ? 'gif' : 'png';
		foreach ($context['gpbp_search_results'] as $row)
			echo '
		<tr class="windowbg', $i % 2 ? '' : '2' , ' centertext">
			<td', !empty($_REQUEST['topic']) ? ' class="highlight2"' : '', '>', empty($row['id_topic']) ? $txt['gpbp_vote_list_deleted'] : '<a href="'. $scripturl .'?topic=' .$row['id_topic']. '.0">'. $row['id_topic'] .'</a>', '
			</td>
			<td', !empty($_REQUEST['message']) ? ' class="highlight2"' : '', '>', empty($row['id_topic']) ?  $row['id_msg'] . ' - ' . $txt['gpbp_vote_list_mdeleted'] : '<a href="'. $scripturl .'?topic='. $row['id_topic'] .'.msg'. $row['id_msg'] .'#msg'. $row['id_msg'] .'">'. $row['id_msg'] .'</a>', '
			</td>
			<td', !empty($_REQUEST['poster']) ? ' class="highlight2"' : '', '>', $context['gpbp_posters'][$row['id_poster']] === false ? '<span class="error">'. sprintf($txt['gpbp_vote_list_account_deleted'], $row['id_poster']) .' </span>' : $context['gpbp_posters'][$row['id_poster']], '
			</td>
			<td', !empty($_REQUEST['voter']) ? ' class="highlight2"' : '', '><strong>', $row['real_name'] === false ? '<span class="error">'. sprintf($txt['gpbp_vote_list_account_deleted'], $row['id_member']) .'</span>' : $row['real_name'] , '</strong>
			</td>
			<td', !empty($_REQUEST['score']) && in_array($_REQUEST['score'],array(1,-1)) ? ' class="highlight2"' : '', '><img src="', $settings['default_images_url'], '/gpbp', $context['gpbp_button_set'], '_arrow_', $row['score'] == 1 ? 'up' : 'down', '_lit.', $gpbp_ext, '" alt="', $txt['gpbp_voted_'.($row['score'] == 1 ? 'up' : 'down').'_alt'] ,'" />
			</td>
			<td class="smalltext">', $row['when'], '
			</td>
			<td><input type="checkbox" name="vote[]" value="', $row['id_member'], 'msg', $row['id_msg'], '" class="input_check" />
			</td>
		</tr>
		<tr class="windowbg', $i++ % 2 ? '' : '2' , ' smalltext">
			<td colspan="7">', $txt['gpbp_vote_list_subject'], '
			<strong>', empty($row['id_topic']) ? '<span class="error">'. $txt['gpbp_vote_list_topic_deleted'] .'</span>' : $context['gpbp_topics'][$row['id_topic']], '</strong> 
			</td>
		</tr>';
		if (isset($_REQUEST['ssa']) && $_REQUEST['ssa'] == 'search' && empty($context['gpbp_search_results']))
			echo '
		<tr class="windowbg centertext">
			<td colspan="7">', $txt['gpbp_vote_list_no_votes'], '
			</td>
		</tr>';
		echo '
		<tr class="titlebg">
			<td colspan="7">
			<div class="floatright">', !empty($context['page_index']) ? $context['page_index'] : '', '</div>
			</td>
		</tr>
		<tr class="windowbg3">
			<td colspan="7">
				<select name="ssa">
					<option value="delete">', $txt['gpbp_votes_delete'], ' </option>
					<option value="flip">', $txt['gpbp_votes_reverse'], ' </option>
				</select>
				', $txt['gpbp_votes_all_checked'], '
				<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
				<br />
				<label for="updaterespect">', $txt['gpbp_votes_also_update'], '</label><input type="checkbox" name="respect" value="1" id="updaterespect" checked="checked" />
				<input type="submit" style="font-weight:bold;margin: 0 20px;" value="', $txt['quick_mod_go'], '" />
				<input type="hidden" name="topic" value="', !empty($_REQUEST['topic']) ? $_REQUEST['topic'] : '', '" />
				<input type="hidden" name="message" value="', !empty($_REQUEST['message']) ? $_REQUEST['message'] : '', '" />
				<input type="hidden" name="poster" value="', !empty($_REQUEST['poster']) ? $_REQUEST['poster'] : '', '" />
				<input type="hidden" name="voter" value="', !empty($_REQUEST['voter']) ? $_REQUEST['voter'] : '', '" />
				<input type="hidden" name="score" value="', !empty($_REQUEST['score']) ? $_REQUEST['score'] : '0', '" />
			</td>
		</tr>';
		echo '
		</table>
	</form>';
		if (!empty($context['gpbp_search_results']))
			echo '
<script type="text/javascript"><!-- // --><![CDATA[
function gpbpConfirmMassEdit()
{
	return ', $context['gpbp_vote_list_rowcount'] > 1 ? 'window.confirm("' . sprintf($txt['gpbp_filtered_confirm'], $context['gpbp_vote_list_rowcount']) . '")' : 'true', ';
}
// ]]></script>
<form onsubmit="return gpbpConfirmMassEdit();" action="', $scripturl, '?action=admin;area=gpbp;sa=tools#searchtable" method="post" accept-charset="', $context['character_set'], '">
	<table id="edittable" border="0" width="100%" cellspacing="0" cellpadding="4" align="center">
		<tr class="catbg">
			<td>', $txt['gpbp_filtered_edit'], '
			</td>
		</tr>
		<tr class="windowbg">
			<td>', $txt['gpbp_filtered_edit_desc'], '
			</td>
		</tr>
		<tr class="windowbg2">
			<td>
				<input type="radio" name="ssa" value="filterdelete" id="filterdelete" /> <label for="filterdelete">', $txt['gpbp_filtered_delete'], '</label>
				<input type="radio" name="ssa" value="filterflip" id="filterflip" /> <label for="filterflip">', $txt['gpbp_filtered_reverse'], '</label>
				<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
				<br />
				<label for="updaterespect_filter">', $txt['gpbp_votes_also_update'], '</label><input type="checkbox" name="respect" value="1" id="updaterespect_filter" checked="checked" />
				<input type="submit" style="font-weight:bold;margin: 0 20px;" value="', $txt['quick_mod_go'], '" />
				<input type="hidden" name="topic" value="', !empty($_REQUEST['topic']) ? $_REQUEST['topic'] : '', '" />
				<input type="hidden" name="message" value="', !empty($_REQUEST['message']) ? $_REQUEST['message'] : '', '" />
				<input type="hidden" name="id_poster" value="', !empty($context['gpbp_found_id_poster']) ? $context['gpbp_found_id_poster'] : '', '" />
				<input type="hidden" name="id_voter" value="', !empty($context['gpbp_found_id_voter']) ? $context['gpbp_found_id_voter'] : '', '" />
				<input type="hidden" name="score" value="', !empty($_REQUEST['score']) ? $_REQUEST['score'] : '0', '" />
				<input type="hidden" name="filteredit" value="1" />
				<input type="hidden" name="poster" value="', !empty($_REQUEST['poster']) ? $_REQUEST['poster'] : '', '" />
				<input type="hidden" name="voter" value="', !empty($_REQUEST['voter']) ? $_REQUEST['voter'] : '', '" />
			</td>
		</tr>
	</table>
</form>';
	}
	echo '
<br />
<form action="', $scripturl, '?action=admin;area=gpbp;sa=tools;ssa=score#scoretable" method="post" accept-charset="', $context['character_set'], '">
	<table id="scoretable" border="0" width="100%" cellspacing="0" cellpadding="4" align="center">
		<tr class="titlebg">
			<td>', $txt['gpbp_manual_changes'], '
			</td>
		</tr>
		<tr class="catbg">
			<td>', $txt['gpbp_manual_edit_scores'], '
			</td>
		</tr>';
	if (! empty($context['gpbp_change_error']))
		echo '
		<tr>
			<td>
				<div class="errorbox">', $context['gpbp_change_error'], '
				</div>
			</td>
		</tr>';
	if (! empty($context['gpbp_respect_success']) || ! empty($context['gpbp_score_success']))
		echo '
		<tr>
			<td>
				<div class="information">', ! empty($context['gpbp_respect_success']) ? $context['gpbp_respect_success'] : $context['gpbp_score_success'], '
				</div>
			</td>
		</tr>';
	echo '
		<tr class="windowbg">
			<td>', $txt['gpbp_manual_specific_change'], '
			</td>
		</tr>
		<tr class="windowbg2">
			<td>
				', $txt['gpbp_vote_search_msg_id'], ' <input type="text" name="chosenmessage" size="6" /><br />
				', $txt['gpbp_manual_new_score'], ' <input type="text" name="score" value="0" />
				<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
				<input type="submit" style="font-weight:bold;margin: 0 20px;" value="', $txt['quick_mod_go'], '" />
			</td>
		</tr>
	</table>
</form>
<form action="', $scripturl, '?action=admin;area=gpbp;sa=tools;ssa=respect#scoretable" method="post" accept-charset="', $context['character_set'], '">
	<table id="respecttable" border="0" width="100%" cellspacing="0" cellpadding="4" align="center">
		<tr class="catbg">
			<td>', $txt['gpbp_edit_respect'], ' 
			</td>
		</tr>
		<tr class="windowbg">
			<td>', $txt['gpbp_edit_respect_desc'], '
			</td>
		</tr>
		<tr class="windowbg2">
			<td>
				', $txt['gpbp_edit_respect_member'], ' <input type="text" name="chosenmember" size="20"', !empty($_REQUEST['chosenmember']) ? ' value="' . $_REQUEST['chosenmember'] . '"' : '' , ' /><br />
				', $txt['gpbp_edit_respect_new'], ' <input type="text" name="respect" value="0" />
				<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
				<input type="submit" style="font-weight:bold;margin: 0 20px;" value="', $txt['quick_mod_go'], '" />
			</td>
		</tr>
	</table>
</form>
<br />
	<table id="gpbptotals" border="0" width="100%" cellspacing="0" cellpadding="4" align="center">
		<tr class="titlebg">
			<td>', $txt['gpbp_totals'], ' 
			</td>
		</tr>',
		! empty($context['gpbp_totals_success']) ? '
		<tr>
			<td class="windowbg2">
				<span class="maintenance_finished">' . $context['gpbp_totals_success'] . '
				</span>
			</td>
		</tr>' : '', '
		<tr class="windowbg2">
			<td>', $txt['gpbp_totals_desc'], '  
				<br />   
				<form action="', $scripturl, '?action=admin;area=gpbp;sa=tools;ssa=totals#gpbptotals" method="post" accept-charset="', $context['character_set'], '">
					<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
					<input type="submit" style="font-weight:bold;margin: 0 20px;" value="', $txt['gpbp_totals'], '" />
				</form>
			</td>
		</tr>
	</table>
<br />
	<table id="orphantable" border="0" width="100%" cellspacing="0" cellpadding="4" align="center">
		<tr class="titlebg">
			<td>', $txt['gpbp_orphaned_title'], ' 
			</td>
		</tr>
		<tr>
			<td class="windowbg2">
				<span class="', ! empty($context['gpbp_orphaned_success']) ? 'information' : 'description', '">', ! empty($context['gpbp_orphaned_success']) ? $context['gpbp_orphaned_success'] : $context['gpbp_orphaned_report'], '
				</span>
			</td>
		</tr>
		<tr class="windowbg2">
			<td>', $txt['gpbp_orphaned_desc'], '
				<br />   
				<form action="', $scripturl, '?action=admin;area=gpbp;sa=tools;ssa=orphaned#orphantable" method="post" accept-charset="', $context['character_set'], '">
					<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
					<input type="submit" style="font-weight:bold;margin: 0 20px;" value="', $txt['gpbp_orphaned_go'], '" />
				</form>
			</td>
		</tr>
	</table>
	';
}

function template_GPBPXml()
{
	global $context;

	echo '<', '?xml version="1.0" encoding="', $context['character_set'], '"?', '>
<smf>
	<success>', $context['gpbp_vote']['success'], '</success>
	<opposite>', $context['gpbp_vote']['opposite'], '</opposite>
	<message>', $context['gpbp_vote']['message'], '</message>
	<counter>', $context['gpbp_vote']['counter'], '</counter>
	<who respect="', $context['gpbp_vote']['who_respect'], '">', $context['gpbp_vote']['who_id'], '</who>
</smf>';
}

function template_GPBPlistXml()
{
	global $context;

	echo '<', '?xml version="1.0" encoding="', $context['character_set'], '"?', '>
<smf>
	<message>', $context['gpbp_message'], '</message>';
	foreach ($context['gpbp_voterslist'] as $voter)
		echo '
	<voter id="', $voter['id_member'], '" score="', $voter['score'], '">', $voter['real_name'], '</voter>';
	echo '
</smf>';
}
?>