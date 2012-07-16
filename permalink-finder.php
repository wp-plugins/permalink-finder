<?php
/*
Plugin Name: Permalink Finder
Plugin URI: http://www.BlogsEye.com/
Description: Never get a 404 page not found again. If you have restructured or moved your blog, this plugin will find the right post or page every time.
Version: 2.0
Author: Keith P. Graham
Author URI: http://www.BlogsEye.com/

This software is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*/
/***************************************************************

My apologies to anyone trying to read this code.
This was the first plugin that I wrote and at the time
I did not understand how things work in PHP and Wordpress.
I have added to it with cut and paste from my other plugins
and I have moved and changed many parts within.

The results work, but who knows why? It is still filled
with redundnacies and remnents of my early days of PHP
coding. The 2.0 version rewrote great parts of it, but
left in much of the guts. I cringe at some of my code, but 
it all should work.

***************************************************************/
// installation
// add the hooks:
add_action( 'template_redirect', 'kpg_permalink_finder' ); // fall back 404 check - old way to check
add_action('admin_menu', 'kpg_permalink_finder_admin_menu');



// check to see if this is an MU installation
if (function_exists('is_multisite') && is_multisite()) {
	// include the hook the get/set options so that it works for multisite
	// check the blog 1 options to see if we should hook the mu options
	$kpg_pf_mu='Y';
	global $blog_id;
	if (isset($blog_id)&&$blog_id==1) {
		// no need to switch blogs
		$ansa=get_option('kpg_permalinfinder_options');
		if (empty($ansa)) $ansa=array();
		if (!is_array($ansa)) $ansa=array();
	} else {
		switch_to_blog(1);
		$ansa=get_option('kpg_permalinfinder_options');
		if (empty($ansa)) $ansa=array();
		if (!is_array($ansa)) $ansa=array();
		restore_current_blog();
	}
	if (array_key_exists('kpg_pf_mu',$ansa)) $kpg_pf_mu=$ansa['kpg_pf_mu'];
	if ($kpg_pf_mu!='N') $kpg_pf_mu='Y';
	if ($kpg_pf_mu=='Y') {
		include('includes/pf-mu-options.php');
		kpg_pf_global_setup();
	}
}

function kpg_permalink_finder_admin_menu() {
	add_options_page('Permalink Finder', 'Permalink Finder', 'manage_options', 'permalink_finder','kpg_permalink_finder_control');
}

// how early can we determine the 404 error?
// we may know 404 anywhere before template redirect
/* parse_request*
	send_headers*
	parse_query*
	pre_get_posts*
	posts_selection
	wp*
	template_redirect
*/

// uninstall routine
function kpg_permalink_finder_uninstall() {
	if(!current_user_can('manage_options')) {
		die('Access Denied');
	}
	delete_option('kpg_permalinfinder_options'); 
	return;
}
if ( function_exists('register_uninstall_hook') ) {
	register_uninstall_hook(__FILE__, 'kpg_permalink_finder_uninstall');
}
// actions to handle 404
function kpg_permalink_finder() {
	// if we made it here, remove the redundant actions
	if (!is_404()) return;
	remove_action('template_redirect', 'kpg_permalink_finder');
	kpg_pf_errorsonoff();
	kpg_permalink_fixer(); 
	kpg_pf_errorsonoff('off');
	return; // if we are redirecting we will be back. if not return for legit 404
}


