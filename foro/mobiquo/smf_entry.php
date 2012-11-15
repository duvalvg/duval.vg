<?php

defined('IN_MOBIQUO') or exit;
$forum_version = 'SMF 2.0';

// Get everything started up...
define('SMF', 1);

//error_reporting(defined('E_STRICT') ? E_ALL | E_STRICT : E_ALL);
error_reporting(0);

$time_start = microtime();

// This makes it so headers can be sent!
ob_start();

// Do some cleaning, just in case.
foreach (array('db_character_set', 'cachedir') as $variable)
    if (isset($GLOBALS[$variable]))
        unset($GLOBALS[$variable]);

// Load the settings...
require_once(dirname(dirname(__FILE__)) . '/Settings.php');

// Make absolutely sure the cache directory is defined.
if ((empty($cachedir) || !file_exists($cachedir)) && file_exists($boarddir . '/cache'))
    $cachedir = $boarddir . '/cache';

// And important includes.
require_once($sourcedir . '/QueryString.php');
require_once('include/Subs.php');
require_once('include/error_control.php');
require_once('include/Load.php');
require($sourcedir . '/Security.php');

// Using an pre-PHP5 version?
if (@version_compare(PHP_VERSION, '5') == -1)
    require_once($sourcedir . '/Subs-Compat.php');

// If $maintenance is set specifically to 2, then we're upgrading or something.
if (!empty($maintenance) && $maintenance == 2)
    db_fatal_error();

// Create a variable to store some SMF specific functions in.
$smcFunc = array();

// Initate the database connection and define some database functions to use.
loadDatabase();

// Load the settings from the settings table, and perform operations like optimizing.
reloadSettings();
$_SERVER['QUERY_STRING'] = '';

// Clean the request variables, add slashes, etc.
cleanRequest();
$context = array();

// Seed the random generator.
if (empty($modSettings['rand_seed']) || mt_rand(1, 250) == 69)
    smf_seed_generator();

// Before we get carried away, are we doing a scheduled task? If so save CPU cycles by jumping out!
if (isset($_GET['scheduled']))
{
    require_once($sourcedir . '/ScheduledTasks.php');
    AutoTask();
}

// Check if compressed output is enabled, supported, and not already being done.
//if (!empty($modSettings['enableCompressedOutput']) && !headers_sent())
//{
//    // If zlib is being used, turn off output compression.
//    if (@ini_get('zlib.output_compression') == '1' || @ini_get('output_handler') == 'ob_gzhandler' || @version_compare(PHP_VERSION, '4.2.0') == -1)
//        $modSettings['enableCompressedOutput'] = '0';
//    else
//    {
//        ob_end_clean();
//        ob_start('ob_gzhandler');
//    }
//}

// Register an error handler.
set_error_handler('error_handler');

//set topic and board for mobiquo function
set_topic_and_board_by_message();

// Start the session. (assuming it hasn't already been.)
loadSession();
$sc = $_SESSION['session_value'];
$_GET[$_SESSION['session_var']] = $_SESSION['session_value'];
$_POST[$_SESSION['session_var']] = $_SESSION['session_value'];

define('WIRELESS', false);

// What function shall we execute? (done like this for memory's sake.)
call_user_func(smf_main());

// Call obExit specially; we're coming from the main area ;).
//obExit(null, null, true);

// Clear out the stat cache.
trackStats();

// If we have mail to send, send it.
if (!empty($context['flush_mail']))
    AddMailQueue(true);

//-------------do something after action--------------
if (function_exists('after_action_'.$request_name))
    call_user_func('after_action_'.$request_name);

if (!empty($settings['strict_doctype']))
{
    // The theme author wants to use the STRICT doctype (only God knows why).
    ob_get_contents();
    if (function_exists('ob_clean'))
        ob_clean();
    else
    {
        ob_end_clean();
        ob_start();
    }
}

