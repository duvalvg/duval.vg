<?php

defined('IN_MOBIQUO') or exit;

function db_fatal_error($loadavg = false)
{
    //for tapatalk
    @get_error('Sorry, SMF was unable to connect to the database.  This may be caused by the server being busy.  Please try again later.');
}

// Log an error, if the option is on.
function log_error($error_message, $error_type = 'general', $file = null, $line = null)
{
	global $txt, $modSettings, $sc, $user_info, $smcFunc, $scripturl, $last_error;

	// Check if error logging is actually on.
	if (empty($modSettings['enableErrorLogging']))
		return $error_message;

	// Basically, htmlspecialchars it minus &. (for entities!)
	$error_message = strtr($error_message, array('<' => '&lt;', '>' => '&gt;', '"' => '&quot;'));
	$error_message = strtr($error_message, array('&lt;br /&gt;' => '<br />', '&lt;b&gt;' => '<strong>', '&lt;/b&gt;' => '</strong>', "\n" => '<br />'));

	// Add a file and line to the error message?
	// Don't use the actual txt entries for file and line but instead use %1$s for file and %2$s for line
	if ($file == null)
		$file = '';
	else
		// Window style slashes don't play well, lets convert them to the unix style.
		$file = str_replace('\\', '/', $file);

	if ($line == null)
		$line = 0;
	else
		$line = (int) $line;

	// Just in case there's no id_member or IP set yet.
	if (empty($user_info['id']))
		$user_info['id'] = 0;
	if (empty($user_info['ip']))
		$user_info['ip'] = '';

	// Find the best query string we can...
	$query_string = empty($_SERVER['QUERY_STRING']) ? (empty($_SERVER['REQUEST_URL']) ? '' : str_replace($scripturl, '', $_SERVER['REQUEST_URL'])) : $_SERVER['QUERY_STRING'];

	// Don't log the session hash in the url twice, it's a waste.
	$query_string = htmlspecialchars((SMF == 'SSI' ? '' : '?') . preg_replace(array('~;sesc=[^&;]+~', '~' . session_name() . '=' . session_id() . '[&;]~'), array(';sesc', ''), $query_string));

	// Just so we know what board error messages are from.
	if (isset($_POST['board']) && !isset($_GET['board']))
		$query_string .= ($query_string == '' ? 'board=' : ';board=') . $_POST['board'];

	// What types of categories do we have?
	$known_error_types = array(
		'general',
		'critical',
		'database',
		'undefined_vars',
		'user',
		'template',
		'debug',
	);

	// Make sure the category that was specified is a valid one
	$error_type = in_array($error_type, $known_error_types) && $error_type !== true ? $error_type : 'general';

	// Don't log the same error countless times, as we can get in a cycle of depression...
	$error_info = array($user_info['id'], time(), $user_info['ip'], $query_string, $error_message, (string) $sc, $error_type, $file, $line);
	if (empty($last_error) || $last_error != $error_info)
	{
		// Insert the error into the database.
		$smcFunc['db_insert']('',
			'{db_prefix}log_errors',
			array('id_member' => 'int', 'log_time' => 'int', 'ip' => 'string-16', 'url' => 'string-65534', 'message' => 'string-65534', 'session' => 'string', 'error_type' => 'string', 'file' => 'string-255', 'line' => 'int'),
			$error_info,
			array('id_error')
		);
		$last_error = $error_info;
	}

	// Return the message to make things simpler.
	return $error_message;
}

// An irrecoverable error.
function fatal_error($error, $log = 'general')
{
	global $txt, $context, $modSettings;

	// We don't have $txt yet, but that's okay...
	if (empty($txt))
		@get_error($error);

	setup_fatal_error_context($log || (!empty($modSettings['enableErrorLogging']) && $modSettings['enableErrorLogging'] == 2) ? log_error($error, $log) : $error);
}

