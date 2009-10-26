<?php
/*
	WordPress 2.8 Plugin: Permalink-Finder 1.1 				
	Copyright (c) 2009 Keith P. Graham 	
 
	File Information:  					
	- Permalink-Finder				
	- wp-content/plugins/permalink-pinder/permalink-finder-options.php 	
 
*/


// just a quick check to keep out the riff-raff
if(!current_user_can('manage_options')) {
	die('Access Denied');
}

$kpg_pf_find='2';
$kpg_pf_index='Y';

// see if we are getting anything from the admin update post.
if(!empty($_POST['Submit'])) { // we have a post - need to change some options
	// data:
	//		kpg_pf_find: this is a radio box that indicates if the finder should be working or not default = true
	//		kpg_pf_index: fix up incoming hits with index.html 
	$kpg_pf_find = $_POST['kpg_pf_find'];
	if ($kpg_pf_find==null) $kpg_pf_find='2';
	if ($kpg_pf_find!='9999' && $kpg_pf_find!='1' && $kpg_pf_find!='2' && $kpg_pf_find!='3' && $kpg_pf_find!='4') {
		$kpg_pf_find='2';
	}
	$kpg_pf_index = $_POST['kpg_pf_index'];
	if ($kpg_pf_index==null) $kpg_pf_index='';
	if ($kpg_pf_index!='') $kpg_pf_index='Y';
	
	// out in data array
	$updateData = array();
	$updateData['find']=$kpg_pf_find;
	$updateData['index']=$kpg_pf_index;
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
	$kpg_pf_find=$updateData['find'];
	$kpg_pf_index=$updateData['index'];
	// check data and set defaults
	if ($kpg_pf_find==null) $kpg_pf_find='2';
	if ($kpg_pf_find!='9999' && $kpg_pf_find!='1' && $kpg_pf_find!='2' && $kpg_pf_find!='3' && $kpg_pf_find!='4') {
		$kpg_pf_find='2';
	}
	if ($kpg_pf_index==null) $kpg_pf_index='';
	if ($kpg_pf_index!='') $kpg_pf_index='Y';
?>

<div class="wrap">
<h2>Permalink-Finder Options </h2>

<form method="post" action="">
<input type="hidden" name="action" value="update" />
<input type="hidden" name="page_options" value="Submit,kpg_pf_find,kpg_pf_index" />

<?php wp_nonce_field('update-options'); ?>
<table class="form-table">
<tr valign="middle">
<th scope="row">Finding Permalinks </th>
<td> 
  <select name="kpg_pf_find">
    <option value="9999" <?php if ($kpg_pf_find=='9999') {?> selected="selected" <?php } ?>>Disabled</option>
    <option value="1" <?php if ($kpg_pf_find=='1') {?> selected="selected" <?php } ?>>any single word match</option>
    <option value="2" <?php if ($kpg_pf_find=='2') {?> selected="selected" <?php } ?>>at least 2 words match (recomended)</option>
    <option value="3" <?php if ($kpg_pf_find=='3') {?> selected="selected" <?php } ?>>at least 3 words match</option>
    <option value="4" <?php if ($kpg_pf_find=='4') {?> selected="selected" <?php } ?>>at least 4 words match</option>
  </select></td>
<td>Indicate how many words in the bad url must match a real permalink.<br/>
For instance: if the mistaken link is "a-list-of-games" this will find a post called "list-of-games" or "games-list". <br/>Matching any single word might redirect to a totally unrelated post, but if you ask for 4 matches you will never be able to fix links with only three words.<br/>This is useful for people who have imported a Blogger.com FTP site into Wordpress.</tr>


<tr valign="middle">
<th scope="row">Redirect index.html to your blog main page. </th>
<td><input name="kpg_pf_index" type="checkbox" value="Y" <?php if ($kpg_pf_index=='Y') {?> checked <?php } ?>/></td>
<td>If your have incoming links to index.html, index,htm, or index.shtml, then checking this will redirect any of these to your blog's main page and not show a 404 page not found. Useful for websites that previously had an index page.
</tr>
</table>

<p class="submit">
<input type="submit" name="Submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
</p>


</form>
Version 1.1 October 26, 2009 </div>

<p>
  <?php } ?>
</p>
<p>&nbsp;</p>

</p>
