<?php
/*
 * @package AJAX_Chat
 * @author Sebastian Tschan
 * @copyright (c) Sebastian Tschan
 * @license GNU Affero General Public License
 * @link https://blueimp.net/ajax/
 */

if (!$_GET['shoutbox']){
	$host  = $_SERVER['HTTP_HOST'];
	$uri   = rtrim(dirname(dirname($_SERVER['PHP_SELF'])), '/\\');
	$extra = 'chat/index.php';
	header("Location: http://$host$uri/$extra");
	}

// Show all errors:
error_reporting(E_ALL);

// Path to the chat directory:
define('AJAX_CHAT_PATH', dirname($_SERVER['SCRIPT_FILENAME']).'/');

// Include custom libraries and initialization code:
require(AJAX_CHAT_PATH.'lib/custom.php');

// Include Class libraries:
require(AJAX_CHAT_PATH.'lib/classes.php');

// Initialize the chat:
$ajaxChat = new CustomAJAXChat();
?>