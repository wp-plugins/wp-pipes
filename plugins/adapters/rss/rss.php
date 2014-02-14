<?php
/**
 * @package		WP Pipes plugin
 * @version		$Id: post.php 135 2014-01-22 10:48:35Z tung $
 * @author		wppipes.com
 * @copyright	(c) 2007-2013 wppipes.com. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html
 */

defined('_JEXEC') or die( 'Restricted access' );

// test address: http://localhost/joomla7/index.php?option=com_wppipes&task=runaddon&type=adapter&addon=k2
require_once OBGRAB_ENGINES. DS .'rssreader'.DS. 'helpers' . DS . 'autoloader.php';
require_once dirname(__FILE__).DS.'helpers'.DS.'feedcreator.php';
class WPPipesAdapter_rss
{
	/**
	 * Check if an item is duplicated
	 * @param array $fields
	 * @return bool|int
	 */
	public static function checkDuplicate($fields = array ()) {
		global $wpdb;
		if (!count($fields)) {
			return false;
		}
		
		$res = 0;
		$qry = "SELECT `ID` FROM " . $wpdb->prefix . "posts WHERE `post_name`='".addslashes($fields['title'])."' AND `post_type` = 'post'";
		$res = (int)$wpdb->get_var($qry);
		return $res;
	}

	/**
	 * Logging
	 * @param $id
	 * @param $action
	 * @param $msg
	 * @return stdclass
	 */
	public static function makeLog($id,$action,$msg) {
		$res	= new stdclass();
		$res->name		= 'RSS feed';
		$res->action	= $action;
		$res->msg		= $msg;
		$res->id		= $id;
		$res->viewLink	= $msg;
		$res->editLink	= '';
		
		return $res;
	}

	/**
	 * Storing item
	 * @param $data
	 * @param $params
	 * @return stdclass
	 */
	static function store($data,$params) {
		
		$file_path = ABSPATH.$params->folder.DS.$params->filename;
		$rows		= array();
		$feed_url	= get_site_url().'/'.$params->folder.'/'.$params->filename;
		if( is_file($file_path) ) {
			$rows = self::get_items_feed($feed_url);
		}
		
		$item_exists = false;
		if( $rows && count($rows)){
			foreach($rows as $row){
				if($row->title == $data->title ||$row->link == $data->link  ){
					$item_exists = true;
				}
			}
		}

		if(!$item_exists){
			$items = array_merge(array($data),$rows);
			$items_number = $params->items_number;
			if(!$items_number) {
				$items_number = 10;
			}
			if( $items && count($items)> $items_number){
				$items = array_slice($items, 0, $items_number );
			}
			if(count($items)){
				$feed_title = $params->feed_title;
				if(!trim($feed_title)){
					$feed_title = get_bloginfo('title');
				}
				$feed_desc = $params->feed_description;
				if(!trim($feed_desc)){
					$feed_desc = get_bloginfo('description');
				}
				$feed_link = get_site_url();
				$rss_data = new stdClass();

				$rss_data->title		= $feed_title;
				$rss_data->description	= $feed_desc;
				$rss_data->link			= $feed_link;
				$rss_data->items		= $items;

				self::createRSSFile($rss_data, $file_path);
				$action = 'Create RSS feed';
				$id		= '';
				$msg	= $feed_url;
				$res	= self::makeLog($id,$action,$msg);
				return $res;
			}
		}
		
		$action = 'Create RSS feed';
		$id		= '';
		$msg	= $feed_url;
		$res	= self::makeLog($id,$action,$msg);
		return $res;
	}


