<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) || ! defined( 'S2_WL_VERSION' ) ) {
	exit;
}

/**
 * Implements admin plugin panel features of S2 Wishlist
 *
 * @class   S2_Wishlist_Plugin_Panel
 * @package S2 Wishlist
 * @since   1.0.0
 * @author  Shuban Studio <shuban.studio@gmail.com>
 */
if ( ! class_exists( 'S2_Wishlist_Plugin_Panel' ) ) {

	class S2_Wishlist_Plugin_Panel {

		/**
		 * Single instance of the class
		 *
		 * @var \S2_Wishlist_Plugin_Panel
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @since  1.0.0
		 * @return \S2_Wishlist_Plugin_Panel
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * @param array $args
		 *
		 * @since  1.0.0
		 */
		public function __construct() {

			$this->create_menu_items();

			// Add action links.
			add_filter( 'plugin_action_links_' . plugin_basename( S2_WL_DIR . '/' . basename( S2_WL_FILE ) ), [ $this, 'action_links' ] );

		}

		/**
		 * Create Menu Items
		 *
		 * Print admin menu items
		 *
		 * @since  1.0.0
		 */
		private function create_menu_items() {

			// Add a panel or menu pages
			add_action( 'admin_menu', [ $this, 'register_panel' ], 5 );

		}

		/**
		 * Add a panel or menu pages
		 *
		 * @since  1.0.0
		 */
		public function register_panel() {

			global $submenu;

			$args = [
				'page_title'    => 'Home',
				'menu_title'    => 'S2 Plugins',
				'capability'    => 'manage_options',
				'menu_slug'     => 's2-admin',
				'function_name' => '',
				'icon_url'      => 'dashicons-heart',
				'position'      => null,
			];
			if( empty( $submenu['s2-admin'] ) ) $this->add_menu_page( $args );

			$args = [
				'parent_slug'   => 's2-admin',
				'page_title'    => 'Wishlist',
				'menu_title'    => 'Wishlist',
				'capability'    => 'manage_options',
				'menu_slug'     => 's2-wishlist',
				'function_name' => 'show_wishlist',
				'position'      => null,
			];
			$this->add_submenu_page( $args );

			unset( $submenu['s2-admin'][0] );

		}

		/**
		 * Add Menu page link
		 *
		 * @param array $args
		 *
		 * @since  1.0.0
		 */
		public function add_menu_page( $args ) {

			add_menu_page( $args['page_title'], $args['menu_title'], $args['capability'], $args['menu_slug'], [ $this, $args['function_name'] ], $args['icon_url'], $args['position'] );

		}

		/**
		 * Add Menu page link
		 *
		 * @param array $args
		 *
		 * @since  1.0.0
		 */
		public function add_submenu_page( $args ) {

			add_submenu_page( $args['parent_slug'], $args['page_title'], $args['menu_title'], $args['capability'], $args['menu_slug'], [ $this, $args['function_name'] ], $args['position'] );

		}

		/**
		 * Show wishlist admin page
		 *
		 * @since  1.0.0
		 */
		public function show_wishlist() {

			$args = [
				'page'			=> 's2-wishlist',
				'admin_tabs' 	=> [
										'report'   => __( 'Report', 's2-wishlist' ),
										'settings' => __( 'Settings', 's2-wishlist' ),
									],
			];
			$this->print_tabs_nav( $args );

			$current_tab = 'report';
            if( ! empty( $_GET['tab'] ) ) {
            	$current_tab = sanitize_text_field( $_GET['tab'] );
        	}

			if( $current_tab == 'report' ) $this->show_wishlist_report_page();
			elseif( $current_tab == 'settings' ) $this->show_wishlist_setting_page();

		}

		/**
         * Print the tabs navigation
         *
         * @param array $args
         *
         * @since  1.0.0
         */
        public function print_tabs_nav( $args = [] ) {

            /**
             * @var string $admin_tabs
             * @var string $page
             */
            extract( $args );

            $current_tab = 'report';
            if( ! empty( $_GET['tab'] ) ) {
            	$current_tab = sanitize_text_field( $_GET['tab'] );
        	}

            $tabs = '';

            foreach ( $admin_tabs as $tab => $tab_value ) {

                $active_class  = ( $current_tab == $tab ) ? ' nav-tab-active' : '';

                $url = $this->get_nav_url( $tab, $page );

                $tabs .= '<a class="nav-tab' . $active_class . '" href="' . $url . '">' . $tab_value . '</a>';

            }

            echo '<h2 class="nav-tab-wrapper">' . $tabs .'</h2>';

        }

        /**
         * Get tab nav url
         *
         * @param string $tab
         *
         * @since  1.0.0
         */
        public function get_nav_url( $tab, $page ) {

            $url = "?page={$page}&tab={$tab}";
            $url = admin_url( "admin.php{$url}" );

            return $url;

        }

        /**
		 * Action Links
		 *
		 * add the action links to plugin admin page
		 *
		 * @param $links | links plugin array
		 *
		 * @since  1.0.0
		 *
		 * @return mixed | array
		 * @use    plugin_action_links_{$plugin_file_name}
		 */
		public function action_links( $links ) {

			$links = is_array( $links ) ? $links : [];

			$links[] = sprintf( '<a href="%s">%s</a>', admin_url( "admin.php?page=s2-wishlist&tab=settings" ), _x( 'Settings', 'Action links',  's2-wishlist' ) );

			return $links;

		}

		/**
		 * Show wishlist report admin page
		 *
		 * @since  1.0.3
		 */
		public function show_wishlist_report_page() {

			$report_list = new S2_Wishlist_Report_List();

			wc_get_template(
				'admin/report-page.php',
				[ 'report_list' => $report_list ],
				'',
				S2_WL_TEMPLATE_PATH . '/'
			);

		}

		/**
		 * Show wishlist setting admin page
		 *
		 * @since  1.0.3
		 */
		public function show_wishlist_setting_page() {

			$plugin_setting = S2_Wishlist_Plugin_Setting();

			wc_get_template(
				'admin/settings-page.php',
				[ 'plugin_setting' => $plugin_setting ],
				'',
				S2_WL_TEMPLATE_PATH . '/'
			);

		}

	}

}

/**
 * Unique access to instance of S2_Wishlist_Plugin_Panel class
 *
 * @return \S2_Wishlist_Plugin_Panel
 */
if ( is_admin() ) {
	S2_Wishlist_Plugin_Panel::get_instance();
}
