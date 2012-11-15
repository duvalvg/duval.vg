<?php
/********************************************************************************
* GoodPostBadPost.php                                                           *
* ----------------------------------------------------------------------------- *
* This file handles post voting and provides mananging for this mod.            *
*********************************************************************************
* Version:                2.0.4                                                 *
* Copyright(C) 2009-2010: OutofOrder                                            *
* E-mail:                 antilopemiope@yahoo.com                               *
* D:                      Special thanks to KoreanRedDragon.                    *
* ============================================================================= *
* This mod is free software; you may redistribute it and/or modify it as long   *
* as you credit me for the original mod. This mod is distributed in the hope    *
* that it is and will be useful, but WITHOUT ANY WARRANTIES; without even any   *
* implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.      *
*                                                                               *
* All SMF copyrights are still in effect. Anything not mine is theirs.          *
* Some code found in here is copy written code by SMF, therefore it can not be  *
* redistributed without official consent from myself or SMF.                    *
********************************************************************************/

if (!defined('SMF'))
	die('Hacking attempt...');

function GPBP_Vote()
{
	global $context, $smcFunc, $board_info, $board, $topic, $modSettings;

	// Trying to do the impossible?
	if (empty($board_info['gpbp_enabled']))
		fatal_lang_error('gpbp_notinthisboard', false);

	// Missing data? Or maybe you're a guest? Bye!
	if (empty($_REQUEST['sa']) || !in_array($_REQUEST['sa'], array('up','down','voterslist')))
		redirectexit();

	//Voters list request, this way please.
	if ($_REQUEST['sa'] == 'voterslist')
	{
		gpbpVotersList();
		return;
	}
	
	// From this point on, guests have nothing to do here.
	if ($context['user']['is_guest'])
		redirectexit();

	// You aren't allowed to not vote not down! (Triple negation! Yay!)
	if ($_REQUEST['sa'] == 'down' && !empty($modSettings['gpbp_disable_negative_voting']))
		fatal_lang_error('gpbp_can_not_vote_down', false);

	// Unauthorized? Bye!
	isAllowedTo('gpbp_vote');

	checkSession('get');

	// Check that the message exists and belongs to the sent topic & board.
	$message = (int) $_REQUEST['msg'];
	if (empty($message))
		fatal_lang_error('gpbp_lacks_msg');
	$request = $smcFunc['db_query']('', '
		SELECT approved, id_member
		FROM {db_prefix}messages
		WHERE id_msg = {int:message}
			AND id_topic = {raw:topic}
			AND id_board = {raw:board}',
		array(
			'message' => $message,
			'topic' => $topic,
			'board' => $board
		)
	);
	if ($smcFunc['db_num_rows']($request) == 0)
		fatal_lang_error('gpbp_msg_not_found', false);
	list ($approved, $id_poster) = $smcFunc['db_fetch_row']($request);
	$smcFunc['db_free_result']($request);
	// And it shouldn't need moderator approval.
	if (empty($approved))
		fatal_lang_error('gpbp_msg_not_approved', false);
	// And no, you can not vote your own posts.
	if ($id_poster == $context['user']['id'])
		fatal_lang_error('gpbp_msg_own_voted', false);

	// Good to go then...
	$request = $smcFunc['db_query']('', '
		SELECT score
		FROM {db_prefix}log_gpbp
		WHERE id_msg = {int:message}
			AND id_member = {raw:user}',
		array(
			'message' => $message,
			'user' => $context['user']['id']
		)
	);

	if ($row = $smcFunc['db_fetch_assoc']($request))
	{
		/*
		 * What does all of this mean?
		 * If you had voted Up before:
		 * 	- Voting Up deletes your vote.
		 *  - Voting Down deletes your vote AND adds a negative vote.
		 * If you had voted Down before... it works the opposite way.
		 * If you had NOT voted, you wouldn't be in this conditional for starts! (check else)
		 * $score is this user's score on the chosen message AFTER voting.
		 * $amount is the value to add to/substract from cumulative values (message count, poster respect)
		 * $success and $opposite are meant for AJAX use.
		 */
		if ($_REQUEST['sa'] == 'up')
			switch ($row['score'])
			{
				case -1:
					$score = 1;
					$amount = 2;
					$success = 'up';
					$opposite = 1;
					break;
				case 1:
					$score = 0;
					$amount = -1;
					$success = 'not up';
					$opposite = 0;
					break;
			}
		else
			switch ($row['score'])
			{
				case -1:
					$score = 0;
					$amount = 1;
					$success = 'not down';
					$opposite = 0;
					break;
				case 1:
					$score = -1;
					$amount = -2;
					$success = 'down';
					$opposite = 1;
					break;
			}
		if (!empty($score))
			$smcFunc['db_query']('', '
				UPDATE {db_prefix}log_gpbp
				SET score = {raw:score}, log_time = {int:time}
				WHERE id_msg = {int:message}
					AND id_member = {raw:user}',
				array(
					'message' => $message,
					'user' => $context['user']['id'],
					'score' => $score,
					'time' => time(),
				)
			);
		// Do not keep empty votes. It's useless and makes tracking stats more difficult. 
		else
			$smcFunc['db_query']('', '
				DELETE FROM {db_prefix}log_gpbp
				WHERE id_msg = {int:message}
					AND id_member = {raw:user}',
				array(
					'message' => $message,
					'user' => $context['user']['id'],
				)
			);
	}
	else
	{
		// Wipe out empty scores.
		$amount = $_REQUEST['sa'] == 'up' ? 1 : -1;
		$success = $_REQUEST['sa'] == 'up' ? 'up' : 'down';
		$opposite = 0;
		$smcFunc['db_insert']('insert',
			'{db_prefix}log_gpbp',
			array(
				'id_msg' => 'int', 'id_member' => 'int', 'score' => 'int', 'id_poster' => 'int', 'log_time' => 'int'
			),
			array(
				$message, $context['user']['id'], $amount, $id_poster, time()
			),
			array()
		);
	}
	$smcFunc['db_free_result']($request);

	$amount = $amount >= 0 ? '+' . $amount : (string) $amount; 
	// Update the post score while we're at it.
	$smcFunc['db_query']('', '
		UPDATE {db_prefix}messages
		SET gpbp_score = gpbp_score ' . $amount . '
		WHERE id_msg = {int:message}',
		array(
			'message' => $message
		)
	);

	// Update the poster respect. He should be glad!
	if (!empty($id_poster))
	{
		// If we are not counting negative votes towards user's Respect values...
		// Don't lower respect when:
		// - Changing an Up vote to Down (instead count it as cancelling a positive vote).
		// - Voting Down the first time.
		if ($modSettings['gpbp_disable_disrespect'] && $amount < 0)
			$amount = $amount == '-2' ? '-1' : ( $success == 'not up' ? '-1' : '+0' );
		// Don't raise respect when:
		// - Changing a Down vote to Up (instead only count it as if a Down vote had never occurred).
		// - Cancelling a Down vote.
		if ($modSettings['gpbp_disable_disrespect'] && $amount > 0)
			$amount = $amount == '+2' ? '+1' : ( $success == 'not down' ? '+0' : '+1' );

		$smcFunc['db_query']('', '
			UPDATE {db_prefix}members
			SET gpbp_respect = gpbp_respect ' . $amount . '
			WHERE id_member = {raw:poster}',
			array(
				'poster' => $id_poster
			)
		);
	}

	if (isset($_REQUEST['xml']))
	{
		loadTemplate('GPBPManage');
		$context['sub_template'] = 'GPBPXml';

		// Retrieve the new vote count for this message.
		$request = $smcFunc['db_query']('', '
			SELECT gpbp_score
			FROM {db_prefix}messages
			WHERE id_msg = {int:message}',
			array(
				'message' => $message
			)
		);
		list ($counter) = $smcFunc['db_fetch_row']($request);
		$smcFunc['db_free_result']($request);

		// Find out the poster's updated Respect amount.
		$new_respect = 0;
		if (!empty($id_poster))
		{
			$request = $smcFunc['db_query']('','
				SELECT gpbp_respect
				FROM {db_prefix}members
				WHERE id_member = {raw:poster}',
				array(
					'poster' => $id_poster
				)
			);
			list ($new_respect) = $smcFunc['db_fetch_row']($request);
			$smcFunc['db_free_result']($request);
		}

		// Get the XML values ready.
		$context['gpbp_vote'] = array(
			'success' => $success,
			'opposite' => $opposite,
			'message' => $message,
			'counter' => $counter,
			'who_id' => $id_poster,
			'who_respect' => $new_respect
		);
	}
	else
	{
		// If this post got hidden, we might still want to see it next.
		$request = $smcFunc['db_query']('', '
			SELECT gpbp_score
			FROM {db_prefix}messages
			WHERE id_msg = {int:message}',
			array(
				'message' => $message
			)
		);
		list ($score) = $smcFunc['db_fetch_row']($request);	
		$smcFunc['db_free_result']($request);

		redirectexit(($score <= $modSettings['gpbp_hide_threshold'] ? 'revealmsg=' . $message .';' : '') . 'topic=' . $topic . '.msg' . $message . '#msg' . $message);
	}
}

