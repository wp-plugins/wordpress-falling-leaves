<?php
/*
	Plugin Name: WP-Leaves
	Plugin URI: http://premiumcoding.com
	Description: Falling leaves for WordPress
	Version: 1.01
	Author: Gljivec & Zdrifko
	Author URI: http://premiumcoding.com
	
	Copyright 2011, Gljivec & Zdrifko
*/

// check for WP context
if ( !defined('ABSPATH') ){ die(); }

//set install options
function wp_leaves_install () {
	$newoptions = get_option('wpleaves_options');
	$newoptions['width'] = '500';
	$newoptions['height'] = '300';
	$newoptions['url'] = '';
	$newoptions['number_leaves'] =  '50';
	$newoptions['rotationType'] = '2D';
}

global $jal_db_version;
$jal_db_version = "1.0";

function jal_installleaves() {
   global $wpdb;
   global $jal_db_version;

   $table_leaves = $wpdb->prefix . "leaves";
   $sql_calendar  = "CREATE TABLE ". $table_leaves ."  (
		id mediumint(9) NOT NULL auto_increment,
		url varchar(500) NOT NULL default '',
		width varchar(100) NOT NULL default '',
		height varchar(8) NOT NULL default '',
		number_leaves varchar(300) NOT NULL default '',
		rotationType varchar(500) NOT NULL default '',
		id_leaves mediumint(9) NOT NULL,
		UNIQUE KEY id (id)
    );";

	
	
   require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
   dbDelta($sql_calendar );
   global $wpdb;
   $table_name = "wp_banner";
   $wpdb->insert( $table_leaves , array( 'url' => '', 'width' => '500', 'height' => '300', 'number_leaves' => '50' , 
										   'rotationType' => '2D'));

 
   add_option("jal_db_version", $jal_db_version);
}
// add the admin page
function wp_leaves_add_pages() {
	add_options_page('WP-Leaves', 'WP-Leaves', 8, __FILE__, 'wp_leaves_options');
}


// template function
function wp_leaves_insert( $atts=NULL ){
	echo wp_leaves_createflashcode( false, $atts );
}


if (isset($_GET['page']) && $_GET['page'] == 'wordpress-falling-leaves/leaves.php'){
	wp_enqueue_script('jquery');	
	wp_register_script('my-upload', plugins_url("wordpress-falling-leaves/script.js"), array('jquery','media-upload','thickbox'));
	wp_enqueue_script('my-upload');
	wp_enqueue_style('thickbox');
	wp_register_style('myStyleSheets', plugins_url("wordpress-falling-leaves/style.css"));
    wp_enqueue_style( 'myStyleSheets');


}
// create html tags and flash tags
function wp_leaves_createflashcode($name,$id,$heightIn,$widthIn){
    $rand = rand(5, 99); 
	$flashtag .= '<div style = "float:left; width:'.$widthIn.'px; height:'.$heightIn.'px; margin:5px; "><script type="text/javascript" src="'.plugins_url("wordpress-falling-leaves/swfobject/swfobject.js").'" charset="utf-8"></script><script type="text/javascript" src="'.plugins_url("wordpress-falling-leaves/swfobject/swfaddress.js").'" charset="utf-8"></script><script type="text/javascript">
								var flashvars = {queryPath:"'.plugins_url('wordpress-falling-leaves/query.php?id='.$rand ).'",BannerID:"'.(string)$id.'"};   
								var params = {};
								var attributes = {};
								params.bgcolor = "000000";
								params.scale = "noscale";
								params.salign = "tl";
								params.wmode = "transparent"; 
								swfobject.embedSWF("'.plugins_url("wordpress-falling-leaves/fallingLeaves.swf?id=".$rand ).'", "'.$name.'-'.$id.'", "'.$widthIn.'px", "'.$heightIn.'px", "9.0.0", "'.plugins_url("wordpress-falling-leaves/swfobject/expressInstall.swf").'", flashvars, params, attributes);</script><div id="'.$name.'-'.$id.'"></div></div>';
	return $flashtag;
}

