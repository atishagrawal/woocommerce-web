<?php

/**
 * This addon Adds the REST API endpoint for WooCommerce Payment via PayPal Express.
 *
 * @link       https://sk8.tech
 * @since      1.3.0
 *
 * @link https://woocommerce.com/products/woocommerce-gateway-paypal-checkout/ PayPal Express
 *
 * @deprecated
 *
 * @package    Wc_Rest_Payment
 * @subpackage Wc_Rest_Payment/includes/gateways
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Wc_Rest_Payment
 * @subpackage Wc_Rest_Payment/public
 * @author     SK8Tech <support@sk8.tech>
 */
class Deprecated_Wc_Rest_Payment_PPEC {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register REST API route
	 *
	 * @since 1.3.0
	 * @link https://developer.wordpress.org/rest-api/extending-the-rest-api/routes-and-endpoints/
	 * @author Jacktator
	 */
	public function register_route() {
		/**
		 * Handle Process Payment request.
		 */
		register_rest_route('wc/v2', 'payment/paypal-express', array(
			'methods' => 'POST',
			'callback' => array($this, 'process_payment'),
		));
	}

	/**
	 * Get the payment token and order id in the request body and Process Payment
	 *
	 * @author Jack
	 *
	 * @since    1.1.0
	 *
	 * @param [type] $request [description]
	 *
	 * @return [type] [description]
	 */
	public function wc_rest_process_payment($request = null) {

		// Create a Response Object
		$response = array();

		// Get JSON parameters
		$parameters = $request->get_json_params();
		$order_id = sanitize_text_field($parameters['order_id']);
		$ppec_token = sanitize_text_field($parameters['ppec_token']);
		$error = new WP_Error();

		// Perform Pre Checks
		if (!class_exists('WooCommerce')) {
			$error->add(400, __("Payment Failed. WooCommerce either missing or deactivated.", 'wc-rest-payment'), array('status' => 400));
			return $error;
		}
		if (empty($order_id)) {
			$error->add(401, __("Payment Failed. Order ID 'order_id' is required.", 'wc-rest-payment'), array('status' => 400));
			return $error;
		} else if (wc_get_order($order_id) == false) {
			$error->add(402, __("Payment Failed. Order ID 'order_id' is invalid. Order does not exist.", 'wc-rest-payment'), array('status' => 400));
			return $error;
		} else if (wc_get_order($order_id)->get_status() !== 'pending' && wc_get_order($order_id)->get_status() !== 'failed') {
			$error->add(403, __("Payment Failed. Order status is Neither 'pending' nor 'failed', meaning order had already received a successful payment. Multiple payment to the same order is not allowed. ", 'wc-rest-payment'), array('status' => 400));
			return $error;
		}
		if (empty($ppec_token)) {
			$error->add(404, __("Payment Failed. Payment Token 'ppec_token' is required.", 'wc-rest-payment'), array('status' => 400));
			return $error;
		}
		if (get_current_user_id() == 0) {
			$error->add(405, __("Payment Failed. A Logged in User is required.", 'wc-rest-payment'), array('status' => 400));
			return $error;
		}

		// Add More Params to Requests
		$_POST['payment_method'] = 'paypal_express';

		// Process Payment
		/**
		 * @link https://github.com/woocommerce/woocommerce-gateway-paypal-express-checkout/blob/master/includes/class-wc-gateway-ppec-with-spb.php#L47 process_payment function
		 * @author Jack
		 */
		if (!class_exists('WC_Gateway_PPEC')) {
			$error->add(406, __("Failed to process payment. WooCommerce PayPal Express Checkout Gateway either missing or deactivated.", 'wc-rest-payment'), array('status' => 400));
			return $error;
		} else {
			// if ( isset( $_POST['payerID'] ) && isset( $_POST['paymentToken'] ) ) {
			$session = WC()->session->get('paypal');

			$session->checkout_completed = true;
			$session->payer_id = get_current_user_id();
			$session->token = $ppec_token;

			WC()->session->set('paypal', $session);
			// }

			$payment_result = WC_Gateway_PPEC::process_payment($order_id);
			if ($payment_result['result'] === "success") {
				$response['code'] = 200;
				$response['message'] = __("Payment Successful.", "wc-rest-payment");
				$response['result'] = $payment_result;
			} else {
				return new WP_Error('payment_error', 'PayPal Payment Failed, Check WooCommerce or PayPal for further information.', array('status' => 400));
			}
			// Return Successful Response
			return new WP_REST_Response($response, 200);
		}

	}

}
