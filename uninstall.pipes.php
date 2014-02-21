<?php
/**
 * @package          WP Pipes plugin - PIPES
 * @version          $Id: uninstall.pipes.php 166 2014-01-26 02:45:27Z thongta $
 * @author           wppipes.com
 * @copyright        2014 wppipes.com. All rights reserved.
 * @license          GNU/GPL v3, see LICENSE
 */
defined( 'PIPES_CORE' ) or die( 'Restricted access' );

global $wpdb;
require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

#--------------------------------------------------
# Drop Config table 
#--------------------------------------------------
// your code at here


#--------------------------------------------------
# Drop Items table
#--------------------------------------------------

$wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->prefix . "wppipes_items" );
$wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->prefix . "wppipes_pipes" );
$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE '%pipes_%';" );

#--------------------------------------------------
# setup Cronjob
#--------------------------------------------------
//wp_schedule_event(time(), 'once_half_hour', 'my_hourly_event');