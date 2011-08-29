<?php
// **************************************
// Config.php for Comcast LVOD 7/14/09
// **************************************
    $root = dirname(dirname(dirname(dirname(__FILE__))));
    require_once($root.'/wp-config.php');
	global $wpdb;
	$table_leaves = $wpdb->prefix . "leaves";
?>
