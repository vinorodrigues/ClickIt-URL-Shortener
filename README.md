# ClickIt-URL-Shortener

## Introduction

c1k.it <http://c1k.it> was started back in 2011 as a URL shortening service.
This is it's source code.

### What is a URL shortening service?

A URL shortening service is a tool that converts a long URL into a shorter, more manageable version. This shortened URL redirects users to the original, longer URL. URL shortening is often used to make links easier to share, especially on platforms with character limits, like Twitter. Additionally, shortened URLs can be useful for tracking clicks and gathering analytics.

Key features of URL shortening services include:

1. **Shortened URLs** - Converts long URLs into shorter versions.
2. **Redirection** - When the shortened URL is clicked, users are redirected to the original URL.
3. **Customizable Links** - Some services allow users to customize the shortened URL to make it more recognizable or relevant.
4. **Analytics** - Many services provide data on the number of clicks, geographic location of clicks, and other usage statistics.
5. **Expiration** - Some services offer the option to set expiration dates for links, making them inactive after a certain period.
6. **Security** - URL shortening services may include features to protect users from malicious links, such as previewing the destination URL before clicking.

Popular URL shortening services include the grandfather of shorteners <http://tinyurl.com>, the father <http://bit.ly> or more the more modern <http://goo.gl> *(now-defunct)*, <http://ow.ly>, <http://is.gd> and others.

### How is this one different?

In 2011 this was useful as a toolchain from my online marketing clients.  But over a decade later it's less so.

Besides - one of the ancillary reason for this project was to enable people to self-host their own URL shortening services - but even that is now better maintained that this was, like <yourls.org/docs> that was started as far back as 2009.

The main reason was however a set and forget redirector that was easy to install and maintian ... and thus do simple, self-serving redirection, like Harvard Business Review does with <http://s.hbr.org>, or The Economist does with <http://econ.st>.  With a self configured URL shortener you can  change the Long URL, so over time your short can point to different content, like an updated price list, without having to change your short bit and republishing.

### Past Versions

Version 1 ... actually, I never moved beyond version 0.5.2 ... was a fully UI-ed interface.  Most of the work I did on this was UI backend stuff - user management, complexity of showing long list, navigating around data, configuration management, etc.  The actual redirection is like, 2 lines of code *(or can be)*.

### What's new in Ver 2

I realised that this could just be a single file solution.  So I re-wrote it from scratch to be so.  So, Version 2, is one file, install it on you service, create a sorts "database" - a JSON file and it works.  Done.

## Releases

See the <CHANGELOG.md> file


## Get Involved

Co-maintainers are most welcome (actually needed).
Please reach me via a PR to the core repo.

## Thank you

I hope you enjoy learning, or even using, this source.

> Made with &#x2665; by Vino Rodrigues
