<?php
/*
	Permalink Finder Plugin 
	Options Setup Page
*/
	if(!current_user_can('manage_options')) {
		die('Access Denied');
	}
	$options=kpg_pf_get_options();
	extract($options);

	$nonce='';	
	
	if (array_key_exists('kpg_pf_control',$_POST)) $nonce=$_POST['kpg_pf_control'];
	if (array_key_exists('action1',$_POST)&&wp_verify_nonce($nonce,'kpg_pf_update')) {
		// clear the fixed
		$f404=array();
		$options['f404']=$f404;
		update_option('kpg_permalinfinder_options',$options);
		echo "<h2>Fixed Permalinks Cleared</h2>";
	} 
	if (array_key_exists('action2',$_POST)&&wp_verify_nonce($nonce,'kpg_pf_update')) {
		// clear the errors
		$e404=array();
		$options['e404']=$e404;
		update_option('kpg_permalinfinder_options',$options);
		echo "<h2>404 Errors Cleared</h2>";
	} 
	if (array_key_exists('action',$_POST)&&wp_verify_nonce($nonce,'kpg_pf_update')) { 
		if (array_key_exists('find',$_POST)) {
			$find=stripslashes($_POST['find']);
		} else {
			$find='2';
		}
		$options['find']=$find;
					
					
		if (array_key_exists('labels',$_POST)) {
			$labels=stripslashes($_POST['labels']);
		} else {
			$labels='N';
		}
		$options['labels']=$labels;
		
		
		if (array_key_exists('stats',$_POST)) {
			$stats=stripslashes($_POST['stats']);
		} else {
			$stats=30;
		}
		$options['stats']=$stats;
		
		if (array_key_exists('kpg_pf_short',$_POST)) {
			$kpg_pf_short=stripslashes($_POST['kpg_pf_short']);
		} else {
			$kpg_pf_short='N';
		}
		$options['kpg_pf_short']=$kpg_pf_short;
		
		if (array_key_exists('kpg_pf_numbs',$_POST)) {
			$kpg_pf_numbs=stripslashes($_POST['kpg_pf_numbs']);
		} else {
			$kpg_pf_numbs='Y';
		}
		$options['kpg_pf_numbs']=$kpg_pf_numbs;
		
		if (array_key_exists('kpg_pf_common',$_POST)) {
			$kpg_pf_common=stripslashes($_POST['kpg_pf_common']);
		} else {
			$kpg_pf_common='N';
		}
		$options['kpg_pf_common']=$kpg_pf_common;
		
		if (array_key_exists('kpg_pf_301',$_POST)) {
			$kpg_pf_301=stripslashes($_POST['kpg_pf_301']);
		} else {
			$kpg_pf_301='301';
		}
		$options['kpg_pf_301']=$kpg_pf_301;
		
		if (array_key_exists('kpg_pf_mu',$_POST)) {
			$kpg_pf_mu=stripslashes($_POST['kpg_pf_mu']);
		} else {
			$kpg_pf_mu='N';
		}
		$options['kpg_pf_mu']=$kpg_pf_mu;
		
		if (array_key_exists('chkdublin',$_POST)) {
			$chkdublin=stripslashes($_POST['chkdublin']);
		} else {
			$chkdublin='N';
		}
		$options['chkdublin']=$chkdublin;
		
		if (array_key_exists('chkopensearch',$_POST)) {
			$chkopensearch=stripslashes($_POST['chkopensearch']);
		} else {
			$chkopensearch='N';
		}
		$options['chkopensearch']=$chkopensearch;
		
		if (array_key_exists('chkcrossdomain',$_POST)) {
			$chkcrossdomain=stripslashes($_POST['chkcrossdomain']);
		} else {
			$chkcrossdomain='N';
		}
		$options['chkcrossdomain']=$chkcrossdomain;
		
		if (array_key_exists('chkrobots',$_POST)) {
			$chkrobots=stripslashes($_POST['chkrobots']);
		} else {
			$chkrobots='N';
		}
		$options['chkrobots']=$chkrobots;
		
		if (array_key_exists('chkicon',$_POST)) {
			$chkicon=stripslashes($_POST['chkicon']);
		} else {
			$chkicon='N';
		}
		$options['chkicon']=$chkicon;
		
		if (array_key_exists('chksitemap',$_POST)) {
			$chksitemap=stripslashes($_POST['chksitemap']);
		} else {
			$chksitemap='N';
		}
		$options['chksitemap']=$chksitemap;
		
		if (array_key_exists('robots',$_POST)) {
			$robots=stripslashes($_POST['robots']);
		} else {
			$robots='';
		}
		$options['robots']=trim($robots);
		
		if (array_key_exists('nobuy',$_POST)) {
			$nobuy=stripslashes($_POST['nobuy']);
		} else {
			$nobuy='N';
		}
		if ($nobuy!='Y') $nobuy='N';
		$options['nobuy']=$nobuy;
		
		if (array_key_exists('chkmetaphone',$_POST)) {
			$chkmetaphone=stripslashes($_POST['chkmetaphone']);
		} else {
			$chkmetaphone='N';
		}
		if ($chkmetaphone!='Y') $chkmetaphone='N';
		$options['chkmetaphone']=$chkmetaphone;
		
		
		if (function_exists('is_multisite') && is_multisite() 
				&& function_exists('kpg_pf_global_unsetup') && function_exists('kpg_pf_global_setup')) {
			if ($kpg_pf_mu=='N') {
				kpg_pf_global_unsetup();
			} else {
				kpg_pf_global_setup();
			}
		}			
		update_option('kpg_permalinfinder_options',$options);
		echo "<h2>Options Updated</h2>";

		$options=kpg_pf_get_options();
		extract($options);
	}
