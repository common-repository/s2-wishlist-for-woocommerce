<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) || ! defined( 'S2_WL_VERSION' ) ) {
	exit;
}

/**
 * Implements admin report list features of S2 Wishlist
 *
 * @class   S2_Wishlist_Report_List
 * @package S2 Wishlist
 * @since   1.0.3
 * @author  Shuban Studio <shuban.studio@gmail.com>
 */
if ( ! class_exists( 'S2_Wishlist_Report_List' ) ) {

	if ( ! class_exists( 'WP_List_Table' ) ) require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';

	class S2_Wishlist_Report_List extends WP_List_Table {

		/**
		 * @var string
		 */
		private $post_type;

		/**
		 * @var int
		 */
		private $per_page;

		/**
		 * Constructor.
		 *
		 * @since  1.0.3
		 */
		public function __construct() {

			parent::__construct(
				[
					'singular' => 'Report',
					'plural'   => 'Reports',
					'ajax'     => false,
				]
			);

			$this->post_type = 'product';
			$this->per_page  = 10;

		}

		/**
		 * No items found text.
		 *
		 * @since  1.0.3
		 */
		public function no_items() {
			esc_html_e( 'No wishlisted product found.', 's2-wishlist' );
		}

		/**
		 * Get column value.
		 *
		 * @param WP_Post $post WP Post object.
		 * @param string  $column_name Column name.
		 *
		 * @since  1.0.3
		 *
		 * @return string
		 */
		public function column_default( $post, $column_name ) {

			switch ( $column_name ) {

				case 'product_title':
					$edit_post_link = get_edit_post_link( $post->ID );

					return '<a href="' . $edit_post_link . '">' . $post->post_title . '</a>';

				case 'wishlist_count':
					return $post->wishlist_count;

			}

			return '';
		}

		/**
		 * Get columns.
		 *
		 * @since  1.0.3
		 *
		 * @return array
		 */
		public function get_columns() {
			$columns = [
				'product_title'  => __( 'Product title', 's2-wishlist' ),
				'wishlist_count' => __( 'Wishlist count', 's2-wishlist' ),
			];

			return $columns;
		}

		/**
		 * Get sortable columns.
		 *
		 * @since  1.0.3
		 *
		 * @return array
		 */
		function get_sortable_columns() {
			$sortable_columns = [
				'product_title'  => [ 'post_title', false ],
				'wishlist_count' => [ 'wishlist_count', false ],
			];

			return $sortable_columns;
		}

		/**
		 * Prepare customer list items.
		 *
		 * @since  1.0.3
		 */
		public function prepare_items() {
			global $wpdb;

			$current_page = absint( $this->get_pagenum() );

			/**
			 * Init column headers.
			 */
			$this->_column_headers = [ $this->get_columns(), [], $this->get_sortable_columns() ];

			// Check if the request came from search form
			$where = "AND w_p.post_type = 'product'";
			if ( ! empty( $_REQUEST['s'] ) ) {
				$query_search = sanitize_text_field( $_REQUEST['s'] );

				$where .= $wpdb->prepare( " AND ( w_p.post_title LIKE CONCAT('%', %s, '%') )", [ $query_search ] );
			}

			$orderby = 'post_title';
	        if( ! empty( $_GET['orderby'] ) ) {
	        	$orderby = sanitize_text_field( $_GET['orderby'] );
	        }

			$order = 'ASC';
	        if( ! empty( $_GET['order'] ) ) {
	        	$order = sanitize_text_field( $_GET['order'] );
	        }

	    	$query = "SELECT w_p.ID, w_p.post_title, COUNT(w_um.user_id) AS wishlist_count FROM $wpdb->posts AS w_p 
		    		LEFT JOIN $wpdb->usermeta AS w_um ON w_um.meta_value LIKE CONCAT('%i:', w_p.ID, ';%') AND w_um.meta_key = 's2-wishlist' 
		    		WHERE 1=1 $where 
		    		GROUP BY w_p.ID 
		    		ORDER BY $orderby $order";

	    	$total_items = $wpdb->query( $query );

	    	// Page Number
			$paged = ! empty( $_GET['paged'] ) ? $_GET['paged'] : 1;
			if ( ! is_numeric( $paged ) || $paged <= 0 ) {
				$paged = 1;
			}
			$offset = ( $paged - 1 ) * $this->per_page;
			$query .= ' LIMIT ' . (int) $offset . ',' . (int) $this->per_page;

			$this->items = $wpdb->get_results( $query );

			/**
			 * Pagination.
			 */
			$this->set_pagination_args(
				[
					'total_items' => $total_items,
					'per_page'    => $this->per_page,
					'total_pages' => ceil( $total_items / $this->per_page ),
				]
			);
		}

		/**
		 * Display the search box.
		 *
		 * @param string $text     The search button text
		 * @param string $input_id The search input id
		 *
		 * @since  1.0.3
		 */
		public function search_box( $text, $input_id ) {

			$input_id = $input_id . '-search-input';
			$input_id = esc_attr( $input_id );

			if ( ! empty( $_REQUEST['orderby'] ) ) {
				echo '<input type="hidden" name="orderby" value="' . esc_attr( $_REQUEST['orderby'] ) . '" />';
			}
			if ( ! empty( $_REQUEST['order'] ) ) {
				echo '<input type="hidden" name="order" value="' . esc_attr( $_REQUEST['order'] ) . '" />';
			}

			?>
			<p class="search-box">
				<label class="screen-reader-text" for="<?php echo $input_id; ?>"><?php echo $text; ?>:</label>
				<input type="search" id="<?php echo $input_id; ?>" name="s" value="<?php _admin_search_query(); ?>" placeholder="<?php _e( 'Search by product', 's2-wishlist' ); ?>" />
				<?php submit_button( $text, 'button', '', false, [ 'id' => 'search-submit' ] ); ?>
			</p>
			<?php

		}

	}

}
