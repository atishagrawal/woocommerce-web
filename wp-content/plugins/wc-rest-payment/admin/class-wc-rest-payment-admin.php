<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://sk8.tech
 * @since      1.1.0
 *
 * @package    Wc_Rest_Payment
 * @subpackage Wc_Rest_Payment/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wc_Rest_Payment
 * @subpackage Wc_Rest_Payment/admin
 * @author     SK8Tech <support@sk8.tech>
 */
class Wc_Rest_Payment_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.1.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.1.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.1.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.1.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wc_Rest_Payment_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wc_Rest_Payment_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/wc-rest-payment-admin.css', array(), $this->version, 'all');

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.1.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wc_Rest_Payment_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wc_Rest_Payment_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/wc-rest-payment-admin.js', array('jquery'), $this->version, false);

	}

	/**
	 * Register the required plugins for this theme.
	 *
	 * In this example, we register five plugins:
	 * - one included with the TGMPA library
	 * - two from an external source, one from an arbitrary source, one from a GitHub repository
	 * - two from the .org repo, where one demonstrates the use of the `is_callable` argument
	 *
	 * The variables passed to the `tgmpa()` function should be:
	 * - an array of plugin arrays;
	 * - optionally a configuration array.
	 * If you are not changing anything in the configuration array, you can remove the array and remove the
	 * variable from the function call: `tgmpa( $plugins );`.
	 * In that case, the TGMPA default settings will be used.
	 *
	 * This function is hooked into `tgmpa_register`, which is fired on the WP `init` action on priority 10.
	 */
	function wc_rest_payment_register_required_plugins() {
		/*
			 * Array of plugin arrays. Required keys are name and slug.
			 * If the source is NOT from the .org repo, then source is also required.
		*/
		$plugins = array(

			// This is an example of how to include a plugin bundled with a theme.
			// array(
			// 	'name' => 'TGM Example Plugin', // The plugin name.
			// 	'slug' => 'tgm-example-plugin', // The plugin slug (typically the folder name).
			// 	'source' => dirname(__FILE__) . '/includes/plugins/tgm-example-plugin.zip', // The plugin source.
			// 	'required' => true, // If false, the plugin is only 'recommended' instead of required.
			// 	'version' => '', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
			// 	'force_activation' => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
			// 	'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
			// 	'external_url' => '', // If set, overrides default API URL and points to an external URL.
			// 	'is_callable' => '', // If set, this callable will be be checked for availability to determine if a plugin is active.
			// ),

			// This is an example of how to include a plugin from an arbitrary external source in your theme.
			// array(
			// 	'name' => 'TGM New Media Plugin', // The plugin name.
			// 	'slug' => 'tgm-new-media-plugin', // The plugin slug (typically the folder name).
			// 	'source' => 'https://s3.amazonaws.com/tgm/tgm-new-media-plugin.zip', // The plugin source.
			// 	'required' => true, // If false, the plugin is only 'recommended' instead of required.
			// 	'external_url' => 'https://github.com/thomasgriffin/New-Media-Image-Uploader', // If set, overrides default API URL and points to an external URL.
			// ),

			// This is an example of how to include a plugin from the WordPress Plugin Repository.
			array(
				'name' => 'WooCommerce',
				'slug' => 'woocommerce',
				'required' => true,
			),

			// This is an example of how to include a plugin from a GitHub repository in your theme.
			// This presumes that the plugin code is based in the root of the GitHub repository
			// and not in a subdirectory ('/src') of the repository.
			// array(
			// 	'name' => 'WooCommerce Stripe Payment Gateway',
			// 	'slug' => 'woocommerce-gateway-stripe',
			// 	'required' => false,
			// ),

			// This is an example of how to include a plugin from a GitHub repository in your theme.
			// This presumes that the plugin code is based in the root of the GitHub repository
			// and not in a subdirectory ('/src') of the repository.
			// array(
			// 	'name' => 'WooCommerce PayPal Checkout Payment Gateway',
			// 	'slug' => 'woocommerce-gateway-paypal-express-checkout',
			// 	'required' => false,
			// ),

			// This is an example of the use of 'is_callable' functionality. A user could - for instance -
			// have WPSEO installed *or* WPSEO Premium. The slug would in that last case be different, i.e.
			// 'wordpress-seo-premium'.
			// By setting 'is_callable' to either a function from that plugin or a class method
			// `array( 'class', 'method' )` similar to how you hook in to actions and filters, TGMPA can still
			// recognize the plugin as being installed.
			// array(
			// 	'name' => 'WordPress SEO by Yoast',
			// 	'slug' => 'wordpress-seo',
			// 	'is_callable' => 'wpseo_init',
			// ),

		);

		/*
			 * Array of configuration settings. Amend each line as needed.
			 *
			 * TGMPA will start providing localized text strings soon. If you already have translations of our standard
			 * strings available, please help us make TGMPA even better by giving us access to these translations or by
			 * sending in a pull-request with .po file(s) with the translations.
			 *
			 * Only uncomment the strings in the config array if you want to customize the strings.
		*/
		$config = array(
			'id' => 'wc-rest-payment', // Unique ID for hashing notices for multiple instances of TGMPA.
			'default_path' => '', // Default absolute path to bundled plugins.
			'menu' => 'tgmpa-install-plugins', // Menu slug.
			'parent_slug' => 'plugins.php', // Parent menu slug.
			'capability' => 'manage_options', // Capability needed to view plugin install page, should be a capability associated with the parent menu used.
			'has_notices' => true, // Show admin notices or not.
			'dismissable' => true, // If false, a user cannot dismiss the nag message.
			'dismiss_msg' => '', // If 'dismissable' is false, this message will be output at top of nag.
			'is_automatic' => false, // Automatically activate plugins after installation or not.
			'message' => '', // Message to output right before the plugins table.

			/*
				'strings'      => array(
					'page_title'                      => __( 'Install Required Plugins', 'wc-rest-payment' ),
					'menu_title'                      => __( 'Install Plugins', 'wc-rest-payment' ),
					/* translators: %s: plugin name. * /
					'installing'                      => __( 'Installing Plugin: %s', 'wc-rest-payment' ),
					/* translators: %s: plugin name. * /
					'updating'                        => __( 'Updating Plugin: %s', 'wc-rest-payment' ),
					'oops'                            => __( 'Something went wrong with the plugin API.', 'wc-rest-payment' ),
					'notice_can_install_required'     => _n_noop(
						/* translators: 1: plugin name(s). * /
						'This theme requires the following plugin: %1$s.',
						'This theme requires the following plugins: %1$s.',
						'wc-rest-payment'
					),
					'notice_can_install_recommended'  => _n_noop(
						/* translators: 1: plugin name(s). * /
						'This theme recommends the following plugin: %1$s.',
						'This theme recommends the following plugins: %1$s.',
						'wc-rest-payment'
					),
					'notice_ask_to_update'            => _n_noop(
						/* translators: 1: plugin name(s). * /
						'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.',
						'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.',
						'wc-rest-payment'
					),
					'notice_ask_to_update_maybe'      => _n_noop(
						/* translators: 1: plugin name(s). * /
						'There is an update available for: %1$s.',
						'There are updates available for the following plugins: %1$s.',
						'wc-rest-payment'
					),
					'notice_can_activate_required'    => _n_noop(
						/* translators: 1: plugin name(s). * /
						'The following required plugin is currently inactive: %1$s.',
						'The following required plugins are currently inactive: %1$s.',
						'wc-rest-payment'
					),
					'notice_can_activate_recommended' => _n_noop(
						/* translators: 1: plugin name(s). * /
						'The following recommended plugin is currently inactive: %1$s.',
						'The following recommended plugins are currently inactive: %1$s.',
						'wc-rest-payment'
					),
					'install_link'                    => _n_noop(
						'Begin installing plugin',
						'Begin installing plugins',
						'wc-rest-payment'
					),
					'update_link' 					  => _n_noop(
						'Begin updating plugin',
						'Begin updating plugins',
						'wc-rest-payment'
					),
					'activate_link'                   => _n_noop(
						'Begin activating plugin',
						'Begin activating plugins',
						'wc-rest-payment'
					),
					'return'                          => __( 'Return to Required Plugins Installer', 'wc-rest-payment' ),
					'plugin_activated'                => __( 'Plugin activated successfully.', 'wc-rest-payment' ),
					'activated_successfully'          => __( 'The following plugin was activated successfully:', 'wc-rest-payment' ),
					/* translators: 1: plugin name. * /
					'plugin_already_active'           => __( 'No action taken. Plugin %1$s was already active.', 'wc-rest-payment' ),
					/* translators: 1: plugin name. * /
					'plugin_needs_higher_version'     => __( 'Plugin not activated. A higher version of %s is needed for this theme. Please update the plugin.', 'wc-rest-payment' ),
					/* translators: 1: dashboard link. * /
					'complete'                        => __( 'All plugins installed and activated successfully. %1$s', 'wc-rest-payment' ),
					'dismiss'                         => __( 'Dismiss this notice', 'wc-rest-payment' ),
					'notice_cannot_install_activate'  => __( 'There are one or more required or recommended plugins to install, update or activate.', 'wc-rest-payment' ),
					'contact_admin'                   => __( 'Please contact the administrator of this site for help.', 'wc-rest-payment' ),

					'nag_type'                        => '', // Determines admin notice type - can only be one of the typical WP notice classes, such as 'updated', 'update-nag', 'notice-warning', 'notice-info' or 'error'. Some of which may not work as expected in older WP versions.
				),
			*/
		);

		tgmpa($plugins, $config);
	}

}