// Guts of the plugin. This is where we do the redirect. We are already in a 404 before we get here.
function kpg_permalink_fixer() {
	$options=kpg_pf_get_options();
	extract($options);
	$plink = $_SERVER['REQUEST_URI'];
	
	if (strpos($plink,'?')!==false)  $plink=substr($plink,0,strpos($plink,'?'));
	if (strpos($plink,'#')!==false)  $plink=substr($plink,0,strpos($plink,'#'));
	$flink = $plink; // flink has the page that was 404'd - not the basename
	if (strpos($plink."\t","/\t")!==false)  $plink=substr($plink,0,strpos($plink."\t","/\t"));
	
	
	//$plink=basename($plink); // plink now is the permalink part of the request.
	// often I found this is wrong, I want to use the wholw taxonomy in the search
	
	// remove the indexes if exist
	if (strpos(strtolower($plink)."\t","/index.html\t")!==false) $plink=substr($plink."\t",0,strpos(strtolower($plink)."\t","/index.html\t"));
	if (strpos(strtolower($plink)."\t","/index.htm\t")!==false) $plink=substr($plink."\t",0,strpos(strtolower($plink)."\t","/index.htm\t"));
	if (strpos(strtolower($plink)."\t","/index.shtml\t")!==false) $plink=substr($plink."\t",0,strpos(strtolower($plink)."\t","/index.shtml\t"));
	if (strpos(strtolower($plink)."\t","/default.asp\t")!==false) $plink=substr($plink."\t",0,strpos(strtolower($plink)."\t","/default.asp\t"));

	// remove the server
	$plink=str_ireplace(home_url(),'',$plink);
	// now get rid of the slashes
	$plink=trim($plink);
	$plink=trim($plink,'/');

	$plink=str_replace('/','-',$plink); // this way the taxonomy becomes part of the search
	
	
	$ref=$_SERVER['HTTP_REFERER'];	
	$ref=esc_url_raw($ref);
	$ref=strip_tags($ref);
	$ref=remove_accents($ref);
	$ref=kpg_pf_really_clean($ref);
	$agent=$_SERVER['HTTP_USER_AGENT'];
	$agent=strip_tags($agent);
	$agent=remove_accents($agent);
	$agent=kpg_pf_really_clean($agent);
	$agent=htmlentities($agent);
	$request=$_SERVER['REQUEST_URI'];
	$request=esc_url_raw($request);
	$request=strip_tags($request);
	$request=remove_accents($request);
	$request=kpg_pf_really_clean($request);
	
	
	// set up stats
	$r404=array();
	$r404[0]=date('m/d/Y H:i:s',time() + ( get_option( 'gmt_offset' ) * 3600 ));
	$r404[1]=$request;
	$r404[2]=$ref;
	$r404[3]=$agent;
	$r404[4]=$_SERVER['REMOTE_ADDR'];

	// do not mess with robots trying to find wp-login.php and wp-signup.php
	if (strpos($plink."\t","/wp-login.php\t")!==false||strpos($plink."\t","/wp-signup.php\t")!==false||strpos($plink."\t","/feed\t")!==false){
		array_unshift($e404,$r404);
		for ($j=0;$j<10;$j++) {
			$n=count($e404);
			if ($n>$stats) {
				unset($e404[$n-1]);
			}
		}
		//echo "\r\n\r\n<!-- step 6 -->\r\n\r\n";
		$options['e404']=$e404;
		update_option('kpg_permalinfinder_options', $options);
		return;
	}

	// check for bypassed or generated files
	if ($chkrobots=='Y'&&strpos(strtolower($plink)."\t","robots.txt\t")!==false) {
		// looking for a robots.txt
		// header out the .txt file
		header('HTTP/1.1 200 OK');
		header('Content-Type: text/plain');
		echo $robots;
		exit();
	}
	
	if ($chkcrossdomain=='Y'&&strpos(strtolower($plink)."\t","crossdomain.xml\t")!==false) {
		// looking for a robots.txt
		// header out the .txt file
		header('HTTP/1.1 200 OK');
		header('Content-Type: application/xml');  
		echo '<'.'?xml version="1.0"?'.">\r\n"; // because of ? and stuff need to echo this separate
		?>
<!DOCTYPE cross-domain-policy SYSTEM "http://www.macromedia.com/xml/dtds/cross-domain-policy.dtd">
<cross-domain-policy>
<allow-access-from domain="<?php echo $_SERVER["HTTP_HOST"]; ?>" />
</cross-domain-policy>
		<?php
		exit();
	}


	if ($chkicon=='Y'&&strpos(strtolower($plink)."\t","favicon.ico\t")!==false) {
		// this only works if the favicon.ico is being redirected to wordpress on a 404
		$f=dirname(__FILE__)."/includes/favicon.ico";
		if (file_exists($f)) {
			if (function_exists('header_remove'))header_remove();
			ini_set('zlib.output_compression','Off');
			header('HTTP/1.1 200 OK');
			header('Content-Type: image/x-icon');           
			header('Content-Disposition: attachment; filename="favicon.ico"');
			readfile($f);
			exit();
		}
	}
/*
	apple-touch-icon-57x57-precomposed.png
	apple-touch-icon-57x57.png
	apple-touch-icon-precomposed.png
	apple-touch-icon.png
*/
	if ($chkicon=='Y'&&(strpos(strtolower($plink)."\t","apple-touch-icon.png\t")!==false
			||strpos(strtolower($plink)."\t","apple-touch-icon-57x57.png\t")!==false
			||strpos(strtolower($plink)."\t","apple-touch-icon-precomposed.png\t")!==false
			||strpos(strtolower($plink)."\t","apple-touch-icon.png\t")!==false
		)
	) {
		// this only works if the favicon.ico is being redirected to wordpress on a 404
		$f=dirname(__FILE__)."/includes/apple-touch-icon.png";
		if (file_exists($f)) {
			if (function_exists('header_remove'))header_remove();
			ini_set('zlib.output_compression','Off');
			header('HTTP/1.1 200 OK');
			header('Content-Type: image/png');           
			readfile($f);
			exit();
		}
	}

	if ($chksitemap=='Y'&&strpos(strtolower($plink)."\t","sitemap.xml\t")!==false) {
		// if there is no sitemap, return the last 20 entries made
		header('HTTP/1.1 200 OK');
		header('Content-Type: application/xml');  
		$sitemap=kpg_pf_sitemap();
		exit();
	}
	if ($chkdublin=='Y'&&strpos(strtolower($plink)."\t","dublin.rdf\t")!==false) {
		// dublin.rdf is a little used method for robots to get more info about your site
		header('HTTP/1.1 200 OK');
		header('Content-Type: application/xml');  
		echo '<'.'?xml version="1.0"?'.'>'; // because of ? and stuff need to echo this separate
	?>
 <rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:dc= "http://purl.org/dc/elements/1.1/">
 <rdf:Description rdf:about="<?php echo get_home_url(); ?>">
 <dc:contributor><?php echo get_bloginfo('name'); ?></dc:contributor>
 <dc:date><?php echo date('Y-m-d',time() + ( get_option( 'gmt_offset' ) * 3600 )); ?></dc:date>
 <dc:description><?php echo get_bloginfo('description'); ?></dc:description>
 <dc:language><?php echo get_bloginfo('language'); ?></dc:language>
 <dc:publisher></dc:publisher>
 <dc:source><?php echo get_home_url(); ?></dc:source>
 </rdf:Description>
 </rdf:RDF>

	<?php
		exit();
	}
	if ($chkopensearch=='Y'&&(strpos(strtolower($plink)."\t","opensearch.xml\t")!==false||strpos(strtolower($plink)."\t","search.xml\t")!==false)) {
		// search.xml may hel people search your site.
		header('HTTP/1.1 200 OK');
		header('Content-Type: application/xml');  
		echo '<'.'?xml version="1.0"?'.">\r\n"; // because of ? and stuff need to echo this separate
	?>
 <OpenSearchDescription xmlns="http://a9.com/-/spec/opensearch/1.1/">
 <ShortName><?php echo get_bloginfo('name'); ?></ShortName>
 <Description>Search this site</Description>
 <Image>favicon.ico</Image>
 <Url type="text/html" template="<?php echo get_home_url(); ?>/seach"/>
 </OpenSearchDescription>
 

	<?php
		exit();
	}
	// some file types should not be included. these files are true 404s and Wordpress can't fix that.
	$ignoreTypes=array(
	'jpg',
	'gif',
	'png',
	'pdf',
	'txt',
	'asp',
	'php',
	'cfm',
	'js',
	'xml',
	'php',
	'mp3',
	'wmv',
	'css'
	);
    foreach ($ignoreTypes as $it) {
		if(strpos(strtolower($plink)."\t",'.'.$it."\t")!==false) {
			array_unshift($e404,$r404);
			for ($j=0;$j<10;$j++) {
				$n=count($e404);
				if ($n>$stats) {
					unset($e404[$n-1]);
				}
			}
			//echo "\r\n\r\n<!-- step 6 -->\r\n\r\n";
			$options['e404']=$e404;
			update_option('kpg_permalinfinder_options', $options);
			return;
		}
	}

	// santize to get rid of all odd characters, including cross browser scripts.
	$plink=strtolower($plink); // make it case insensitive
	// do some more cleanup
	$plink=urldecode($plink);
	$plink=strip_tags($plink);
	$plink=remove_accents($plink);
	$plink=kpg_pf_really_clean($plink);
	$plink=str_replace('_','-',$plink);
	$plink=str_replace(' ','-',$plink); 
	$plink=sanitize_title_with_dashes($plink); // gets rid of some words that wordpress things are unimportant
	// check if the incoming line needs a blogger fix
	if (empty($plink)) {
		// redirect back to siteurl
		$flink=home_url();
		$r404[5]=$flink;
		$options['f404']=$f404;
		update_option('kpg_permalinfinder_options', $options);
		wp_redirect($flink,(int)$kpg_pf_301); // let wp do it - more compatable.
		exit();
	}

	if ($labels=='Y') { 
			if (strpos($flink,'/labels/')>0) {
				$flink=str_replace('/labels/','/category/',$flink);
				$flink=str_replace('.html','',$flink); // get dir of html and shtml at the end - don't need to search for these
				$flink=str_replace('.shtml','',$flink); 
				$flink=str_replace('.htm','',$flink); 
				$flink=str_replace('_','-',$flink); // underscores should be dashes
				$flink=str_replace('.','-',$flink); // periods should be dashes 
				$flink=str_replace(' ','-',$flink); // spaces are wrong
				$flink=str_replace('%20','-',$flink); // spaces are wrong
			if ($stats>0) {
				$r404[5]=$flink;
				array_unshift($f404,$r404);
				for ($j=0;$j<10;$j++) {
					$n=count($f404);
					if ($n>$stats) {
						array_pop($f404);
					}
				}
				$options['f404']=$f404;
				update_option('kpg_permalinfinder_options', $options);
			}
			wp_redirect($flink,(int)$kpg_pf_301); // let wp do it - more compatable.
			exit();
		}
	}
	
	// check to see if the user is coming in on a base default

	// now figure if we need to fix a permalink
	//echo "\r\n\r\n<!-- step 2 $find -->\r\n\r\n";
	if ($find>0) {
		$plink=str_replace('.html','',$plink); // get dir of html and shtml at the end - don't need to search for these
		$plink=str_replace('.shtml','',$plink); 
		$plink=str_replace('.htm','',$plink); 
		$plink=str_replace('.php','',$plink); 
		$ID = kpg_find_permalink_post( $plink,$find ,$kpg_pf_numbs ,$kpg_pf_common ,$kpg_pf_short,false );
		if( $ID==false && $chkmetaphone=='Y')  { 
			// missed on regular words - try a metaphone search??
			$ID = kpg_find_permalink_post( $plink,$find ,$kpg_pf_numbs ,$kpg_pf_common ,$kpg_pf_short,true );
		}
		if( $ID!==false)  { 
			if ($stats>0) {
				$r404[5]=get_permalink( $ID );
				array_unshift($f404,$r404);
				for ($j=0;$j<10;$j++) {
					$n=count($f404);
					if ($n>$stats) {
						array_pop($f404);
					}
				}
				$options['f404']=$f404;
				update_option('kpg_permalinfinder_options', $options);
			} 
			wp_redirect(get_permalink( $ID ),(int)$kpg_pf_301); // let wp do it - more compatable.
			exit();
		}
	}
	// still here, it must be a real 404, we should log it
	if ($stats>0) {
		//echo "\r\n\r\n<!-- step 5 -->\r\n\r\n";
		array_unshift($e404,$r404);
		for ($j=0;$j<10;$j++) {
			$n=count($e404);
			if ($n>$stats) {
				unset($e404[$n-1]);
			}
		}
		//echo "\r\n\r\n<!-- step 6 -->\r\n\r\n";
		$options['e404']=$e404;
		update_option('kpg_permalinfinder_options', $options);
	}

	return; // end of permalink fixer
}