// The main controlling function.
function smf_main()
{
    global $modSettings, $settings, $user_info, $board, $topic, $board_info, $maintenance, $sourcedir, $request_name, $txt, $user_settings, $mobiquo_config, $topic_per_page, $limit_num;

    // Load the user's cookie (or set as guest) and load their settings.
    loadUserSettings();
    
    // Load the current board's information.
    loadBoard();

    // Load the current user's permissions.
    loadPermissions();
    
    // Attachments don't require the entire theme to be loaded.
    loadTheme();
    header('Mobiquo_is_login:'.($GLOBALS['context']['user']['is_logged'] ? 'true' : 'false'));
    
    // Check if the user should be disallowed access.
    if (!in_array($request_name, array('get_config', 'login')))
        is_not_banned();
    
    // If we are in a topic and don't have permission to approve it then duck out now.
    if (!empty($topic) && empty($board_info['cur_topic_approved']) && !allowedTo('approve_posts') && ($user_info['id'] != $board_info['cur_topic_starter'] || $user_info['is_guest']))
        //fatal_lang_error('not_a_topic', false);
        get_error('The topic is not approved');

    // Do some logging, unless this is an attachment, avatar, toggle of editor buttons, theme option, XML feed etc.
    if (empty($_REQUEST['action']) || !in_array($_REQUEST['action'], array('dlattach', 'findmember', 'jseditor', 'jsoption', 'requestmembers', 'smstats', '.xml', 'xmlhttp', 'verificationcode', 'viewquery', 'viewsmfile')))
    {
        // Log this user as online.
        writeLog();

        // Track forum statistics and hits...?
        if (!empty($modSettings['hitStats']))
            trackStats(array('hits' => '+'));
    }

    // Is the forum in maintenance mode? (doesn't apply to administrators.)
    if (!empty($maintenance) && !allowedTo('admin_forum'))
    {
        if ($request_name != 'get_config' && $request_name != 'login') {
            get_error($txt['maintain_mode_on']);
        }
    }
    // If guest access is off, a guest can only do one of the very few following actions.
    elseif ((empty($modSettings['allow_guestAccess']) || !$modSettings['tp_guestOkayEnabled']) && $user_info['is_guest'] && (!isset($_REQUEST['action']) || !in_array($_REQUEST['action'], array('coppa', 'login', 'login2', 'register', 'register2', 'reminder', 'activate', 'help', 'smstats', 'mailq', 'verificationcode', 'openidreturn',))))
    {
        if ($request_name != 'get_config') {
            loadLanguage('Login');
            get_error($txt['only_members_can_access']);
            //require_once($sourcedir . '/Subs-Auth.php');
            //return 'KickGuest';
        } else {
            $modSettings['tp_guestOkayEnabled'] = 0;
        }
    }
    
    //-------------transform input data to local character set if needed
    utf8_to_local();
    //-------------change some setting for tapatalk display
    $settings['message_index_preview'] = 1;
    $modSettings['todayMod_bak'] = $modSettings['todayMod'];
    $modSettings['todayMod'] = 0;
    $user_settings['pm_prefs'] = 0;
    $user_info['user_time_format'] = $user_info['time_format'];
    $user_info['time_format'] = '%Y%m%dT%H:%M:%S+00:00';
    $modSettings['disableCustomPerPage'] = 1;
    $modSettings['disableCheckUA'] = 1;
    $modSettings['defaultMaxMessages'] = isset($limit_num) ? $limit_num : 20;
    $modSettings['defaultMaxMembers'] = 100;
    $modSettings['search_results_per_page'] = isset($topic_per_page) && $topic_per_page > 0 ? $topic_per_page : 20;
    $modSettings['defaultMaxTopics'] = isset($topic_per_page) && $topic_per_page > 0 ? $topic_per_page : 20;
    $modSettings['disable_pm_verification'] = $mobiquo_config['disable_pm_verification'];
    //-------------do something before action--------------

    if (function_exists('before_action_'.$request_name))
        call_user_func('before_action_'.$request_name);
    if (empty($_REQUEST['action']) && !empty($board))
    {
        if (empty($topic))
        {
            require_once('include/MessageIndex.php');
            return 'MessageIndex';
        }
        // Board is not empty... topic is not empty... action is empty.. Display!
        else
        {
            require_once('include/Display.php');
            return 'Display';
        }
    }
    // Here's the monstrous $_REQUEST['action'] array - $_REQUEST['action'] => array($file, $function).
    $actionArray = array(
        'activate' => array('Register.php', 'Activate'),
        'admin' => array('Admin.php', 'AdminMain'),
        'announce' => array('Post.php', 'AnnounceTopic'),
        'attachapprove' => array('ManageAttachments.php', 'ApproveAttach'),
        'buddy' => array('Subs-Members.php', 'BuddyListToggle'),
        'calendar' => array('Calendar.php', 'CalendarMain'),
        'clock' => array('Calendar.php', 'clock'),
        'collapse' => array('BoardIndex.php', 'CollapseCategory'),
        'coppa' => array('Register.php', 'CoppaForm'),
        'credits' => array('Who.php', 'Credits'),
        'deletemsg' => array('RemoveTopic.php', 'DeleteMessage'),
        'display' => array('Display.php', 'Display'),
        'dlattach' => array('Display.php', 'Download'),
        'editpoll' => array('Poll.php', 'EditPoll'),
        'editpoll2' => array('Poll.php', 'EditPoll2'),
        'emailuser' => array('SendTopic.php', 'EmailUser'),
        'findmember' => array('Subs-Auth.php', 'JSMembers'),
        'groups' => array('Groups.php', 'Groups'),
        'help' => array('Help.php', 'ShowHelp'),
        'helpadmin' => array('Help.php', 'ShowAdminHelp'),
        'im' => array('PersonalMessage.php', 'MessageMain'),
        'jseditor' => array('Subs-Editor.php', 'EditorMain'),
        'jsmodify' => array('Post.php', 'JavaScriptModify'),
        'jsoption' => array('Themes.php', 'SetJavaScript'),
        'lock' => array('LockTopic.php', 'LockTopic'),
        'lockvoting' => array('Poll.php', 'LockVoting'),
        'login' => array('LogInOut.php', 'Login'),
        'login2' => array('LogInOut.php', 'Login2'),
        'logout' => array('LogInOut.php', 'Logout'),
        'markasread' => array('Subs-Boards.php', 'MarkRead'),
        'mergetopics' => array('SplitTopics.php', 'MergeTopics'),
        'mlist' => array('Memberlist.php', 'Memberlist'),
        'moderate' => array('ModerationCenter.php', 'ModerationMain'),
        'modifycat' => array('ManageBoards.php', 'ModifyCat'),
        'modifykarma' => array('Karma.php', 'ModifyKarma'),
        'movetopic' => array('MoveTopic.php', 'MoveTopic'),
        'movetopic2' => array('MoveTopic.php', 'MoveTopic2'),
        'notify' => array('Notify.php', 'Notify'),
        'notifyboard' => array('Notify.php', 'BoardNotify'),
        'openidreturn' => array('Subs-OpenID.php', 'smf_openID_return'),
        'pm' => array('PersonalMessage.php', 'MessageMain'),
        'post' => array('Post.php', 'Post'),
        'post2' => array('Post.php', 'Post2'),
        'printpage' => array('Printpage.php', 'PrintTopic'),
        'profile' => array('Profile.php', 'ModifyProfile'),
        'quotefast' => array('Post.php', 'QuoteFast'),
        'quickmod' => array('MessageIndex.php', 'QuickModeration'),
        'quickmod2' => array('Display.php', 'QuickInTopicModeration'),
        'recent' => array('Recent.php', 'RecentPosts'),
        'register' => array('Register.php', 'Register'),
        'register2' => array('Register.php', 'Register2'),
        'reminder' => array('Reminder.php', 'RemindMe'),
        'removepoll' => array('Poll.php', 'RemovePoll'),
        'removetopic2' => array('RemoveTopic.php', 'RemoveTopic2'),
        'reporttm' => array('SendTopic.php', 'ReportToModerator'),
        'requestmembers' => array('Subs-Auth.php', 'RequestMembers'),
        'restoretopic' => array('RemoveTopic.php', 'RestoreTopic'),
        'search' => array('Search.php', 'PlushSearch1'),
        'search2' => array('Search.php', 'PlushSearch2'),
        'sendtopic' => array('SendTopic.php', 'EmailUser'),
        'smstats' => array('Stats.php', 'SMStats'),
        'suggest' => array('Subs-Editor.php', 'AutoSuggestHandler'),
        'spellcheck' => array('Subs-Post.php', 'SpellCheck'),
        'splittopics' => array('SplitTopics.php', 'SplitTopics'),
        'stats' => array('Stats.php', 'DisplayStats'),
        'sticky' => array('LockTopic.php', 'Sticky'),
        'theme' => array('Themes.php', 'ThemesMain'),
        'trackip' => array('Profile-View.php', 'trackIP'),
        'about:mozilla' => array('Karma.php', 'BookOfUnknown'),
        'about:unknown' => array('Karma.php', 'BookOfUnknown'),
        'unread' => array('Recent.php', 'UnreadTopics'),
        'unreadreplies' => array('Recent.php', 'UnreadTopics'),
        'verificationcode' => array('Register.php', 'VerificationCode'),
        'viewprofile' => array('Profile.php', 'ModifyProfile'),
        'vote' => array('Poll.php', 'Vote'),
        'viewquery' => array('ViewQuery.php', 'ViewQuery'),
        'viewsmfile' => array('Admin.php', 'DisplayAdminFile'),
        'who' => array('Who.php', 'Who'),
        '.xml' => array('News.php', 'ShowXmlFeed'),
        'xmlhttp' => array('Xml.php', 'XMLhttpMain'),
    );
    
    // Allow modifying $actionArray easily.
    call_integration_hook('integrate_actions', array(&$actionArray));
    // Get the function and file to include - if it's not there, do the board index.
    if (!isset($_REQUEST['action']) || !isset($actionArray[$_REQUEST['action']]))
    {
        if (function_exists('action_'.$request_name))
            return 'action_'.$request_name;
        else
            get_error('Invalid action');
    }
    $local_action = array('login2', 'post', 'post2', 'who', 'profile', 'notify', 'notifyboard', 'markasread', 'unread', 'search2', 'pm');
    // Otherwise, it was set - so let's go to that action.
    if (in_array($_REQUEST['action'], $local_action))
        require_once('include/' . $actionArray[$_REQUEST['action']][0]);
    else
        require_once($sourcedir . '/' . $actionArray[$_REQUEST['action']][0]);
    return $actionArray[$_REQUEST['action']][1];
}

?>