=== Permalink Finder Plugin ===
Tags: permalinks, move, migrate, 301, 404, redirect, PageRank, seo, forward, index file, blogger, blogspot, MU      
Requires at least: 2.8  
Stable tag: 1.70     
Tested up to: 3.1
Contributors: Keith Graham       
Donate link: https://online.nwf.org/site/Donation2?df_id=6620&6620.donation=form1


Never get a 404 page not found again. If you have restructured or moved your blog, this plugin will find the right post or page every time.

== Description ==

The Permalink Finder Plugin detects when Wordpress cannot find a permalink. Before it generates the 404 error it tries to locate any posts with similar words. It does this by searching through the database trying to find any of the word values from the bad link. It takes the best match and then, rather than issuing a 404 error it sends back a redirect to the correct page.
Users will see the page that they are looking for, and search engine spiders will see the 301 redirect and update their databases so that the page appears correctly in searches.

This is especially useful where Wordpress removes words like "the" and "a" from the permalink during conversions from Blogger accounts. It is also useful for migrations that formerly used extensions such as html and shtml, when Wordpress does not.

The search of the database requires a small amount of extra overhead, but it only occurs when Wordpress cannot find the original post and resorts to using this plugin, which should be rare, especially after the search engines fix up their own indexes.

The configuration panel allows a user to select how the plugin finds a missing page. The plugin counts the number of words that match to a post. By default, a two word match is sufficient to cause a redirect to the found page. False positives are possible, especially if the user selects a one word match. Increasing the number of words, however makes it unlikely that the plugin will ever find a match.

Optionally, the plugin will redirect hits on index.html, index.htm, and index.shtml to the blog home page. This is useful when a website previously used a non-php home page.

The plugin will also optionally keep track of the last few 404's or redirects. This is useful to find out what pages are missing or named badly that keep causing 404 errors or forcing redirects.

On Wordpress MU networked installations the plugin can be installed as a network plugin. On Blog #1, the settings can be made so that all other networked blogs will not see the settings. The statistics then will only be visible on the main blog (#1). 


Note: The permalink structure on your blog must be set to include postname. This plugin is only for use with postname permalink structures.


Donations:
If you find this plugin useful and you wish to support me, you can buy my book (cheap) <a href="http://www.amazon.com/gp/product/1456336584?ie=UTF8&tag=thenewjt30page&linkCode=as2&camp=1789&creative=390957&creativeASIN=1456336584">Error Message Eyes: A Programmer's Guide to the Digital Soul</a>
At the very least please visit my websites and, if appropriate, add a link to one on your blog: 
<a href="http://www.blogseye.com" target="_blank">Blog&apos;s Eye</a> (My Wordpress Plugins and other PHP coding projects) <br />
<a href="http://www.cthreepo.com/blog" target="_blank">Wandering Blog </a>(My personal Blog) <br />
<a href="http://www.cthreepo.com" target="_blank">Resources for Science Fiction</a> (Writing Science Fiction) <br />
<a href="http://www.jt30.com" target="_blank">The JT30 Page</a> (Amplified Blues Harmonica) <br />
<a href="http://www.harpamps.com" target="_blank">Harp Amps</a> (Vacuum Tube Amplifiers for Blues) <br />
<a href="http://www.cthreepo.com/bees" target="_blank">Bee Progress Beekeeping Blog</a> (My adventures as a new beekeeper) </p>


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

= 1.50 =
* 04/29/2010 Changed redirect method for to make the plugin compatible with future versions of Wordpress.

= 1.60 =
* 01/14/2011 Cleaned up code. Added support for MU. Used wordpress functions to sanitize urls and find alternate encodings.
* This revision changed the way the plugin works, so please let me know if you experience any problems.

= 1.70 =
* Due to many suggestions for features: Added code to strip get parameters like UTM tags. Added code to optionally strip numbers, common words, and short words.

== Support ==
This plugin is free and I expect nothing in return. If you wisht to support my programming, buy my book: 
<a href="http://www.amazon.com/gp/product/1456336584?ie=UTF8&tag=thenewjt30page&linkCode=as2&camp=1789&creative=390957&creativeASIN=1456336584">Error Message Eyes: A Programmer's Guide to the Digital Soul</a>
Other plugins:
<a href="http://wordpress.org/extend/plugins/open-in-new-window-plugin/">Open in New Window Plugin</a>
<a href="http://wordpress.org/extend/plugins/kindle-this/">Kindle This - publish blog to user's Kindle</a>
<a href="http://wordpress.org/extend/plugins/stop-spammer-registrations-plugin/">Stop Spammer Registrations Plugin</a>
<a href="http://wordpress.org/extend/plugins/no-right-click-images-plugin/">No Right Click Images Plugin</a>
<a href="http://wordpress.org/extend/plugins/collapse-page-and-category-plugin/">Collapse Page and Category Plugin</a>
<a href="http://wordpress.org/extend/plugins/custom-post-type-list-widget/">Custom Post Type List Widget</a>
<a href="http://wordpress.org/extend/plugins/permalink-finder/">Permalink Finder Plugin</a>

