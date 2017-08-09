=== eSimple Wiki ===
Contributors: Rich Pedley
Donate link: http://quirm.net/download/
Tags: wiki, ewiki, simple, esimple
Requires at least: 3.0
Tested up to: 3.0
Stable tag: 0.6

A simple Wiki plugin, well hopefully.

== Description ==

Basic Wiki functionality for your blog.

= Coding =

* Content menu code based entirely on Matt Beck's wiki-menus plugin.
* Footnotes code based entirely on Andrew Nacin's simple footnotes plugin.
* Wiki style [[ ... ]] interlinking based entirely on harleyquinedotcom's Interlinks plugin.
* Content menu javascript uses  Andy Langton's show/hide/mini-accordion script
* Capabilities/roles uses code by Justin Tadlock
* amended and hacked together by Rich Pedley

== Installation ==

1. Upload esimple-wiki to the `/wp-content/plugins/` directory or automatically install from the dashboard.
2. Activate the plugin through the 'Plugins' menu in the WordPress Dashboard.

== Frequently Asked Questions ==

= How do I list wiki pages? =

Currenty to list all wiki pages I suggest creating a page called wiki, and using the `[wikilist]` shortcode.

= What Wiki functiobality is there? =

Currently not much. You can create a wiki page, a Table of Contents is created automatically from your headings (&lt;h1&gt;-&lt;h6&gt;). You can also use the shortcode `[ref]` to enclose a footnote that will be appended to the bottom of the page. You can use `[[` and `]]` around a post title within your page and it will be automatically turned it into a link for you. You can also use the more advanced wiki style `[[Post Title|This is a link to a post]]`. If a link is red then the page doesn't exist.

= Does this work with Wordpress MultiSite =

It has not been tested, feel free to test and let me know.

= Where can I get support? =

Available via the WordPress forums (please tag the post esimple-wiki) or via http://quirm.net/forum/

= Compatible with role manager plugins =

Well sort of - to enable full wiki access you also have to allow user to edit_others_posts - this give access to the revisons. This is in addition to all the standard wiki capabilities.

= What is on your To Do list for the plugin? =

Nothing! I'm trying to keep it as small and as lightwieght as possible, but feel free to suggest ideas.

== Changelog == 

= Version 0.6 =

* *fixed* adding capabilities to incorrect role meant administrators couldn't see the wiki

= Version 0.5 =

* *fixed* issue with featured image
* *fixed* styles and scripts no longer load on admin side

= Version 0.4 =

* *fixed* oops.

= Version 0.3 =

* *fixed* contextual help issue.
* *new* compatible with User Role Editor or similar

= Version 0.2 =

* *amended* immproved CSS
* *added* shortcode to show if a page is in need of updating [wikiupdate]
* *fixed* minor bug fixes

= Version 0.1 =

* initial release

== Screenshots ==

may well make it here.

== Upgrade Notice ==

Simply delete the old one, upload and activate or automatically through WordPress.