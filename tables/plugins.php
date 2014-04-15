<?php
/**
 * @package              WP Pipes plugin - PIPES
 * @version              $Id: plugins.php 170 2014-01-26 06:34:40Z thongta $
 * @author               wppipes.com
 * @copyright            2014 wppipes.com. All rights reserved.
 * @license              GNU/GPL v3, see LICENSE
 */
defined( 'PIPES_CORE' ) or die( 'Restricted access' );

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

//@session_start();
@$_SESSION['PIPES']['plugins'] = array();

class PIPES_Plugins_List_Table extends WP_List_Table {
	public $per_page = 10;
	function extra_tablenav( $which ) {
		global $addon_type;
		if ( 'top' == $which ) {
			$addon_types = apply_filters( 'admin_addon_types_dropdown', array(
				'adapters'   => __( 'Destinations' ),
				'engines'    => __( 'Sources' ),
				'processors' => __( 'Processors' ),
			) );
			echo '<select name="addon_type">
				<option value="">' . __( 'Show all addon types' ) . '</option>';
			foreach ( $addon_types as $type => $label ) {
				echo "\t<option value='" . esc_attr( $type ) . "'" . selected( $addon_type, $type, false ) . ">$label</option>\n";
			}
			echo '</select>';
			submit_button( __( 'Filter' ), 'button', false, false, array( 'id' => 'post-query-submit' ) );
		}
	}


	/** ************************************************************************
	 * Normally we would be querying data from a database and manipulating that
	 * for use in your list table. For this example, we're going to simplify it
	 * slightly and create a pre-built array. Think of this as the data that might
	 * be returned by $wpdb->query().
	 *
	 * @var array
	 **************************************************************************/
// 	public $networks = array();
	public function getPlugins() {
		require_once dirname( dirname( __FILE__ ) ) . DS . 'helpers' . DS . 'plugins.php';

		return PIPES_Helper_Plugins::getPlugins( true );
	}


	/** ************************************************************************
	 * REQUIRED. Set up a constructor that references the parent constructor. We
	 * use the parent reference to set some default configs.
	 ***************************************************************************/
	function __construct() {
		parent::__construct( array(
			'singular' => 'addon', //singular name of the listed records
			'plural'   => 'addons', //plural name of the listed records
			'ajax'     => false //does this table support ajax?
		) );
	}


	/** ************************************************************************
	 * Recommended. This method is called when the parent class can't find a method
	 * specifically build for a given column. Generally, it's recommended to include
	 * one method for each column you want to render, keeping your package class
	 * neat and organized. For example, if the class needs to process a column
	 * named 'title', it would first see if a method named $this->column_title()
	 * exists - if it does, that method will be used. If it doesn't, this one will
	 * be used. Generally, you should try to use custom column methods as much as
	 * possible.
	 *
	 * Since we have defined a column_title() method later on, this method doesn't
	 * need to concern itself with any column with a name of 'title'. Instead, it
	 * needs to handle everything else.
	 *
	 * For more detailed insight into how columns are handled, take a look at
	 * WP_List_Table::single_row_columns()
	 *
	 * @param array $item        A singular item (one full row's worth of data)
	 * @param array $column_name The name/slug of the column to be processed
	 *
	 * @return string Text or HTML to be placed inside the column <td>
	 **************************************************************************/
	function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'name':
			case 'description':
				return $item[$column_name];
			default:
				return print_r( $item, true ); //Show the whole array for troubleshooting purposes
		}
	}


	function column_name( $item ) {
		//Build row actions
		/*$actions = array(
//			'addconnection' => sprintf( '<a href="?page=%s&action=%s&addon=%s">Add Connection</a>', 'obsocialsubmit_filter', 'add', $item['addon'] ),
			'uninstall' => sprintf( '<a href="?page=%s&action=%s&addon=%s">Uninstall</a>', $_REQUEST['page'], 'uninstall', $item['addon']. '-' .$item['element'] ),
		);*/
		switch ( $item['addon'] ) {
			case 'engine':
				$addon = 'source';
				break;
			case 'adapter':
				$addon = 'destination';
				break;
			default:
				$addon = $item['addon'];
				break;
		}
		//Return the title contents
		$name_addon = sprintf( '<b>%1$s</b> <span style="color:silver">(type: %2$s)</span>',
			/*$1%s*/
			$item['name'],
			/*$2%s*/
			$addon
			/*$3%s*/
			//$this->row_actions( $actions )
		);

		return $name_addon;
	}

	function column_description( $item ) {
		//Build row actions
		$html = '<div class="plugin-description"><p>' . $item['description'] . '</p></div>'
			. '<div class="active second plugin-version-author-uri">'
			. 'Version ' . $item['version'] . ' | '
			. 'By <a href="' . $item['authorUrl'] . '" title="Visit author homepage">' . $item['author'] . '</a> | '
			. '<a href="' . $item['authorUrl'] . '" title="Visit plugin site">Visit network site</a>'
			. '</div>';

		return $html;
	}

	/** ************************************************************************
	 * REQUIRED if displaying checkboxes or using bulk actions! The 'cb' column
	 * is given special treatment when columns are processed. It ALWAYS needs to
	 * have it's own method.
	 *
	 * @see WP_List_Table::::single_row_columns()
	 *
	 * @param array $item A singular item (one full row's worth of data)
	 *
	 * @return string Text to be placed inside the column <td> (movie title only)
	 **************************************************************************/
