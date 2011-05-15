[![c1k.it](http://c1k.it/images/logo2.png)](http://c1k.it)


# Introduction

c1k.it <http://c1k.it> is URL shortening service.  This is it's source code.

Similar to other public shortening services - like the grandfather of shorteners
<http://tinyurl.com>, the father <http://bit.ly> or more the more modern
<http://goo.gl>, <http://tr.im>, <http://ow.ly>, <http://is.gd>, <http://su.pr>
and others - It is run by tecsmith.com.au for its online marketing clients.

Open source means you can use it to create your own shortener, like Harvard
Business Review does with <http://s.hbr.org>, or The Economist does with
<http://econ.st>.  Unlike some of the others it allows you to change the Long
URL, so over time your short can point to different content, like a price list,
without having to change your short bit and republishing.

We hope you enjoy learning, or even using, this source.


# Releases

* 0.1 Beta
	* Basic site functionality, installer and template engine
	* Database access external, you'll need to use someting like phpMyAdmin
	* _Released 14 Mar 2011_
        
* 0.2 Beta
	* Basic user functionality like sign-on and URL creation
	* Hash-tag engine, internationalization
	* Home page intergration with Facebook and Google Analytics
	* _Released 21 Apr 2011_
           
* 0.3 Beta
	* User management & settings management
	* _Released 4 May 2011_
           
* 0.4 Beta
	* ShortURL integration with Piwik (we could not get serverside GA to work)
	* Home page integration with Piwik and Twitter
	* Events table for extra debug info on some exceptions (for debugging)
	* Automated update process
	* _Released 9 May 2011_

* 0.5 Beta
	* Mobile links (QR-Code) - prepend "@" to your short URL
	* Copy to clipboad (using clippy.swf)
	* _Released 14 May 2011_


## Upcomming milestones

* 0.6 Beta - Basic Reporting

* 0.9 RC1 - Basic cleanups for release candidate

* 1.0 - Feature freez

* 1.1 - Jason and XML API's

* (1.2) - Drupal plugin

* (1.3) - Wordpress plugin


# Get Involved

Co-maintainers are most welcome (actually needed), please email author via GitHub.

- [Vino Rodrigues](http://www.tecsmith.com.au) ;)