?>

<div class="wrap">
  <h2>Permalink-Finder Options</h2>
  <h3>Version 2.0</h3>
  <?php
	if ($nobuy!='Y') {
?>
  <div style="width:60%;background-color:ivory;border:#333333 medium groove;padding:4px;margin-left:4px;margin-left:auto;margin-right:auto;">
    <p>This plugin is free and I expect nothing in return. If you would like to support my programming, you can buy my book of short stories.</p>
    <p>Some plugin authors ask for a donation. I ask you to spend a very small amount for something that you will enjoy. eBook versions for the Kindle and other book readers start at 99&cent;. The book is much better than you might think, and it has some very good science fiction writers saying some very nice things. <br/>
      <a target="_blank" href="http://www.blogseye.com/buy-the-book/">Error Message Eyes: A Programmer's Guide to the Digital Soul</a></p>
    <p>A link on your blog to one of my personal sites would also be appreciated.</p>
    <p><a target="_blank" href="http://www.WestNyackHoney.com">West Nyack Honey</a> (I keep bees and sell the honey)<br />
      <a target="_blank" href="http://www.cthreepo.com/blog">Wandering Blog</a> (My personal Blog) <br />
      <a target="_blank" href="http://www.cthreepo.com">Resources for Science Fiction</a> (Writing Science Fiction) <br />
      <a target="_blank" href="http://www.jt30.com">The JT30 Page</a> (Amplified Blues Harmonica) <br />
      <a target="_blank" href="http://www.harpamps.com">Harp Amps</a> (Vacuum Tube Amplifiers for Blues) <br />
      <a target="_blank" href="http://www.blogseye.com">Blog&apos;s Eye</a> (PHP coding) <br />
      <a target="_blank" href="http://www.cthreepo.com/bees">Bee Progress Beekeeping Blog</a> (My adventures as a new beekeeper) </p>
  </div>
  <?php
	}
	
	   $nonce=wp_create_nonce('kpg_pf_update');
 
