<?php

defined('IN_MOBIQUO') or exit;

// Turn on/off notifications...
function Notify()
{
	global $scripturl, $txt, $topic, $user_info, $context, $smcFunc;

	// Make sure they aren't a guest or something - guests can't really receive notifications!
	is_not_guest();
	isAllowedTo('mark_any_notify');

	// Make sure the topic has been specified.
	if (empty($topic) && !$_GET['isUnsubscribeAll'])
		fatal_lang_error('not_a_topic', false);

	// What do we do?  Better ask if they didn't say..
	if (empty($_GET['sa']))
	{
		// Load the template, but only if it is needed.
		loadTemplate('Notify');

		// Find out if they have notification set for this topic already.
		$request = $smcFunc['db_query']('', '
			SELECT id_member
			FROM {db_prefix}log_notify
			WHERE id_member = {int:current_member}
				AND id_topic = {int:current_topic}
			LIMIT 1',
			array(
				'current_member' => $user_info['id'],
				'current_topic' => $topic,
			)
		);
		$context['notification_set'] = $smcFunc['db_num_rows']($request) != 0;
		$smcFunc['db_free_result']($request);

		// Set the template variables...
		$context['topic_href'] = $scripturl . '?topic=' . $topic . '.' . $_REQUEST['start'];
		$context['start'] = $_REQUEST['start'];
		$context['page_title'] = $txt['notification'];

		return;
	}
	elseif ($_GET['sa'] == 'on')
	{
		checkSession('get');

		// Attempt to turn notifications on.
		$smcFunc['db_insert']('ignore',
			'{db_prefix}log_notify',
			array('id_member' => 'int', 'id_topic' => 'int'),
			array($user_info['id'], $topic),
			array('id_member', 'id_topic')
		);
	}
	else
	{
		checkSession('get');
    if($_GET['isUnsubscribeAll'])
    {
        $smcFunc['db_query']('', '
        			DELETE FROM {db_prefix}log_notify
        			WHERE id_member = {int:current_member}',
    			array(
    				'current_member' => $user_info['id'],
    			)
    		);
    }
    else
    {
    		// Just turn notifications off.
    		$smcFunc['db_query']('', '
        			DELETE FROM {db_prefix}log_notify
        			WHERE id_member = {int:current_member}
        				AND id_topic = {int:current_topic}',
    			array(
    				'current_member' => $user_info['id'],
    				'current_topic' => $topic,
    			)
    		);
    }
	}

	// Send them back to the topic.
	redirectexit('topic=' . $topic . '.' . $_REQUEST['start']);
}

function BoardNotify()
{
	global $scripturl, $txt, $board, $user_info, $context, $smcFunc;
	// Permissions are an important part of anything ;).
	is_not_guest();
	isAllowedTo('mark_notify');
	// You have to specify a board to turn notifications on!
	if (empty($board) && !$_GET['isUnsubscribeAll'])
		fatal_lang_error('no_board', false);

	// No subaction: find out what to do.
	if (empty($_GET['sa']))
	{
		// We're gonna need the notify template...
		loadTemplate('Notify');

		// Find out if they have notification set for this topic already.
		$request = $smcFunc['db_query']('', '
			SELECT id_member
			FROM {db_prefix}log_notify
			WHERE id_member = {int:current_member}
				AND id_board = {int:current_board}
			LIMIT 1',
			array(
				'current_board' => $board,
				'current_member' => $user_info['id'],
			)
		);
		$context['notification_set'] = $smcFunc['db_num_rows']($request) != 0;
		$smcFunc['db_free_result']($request);

		// Set the template variables...
		$context['board_href'] = $scripturl . '?board=' . $board . '.' . $_REQUEST['start'];
		$context['start'] = $_REQUEST['start'];
		$context['page_title'] = $txt['notification'];
		$context['sub_template'] = 'notify_board';

		return;
	}
	// Turn the board level notification on....
	elseif ($_GET['sa'] == 'on')
	{
		checkSession('get');

		// Turn notification on.  (note this just blows smoke if it's already on.)
		$smcFunc['db_insert']('ignore',
			'{db_prefix}log_notify',
			array('id_member' => 'int', 'id_board' => 'int'),
			array($user_info['id'], $board),
			array('id_member', 'id_board')
		);
	}
	// ...or off?
	else
	{
    checkSession('get');
    if ($_GET['isUnsubscribeAll'])
    {
        $smcFunc['db_query']('', '
            	DELETE FROM {db_prefix}log_notify
            	WHERE id_member = {int:current_member}',
        	array(
        		'current_member' => $user_info['id'],
        	)
        );
    }
    else
    {
        // Turn notification off for this board.
        $smcFunc['db_query']('', '
            	DELETE FROM {db_prefix}log_notify
            	WHERE id_member = {int:current_member}
            		AND id_board = {int:current_board}',
        	array(
        		'current_board' => $board,
        		'current_member' => $user_info['id'],
        	)
        );
    }
	}

	// Back to the board!
	redirectexit('board=' . $board . '.' . $_REQUEST['start']);
}

?>