// A fatal error with a message stored in the language file.
function fatal_lang_error($error, $log = 'general', $sprintf = array())
{
	global $txt, $language, $modSettings, $user_info, $context;
	static $fatal_error_called = false;
	
	if ($error == 'loginWaitTime_broken' && is_int($sprintf[0]))
	{
	    sleep($sprintf[0]);
	    return;
	}

	// Try to load a theme if we don't have one.
	if (empty($context['theme_loaded']) && empty($fatal_error_called))
	{
		$fatal_error_called = true;
		loadTheme();
	}

	// If we have no theme stuff we can't have the lanuage file...
	if (empty($context['theme_loaded']))
		die($error);

	$reload_lang_file = true;
	// Log the error in the forum's language, but don't waste the time if we aren't logging
	if ($log || (!empty($modSettings['enableErrorLogging']) && $modSettings['enableErrorLogging'] == 2))
	{
		loadLanguage('Errors', $language);
		$reload_lang_file = $language != $user_info['language'];
		$error_message = empty($sprintf) ? $txt[$error] : vsprintf($txt[$error], $sprintf);
		log_error($error_message, $log);
	}

	// Load the language file, only if it needs to be reloaded
	if ($reload_lang_file)
	{
		loadLanguage('Errors');
		$error_message = empty($sprintf) ? $txt[$error] : vsprintf($txt[$error], $sprintf);
	}

	setup_fatal_error_context($error_message);
}

// Handler for standard error messages.
function error_handler($error_level, $error_string, $file, $line)
{
	global $settings, $modSettings, $db_show_debug;

	// Ignore errors if we're ignoring them or they are strict notices from PHP 5 (which cannot be solved without breaking PHP 4.)
	if (error_reporting() == 0 || (defined('E_STRICT') && $error_level == E_STRICT && (empty($modSettings['enableErrorLogging']) || $modSettings['enableErrorLogging'] != 2)))
		return;

	if (strpos($file, 'eval()') !== false && !empty($settings['current_include_filename']))
	{
		if (function_exists('debug_backtrace'))
		{
			$array = debug_backtrace();
			for ($i = 0; $i < count($array); $i++)
			{
				if ($array[$i]['function'] != 'loadSubTemplate')
					continue;

				// This is a bug in PHP, with eval, it seems!
				if (empty($array[$i]['args']))
					$i++;
				break;
			}

			if (isset($array[$i]) && !empty($array[$i]['args']))
				$file = realpath($settings['current_include_filename']) . ' (' . $array[$i]['args'][0] . ' sub template - eval?)';
			else
				$file = realpath($settings['current_include_filename']) . ' (eval?)';
		}
		else
			$file = realpath($settings['current_include_filename']) . ' (eval?)';
	}

	if (isset($db_show_debug) && $db_show_debug === true)
	{
		// Commonly, undefined indexes will occur inside attributes; try to show them anyway!
		if ($error_level % 255 != E_ERROR)
		{
			$temporary = ob_get_contents();
			if (substr($temporary, -2) == '="')
				echo '"';
		}

		// Debugging!  This should look like a PHP error message.
		echo '<br />
<strong>', $error_level % 255 == E_ERROR ? 'Error' : ($error_level % 255 == E_WARNING ? 'Warning' : 'Notice'), '</strong>: ', $error_string, ' in <strong>', $file, '</strong> on line <strong>', $line, '</strong><br />';
	}

	$error_type = strpos(strtolower($error_string), 'undefined') !== false ? 'undefined_vars' : 'general';

	$message = log_error($error_level . ': ' . $error_string, $error_type, $file, $line);

	// Let's give integrations a chance to ouput a bit differently
	call_integration_hook('integrate_output_error', array($message, $error_type, $error_level, $file, $line));

	// Dying on these errors only causes MORE problems (blank pages!)
	if ($file == 'Unknown')
		return;

	// If this is an E_ERROR or E_USER_ERROR.... die.  Violently so.
	if ($error_level % 255 == E_ERROR)
		@get_error($message);
	else
		return;

	// If this is an E_ERROR, E_USER_ERROR, E_WARNING, or E_USER_WARNING.... die.  Violently so.
	if ($error_level % 255 == E_ERROR || $error_level % 255 == E_WARNING)
		fatal_error(allowedTo('admin_forum') ? $message : $error_string, false);
    
    @get_error();
}

function setup_fatal_error_context($error_message)
{
    @get_error($error_message);
}

?>