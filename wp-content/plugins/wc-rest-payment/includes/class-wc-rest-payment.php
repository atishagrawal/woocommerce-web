<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://sk8.tech
 * @since      1.1.0
 *
 * @package    Wc_Rest_Payment
 * @subpackage Wc_Rest_Payment/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.1.0
 * @package    Wc_Rest_Payment
 * @subpackage Wc_Rest_Payment/includes
 * @author     SK8Tech <support@sk8.tech>
 */
class Wc_Rest_Payment {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.1.0
	 * @access   protected
	 * @var      Wc_Rest_Payment_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.1.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.1.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.1.0
	 */
	public function __construct() {
		if (defined('WC_REST_PAYMENT_VERSION')) {
			$this->version = WC_REST_PAYMENT_VERSION;
		} else {
			$this->version = '1.1.0';
		}
		$this->plugin_name = 'wc-rest-payment';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Wc_Rest_Payment_Loader. Orchestrates the hooks of the plugin.
	 * - Wc_Rest_Payment_i18n. Defines internationalization functionality.
	 * - Wc_Rest_Payment_Admin. Defines all hooks for the admin area.
	 * - Wc_Rest_Payment_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.1.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for loading third party plugin as dependency
		 *
		 * @link http://tgmpluginactivation.com/ TGM Plugin Activation
		 * @author Jacktator
		 * @since 1.3.0
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/plugins/class-tgm-plugin-activation.php';

		/**
		 * The class responsible for loading payment gateways
		 *
		 * @author Jacktator
		 * @since 1.3.0
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/gateways/class-wc-rest-gateway-bacs.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/gateways/class-wc-rest-gateway-cheque.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/gateways/class-wc-rest-gateway-cod.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/gateways/class-wc-rest-gateway-paypal.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/gateways/class-wc-rest-payment-stripe.php'; // Deprecated. Remove in 2.0.
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/gateways/class-wc-rest-payment-paypal-express.php'; // Deprecated. Remove in 2.0.

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-wc-rest-payment-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-wc-rest-payment-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-wc-rest-payment-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-wc-rest-payment-public.php';

		$this->loader = new Wc_Rest_Payment_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Wc_Rest_Payment_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.1.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Wc_Rest_Payment_i18n();

		$this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.1.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Wc_Rest_Payment_Admin($this->get_plugin_name(), $this->get_version());

		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');

		// Load Plugin Dependencies
		$this->loader->add_action('tgmpa_register', $plugin_admin, 'wc_rest_payment_register_required_plugins');

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.1.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Wc_Rest_Payment_Public($this->get_plugin_name(), $this->get_version());

		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');

		$wrp_gateways = Array();

		// Payment Gateway via Direct Bank Transfer
		$wrp_gateway_bacs = new Wc_REST_Gateway_BACS();
		array_push($wrp_gateways, $wrp_gateway_bacs);

		// Payment Gateway via Cheque
		$wrp_gateway_cheque = new WC_REST_Gateway_Cheque();
		array_push($wrp_gateways, $wrp_gateway_cheque);

		// Payment Gateway via Cash on Delivery
		$wrp_gateway_cod = new WC_REST_Gateway_COD();
		array_push($wrp_gateways, $wrp_gateway_cod);

		// Payment Gateway via PayPal Standard
		$wrp_gateway_paypal = new WC_REST_Gateway_PayPal();
		array_push($wrp_gateways, $wrp_gateway_paypal);

		// Get Additoinal Gateways from Addons
		$wrp_gateways = apply_filters("wrp_addon_gateways", $wrp_gateways);

		// Register Payment Endpoint for all Gateways
		foreach ($wrp_gateways as &$wrp_gateway) {
			$this->loader->add_filter('wrp_pre_process_' . $wrp_gateway->gateway_id . '_payment', $wrp_gateway, 'wrp_pre_process_payment');
			// TODO: do_action();
		}

		// Register REST API Route
		$this->loader->add_action('rest_api_init', $plugin_public, 'wrp_register_rest_api_route');

		// Deprecated: Register Payment Endpoint via Stripe. To be remved in v2.0
		$wrp_gateway_stripe = new Deprecated_Wc_Rest_Payment_Stripe($this->get_plugin_name(), $this->get_version());
		$this->loader->add_action('rest_api_init', $wrp_gateway_stripe, 'register_route');

		// Deprecated: Register Payment Endpoint via PayPal Express. To be remved in v2.0
		$wrp_gateway_paypal_express = new Deprecated_Wc_Rest_Payment_PPEC($this->get_plugin_name(), $this->get_version());
		$this->loader->add_action('rest_api_init', $wrp_gateway_paypal_express, 'register_route');

		// Deprecated: Adds REST API Route for Process Payment
		/**
		 * Deprecated. To be remved in v2.0
		 *
		 * @deprecated
		 *
		 * @author Jacktator
		 * @since 1.1.0
		 */
		$this->loader->add_action('rest_api_init', $plugin_public, 'add_api_routes');

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.1.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.1.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.1.0
	 * @return    Wc_Rest_Payment_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.1.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
