<?php
/*
	WordPress 2.8 Plugin: Permalink-Finder 1.40 				
	Copyright (c) 2009 Keith P. Graham 	
 
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
$e404=array();
$f404=array();

// see if we are getting anything from the admin update post.
if(!empty($_POST['Submit'])) { // we have a post - need to change some options
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
	}
	if ($kpg_pf_index!='Y' && $kpg_pf_index!='N') $kpg_pf_index='N';
	//		kpg_pf_labels: fix up blogger labels folder 
	if (array_key_exists('kpg_pf_labels',$_POST)) {
		$kpg_pf_labels = $_POST['kpg_pf_labels'];
	}
	// labels can be Y or N
	if ($kpg_pf_labels!='Y' && $kpg_pf_labels!='N') $kpg_pf_labels='N';
	//		kpg_pf_stats: this is a radio box that indicates if the finder should be working or not default = true
	if (array_key_exists('kpg_pf_stats',$_POST) ){
		$kpg_pf_stats = $_POST['kpg_pf_stats'];
	}
	// stats van be 0,10,20, or 30
	if ($kpg_pf_stats!='10' && $kpg_pf_stats!='20' && $kpg_pf_stats!='30') {
		$kpg_pf_stats='0';
	}
	
	// out in data array
	$updateData=get_option('kpg_permalinfinder_options');
	if ($updateData==null) $updateData=array();
	if (!is_array($updateData)) $updateData=array();
	$updateData['find']=$kpg_pf_find;
	$updateData['index']=$kpg_pf_index;
	$updateData['stats']=$kpg_pf_stats;
	$updateData['labels']=$kpg_pf_labels;
	if ($kpg_pf_stats=='0') { 
		// clear out any statistics
		if (array_key_exists('f404',$updateData) ) unset($updateData['f404']);
		if (array_key_exists('e404',$updateData) ) unset($updateData['e404']);
	}
	// save the results in repository
	update_option('kpg_permalinfinder_options', $updateData);
	// done
}	
	// check for an uninstall going on - this means clean the options
	$mode = trim($_GET['mode']);
	if ($mode=='end-UNINSTALL') {
		//  Deactivating our little plugin
		// need to pull out the settings and clean up after ourselves
	
	} else {
	
	// not deactivating and we have finished with any updates or housekeeping - get the variables and show them on the form
	$updateData=get_option('kpg_permalinfinder_options');
	if ($updateData==null) $updateData=array();
	if (!is_array($updateData)) $updateData=array();
	if (array_key_exists('find',$updateData)) $kpg_pf_find=$updateData['find'];
	if (array_key_exists('index',$updateData)) $kpg_pf_index=$updateData['index'];
	if (array_key_exists('stats',$updateData)) $kpg_pf_stats=$updateData['stats'];
	if (array_key_exists('labels',$updateData)) $kpg_pf_labels=$updateData['labels'];
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
?>

<div class="wrap">
<h2>Permalink-Finder Options </h2>

<form method="post" action="">
<input type="hidden" name="action" value="update" />
<input type="hidden" name="page_options" value="Submit,kpg_pf_find,kpg_pf_index,action" />

<?php wp_nonce_field('update-options'); ?>
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
	  </select>
	</td>
	<td>Indicate how many words in the bad url must match a real permalink.<br/>
	For instance: if the mistaken link is "a-list-of-games" this will find a post called "list-of-games" or "games-list". <br/>Matching any single word might redirect to a totally unrelated post, but if you ask for 4 matches you will never be able to fix links with only three words.<br/>This is useful for people who have imported a Blogger.com FTP site into Wordpress.
	</td>
</tr>
<tr valign="middle">
	<td>Redirect index.html to your blog main page. </td>
	<td><input name="kpg_pf_index" type="checkbox" value="Y" <?php if ($kpg_pf_index=='Y') {?> checked="checked" <?php } ?>/></td>
	<td>If your have incoming links to index.html, index,htm, or index.shtml, then checking this will redirect any of these to your blog's main page and not show a 404 page not found. Useful for websites that previously had an index page.
	</td>
</tr>
<tr valign="middle">
	<td>Fix Blogger Labels </td>
	<td><input name="kpg_pf_labels" type="checkbox" value="Y" <?php if ($kpg_pf_labels=='Y') {?> checked="checked" <?php } ?>/></td>
	<td>Blogger.com uses the url &quot;/labels/&quot; folder instead of categories. If you have imported your site from Blogger.com, you can check off this option to automatically redirect links from /labels/string to /category/string. </td>
</tr>
<tr>
<td>Track 404 and redirects</td>
<td><select name="kpg_pf_stats">
    <option value="0" <?php if ($kpg_pf_stats=='0') {?> selected="selected" <?php } ?>>Disabled</option>
    <option value="10" <?php if ($kpg_pf_stats=='10') {?> selected="selected" <?php } ?>>Last 10</option>
    <option value="20" <?php if ($kpg_pf_stats=='20') {?> selected="selected" <?php } ?>>Last 20</option>
    <option value="30" <?php if ($kpg_pf_stats=='30') {?> selected="selected" <?php } ?>>Last 30</option>
</select>
</td>
	<td>As long as we are looking at 404&apos;s and trying to redirect them we might as well keep track of the last few hits and what happened to them. You can keep up to 30 of the last hits in memory. (If you set this to zero you will lose any statistics that have been recorded.)
	</td>
</tr>
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
<td style="background-color:#FFFFFF"><a href="<?php echo $f404[$j][1]; ?>" title="<?php echo $f404[$j][1]; ?>" target="_blank"><?php echo $f1; ?></td>
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
<h3>If you like this plugin, why not try out these other interesting plugins.</h3>
<?php
// list of plugins
$p=array(
"facebook-open-graph-widget"=>"The easiest way to add a Facebook Like buttons to your blog' sidebar",
"threat-scan-plugin"=>"Check your blog for virus, trojans, malicious software and other threats",
"open-in-new-window-plugin"=>"Keep your surfers. Open all external links in a new window",
"youtube-poster-plugin"=>"Automagically add YouTube videos as posts. All from inside the plugin. Painless, no heavy lifting.",
"permalink-finder"=>"Never get a 404 again. If you have restructured or moved your blog, this plugin will find the right post or page every time",
);
  $f=$_SERVER["REQUEST_URI"];
  // get the php out
  $ff=explode('page=',$f);
  $f=$ff[1];
  $ff=explode('/',$f);
  $f=$ff[0];
  foreach ($p as $key=>$data) {
	if ($f!=$key) { 
	$kk=urlencode($key);
		?><p>&bull;<span style="font-weight:bold;"> <?PHP echo $key ?>: </span> <a href="plugin-install.php?tab=plugin-information&plugin=<?PHP echo $kk ?>&TB_iframe=true&width=640&height=669">Install Plugin</a> - <span style="font-style:italic;font-weight:bold;"><?PHP echo $data ?></span></p><?PHP 
	}
  }
?>


<hr/>
<p>This plugin is free and I expect nothing in return. 
However, a link on your blog to one of my personal sites would be appreciated.</p>
</p>
<p>Keith Graham</p>
<p>
	<a href="http://www.cthreepo.com/blog" target="_blank">Wandering Blog </a>(My personal Blog) <br />
	<a href="http://www.cthreepo.com" target="_blank">Resources for Science Fiction</a> (Writing Science Fiction) <br />
	<a href="http://www.jt30.com" target="_blank">The JT30 Page</a> (Amplified Blues Harmonica) <br />
	<a href="http://www.harpamps.com" target="_blank">Harp Amps</a> (Vacuum Tube Amplifiers for Blues) <br />
	<a href="http://www.blogseye.com" target="_blank">Blog's Eye</a> (PHP coding) <br />
	<a href="http://www.cthreepo.com/bees" target="_blank">Bee Progress Beekeeping Blog</a> (My adventures as a new beekeeper) </p>
</div>


<?php 

} // end if end-install else
?>
<p>&nbsp;</p>

</p>
