<?php
/**
 * @package          WP Pipes plugin - PIPES
 * @version          $Id: settings-init.php 170 2014-01-26 06:34:40Z thongta $
 * @author           wppipes.com
 * @copyright        2014 wppipes.com. All rights reserved.
 * @license          GNU/GPL v3, see LICENSE
 */
defined( 'PIPES_CORE' ) or die( 'Restricted access' );

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $pipes;


$pipes_settings['general'] = apply_filters('pipes_general_settings', array(

	array( 'title' => __( 'General Settings', 'pipes' ), 'type' => 'title', 'desc' => '', 'id' => 'general_settings' ),

	array(
		'title' 	=> __( 'Enable Cronjob / Schedule', 'pipes' ),
		'desc' 		=> __( 'Enable the cronjob to be executed. You have option to run the cronjob out-of-box by the below option. Set this option NO to run Pipes by manually post only.', 'pipes' ),
		'id' 		=> 'pipes_cronjob_active',
		'default'	=> '1',
		'type' 		=> 'radio',
		'options'	=> array(
			'1' => __( 'Yes', 'pipes' ),
			'0' => __( 'No', 'pipes' )
		),
		'desc_tip'	=>  true,
	),

	array(
		'title' 	=> __( 'Auto Run', 'pipes' ),
		'desc' 		=> __( "If set to yes, the cronjob will be executed via browser. If not, you will have to setup cronjob via your hosting panel.", 'pipes' ),
		'id' 		=> 'pipes_active',
		'default'	=> '0',
		'type' 		=> 'radio',
		'desc_tip'	=>  true,
		'options'	=> array(
			'1' => __( 'Yes', 'pipes' ),
			'0' => __( 'No', 'pipes' )
		),
	),

	array(
		'title' => __( 'Schedule', 'pipes' ),
		'desc' 		=> __( 'Set the time period to execute the cronjob.', 'pipes' ),
		'id' 		=> 'pipes_schedule',
		'default'	=> 'h3',
		'type' 		=> 'select',
		'class'		=> 'chosen_select',
		'css' 		=> 'min-width:350px;',
		'desc_tip'	=>  true,
		'options' => array(
			'i5' => __( '5 minutes', 'pipes' ),
			'i10' => __( '10 minutes', 'pipes' ),
			'i15' => __( '15 minutes', 'pipes' ),
			'i20' => __( '20 minutes', 'pipes' ),
			'i25' => __( '25 minutes', 'pipes' ),
			'i30' => __( '30 minutes', 'pipes' ),
			'h1' => __( '1 hour', 'pipes' ),
			'h2' => __( '2 hours', 'pipes' ),
			'h3' => __( '3 hours', 'pipes' ),
			'h4' => __( '4 hours', 'pipes' ),
			'h6' => __( '6 hours', 'pipes' ),
			'h8' => __( '8 hours', 'pipes' ),
			'h12' => __( '12 hours', 'pipes' ),
			'h24' => __( '24 hours', 'pipes' )
		)
	),

	array(
		'title' 	=> __( 'Not Use Cache', 'pipes' ),
		'desc' 		=> __( "If set to yes, the cronjob will be executed directly from the source, not from Cache. If not, cronjob will get data from the cache if the cache is not expired.", 'pipes' ),
		'id' 		=> 'pipes_not_use_cache',
		'default'	=> '0',
		'type' 		=> 'radio',
		'desc_tip'	=>  true,
		'options'	=> array(
			'1' => __( 'Yes', 'pipes' ),
			'0' => __( 'No', 'pipes' )
		),
	),

	array(
		'title' => __( 'Start at', 'pipes' ),
		'desc' 		=> __( 'Select the time for getting started', 'pipes' ),
		'id' 		=> 'pipes_start_at',
		'css' 		=> 'min-width:350px;',
		'default'	=> '',
		'type' 		=> 'text'
	)

)); // End general settings