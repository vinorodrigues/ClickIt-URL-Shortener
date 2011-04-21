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

$use_fb = isset($settings['facebook_id']) && (!empty($settings['facebook_id']));
$use_analytics = isset($settings['google_analytics']) && (!empty($settings['google_analytics']));
if ($use_fb || $use_analytics) $page['scripts'] = '';

if ($use_analytics) :
	ob_start(); ?>
<script type="text/javascript">//<![CDATA[
	var _gaq = _gaq || [];
	_gaq.push(['_setAccount', '<?php print $settings['google_analytics']; ?>']);
	_gaq.push(['_trackPageview']);

	(function() {
		var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	})();
//]]></script>
	<?php $page['scripts'] .= ob_get_clean();
	endif;

if ($use_fb) :
	ob_start(); ?>
<div id="fb-root"></div>
<script type="text/javascript">//<![CDATA[
	window.fbAsyncInit = function() {
		FB.init({appId: '<?php print $settings['facebook_id']; ?>', status: true, cookie: true, xfbml: true});
<?php if ($use_analytics) : /* _trackEvent(category, action, opt_label, opt_value) */ ?>
		FB.Event.subscribe('edge.create', function(href, widget) {
			_gaq.push(['_trackEvent', 'Likes', 'Like', '<?php print $page['full_path']; ?>#like']);
		});
		FB.Event.subscribe('edge.remove', function(href, widget) {
			_gaq.push(['_trackEvent', 'Likes', 'Unlike', '<?php print $page['full_path']; ?>#unlike']);
		});
<?php endif; ?>
	};
	(function() {
		var e = document.createElement('script');
		e.type = 'text/javascript';
		e.src = document.location.protocol + '//connect.facebook.net/en_US/all.js';
		e.async = true;
		document.getElementById('fb-root').appendChild(e);
	}());
//]]></script>
<?php $page['scripts'] .= ob_get_clean();

	/* <fb:like
 	*   href="URL"
 	*   layout="standard|button_count|box_count"
 	*   show-faces="true|false"
 	*   width="450"
 	*   action="like|recommend"
 	*   colorscheme="light|dark"
 	*   font="arial|lucida grande|segoe ui|tahoma|trebuchet ms|verdana"
 	*   ></fb:like>
 	*/

	$page['footer'] = '<div class="facebook clearfix">';
	$_gub = $use_analytics ? '?utm_source=facebook&utm_medium=social&utm_campaign=likes' : '';
	$page['footer'] .= '<fb:like' . 
	' href="' . $page['full_path'] . $_gub . '"' .
	' layout="standard"' .
	' show-faces="true"' .
	' width="320"' .  // size of iPhone
	' action="recommend"' .
	' colorscheme="light"' .
	// ' font="arial"' .
	'></fb:like>';
	$page['footer'] .= '</div>';
	
	// See: http://www.facebook.com/insights/
	$page['head_suffix'] = "\t<meta property=\"fb:app_id\" content=\"" . $settings['facebook_id'] . "\" />\n";
endif;

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

<?php $page['content'] .= ob_get_clean();

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