//kpg_find_permalink_post
function kpg_find_permalink_post( $plink,$find,$kpg_pf_numbs ,$kpg_pf_common ,$kpg_pf_short,$metaphone=false  ) {
	global $wpdb; // useful db functions
	// common word list - these tend to skew results so don't use them
	$common="  am an and at be but by did does had has her him his its may she than that the them then there these they ";
	
	// first check to see if it is a good slug already - without the fuzzy search
	$post = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_name = %s AND post_status = 'publish'", $plink ));
	//echo "\r\n\r\n<!-- step 3a '$plink' -->\r\n\r\n";

	if ( $post ) return $post;

	$ss1=explode("-",$plink); // place into an arrary
	$ss=array();
	// look for each word in the array. If found add in 1; if not add in 0. Order by sum and the best bet bubbles to top.
	// remove the numbers and small words from $ss1
	foreach($ss1 as $se) {
		if (!empty($se)) {
			if($kpg_pf_numbs=='Y' && is_numeric($se)) {
				// ignore this guy - he's numeric
			} else if ($kpg_pf_common=='Y'&& strpos(' '.$common.' ',$se)!==false) {
				// ignore because of a common word
			} else if ($kpg_pf_short=='Y' && strlen($se)<3) {
				// ignore the word it is too short
			} else {
				// use this word
				$ss[count($ss)]=$se;
			}
		}
	}
	if (empty($ss)) return false;
	if ($metaphone) {
		// we need to do the search but do a metaphone  search on each word
		$ss1=$ss;
		$ss=array();
		foreach($ss1 as $se) {
			if (strlen(metaphone($se))>1) {
				$ss[]=metaphone($se);
			}
		}
		if (empty($ss)) return false;
		if ($find>count($ss)) $find=$count($ss);
		if (empty($ss)) return false;
		$sql="SELECT ID,post_name as PN FROM ".$wpdb->posts." WHERE post_status = 'publish' ORDER BY post_modified DESC";
		$rows=$wpdb->get_results($sql,ARRAY_A);
		$ansa=array();
		foreach ($rows as $row) {
			extract($row);
			$PN=str_replace(' ','-',$PN); // just for the hell of it
			$PN=str_replace('_','-',$PN);
			$st=explode('-',$PN);
			$CNT=0;
			foreach ($st as $sst) {
				$se=metaphone($sst);
				if (strlen($se)>1) {
					if (in_array($se,$ss)) $CNT++;
				}
			}
			if ($CNT>=$find) $ansa[$ID]=$CNT;
		}
		if (empty($ansa)) return false;
		// sort array by CNT keeping keys
		arsort($ansa);
		foreach ($ansa as $ID=>$CNT) {
			return $ID;
		}
	
		return false;
	}
	// first time look for the -key- combo with dashes. This is more exact than any old hit in the string
	$sql="SELECT ID, ";
	if ($find>count($ss)) $find=$count($ss);

	for ($j=0;$j<count($ss);$j++) {
		// CONCAT(name, ' - ', description)
		$sql=$sql." if(INSTR(CONCAT('-',LCASE(post_name),'-'),'-".mysql_real_escape_string($ss[$j])."-'),1,0)+" ;
	}
	$sql=$sql."0 as CNT FROM ".$wpdb->posts." WHERE post_status = 'publish' ORDER BY CNT DESC, post_modified DESC LIMIT 1";
	$row=$wpdb->get_row($sql);
	if ($row) {	
	   $ID=$row->ID; 
	   $CNT=$row->CNT;
		//echo "\r\n\r\n<!-- step 3c '$CNT' '$find' -->\r\n\r\n";
	   if ($CNT>=$find) return $ID;
	} 
	// try it the old way without explicit searching for the dashes, hits anywhere on any part of a word.
	$sql="SELECT ID, ";
	for ($j=0;$j<count($ss);$j++) {
		$sql=$sql." if(INSTR(LCASE(post_name),'".mysql_real_escape_string($ss[$j])."'),1,0)+" ;
	}
	$sql=$sql."0 as CNT FROM ".$wpdb->posts." WHERE post_status = 'publish' ORDER BY CNT DESC, post_modified DESC LIMIT 1";
	//echo "\r\n\r\n<!-- step 3b  - $sql - -->\r\n\r\n";
	$row=$wpdb->get_row($sql);
	if ($row) {	
	   $ID=$row->ID; 
	   $CNT=$row->CNT;
	   if ($CNT>=$find) return $ID;
	} 
	return false;
}

