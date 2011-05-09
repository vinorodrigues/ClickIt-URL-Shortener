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
initialize_settings();
initialize_lang();

$page['head_title'] = "c1k.it LICENSE";
$page['title'] = "License and other legalities";

$page['head_suffix'] = "\t<style>" .
	" td, th { border: none; vertical-align: top; }" .
	" p { margin-top: 0; margin-bottom: 1em; }" .
	" img { border-width: 0; }" .
	" p.indent { padding-left: 2em; font-size: 85% }" .
	" li.indent { list-style: square outside; font-size: 85% }" .
	" td.l { text-align: center; }" .
	" td.r { border-left: 1px dashed #CCC; }" .
	" th { border-bottom: 1px dashed #CCC; }" .
	" td.s { background: rgba(238, 238, 238, 0.2);  vertical-align:middle; " .
		"border-top: 1px solid #DDD; border-bottom: 1px solid #DDD; } " .
	" span.s { color: navy; font-weight: bold; }" .
	" small.s { font-size: 77%; font-style: italic; }" .
	"</style>\n";

ob_start();
?>

<table>
<tbody>
<tr>
<td class="l">
<a rel="source" href="http://c1k.it/code"><img alt="c1k.it code" src="images/c1kit-80x15.png" /></a><br />
<br />
<a rel="source" href="http://c1k.it/code"><img alt="c1k.it code" src="images/c1kit-88x31.png" /></a><br />
<br />
<a rel="author" href="http://tecsmith.com.au"><img alt="Tecsmith" src="images/tecsmith-88x31.png" /></a><br />
<br />
<a rel="license" href="http://creativecommons.org/licenses/by-sa/3.0/"><img alt="Creative Commons License" src="http://i.creativecommons.org/l/by-sa/3.0/88x31.png" /></a><br />
<br />
<a rel="source" href="http://c1k.it/code"><img alt="GitHub" src="images/github-88x31.png" /></a><br />
</td>
<td class="r">
<p>c1k.it is an open source project - it's source is free (as in freedom) and free (as in no-cost). Its source is hosted on <a rel="source" href="http://c1k.it/code">GitHub</a>.</p>
<p>It is licensed under a <a rel="license" href="http://creativecommons.org/licenses/by-sa/3.0/">Creative Commons Attribution-ShareAlike 3.0 Unported License</a>.</p>
<p class="indent">Basically, that means that you can use its source for your own work, including commercial uses.  But...</p>
<ul>
	<li class="indent">You must attribute (or openly mention) the original Author.</li>
	<li class="indent">You must "share alike" � meaning you'll need to make the parts that you use or modify also freely available.</li>
</ul>
<p class="indent">The best way to do that is to have a link back to the source repository on GitHub using one of the two ("c1k.it") logos on the top left of this page.</p>
<p>But writing ones code from scratch is ludicrous, so we�ve used some other work that needs attribution:</p>
</td>
</tr>
</tbody>
<tfoot>
<tr><th colspan="2">Sources of Intellectual Property Included</th><tr>
<tr>
<td class="l">
<a rel="license" href="http://opensource.org/licenses/gpl-license.php"><img src="images/gplv2-88x31.png" alt="GNU GPLv2" /></a>
</td>
<td class="r">
The DBAL (Database Abstraction Layer) is sourced from <a href="http://www.phpbb.com">phpBB</a>.<br />
&copy; Copyright 2000, 2002, 2005, 2007 phpBB Group<br />
* <a href="http://www.phpbb.com">http://www.phpbb.com</a> - GPLv2 License
</td>
</tr>
<tr>
<td class="l">
<a rel="license" href="http://developer.yahoo.com/yui/license.html"><img src="images/bsd-88x31.png" alt="BSD License" /></a>
</td>
<td class="r">
Some UI is sourced from the <a href="http://developer.yahoo.com/yui/" title="Yahoo! UI Library">Yahoo! UI Library</a> (YUI3).<br />
Copyright &copy; 2011, Yahoo! Inc.<br />
* <a href="http://developer.yahoo.com/yui/">http://developer.yahoo.com/yui</a> - BSD License
</td>
</tr>
<tr>
<td class="l s">
<img alt="" src="images/google-88x31.png" /><br /><br />
<img alt="" src="images/facebook-88x31.png" /><br /><br />
<img alt="" src="images/twitter-88x31.png" /><br /><br />
<img alt="" src="images/apple-88x31.png" />
</td>
<td class="r s">
<span class="s">Special thanks to:</span> <small class="s">(See source code for links to author sites)</small>
<ul>
	<li class="indent">Browser detection <i>by</i> Chris Schuld</li>
	<li class="indent">Icons <i>by</i> FAMFAMFAM</li>
	<li class="indent">Button images <i>by</i> SomeRandomDude</li>
	<li class="indent">GitHub Minibutton <i>by</i> David Walsh</li>
	<li class="indent">Captcha &amp; reCaptchaLib <i>by</i> ReCAPTCHA (Google)</li>
	<li class="indent">JSMin <i>by</i> Douglas Crockford &amp; Ryan Grove</li>
	<li class="indent">UUID generator <i>by</i> Andrew Moore</li>
	<li class="indent">Analitics <i>by</i> Google</li>
	<li class="indent">Chart API <i>by</i> Google</li>
	<li class="indent">Facebook Lib <i>by</i> Facebook</li>
	<li class="indent">Twitter API <i>by</i> Twitter</li>
	<li class="indent">iPhone integration <i>by</i> Apple Developer</li>
</ul>
</td></tr>
<tr>
<td class="l">
<a href="http://www.php.net"><img src="images/php-88x31.png" alt="PHP" /></a>
</td>
<td class="r">
And finaly, but not least, thanks to the PHP Group and community, specifically
Andrew Moore,
Enrico Pallazzo,
and other PHP Manual <a href="http://www.php.net/manual/en/preface.php#contributors">Contributors</a>.
</td>
</tr>
</tfoot>
</table>

<?php
$page['content'] = ob_get_clean();
include('includes/' . TEMPLATE . '.' . $phpEx);
