<?php
/**
 * @package              WP Pipes plugin - PIPES
 * @version              $Id: items.php 148 2014-01-25 04:47:00Z thongta $
 * @author               wppipes.com
 * @copyright            2014 wppipes.com. All rights reserved.
 * @license              GNU/GPL v3, see LICENSE
 */
defined( 'PIPES_CORE' ) or die( 'Restricted access' );

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Lo_Items_List_Table extends WP_List_Table {

	public $data = array();
	public $per_page = 5;
	public $messages = '';

	function __construct( $args = array() ) {
		parent::__construct( array(
			'singular' => 'item',
			'plural'   => 'items',
			'screen'   => isset( $args['screen'] ) ? $args['screen'] : null,
			'ajax'     => false
		) );
		add_filter( "manage_{$this->screen->id}_columns", array( $this, 'get_columns' ), 0 );
	}

	function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'name':
			case 'engine':
			case 'adapter':
			case 'published':
			case 'id':
				return $item[$column_name];
			default:
				return print_r( $column_name, true ); //Show the whole array for troubleshooting purposes
		}
	}

	function column_title( $item ) {
		//Build row actions
		$actions = array(
			'edit'   => sprintf( '<a href="?page=%s&task=%s&id=%s">Edit</a>', $_REQUEST['page'], 'edit', $item['id'] ),
			'delete' => sprintf( '<a href="?page=%s&task=%s&id=%s">Delete</a>', $_REQUEST['page'], 'delete', $item['id'] ),
		);

		//Return the title contents
		return sprintf( '%1$s <span style="color:silver">(id:%2$s)</span>%3$s',
			/*$1%s*/
			$item['name'],
			/*$2%s*/
			$item['id'],
			/*$3%s*/
			$this->row_actions( $actions )
		);
	}

	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="%1$s[]" value="%2$s" />',
			/*$1%s*/
			$this->_args['singular'], //Let's simply repurpose the table's singular label ("movie")
			/*$2%s*/
			$item['id'] //The value of the checkbox should be the record's id
		);
	}

	function views() {
		$views = $this->get_views();
		/**
		 * Filter the list of available list table views.
		 *
		 * The dynamic portion of the hook name, $this->screen->id, refers
		 * to the ID of the current screen, usually a string.
		 *
		 * @since 3.5.0
		 *
		 * @param array $views An array of available list table views.
		 */
		$views = apply_filters( "views_{$this->screen->id}", $views );

		if ( empty( $views ) ) {
			return;
		}

		echo "<ul class='subsubsub'>\n";
		foreach ( $views as $class => $view ) {
			$views[$class] = "\t<li class='$class'>$view";
		}
		echo implode( " |</li>\n", $views ) . "</li>\n";
		echo "</ul>";
	}

	function get_views() {
		global $status;
		$status_arr = array(
			'all'     => __( 'All' ),
			'publish' => __( 'Publish' )
		);
		foreach ( $status_arr as $key => $title ) {
			$total            = $this->getTotal( $condition = '', $key );
			$class            = ( $status == $key ) ? 'class="current"' : '';
			$status_arr[$key] = "<a href='" . esc_url( add_query_arg( 'post_status', $key, $_SERVER['REQUEST_URI'] ) ) . "' $class>$title  <span class=\"count\">($total)</span></a>";
		}

		return $status_arr;
	}

	function extra_tablenav( $which ) {
		if ( 'top' == $which ) {
			echo '<div class="alignleft actions">';
			$dropdown_adapters = array(
				'selected'        => 'Adapter',
				'name'            => 'adapter',
				'taxonomy'        => 'link_category',
				'show_option_all' => __( 'View all destinations' ),
				'hide_empty'      => true,
				'hierarchical'    => 1,
				'show_count'      => 0,
				'orderby'         => 'name',
			);
			$dropdown_engines  = array(
				'selected'        => 'Engine',
				'name'            => 'engine',
				'taxonomy'        => 'link_category',
				'show_option_all' => __( 'View all sources' ),
				'hide_empty'      => true,
				'hierarchical'    => 1,
				'show_count'      => 0,
				'orderby'         => 'name',
			);
			$this->render_list_plugins( $dropdown_engines );
			$this->render_list_plugins( $dropdown_adapters ); //var_dump($this->get_list_adapter( $dropdown_adapters ));

			submit_button( __( 'Filter' ), 'button', false, false, array( 'id' => 'post-query-submit' ) );
			echo '</div>';
		}
	}

	function get_list_used_plugins( $type ) {
		global $wpdb;
		$sql   = "SELECT `" . $type . "`
				FROM `" . $wpdb->prefix . "wppipes_items`
				GROUP BY `" . $type . "`";
		$datas = $wpdb->get_results( $sql, ARRAY_A );

		return $datas;
	}

	function render_list_plugins( $render ) {
		$value_arrays = $this->get_list_used_plugins( $render['name'] );
		$options      = '';
		foreach ( $value_arrays as $element ) {
			$select = ( ! empty( $_REQUEST[$render['name']] ) AND ( $_REQUEST[$render['name']] == $element[$render['name']] ) ) ? ' selected ' : '';
			if ( $element[$render['name']] != '' ) {
				$options .= '<option value="' . $element[$render['name']] . '"' . $select . '>' . $element[$render['name']] . '</option>';
			}
		}
		$output = '<select name="' . $render['name'] . '" id="' . $render['name'] . '" class="postform">
				<option value="">' . $render['show_option_all'] . '</option>' . $options . '
				</select>';
		$output = apply_filters( 'wp_dropdown_cats', $output );
		echo $output;

		return $output;
	}

	function get_bulk_actions() {
		$actions = array();

		$actions['copy']            = __( 'Copy' );
		$actions['delete']          = __( 'Delete' );
		$actions['export_to_share'] = __( 'Export' );
		$actions['publish']         = __( 'Publish' );
		$actions['move_to_draft']   = __( 'Draft' );

		//$actions['inherit'] = __( 'Inherit' );


		return $actions;
	}

	function get_columns() {
		$columns = array(
			'cb'      => '<input type="checkbox" />', //Render a checkbox instead of text
			'name'    => 'Name',
			'engine'  => 'Source',
			'adapter' => 'Destination',
//				'created_time'	=> 'Created Time',
//				'hits'			=> 'Hits',
//				'published'		=> 'Published',
//			'id'      => 'Id'
		);

		return $columns;
	}

	function row_actions( $actions, $always_visible = false ) {
		return array();
	}

	function get_sortable_columns() {
		$sortable_columns = array(
			'name'      => array( 'name', false ), //true means it's already sorted
			'published' => array( 'published', false ),
			'id'        => array( 'id', false )
		);

		return $sortable_columns;
	}

	function prepare_items() {
		global $mode, $status; //This is used only if making any database queries
		$mode      = empty( $_REQUEST['mode'] ) ? 'excerpt' : $_REQUEST['mode'];
		$status    = empty( $_REQUEST['post_status'] ) ? 'all' : $_REQUEST['post_status'];
		$condition = $this->buildquery_condition();
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
		$total_items = $this->getTotal( $condition );

		/**
		 * REQUIRED. Now we can add our *sorted* data to the items property, where
		 * it can be used by the rest of the class.
		 */
		$this->items = $this->getItems( $condition );


		/**
		 * REQUIRED. We also have to register our pagination options & calculations.
		 */
		$this->set_pagination_args( array(
			'total_items' => $total_items, //WE have to calculate the total number of items
			'per_page'    => $this->per_page, //WE have to determine how many items to show on a page
			'total_pages' => ceil( $total_items / $this->per_page ) //WE have to calculate the total number of pages
		) );


	}

	public function buildquery_condition() {
		global $status;
		$where   = array();
		$where[] = "`engine` <> '' AND `adapter` <> ''";
		if ( $status != 'all' ) {
			$where[] = ' `published` = 1';
		}
		if ( ! empty( $_REQUEST['s'] ) ) {
			$where[] = ' `name` LIKE "%' . $_REQUEST['s'] . '%"';
		}
		if ( ! empty( $_REQUEST['adapter'] ) ) {
			$where[] = ' `adapter` LIKE "' . $_REQUEST['adapter'] . '"';
		}
		if ( ! empty( $_REQUEST['engine'] ) ) {
			$where[] = ' `engine` LIKE "' . $_REQUEST['engine'] . '"';
		}
		$where_str = ( count( $where ) > 0 ) ? 'WHERE ' . implode( ' AND ', $where ) : '';

		return $where_str;
	}

	public function getItems( $condition = '' ) {
		global $wpdb;


		$orderby     = ( ! empty( $_REQUEST['orderby'] ) ) ? $_REQUEST['orderby'] : 'id'; //If no sort, default to title
		$order       = ( ! empty( $_REQUEST['order'] ) ) ? $_REQUEST['order'] : 'asc'; //If no order, default to asc
		$paged       = ( ! empty( $_REQUEST['paged'] ) ) ? $_REQUEST['paged'] : 1;
		$limit_start = ( $this->per_page * ( $paged - 1 ) );
		$sql         = "SELECT `id`, `name`, `engine`, `adapter`, `engine_params`, `adapter_params`, `published`
				FROM `" . $wpdb->prefix . "wppipes_items` $condition
				ORDER BY {$orderby} {$order} LIMIT {$limit_start},{$this->per_page}";

		$datas = $wpdb->get_results( $sql, ARRAY_A );

		return $datas;
	}

	public function getTotal( $condition = '', $status = null ) {
		global $wpdb;
		$status  = empty( $status ) ? 'all' : $status;
		$where   = array();
		$where[] = "`engine` <> '' AND `adapter` <> ''";
		if ( $status != 'all' ) {
			$where[] = "`published` = 1";
		}
		if ( '' == $condition ) {
			$condition = 'WHERE ' . implode( ' AND ', $where );
		}
		$sql = "SELECT count(*)
				FROM `" . $wpdb->prefix . "wppipes_items` $condition";
		$res = $wpdb->get_var( $sql );

		return $res;
	}

	function pagination( $which ) {
		global $mode;

		parent::pagination( $which );

		if ( 'top' == $which ) {
			$this->view_switcher( $mode );
		}
	}

	function get_more_display( $params ) {
		$params_obj = json_decode( $params );

		return $params_obj->feed_url;
	}

	public function single_row_columns( $item ) {
		global $mode;
		if ( 'rssreader' == $item['engine'] ) {
			$more_display = $this->get_more_display( $item['engine_params'] );
		} else {
			$more_display = '';
		}
		list( $columns, $hidden ) = $this->get_column_info();
		foreach ( $columns as $column_name => $column_display_name ) {
			$class = "class='$column_name column-$column_name'";

			$style = '';
			if ( in_array( $column_name, $hidden ) ) {
				$style = ' style="display:none;"';
			}

			$attributes = "$class$style";

			if ( 'cb' == $column_name ) {
				echo "<th scope='row' class='check-column'>";
				echo sprintf( '<input id="cb-select-%s" type="checkbox" name="id[]" value="%s" />', $item['id'], $item['id'] );
				echo "</th>";
			} elseif ( 'name' == $column_name ) {
				echo "<td $attributes>";

				echo '<strong>';
				echo '
					<a class="row-title" id="row_title_' . $item['id'] . '" href="' . sprintf( '?page=%s&task=%s&id=%s"', $_REQUEST['page'], 'edit', $item['id'] ) . '>'
					. sprintf( 'Pipe#%s', $item['id'] ) . ' - ' . $item['name'] . '
					</a>
				';

				// If the Pipe is unpublished, show it as Draft
				if ( $item['published'] == 0 ) {
					echo '<span class="post-state">' . __( ' - Draft' ) . '</span>';
				}
				echo '</strong>';

				echo ( 'excerpt' == $mode && $more_display != '' ) ? '</br><span>' . $more_display . '</span></br>' : '';

				echo "<div class='row-actions'><span class='edit'>";
				echo sprintf( '<a href="?page=%s&task=%s&id=%s">Edit</a>', $_REQUEST['page'], 'edit', $item['id'] );
				echo "</span> | <span class='quickedit'>";
				echo '<span class="quick_edit_link" onclick="display_quick_edit(this,'. $item['id'] .');">Quick Edit</span>';
				echo "</span> | <span class='trash'>";
				echo sprintf( '<a href="?page=%s&task=%s&id=%s">Delete</a>', $_REQUEST['page'], 'delete', $item['id'] );
				echo "</span> | <span class='post'>";
				echo sprintf( '<a data-id="%s" class="btn-pipes-post" href="?page=%s&task=%s&id=%s">Test</a>', $item['id'], PIPES::$__page_prefix . '.pipe', 'post', $item['id'] );
				echo "</span> | <span class='postupdate'>";
				echo sprintf( '<a data-id="%s" class="btn-pipes-post" href="?page=%s&task=%s&id=%s&u=1">Test in Update mode</a>', $item['id'], PIPES::$__page_prefix . '.pipe', 'post', $item['id'] );
				echo "</span> | <span class='export'>";
				echo sprintf( '<a data-id="%s" class="btn-pipes-export" href="?page=%s&task=%s&id=%s">Export</a>', $item['id'], PIPES::$__page_prefix . '.pipes', 'export_to_share', $item['id'] );
				echo "</span></div></td>";
			} else {
				echo "<td $attributes>";
				echo $this->column_default( $item, $column_name );
				echo "</td>";
			}

		}
		$select = $item['published'] == 1 ? 'selected' : '';
		echo "<tr id='quickeditpipe_" . $item['id'] . "' style='display:none;' class='quick_edit_pipe inline-edit-row inline-edit-row-post inline-edit-post quick-edit-row quick-edit-row-post inline-edit-post inline-editor'>";
		echo "<td colspan=\"4\">
			<fieldset class=\"inline-edit-col-left\">
				<div class=\"inline-edit-col\">
					<h4>Quick Edit</h4>

					<label>
						<span class=\"title\">Title</span>
						<span class=\"input-text-wrap\"><input type=\"text\" name=\"pipe_title\" class=\"ptitle\" value=\"{$item['name']}\"></span>
					</label>
				</div>
			</fieldset>
			<fieldset class=\"inline-edit-col-right\">
				<div class=\"inline-edit-col\">
					<span class=\"title inline-edit-categories-label\">Status</span>
					<select name=\"pipe_status\">
						<option value=\"1\" ";
		echo $item['published'] == 1 ? 'selected' : '';
		echo ">Published</option>
						<option value=\"0\" ";
		echo $item['published'] == 0 ? 'selected' : '';
		echo ">Draft</option>
					</select>

				</div>
			</fieldset>
			<p class=\"submit inline-edit-save\">
				<span accesskey=\"c\" onclick=\"display_quick_edit(this, {$item['id']}, 1);\" class=\"button-secondary cancel alignleft\">Cancel</span>
				<span accesskey=\"s\" onclick=\"display_quick_edit(this, {$item['id']}, 2);\" class=\"button-primary save alignright\">Update</span>
				<br class=\"clear\">
			</p>
		</td>";
		echo "</tr>";
	}

}
