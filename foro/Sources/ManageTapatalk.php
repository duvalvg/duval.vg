<?php
if (!defined('SMF'))
	die('Hacking attempt...');
	
// This function passes control through to the relevant tab.
function ManageTapatalk()
{
	global $context, $txt, $scripturl, $modSettings, $settings, $sourcedir;

	// You need to be an admin to edit settings!
	isAllowedTo('admin_forum');

	loadLanguage('Help');
	loadLanguage('ManageSettings');

	$context['page_title'] = $txt['tapatalktitle'];

	// Will need the utility functions from here.
	require_once($sourcedir . '/ManageServer.php');

	// This is the standard Template sometimes i need a diffrent one :P
	$context['sub_template'] = 'show_settings';

	$subActions = array(
		'general' => 'ManageTapatalkGeneral',
		'others' => 'ManageTapatalkOthers',
		'boards' => 'ManageTapatalkBoards',
	);

	// Load up all the tabs...
	$context[$context['admin_menu_name']]['tab_data'] = array(
		'title' => &$txt['tapatalktitle'],
		'tabs' => array(
			'general' => array(
				'label' => $txt['tp_general_settings'],
				'description' => $txt['tp_general_settingsDesc'],
			),
			'boards' => array(
				'label' => $txt['tp_board_settings'],
				'description' => $txt['tp_board_settingsDesc'],
			),
			'others' => array(
				'label' => $txt['tp_other_settings'],
				'description' => $txt['tp_other_settingsDesc'],
			),
		),
	);
	// Call the right function for this sub-acton.
	if(!empty($_REQUEST['sa']) && !empty($subActions[$_REQUEST['sa']]))
		$subActions[$_REQUEST['sa']]();
	else
		$subActions['general']();
}

function ManageTapatalkGeneral($return_config = false)
{
	global $txt, $scripturl, $context, $settings, $sc, $modSettings;

	$config_vars = array(
			array('check', 'tapatalkEnabled'),
			array('check', 'tp_pushEnabled'),
			array('check', 'tp_guestOkayEnabled'),
			array('check', 'tp_tapatalkNotifierForChromeEnabled'),
			array('text', 'tp_register_page_url', 'value'=> isset($modSettings['tp_register_page_url'])? $modSettings['tp_register_page_url']: 'index.php?action=register', 'size' => '30'),
	);

	if ($return_config)
		return $config_vars;

	// Saving?
	if (isset($_GET['save']))
	{
		saveDBSettings($config_vars);
		redirectexit('action=admin;area=tapatalksettings;');
	}

	$context['post_url'] = $scripturl . '?action=admin;area=tapatalksettings;save';
	$context['settings_title'] = $txt['tapatalktitle'];

	prepareDBSettingContext($config_vars);
}

