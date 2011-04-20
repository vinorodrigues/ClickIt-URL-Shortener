/**
 * @author     GitHub
 * @copyright  (c) 2011 GitHub Inc. All rights reserved.
 *   Copied verbatum from github.com
 *   Translated from JQuery to YUI3 
 */

YUI().use('node', function(Y) {
 
	function mouseBIn(e) {
		var button = e.target;
		button.addClass('mousedown');		
	}
	
	function mouseBOut(e) {
		var button = e.target;
		button.removeClass('mousedown');
		if (Y.UA.gecko) { button.blur(); }  // firefox bug workaround
	}
 
	var buttons = Y.all('.minibutton');
	buttons.on({
		mousedown: mouseBIn,
		blur: mouseBOut, 
		mouseup: mouseBOut,
		mouseleave: mouseBOut,
		mouseout: mouseBOut
		});

});
