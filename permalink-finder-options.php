<?php
/*
	WordPress 3.1 Plugin: Permalink-Finder 1.70 				
	Copyright (c) 2011 Keith P. Graham 	
 
	File Information:  					
	- Permalink-Finder				
	- wp-content/plugins/permalink-finder/permalink-finder-options.php 	
 
*/

// just a quick check to keep out the riff-raff
if(!current_user_can('manage_options')) {
	die('Access Denied');
}
// defaults if value on update is null
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
// get the options file
$options=get_option('kpg_permalinfinder_options');
if (empty($options)) $options=array();
if (!is_array($options)) $options=array();
// set all the params
if (array_key_exists('find',$options)) $kpg_pf_find=$options['find'];
if (array_key_exists('index',$options)) $kpg_pf_index=$options['index'];
if (array_key_exists('stats',$options)) $kpg_pf_stats=$options['stats'];
if (array_key_exists('labels',$options)) $kpg_pf_labels=$options['labels'];
if (array_key_exists('mu',$options)) $kpg_pf_mu=$options['mu'];
if (array_key_exists('kpg_pf_short',$options) ) $kpg_pf_short=$options['kpg_pf_short'];
if (array_key_exists('kpg_pf_numbs',$options) ) $kpg_pf_numbs=$options['kpg_pf_numbs'];
if (array_key_exists('kpg_pf_common',$options) ) $kpg_pf_common=$options['kpg_pf_common'];
if ($kpg_pf_index!='Y' && $kpg_pf_index!='N') $kpg_pf_index='N';
if ($kpg_pf_labels!='Y' && $kpg_pf_labels!='N') $kpg_pf_labels='N';
if ($kpg_pf_short!='Y' && $kpg_pf_short!='N') $kpg_pf_short='N';
if ($kpg_pf_common!='Y' && $kpg_pf_common!='N') $kpg_pf_common='N';
if ($kpg_pf_numbs!='Y' && $kpg_pf_numbs!='N') $kpg_pf_numbs='N';
if ($kpg_pf_stats!='10' && $kpg_pf_stats!='20' && $kpg_pf_stats!='30') {
	$kpg_pf_stats='0';
}
// history files
if (array_key_exists('e404',$options) ) $e404=$options['e404']; 
if (array_key_exists('f404',$options) ) $f404=$options['f404']; 
global $blog_id;

// see if we are getting anything from the admin update post.
if(!empty($_POST['Submit'])&&wp_verify_nonce($_POST['kpg_pf_nonce'],'kpg_pf') ) {
	// data:
	//		kpg_pf_find: this is a radio box that indicates if the finder should be working or not default = true
	if (array_key_exists('kpg_pf_find',$_POST) ){
		$kpg_pf_find = $_POST['kpg_pf_find'];
	}
	// kpg_pf_find is 999,1,2,3, or 4
	if ($kpg_pf_find!='9999' && $kpg_pf_find!='1' && $kpg_pf_find!='2' && $kpg_pf_find!='3' && $kpg_pf_find!='4') {
		$kpg_pf_find='2';
	}
	//		kpg_pf_index: fix up incoming hits with index.html 
	if (array_key_exists('kpg_pf_index',$_POST) ){
		$kpg_pf_index = $_POST['kpg_pf_index'];
	} else {
		$kpg_pf_index='N';
	}

	if ($kpg_pf_index!='Y' && $kpg_pf_index!='N') $kpg_pf_index='N';
	//		kpg_pf_labels: fix up blogger labels folder 
	if (array_key_exists('kpg_pf_labels',$_POST)) {
		$kpg_pf_labels = $_POST['kpg_pf_labels'];
	} else {
		$kpg_pf_labels='N';
	}

	// labels can be Y or N
	if ($kpg_pf_labels!='Y' && $kpg_pf_labels!='N') $kpg_pf_labels='N';
	// numbers, common and short options
	
	
	if (array_key_exists('kpg_pf_common',$_POST)) {
		$kpg_pf_common = $_POST['kpg_pf_common'];
	} else {
		$kpg_pf_common='N';
	}
	if ($kpg_pf_common!='Y' && $kpg_pf_common!='N') $kpg_pf_common='N';
	
	if (array_key_exists('kpg_pf_short',$_POST)) {
		$kpg_pf_short = $_POST['kpg_pf_short'];
	} else {
		$kpg_pf_short='N';
	}
	if ($kpg_pf_short!='Y' && $kpg_pf_short!='N') $kpg_pf_short='N';
	if (array_key_exists('kpg_pf_numbs',$_POST)) {
		$kpg_pf_numbs = $_POST['kpg_pf_numbs'];
	} else {
		$kpg_pf_numbs='N';
	}
	if ($kpg_pf_numbs!='Y' && $kpg_pf_numbs!='N') $kpg_pf_numbs='N';
	
	//		kpg_pf_stats: this is a radio box that indicates if the finder should be working or not default = true
	if (array_key_exists('kpg_pf_stats',$_POST) ){
		$kpg_pf_stats = $_POST['kpg_pf_stats'];
	} 
	// stats van be 0,10,20, or 30
	if ($kpg_pf_stats!='10' && $kpg_pf_stats!='20' && $kpg_pf_stats!='30') {
		$kpg_pf_stats='0';
	}
	// mu function can only be set on main page
	if (function_exists('switch_to_blog')) {
		if ($blog_id==1) {
			if (array_key_exists('kpg_pf_mu',$_POST)) {
				$kpg_pf_mu = $_POST['kpg_pf_mu'];
			} else {
				$kpg_pf_mu='N';
			}
		} 
	} 
	// labels can be Y or N
	if ($kpg_pf_mu!='Y' && $kpg_pf_mu!='N') $kpg_pf_mu='N';
	
	// out in data array
	$options['find']=$kpg_pf_find;
	$options['index']=$kpg_pf_index;
	$options['stats']=$kpg_pf_stats;
	$options['labels']=$kpg_pf_labels;
	$options['kpg_pf_common']=$kpg_pf_common;
	$options['kpg_pf_short']=$kpg_pf_short;
	$options['kpg_pf_numbs']=$kpg_pf_numbs;
	$options['mu']=$kpg_pf_mu;
	if ($kpg_pf_stats=='0') { 
		// clear out any statistics
		if (array_key_exists('f404',$options) ) unset($options['f404']);
		if (array_key_exists('e404',$options) ) unset($options['e404']);
		$e404=array();
		$f404=array();
	}
	// save the results in repository
	update_option('kpg_permalinfinder_options', $options);
	// done
}	
?>

