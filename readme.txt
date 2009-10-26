=== Permalink Finder Plugin ===
Tags: permalinks, move, migrate, 301, 404, redirect, PageRank, google, seo, forward       
Stable tag: trunk       
Requires at least: 2.0       
Tested up to: 2.8.5
Contributors: Keith Graham       
Stable tag: 1.1

Detects 404 errors and does a search for pages that partially match the url entered. Fixes mangled or altered permalinks and index files.

== Description ==

The Permalink Finder Plugin detects when Wordpress cannot find a permalink. Before it generates the 404 error it tries to locate any posts with similar words. It does this by searching through the database trying to find any of the word values from the bad link. It takes the best match and then, rather than issuing a 404 error it sends back a redirect to the correct page.
Users will see the page that they are looking for, and search engine spiders will see the 301 redirect and update their databases so that the page appears correctly in searches.

This is especially useful where Wordpress removes words like "the" and "a" from the permalink during conversions from Blogger accounts. It is also useful for migrations that formerly used extensions such as html and shtml, when Wordpress does not.

The search of the database requires a small amount of extra overhead, but it only occurs when Wordpress cannot find the original post and resorts to using this plugin, which should be rare, especially after the search engines fix up their own indexes.

The configuration panel allows a user to select how the plugin finds a missing page. The plugin counts the number of words that match to a post. By default, a two word match is sufficient to cause a redirect to the found page. False positives are possible, especially if the user selects a one word match. Increasing the number of words, however makes it unlikely that the plugin will ever find a match.

Optionally, the plugin will redirect hits on index.html, index.htm, and index.shtml to the blog home page. This is useful when a website previously used a non-php home page.

== Installation ==

1. Download the plugin.
2. Upload the plugin to your wp-content/plugins directory.
3. Activate the plugin.
The plugin can be tested by adding or deleting words from a working permalink in your browser address area. Even if you mangle the permalink it should find a valid link and almost always it will find the correct link.

== Changelog ==

= 1.0 =
* initial release 

= 1.1 =
* added ability select degree of matching on bad urls.
* added the ability to redirect index.htm, index.html and index.shtml to blog home page.
* fixed a stupid name in the install directory - should be "permalink-finder" no s.

== Support ==

This plugin is in active development. All feedback is welcome on <a href="http://www.blogseye.com/permalink-finder-plugin" title="Wordpress plugin: Permalinks Finder Plugin">My Wordpress and other program development</a>.
