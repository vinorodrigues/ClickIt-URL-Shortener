/**
 * @package    c1k.it
 * @author     Vino Rodrigues
 * @copyright  Tecsmith.com.au
 *   See LICENSE.TXT for copyright notice and details.
 * @license    Creative Commons Attribution-ShareAlike 3.0 Unported License
 * @author     Vino Rodrigues 
 *   clickit [dot] source [at] mail [dot] vinorodrigues [dot] com
 */

YUI().use('node', function(Y) {
 
	function reTitle(e) {
		var input = e.target;
		// alert('Boo!');
	}
 
	Y.all('#f_title').on({change: reTitle});
	Y.all('#f_longURL').on({change: reTitle});
});