<div class="wrap">
<h2>Permalink-Finder Options </h2>

<form method="post" action="">
<input type="hidden" name="action" value="update" />
<input type="hidden" name="page_options" value="Submit,kpg_pf_find,kpg_pf_index,action" />

	<?php wp_nonce_field( 'kpg_pf', 'kpg_pf_nonce' ) ?> 
<table class="form-table">
<tr valign="middle">
<td>Finding Permalinks </td>
	<td> 
	  <select name="kpg_pf_find">
		<option value="9999" <?php if ($kpg_pf_find=='9999') {?> selected="selected" <?php } ?>>Disabled</option>
		<option value="1" <?php if ($kpg_pf_find=='1') {?> selected="selected" <?php } ?>>any single word match</option>
		<option value="2" <?php if ($kpg_pf_find=='2') {?> selected="selected" <?php } ?>>at least 2 words match (recomended)</option>
		<option value="3" <?php if ($kpg_pf_find=='3') {?> selected="selected" <?php } ?>>at least 3 words match</option>
		<option value="4" <?php if ($kpg_pf_find=='4') {?> selected="selected" <?php } ?>>at least 4 words match</option>
	  </select>	</td>
	<td>Indicate how many words in the bad url must match a real permalink.<br/>
	For instance: if the mistaken link is "a-list-of-games" this will find a post called "list-of-games" or "games-list". <br/>Matching any single word might redirect to a totally unrelated post, but if you ask for 4 matches you will never be able to fix links with only three words.<br/>This is useful for people who have imported a Blogger.com FTP site into Wordpress.	</td>
</tr>
<tr valign="middle">
	<td>Redirect index.html to your blog main page. </td>
	<td><input name="kpg_pf_index" type="checkbox" value="Y" <?php if ($kpg_pf_index=='Y') {?> checked="checked" <?php } ?>/></td>
	<td>If your have incoming links to index.html, index,htm, or index.shtml, then checking this will redirect any of these to your blog's main page and not show a 404 page not found. Useful for websites that previously had an index page.	</td>
</tr>
<tr valign="middle">
	<td>Fix Blogger Labels </td>
	<td><input name="kpg_pf_labels" type="checkbox" value="Y" <?php if ($kpg_pf_labels=='Y') {?> checked="checked" <?php } ?>/></td>
	<td>Blogger.com uses the url &quot;/labels/&quot; folder instead of categories. If you have imported your site from Blogger.com, you can check off this option to automatically redirect links from /labels/string to /category/string. </td>