function kpg_pf_sitemap() {
	// get the last 20 entries in descending order and make them into a sitemap
echo '<'.'?xml version="1.0" encoding="UTF-8"?'.'>'; // because of ? and stuff need to echo this separate
echo "\r\n";
	// header goes out
	$pd=date('c',time() + ( get_option( 'gmt_offset' ) * 3600 ));
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

<url>
<loc><?php echo home_url('/'); ?></loc>
<lastmod><?php echo $pd; ?></lastmod>
<changefreq>daily</changefreq>
<priority>0.8</priority>
</url>

<?php
	global $wpdb;	
	$sql="SELECT ID FROM ".$wpdb->posts." WHERE post_status = 'publish' ORDER BY post_modified DESC LIMIT 20";
	$rows=$wpdb->get_results($sql,ARRAY_A);
	foreach ($rows as $row) {
		extract($row);
		// get the info from the ID
		$link=get_permalink($ID);
		
// body of xml
?>

<url>
<loc><?php echo $link ?></loc>
<changefreq>daily</changefreq>
<priority>0.8</priority>
</url>

<?php


// end xml		
		
		
	}
?>
</urlset>
<?php
}



// admin page
function kpg_permalink_finder_control() {
	if(!current_user_can('manage_options')) {
		die('Access Denied');
	}
	// in order save memory, we are including this file. The include is only done when the admin options are displayed
	require("includes/pf-options.php");
}


