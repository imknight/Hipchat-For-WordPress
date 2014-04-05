<?php
/*
Plugin Name: Hipchat For WordPress
Plugin URI: 
Description: Send a message to a HipChat room whenever a content is published.
Version: 1.0.2
Author: knight
Author URI: http://imknight.net
License: GNU GPL v2
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once( plugin_dir_path( __FILE__ ) . 'class-rework-hipchat.php' );

register_activation_hook( __FILE__, array( 'Rework_Hipchat', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Rework_Hipchat', 'deactivate' ) );

Rework_Hipchat::get_instance();