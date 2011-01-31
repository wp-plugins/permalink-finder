<?php
/*
Plugin Name: Permalink Finder
Plugin URI: http://www.BlogsEye.com/
Description: Never get a 404 page not found again. If you have restructured or moved your blog, this plugin will find the right post or page every time.
Version: 1.70
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
		$kpg_pf_short='N'; // new with 1.7
		$kpg_pf_numbs='N'; // new with 1.7
		$kpg_pf_common='N'; // new with 1.7
		$kpg_pf_mu='Y';
		$e404=array();
		$f404=array();
		$options=kpg_pf_get_global_option('kpg_permalinfinder_options');
		if (!is_array($options)) $options=array();
		if (array_key_exists('mu',$options) ) $kpg_pf_mu=$options['mu'];
		if ($kpg_pf_mu=='N') $options=get_option('kpg_permalinfinder_options');
		if ($options==null) $options=array();
		if (!is_array($options)) $options=array();
		if (array_key_exists('find',$options) ) $kpg_pf_find=$options['find'];
		if (array_key_exists('index',$options) ) $kpg_pf_index=$options['index'];
		if (array_key_exists('stats',$options) ) $kpg_pf_stats=$options['stats'];
		if (array_key_exists('labels',$options) ) $kpg_pf_labels=$options['labels'];
		if (array_key_exists('kpg_pf_short',$options) ) $kpg_pf_short=$options['kpg_pf_short'];
		if (array_key_exists('kpg_pf_numbs',$options) ) $kpg_pf_numbs=$options['kpg_pf_numbs'];
		if (array_key_exists('kpg_pf_common',$options) ) $kpg_pf_common=$options['kpg_pf_common'];
		// check data and set defaults
		if ($kpg_pf_find!='9999' && $kpg_pf_find!='1' && $kpg_pf_find!='2' && $kpg_pf_find!='3' && $kpg_pf_find!='4') {
			$kpg_pf_find='2';
		}
		if ($kpg_pf_index!='Y' && $kpg_pf_index!='N') $kpg_pf_index='N';
		if ($kpg_pf_labels!='Y' && $kpg_pf_labels!='N') $kpg_pf_labels='N';
		if ($kpg_pf_short!='Y' && $kpg_pf_short!='N') $kpg_pf_short='N';
		if ($kpg_pf_common!='Y' && $kpg_pf_common!='N') $kpg_pf_common='N';
		if ($kpg_pf_numbs!='Y' && $kpg_pf_numbs!='N') $kpg_pf_numbs='N';
		if ($kpg_pf_stats!='10' && $kpg_pf_stats!='20' && $kpg_pf_stats!='30') {
			$kpg_pf_stats='0';
		}
		if (array_key_exists('e404',$options) ) $e404=$options['e404']; 
		if (array_key_exists('f404',$options) ) $f404=$options['f404']; 
		
		// record any pertanent data
		if ($kpg_pf_stats>'0') {
			$r404=array();
				$r404[0]=date('m/d/Y H:i:s');
				$r404[1]=esc_url($_SERVER['REQUEST_URI']);
				$r404[2]=esc_url_raw(html_entity_decode($_SERVER['HTTP_REFERER']));
				$r404[3]=htmlentities($_SERVER['HTTP_USER_AGENT']);
				$r404[4]=$_SERVER['REMOTE_ADDR'];
		}

		$plink = basename( $_SERVER['REQUEST_URI'] ); // flink has full url that was missed
		if (strpos($plink,'/?'))  $plink=substr($plink,0,strpos($plink,'/?'));
		if (strpos($plink,'?'))  $plink=substr($plink,0,strpos($plink,'?'));
		$plink=trim($plink);
		$plink=trim($plink,'/');
		// check if the incoming line needs a blogger fix
		if ($kpg_pf_labels=='Y') { 
			$flink = $plink; // flink has the page that was 404'd	
				if (strpos($flink,'/labels/')>0) {
				$flink=urldecode($flink);
				$flink=remove_accents($flink);
				$flink=str_replace('/labels/','/category/',$flink);
				$flink=str_replace('.html','',$flink); // get dir of html and shtml at the end - don't need to search for these
				$flink=str_replace('.shtml','',$flink); 
				$flink=str_replace('.htm','',$flink); 
				$flink=sanitize_url($flink);

				if ($kpg_pf_stats>'0') {
					$r404[5]=$flink;
					array_unshift($f404,$r404);
					for ($j=0;$j<10;$j++) {
						$n=count($f404);
						if ($n>$kpg_pf_stats) {
							array_pop($f404);
						}
					}
					$options['f404']=$f404;
					if ($kpg_pf_mu=='N') {
						update_option('kpg_permalinfinder_options', $options);
					} else {
						kpg_pf_set_global_option('kpg_permalinfinder_options', $options);
					}
					
				}
				wp_redirect($flink,"301"); // let wp do it - more compatable.
				exit();
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
					$options['f404']=$f404;
					if ($kpg_pf_mu=='N') {
						update_option('kpg_permalinfinder_options', $options);
					} else {
						kpg_pf_set_global_option('kpg_permalinfinder_options', $options);
					}
				}
				wp_redirect(get_bloginfo('url'),"301"); // let wp do it - more compatable.
				exit();
			}
		}

		// now figure if we need to fix a permalink
		if ($kpg_pf_find<5) {
			$ID = kpg_find_permalink_post( $plink,$kpg_pf_find ,$kpg_pf_numbs ,$kpg_pf_common ,$kpg_pf_short );
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
					$options['f404']=$f404;
					if ($kpg_pf_mu=='N') {
						update_option('kpg_permalinfinder_options', $options);
					} else {
						kpg_pf_set_global_option('kpg_permalinfinder_options', $options);
					}
				} 
				wp_redirect(get_permalink( $ID ),"301"); // let wp do it - more compatable.
				exit();
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
			$options['e404']=$e404;
				if ($kpg_pf_mu=='N') {
					update_option('kpg_permalinfinder_options', $options);
				} else {
					kpg_pf_set_global_option('kpg_permalinfinder_options', $options);
				}
		}
	}
}

/************************************************************
*	kpg_permalink_finder( $plink )
*	$plink is the permalink value from the url
*	does a lookup on the posts table in order to find the post
*	if not found in posts it tries the pages table.
*************************************************************/
function kpg_find_permalink_post( $plink,$kpg_pf_find,$kpg_pf_numbs ,$kpg_pf_common ,$kpg_pf_short  ) {
	global $wpdb; // useful db functions
	// common word list - these tend to skew results so don't use them
	$common="  able about add after again all also am an and any are ask at be been being big but by call came can come could did does don't each end even every for from get give good had has have her here him his how into its just let look low made make many may might more most much must near need new next not now off one other our out over own place put real run same saw say see seem self set she should show side some still such take tell than that the their them then there these they this too try up upon use very want was way well went were what when where which while who why will with would you your ";


	// need to strip off the get params that may have been added at the end for some reason

	// fix up the link - NEW - use the wordpress sanitize link 
	$plink=' '.urldecode($plink); // have no idea why I need this space here.
	$plink = remove_accents($plink);
	$plink=strtolower($plink); // make it case insensitive
	$plink=str_replace('.html','',$plink); // get dir of html and shtml at the end - don't need to search for these
	$plink=str_replace('.shtml','',$plink); 
	$plink=str_replace('.htm','',$plink); 
	$plink=str_replace('.php','',$plink); 
	
	$plink=str_replace(' ','-',$plink); 
	$plink=sanitize_url($plink);
	// first check to see if it is a good slug already - without the fuzzy search
	$post = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_name = %s AND post_status = 'publish'", $plink ));
	if ( $post ) return $post;

	$ss=explode("-",$plink); // place into an arrary
	// look for each word in the array. If found add in 1; if not add in 0. Order by sum and the best bet bubbles to top.
	$sql="SELECT ID, ";
	for ($j=0;$j<count($ss);$j++) {
	    // elminate shorts, numbers, and common
		
		if (!empty($ss[$j])&&((	$kpg_pf_short=='Y'&&strlen($ss[$j])>2)||$kpg_pf_short=='N') 
				&& (($kpg_pf_common=='Y'&&!strpos($common,$ss[$j]))||$kpg_pf_common=='N') 
				&& (($kpg_pf_numbs=='Y'&&!is_numeric($ss[$j]))||$kpg_pf_numbs=='N')  ) {
			$sql=$sql." if(INSTR(LCASE(post_name),'".mysql_real_escape_string($ss[$j])."'),1,0)+" ;
		}
	}
	$sql=$sql."0 as CNT FROM ".$wpdb->posts." WHERE post_status = 'publish' ORDER BY CNT DESC, POST_DATE";
	$row=$wpdb->get_row($sql);
	if ($row) {	
	   $ID=$row->ID; 
	   $CNT=$row->CNT;
	   if ($CNT>=$kpg_pf_find) return $ID;
	} 
	return 0;
}

