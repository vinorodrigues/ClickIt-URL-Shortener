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
require_once('includes/lang.' . $phpEx);

// *** check for config file else perform install
if ((!file_exists('config.' . $phpEx)) && (!file_exists('~config.' . $phpEx))) :
	if (file_exists('install.' . $phpEx)) :
		include('install.' . $phpEx);
		exit;
	else :
		$e = 501;  // Not implemented
		$m = 'Neither configuration nor installation files exist';
		include('error.' . $phpEx);
		die(501);
	endif;
endif;

initialize_settings();
initialize_db(TRUE);

// *** check_update
if (floatval($settings['version']) < floatval(CLICKIT_VER)) :
	if (file_exists('update.' . $phpEx)) :
		include_once('update.' . $phpEx);
		perform_updates();
	else :
		// where's the update.php file?
		poke_warning('Update required, please contact webmaster');
	endif;
endif;

// *** check_cron
if ((isset($settings['cron_auto'])) && ($settings['cron_auto'])) :
	// TODO : Check CRON run times & run if required
endif;

initialize_lang();

$page['title'] = T('WELCOME');
$page['content'] = T('PREFACE', NULL, '<p class="intro">', '</p>' . PHP_EOL);

include('includes/social.' . $phpEx);

if (!isset($settings['mustlogin'])) $settings['mustlogin'] = TRUE;
initialize_security($settings['mustlogin']);
define('USER_LEVEL_MAGIC', ($settings['mustlogin'] ? USER_LEVEL_CR : -1));

ob_start(); ?>

<form action="edit.<?php print  $phpEx; ?>" method="get" name="f">

<table align="center" class="dithered rounded"><tr class="row_1 odd first<?php print ($userlevel < USER_LEVEL_CU) ? ' last single' : ''; ?>"><td>
<?php if ($userlevel >= USER_LEVEL_MAGIC) : ?>
<label for="longURL"><?php P('ENTERLONG'); ?></label>:<br />
<input type="<?php __('text', 'url') ?>" name="longURL" id="long_URL" size="30"<?php __('', 'autofocus="autofocus" required="required" pattern="' . REGEX_ECMA_URL . '"')?> style="width:12em;" /><input type="submit" class="minibutton" value="<?php P('CREATE'); ?>" /><br />
<div id="long_feedback_display" class="tips"></div>
<?php if ($userlevel >= USER_LEVEL_CU) : ?>
</td></tr><tr class="row_2 even last"><td>
<label for="shortURL"><?php P('ENTERSHORT'); ?></label>:<br />
<label for="shortURL"><code class="nowrap"><?php print $page['full_path']; ?></code><input type="text" name="shortURL" id="short_URL" value="" size="12" maxlength="30" style="width:6em;"></label><br />
<div id="short_feedback_display" class="tips"><?php P('MAY_CONTAIN_LETTERS'); ?></div>
<?php endif;
else :
	P('ACCESS_DENIED');  echo $userlevel;
endif; ?>
</td></tr></table>

</form>

<?php $page['content'] .= ob_get_clean();

if (!isset($page['head_suffix'])) $page['head_suffix'] = '';

$page['head_suffix'] .= ajaxjs('yui/yui-min.js');

if ($userlevel >= USER_LEVEL_CR) :
	$page['head_suffix'] .= ajaxjs('oop/oop-min.js');
	$page['head_suffix'] .= ajaxjs('event-custom/event-custom-base-min.js');
	$page['head_suffix'] .= ajaxjs('querystring/querystring-stringify-simple-min.js');
	$page['head_suffix'] .= ajaxjs('io/io-base-min.js');

	$phpExFill = (strcmp($phpEx, 'php') !== 0) ? '?php=' . $phpEx : '';
	$page['head_suffix'] .= loadjs('includes/loadjs.' . $phpEx . '?f=ajaxtest.js&xxx=long&fff=test_long' . $phpExFill);

	if ($userlevel >= USER_LEVEL_CU)
		$page['head_suffix'] .= loadjs('includes/loadjs.' . $phpEx . '?f=ajaxtest.js&xxx=short&fff=test_short' . $phpExFill);
endif;

$page['head_suffix'] .= loadjs('includes/loadjs.' . $phpEx . '?f=minibutton.js');

include('includes/' . TEMPLATE . '.' . $phpEx);
?>
