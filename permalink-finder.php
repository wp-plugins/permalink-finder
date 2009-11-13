<?php
/*
Plugin Name: Permalink Finder
Plugin URI: http://www.BlogsEye.com/permalink-finder/
Description: When you migrate from another platform to Wordpress, the canonical names of your posts may subtly change. Old links, including Google may throw 404 errors on your permalinks. In order to redirect your valuable links to the new naming structure, you will need some way of locating the poast based on the information available in the old link. Redirects links to index pages and keeps a log of recent 404 errors and redirects.
Version: 1.20
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
		// record any pertanent data
		// get the configuration
		$updateData=get_option('kpg_permalinfinder_options');
		if ($updateData==null) $updateData=array();
		$kpg_pf_find=$updateData['find']; // used to tell if permalink fixing is going on
		if ($kpg_pf_find==null) $kpg_pf_find='2';
		if ($kpg_pf_find!='9999' && $kpg_pf_find!='1' && $kpg_pf_find!='2' && $kpg_pf_find!='3' && $kpg_pf_find!='4') {
			$kpg_pf_find='2';
		}
		$kpg_pf_index=$updateData['index']; // used to tell if we are redirecting index pages
		if ($kpg_pf_index==null) $kpg_pf_index='';
		if ($kpg_pf_index!='') $kpg_pf_index='Y';
		$kpg_pf_stats = $updateData['stats'];
		if ($kpg_pf_stats==null) $kpg_pf_stats='10';
		if ($kpg_pf_stats!='10' && $kpg_pf_stats!='20' && $kpg_pf_stats!='30') {
			$kpg_pf_stats='0';
		}
		if ($kpg_pf_stats>'0') {
		$r404=array();
			$r404[0]=date('m/d/Y H:i:s');
			$r404[1]=$_SERVER['REQUEST_URI'];
			$r404[2]=html_entity_decode($_SERVER['HTTP_REFERER']);
			$r404[3]=$_SERVER['HTTP_USER_AGENT'];
			$r404[4]=$_SERVER['REMOTE_ADDR'];
		}

		$plink = basename( $_SERVER['REQUEST_URI'] ); // plink has the page that was 404'd	
		// check to see if the user is coming in on a base default
		if ($kpg_pf_index=='Y') { // quick check to see if we are accessing an index page
			if ($plink=='index.html'||$plink=='index.htm'||$plink=='index.shtml'||$plink=='default.asp') {
				if ($kpg_pf_stats>'0') {
					$f404=$updateData['f404']; // keep this in an array of arrays
					if ($f404==null) {
						$f404=array();
					}
					$r404[5]=get_bloginfo('url');
					$n=count($f404,$r404);
					 while ($n>$kpg_pf_stats) {
						unset($f404[$kpg_pf_stats]);
						$n=count($f404);
					}
					$updateData['f404']=$f404;
					update_option('kpg_permalinfinder_options', $updateData);
				}
				kpg_301_forward(get_bloginfo('url'));
			}
		}

		// now figure if we need to fix a permalink
		if ($kpg_pf_find<5) {
			if( $ID = kpg_find_permalink_post( $plink,$kpg_pf_find ) ) { //check for match	
				if ($kpg_pf_stats>'0') {
					$f404=$updateData['f404']; // keep this in an array of arrays
					if ($f404==null) $f404=array();
					$r404[5]=get_permalink( $ID );
					$n=array_unshift($f404,$r404);
					while ($n>$kpg_pf_stats) {
						unset($f404[$kpg_pf_stats]);
						$n=count($f404);
					}
					$updateData['f404']=$f404;
					update_option('kpg_permalinfinder_options', $updateData);
				}
				kpg_301_forward( get_permalink( $ID ) ); // if match forward it.
				return false;
			}
		}
		// still here, it must be a real 404, we should log it
		if ($kpg_pf_stats>'0') {
			$e404=$updateData['e404']; // keep this in an array of arrays
			if ($e404==null) $e404=array();
			$n=array_unshift($e404,$r404);
			if ($n>$kpg_pf_stats) {
				unset($e404[$kpg_pf_stats]);
			}
			$updateData['e404']=$e404;
			update_option('kpg_permalinfinder_options', $updateData);
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