</tr>

<tr valign="middle">
	<td>Don&apos;t use Common words</td>
	<td><input name="kpg_pf_common" type="checkbox" value="Y" <?php if ($kpg_pf_common=='Y') {?> checked="checked" <?php } ?>/></td>
	<td>common words such as &quot;the&quot;, &quot;fix&quot;, &quot;why&quot;, &quot;could&quot;, &quot;not&quot;, can screw up the accuracy of the search for the right slug. Try checking this box to get more accuracy. If you get too many 404s uncheck it
	</td>
</tr>
<tr valign="middle">
	<td>Don&apos;t use short words</td>
	<td><input name="kpg_pf_short" type="checkbox" value="Y" <?php if ($kpg_pf_short=='Y') {?> checked="checked" <?php } ?>/></td>
	<td>Words that are one or two letters long can interfere with accuracy. By checking this, the search for a permalink will not
	use words like &quot;a&quot;, &quot;an&quot;,&quot;to&quot;,&quot;I&quot;,&quot;it&quot;, increasing accuracy.</td>
</tr>
<tr valign="middle">
	<td>Don&apos;t use numbers</td>
	<td><input name="kpg_pf_numbs" type="checkbox" value="Y" <?php if ($kpg_pf_numbs=='Y') {?> checked="checked" <?php } ?>/></td>
	<td>Numbers can confuse the search for a permalink. the number 11 will find 911 and 2011, not just 11. Check this if you accuracy is being hurt by numbers.</td>
</tr>





<tr>
<td>Track 404 and redirects</td>
<td><select name="kpg_pf_stats">
    <option value="0" <?php if ($kpg_pf_stats=='0') {?> selected="selected" <?php } ?>>Disabled</option>
    <option value="10" <?php if ($kpg_pf_stats=='10') {?> selected="selected" <?php } ?>>Last 10</option>
    <option value="20" <?php if ($kpg_pf_stats=='20') {?> selected="selected" <?php } ?>>Last 20</option>
    <option value="30" <?php if ($kpg_pf_stats=='30') {?> selected="selected" <?php } ?>>Last 30</option>
</select></td>
	<td>As long as we are looking at 404&apos;s and trying to redirect them we might as well keep track of the last few hits and what happened to them. You can keep up to 30 of the last hits in memory. (If you set this to zero you will lose any statistics that have been recorded.)	</td>
