<?php
/*
Plugin Name: Permalink Finder
Plugin URI: http://www.BlogsEye.com/
Description: Never get a 404 page not found again. If you have restructured or moved your blog, this plugin will find the right post or page every time.
Version: 1.50
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
		// get the repository flags and data
		$kpg_pf_find='2'; 
		$kpg_pf_index='N';
		$kpg_pf_stats='0';
		$kpg_pf_labels='N';
		$kpg_pf_oldcat='';
		$kpg_pf_newcat='';
		$kpg_pf_oldtag='';
		$kpg_pf_newtag='';
		$e404=array();
		$f404=array();
		$updateData=get_option('kpg_permalinfinder_options');
		if ($updateData==null) $updateData=array();
		if (!is_array($updateData)) $updateData=array();
		if (array_key_exists('find',$updateData) ) $kpg_pf_find=$updateData['find'];
		if (array_key_exists('index',$updateData) ) $kpg_pf_index=$updateData['index'];
		if (array_key_exists('stats',$updateData) ) $kpg_pf_stats=$updateData['stats'];
		if (array_key_exists('labels',$updateData) ) $kpg_pf_labels=$updateData['labels'];
		// working on this - holding off installing right now
		if (array_key_exists('oldtag',$updateData) ) $kpg_pf_oldtag=$updateData['oldtag'];
		if (array_key_exists('newtag',$updateData) ) $kpg_pf_newtag=$updateData['newtag'];
		if (array_key_exists('oldcat',$updateData) ) $kpg_pf_oldcat=$updateData['oldcat'];
		if (array_key_exists('newcat',$updateData) ) $kpg_pf_newcat=$updateData['newcat'];
		// check data and set defaults
		if ($kpg_pf_find!='9999' && $kpg_pf_find!='1' && $kpg_pf_find!='2' && $kpg_pf_find!='3' && $kpg_pf_find!='4') {
			$kpg_pf_find='2';
		}
		if ($kpg_pf_index!='Y' && $kpg_pf_index!='N') $kpg_pf_index='N';
		if ($kpg_pf_labels!='Y' && $kpg_pf_labels!='N') $kpg_pf_labels='N';
		if ($kpg_pf_stats!='10' && $kpg_pf_stats!='20' && $kpg_pf_stats!='30') {
			$kpg_pf_stats='0';
		}
		if (array_key_exists('e404',$updateData) ) $e404=$updateData['e404']; 
		if (array_key_exists('f404',$updateData) ) $f404=$updateData['f404']; 
		
		// record any pertanent data
		if ($kpg_pf_stats>'0') {
			$r404=array();
				$r404[0]=date('m/d/Y H:i:s');
				$r404[1]=$_SERVER['REQUEST_URI'];
				$r404[2]=html_entity_decode($_SERVER['HTTP_REFERER']);
				$r404[3]=$_SERVER['HTTP_USER_AGENT'];
				$r404[4]=$_SERVER['REMOTE_ADDR'];
		}

		$plink = basename( $_SERVER['REQUEST_URI'] ); // flink has full url that was missed
		// check if the incoming line needs a blogger fix
		if ($kpg_pf_labels=='Y') { 
			$flink = $_SERVER['REQUEST_URI']; // plink has the page that was 404'd	
			if (strpos($flink,'/labels/')>0) {
				$flink=str_replace('/labels/','/category/',$flink);
				$flink=str_replace('.html','',$flink); // get dir of html and shtml at the end - don't need to search for these
				$flink=str_replace('.shtml','',$flink); 
				$flink=str_replace('.htm','',$flink); 
				$flink=str_replace('_','-',$flink); // underscores should be dashes
				$flink=str_replace('.','-',$flink); // periods should be dashes 
				$flink=str_replace(' ','-',$flink); // spaces are wrong
				$flink=str_replace('%20','-',$flink); // spaces are wrong
				if ($kpg_pf_stats>'0') {
					$r404[5]=$flink;
					array_unshift($f404,$r404);
					for ($j=0;$j<10;$j++) {
						$n=count($f404);
						if ($n>$kpg_pf_stats) {
							array_pop($f404);
						}
					}
					$updateData['f404']=$f404;
					update_option('kpg_permalinfinder_options', $updateData);
				}
				wp_redirect($flink,"301"); // let wp do it - more compatable.
				exit();
				//kpg_301_forward($flink);
				//return;
			}
		}
		
		// check to see if the user is coming in on a base default
		if ($kpg_pf_index=='Y') { // quick check to see if we are accessing an index page
			if ($plink=='index.html'||$plink=='index.htm'||$plink=='index.shtml'||$plink=='default.asp') {
				if ($kpg_pf_stats>'0') {
					$r404[5]=get_bloginfo('url');
					array_unshift($f404,$r404);
					for ($j=0;$j<10;$j++) {
						$n=count($f404);
						if ($n>$kpg_pf_stats) {
							array_pop($f404);
						}
					}
					$updateData['f404']=$f404;
					update_option('kpg_permalinfinder_options', $updateData);
				}
				wp_redirect(get_bloginfo('url'),"301"); // let wp do it - more compatable.
				exit();
				//kpg_301_forward(get_bloginfo('url'));
				//return;
			}
		}

		// now figure if we need to fix a permalink
		if ($kpg_pf_find<5) {
			$ID = kpg_find_permalink_post( $plink,$kpg_pf_find );
			if( $ID>0 )  { //check for match	
				if ($kpg_pf_stats>'0') {
					$r404[5]=get_permalink( $ID );
					array_unshift($f404,$r404);
					for ($j=0;$j<10;$j++) {
						$n=count($f404);
						if ($n>$kpg_pf_stats) {
							array_pop($f404);
						}
					}
					$updateData['f404']=$f404;
					update_option('kpg_permalinfinder_options', $updateData);
				} 
				wp_redirect(get_permalink( $ID ),"301"); // let wp do it - more compatable.
				exit();
				//kpg_301_forward( get_permalink( $ID ) ); // if match forward it.
				// return;
			}
		}
		// still here, it must be a real 404, we should log it
		if ($kpg_pf_stats>'0') {
			array_unshift($e404,$r404);
			for ($j=0;$j<10;$j++) {
				$n=count($e404);
				if ($n>$kpg_pf_stats) {
					unset($e404[$n-1]);
				}
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
    // write out a log of what is happening
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
	return 0;
}
function kpg_permalink_finder_uninstall() {
	if(!current_user_can('manage_options')) {
		die('Access Denied');
	}
	delete_option('kpg_permalinfinder_options'); 
	return;
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
if ( function_exists('register_uninstall_hook') ) {
	register_uninstall_hook(__FILE__, 'kpg_permalink_finder_uninstall');
}

?>