function gpbpVotersList() {
	global $smcFunc, $context, $board, $topic, $modSettings;
	
	isAllowedTo('gpbp_voterslist');
	
	checkSession('get');

	// No use in retrieving anything if it's disabled.
	if (! empty($modSettings['gpbp_voters_list_limit']))
	{
		// Check that the message exists and belongs to the sent topic & board.
		$message = (int) $_REQUEST['msg'];
		if (empty($message))
			fatal_lang_error('gpbp_msg_not_found');
		$request = $smcFunc['db_query']('', '
			SELECT approved
			FROM {db_prefix}messages
			WHERE id_msg = {int:message}
				AND id_topic = {raw:topic}
				AND id_board = {raw:board}',
			array(
				'message' => $message,
				'topic' => $topic,
				'board' => $board
			)
		);
		if ($smcFunc['db_num_rows']($request) == 0)
			fatal_lang_error('gpbp_msg_not_found', false);
		list ($approved) = $smcFunc['db_fetch_row']($request);
		$smcFunc['db_free_result']($request);
		// It shouldn't need moderator approval. Silently leave.
		if (empty($approved))
			redirectexit();
	
		// Alright, get the voters list.
		$request = $smcFunc['db_query']('','
			SELECT l.id_member, l.score, m.real_name
			FROM {db_prefix}log_gpbp AS l
				LEFT JOIN {db_prefix}members AS m ON (l.id_member = m.id_member)
			WHERE id_msg = {int:message}
			ORDER BY real_name
			LIMIT {int:list_limit}',
			array(
				'message' => $message,
				'list_limit' => empty($modSettings['gpbp_voters_list_limit']) ? 30 : $modSettings['gpbp_voters_list_limit'],
			)
		);
		$voters = array();
		while ($row = $smcFunc['db_fetch_assoc']($request))
			$voters[] = array(
				'id_member' => $row['id_member'],
				'score' => $row['score'],
				'real_name' => $smcFunc['htmlspecialchars']($row['real_name'], ENT_QUOTES),
			);
		$smcFunc['db_free_result']($request);
	}
	else
	{
		// Why would this be loaded in the first place? Just send empty data.
		$message = 0;
		$voters = array();
	}
	// Prepare to send XML data.
	loadTemplate('GPBPManage');
	$context['sub_template'] = 'GPBPlistXml';
	$context['gpbp_message'] = $message;
	$context['gpbp_voterslist'] = $voters;
}

