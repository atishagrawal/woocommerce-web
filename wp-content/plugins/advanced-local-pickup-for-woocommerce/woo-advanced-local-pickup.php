<?php
/*
* Plugin Name: Advanced Local Pickup for WooCommerce
* Plugin URI:  https://www.zorem.com/shop
* Description: Local Pickup is a shipping method for WooCommerce that allows customers to choose to pick up the order from your stroe. WooCommerce uses the standard order flow when the local pickup shipping method has been selected. 
* Author: zorem
* Author URI: https://www.zorem.com/
* Version: 1.1.4
* Text Domain: advanced-local-pickup-for-woocommerce
* Domain Path: /lang/
* WC requires at least: 4.0
* WC tested up to: 4.3.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Woocommerce_Local_Pickup {
	
	/**
	 * Local Pickup for WooCommerce
	 *
	 * @var string
	 */
	public $version = '1.1.4';
	
	/**
	 * Constructor
	 *
	 * @access public
	 * @since  1.0.0
	*/
	public function __construct() {

		// Check if Wocoomerce is activated
		if ( $this->is_wc_active() ) {
			$this->includes();
			$this->init();			
			add_action( 'plugins_loaded', array( $this, 'on_plugins_loaded' ) );
		}
		add_action( 'admin_footer', array( $this, 'uninstall_notice') );
	}
	
	/**
	 * Check if WooCommerce is active
	 *
	 * @access private
	 * @since  1.0.0
	 * @return bool
	*/
	private function is_wc_active() {
		
		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
		}
		if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			$is_active = true;
		} else {
			$is_active = false;
		}		

		// Do the WC active check
		if ( false === $is_active ) {
			add_action( 'admin_notices', array( $this, 'notice_activate_wc' ) );
		}		
		return $is_active;
	}
	
	/**
	 * Display WC active notice
	 *
	 * @access public
	 * @since  1.0.0
	*/
	public function notice_activate_wc() {
		?>
		<div class="error">
			<p><?php printf( __( 'Please install and activate %sWooCommerce%s for WC local pickup to work!', 'advanced-local-pickup-for-woocommerce' ), '<a href="' . admin_url( 'plugin-install.php?tab=search&s=WooCommerce&plugin-search-input=Search+Plugins' ) . '">', '</a>' ); ?></p>
		</div>
		<?php
	}
	
	/**
	 * Include plugin file.
	 *
	 * @since 1.0.0
	 *
	 */	
	function includes() {		
		require_once $this->get_plugin_path() . '/include/wc-local-pickup-admin.php';
		$this->admin = WC_Local_Pickup_admin::get_instance();		
	}

	/**
	 * Initialize plugin
	 *
	 * @access private
	 * @since  1.0.0
	*/
	private function init() {
		
		// Load plugin textdomain
		add_action('plugins_loaded', array($this, 'load_textdomain'));
		
		//load javascript in admin
		add_action('admin_enqueue_scripts', array( $this, 'alp_script_enqueue' ) );
		
		//callback for add action link for plugin page	
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ),  array( $this , 'my_plugin_action_links' ));			
		
		// Add to custom email for WC Order statuses
		add_filter( 'woocommerce_email_classes', array( $this, 'custom_init_emails' ) );
		add_action( 'woocommerce_order_status_ready-pickup', array( $this, 'email_trigger_ready_pickup' ), 10, 2 );
		add_action( 'woocommerce_order_status_pickup', array( $this, 'email_trigger_pickup' ), 10, 2 );
	}
	
	/*
	* include file on plugin load
	*/
	public function on_plugins_loaded() {		
		require_once $this->get_plugin_path() . '/include/customizer/wclp-customizer.php';				
		require_once $this->get_plugin_path() . '/include/customizer/wc-ready-pickup-email-customizer.php';
		require_once $this->get_plugin_path() . '/include/customizer/wc-pickup-email-customizer.php';					
	}
	
	/*
	* load text domain
	*/
	public function load_textdomain(){
		load_plugin_textdomain( 'advanced-local-pickup-for-woocommerce', false, plugin_dir_path( plugin_basename(__FILE__) ) . 'lang/' );
	}
	
	/**
	 * Gets the absolute plugin path without a trailing slash, e.g.
	 * /path/to/wp-content/plugins/plugin-directory.
	 *
	 * @return string plugin path
	 */
	public function get_plugin_path() {
		if ( isset( $this->plugin_path ) ) {
			return $this->plugin_path;
		}

		$this->plugin_path = untrailingslashit( plugin_dir_path( __FILE__ ) );
		return $this->plugin_path;
	}
	
	public static function get_plugin_domain(){
		return __FILE__;
	}
	
	/*
	* plugin file directory function
	*/	
	public function plugin_dir_url(){
		return plugin_dir_url( __FILE__ );
	}
	
	/**
	 * Add plugin action links.
	 *
	 * Add a link to the settings page on the plugins.php page.
	 *
	 * @since 1.0.0
	 *
	 * @param  array  $links List of existing plugin action links.
	 * @return array         List of modified plugin action links.
	 */
	function my_plugin_action_links( $links ) {
		$links = array_merge( array(
			'<a href="' . esc_url( admin_url( '/admin.php?page=local_pickup' ) ) . '">' . __( 'Settings', 'woocommerce' ) . '</a>'
		), $links );
		return $links;
	}
	
	/*
	* Add admin javascript
	*/	
	public function alp_script_enqueue() {
		
		
		// Add condition for css & js include for admin page  
		if(!isset($_GET['page'])) {
				return;
		}
		if(  $_GET['page'] != 'local_pickup') {
			return;
		}
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';	
		// Add the color picker css file       
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );
			
		// Add the WP Media 
		wp_enqueue_media();
		
		// Add tiptip js and css file
		wp_register_style( 'woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css', array(), WC_VERSION );
		wp_enqueue_style( 'woocommerce_admin_styles' );
	
		wp_register_script( 'jquery-tiptip', WC()->plugin_url() . '/assets/js/jquery-tiptip/jquery.tipTip.min.js', array( 'jquery' ), WC_VERSION, true );
		wp_register_script( 'jquery-blockui', WC()->plugin_url() . '/assets/js/jquery-blockui/jquery.blockUI' . $suffix . '.js', array( 'jquery' ), '2.70', true );
		wp_enqueue_script( 'jquery-tiptip' );
		wp_enqueue_script( 'jquery-blockui' );
		
		wp_enqueue_script( 'alp-admin-js', plugin_dir_url(__FILE__) . 'assets/js/admin.js', array(), $this->version );
		wp_enqueue_script( 'alp-material-min-js', plugin_dir_url(__FILE__) . 'assets/js/material.min.js', array(), $this->version );
		wp_enqueue_style( 'alp-admin-css', plugin_dir_url(__FILE__) . 'assets/css/admin.css', array(), $this->version );
		wp_enqueue_style( 'alp-material-css', plugin_dir_url(__FILE__) . 'assets/css/material.css', array(), $this->version );
	}
	
	// Add to custom email for WC Order statuses
	public function custom_init_emails( $emails ) {

		// Include the email class file if it's not included already
		if (!defined('WC_LOCAL_PICKUP_TEMPLATE_PATH')) define('WC_LOCAL_PICKUP_TEMPLATE_PATH', wc_local_pickup()->get_plugin_path() . '/templates/');
		
		if ( ! isset( $emails[ 'WC_Email_Customer_Ready_Pickup_Order' ] ) ) {
	        $emails[ 'WC_Email_Customer_Ready_Pickup_Order' ] = include_once( 'include/emails/ready-pickup-order.php' );
	    }
	    if ( ! isset( $emails[ 'WC_Email_Customer_Pickup_Order' ] ) ) {
	        $emails[ 'WC_Email_Customer_Pickup_Order' ] = include_once( 'include/emails/pickup-order.php' );
	    }
	
	    return $emails;		
	}
	
	/**
	 * Send email when order status change to "pickuped"
	 *
	*/
	public function email_trigger_ready_pickup($order_id, $order = false){						
		WC()->mailer()->emails['WC_Email_Customer_Ready_Pickup_Order']->trigger( $order_id, $order );
	}
	
	/**
	 * Send email when order status change to "pickuped"
	 *
	*/
	public function email_trigger_pickup($order_id, $order = false){		
		WC()->mailer()->emails['WC_Email_Customer_Pickup_Order']->trigger( $order_id, $order );
	}
	
	/*
	* Plugin uninstall code 
	*/	
	public function uninstall_notice(){
		wp_enqueue_style( 'alp-admin-js',  plugin_dir_url(__FILE__) . 'assets/css/admin.css', array(), $this->version );
		?>
		<script>
		jQuery(document).on("click","[data-slug='advanced-local-pickup-for-woocommerce'] .deactivate a",function(e){			
			e.preventDefault();
			jQuery('.alp_uninstall_popup').show();
			var theHREF = jQuery(this).attr("href");
			jQuery(document).on("click",".alp_uninstall_plugin",function(e){
				window.location.href = theHREF;
			});			
		});
		jQuery(document).on("click",".alp_popupclose",function(e){
			jQuery('.alp_uninstall_popup').hide();
		});
		jQuery(document).on("click",".alp_uninstall_close",function(e){
			jQuery('.alp_uninstall_popup').hide();
		});
		
		</script>
		<div id="" class="alp_popupwrapper alp_uninstall_popup" style="display:none;">
			<div class="alp_popuprow" style="text-align: left;max-width: 380px;">
				<h3 class="alp_popup_title">Advanced Local Pickup for WooCommerce</h2>
				<p><?php echo sprintf(__('<strong>Note:</strong> - If you use the custom order status, when you deactivate the plugin, you must register the order status, otherwise these orders will not display on your orders admin. You can find more information and the code <a href="%s" target="blank">snippet</a> to use in functions.php here.', 'advanced-local-pickup-for-woocommerce'), 'https://www.zorem.com/docs/advanced-local-pickup-for-woocommerce/plugin-settings/#code-snippets'); ?></p>
				<p class="" style="text-align:left;">	
					<input type="button" value="Uninstall" class="alp_uninstall_plugin button-primary">
					<input type="button" value="Close" class="alp_uninstall_close button-primary">				
				</p>
			</div>
			<div class="alp_popupclose"></div>
		</div>		
	<?php }
}

/**
 * Returns an instance of zorem_woocommerce_advanced_salse_report_email.
 *
 * @since 1.6.5
 * @version 1.6.5
 *
 * @return zorem_woocommerce_advanced_salse_report_email
*/
function wc_local_pickup() {
	static $instance;

	if ( ! isset( $instance ) ) {		
		$instance = new Woocommerce_local_pickup();
	}

	return $instance;
}

/**
 * Register this class globally.
 *
 * Backward compatibility.
*/
wc_local_pickup();