=== Permalink Finder Plugin ===
Tags: permalinks, move, migrate, 301, 404, redirect, PageRank, seo, forward, index file, blogger, blogspot       
Requires at least: 2.0       
Tested up to: 2.9.1
Contributors: Keith Graham       
Donate link: https://online.nwf.org/site/Donation2?df_id=6620&6620.donation=form1
Stable tag: 1.40

Detects 404 errors and does a search for pages that partially match the url entered. Redirects to good permalinks or index files.

== Description ==

The Permalink Finder Plugin detects when Wordpress cannot find a permalink. Before it generates the 404 error it tries to locate any posts with similar words. It does this by searching through the database trying to find any of the word values from the bad link. It takes the best match and then, rather than issuing a 404 error it sends back a redirect to the correct page.
Users will see the page that they are looking for, and search engine spiders will see the 301 redirect and update their databases so that the page appears correctly in searches.

This is especially useful where Wordpress removes words like "the" and "a" from the permalink during conversions from Blogger accounts. It is also useful for migrations that formerly used extensions such as html and shtml, when Wordpress does not.

The search of the database requires a small amount of extra overhead, but it only occurs when Wordpress cannot find the original post and resorts to using this plugin, which should be rare, especially after the search engines fix up their own indexes.

The configuration panel allows a user to select how the plugin finds a missing page. The plugin counts the number of words that match to a post. By default, a two word match is sufficient to cause a redirect to the found page. False positives are possible, especially if the user selects a one word match. Increasing the number of words, however makes it unlikely that the plugin will ever find a match.

Optionally, the plugin will redirect hits on index.html, index.htm, and index.shtml to the blog home page. This is useful when a website previously used a non-php home page.

The plugin will also optionally keep track of the last few 404's or redirects. This is useful to find out what pages are missing or named badly that keep causing 404 errors or forcing redirects.

Note: The permalink structure on your blog must be set to include postname. This plugin is only for use with postname permalink structures.


Donations:
If you find this plugin useful, please visit my websites and, if appropriate, add a link to one on your blog: 
<a href="http://www.cthreepo.com/">Resources for Science Fiction Writers</a>
<a href="http://www.freenameastar.com/">Name a real star for free</a>
<a href="http://www.jt30.com/">Amplified Blues Harmonica</a>
or visit the <a href="https://online.nwf.org/site/Donation2?df_id=6620&6620.donation=form1">National Wildlife Federation</a>.


== Installation ==

1. Download the plugin.
2. Upload the plugin to your wp-content/plugins directory.
3. Activate the plugin.
4. Change any options in the Permalink Finder settings.
The plugin can be tested by adding or deleting words from a working permalink in your browser address area. Even if you mangle the permalink it should find a valid link and almost always it will find the correct link. 


== Changelog ==

= 1.0 =
* initial release 

= 1.1 =
* added ability select degree of matching on bad urls.
* added the ability to redirect index.htm, index.html and index.shtml to blog home page.
* fixed a stupid name in the install directory - should be "permalink-finder" no s.

= 1.11 =
* 10/26/2009 Fixed index option to work on PHP4 on some servers.

= 1.20 =
* 11/04/2009 Added a short log of fixed and unfixed permalinks.

= 1.21 =
* 11/24/2009 Fixed a bug in recording the permalinks that caused a 500 error. Formatted the urls as links in the report.

= 1.30 =
* 01/10/2010 added uninstall procedure. Add links to 404 area of report.

= 1.40 =
* 02/23/2010 Fixed errors setting and unsetting variables.

== Support ==

This plugin is in active development. All feedback is welcome on <a href="http://www.blogseye.com/" title="Wordpress plugin: Permalinks Finder Plugin">My Wordpress and other program development</a>.