// Return a list of best scoring topics of the past week
function gpbp_getBestTopics()
{
	global $context, $txt, $smcFunc, $modSettings;

	$data = array();
	$messages = array();
	$aWeekAgo = time() - 3600 * 24 * 7;
	$request = $smcFunc['db_query']('','
		SELECT SUM(score) AS score, id_msg AS msg
		FROM {db_prefix}log_gpbp
		WHERE log_time >= {int:weekago}
		GROUP BY msg 
		ORDER BY score DESC
		LIMIT {int:limit}',
		array(
			'weekago' => $aWeekAgo,
			'limit' => $modSettings['gpbp_show_best_topics']
		)
	);
	while ($row = $smcFunc['db_fetch_assoc']($request))
	{
		$messages[] = $row['msg'];
		$data[$row['msg']] = $row;
	}
	$smcFunc['db_free_result']($request);
	if (! empty($messages))
	{
		// Got 'em posts 'n scores, now reckognize them
		$request = $smcFunc['db_query']('','
			SELECT ms.id_msg, ms.id_topic AS topic, ms.id_member AS member_id, IF( ms.id_member = 0, ms.poster_name, mem.real_name) AS member_name, ms.subject, b.name AS board_name
			FROM {db_prefix}messages AS ms
				LEFT JOIN {db_prefix}members AS mem ON ( ms.id_member = mem.id_member )
				LEFT JOIN {db_prefix}boards AS b ON ( ms.id_board = b.id_board )
			WHERE ms.id_msg IN ({array_int:id_msgs})',
			array( 'id_msgs' => $messages )
		);
		while ($row = $smcFunc['db_fetch_assoc']($request))
			$data[$row['id_msg']] += $row;
		$smcFunc['db_free_result']($request);
		return $data;
	}
	else
		return array();
}

function GPBP_Manage()
{
	global $context, $txt;

	validateSession();
	
	$subActions = array(
		'basic' => 'gpbpManageMain',
		'advanced' => 'gpbpManageAdvanced',
		'tools' => 'gpbpManageTools',
		'savebasic' => 'gpbpSaveBasicSettings',
		'saveboards' => 'gpbpSaveBoardSettings',
		'saveadvanced' => 'gpbpSaveAdvancedSettings',
	);

	$_REQUEST['sa'] = isset($_REQUEST['sa']) && !empty($subActions[$_REQUEST['sa']]) ? $_REQUEST['sa'] : 'basic';

	$context['page_title'] = $txt['gpbp_manage_title'];

	// O hai thar. 
	isAllowedTo('gpbp_manage');

	// Load the template.
	loadTemplate('GPBPManage');

	// Call the right function.
	$subActions[$_REQUEST['sa']]();
}

// Basic configuration page
function gpbpManageMain()
{
	global $context, $smcFunc, $txt;

	$context['page_title'] .= ' - ' . $txt['gpbp_managearea_basic'];
	$context[$context['admin_menu_name']]['tab_data'] = array(
		'title' => $txt['gpbp_managearea_basic'],
		'description' => $txt['gpbp_managearea_basic_desc'],
	);

	// Get the list of boards with this mod enabled.
	$request = $smcFunc['db_query']('', '
		SELECT id_board, name, enable_gpbp
		FROM {db_prefix}boards
		ORDER BY board_order'
	);
	$boardList = array();
	while ($row = $smcFunc['db_fetch_assoc']($request))
		$boardList[] = array(
			'id' => $row['id_board'],
			'name' => trim($row['name']),
			'gpbp' => (bool) $row['enable_gpbp']
		);
	$smcFunc['db_free_result']($request);

	$context['boardlist'] = $boardList;
	$context['sub_template'] = 'main';
}

// Advanced configuration page
function gpbpManageAdvanced()
{
	global $context, $smcFunc, $txt;

	$context['page_title'] .= ' - ' . $txt['gpbp_managearea_advanced'];
	$context[$context['admin_menu_name']]['tab_data'] = array(
		'title' => $txt['gpbp_manage_title'],
		'description' => $txt['gpbp_managearea_advanced_desc'],
	);
	$context['sub_template'] = 'advanced';
}

