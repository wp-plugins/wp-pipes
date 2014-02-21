<?php
/**
 * @package          WP Pipes plugin - PIPES
 * @version          $Id: install.pipes.php 166 2014-01-26 02:45:27Z thongta $
 * @author           wppipes.com
 * @copyright        2014 wppipes.com. All rights reserved.
 * @license          GNU/GPL v3, see LICENSE
 */
defined( 'PIPES_CORE' ) or die( 'Cannot direct access' );

global $wpdb;
require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

#--------------------------------------------------
# Add user_meta for all admins
#--------------------------------------------------
$users     = get_users();
$user_meta = array( 'pipes_help_box' => 1, 'pipes_per_page' => 20, 'addons_per_page' => 20 );
foreach ( $users as $user ) {
	if ( is_super_admin( $user->ID ) ) {
		foreach ( $user_meta as $meta_key => $value ) {
			$meta_value = get_user_meta( $user->ID, $meta_key, true );
			if ( $meta_value == '' ) {
				update_user_meta( $user->ID, $meta_key, $value );
			}
		}
	}
}

#--------------------------------------------------
# Create Items table 
#--------------------------------------------------

$sql = 'CREATE TABLE IF NOT EXISTS `' . $wpdb->prefix . 'wppipes_items` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`name` varchar(255) NOT NULL,
	`published` tinyint(1) NOT NULL,
	`engine` varchar(100) NOT NULL,
	`engine_params` text NOT NULL,
	`adapter` varchar(100) NOT NULL,
	`adapter_params` text NOT NULL,
	`inherit` int(11) NOT NULL DEFAULT "0",
	`inputs` text NOT NULL,
	`outputs` text NOT NULL,
	PRIMARY KEY (`id`)
  ) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8';
dbDelta( $sql );


#--------------------------------------------------
# Create Pipes table
#--------------------------------------------------
$sql = 'CREATE TABLE IF NOT EXISTS `' . $wpdb->prefix . 'wppipes_pipes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `item_id` int(11) NOT NULL,
  `params` text NOT NULL,
  `ordering` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8';
dbDelta( $sql );
#--------------------------------------------------
# setup Cronjob
#--------------------------------------------------
//wp_schedule_event(time(), 'once_half_hour', 'my_hourly_event');