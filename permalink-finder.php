<?php
/*
Plugin Name: Permalink Finder
Plugin URI: http://www.BlogsEye.com/PermaLinks-Finder/
Description: When you migrate from another platform to Wordpress, the canonical names of your posts may subtly change. Old links, including Google may throw 404 errors on your permalinks. In order to redirect your valuable links to the new naming structure, you will need some way of locating the poast based on the information available in the old link.
Version: 1.0
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
		$plink = basename( $_SERVER['REQUEST_URI'] ); // plink has the page that was 404'd	 
		if( $ID = kpg_find_permalink_post( $plink ) ) { //check for match	
			//logError("permalink=".get_permalink( $ID ) );
			kpg_301_forward( get_permalink( $ID ) ); // if match forward it.
			return false;
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
	//logError("Trying to relocate to ".post_loc);
	//echo "Trying to relocate to ".$post_loc;
	header( "Location: $post_loc" );
	exit();
}


/************************************************************
*	kpg_permalink_finder( $plink )
*	$plink is the permalink value from the url
*	does a lookup on the posts table in order to find the post
*	if not found in posts it tries the pages table.
*************************************************************/
function kpg_find_permalink_post( $plink ) {
 
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
		$sql=$sql." if(INSTR(LCASE(post_name),'".$ss[$j]."'),1,0)+" ;
	}
	$sql=$sql."0 as CNT FROM ".$wpdb->posts." WHERE post_status = 'publish' ORDER BY CNT DESC";
	$row=$wpdb->get_row($sql);
	if ($row) {	
	   $ID=$row->ID; 
	   $CNT=$row->CNT;
	   if ($CNT>0) return $ID;
	}
	return false;
}

function logError($msg) {
	// used during debugging
	 $f = fopen( 'kpgerrors.txt', "a" );
	 $d=date('Y/m/d H:i:s');
	 fwrite($f,"$d: check mover $msg \r\n");
	 fclose($f);

}

  // Plugin added to Wordpress plugin architecture
add_action( 'template_redirect', 'kpg_permalink_finder' );

?>