// here are the debug functions
// change the debug=false to debug=true to start debugging.
// the plugin will drop a file kpg_pf_debug_output.txt in the current directory (root, wp-admin, or network) 
// directory must be writeable or plugin will crash.

function kpg_pf_errorsonoff($old=null) {
	$debug=true;  // change to true to debug, false to stop debugging.
	if (!$debug) return;
	if (empty($old)) return set_error_handler("kpg_pf_ErrorHandler");
	restore_error_handler();
}
function kpg_pf_ErrorHandler($errno, $errmsg, $filename, $linenum, $vars) {
	// write the answers to the file
	// we are only conserned with the errors and warnings, not the notices
	//if ($errno==E_NOTICE || $errno==E_WARNING) return false;
	$serrno="";
	if ((strpos($filename,'stop-spammer-registrations')<1)&&(strpos($filename,'options-general.php')<1)) return false;
	switch ($errno) {
		case E_ERROR: 
			$serrno="Fatal run-time errors. These indicate errors that can not be recovered from, such as a memory allocation problem. Execution of the script is halted. ";
			break;
		case E_WARNING: 
			$serrno="Run-time warnings (non-fatal errors). Execution of the script is not halted. ";
			break;
		case E_NOTICE: 
			$serrno="Run-time notices. Indicate that the script encountered something that could indicate an error, but could also happen in the normal course of running a script. ";
			break;
		default;
			$serrno="Unknown Error type $errno";
	}
	if (strpos($errmsg,'modify header information')) return false;
 
	$msg="
	Error number: $errno
	Error type: $serrno
	Error Msg: $errmsg
	File name: $filename
	Line Number: $linenum
	---------------------
	";
	// write out the error
	$f=fopen(dirname(__FILE__)."/pf_debug_output.txt",'a');
	fwrite($f,$msg);
	fclose($f);
	return false;
}
function kpg_pf_really_clean($s) {
	// try to get all non 7-bit things out of the string
	// otherwise the serialize fails - this fixes failed serialize in get and set_options
	if (empty($s)) return $s;
	$ss=array_slice(unpack("c*", "\0".$s), 1);
	if (empty($ss)) return $s;
	$s='';
	for ($j=0;$j<count($ss);$j++) {
		if ($ss[$j]<127&&$ss[$j]>31) $s.=pack('C',$ss[$j]);
	}
	return $s;
}


