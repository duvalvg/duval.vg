<?php
/*
 * @package AJAX_Chat
 * @author Sebastian Tschan
 * @copyright (c) Sebastian Tschan
 * @license GNU Affero General Public License
 * @link https://blueimp.net/ajax/
 */

class CustomAJAXChatShoutBox extends CustomAJAXChat {

	function initialize() {
		// Initialize configuration settings:
		$this->initConfig();
	}

	function getShoutBoxContent($mini) {
		if($mini)
			$template = new AJAXChatTemplate($this, AJAX_CHAT_PATH.'lib/template/miniShoutbox.html');
		else
			$template = new AJAXChatTemplate($this, AJAX_CHAT_PATH.'lib/template/shoutbox.html');
		
		
		// Return parsed template content:
		return $template->getParsedContent();
	}	

}
?>