function ManageTapatalkBoards($return_config = false)
{
	global $context, $txt, $settings, $user_info, $modSettings, $smcFunc, $scripturl;
	// Saving?
	if (isset($_GET['save']))
	{
		//Nothing to do?
		if(!empty($_POST['brd']))
		{
			fix_brd_value();
			//No Double Entries ;)
			$_POST['brd'] = array_unique($_POST['brd']);
			$brds = implode(",",$_POST['brd']);
		}
		$hide_boards = array(
			'boards_hide_for_tapatalk' => empty($brds)? '': $brds,
		);
		updateSettings($hide_boards);

		//Redirect ;)
		redirectexit('action=admin;area=tapatalksettings;sa=boards');
	}

    $boards_hide_for_tp = array();
    if (isset($modSettings['boards_hide_for_tapatalk']))
    {
        $boards_from_setting = explode(",",$modSettings['boards_hide_for_tapatalk']);
        foreach($boards_from_setting as $arr_id => $board_id)
            $boards_hide_for_tp[$board_id] = $board_id;
         
    }
    else
    {
    	$request = $smcFunc['db_query']('', '
    		SELECT variable
    		FROM {db_prefix}settings
    		WHERE variable = \'boards_hide_for_tapatalk\''
    	);
    	$row = $smcFunc['db_fetch_assoc']($request);
    	if (!empty($row['variable']))
    	{
    	    $boards_from_setting =  explode(',',$row['variable']);
             foreach($boards_from_setting as $arr_id => $board_id)
                $boards_hide_for_tp[$board_id] = $board_id;
        }
        else
        {
            // if enabled_boards_for_tapatalk not set in smf settings, select all boards as default
        	$another_request = $smcFunc['db_query']('', '
        		SELECT id_board
        		FROM {db_prefix}boards'
            );
            while($row1 = $smcFunc['db_fetch_assoc']($another_request))
	            $boards_hide_for_tp[$row['id_board']] = $row1['id_board'];
        }
    }
	// Find all the boards this user is allowed to see.
	$request = $smcFunc['db_query']('', "
		SELECT b.id_cat, c.name AS cat_name, b.id_board, b.name, b.child_level
		FROM {db_prefix}boards AS b
			LEFT JOIN {db_prefix}categories AS c ON (c.id_cat = b.id_cat)
		WHERE 1");
	$context['num_boards'] = $smcFunc['db_num_rows']($request);
	$context['categories'] = array();
	while ($row = $smcFunc['db_fetch_assoc']($request))
	{
		// This category hasn't been set up yet..
		if (!isset($context['categories'][$row['id_cat']]))
			$context['categories'][$row['id_cat']] = array(
				'id' => $row['id_cat'],
				'name' => $row['cat_name'],
				'boards' => array()
			);
		// Set this board up, and let the template know when it's a child.  (indent them..)
		$context['categories'][$row['id_cat']]['boards'][$row['id_board']] = array(
			'id' => $row['id_board'],
			'name' => $row['name'],
			'is_moderator' => isset($boards_hide_for_tp[$row['id_board']]),
			'child_level' => $row['child_level'],
		);
	}
	mysql_free_result($request);

	// Now, let's sort the list of categories into the boards for templates that like that.
	$temp_boards = array();
	foreach ($context['categories'] as $category)
	{
		$temp_boards[] = array(
			'name' => $category['name'],
			'child_ids' => array_keys($category['boards'])
		);
		$temp_boards = array_merge($temp_boards, array_values($category['boards']));
	}

	$max_boards = ceil(count($temp_boards) / 2);
	if ($max_boards == 1)
		$max_boards = 2;

	// Now, alternate them so they can be shown left and right ;).
	$context['board_columns'] = array();
	for ($i = 0; $i < $max_boards; $i++)
	{
		$context['board_columns'][] = $temp_boards[$i];
		if (isset($temp_boards[$i + $max_boards]))
			$context['board_columns'][] = $temp_boards[$i + $max_boards];
		else
			$context['board_columns'][] = array();
	}

	$context['all_checked'] = $context['num_boards'] == count($boards_hide_for_tp);
	$context['post_url'] = $scripturl . '?action=admin;area=tapatalksettings;sa=boards;save;';
	$context['settings_title'] = $txt['tapatalktitle'];

	// Load a diffrent Template
	loadTemplate('Tapatalk');
	$context['sub_template'] = 'tapatalk_show_boards';
}
function hide_boards(&$boards_hide_for_tapatalk, $board_content, $hide_all_children = false)
{
    if(empty($board_content['children']))
        return;
    foreach($board_content['children'] as $board_id => $_board_content)
    {
        if ($hide_all_children)
        {
            $boards_hide_for_tapatalk[] = $board_id;
        }
        else
        {
            if (isset($modSettings['tp_Board('.$board_id.')']))
            {
                if($modSettings['tp_Board('.$board_id.')'] == 0)
                {
                    $boards_hide_for_tapatalk[] = $board_id;
                    $hide_all_children = true;
                }
            }
            else
                $boards_hide_for_tapatalk[] = $board_id;
        }
        hide_boards($boards_hide_for_tapatalk, $_board_content, $hide_all_children);
    }
}

function orgnize_config_vars_tree(&$_config_vars, $board_content)
{
    global $txt;
    if(empty($board_content['children']))
        return;
    foreach($board_content['children'] as $board_id => $_board_content)
    {
        $txt['tp_Board('.$board_id.')'] = $_board_content['name'];
        $_config_vars[] = array('check', 'tp_Board('.$board_id.')'); 
        orgnize_config_vars_tree($_config_vars, $_board_content);
    }
}
function fix_brd_value()
{
    global  $boards, $boardList, $tapatalk_board_tree, $cat_tree, $sourcedir;
    
    require_once($sourcedir . '/Subs-Boards.php');
    getBoardTree();
    foreach ($cat_tree as $catID => $node)
	{
		recursiveTpBoards($node);
	}
}
function recursiveTpBoards(&$_tree, $hide_all_under_tree = false)
{
	if (empty($_tree['children']))
		return;
	foreach ($_tree['children'] as $id => $node)
	{
	    $_hide_all_under_tree = $hide_all_under_tree;
	    $mark_get_equal_id = false;
	    $same_id_exist = false;
	    foreach($_POST['brd'] as $temp_id => $brd_id)
	    {
            if(!$same_id_exist)
            {
                    if($brd_id == $id)
                    {
                        $_hide_all_under_tree = true;
                        $same_id_exist = true;
                    }
            }
	    }
	    if(!$same_id_exist && $_hide_all_under_tree){
	         $_POST['brd'][] = $id; 
	        }
	    
		recursiveTpBoards($node, $_hide_all_under_tree);
	}
}

function ManageTapatalkOthers($return_config = false)
{
	global $txt, $scripturl, $context, $settings, $sc, $modSettings;
    
    $context['settings_save_dont_show'] = 1;
	$config_vars = array(
			array('var_message', 'taptalk_descp'),
	);
	if ($return_config)
		return $config_vars;
	// Saving?
	if (isset($_GET['save']))
	{
		saveDBSettings($config_vars);
		redirectexit('action=admin;area=tapatalksettings;');
	}

	$context['post_url'] = $scripturl . '?action=admin;area=tapatalksettings;save';
	$context['settings_title'] = $txt['tapatalktitle'];

	prepareDBSettingContext($config_vars);
}



?>