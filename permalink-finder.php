<?php
/*
Plugin Name: Permalink Finder
Plugin URI: http://www.BlogsEye.com/permalink-finder/
Description: When you migrate from another platform to Wordpress, the canonical names of your posts may subtly change. Old links, including Google may throw 404 errors on your permalinks. In order to redirect your valuable links to the new naming structure, you will need some way of locating the poast based on the information available in the old link. This version adds some configuration control and redirects index pages.
Version: 1.11
Author: Keith P. Graham
Author URI: http://www.BlogsEye.com/

This software is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*/


  
/************************************************************
* 	kpg_permalink_finder()
*	This is kicked off from the template_redirect event.
*	If we arrived here through a 404 error, parse the URL	
*	and try to find a page as much like the bad one as
*	possible.
*************************************************************/
function kpg_permalink_finder() {
	if( is_404() ) {
		// get the configuration
		$updateData=get_option('kpg_permalinfinder_options');
		if ($updateData==null) $updateData=array();
		$kpg_pf_find=$updateData['find']; // used to tell if permalink fixing is going on
		$kpg_pf_index=$updateData['index']; // used to tell if we are redirecting index pages
		// check data and set defaults
		if ($kpg_pf_find==null) $kpg_pf_find='2';
		if ($kpg_pf_find!='9999' && $kpg_pf_find!='1' && $kpg_pf_find!='2' && $kpg_pf_find!='3' && $kpg_pf_find!='4') {
			$kpg_pf_find='2';
		}
		if ($kpg_pf_index==null) $kpg_pf_index='';
		if ($kpg_pf_index!='') $kpg_pf_index='Y';

		$furl=$_SERVER['REQUEST_URI'];
		$burl=get_bloginfo('url');
		// check to see if the user is coming in on a base default
		//echo "debug: $furl, $burl <br/>";
		if ($kpg_pf_index=='Y') { // quick check to see if we are accessing an index page
			if ($furl=='/index.html'||$furl=='/index.htm'||$furl=='/index.shtml'||$furl=='/default.asp') {
					kpg_301_forward(get_bloginfo('url'));
			}
		}

		$plink = basename( $_SERVER['REQUEST_URI'] ); // plink has the page that was 404'd	
		// now figure if we need to fix a permalink
		if ($kpg_pf_find<5) {
			if( $ID = kpg_find_permalink_post( $plink,$kpg_pf_find ) ) { //check for match	
				kpg_301_forward( get_permalink( $ID ) ); // if match forward it.
				return false;
			}
		}
	}
}
/************************************************************
*	kpg_301_forward( $post_new_location )
*	$post_new_location is the new URL.
*	Sends redirect error with new page location
*	Browsers should display the right page.
*	Search Engine Spiders should make note and change index
*************************************************************/
function kpg_301_forward( $post_loc ) {
	header( "HTTP/1.1 301 Moved Permanently" );
	header( "Location: $post_loc" );
	exit();
}
/************************************************************
*	kpg_permalink_finder( $plink )
*	$plink is the permalink value from the url
*	does a lookup on the posts table in order to find the post
*	if not found in posts it tries the pages table.
*************************************************************/
function kpg_find_permalink_post( $plink,$kpg_pf_find ) {
	global $wpdb; // useful db functions
	$plink=strtolower($plink); // make it case insensitive
	$plink=str_replace('.html','',$plink); // get dir of html and shtml at the end - don't need to search for these
	$plink=str_replace('.shtml','',$plink); 
	$plink=str_replace('.htm','',$plink); 
 	$plink=str_replace('_','-',$plink); // underscores should be dashes
 	$plink=str_replace('.','-',$plink); // periods should be dashes (might or might not mean the existence of an extension)
 	$plink=str_replace(' ','-',$plink); // spaces are wrong
 	$plink=str_replace('%20','-',$plink); // spaces are wrong
	$ss=explode("-",$plink); // place into an arrary
	// look for each word in the array. If found add in 1; if not add in 0. Order by sum and the best bet bubbles to top.
	$sql="SELECT ID, ";
	for ($j=0;$j<count($ss);$j++) {
		$sql=$sql." if(INSTR(LCASE(post_name),'".mysql_real_escape_string($ss[$j])."'),1,0)+" ;
	}
	$sql=$sql."0 as CNT FROM ".$wpdb->posts." WHERE post_status = 'publish' ORDER BY CNT DESC, POST_DATE DESC";
	$row=$wpdb->get_row($sql);
	if ($row) {	
	   $ID=$row->ID; 
	   $CNT=$row->CNT;
	   if ($CNT>=$kpg_pf_find) return $ID;
	} 
	return false;
}


/************************************************************
*	kpg_permalink_finder_admin_menu()
*	Adds the admin menu
*************************************************************/
function kpg_permalink_finder_admin_menu() {
   add_options_page('Permalink Finder', 'Permalink Finder', 'manage_options', 'permalink-finder/permalink-finder-options.php');
}

add_action( 'template_redirect', 'kpg_permalink_finder' );
// add the the options to the admin menu
add_action('admin_menu', 'kpg_permalink_finder_admin_menu');

?>