	/**
	 * @param bool $param
	 * @return stdClass
	 */
	public static function getDataFields($param = false) {
		$data	= new stdClass();
		$inputs	= 'title,link,description,author,pubDate';
		$data->input	= explode(',',$inputs);
		return $data;
	}

	
	public static function createRSSFile($data,$file_path){
		#TODO: create RSS
		$rss = new UniversalFeedCreator();
		$rss->useCached(); // use cached version if age<1 hour
		$rss->title = $data->title;
		$rss->description = $data->description;
		//optional
		$rss->descriptionTruncSize = 500;
		$rss->descriptionHtmlSyndicated = true;
		$rss->link = $data->link;
		$rss->syndicationURL = get_site_url();
//		$image = new FeedImage();
//		$image->title = "dailyphp.net logo";
//		$image->url = "http://www.dailyphp.net/images/logo.gif";
//		$image->link = get_site_url();
//		$image->description = "Feed provided by dailyphp.net. Click to visit.";
		//optional
//		$image->descriptionTruncSize = 500;
//		$image->descriptionHtmlSyndicated = true;
//		$rss->image = $image;

		$items = $data->items;
		foreach ( $items AS $item ) {
			$feed_item = new FeedItem();
			$feed_item->title			= $item->title;
			$feed_item->link			= $item->link;
			$feed_item->description		= $item->description;
			/*
			//optional
			$feed_item->descriptionTruncSize = 500;
			$feed_item->descriptionHtmlSyndicated = true;
			//optional (enclosure)
			$feed_item->enclosure = new EnclosureItem();
			$feed_item->enclosure->url='http://http://www.dailyphp.net/media/voice.mp3';
			$feed_item->enclosure->length="950230";
			$feed_item->enclosure->type='audio/x-mpeg';
			
			 */
			$date = isset($item->date)?$item->date:'';
			if(!$date){
				$date = '';
			}
			$feed_item->date	= $date;
//			$feed_item->source	= $item->source;
			$feed_item->author	= $item->author;
			$rss->addItem($feed_item);
		}
		// valid format strings are: RSS091, RSS10, RSS20, PIE01 (deprecated),
		// MBOX, OPML, ATOM, ATOM10, ATOM03, HTML, JS
		$rss->saveFeed("RSS20", $file_path, false);
		//to generate "on-the-fly"
//		$rss->outputFeed("RSS20");
	}
	
	
	public static function get_items_feed( $url, $params=null ) {
		$feed = new SimplePie();
		$mode = isset( $params->mode ) ? $params->mode : 0;
		if ( $mode == 0 ) {
			$feed->set_feed_url( $url );
		} else {
			$html = ogbFile::get_curl( $url );
			$feed->set_raw_data( $html );
		}
		$feed->set_autodiscovery_level( SIMPLEPIE_LOCATOR_NONE );
		$feed->set_timeout( 20 );
		$feed->enable_cache( false );
		$feed->set_stupidly_fast( true );
		$feed->enable_order_by_date( false ); // we don't want to do anything to the feed
		$feed->set_url_replacements( array() );
		$feed->force_feed(true);
		$result = $feed->init();
		if ( isset( $_GET['x'] ) ) {
			echo "\n\n<br /><i><b>File:</b>" . __FILE__ . ' <b>Line:</b>' . __LINE__ . "</i><br />\n\n";
			echo "<p>URL: [{$url}]</p>";
			echo "<p>Error: [{$feed->error}]</p>";
		}
		$items   = $feed->get_items();

		$c_items = count( $items );
		if ( $c_items == 0 ) {
			echo "<p>Error: [{$feed->error}]</p>";

			return array();
		}
		for ( $i = 0; $i < count( $items ); $i ++ ) {
			$row              = new stdclass();
			$row->title       = $items[$i]->get_title(); # the title for the post
			$row->link        = $items[$i]->get_link(); # a single link for the post
			$row->description = $items[$i]->get_description(); # the content of the post (prefers summaries)
			$row->author      = $items[$i]->get_author(); # a single author for the post
			$row->date        = $items[$i]->get_date( 'Y-m-d H:i:s' );
			$row->enclosures  = $items[$i]->get_enclosures();
			$rows[]           = $row;
		}

		return $rows;
	}
}