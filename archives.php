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

$enabled_state = false;
include('list.php');

/*
 * This page is the same code base as list.php, but instead of duplication we just include it.
 * This can be achieved with just list.php, but one would need a parameter to trigger it, like
 * index.php?archives - this way is neater.
 */
