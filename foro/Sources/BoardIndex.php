<?php

/**
 * Simple Machines Forum (SMF)
 *
 * @package SMF
 * @author Simple Machines http://www.simplemachines.org
 * @copyright 2011 Simple Machines
 * @license http://www.simplemachines.org/about/smf/license.php BSD
 *
 * @version 2.0
 */

if (!defined('SMF'))
	die('Hacking attempt...');

/*	The single function this file contains is used to display the main
	board index.  It uses just the following functions:

	void BoardIndex()
		- shows the board index.
		- uses the BoardIndex template, and main sub template.
		- may use the boardindex subtemplate for wireless support.
		- updates the most online statistics.
		- is accessed by ?action=boardindex.

	void CollapseCategory()
		- collapse or expand a category
*/

// Show the board index!
function BoardIndex()
{
	global $txt, $user_info, $sourcedir, $modSettings, $context, $settings, $scripturl, $smcFunc;

	// For wireless, we use the Wireless template...
	if (WIRELESS)
		$context['sub_template'] = WIRELESS_PROTOCOL . '_boardindex';
	else
		loadTemplate('BoardIndex');

	// Set a canonical URL for this page.
	$context['canonical_url'] = $scripturl;

	// Do not let search engines index anything if there is a random thing in $_GET.
	if (!empty($_GET))
		$context['robot_no_index'] = true;

	// Retrieve the categories and boards.
	require_once($sourcedir . '/Subs-BoardIndex.php');
	$boardIndexOptions = array(
		'include_categories' => true,
		'base_level' => 0,
		'parent_id' => 0,
		'set_latest_post' => true,
		'countChildPosts' => !empty($modSettings['countChildPosts']),
	);
	$context['categories'] = getBoardIndex($boardIndexOptions);

	// Get the user online list.
	require_once($sourcedir . '/Subs-MembersOnline.php');
	$membersOnlineOptions = array(
		'show_hidden' => allowedTo('moderate_forum'),
		'sort' => 'log_time',
		'reverse_sort' => true,
	);
	$context += getMembersOnlineStats($membersOnlineOptions);

	$context['show_buddies'] = !empty($user_info['buddies']);

	// Are we showing all membergroups on the board index?
	if (!empty($settings['show_group_key']))
		$context['membergroups'] = cache_quick_get('membergroup_list', 'Subs-Membergroups.php', 'cache_getMembergroupList', array());

	// Track most online statistics? (Subs-MembersOnline.php)
	if (!empty($modSettings['trackStats']))
		trackStatsUsersOnline($context['num_guests'] + $context['num_spiders'] + $context['num_users_online']);

	// Retrieve the latest posts if the theme settings require it.
	if (isset($settings['number_recent_posts']) && $settings['number_recent_posts'] > 1)
	{
		$latestPostOptions = array(
			'number_posts' => $settings['number_recent_posts'],
		);
		$context['latest_posts'] = cache_quick_get('boardindex-latest_posts:' . md5($user_info['query_wanna_see_board'] . $user_info['language']), 'Subs-Recent.php', 'cache_getLastPosts', array($latestPostOptions));
	}

	$settings['display_recent_bar'] = !empty($settings['number_recent_posts']) ? $settings['number_recent_posts'] : 0;
	$settings['show_member_bar'] &= allowedTo('view_mlist');
	$context['show_stats'] = allowedTo('view_stats') && !empty($modSettings['trackStats']);
	$context['show_member_list'] = allowedTo('view_mlist');
	$context['show_who'] = allowedTo('who_view') && !empty($modSettings['who_enabled']);

	// Load the calendar?
	if (!empty($modSettings['cal_enabled']) && allowedTo('calendar_view'))
	{
		// Retrieve the calendar data (events, birthdays, holidays).
		$eventOptions = array(
			'include_holidays' => $modSettings['cal_showholidays'] > 1,
			'include_birthdays' => $modSettings['cal_showbdays'] > 1,
			'include_events' => $modSettings['cal_showevents'] > 1,
			'num_days_shown' => empty($modSettings['cal_days_for_index']) || $modSettings['cal_days_for_index'] < 1 ? 1 : $modSettings['cal_days_for_index'],
		);
		$context += cache_quick_get('calendar_index_offset_' . ($user_info['time_offset'] + $modSettings['time_offset']), 'Subs-Calendar.php', 'cache_getRecentEvents', array($eventOptions));

		// Whether one or multiple days are shown on the board index.
		$context['calendar_only_today'] = $modSettings['cal_days_for_index'] == 1;

		// This is used to show the "how-do-I-edit" help.
		$context['calendar_can_edit'] = allowedTo('calendar_edit_any');
	}
	else
		$context['show_calendar'] = false;

	// GPBP: Load Best topics of the week?
	if (!empty($modSettings['gpbp_show_best_topics']))
	{
		require_once($sourcedir . '/GoodPostBadPost.php');
		$context['gpbp_best_topics'] = gpbp_getBestTopics();
	}

	$context['page_title'] = sprintf($txt['forum_index'], $context['forum_name']);
	//ajax chat start
	$userIDs = chatOnlineUsers();
	$context['chat_links'] = array();
	if (count($userIDs) > 0){
		if (count($userIDs) == 1){
			$result = $smcFunc['db_query']('', '
   			SELECT mem.ID_MEMBER, mem.real_name, mem.ID_GROUP, mg.online_color, mg.ID_GROUP
			 		FROM	{db_prefix}members AS mem 
			 		LEFT JOIN {db_prefix}membergroups AS mg ON 
			 		(mg.ID_GROUP = IF(mem.ID_GROUP = 0, mem.ID_POST_GROUP, mem.ID_GROUP)) WHERE mem.ID_MEMBER = {int:the_id}' ,
  	 		array(
      		'the_id' => $userIDs[0],
   				)
  			);
			}else{
				$result = $smcFunc['db_query']('', '
	   			SELECT mem.ID_MEMBER, mem.real_name, mem.ID_GROUP, mg.online_color, mg.ID_GROUP
			 			FROM	{db_prefix}members AS mem 
			 			LEFT JOIN {db_prefix}membergroups AS mg ON 
			 			(mg.ID_GROUP = IF(mem.ID_GROUP = 0, mem.ID_POST_GROUP, mem.ID_GROUP)) WHERE mem.ID_MEMBER IN ({array_int:the_ids})',
   				array(
      			'the_ids' => $userIDs,
  	 			)
  			);				
			}
			while ($row =$smcFunc['db_fetch_assoc']($result))
				{
				$link = '<a href="' . $scripturl . '?action=profile;u=' . $row['ID_MEMBER'];
				if($row['online_color'] != ""){
					$link.= '" style="color: ' . $row['online_color'];
				}
				$link.= '">' . $row['real_name'] . '</a>';
   			array_push($context['chat_links'], $link);
				}
			$smcFunc['db_free_result']($result);
			}
		//set the flag true for home page
		$context['chat_isHome'] = true;
		//end ajax chat
}

// Collapse or expand a category
function CollapseCategory()
{
	global $user_info, $sourcedir, $context;

	// Just in case, no need, no need.
	$context['robot_no_index'] = true;

	checkSession('request');

	if (!isset($_GET['sa']))
		fatal_lang_error('no_access', false);

	// Check if the input values are correct.
	if (in_array($_REQUEST['sa'], array('expand', 'collapse', 'toggle')) && isset($_REQUEST['c']))
	{
		// And collapse/expand/toggle the category.
		require_once($sourcedir . '/Subs-Categories.php');
		collapseCategories(array((int) $_REQUEST['c']), $_REQUEST['sa'], array($user_info['id']));
	}

	// And go back to the board index.
	BoardIndex();
}

?>