// use this to find out global options
function kpg_pf_get_global_option($option) {
	// this gets the plugin control - never globalized (I hope)
	if (!function_exists('switch_to_blog')) return get_option($option);
	switch_to_blog(1); 
	$ansa=get_option($option);
	restore_current_blog();
	return $ansa;  
}
function kpg_pf_set_global_option($option,$value) {
	// this gets the plugin control - never globalized (I hope)
	if (!function_exists('switch_to_blog')) return update_option($option,$value);
	switch_to_blog(1); 
	$ansa=update_option($option, $value);
	restore_current_blog();
	return $ansa;  
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
// check to see if we are in MU and the options have been set to the exclusively MU blog 1
	global $blog_id;
	$kpg_pf_mu='Y';
	$options=kpg_pf_get_global_option('kpg_permalinfinder_options');
	if (!is_array($options)) $options=array();
	if (array_key_exists('mu',$options)) $kpg_pf_mu=$options['mu'];
	if ($kpg_pf_mu=='N'||$blog_id==1||(!function_exists('switch_to_blog'))) {
		add_options_page('Permalink Finder', 'Permalink Finder', 'manage_options', 'permalink-finder/permalink-finder-options.php');
	} 
}

add_action( 'template_redirect', 'kpg_permalink_finder' );
// add the the options to the admin menu

add_action('admin_menu', 'kpg_permalink_finder_admin_menu');
if ( function_exists('register_uninstall_hook') ) {
	register_uninstall_hook(__FILE__, 'kpg_permalink_finder_uninstall');
}

?>