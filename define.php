<?php
/**
 * @package          WP Pipes plugin - PIPES
 * @version          $Id: define.php 170 2014-01-26 06:34:40Z thongta $
 * @author           wppipes.com
 * @copyright        2014 wppipes.com. All rights reserved.
 * @license          GNU/GPL v3, see LICENSE
 */
defined( 'PIPES_CORE' ) or die( 'Restricted access' );
defined( '_JEXEC' ) or die( 'Restricted access' );


define( 'OBGRAB_SITE', dirname( __FILE__ ) . DS );
define( 'OBGRAB_ADMIN', dirname( __FILE__ ) . DS );
define( 'JPATH_ROOT', dirname( __FILE__ ) );
define( 'OBGRAB_ADDONS', OBGRAB_SITE . 'plugins' . DS );
define( 'OBGRAB_HELPERS', OBGRAB_SITE . 'helpers' . DS );

define( 'OBGRAB_ENGINES', OBGRAB_SITE . DS . 'plugins' . DS . 'engines' . DS );
define( 'OBGRAB_ADAPTERS', OBGRAB_SITE . DS . 'plugins' . DS . 'adapters' . DS );
define( 'OBGRAB_PROCESSORS', OBGRAB_SITE . DS . 'plugins' . DS . 'processors' . DS );

define( 'OGRAB_CACHE', dirname( __FILE__ ) . DS . 'cache' . DS . 'wppipes' . DS );
define( 'OGRAB_ECACHE', OGRAB_CACHE . 'ecache' . DS );

//define( 'OGRAB_CACHE'			,JPATH_ROOT.DS.'cache'.DS.'wppipes'.DS);
define( 'OGRAB_EDATA', OGRAB_CACHE . 'edata' . DS );
define( 'OGRAB_CACHE_SAVED', OGRAB_CACHE . 'saved' . DS );

define( 'OGRAB_MEDATA', OGRAB_EDATA . 'maxid' . DS );

define( 'OB_PATH_PLUGIN', dirname( getcwd() ) . DS . 'wp-content' . DS . 'plugins' . DS );

define( 'OB_PATH_PLUGIN_OTHER', dirname( dirname( __FILE__ ) ) . DS );

define( 'SITE_UPLOAD_DIR', WP_CONTENT_DIR . DS . 'uploads' );