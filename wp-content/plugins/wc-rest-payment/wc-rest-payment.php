<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://wc-rest-payment.com?utm_source=plugin&utm_medium=readme&utm_campaign=source-code
 * @since             1.1.0
 * @package           Wc_Rest_Payment
 *
 * @wordpress-plugin
 * Plugin Name:       WC REST Payment
 * Plugin URI:        https://wc-rest-payment.com?utm_source=plugin&utm_medium=readme&utm_campaign=source-code
 * Description:       WC REST Payment adds in the missing REST API endpoint for processing payment in WooCommerce.
 * Version:           1.4.1
 * Author:            SK8Tech
 * Author URI:        https://sk8.tech?utm_source=plugin&utm_medium=readme&utm_campaign=wc-rest-payment
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wc-rest-payment
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.1.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'WC_REST_PAYMENT_VERSION', '1.4.1' );

/**
 * Initialize Freemius SDK
 *
 * @link https://freemius.com/help/documentation/selling-with-freemius/integrating-freemius-sdk/ Freemius Intregration Guide
 * @since 1.2.0
 * @author Jacktator
 */
if ( ! function_exists( 'wrp_fs' ) ) {
	// Create a helper function for easy SDK access.
	function wrp_fs() {
		global $wrp_fs;

		if ( ! isset( $wrp_fs ) ) {
			// Include Freemius SDK.
			require_once dirname( __FILE__ ) . '/freemius/start.php';

			$wrp_fs = fs_dynamic_init( array(
				'id'             => '3363',
				'slug'           => 'wc-rest-payment',
				'type'           => 'plugin',
				'public_key'     => 'pk_815fcbb68bb0c1e6c0485597e8235',
				'is_premium'     => false,
				'has_addons'     => false,
				'has_paid_plans' => false,
				'menu'           => array(
					'first-path' => 'plugins.php',
				),
			) );
		}

		return $wrp_fs;
	}

	// Init Freemius.
	wrp_fs();
	// Signal that SDK was initiated.
	do_action( 'wrp_fs_loaded' );

	function wrp_fs_custom_connect_message_on_connect(
		$message,
		$user_first_name,
		$plugin_title,
		$user_login,
		$site_link,
		$freemius_link
	) {
		return sprintf(
			__( 'Hey %1$s' ) . ',<br>' .
			__( 'never miss an important update -- opt-in to our security and feature updates notifications, and non-sensitive diagnostic tracking.', 'wp-rest-filter' ),
			$user_first_name,
			'<b>' . $plugin_title . '</b>',
			'<b>' . $user_login . '</b>',
			$site_link,
			$freemius_link
		);
	}

	wrp_fs()->add_filter( 'connect_message', 'wrp_fs_custom_connect_message_on_connect', 10, 6 );

	function wrp_fs_custom_connect_message_on_update(
		$message,
		$user_first_name,
		$plugin_title,
		$user_login,
		$site_link,
		$freemius_link
	) {
		return sprintf(
			__( 'Hey %1$s' ) . ',<br>' .
			__( 'Please help us improve %2$s! If you opt-in, some data about your usage of %2$s will be sent. If you skip this, that\'s okay! %2$s will still work just fine.', 'wp-rest-filter' ),
			$user_first_name,
			'<b>' . $plugin_title . '</b>',
			'<b>' . $user_login . '</b>',
			$site_link,
			$freemius_link
		);
	}

	wrp_fs()->add_filter( 'connect_message_on_update', 'wrp_fs_custom_connect_message_on_update', 10, 6 );

	// Not like register_uninstall_hook(), you do NOT have to use a static function.
	wrp_fs()->add_action( 'after_uninstall', 'wrp_fs_uninstall_cleanup' );
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wc-rest-payment-activator.php
 */
function activate_wc_rest_payment() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wc-rest-payment-activator.php';
	Wc_Rest_Payment_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wc-rest-payment-deactivator.php
 */
function deactivate_wc_rest_payment() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wc-rest-payment-deactivator.php';
	Wc_Rest_Payment_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wc_rest_payment' );
register_deactivation_hook( __FILE__, 'deactivate_wc_rest_payment' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wc-rest-payment.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.1.0
 */
function run_wc_rest_payment() {

	$plugin = new Wc_Rest_Payment();
	$plugin->run();

}

run_wc_rest_payment();