// GPBP tools page
function gpbpManageTools()
{
	global $context, $smcFunc, $txt;

	$context['page_title'] .= ' - ' . $txt['gpbp_managearea_tools'];
	$context[$context['admin_menu_name']]['tab_data'] = array(
		'title' => $txt['gpbp_manage_title'],
		'description' => $txt['gpbp_managearea_tools_desc'],
	);
	$context['sub_template'] = 'tools';

	$context['gpbp_search_results'] = array();

	if (! empty($_REQUEST['ssa']))
	{
		$ssa = array(
			'search' => 'gpbpManageTools_search',
			'delete' => 'gpbpManageTools_edit',
			'flip' => 'gpbpManageTools_edit',
			'filterdelete' => 'gpbpManageTools_filteredit',
			'filterflip' => 'gpbpManageTools_filteredit',
			'score' => 'gpbpManageTools_score',
			'respect' => 'gpbpManageTools_respect',
			'orphaned' => 'gpbpManageTools_orphaned',
			'totals' => 'gpbpManageTools_totals',
		);
		$_REQUEST['ssa'] = isset($_REQUEST['ssa']) && !empty($ssa[$_REQUEST['ssa']]) ? $_REQUEST['ssa'] : 'search';
		$ssa[$_REQUEST['ssa']]();
	}

	// Find out how many orphaned votes there are.
	$request = $smcFunc['db_query']('','
		SELECT COUNT(*)
		FROM {db_prefix}log_gpbp AS lg
			LEFT JOIN {db_prefix}messages AS ms ON (lg.id_msg = ms.id_msg)
		WHERE ms.id_msg IS NULL');
	list ($orphaned) = $smcFunc['db_fetch_row']($request);
	$smcFunc['db_free_result']($request);
	$context['gpbp_orphaned_report'] = sprintf($txt['gpbp_orphaned_report'], $orphaned);

	// Maybe forgot to choose a filter edit action.
	if (!empty($_REQUEST['filteredit']) && ! empty($_REQUEST['ssa']) && !in_array($_REQUEST['ssa'], array('filterdelete', 'filterflip')))
		fatal_lang_error('gpbp_filter_edit_needed', false);
}

// Handle vote searching
function gpbpManageTools_search()
{
	global $context, $smcFunc, $txt, $scripturl, $modSettings;
	// Fail silently in this case.
	if (!empty($_REQUEST['score']) && ! in_array($_REQUEST['score'], array('1','0','-1')))
		redirectexit();
	// Look for ID's out of membernames
	for ($i = 1; $i <= 2; $i++) {
		$who = $i == 1 ? 'voter' : 'poster';
		if (empty($_REQUEST[$who]))
			continue;
		// Get all the membernames to be added.
		$_REQUEST[$who] = strtr($smcFunc['htmlspecialchars']($_REQUEST[$who], ENT_QUOTES), array('&quot;' => '"'));
		preg_match_all('~"([^"]+)"~', $_REQUEST[$who], $matches);
		$member_names = array_unique(array_merge($matches[1], explode(',', preg_replace('~"[^"]+"~', '', $_REQUEST[$who]))));
	
		foreach ($member_names as $index => $member_name)
		{
			$member_names[$index] = trim($smcFunc['strtolower']($member_names[$index]));
	
			if (strlen($member_names[$index]) == 0)
				unset($member_names[$index]);
			else
				// Only need one name, really.
				break;
		}
	
		$members = array();
		if (!empty($member_names))
		{
			$request = $smcFunc['db_query']('', '
				SELECT id_member
				FROM {db_prefix}members
				WHERE LOWER(member_name) IN ({string:member_name})
					OR LOWER(real_name) IN ({string:member_name})',
				array( 'member_name' => $member_names[0] )
			);
			while ($row = $smcFunc['db_fetch_assoc']($request)) {
				// Wondering why are we in a 2-cycle for loop? Here's why!
				if ($i == 1)
				{
					$voter = $row['id_member'];
					$context['gpbp_found_id_voter'] = $voter;
				}
				else
				{
					$poster = $row['id_member'];
					$context['gpbp_found_id_poster'] = $poster;
				}
			}
			// Don't bother highlighting the related column.
			if (! $smcFunc['db_num_rows']($request))
			{
				$context['gpbp_names_error'] = !empty($context['gpbp_names_error']) ? $txt['gpbp_names_error'] : $txt['gpbp_'. $who .'_not_found'];
				unset($_REQUEST[$who]);
			}
			$smcFunc['db_free_result']($request);
		}
	}

	// Prepare the next queries
	// Clean out IDs just to prevent critical errors from silly admins.
	$array = array();
	$where_conditions = array();
	if (!empty($_REQUEST['topic']))
	{
		$_REQUEST['topic'] = (int) $_REQUEST['topic'];
		$array['topic'] = $_REQUEST['topic'];
		$where_conditions[] = 'mes.id_topic = {int:topic}';
	}
	if (!empty($_REQUEST['message']))
	{
		$_REQUEST['message'] = (int) $_REQUEST['message'];
		$array['message'] = $_REQUEST['message'];
		$where_conditions[] = 'gpbp.id_msg = {int:message}';
	}
	if (!empty($voter))
	{
		$array['voter'] = $voter;
		$where_conditions[] = 'gpbp.id_member = {int:voter}';
	}
	if (!empty($poster))
	{
		$array['poster'] = $poster;
		$where_conditions[] = 'gpbp.id_poster = {int:poster}';
	}
	if (!empty($_REQUEST['score']))
	{
		$array['score'] = $_REQUEST['score'];
		$where_conditions[] = 'gpbp.score = {int:score}';
	}
	$where = empty($where_conditions) ? '' : '
		WHERE ' . implode('
			AND ', $where_conditions);
	// Paging related code.
	if (!empty($_REQUEST['start']))
		$array['start'] = $_REQUEST['start'];
	else
	{
		$_REQUEST['start'] = 0;
		$array['start'] = 0;
	}
	// How many rows would a rower row, if a rower rowed rows? 
	$request = $smcFunc['db_query']('','
		SELECT COUNT(*)
		FROM {db_prefix}log_gpbp AS gpbp
			LEFT JOIN {db_prefix}messages AS mes ON (gpbp.id_msg = mes.id_msg)'.
		$where,
		$array
	);
	list ($rowcount) = $smcFunc['db_fetch_row']($request);
	$context['gpbp_vote_list_rowcount'] = $rowcount;
	$smcFunc['db_free_result']($request);
	$context['page_index'] = constructPageIndex($scripturl . '?action=admin;area=gpbp;sa=tools;'. $context['session_var'] .'='. $context['session_id'] .';ssa=search', $_REQUEST['start'], $rowcount, 50);
	// Get the desired data
	$request = $smcFunc['db_query']('','
		SELECT gpbp.id_msg, mes.id_topic, gpbp.id_member, mem.real_name, gpbp.score, gpbp.id_poster, gpbp.log_time
		FROM {db_prefix}log_gpbp AS gpbp
			LEFT JOIN {db_prefix}messages AS mes ON (gpbp.id_msg = mes.id_msg)
			LEFT JOIN {db_prefix}members AS mem ON (gpbp.id_member = mem.id_member)'.
		$where . '
		ORDER BY mes.id_topic, gpbp.id_msg, gpbp.log_time
		LIMIT {int:start}, 50',
		$array
	);
	$context['gpbp_vote_list_showing'] = sprintf($txt['gpbp_vote_list_showing'], $smcFunc['db_num_rows']($request), $rowcount);
	$rows = array();
	while ($row = $smcFunc['db_fetch_assoc']($request))
	{
		// Deleted members have no name.
		if (! isset($row['real_name']) || is_null($row['real_name']))
			$row['real_name'] = false;
		// Deleted messages have no content, or topic to relate to.
		if (! isset($row['id_topic']) || is_null($row['id_topic']))
			$row['id_topic'] = false;
		// Format me time.
		$row['when'] = timeformat($row['log_time']);
		$rows[] = $row;
	}
	$smcFunc['db_free_result']($request);
	$context['gpbp_topics'] = array();
	$context['gpbp_posters'] = array();
	foreach ($rows as $index => $row)
	{
		if (!empty($row['id_topic']) && ! array_key_exists($row['id_topic'], $context['gpbp_topics']))
		{
			$context['gpbp_topics'][$row['id_topic']] = false;
			if (!empty($row['id_topic']))
			{
				// Associate topic IDs with subjects
				$request = $smcFunc['db_query']('','
					SELECT m.subject
					FROM {db_prefix}topics AS t, {db_prefix}messages AS m
					WHERE t.id_first_msg = m.id_msg
						AND t.id_topic = {int:topic}',
					array(
						'topic' => $row['id_topic']
					)
				);
				// There's no reason why this would be empty, right?
				if ($smcFunc['db_num_rows']($request))
					list($context['gpbp_topics'][$row['id_topic']]) = $smcFunc['db_fetch_row']($request);
				$smcFunc['db_free_result']($request);
			}
		}
		// We need the name of the poster!
		if (! array_key_exists($row['id_poster'], $context['gpbp_posters']))
		{
			// Uhh, maybe the member is a guest?
			if (! empty($row['id_poster'])) {
				$context['gpbp_posters'][$row['id_poster']] = false;
				// Associate poster IDs with their real names
				$request = $smcFunc['db_query']('','
					SELECT real_name
					FROM {db_prefix}members
					WHERE id_member = {int:id_poster}',
					array(
						'id_poster' => $row['id_poster']
					)
				);
				// Have in mind a member might have been deleted
				if ($smcFunc['db_num_rows']($request))
					list($context['gpbp_posters'][$row['id_poster']]) = $smcFunc['db_fetch_row']($request);
				$smcFunc['db_free_result']($request);
			}
			else
				$context['gpbp_posters'][$row['id_poster']] = '<span class="error">Guest.</span>';
		}
	}
	$context['gpbp_search_results'] = $rows;
	$context['gpbp_button_set'] = $modSettings['gpbp_button_set'];
}

// Handle vote editing
function gpbpManageTools_edit()
{
	global $context, $smcFunc, $txt;
	// Look for rows to edit
	if (empty($_REQUEST['vote']) || !is_array($_REQUEST['vote']))
		fatal_lang_error('gpbp_vote_not_sent', false);
	$votes = array();
	foreach ($_REQUEST['vote'] as $vote)
	{
		$vote = explode('msg', $vote);
		if ((int) $vote[0] == 0 || $vote[0] < 0 || (int) $vote[1] == 0 || $vote[1] < 0)
			// Why would this happen? Silently go away.
			redirectexit();
		$votes[] = array(
			(int) $vote[0], // voter ID
			(int) $vote[1] // message ID
		);
	}
	if ($_REQUEST['ssa'] == 'delete')
	{
		$deletecount = 0;
		foreach ($votes as $vote)
		{
			// Shall we update the poster's Respect count?
			if (! empty($_REQUEST['respect']))
				$smcFunc['db_query']('','
					UPDATE {db_prefix}members AS m
						LEFT JOIN {db_prefix}log_gpbp AS lg ON (lg.id_poster = m.id_member)
					SET m.gpbp_respect = m.gpbp_respect - lg.score
					WHERE lg.id_msg = {int:message}
						AND lg.id_member = {int:voter}',
					array(
						'message' => $vote[1],
						'voter' => $vote[0]
					)
				);
			// Update the score for this post, if it exists.
			$smcFunc['db_query']('','
				UPDATE {db_prefix}messages AS ms
					LEFT JOIN {db_prefix}log_gpbp AS lg ON (lg.id_msg = ms.id_msg)
				SET ms.gpbp_score = ms.gpbp_score - lg.score
				WHERE lg.id_msg = {int:message}
					AND lg.id_member = {int:voter}',
				array(
					'message' => $vote[1],
					'voter' => $vote[0]
				)
			);
			// Delete 'em votes.
			$smcFunc['db_query']('','
				DELETE FROM {db_prefix}log_gpbp
				WHERE id_msg = {int:message}
					AND id_member = {int:voter}',
				array(
					'message' => $vote[1],
					'voter' => $vote[0]
				)
			);
			if ($smcFunc['db_affected_rows']())
				$deletecount++;
		}
		// Log this task.
		logAction('gpbp_deleted_votes', array( 'amount' => $deletecount, 'respect' => ! empty($_REQUEST['respect']) ? $txt['gpbp_was'] : $txt['gpbp_was_not'] )); 
		$context['gpbp_votes_edited'] = $deletecount == 1 ? sprintf($txt['gpbp_votes_deleted_s'], $deletecount) : sprintf($txt['gpbp_votes_deleted'], $deletecount);
	}
	// Note that reversing a vote works in a different way than deleting it!
	if ($_REQUEST['ssa'] == 'flip')
	{
		$editcount = 0;
		foreach ($votes as $vote)
		{
			// Shall we update the poster's Respect count? 
			if (! empty($_REQUEST['respect']))
				$smcFunc['db_query']('','
					UPDATE {db_prefix}members AS m
						LEFT JOIN {db_prefix}log_gpbp AS lg ON ( lg.id_poster = m.id_member )
					SET m.gpbp_respect = m.gpbp_respect - ( 2 * lg.score )
					WHERE lg.id_msg = {int:message}
						AND lg.id_member = {int:voter}',
					array(
						'message' => $vote[1],
						'voter' => $vote[0]
					)
				);
			// Update both the score for this post (if it exists) and the vote itself. 
			$smcFunc['db_query']('','
				UPDATE {db_prefix}log_gpbp AS lg
					LEFT JOIN {db_prefix}messages AS ms ON (lg.id_msg = ms.id_msg)
				SET ms.gpbp_score = ms.gpbp_score + ( 2 * lg.score ), lg.score = - lg.score
				WHERE lg.id_msg = {int:message}
					AND lg.id_member = {int:voter}',
				array(
					'message' => $vote[1],
					'voter' => $vote[0]
				)
			);
			if ($smcFunc['db_affected_rows']())
				$editcount++;
		}
		// Log this task.
		logAction('gpbp_reversed_votes', array( 'amount' => $editcount, 'respect' => ! empty($_REQUEST['respect']) ? $txt['gpbp_was'] : $txt['gpbp_was_not'] )); 
		$context['gpbp_votes_edited'] = $editcount == 1 ? sprintf($txt['gpbp_votes_reversed_s'], $editcount) : sprintf($txt['gpbp_votes_reversed'], $editcount);
	}

	// Assuming there's a search filter leftover, load it.
	gpbpManageTools_search();
}

// Massive vote edits! Combat stations!
function gpbpManageTools_filteredit()
{
	global $context, $smcFunc, $txt;

	// Fail silently in this case.
	if (!empty($_REQUEST['score']) && ! in_array($_REQUEST['score'], array('1','0','-1')))
		redirectexit();
	// Prepare the next queries
	$array = array();
	$where_conditions = array();
	if (!empty($_REQUEST['topic']))
	{
		$array['topic'] = $_REQUEST['topic'];
		$where_conditions[] = 'mes.id_topic = {int:topic}';
	}
	if (!empty($_REQUEST['message']))
	{
		$array['message'] = $_REQUEST['message'];
		$where_conditions[] = 'gpbp.id_msg = {int:message}';
	}
	if (!empty($_REQUEST['id_voter']))
	{
		$array['voter'] = $_REQUEST['id_voter'];
		$where_conditions[] = 'gpbp.id_member = {int:voter}';
	}
	if (!empty($_REQUEST['id_poster']))
	{
		$array['poster'] = $_REQUEST['id_poster'];
		$where_conditions[] = 'gpbp.id_poster = {int:poster}';
	}
	if (!empty($_REQUEST['score']))
	{
		$array['score'] = $_REQUEST['score'];
		$where_conditions[] = 'gpbp.score = {int:score}';
	}
	$where = empty($where_conditions) ? '' : '
		WHERE ' . implode('
			AND ', $where_conditions);
	// So, what will it be?
	if ($_REQUEST['ssa'] == 'filterdelete')
	{
		if (!empty($_REQUEST['respect']))
		{
			// Gather grouped respect
			$request = $smcFunc['db_query']('','
				SELECT SUM(gpbp.score) AS respect, gpbp.id_poster, mem.id_member
				FROM {db_prefix}log_gpbp AS gpbp
					LEFT JOIN {db_prefix}messages AS mes ON (gpbp.id_msg = mes.id_msg)
					LEFT JOIN {db_prefix}members AS mem ON (gpbp.id_member = mem.id_member)'.
				$where .'
				GROUP BY gpbp.id_poster
				HAVING gpbp.id_poster != 0
					AND mem.id_member IS NOT NULL',
				$array
			);
			$members = array();
			while ($row = $smcFunc['db_fetch_assoc']($request))
				$members[] = $row;
			$smcFunc['db_free_result']($request);
			// Update per poster
			$updatedMembers = 0;
			foreach ($members as $member)
			{
				// Don't bother if respect doesn't change
				if ($member['respect'] == 0)
					continue;
				$smcFunc['db_query']('','
					UPDATE {db_prefix}members
					SET gpbp_respect = gpbp_respect - {int:respect}
					WHERE id_member = {int:id_poster}',
					array(
						'respect' => $member['respect'],
						'id_poster' => $member['id_poster']
					)
				);
				if ($smcFunc['db_affected_rows']())
					$updatedMembers++;
			}
		}
		else
			$updatedMembers = 0;
		// Gather grouped score.
		$request = $smcFunc['db_query']('','
			SELECT SUM(gpbp.score) AS score, mes.id_msg
			FROM {db_prefix}log_gpbp AS gpbp
					LEFT JOIN {db_prefix}messages AS mes ON (gpbp.id_msg = mes.id_msg)
					LEFT JOIN {db_prefix}members AS mem ON (gpbp.id_member = mem.id_member)'.
			$where.'
			GROUP BY mes.id_msg
			HAVING mes.id_msg IS NOT NULL',
			$array
		);
		$messages = array();
		while ($row = $smcFunc['db_fetch_assoc']($request))
			$messages[] = $row;
		$smcFunc['db_free_result']($request);
		// Update per message
		$updatedMessages = 0;
		foreach ($messages as $message)
		{
			// Don't bother if post score doesn't change
			if ($message['score'] == 0)
				continue;
			$smcFunc['db_query']('','
				UPDATE {db_prefix}messages
				SET gpbp_score = gpbp_score - {int:score}
				WHERE id_msg = {int:id_msg}',
				array(
					'score' => $message['score'],
					'id_msg' => $message['id_msg']
				)
			);
			if ($smcFunc['db_affected_rows']())
				$updatedMessages++;
		}
		// Finally erase 'em votes.
		$request = $smcFunc['db_query']('','
			DELETE gpbp
			FROM {db_prefix}log_gpbp AS gpbp
				LEFT JOIN {db_prefix}messages AS mes ON (gpbp.id_msg = mes.id_msg)
				LEFT JOIN {db_prefix}members AS mem ON (gpbp.id_member = mem.id_member)'.
			$where,
			$array
		);
		$editcount = $smcFunc['db_affected_rows']();
		// Log this task.
		logAction('gpbp_deleted_votes', array( 'amount' => $editcount, 'respect' => ! empty($_REQUEST['respect']) ? $txt['gpbp_was'] : $txt['gpbp_was_not'] )); 
		$context['gpbp_votes_edited_filter'] = $editcount == 1 ? sprintf($txt['gpbp_votes_deleted_s'], $editcount) : sprintf($txt['gpbp_votes_deleted_filter'], $editcount, $updatedMembers, $updatedMessages);
	}
	elseif ($_REQUEST['ssa'] == 'filterflip')
	{
		if (!empty($_REQUEST['respect']))
		{
			// Gather grouped respect
			$request = $smcFunc['db_query']('','
				SELECT SUM(gpbp.score) AS respect, gpbp.id_poster, mem.id_member
				FROM {db_prefix}log_gpbp AS gpbp
					LEFT JOIN {db_prefix}messages AS mes ON (gpbp.id_msg = mes.id_msg)
					LEFT JOIN {db_prefix}members AS mem ON (gpbp.id_member = mem.id_member)'.
				$where .'
				GROUP BY gpbp.id_poster
				HAVING gpbp.id_poster != 0
					AND mem.id_member IS NOT NULL',
				$array
			);
			$members = array();
			while ($row = $smcFunc['db_fetch_assoc']($request))
				$members[] = $row;
			$smcFunc['db_free_result']($request);
			// Update per poster
			$updatedMembers = 0;
			foreach ($members as $member)
			{
				// Don't bother if respect doesn't change
				if ($member['respect'] == 0)
					continue;
				// Note that for reversing respect, we SUBSTRACT TWICE the respect substracted from the post
				$smcFunc['db_query']('','
					UPDATE {db_prefix}members
					SET gpbp_respect = gpbp_respect - ( 2 * {int:respect} )
					WHERE id_member = {int:id_poster}',
					array(
						'respect' => $member['respect'],
						'id_poster' => $member['id_poster']
					)
				);
				if ($smcFunc['db_affected_rows']())
					$updatedMembers++;
			}
		}
		else
			$updatedMembers = 0;
		// Gather grouped score.
		$request = $smcFunc['db_query']('','
			SELECT SUM(gpbp.score) AS score, mes.id_msg
			FROM {db_prefix}log_gpbp AS gpbp
					LEFT JOIN {db_prefix}messages AS mes ON (gpbp.id_msg = mes.id_msg)
					LEFT JOIN {db_prefix}members AS mem ON (gpbp.id_member = mem.id_member)'.
			$where.'
			GROUP BY mes.id_msg
			HAVING mes.id_msg IS NOT NULL',
			$array
		);
		$messages = array();
		while ($row = $smcFunc['db_fetch_assoc']($request))
			$messages[] = $row;
		$smcFunc['db_free_result']($request);
		// Update per message
		$updatedMessages = 0;
		foreach ($messages as $message)
		{
			// Don't bother if post score doesn't change
			if ($message['score'] == 0)
				continue;
			// Note that for reversing post scores, we SUBSTRACT TWICE the score substracted from the post
			$smcFunc['db_query']('','
				UPDATE {db_prefix}messages
				SET gpbp_score = gpbp_score - ( 2 * {int:score} )
				WHERE id_msg = {int:id_msg}',
				array(
					'score' => $message['score'],
					'id_msg' => $message['id_msg']
				)
			);
			if ($smcFunc['db_affected_rows']())
				$updatedMessages++;
		}
		// Finally reverse 'em votes.
		$request = $smcFunc['db_query']('','
			UPDATE {db_prefix}log_gpbp AS gpbp
				LEFT JOIN {db_prefix}messages AS mes ON (gpbp.id_msg = mes.id_msg)
				LEFT JOIN {db_prefix}members AS mem ON (gpbp.id_member = mem.id_member)
			SET gpbp.score = - gpbp.score'.
			$where,
			$array
		);
		$editcount = $smcFunc['db_affected_rows']();
		// Log this task.
		logAction('gpbp_reversed_votes', array( 'amount' => $editcount, 'respect' => ! empty($_REQUEST['respect']) ? $txt['gpbp_was'] : $txt['gpbp_was_not'] )); 
		$context['gpbp_votes_edited_filter'] = $editcount == 1 ? sprintf($txt['gpbp_votes_reversed_s'], $editcount) : sprintf($txt['gpbp_votes_reversed_filter'], $editcount, $updatedMembers, $updatedMessages);

		// Re-run this search.
		gpbpManageTools_search();
	}
}

function gpbpManageTools_score()
{
	global $context, $smcFunc, $txt;

	// This shouldn't fail
	if (! isset($_REQUEST['score']) || ! isset($_REQUEST['chosenmessage']))
		redirectexit();
	$score = (int) $_REQUEST['score'];
	$message = (int) $_REQUEST['chosenmessage'];
	// Update the database.
	$smcFunc['db_query']('','
		UPDATE {db_prefix}messages
		SET gpbp_score = {int:score}
		WHERE id_msg = {int:message}',
		array(
			'score' => $score,
			'message' => $message
		)
	);
	if ($smcFunc['db_affected_rows']())
	{
		$context['gpbp_score_success'] = sprintf($txt['gpbp_score_success'], $message);
		logAction('gpbp_score_updated', array( 'msg' => $message, 'score' => $score )); 
	}
	else
		$context['gpbp_change_error'] = $txt['gpbp_message_not_found'];
}

function gpbpManageTools_respect()
{
	global $context, $smcFunc, $txt;

	// This shouldn't fail
	if (! isset($_REQUEST['respect']))
		redirectexit();
	$respect = (int) $_REQUEST['respect'];
	// Get the member's name
	$_REQUEST['chosenmember'] = strtr($smcFunc['htmlspecialchars']($_REQUEST['chosenmember'], ENT_QUOTES), array('&quot;' => '"'));
	preg_match_all('~"([^"]+)"~', $_REQUEST['chosenmember'], $matches);
	$member_names = array_unique(array_merge($matches[1], explode(',', preg_replace('~"[^"]+"~', '', $_REQUEST['chosenmember']))));

	foreach ($member_names as $index => $member_name)
	{
		$member_names[$index] = trim($smcFunc['strtolower']($member_names[$index]));

		if (strlen($member_names[$index]) == 0)
			unset($member_names[$index]);
		else
			// Only need one name, really.
			break;
	}

	$members = array();
	if (!empty($member_names))
	{
		$request = $smcFunc['db_query']('', '
			SELECT id_member
			FROM {db_prefix}members
			WHERE LOWER(member_name) IN ({string:member_name})
				OR LOWER(real_name) IN ({string:member_name})',
			array( 'member_name' => $member_names[0] )
		);
		if ($row = $smcFunc['db_fetch_assoc']($request)) 
			$member = $row['id_member'];
		else
		{
			$context['gpbp_change_error'] = $txt['gpbp_chosenmember_not_found'];
			unset($_REQUEST['chosenmember']);
			return;
		}
		$smcFunc['db_free_result']($request);
	}
	// Update the database
	$smcFunc['db_query']('','
		UPDATE {db_prefix}members
		SET gpbp_respect = {int:respect}
		WHERE id_member = {int:id_member}',
		array(
			'respect' => $respect,
			'id_member' => $member
		)
	);
	if ($smcFunc['db_affected_rows']())
	{
		$context['gpbp_respect_success'] = sprintf($txt['gpbp_respect_success'], $_REQUEST['chosenmember']);
		logAction('gpbp_respect_updated', array( 'membername' => $_REQUEST['chosenmember'], 'respect' => $respect )); 
	}		
	else
		$context['gpbp_respect_success'] = $txt['gpbp_respect_not_needed'];
}

// Delete votes related to deleted messages.
function gpbpManageTools_orphaned()
{
	global $context, $smcFunc, $txt;

	// Simply done like this.
	$smcFunc['db_query']('','
		DELETE lg
		FROM {db_prefix}log_gpbp AS lg
			LEFT JOIN {db_prefix}messages AS ms ON (lg.id_msg = ms.id_msg)
		WHERE ms.id_msg IS NULL'
	);
	$deleted = $smcFunc['db_affected_rows']();
	if ($deleted)
	{
		$context['gpbp_orphaned_success'] = sprintf($txt['gpbp_orphaned_success'], $deleted);
		logAction('gpbp_clean_orphaned', array( 'orphaned' => $deleted )); 
	}		
}

// Recount all possible values for members and posts
function gpbpManageTools_totals()
{
	global $context, $smcFunc, $txt, $modSettings;

	// Start by setting all values to zero
	$smcFunc['db_query']('','
		UPDATE {db_prefix}messages
		SET gpbp_score = 0'
	);
	$smcFunc['db_query']('','
		UPDATE {db_prefix}members
		SET gpbp_respect = 0'
	);
	
	$members = array();
	$request = $smcFunc['db_query']('','
		SELECT SUM(lg.score) AS total, lg.id_poster
		FROM {db_prefix}log_gpbp AS lg
			LEFT JOIN {db_prefix}members AS mem ON ( lg.id_poster = mem.id_member )
		WHERE mem.id_member IS NOT NULL'. ( $modSettings['gpbp_disable_disrespect'] ? '
			AND lg.score = 1' : '' ) .'
		GROUP BY lg.id_poster'
	);
	$membercount = 0;
	while ($row = $smcFunc['db_fetch_assoc']($request))
	{
		$members[$row['total']][] = $row['id_poster'];
		$membercount++;
	}
	$smcFunc['db_free_result']($request);
	foreach ($members as $respect => $ids)
	{
		// Since they're grouped by score, use an array of members (fewer queries than doing it member by member)
		$smcFunc['db_query']('','
			UPDATE {db_prefix}members
			SET gpbp_respect = {raw:respect}
			WHERE id_member IN ({array_int:ids})',
			array(
				'respect' => (string) $respect,
				'ids' => $ids
			)
		);
	}
	unset($members);
	$messagecount = 0;
	$messages = array();
	$request = $smcFunc['db_query']('','
		SELECT SUM(lg.score) AS total, lg.id_msg
		FROM {db_prefix}log_gpbp AS lg
			LEFT JOIN {db_prefix}messages AS ms ON ( lg.id_msg = ms.id_msg )
		WHERE ms.id_msg IS NOT NULL
		GROUP BY lg.id_msg'
	);
	while ($row = $smcFunc['db_fetch_assoc']($request))
	{
		$messages[$row['total']][] = $row['id_msg'];
		$messagecount++;
	}
	$smcFunc['db_free_result']($request);
	foreach ($messages as $score => $ids)
	{
		// Since the $ids array is bound to be huge (comparing to the recount of members), better to do these queries in parts
		$array = array_chunk($ids, 50);
		foreach ($array as $chunks)
			$smcFunc['db_query']('','
				UPDATE {db_prefix}messages
				SET gpbp_score = {raw:score}
				WHERE id_msg IN ({array_int:ids})',
				array(
					'score' => (string) $score,
					'ids' => $chunks
				)
			);
	}
	unset($messages);
	if ($membercount + $messagecount != 0)
	{
		logAction('gpbp_totals');
		$context['gpbp_totals_success'] = sprintf($txt['gpbp_totals_success'], $membercount, $messagecount);
	}
}

function gpbpSaveBasicSettings()
{
	global $context, $smcFunc, $sourcedir;

	checkSession();

	// Variable cleanup.
	if (! empty($_POST['gpbp_show_best_topics']))
		$_POST['gpbp_show_best_topics'] = (int) $_POST['gpbp_show_best_topics'] < 0 ? 0 : $_POST['gpbp_show_best_topics'];

	require_once($sourcedir . '/ManageServer.php');
	// Basic settings update.
	$config_vars = array(
		array('int', 'gpbp_hide_threshold'),
		array('check', 'gpbp_display_respect'),
		array('check', 'gpbp_display_stats'),
		array('int', 'gpbp_show_best_topics'),
		array('text', 'gpbp_button_set'),
	);
	saveDBSettings($config_vars);
	logAction('gpbp_basic_settings'); 

	redirectexit('action=admin;area=gpbp');
}

function gpbpSaveBoardSettings()
{
	global $context, $smcFunc;

	checkSession();

	// Variable cleanup.
	$boards = array();
	if (!empty($_POST['boards']))
	{
		if (! is_array($_POST['boards']))
			$_POST['boards'] = array();
		foreach ($_POST['boards'] as $board)
			$boards[] = (int) $board;
	}

	// Update boards. First wipe out the setting, then enable where required.
	$smcFunc['db_query']('', '
		UPDATE {db_prefix}boards
		SET enable_gpbp = 0'
	);
	if (! empty($boards))
		$smcFunc['db_query']('', '
			UPDATE {db_prefix}boards
			SET enable_gpbp = 1
			WHERE id_board IN ({array_int:boardlist})',
			array(
				'boardlist' => $boards
			)
		);

	logAction('gpbp_boards'); 

	redirectexit('action=admin;area=gpbp');
}

function gpbpSaveAdvancedSettings()
{
	global $context, $smcFunc, $sourcedir;

	checkSession();

	// Variable cleanup.
	if (! empty($_POST['gpbp_voters_list_limit']))
		$_POST['gpbp_voters_list_limit'] = (int) $_POST['gpbp_voters_list_limit'] < 0 ? 0 : $_POST['gpbp_voters_list_limit'];
	if (! empty($_POST['gpbp_post_count_limit']))
		$_POST['gpbp_post_count_limit'] = (int) $_POST['gpbp_post_count_limit'] < 0 ? 0 : $_POST['gpbp_post_count_limit'];

	require_once($sourcedir . '/ManageServer.php');
	// Now to the general setting.
	$config_vars = array(
		// Just this mod's options
		array('check', 'gpbp_users_choice'),
		array('check', 'gpbp_users_choice_none'),
		array('check', 'gpbp_users_hide_again'),
		array('int', 'gpbp_voters_list_limit'),
		array('check', 'gpbp_disable_negative_voting'),
		array('check', 'gpbp_disable_disrespect'),
		array('int', 'gpbp_post_count_limit'),
	);
	saveDBSettings($config_vars);
	// Log these actions.
	logAction('gpbp_advanced_settings'); 

	redirectexit('action=admin;area=gpbp;sa=advanced');
}
?>