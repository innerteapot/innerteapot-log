<?php
// if uninstall not called from WordPress exit
//
if (!defined('WP_UNINSTALL_PLUGIN')) 
    exit();

// drop a custom db table
//
global $wpdb;
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}innerteapot_log" );

?>
