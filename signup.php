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
initialize_settings();
initialize_lang();

ob_start();
?>

Your c1k.it account gives you access to create and edit short URL's.
If you already have a c1k.it account, you can <a href="login.php">log in here</a>.

<?php
$page['content'] = ob_get_clean();

poke_warning("Sorry, new signup's are currenlty disabled");

$page['title'] = 'Create an Account';
include('includes/' . TEMPLATE . '.php');
