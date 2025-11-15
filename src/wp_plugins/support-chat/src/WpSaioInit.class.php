<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class WpSaioInit {

	private static $_instance   = null;
	private $main_menu_slug     = 'wp-support-all-in-one.php';
	private $admin_page_hookfix = '';

	public function __construct() {
		add_filter( 'plugin_action_links_' . WP_SAIO_BASE_NAME, array( $this, 'settings_link' ) );
		/*
		 * Load Text Domain
		 */
		add_action( 'init', array( $this, 'loadTextDomain' ) );

		/*
		 * Register Enqueue
		 */
		add_action( 'admin_enqueue_scripts', array( $this, 'registerAdminEnqueue' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'registerEnqueue' ) );

		/*
		 * Register Menu
		 */
		add_action( 'admin_menu', array( $this, 'registerAdminMenu' ) );

		/*
		 * Admin head
		 */
		add_action( 'admin_head', array( $this, 'removeAdminNotices' ), 999 );

		/*
		 * WP Footer
		 */
		add_action( 'wp_footer', array( $this, 'wpFooter' ) );

		/*
		 * Register Shortcode
		 */
		WpSaioShortcodes::instance();

		/*
		 * Register Ajax
		 */
		WpSaioAjax::instance();

		/*
		 * Register Helper
		 */
		WpSaioHelper::instance();

		add_action( 'admin_init', array( $this, 'registerSettings' ) );
	}
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	public function registerAdminEnqueue( $hook_suffix ) {
		if ( $hook_suffix !== $this->admin_page_hookfix ) {
			return;
		}
		wp_register_style( 'wp-saio', WP_SAIO_URL . '/assets/admin/css/wp-saio.css', array(), WP_SAIO_VERSION );
		wp_register_style( 'wp-saio-preview', WP_SAIO_URL . '/assets/home/css/wp-saio.css', array(), WP_SAIO_VERSION );
		wp_register_style( 'ui-range', WP_SAIO_URL . '/assets/admin/css/ui-range.css', array(), WP_SAIO_VERSION );
		wp_enqueue_style( 'wp-saio' );
		wp_enqueue_style( 'wp-saio-preview' );
		wp_style_add_data( 'wp-saio', 'rtl', 'replace' );
		wp_style_add_data( 'wp-saio-preview', 'rtl', 'replace' );
		wp_enqueue_style( 'ui-range' );

		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker');
		wp_enqueue_script( 'sortable', WP_SAIO_URL . '/assets/admin/js/Sortable.min.js', array(), '1.13.0', false );

		// Load our React app
		$asset_file = WP_SAIO_DIR . '/app/build/index.asset.php';

		if ( ! file_exists( $asset_file ) ) {
			return;
		}

		$asset = include $asset_file;
		wp_register_script( 'wp-saio', WP_SAIO_URL . '/app/build/index.js', $asset['dependencies'], $asset['version'], true );
		wp_enqueue_script( 'wp-saio' );
		
		// wp_register_script( 'wp-saio-preview', WP_SAIO_URL . '/assets/home/js/wp-saio.min.js', array(), WP_SAIO_VERSION, false );
		// wp_enqueue_script( 'wp-saio-preview' );
		wp_enqueue_media();

		wp_localize_script(
			'wp-saio',
			'wp_saio_object',
			array(
				'are_you_sure'          => esc_html__( 'Are you sure you want to remove this app. All data will be erase?', 'support-chat' ),
				'wp_saio_html_inputs'   => json_encode( WpSaio::renderForm() ),
				'add_media_text_title'  => esc_html__( 'Choose Image', 'support-chat' ),
				'add_media_text_button' => esc_html__( 'Choose Image', 'support-chat' ),
				'translate'             => array(
					// Footer texts
					'footerText'   => esc_html__( 'We need your support to keep updating and improving the plugin. Please,', 'support-chat' ),
					'reviewLink'   => esc_html__( 'help us by leaving a good review', 'support-chat' ),
					'thanks'       => esc_html__( 'Thanks!', 'support-chat' ),
					'thankYouText' => esc_html__( 'Thank you for using Support Chat from NinjaTeam', 'support-chat' ),
					
					// Choose Apps Tab
					'selectAppIcons' => esc_html__( 'Select app icons to add them to your list.', 'support-chat' ),
					'addNewApp' => esc_html__( 'Add New App', 'support-chat' ),
					'enterAppTitle' => esc_html__( 'Enter your app title', 'support-chat' ),
					'uploadIcon' => esc_html__( 'Upload Icon', 'support-chat' ),
					'yourAppIcon' => esc_html__( 'Your app icon/image', 'support-chat' ),
					'settingsSavedSuccess' => esc_html__( 'Settings saved successfully!', 'support-chat' ),
					'errorSavingSettings' => esc_html__( 'Error saving settings. Please try again.', 'support-chat' ),
					'saving' => esc_html__( 'Saving...', 'support-chat' ),
					'saveChanges' => esc_html__( 'Save Changes', 'support-chat' ),
					
					// Design Tab
					'settingStyleWidget' => esc_html__( 'Setting style for the floating widget.', 'support-chat' ),
					'enablePlugin' => esc_html__( 'Enable plugin', 'support-chat' ),
					'widgetPosition' => esc_html__( 'Widget position', 'support-chat' ),
					'left' => esc_html__( 'Left', 'support-chat' ),
					'right' => esc_html__( 'Right', 'support-chat' ),
					'style' => esc_html__( 'Style', 'support-chat' ),
					'redirect' => esc_html__( 'Redirect', 'support-chat' ),
					'popup' => esc_html__( 'Popup', 'support-chat' ),
					'tooltip' => esc_html__( 'Tooltip', 'support-chat' ),
					'appName' => esc_html__( 'App Name', 'support-chat' ),
					'appContent' => esc_html__( 'App Content', 'support-chat' ),
					'paddingFromBottom' => esc_html__( 'Padding from bottom', 'support-chat' ),
					'customIconAvatar' => esc_html__( 'Custom icon/avatar', 'support-chat' ),
					'chooseImage' => esc_html__( 'Choose Image', 'support-chat' ),
					'buttonStyle' => esc_html__( 'Button style', 'support-chat' ),
					'contain' => esc_html__( 'Contain', 'support-chat' ),
					'cover' => esc_html__( 'Cover', 'support-chat' ),
					'buttonColor' => esc_html__( 'Button color', 'support-chat' ),
					'designSettingsSavedSuccess' => esc_html__( 'Design settings saved successfully!', 'support-chat' ),
					'errorSavingDesignSettings' => esc_html__( 'Error saving settings. Please try again.', 'support-chat' ),
					
					// Display Tab
					'settingTextStyleWidget' => esc_html__( 'Setting text and style for the floating widget.', 'support-chat' ),
					'showOnDesktop' => esc_html__( 'Show on desktop', 'support-chat' ),
					'showOnMobile' => esc_html__( 'Show on mobile', 'support-chat' ),
					'display' => esc_html__( 'Display', 'support-chat' ),
					'showOnAllPages' => esc_html__( 'Show on all pages', 'support-chat' ),
					'showOnThesePages' => esc_html__( 'Show on these pages...', 'support-chat' ),
					'hideOnThesePages' => esc_html__( 'Hide on these pages...', 'support-chat' ),
					'all' => esc_html__( 'All', 'support-chat' ),
					'displaySettingsSavedSuccess' => esc_html__( 'Display settings saved successfully!', 'support-chat' ),
					'errorSavingDisplaySettings' => esc_html__( 'Error saving settings. Please try again.', 'support-chat' ),
					
					// Tabs
					'chooseApps' => esc_html__( 'Choose Apps', 'support-chat' ),
					'design' => esc_html__( 'Design', 'support-chat' ),
					'display' => esc_html__( 'Display', 'support-chat' ),
					
					// App.jsx
					'doYouNeedHelp' => esc_html__( 'Do you need help?', 'support-chat' ),
					'thanksUsingNinjaTeam' => __( 'Thanks for using NinjaTeam\'s Products!', 'support-chat' ),
					'contactSupport' => esc_html__( 'contact support', 'support-chat' ),
					'rateUs' => esc_html__( 'rate us', 'support-chat' ),
					'bestWishes' => esc_html__( 'Best wishes,', 'support-chat' ),
					'kellyFromNinjaTeam' => esc_html__( 'Kelly from NinjaTeam', 'support-chat' ),
					
					// Header
					'clickToChat' => esc_html__( 'Click to Chat', 'support-chat' ),
					'byNinjaTeam' => esc_html__( 'by NinjaTeam', 'support-chat' ),
					
					// Toast messages
					'close' => esc_html__( 'Close', 'support-chat' ),
				),
				'pages'                 => get_pages(),
				'style'                 => get_option( 'wpsaio_style' ),
				'ajax_url'              => admin_url( 'admin-ajax.php' ),
				'nonce'                 => wp_create_nonce( 'wpsaio_nonce' ),
				'add_icon_text_title'   => esc_html__( 'Choose Icon', 'support-chat' ),
				'add_icon_text_button'  => esc_html__( 'Choose Icon', 'support-chat' ),
				'is_reviewed'           => get_option( 'wpsaio_review_tracked', '0' ),
				'plugin_url'           => WP_SAIO_URL,
				'enablePlugin'          => get_option( 'wpsaio_enable_plugin', 1 ),
				'widgetPosition'        => get_option( 'wpsaio_widget_position', 'right' ),
				'tooltip'               => get_option( 'wpsaio_tooltip', 'appname' ),
				'bottomDistance'        => get_option( 'wpsaio_bottom_distance', 30 ),
				'buttonIcon'            => get_option( 'wpsaio_button_icon', '' ),
				'buttonImage'           => get_option( 'wpsaio_button_image', 'contain' ),
				'buttonColor'           => get_option( 'wpsaio_button_color', '' ),
				'showOnDesktop'         => get_option( 'wpsaio_show_on_desktop', 1 ),
				'showOnMobile'          => get_option( 'wpsaio_show_on_mobile', 1 ),
				'displayCondition'      => get_option( 'wpsaio_display_condition', 'allPages' ),
				'includePages'          => get_option( 'wpsaio_includes_pages', array() ),
				'excludePages'          => get_option( 'wpsaio_excludes_pages', array() ),
				//choose apps page
				'page_choose_apps' => [
					'apps' => WpSaio::defaultAppsWithCustomApps(),
					'app_order' => WpSaio::addedAppsOrder(),
				],
				'plugin_url' => WP_SAIO_URL,
			)
		);
	}
	public function registerEnqueue() {
		if ( ! $this->isActivePlugin() ) {
			return false;
		}
		wp_register_style( 'wp-saio', WP_SAIO_URL . '/assets/home/css/wp-saio.css', [], WP_SAIO_VERSION );
		wp_enqueue_style( 'wp-saio' );
		wp_style_add_data( 'wp-saio', 'rtl', 'replace' );

		wp_register_script( 'wp-saio', WP_SAIO_URL . '/assets/home/js/wp-saio.min.js', array( 'jquery' ), WP_SAIO_VERSION, false );
		wp_enqueue_script( 'wp-saio' );

		wp_localize_script(
			'wp-saio',
			'wp_saio_object',
			array(
				'style' => get_option( 'wpsaio_style' ),
			)
		);
	}
	public function loadTextDomain() {
		if ( function_exists( 'determine_locale' ) ) {
			$locale = determine_locale();
		} else {
			$locale = is_admin() ? get_user_locale() : get_locale();
		}
		unload_textdomain( 'support-chat' );
		load_textdomain( 'support-chat', WP_SAIO_DIR . '/languages/' . $locale . '.mo' );
		// load_plugin_textdomain( 'support-chat', false, WP_SAIO_DIR . '/languages' );
	}
	public function registerAdminMenu() {
		$page_title = esc_html__( 'Support Chat All In One', 'support-chat' );
		$menu_title = esc_html__( 'Click to Chat', 'support-chat' );

		$this->admin_page_hookfix = add_menu_page( $page_title, $menu_title, 'manage_options', $this->main_menu_slug, array( $this, 'wpSaioMenuCallBack' ), WP_SAIO_URL . '/assets/admin/img/support-icon.svg' );
	}
	// public function wpsaioLoadMainMenu() {
	// 	global $plugin_page;
	// 	$data = array();
	// 	if ( isset( $_POST['save-wp-saio'] ) && isset( $_POST['data'] ) ) {
	// 		$_data = WpSaioHelper::sanitize_array( $_POST['data'] );
	// 		foreach ( $_data as $k => $v ) {
	// 			$data[ $k ]['params'] = array();
	// 			foreach ( $v as $k2 => $v2 ) {
	// 				$data[ $k ]['params'][ $k2 ] = wp_unslash( trim( $v2 ) );
	// 			}
	// 		}
	// 		update_option( 'njt_wp_saio', $data );

	// 		wp_safe_redirect(
	// 			esc_url(
	// 				add_query_arg( array( 'page' => $this->main_menu_slug ), admin_url( 'admin.php' ) )
	// 			)
	// 		);
	// 	}
	// }
	public function removeAdminNotices() {
		$current_screen = get_current_screen();
		if( $current_screen->id !== 'toplevel_page_wp-support-all-in-one' ) {
			return;
		}
		remove_all_actions( 'admin_notices' );
        remove_all_actions( 'all_admin_notices' );
        remove_all_actions( 'user_admin_notices' );
        remove_all_actions( 'network_admin_notices' );
	}
	public function wpFooter() {
		if ( ! $this->isActivePlugin() ) {
			return;
		}
		$icon_bg_color = get_option( 'wpsaio_button_color', '' );
		$btn_icon      = get_option( 'wpsaio_button_icon', '' );
		$btn_image     = get_option( 'wpsaio_button_image', 'contain' );
		$data          = array(
			'buttons'       => WpSaio::generateFrontendButtons(),
			'contents'      => do_shortcode( implode( '', WpSaio::renderShortcodes() ) ),
			'icon_bg_color' => $icon_bg_color,
			'btn_icon'      => $btn_icon,
			'btn_image'     => $btn_image,
		);
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo WpSaioView::load( 'home.main', $data );
	}
	private function isActivePlugin() {
		return ( get_option( 'wpsaio_enable_plugin' ) == 1 );
	}
	public function wpSaioMenuCallBack() {
		?>
		<div id="wpsaio-react-root"></div>
		<?php
	}
	
	public function wpSaioMenuSettingsCallBack() {
		wp_enqueue_media();
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo WpSaioView::load( 'admin.settings' );
	}
	public function registerSettings() {
		register_setting( 'wpsaio', 'wpsaio_enable_plugin', array( 'type' => 'boolean', 'sanitize_callback' => 'sanitize_text_field' ) );
		register_setting( 'wpsaio', 'wpsaio_style', array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field' ) );
		register_setting( 'wpsaio', 'wpsaio_tooltip', array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field' ) );
		register_setting( 'wpsaio', 'wpsaio_widget_position', array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field' ) );
		register_setting( 'wpsaio', 'wpsaio_bottom_distance', array( 'type' => 'integer', 'sanitize_callback' => 'sanitize_text_field' ) );
		register_setting( 'wpsaio', 'wpsaio_button_icon', array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field' ) );
		register_setting( 'wpsaio', 'wpsaio_button_color', array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field' ) );
	}
	public static function activate() {
		$installed = get_option( 'wpsaio_enable_plugin' );

		if ( ! $installed ) {
			update_option( 'wpsaio_enable_plugin', 1 );
			update_option( 'wpsaio_style', 'redirect' );
			update_option( 'wpsaio_tooltip', 'appname' );
			update_option( 'wpsaio_widget_position', 'right' );
			update_option( 'wpsaio_button_image', 'contain' );
		}
	}

	public static function deactivate() {
	}

	public function settings_link( $link ) {
		// add custom link
		$setting_link = '<a href="admin.php?page=wp-support-all-in-one.php">Settings</a>';
		array_unshift( $link, $setting_link );
		return $link;
	}
}
