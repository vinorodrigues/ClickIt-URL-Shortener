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
initialize_settings();
initialize_lang();

ob_start();
?>

<blockquote class="dithered">
If you trust c1k.it with your email address, we promise never to spam you or give your email address to spammers.
</blockquote>
– Webmaster <tt>;)</tt>

<br />&nbsp;<br />
<hr />
<br />&nbsp;<br />

But then again this is the <cite title="George W. Bush, 2004 election's second debate in St. Louis, Missouri">Internets</cite> -- if you <i>trust</i> a nameless, faceless entity, in some unknown country then you have bigger issues than privacy... just a thought.

<br />&nbsp;<br />

<?php
$page['content'] = ob_get_clean();
$page['title'] = 'Privacy';
include('includes/' . TEMPLATE . '.php');