?>
  <p style="font-weight:bold;">The Permalink-Finder Plugin is installed and working correctly.</p>
  <p style="font-weight:bold;">Version 2.0 <a href="#stats" onclick="window.location.href=window.location.href;">Refresh</a></p>
  <hr/>
  <h4>For questions and support please check my website <a href="http://www.blogseye.com/i-make-plugins/permalink-finder-plugin/">BlogsEye.com</a>.</h4>
  <form method="post" action="">
    <input type="hidden" name="action" value="update" />
    <input type="hidden" name="kpg_pf_control" value="<?php echo $nonce;?>" />
    <?php
		if (function_exists('is_multisite') && is_multisite()) {
	?>

    <h3>Network Blog Option:</h3>
    <table align="center" cellspacing="2" style="background-color:#CCCCCC;font-size:.9em;">
      <tr bgcolor="white">
        <td width="20%" valign="top"><strong>Select how you want to control options in a networked blog environment:&nbsp;</strong></td>
        <td valign="top"> Networked ON:
          <input name="kpg_pf_mu" type="radio" value='Y'  <?php if ($kpg_pf_mu=='Y') echo "checked=\"true\""; ?> />
          <br/>
          Networked OFF:
          <input name="kpg_pf_mu" type="radio" value='N' <?php if ($kpg_pf_mu!='Y') echo "checked=\"true\""; ?>  />
        </td>
        <td valign="top"> If you are running WPMU and want to control all options and logs through the main log admin panel, select on. If you select OFF, each blog will have to configure the plugin separately. </td>
      </tr>
    </table>
 
    <br/>
    <?php
		}
	?>

    <h3>Permalink Finder Options:</h3>
    <p>You can control how the Permalink Finder finds the correct match when a 404 occurs.</p>
    <table align="center" cellspacing="2" style="background-color:#CCCCCC;font-size:.9em;">
      <tr bgcolor="white">
        <td width="20%" valign="top"><strong>Finding Permalinks:&nbsp;</strong></td>
        <td valign="top"><select name="find">
            <option value="9999" <?php if ($find=='9999') {?> selected="selected" <?php } ?>>Disabled</option>
            <option value="1" <?php if ($find=='1') {?> selected="selected" <?php } ?>>any single word match</option>
            <option value="2" <?php if ($find=='2') {?> selected="selected" <?php } ?>>at least 2 words match (recommended)</option>
            <option value="3" <?php if ($find=='3') {?> selected="selected" <?php } ?>>at least 3 words match</option>
            <option value="4" <?php if ($find=='4') {?> selected="selected" <?php } ?>>at least 4 words match</option>
          </select>
        </td>
        <td valign="top"> Indicate how many words in the bad url must match a real permalink. 
          For instance: if the mistaken link is "a-list-of-games" this will find a post called "list-of-games" or "games-list". <br/>
          Matching any single word might redirect to a totally unrelated post, but if you ask for 4 matches you will never be able to fix links with only three words. </td>
      </tr>
      <tr bgcolor="white">
        <td width="20%" valign="top"><strong>Redirect Status Code:&nbsp;</strong></td>
        <td valign="top"><select name="kpg_pf_301">
            <option value="301" <?php if ($kpg_pf_301=='301') {?> selected="selected" <?php } ?>>301 moved permanently</option>
            <option value="302" <?php if ($kpg_pf_301=='302') {?> selected="selected" <?php } ?>>302 found (originally temporary redirect)</option>
            <option value="303" <?php if ($kpg_pf_301=='303') {?> selected="selected" <?php } ?>>303 see other</option>
            <option value="307" <?php if ($kpg_pf_301=='307') {?> selected="selected" <?php } ?>>307 temporary redirect</option>
          </select></td>
        <td valign="top"> Status code returned with the redirect URL.<br/>
          Usually this is 301. This will tell search engines to update their indexes. Use 302 or 307 if you don't want the new page in the search engines just now, but still want to send this user to a new page. Use 303 to indicate that the page is redirecting to another script to finish processing, but keep using the original url.</td>
      </tr>
     <tr bgcolor="white">
        <td width="20%" valign="top"><strong>Fix Blogger Labels:&nbsp;</strong></td>
        <td valign="top"><input name="labels" type="checkbox" value="Y" <?php if ($labels=='Y') {?> checked="checked" <?php } ?>/>
        </td>
        <td valign="top"> Blogger.com uses the url &quot;/labels/&quot; folder instead of categories. If you have imported your site from Blogger.com, you can check off this option to automatically redirect links from /labels/string to /category/string. </td>
      </tr>
      <tr bgcolor="white">
        <td width="20%" valign="top"><strong>Don&apos;t use Common words:&nbsp;</strong></td>
        <td valign="top"><input name="kpg_pf_common" type="checkbox" value="Y" <?php if ($kpg_pf_common=='Y') {?> checked="checked" <?php } ?>/>
        </td>
        <td valign="top"> common words such as &quot;the&quot;, &quot;fix&quot;, &quot;why&quot;, &quot;could&quot;, &quot;not&quot;, can screw up the accuracy of the search for the right slug. Try checking this box to get more accuracy. If you get too many 404s uncheck it 
	</td>
      </tr>
      <tr bgcolor="white">
        <td width="20%" valign="top"><strong>Don&apos;t use short words:&nbsp;</strong></td>
        <td valign="top"><input name="kpg_pf_short" type="checkbox" value="Y" <?php if ($kpg_pf_short=='Y') {?> checked="checked" <?php } ?>/>
        </td>
        <td valign="top"> Words that are one or two letters long can interfere with accuracy. By checking this, the search for a permalink will not
          use words like &quot;a&quot;, &quot;an&quot;,&quot;to&quot;,&quot;I&quot;,&quot;it&quot;, increasing accuracy. </td>
      </tr>
      <tr bgcolor="white">
        <td width="20%" valign="top"><strong>Don&apos;t use numbers:&nbsp;</strong></td>
        <td valign="top"><input name="kpg_pf_numbs" type="checkbox" value="Y" <?php if ($kpg_pf_numbs=='Y') {?> checked="checked" <?php } ?>/>
        </td>
        <td valign="top"> Numbers can confuse the search for a permalink. the number 11 will find 911 and 2011, not just 11. Check this if you accuracy is being hurt by numbers and you don't want to search for them. </td>
      </tr>
      <tr bgcolor="white">
        <td width="20%" valign="top"><strong>Metaphone search (sounds like): </strong> </td>
        <td valign="top"><input name="chkmetaphone" type="checkbox" value="Y" <?php if ($chkmetaphone=='Y') {?> checked="checked" <?php } ?>/>
        </td>
        <td valign="top"> If a permalink can't be found, then check this to use a second metaphone search. This does a "Sounds-Like" search. Metaphone can solve problems where there is a spelling error in the permalink. </td>
      </tr>
      <tr bgcolor="white">
        <td width="20%" valign="top"><strong>Track 404 and redirects:&nbsp;</strong></td>
        <td valign="top"><select name="stats">
            <option value="0" <?php if ($stats=='0') {?> selected="selected" <?php } ?>>Disabled</option>
            <option value="10" <?php if ($stats=='10') {?> selected="selected" <?php } ?>>Last 10</option>
            <option value="20" <?php if ($stats=='20') {?> selected="selected" <?php } ?>>Last 20</option>
            <option value="30" <?php if ($stats=='30') {?> selected="selected" <?php } ?>>Last 30</option>
          </select></td>
        <td valign="top"> As long as we are looking at 404&apos;s and trying to redirect them we might as well keep track of the last few hits and what happened to them. You can keep up to 30 of the last hits in memory. (If you set this to zero you will lose any statistics that have been recorded.)</td>
      </tr>
    </table>
 
    <br/>

    <h3>Special File Handling:</h3>
    <p>If any of these files result in a 404 file not found, you can return a default version instead.</p>
    <table align="center" cellspacing="2" style="background-color:#CCCCCC;font-size:.9em;">
      <tr bgcolor="white">
        <td width="20%" valign="top"><strong>Robots.txt missing: </strong>
          <input name="chkrobots" type="checkbox" value="Y" <?php if ($chkrobots=='Y') {?> checked="checked" <?php } ?>/>
        </td>
        <td valign="top"><textarea name="robots" cols="48" rows="9"><?php echo $robots ?></textarea>
        </td>
        <td valign="top"> When a spider can't find the robots.txt file return this robots.txt file </td>
      </tr>
      <tr bgcolor="white">
        <td width="20%" valign="top"><strong>favicon.ico or apple-touch-icon.png missing: </strong> </td>
        <td valign="top"><input name="chkicon" type="checkbox" value="Y" <?php if ($chkicon=='Y') {?> checked="checked" <?php } ?>/></td>
        <td valign="top"> When your site does not have a favicon.ico or apple-touch-icon.png file return the default wordpress icon. (Only works if wordpress is set to handle the 404 for the these files.) 
	</td>
      </tr>
      <tr bgcolor="white">
        <td width="20%" valign="top"><strong>sitemap.xml missing: </strong> </td>
        <td valign="top"><input name="chksitemap" type="checkbox" value="Y" <?php if ($chksitemap=='Y') {?> checked="checked" <?php } ?>/></td>
        <td valign="top"> When a robot looks for your site map and can't find it, this will return your last 20 pages modified, ensuring that the search engines will find your most recent posts and pages. Spiders will spider your whole site eventually, but this will cue them that you have new or changed stuff. 
	</td>
      </tr>
      <tr bgcolor="white">
        <td width="20%" valign="top"><strong>crossdomain.xml missing: </strong> </td>
        <td valign="top"><input name="chkcrossdomain" type="checkbox" value="Y" <?php if ($chkcrossdomain=='Y') {?> checked="checked" <?php } ?>/></td>
        <td valign="top">When the adobe crossdomain.xml file is not found, the plugin provides a restrictive version that will protect your site from cross domain flash running and corrupting your site. Malicious spiders look for this file to see if you are vulnerable to exploits.
        </td>
      </tr>
      <tr bgcolor="white">
        <td width="20%" valign="top"><strong>Dublin.rdf missing: </strong> </td>
        <td valign="top"><input name="chkdublin" type="checkbox" value="Y" <?php if ($chkdublin=='Y') {?> checked="checked" <?php } ?>/></td>
        <td valign="top"> Dublin.rdf is a way some search engines can discover a description of your site. When missing use a default one. This does not set the required meta information in the blog head, but is only here if search engines robots look for it. 
	</td>
      </tr>
      <tr bgcolor="white">
        <td width="20%" valign="top"><strong>OpenSearch.txt missing: </strong> </td>
        <td valign="top"><input name="chkopensearch" type="checkbox" value="Y" <?php if ($chkopensearch=='Y') {?> checked="checked" <?php } ?>/></td>
        <td valign="top"> OpenSearch is a method for displaying a search box for your site. When missing use a default one. This does not set the required meta information in the blog head, but is only here if a program looks for it. 
	</td>
      </tr>
    </table>
    <br/>
 
    <br/>

    <h3>Remove &quot;Buy The Book&quot;:</h3>
    <input type="checkbox" name ="nobuy" value="Y" <?php if ($nobuy=='Y') echo 'checked="true"'; ?> >
    <?php 
		if ($nobuy=='Y')  {
			echo "Thanks";		
		} else {
		?>
    Check if you are tired of seeing the <a target="_blank" href="http://www.blogseye.com/buy-the-book/">Buy Keith's Book</a> box at the top of the page.
    <?php 
		}
	?>
 
    <br/>
    <br/>
    <p class="submit">
      <input class="button-primary" value="Save Changes" type="submit">
    </p>
  </form>
 <a name="stats" nid="#stats" />