</tr>
<?php
	if (function_exists('switch_to_blog')) {
		if ($blog_id==1) {
?>
<tr>
  <td>Only show configuration on main blog (MU Only) </td>
  <td><input name="kpg_pf_mu" type="checkbox" value="Y" <?php if ($kpg_pf_mu=='Y') {?> checked="checked" <?php } ?>/></td>
  <td>This option lnly applies to MU blogs. If this plugin is installed for all blogs on the network, then only the Blog #1 can see and change these options. Statistics can only be read on the main blog. </td>
</tr>
<?php
	}
}
?>
</table>
<p class="submit">
<input type="submit" name="Submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
</p>
</form>
<?php 
	if ($kpg_pf_stats>0) {
		if (count($f404)>0) {
?>
<h3 align="center">Fixed Permalinks</h3>
<table align="center" cellspacing="2" style="background-color:#CCCCCC;">
<tr>
<td style="background-color:#FFFFFF">Date/Time</td>
<td style="background-color:#FFFFFF">Requested Page</td>
<td style="background-color:#FFFFFF">Fixed Permalink</td>
<td style="background-color:#FFFFFF">Referring Page</td>
<td style="background-color:#FFFFFF">Browser User Agent</td>
<td style="background-color:#FFFFFF">Remote IP</td>

</tr>
<?php
for ($j=0;$j<count($f404)&&$j<$kpg_pf_stats;$j++ ) {
    $f404[$j][1]=urldecode($f404[$j][1]);
    $f404[$j][5]=urldecode($f404[$j][5]);
    $f404[$j][2]=urldecode($f404[$j][2]);
    $f1=$f404[$j][1];
    $f5=$f404[$j][5];
    $f2=$f404[$j][2];
	if (strlen($f1)>32) $f1=substr($f1,0, 32).'...';
	if (strlen($f5)>32) $f5=substr($f5,0,32).'...';
	if (strlen($f2)>32) $f2=substr($f2,0,32).'...';
?>
<tr>
<td style="background-color:#FFFFFF"><?php echo $f404[$j][0]; ?></td>
<td style="background-color:#FFFFFF"><a href="<?php echo $f404[$j][1]; ?>" title="<?php echo $f404[$j][1]; ?>" target="_blank"><?php echo $f1; ?></a></td>
<td style="background-color:#FFFFFF"><a href="<?php echo $f404[$j][5]; ?>" title="<?php echo $f404[$j][5]; ?>" target="_blank"><?php echo $f5; ?></a></td>
<td style="background-color:#FFFFFF"><a href="<?php echo $f404[$j][2]; ?>" title="<?php echo $f404[$j][2]; ?>" target="_blank"><?php echo $f2; ?></a></td>
<td style="background-color:#FFFFFF"><?php echo $f404[$j][3]; ?></td>
<td style="background-color:#FFFFFF"><?php echo $f404[$j][4]; ?></td>
</tr>
<?php } ?>
</table>
<?php } ?>
<?php 
	if (count($e404)>0) {
?>
<h3 align="center">404 errors</h3>
<table align="center" cellspacing="2" style="background-color:#CCCCCC;">
<tr>
<td style="background-color:#FFFFFF">Date/Time</td>
<td style="background-color:#FFFFFF">Requested Page</td>
<td style="background-color:#FFFFFF">Referring Page</td>
<td style="background-color:#FFFFFF">Browser User Agent</td>
<td style="background-color:#FFFFFF">Remote IP</td>
</tr>
<?php
for ($j=0;$j<count($e404)&&$j<$kpg_pf_stats;$j++ ) {
    $e404[$j][1]=urldecode($e404[$j][1]);
    $e404[$j][2]=urldecode($e404[$j][2]);
    $f1=$e404[$j][1];
    $f2=$e404[$j][2];
	if (strlen($f1)>32) $f1=substr($f1,0, 32).'...';
	if (strlen($f2)>32) $f2=substr($f2,0,32).'...';
?>
<tr>
<td style="background-color:#FFFFFF"><?php echo $e404[$j][0]; ?></td>
<td style="background-color:#FFFFFF"><a href="<?php echo $e404[$j][1]; ?>" title="<?php echo $e404[$j][1]; ?>" target="_blank"><?php echo $f1; ?></td>
<td style="background-color:#FFFFFF"><a href="<?php echo $e404[$j][2]; ?>" title="<?php echo $e404[$j][2]; ?>" target="_blank"><?php echo $f2; ?></a></td>
<td style="background-color:#FFFFFF"><?php echo $e404[$j][3]; ?></td>
<td style="background-color:#FFFFFF"><?php echo $e404[$j][4]; ?></td>
</tr>
<?php } ?>
</table>

  <?php
		}
	} // end if kpg_pf_stats>0
?>
  <br/>


<hr/>
<p>Keith Graham is also the Author a collection of Science Fiction Stories. If you find this plugin useful, you might like to to <strong>Buy the Book</strong>.</p>
<p><a href="http://www.amazon.com/gp/product/1456336584?ie=UTF8&tag=thenewjt30page&linkCode=as2&camp=1789&creative=390957&creativeASIN=1456336584">Error Message Eyes: A Programmer's Guide to the Digital Soul</a><img src="http://www.assoc-amazon.com/e/ir?t=thenewjt30page&l=as2&o=1&a=1456336584" width="1" height="1" border="0" alt="" style="border:none !important; margin:0px !important;" />
</p>
<p>This plugin is free and I expect nothing in return. 
  A link on your blog to one of my personal sites would be appreciated.</p>
<p>Keith Graham</p>
<p>
	<a href="http://www.blogseye.com" target="_blank">Blog&apos;s Eye</a> (My Wordpress Plugins and other PHP coding projects) <br />
<a href="http://www.cthreepo.com/blog" target="_blank">Wandering Blog </a>(My personal Blog) <br />
	<a href="http://www.cthreepo.com" target="_blank">Resources for Science Fiction</a> (Writing Science Fiction) <br />
	<a href="http://www.jt30.com" target="_blank">The JT30 Page</a> (Amplified Blues Harmonica) <br />
	<a href="http://www.harpamps.com" target="_blank">Harp Amps</a> (Vacuum Tube Amplifiers for Blues) <br />
	<a href="http://www.cthreepo.com/bees" target="_blank">Bee Progress Beekeeping Blog</a> (My adventures as a new beekeeper) </p>
</div>

<p>&nbsp;</p>

</p>