function kpg_pf_get_options() {
	$opts=get_option('kpg_permalinfinder_options');
	if (empty($opts)||!is_array($opts)) $opts=array();
	$options=array(
		'e404'=>array(),
		'f404'=>array(),
		'find'=>'2',
		'stats'=>30,
		'labels'=>'N',
		'nobuy'=>'N',
		'chkrobots'=>'Y',
		'chkicon'=>'Y',
		'chksitemap'=>'Y',
		'chkdublin'=>'Y',
		'chkopensearch'=>'Y',
		'chkmetaphone'=>'Y',
		'chkcrossdomain'=>'Y',
		'kpg_pf_mu'=>'Y',
		'kpg_pf_short'=>'N',
		'kpg_pf_numbs'=>'N',
		'kpg_pf_common'=>'N',
		'kpg_pf_301'=>'301',
		'robots'=>"# robots.txt generated by Permalink Finder
User-agent: *
Disallow: */cgi-bin/
Disallow: */wp-admin/
Disallow: */wp-includes/
Disallow: */wp-content/plugins/
Disallow: */wp-content/cache/
Disallow: */wp-content/themes/
Disallow: */category/*/*
Disallow: */trackback/
Disallow: */feed/
Disallow: */comments/
Disallow: /*?
		"
	);
	

	$ansa=array_merge($options,$opts);
	if (!is_array($ansa['e404'])) $ansa['e404']=array();
	if (!is_numeric($ansa['find'])||$ansa['find']<0) $ansa['find']='0';
	if (!is_numeric($ansa['stats'])||$ansa['stats']<0) $ansa['stats']='30';
	if ($ansa['labels']!='Y') $ansa['labels']='N';
	if ($ansa['kpg_pf_mu']!='Y') $ansa['kpg_pf_mu']='N';
	if ($ansa['kpg_pf_short']!='Y') $ansa['kpg_pf_short']='N';
	if ($ansa['kpg_pf_numbs']!='Y') $ansa['kpg_pf_numbs']='N';
	if ($ansa['kpg_pf_common']!='Y') $ansa['kpg_pf_common']='N';
	if ($ansa['chkrobots']!='Y') $ansa['chkrobots']='N';
	if ($ansa['chkicon']!='Y') $ansa['chkicon']='N';
	if ($ansa['chkdublin']!='Y') $ansa['chkdublin']='N';
	if ($ansa['chkopensearch']!='Y') $ansa['chkopensearch']='N';
	if ($ansa['chkmetaphone']!='Y') $ansa['chkmetaphone']='N';
	if ($ansa['chkcrossdomain']!='Y') $ansa['chkcrossdomain']='N';
	if ($ansa['kpg_pf_common']!='Y') $ansa['kpg_pf_common']='N';
	if ($ansa['kpg_pf_301']!='301'&&$ansa['kpg_pf_301']!='302'&&$ansa['kpg_pf_301']!='303'&&$ansa['kpg_pf_301']!='307') 
		$ansa['kpg_pf_301']='301';

	return $ansa;
}// done
?>