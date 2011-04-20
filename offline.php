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
ob_start();
?>

<p class="clearfix">
<img src="images/backsoon.png" style="float: left; margin-right: 2em;" alt="We'll be back soon" /><br />
We are in the process of doing some maintenance on the website, please be patient while this is done.<br />
<br />
If you wish to use an alternative public shortening service please consider
the <cite title="tinyurl.com launched in 2002">grandfather</cite> of shorteners <a href="http://tinyurl.com">tinyurl.com</a>,
the <cite title="bit.ly launched 2009, by November that year serving 2.1 billion short url's">father</cite> <a href="http://bit.ly">bit.ly</a> or more the more modern 
<a href="http://goo.gl">goo.gl</a>,
<a href="http://tr.im">tr.im</a>, 
<a href="http://ow.ly">ow.ly</a>,
<a href="http://is.gd">is.gd</a>, 
<a href="http://su.pr">su.pr</a> and
<a href="http://www.google.com/search?q=%22URL+shortener%22">others</a>.
</p>

<?php
$page['content'] = ob_get_clean();
$page['title'] = 'Website currently <span class="red" style="text-transform: uppercase;">offline</span>';
include('includes/' . TEMPLATE . '.php');