//	function column_cb( $item ) {
//		return sprintf(
//			'<input type="checkbox" name="%1$s[]" value="%2$s" />',
//			/*$1%s*/
//			$this->_args['singular'], //Let's simply repurpose the table's singular label ("movie")
//			/*$2%s*/
//			$item['addon'].'-'.$item['element'] //The value of the checkbox should be the record's id
//		);
//	}


	/** ************************************************************************
	 * REQUIRED! This method dictates the table's columns and titles. This should
	 * return an array where the key is the column slug (and class) and the value
	 * is the column's title text. If you need a checkbox for bulk actions, refer
	 * to the $columns array below.
	 *
	 * The 'cb' column is treated differently than the rest. If including a checkbox
	 * column in your table you must create a column_cb() method. If you don't need
	 * bulk actions or checkboxes, simply leave the 'cb' entry out of your array.
	 *
	 * @see WP_List_Table::::single_row_columns()
	 * @return array An associative array containing column information: 'slugs'=>'Visible Titles'
	 **************************************************************************/
	function get_columns() {
		$columns = array(
			//'cb'          => '<input type="checkbox" />', //Render a checkbox instead of text
			'name'        => 'Addon',
			'description' => 'Description'
		);

		return $columns;
	}


	/** ************************************************************************
	 * Optional. If you want one or more columns to be sortable (ASC/DESC toggle),
	 * you will need to register it here. This should return an array where the
	 * key is the column that needs to be sortable, and the value is db column to
	 * sort by. Often, the key and value will be the same, but this is not always
	 * the case (as the value is a column name from the database, not the list table).
	 *
	 * This method merely defines which columns should be sortable and makes them
	 * clickable - it does not handle the actual sorting. You still need to detect
	 * the ORDERBY and ORDER querystring variables within prepare_items() and sort
	 * your data accordingly (usually by modifying your query).
	 *
	 * @return array An associative array containing all the columns that should be sortable: 'slugs'=>array('data_values',bool)
	 **************************************************************************/
	function get_sortable_columns() {
		$sortable_columns = array( 'name' => array( 'name', false ) );

		return $sortable_columns;
	}


	/** ************************************************************************
	 * Optional. If you need to include bulk actions in your list table, this is
	 * the place to define them. Bulk actions are an associative array in the format
	 * 'slug'=>'Visible Title'
	 *
	 * If this method returns an empty value, no bulk action will be rendered. If
	 * you specify any bulk actions, the bulk actions box will be rendered with
	 * the table automatically on display().
	 *
	 * Also note that list tables are not automatically wrapped in <form> elements,
	 * so you will need to create those manually in order for bulk actions to function.
	 *
	 * @return array An associative array containing all the bulk actions: 'slugs'=>'Visible Titles'
	 **************************************************************************/
	/*function get_bulk_actions() {
		$actions = array(
			'uninstall' => 'uninstall'
		);

		return $actions;
	}*/


	/** ************************************************************************
	 * Optional. You can handle your bulk actions anywhere or anyhow you prefer.
	 * For this example package, we will handle it in the class to keep things
	 * clean and organized.
	 *
	 * @see $this->prepare_items()
	 **************************************************************************/
	function process_bulk_action() {

		//Detect when a bulk action is being triggered...
		if ( 'uninstall' === $this->current_action() ) {
			wp_die( 'Items deleted (or they would be if we had items to delete)!' );
		}

	}


	/** ************************************************************************
	 * REQUIRED! This is where you prepare your data for display. This method will
	 * usually be used to query the database, sort and filter the data, and generally
	 * get it ready to be displayed. At a minimum, we should set $this->items and
	 * $this->set_pagination_args(), although the following properties and methods
	 * are frequently interacted with here...
	 *
	 * @global WPDB $wpdb
	 * @uses $this->_column_headers
	 * @uses $this->items
	 * @uses $this->get_columns()
	 * @uses $this->get_sortable_columns()
	 * @uses $this->get_pagenum()
	 * @uses $this->set_pagination_args()
	 **************************************************************************/
	function prepare_items() {
		global $wpdb, $addon_type; //This is used only if making any database queries
		$addon_type = ! empty( $_REQUEST['addon_type'] ) ? $_REQUEST['addon_type'] : '';
		/**
		 * First, lets decide how many records per page to show
		 */
		$per_page = $this->per_page;
		/**
		 * REQUIRED. Now we need to define our column headers. This includes a complete
		 * array of columns to be displayed (slugs & titles), a list of columns
		 * to keep hidden, and a list of columns that are sortable. Each of these
		 * can be defined in another method (as we've done here) before being
		 * used to build the value for our _column_headers property.
		 */
		$columns  = $this->get_columns();
		$hidden   = array();
		$sortable = $this->get_sortable_columns();


		/**
		 * REQUIRED. Finally, we build an array to be used by the class for column
		 * headers. The $this->_column_headers property takes an array which contains
		 * 3 other arrays. One for all columns, one for hidden columns, and one
		 * for sortable columns.
		 */
		$this->_column_headers = array( $columns, $hidden, $sortable );


		/**
		 * Optional. You can handle your bulk actions however you see fit. In this
		 * case, we'll handle them within our package just to keep things clean.
		 */
		//$this->process_bulk_action();


		/**
		 * Instead of querying a database, we're going to fetch the example data
		 * property we created for use in this plugin. This makes this example
		 * package slightly different than one you might build on your own. In
		 * this example, we'll be using array manipulation to sort and paginate
		 * our data. In a real-world implementation, you will probably want to
		 * use sort and pagination data to build a custom query instead, as you'll
		 * be able to use your precisely-queried data immediately.
		 */
		$data = $this->getPlugins();
// 		echo '<pre>'.print_r($data, true ).'</pre>';

		/**
		 * This checks for sorting input and sorts the data in our array accordingly.
		 *
		 * In a real-world situation involving a database, you would probably want
		 * to handle sorting by passing the 'orderby' and 'order' values directly
		 * to a custom query. The returned data will be pre-sorted, and this array
		 * sorting technique would be unnecessary.
		 */
		function usort_reorder( $a, $b ) {
			$orderby = ( ! empty( $_REQUEST['orderby'] ) ) ? $_REQUEST['orderby'] : 'name'; //If no sort, default to title
			$order   = ( ! empty( $_REQUEST['order'] ) ) ? $_REQUEST['order'] : 'asc'; //If no order, default to asc
			$result  = strcmp( $a[$orderby], $b[$orderby] ); //Determine sort order
			return ( $order === 'asc' ) ? $result : - $result; //Send final sort direction to usort
		}

		if ( is_array( $data ) ) {
			usort( $data, 'usort_reorder' );
		}


		/***********************************************************************
		 * ---------------------------------------------------------------------
		 * vvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvv
		 *
		 * In a real-world situation, this is where you would place your query.
		 *
		 * ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
		 * ---------------------------------------------------------------------
		 **********************************************************************/


		/**
		 * REQUIRED for pagination. Let's figure out what page the user is currently
		 * looking at. We'll need this later, so you should always include it in
		 * your own package classes.
		 */
		$current_page = $this->get_pagenum();

		/**
		 * REQUIRED for pagination. Let's check how many items are in our data array.
		 * In real-world use, this would be the total number of items in your database,
		 * without filtering. We'll need this later, so you should always include it
		 * in your own package classes.
		 */
		$total_items = count( $data );


		/**
		 * The WP_List_Table class does not handle pagination for us, so we need
		 * to ensure that the data is trimmed to only the current page. We can use
		 * array_slice() to
		 */
		if ( is_array( $data ) ) {
			$data = array_slice( $data, ( ( $current_page - 1 ) * $per_page ), $per_page );
		}


		/**
		 * REQUIRED. Now we can add our *sorted* data to the items property, where
		 * it can be used by the rest of the class.
		 */
		$this->items = $data;


		/**
		 * REQUIRED. We also have to register our pagination options & calculations.
		 */
		$this->set_pagination_args( array(
			'total_items' => $total_items, //WE have to calculate the total number of items
			'per_page'    => $per_page, //WE have to determine how many items to show on a page
			'total_pages' => ceil( $total_items / $per_page ) //WE have to calculate the total number of pages
		) );
	}

}