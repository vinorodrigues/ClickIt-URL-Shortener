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

if (!defined('IN_CLICKIT')) die('Restricted');

$use_fb = isset($settings['facebook_id']) && (!empty($settings['facebook_id']));
$use_pk = (isset($settings['piwik_site']) && (!empty($settings['piwik_site']))) &&
		(isset($settings['piwik_id']) && (!empty($settings['piwik_id'])));
$use_ga = isset($settings['ga_profile']) && (!empty($settings['ga_profile']));
$use_tw = (isset($settings['twitter_share']) && (!empty($settings['twitter_share']))) ||
		(isset($settings['twitter_follow_list']) && (!empty($settings['twitter_follow_list'])));
if ((!isset($page['scripts'])) && ($use_fb || $use_tw || $use_pk || $use_ga))
	$page['scripts'] = '';

// -------------------- common code --------------------

if ($use_fb) $page['scripts'] .= '<div id="fb-root"></div>' . PHP_EOL;

// -------------------- Piwik integration --------------------

if ($use_pk) :
	$ns = rtrim($settings['piwik_site'], '/') . '/';
	$ss = isset($settings['piwik_site_secure']) ? isset($settings['piwik_site_secure']) : '';
	$id = rtrim($settings['piwik_id'], '/');
	if (empty($ss)) :
		$ss = str_replace('http:', 'https:', $ns);
	else :
		$ss = $ss . '/';
	endif;

	$s = "var pkBaseURL = (('https:' == document.location.protocol) ? '$ss' : '$ns');
document.write(unescape(\"%3Cscript src='\" + pkBaseURL + \"piwik.js' type='text/javascript'%3E%3C/script%3E\"));";
	$page['scripts'] .= loadscript($s);

	$s = "try {
var piwikTracker = Piwik.getTracker(pkBaseURL + 'piwik.php', $id);
piwikTracker.trackPageView();
piwikTracker.enableLinkTracking();
} catch( err ) {}";
	$page['scripts'] .= loadscript($s);

	$page['scripts'] .= "\t" . '<noscript><img src="' .
		( (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on')) ? $ss : $ns ) .
		'piwik.php?idsite=' . $id . '" style="border:0" alt="" /></noscript>' . PHP_EOL;
endif;

// -------------------- Google Analytics integration --------------------

if ($use_ga) :
	$s = "var _gaq = _gaq || [];
_gaq.push(['_setAccount', '" . $settings['ga_profile'] . "']);
_gaq.push(['_trackPageview']);
(function() {
	var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
	ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
	var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
})();";
	$page['scripts'] .= loadscript($s);
endif;

// -------------------- Twitter integration --------------------

if ($use_tw) :
	if (!isset($page['footer'])) $page['footer'] = '';
	$page['footer'] .= PHP_EOL . '<div class="twitter clearfix">';
	
	if (isset($settings['twitter_share']) && (!empty($settings['twitter_share']))) {
		$page['footer'] .= '<a href="https://twitter.com/share" class="twitter-share-button" data-dnt="true">Tweet</a> ';
	}
	
	if (isset($settings['twitter_follow_list']) && (!empty($settings['twitter_follow_list']))) {
		$follows = explode(',', $settings['twitter_follow_list']);
		foreach ($follows as $name) :
			$page['footer'] .= '<a href="https://twitter.com/' . $name . '" class="twitter-follow-button" data-show-count="false" data-dnt="true">Follow @' . $name . '</a> ';
		endforeach;
	}

	$page['scripts'] .= "\t<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>" . PHP_EOL;
	
	$page['footer'] .= '</div>' . PHP_EOL . PHP_EOL;
endif;

// -------------------- Facebook integration --------------------

if ($use_fb) :
	$s = "window.fbAsyncInit = function() {
	FB.init({appId: '" . $settings['facebook_id'] . "', status: true, cookie: true, xfbml: true});
";
	if ($use_ga) :
	// _trackEvent(category, action, opt_label, opt_value)
	$s .= "	// push events to Google Analytics
	FB.Event.subscribe('edge.create', function(href, widget) {
		_gaq.push(['_trackEvent', 'Likes', 'Like', '" . $page['full_path'] . "#like']);
	});
	FB.Event.subscribe('edge.remove', function(href, widget) {
		_gaq.push(['_trackEvent', 'Likes', 'Unlike', '" . $page['full_path'] . "#unlike']);
	});";

	endif;

	$s .= "};
(function() {
	var e = document.createElement('script');
	e.type = 'text/javascript';
	e.src = document.location.protocol + '//connect.facebook.net/en_US/all.js';
	e.async = true;
	document.getElementById('fb-root').appendChild(e);
}());";

	$page['scripts'] .= loadscript($s);

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

	if (!isset($page['footer'])) $page['footer'] = '';
	$page['footer'] .= PHP_EOL . '<div class="facebook clearfix">';
	$__gub = $use_ga ? '?utm_source=facebook&utm_medium=social&utm_campaign=likes' : '';
	$page['footer'] .= '<fb:like' .
		' href="' . $page['full_path'] . $__gub . '"' .
		' layout="standard"' .
		' show-faces="true"' .
		' width="320"' .  // size of iPhone
		' action="like"' .
		' colorscheme="light"' .
		' send="true"' .
		// ' font="arial"' .
		'></fb:like>';
	$page['footer'] .= '</div>' . PHP_EOL . PHP_EOL;

	// See: http://www.facebook.com/insights/
	$page['head_suffix'] = "\t<meta property=\"fb:app_id\" content=\"" . $settings['facebook_id'] . "\" />\n";
endif;

// -------------------- Fork Me on GitHub --------------------

if (isset($settings['github_fork_me']) && $settings['github_fork_me']) :
	if (!isset($page['footer'])) $page['footer'] = '';
	$page['footer'] .= '<a href="http://c1k.it/code"><img style="position: absolute; top: 0; right: 0; border: 0;" src="https://s3.amazonaws.com/github/ribbons/forkme_right_red_aa0000.png" alt="Fork me on GitHub"></a>';
endif;

// -------------------- (end) --------------------

?>