<a href="#stats" onclick="window.location.href=window.location.href;">Refresh</a>
<?php
// now show the stats.

	if ($stats>0) {
		if (count($f404)>0) {
?>
  <h3 align="center">Fixed Permalinks</h3>
  <form method="POST" action="">
    <input class="button-primary" value="Clear Fixed Permalinks" type="submit">
    <input type="hidden" name="action1" value="clear_fixed" />
    <input type="hidden" name="kpg_pf_control" value="<?php echo $nonce;?>" />
  </form>
  <table align="center" cellspacing="2" style="background-color:#CCCCCC;font-size:.9em;">
    <tr bgcolor="white">
      <td style="background-color:#FFFFEE">Date/Time</td>
      <td style="background-color:#FFFFEE">Requested Page</td>
      <td style="background-color:#FFFFEE">Fixed Permalink</td>
      <td style="background-color:#FFFFEE">Referring Page</td>
      <td style="background-color:#FFFFEE">Browser User Agent</td>
      <td style="background-color:#FFFFEE">Remote IP</td>
    </tr>
    <?php
for ($j=0;$j<count($f404)&&$j<$stats;$j++ ) {
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
    <tr bgcolor="white">
      <td><?php echo $f404[$j][0]; ?></td>
      <td><a href="<?php echo $f404[$j][1]; ?>" title="<?php echo $f404[$j][1]; ?>" target="_blank"><?php echo $f1; ?></a></td>
      <td><a href="<?php echo $f404[$j][5]; ?>" title="<?php echo $f404[$j][5]; ?>" target="_blank"><?php echo $f5; ?></a></td>
      <td><a href="<?php echo $f404[$j][2]; ?>" title="<?php echo $f404[$j][2]; ?>" target="_blank"><?php echo $f2; ?></a></td>
      <td><?php echo $f404[$j][3]; ?></td>
      <td><?php echo $f404[$j][4]; ?>
        <?php } ?>
  </table>
  <?php } ?>
  <?php 
	if (count($e404)>0) {
?>
  <h3 align="center">404 errors</h3>
  <form method="POST" action="">
    <input class="button-primary" value="Clear 404 Errors" type="submit">
    <input type="hidden" name="action2" value="clear_404" />
    <input type="hidden" name="kpg_pf_control" value="<?php echo $nonce;?>" />
  </form>
  <table align="center" cellspacing="2" style="background-color:#CCCCCC;font-size:.9em;">
    <tr bgcolor="white">
      <td style="background-color:#FFFFEE">Date/Time</td>
      <td style="background-color:#FFFFEE">Requested Page</td>
      <td style="background-color:#FFFFEE">Referring Page</td>
      <td style="background-color:#FFFFEE">Browser User Agent</td>
      <td style="background-color:#FFFFEE">Remote IP
        <?php
for ($j=0;$j<count($e404)&&$j<$stats;$j++ ) {
    $e404[$j][1]=urldecode($e404[$j][1]);
    $e404[$j][2]=urldecode($e404[$j][2]);
    $f1=$e404[$j][1];
    $f2=$e404[$j][2];
	if (strlen($f1)>32) $f1=substr($f1,0, 32).'...';
	if (strlen($f2)>32) $f2=substr($f2,0,32).'...';
?>
    <tr bgcolor="white">
      <td><?php echo $e404[$j][0]; ?></td>
      <td><a href="<?php echo $e404[$j][1]; ?>" title="<?php echo $e404[$j][1]; ?>" target="_blank"><?php echo $f1; ?></td>
      <td><a href="<?php echo $e404[$j][2]; ?>" title="<?php echo $e404[$j][2]; ?>" target="_blank"><?php echo $f2; ?></a></td>
      <td><?php echo $e404[$j][3]; ?></td>
      <td><?php echo $e404[$j][4]; ?>
        <?php } ?>
  </table>
  <?php
	}
	}
?>
</div>
