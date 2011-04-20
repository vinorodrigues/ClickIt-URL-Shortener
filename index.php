<?php
/**
 * @package    c1k.it
 * @author     Vino Rodrigues
 * @copyright  Tecsmith.com.au
 *   See LICENSE.TXT for copyright notice and details.
 * @license    Creative Commons Attribution-ShareAlike 3.0 Unported License
 * @author     Vino Rodrigues 
 *   clickit [dot] source [at] mail [dot] vinorodrigues [dot] com
 */

require_once('includes/library.php');
require_once('includes/lang.php');
		
if (!file_exists('config.php')) :
	if (file_exists('install.php')) :	
		include('install.php');
		exit;
	else :
		$e = 501;
		$m = 'Neither configuration nor installation files exist';
		include('error.php'); 
		die();
	endif;
endif;
	
initialize_settings();
initialize_db(true);
initialize_lang();
$page['title'] = T('WELCOME');
$page['content'] = T('PREFACE', null, '<p class="intro">', '</p>' . PHP_EOL);
initialize_security($settings['mustlogin']);

ob_start(); ?>

<form action="edit.php" method="get" name="f">

<table align="center" class="dithered"><tr class="odd first"><td>
<?php if ($userlevel >= USER_LEVEL_CR) : ?>
<label for="longURL"><?php P('ENTERLONG'); ?></label>:<br />
<input type="<?php __('text', 'url') ?>" name="longURL" id="long_URL" size="30"<?php __('', 'autofocus="autofocus" required="required" pattern="' . REGEX_ECMA_URL . '"')?> style="width:12em;" /><input type="submit" class="minibutton" value="<?php P('CREATE'); ?>" /><br />
<div id="long_feedback_display" class="tips"></div>
<?php if ($userlevel >= USER_LEVEL_CU) : ?>
</td></tr><tr class="even last"><td>
<label for="shortURL"><?php P('ENTERSHORT'); ?></label>:<br /> 
<label for="shortURL"><tt class="nowrap"><?php print $page['full_path']; ?></tt><input type="text" name="shortURL" id="short_URL" value="" size="12" maxlength="30" style="width:6em;"></label><br /> 
<div id="short_feedback_display" class="tips"><?php P('MAY_CONTAIN_LETTERS'); ?></div>
<?php endif;
else :
	P('ACCESS_DENIED');
endif; ?> 
</td></tr></table>

</form>

<?php /*
TODO : Add Facebook link, for Version 0.3
<script src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script>
<fb:like href="http://c1k.it" show_faces="false" width="310" action="recommend" font=""></fb:like>
*/ ?>

<?php 
$page['content'] .= ob_get_clean();

if (!isset($page['head_suffix'])) $page['head_suffix'] = '';
$page['head_suffix'] .= ajaxjs('yui/yui-min.js');

if ($userlevel >= USER_LEVEL_CR) :
	$page['head_suffix'] .= ajaxjs('oop/oop-min.js');
	$page['head_suffix'] .= ajaxjs('event-custom/event-custom-base-min.js');
	$page['head_suffix'] .= ajaxjs('querystring/querystring-stringify-simple-min.js');
	$page['head_suffix'] .= ajaxjs('io/io-base-min.js');
	
	$page['head_suffix'] .= loadjs('includes/loadjs.php?f=ajaxtest.js&xxx=long&fff=test_long'); 
	
	if ($userlevel >= USER_LEVEL_CU)
		$page['head_suffix'] .= loadjs('includes/loadjs.php?f=ajaxtest.js&xxx=short&fff=test_short');
endif;

$page['head_suffix'] .= loadjs('includes/loadjs.php?f=minibutton.js');

include('includes/' . TEMPLATE . '.php');
?>
