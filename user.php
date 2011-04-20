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
initialize_db(true);
initialize_lang();
initialize_security($settings['mustlogin']);

$page['content'] = "<pre> UserId = $userid, UserName = $username, UserLevel = $userlevel";

poke_warning("Sorry, user administration currenlty not available");

$page['title'] = 'Your Account';
include('includes/' . TEMPLATE . '.php');
