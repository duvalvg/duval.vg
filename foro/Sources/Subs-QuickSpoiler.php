<?php
/*******************************************************************************
* Quick Spoiler © 2011-2012, Bugo		        							   *
********************************************************************************
* Subs-QuickSpoiler.php														   *
********************************************************************************
* License http://creativecommons.org/licenses/by-nc-nd/3.0/deed.ru CC BY-NC-ND *
* Support and updates for this software can be found at	http://dragomano.ru    *
*******************************************************************************/

if (!defined('SMF'))
	die('Hacking attempt...');
	
function spoiler_load_theme()
{
	global $txt, $modSettings, $context, $settings;
		
	loadLanguage('QuickSpoiler');
	loadTemplate(false, 'spoiler');

	if (!in_array($context['current_action'], array('helpadmin', 'printpage')) && !WIRELESS)
	{
		$context['insert_after_template'] .= '
	<script type="text/javascript">!window.jQuery && document.write(unescape(\'%3Cscript src="http://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js"%3E%3C/script%3E\'))</script>
	<script type="text/javascript">!window.jQuery && document.write(unescape(\'%3Cscript src="' . $settings['default_theme_url'] . '/scripts/jquery-1.7.1.min.js"%3E%3C/script%3E\'))</script>
	<script type="text/javascript"><!-- // --><![CDATA[
		jQuery(document).ready(function($){
			$(".spoiler-body").hide();
			$(".spoiler-head").click(function(){
				$(this).toggleClass("open").toggleClass("closed").next().toggle();
			});
		});
	// ]]></script>';
	}
}

function spoiler_bbc_codes(&$codes)
{
	global $modSettings, $txt;
	
	// Убираем тег спойлер, если он уже есть
	foreach ($codes as $tag => $dump)
		if ($dump['tag'] == 'spoiler') unset($codes[$tag]);
	
	// Spoiler Tags
	if (allowedTo('view_spoiler'))
	{
		$codes[] = array(
			'tag' => 'spoiler',
			'before' => '<div class="spoiler-wrap"><div class="spoiler-head open">' . (!empty($modSettings['qs_title']) ? $modSettings['qs_title'] : $txt['quick_spoiler']) . '</div><div class="spoiler-body">',
			'after' => '</div></div>',
			'block_level' => true,
		);
		$codes[] = array(
			'tag' => 'spoiler',
			'type' => 'unparsed_equals',
			'before' => '<div class="spoiler-wrap"><div class="spoiler-head open">$1</div><div class="spoiler-body">',
			'after' => '</div></div>',
			'block_level' => true,
		);
	}
	else
	{
		$codes[] = array(
			'tag' => 'spoiler',
			'type' => 'unparsed_content',
			'content' => '<div class="spoiler-wrap centertext">' . $txt['qs_no_spoiler_sorry'] . '</div>',
			'block_level' => true,
		);
		$codes[] = array(
			'tag' => 'spoiler',
			'type' => 'unparsed_equals_content',
			'content' => '<div class="spoiler-wrap centertext">' . $txt['qs_no_spoiler_sorry'] . '</div>',
			'block_level' => true,
		);
	}
}

function spoiler_bbc_buttons(&$buttons)
{
	global $txt;
	
	$buttons[count($buttons) - 1][] = array(
		'image' => 'spoiler',
		'code' => 'spoiler',
		'before' => '[spoiler]',
		'after' => '[/spoiler]',
		'description' => $txt['quick_spoiler'],
	);
}

// Это чтобы не копировать картинки кнопок в папку каждой темы
function spoiler_buffer(&$buffer)
{
	global $context, $settings;
	
	if (isset($_REQUEST['xml']) || $context['current_action'] == 'printpage') return $buffer;
	
	$spoiler = 'sImage: ' . JavaScriptEscape($settings['images_url'] . '/bbc/spoiler.gif');
	$default = 'sImage: ' . JavaScriptEscape($settings['default_images_url'] . '/bbc/spoiler.gif');
	$replacements[$spoiler] = $default;
	
	return str_replace(array_keys($replacements), array_values($replacements), $buffer);
}

function spoiler_permissions(&$permissionGroups, &$permissionList)
{
	$permissionList['membergroup']['view_spoiler'] = array(false, 'general', 'view_basic_info');
}

function spoiler_settings(&$config_vars)
{
	global $modSettings, $txt;
	
	if (isset($config_vars[0])) $config_vars[] = array('title', 'qs_settings');
	if (empty($modSettings['qs_title'])) updateSettings(array('qs_title' => $txt['quick_spoiler']));
	
	$config_vars[] = array('text', 'qs_title');
	$config_vars[] = array('permissions', 'view_spoiler');
}

?>