YUI().use("node","io-base",function(e){var f=e.one("#XXX_feedback_display");var d="";function b(k,j){var g=j.responseXML.documentElement;var i=g.getElementsByTagName("code")[0].firstChild.nodeValue;var h=g.getElementsByTagName("html")[0].firstChild.nodeValue;f.set("innerHTML",h);f.set("className","tips XXX_result XXX_"+i);}function c(h,g){f.set("innerHTML",g.status+": "+g.statusText);f.set("className","tips XXX_result XXX_408");}function a(){var j=e.one("#XXX_URL").get("value");if(j==d){return;}d=j;var k=encodeURI("?f=FFF&q="+j);var i="ajax.php";var h=i+k;var g=e.io(h,{method:"GET",on:{success:b,failure:c}});}e.on("focus",a,"#XXX_URL");e.on("blur",a,"#XXX_URL");e.on("click",a,"#XXX_URL");e.on("change",a,"#XXX_URL");e.on("keyup",a,"#XXX_URL");});