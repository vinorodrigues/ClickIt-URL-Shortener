/**
 * @package    c1k.it
 * @author     Vino Rodrigues
 * @copyright  Tecsmith.com.au
 *   See LICENSE.TXT for copyright notice and details.
 * @license    Creative Commons Attribution-ShareAlike 3.0 Unported License
 * @author     Vino Rodrigues 
 *   clickit [dot] source [at] mail [dot] vinorodrigues [dot] com
 */

YUI().use("node", "io-base", function(Y) {
 
	// Get a Node reference to the div we'll use for displaying results:
	var div = Y.one('#XXX_feedback_display');
	var lookupValue = '';
 
	// Define a function to handle a successful response.
	// The success handler will find the response object in its second argument:
	function successHandler(id, o) {
		var root = o.responseXML.documentElement;
		var oCode = root.getElementsByTagName('code')[0].firstChild.nodeValue;
		var oHtml = root.getElementsByTagName('html')[0].firstChild.nodeValue;
		div.set("innerHTML", oHtml);
		div.set("className", "tips XXX_result XXX_" + oCode);
	}
 
	// Provide a function that can help debug failed requests:
	function failureHandler(id, o) {
		// Set 408 Request Timeout - 
		div.set("innerHTML", o.status + ": " + o.statusText);
		div.set("className", "tips XXX_result XXX_408");
	}
 
	// This function will fire and compose/dispatch the IO request:
	function getModule() {
		// Get the input value:
		var theURL = Y.one('#XXX_URL').get("value");
		if (theURL == lookupValue) { return; }
		lookupValue = theURL;
		// Create a querystring from the input value:
		var queryString = encodeURI('?f=FFF&q=' + theURL);
		//The location of our server-side proxy:
		var entryPoint = 'ajax.php';
		//Compile the full URI for the request:
		var sUrl = entryPoint + queryString;
		//Make the reqeust:
		var request = Y.io(sUrl, {
			method: "GET",
				on: {
						success: successHandler,
						failure: failureHandler
					}
			});
	}
 
	// Use the Event Utility to wire the input tag to the getModule function:
	Y.on("focus", getModule, "#XXX_URL");
	Y.on("blur", getModule, "#XXX_URL");
	Y.on("click", getModule, "#XXX_URL");
	Y.on("change", getModule, "#XXX_URL");
	Y.on("keyup", getModule, "#XXX_URL");
});