function wp_leaves_short($atts){
	$options = get_option('wpleaves_options');
	extract(shortcode_atts(array(
	'id' => 0,
	'width' => $options['width'],
	'height' => $options['height'],
	'upload_image' => $options['upload_image'],
	'number_leaves' => $options['number_leaves'],
	'custom' => 0,	
	'rotationtype' => $options['rotationtype']	
	), $atts));
	if($custom == 0)
		$flashtags = wp_leaves_createflashcode('leaves_short',0,$height,$width);
	else{
		global $wpdb;
		$table_leaves = $wpdb->prefix . "leaves";
		$result = mysql_query("SELECT count(id) FROM ".$table_leaves." WHERE id_leaves = ".$id."");
		$row = mysql_fetch_array($result);
		if($row['count(id)'] == 0){
			$wpdb->insert( $table_leaves , array( 'url' => $upload_image, 'width' => $width, 'height' => $height, 'number_leaves' => $number_leaves , 
													'rotationType' => $rotationtype, 'id_leaves'  => $id));
		}
		else{
			mysql_query("UPDATE ".$table_leaves." SET url = '".$upload_image."' , width = '".$width."', height = '".$height."',  
															number_leaves = '".$number_leaves."', rotationType = '".$rotationtype."' WHERE id_leaves = ".$id."");
		}
		$flashtags = wp_leaves_createflashcode('leaves_short',$id,$height,$width);
	
	}
	
							
return $flashtags;
}


// options page
function wp_leaves_options() {	
	$options = $newoptions = get_option('wpleaves_options');
	// if submitted, process results
	if (!empty($_POST["leaves_submit"]) ) {
		$newoptions['upload_image'] = strip_tags(stripslashes($_POST["upload_image"]));
		$newoptions['width'] = strip_tags(stripslashes($_POST["width"]));
		$newoptions['height'] = strip_tags(stripslashes($_POST["height"]));
		$newoptions['number_leaves'] = strip_tags(stripslashes($_POST["number_leaves"]));
		$newoptions['rotationType'] = strip_tags(stripslashes($_POST["rotationType"]));


	}
	// if changes save!
	if ( $options != $newoptions ) {
		$options = $newoptions;
		update_option('wpleaves_options', $options);
		global $wpdb;
		$table_leaves = $wpdb->prefix . "leaves";
		mysql_query("UPDATE ".$table_leaves." SET url = '".$newoptions['upload_image']."' , width = '".$newoptions['width']."', height = '".$newoptions['height']."',  
															number_leaves = '".$newoptions['number_leaves']."', rotationType = '".$newoptions['rotationType']."' WHERE id = 1");
	}
	global $wpdb;
	$table_leaves = $wpdb->prefix . "leaves";
	$result = mysql_query("SELECT * FROM ".$table_leaves." where id = 1 ");
	$row = mysql_fetch_array($result);
	// options form
	echo '<div class="allBanner">
	<div class = "buttons">
	<div class= "settingsB" id = "settingsB"><a href="" onClick="return false;">Settings</a></div>
	<div class = "helpB" id = "helpB"><a href="" onClick="return false;">Help</a></div>	
	</div>
		<div id="help"><h2 >Help</h2>
	Short Code : <br>If you need custom image use:<br><b><font size="2px">[leaves id=1 custom=1 width=300 height=300 upload_image=url number_leaves=50 rotationtype=2D]</font></b><br>
				 <font size="0.5px">If you use 2 or more shortcode on one page or post id must be diffrent value!</font><br><br>
				 If you need defult image use:<br><font size="2px"><b>[leaves custom=0]</b></font><br><br>
    Visit our support site for more help <a href="http://premiumcoding.com/wordpress-plugin-falling-leaves">PremiumCoding</a>.<br>
    In case you need additional support please contact us at <a href="mailto:info@premiumcoding.com">info@premiumcoding.com</a>	<br>	<br>
	Support our work :
	<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
	<input type="hidden" name="cmd" value="_s-xclick">
	<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----">
	<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
	<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
	</form><br><p style="text-align:right;"><font size="0.5px">Version: 1.0</font></p>
	
	</div>
	<form method="post"><div id="settings">';
	echo "<div class=\"wrap\"><h2>Flash Tag with HTML links Display options</h2>";
	echo '<table class="form-table">';
	// width
	echo '<tr valign="top"><th scope="row">Width of image</th>';
	echo '<td><input type="text" name="width" value="'.$row['width'].'" size="5"></input><br />Width in pixels (200 or more is recommended)</td></tr>';
	// height
	echo '<tr valign="top"><th scope="row">Height of image</th>';
	echo '<td><input type="text" name="height" value="'.$row['height'].'" size="5"></input><br />Height in pixels (300 or more is recommended)</td></tr>';
	//image
	echo '<tr valign="top"><th scope="row">Path do Image</th>';
	echo '<td><label for="upload_image">
		  <input id="upload_image" type="text" size="36" name="upload_image" value="'.$row['url'].'" />
		  <input id="upload_image_button" type="button" value="Upload Image" />
		  <br />Enter an URL or upload an image for the accordion rotator.
		  </label></td>
		  </tr>';
	// number_leaves
	echo '<tr valign="top"><th scope="row">Number leaves</th>';
	echo '<td><input type="text" name="number_leaves" value="'.$row['number_leaves'].'" size="5"></input><br />Height in pixels (300 or more is recommended)</td></tr>';			  
	// transition
	echo '<tr valign="top"><th scope="row">Type of animation</th>';
	echo '<td><select name="rotationType">';
		 if($row['rotationTypen']=='2D')
			echo '<option selected="selected" value="2D">2D</option>';
		 else
			echo '<option value="2D">2D</option>';		
		 /*if($newoptions['animation']=='staticVertical')
			echo '<option selected="selected" value="staticVertical">staticVertical</option>';
		 else
			echo '<option value="staticVertical">staticVertical</option>';			
		 if($newoptions['animation']=='staticHorizontal')
			echo '<option selected="selected" value="staticHorizontal">staticHorizontal</option>';
		 else
			echo '<option value="staticHorizontal">staticHorizontal</option>';		
			*/
	echo '</select></td></tr>';
	echo '</table>';
	echo '<input type="hidden" name="leaves_submit" value="true"></input>';
	echo '<p class="submit"><input type="submit" value="Update Options &raquo;"></input></p>';
	echo "</div></div>";
	echo '</form></div>';
}

//uninstall all options
function wp_leaves_uninstall () {
	delete_option('wpleaves_options');
	delete_option('wpleaves_options');
}



add_action('init', 'widget_leaves_register');

function widget_leaves_register() {
 
	$prefix = 'leaves'; // $id prefix
	$name = __('WP leaves');
	$widget_ops = array('classname' => 'widget_leaves', 'description' => __('WP leaves for Wordpress'));
	$control_ops = array('width' => 200, 'height' => 40, 'id_base' => $prefix);
 
	$options = get_option('widget_leaves');
	if(isset($options[0])) unset($options[0]);
 
	if(!empty($options)){
		foreach(array_keys($options) as $widget_number){
			wp_register_sidebar_widget($prefix.'-'.$widget_number, $name, 'widget_leaves', $widget_ops, array( 'number' => $widget_number ));
			wp_register_widget_control($prefix.'-'.$widget_number, $name, 'widget_leaves_control', $control_ops, array( 'number' => $widget_number ));
		}
	} else{
		$options = array();
		$widget_number = 1;
		wp_register_sidebar_widget($prefix.'-'.$widget_number, $name, 'widget_leaves', $widget_ops, array( 'number' => $widget_number ));
		wp_register_widget_control($prefix.'-'.$widget_number, $name, 'widget_leaves_control', $control_ops, array( 'number' => $widget_number ));
	}
}


function widget_leaves($args, $vars = array()) {
	extract($args);
	// get widget saved options
	$widget_number = (int)str_replace('leaves-', '', @$widget_id);
	$options = get_option('widget_leaves');
	if(!empty($options[$widget_number])){
		$vars = $options[$widget_number];
	}
	// widget open tags
	echo $before_widget;
 
	// print title from admin 
	if(!empty($vars['title'])){
		echo $before_title . $vars['title'] . $after_title;
	} 
	if( !stristr( $_SERVER['PHP_SELF'], 'widgets.php' ) ){
		$mainoptions = get_option('wpleaves_options');
		echo wp_leaves_createflashcode('sidebar-leaves',0,$mainoptions['height'],$mainoptions['width']);
		}
	echo $after_widget;
}


function widget_leaves_control($args) {
	
	$prefix = 'leaves'; // $id prefix
 
	$options = get_option('widget_leaves');
	if(empty($options)) $options = array();
	if(isset($options[0])) unset($options[0]);
 
	// update options array
	if(!empty($_POST[$prefix]) && is_array($_POST)){
		foreach($_POST[$prefix] as $widget_number => $values){
			if(empty($values) && isset($options[$widget_number])) // user clicked cancel
				continue;
 
			if(!isset($options[$widget_number]) && $args['number'] == -1){
				$args['number'] = $widget_number;
				$options['last_number'] = $widget_number;
			}
			$options[$widget_number] = $values;
		}
 
		// update number
		if($args['number'] == -1 && !empty($options['last_number'])){
			$args['number'] = $options['last_number'];
		}
 
		// clear unused options and update options in DB. return actual options array
		$options = bf_smart_multiwidget_update($prefix, $options, $_POST[$prefix], $_POST['sidebar'], 'widget_leaves');
	}
 
	// $number - is dynamic number for multi widget, gived by WP
	// by default $number = -1 (if no widgets activated). In this case we should use %i% for inputs
	//   to allow WP generate number automatically
	
	$number = ($args['number'] == -1)? '%i%' : $args['number'];
	
	// now we can output control
	$opts = @$options[$number];
 
	$title = @$opts['title'];


	?>
	<table class="form-table"><tr valign="top"><tr><td>
    Title <br/><input type="text" name="<?php echo $prefix; ?>[<?php echo $number; ?>][title]" value="<?php echo $title; ?>" /><br/></td></table>
	<?php

}

// helper function can be defined in another plugin
if(!function_exists('bf_smart_multiwidget_update')){
	function bf_smart_multiwidget_update($id_prefix, $options, $post, $sidebar, $option_name = ''){
		global $wp_registered_widgets;
		static $updated = false;
 
		// get active sidebar
		$sidebars_widgets = wp_get_sidebars_widgets();
		if ( isset($sidebars_widgets[$sidebar]) )
			$this_sidebar =& $sidebars_widgets[$sidebar];
		else
			$this_sidebar = array();
 
		// search unused options
		foreach ( $this_sidebar as $_widget_id ) {
			if(preg_match('/'.$id_prefix.'-([0-9]+)/i', $_widget_id, $match)){
				$widget_number = $match[1];
 
				// $_POST['widget-id'] contain current widgets set for current sidebar
				// $this_sidebar is not updated yet, so we can determine which was deleted
				if(!in_array($match[0], $_POST['widget-id'])){
					unset($options[$widget_number]);
				}
			}
		}
 
		// update database
		if(!empty($option_name)){
			update_option($option_name, $options);
			$updated = true;
		}
 
		// return updated array
		return $options;
	}
}

// add the actions
register_activation_hook(__FILE__,'jal_installleaves');
add_action('admin_menu', 'wp_leaves_add_pages');
register_activation_hook( __FILE__, 'wp_leaves_install' );
register_deactivation_hook( __FILE__, 'wp_leaves_uninstall' );
add_shortcode('leaves', 'wp_leaves_short');


?>