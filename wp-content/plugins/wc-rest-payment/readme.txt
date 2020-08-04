=== WC REST Payment ===
Contributors: jack50n9, sk8tech
Donate link: https://sk8.tech/donate?utm_source=readme&utm_medium=plugin&utm_campaign=wc-rest-payment
Tags: wc, woocommerce, woocommerce payment, rest api, json api, payment api, json, stripe, paypal, gateway
Requires at least: 4.7.0
Tested up to: 5.1
Requires PHP: 5.2.4
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
 
Process WooCommerce Payments using REST API. Supports Direct **PayPal Standard**, Bank Transfer, Cheque, Cash on Delivery and more.
 
== Description ==

WordPress and WooCommerce is awesome! Wouldn't it be even more awesome if leverage WooCommerce for mobile apps as well?

With REST API, you can already Create, Read, Update and Delete Posts, Pages, Products and Orders. The only key feature missing is being able to **Process WooCommerce Payments using REST API** via different gateways. 

If you are a front end developer, looking to develop an app/web with WordPress+WooCommerce as your backend using REST API. You will find that [WooCommerce docs does](http://woocommerce.github.io/woocommerce-rest-api-docs/) not provide the **process payment** endpoint. This is where WC REST Payment comes in.

With WC REST Payment, you can process WooCommerce Payments using REST API. Supports Direct **PayPal Standard**, Bank Transfer, Cheque, Cash on Delivery and more.

= Process Payment using REST API = 

1. Send a REST API `POST` Request to `/wp-json/wc/v2/process_payment`.
1. Include a JSON Body with `order_id` and `process_payment`.

Please read the [Getting Started Guide](https://docs.wc-rest-payment.com?utm_source=readme&utm_medium=plugin&utm_campaign=wc-rest-payment).

== WooCommerce Basic Gateways ==

WooCommerce Basic Gateways are supported by WC REST Payment.

* [**PayPal Standard**](https://docs.wc-rest-payment.com/gateways/paypal/?utm_source=readme&utm_medium=plugin&utm_campaign=wc-rest-payment)
* [Direct Bank Transfer](https://docs.wc-rest-payment.com/gateways/bacs/?utm_source=readme&utm_medium=plugin&utm_campaign=wc-rest-payment),
* [Cheque](https://docs.wc-rest-payment.com/gateways/cheque/?utm_source=readme&utm_medium=plugin&utm_campaign=wc-rest-payment)
* [Cash on Delivery](https://docs.wc-rest-payment.com/gateways/cod/?utm_source=readme&utm_medium=plugin&utm_campaign=wc-rest-payment)

== WooCommerce Addon Gateways ==

* **Stripe** ^
* **PayPal Checkout** ^
* More Coming Soon

^ Feature already working, Documentation is Coming Soon.

= Technical Support =

**SK8Tech - Customer Success Specialist** offers **Technical Support** to configure or install ***WP REST User***.

= Our Services =
 * [SK8Tech Sydney Web Design](https://sk8.tech/web-design/?utm_source=readme&utm_medium=plugin&utm_campaign=wc-rest-payment)
 * [SK8Tech Enterprise Email Hosting](https://sk8.tech/services/enterprise/email-hosting/?utm_source=readme&utm_medium=plugin&utm_campaign=wc-rest-payment)
 * [SK8Tech Emergency IT Support](https://sk8.tech/services/enterprise/it-support/emergency-support/?utm_source=readme&utm_medium=plugin&utm_campaign=wc-rest-payment)
 * [SK8Tech WeChat Advertising](https://sk8.tech/services/wechat/?utm_source=readme&utm_medium=plugin&utm_campaign=wc-rest-payment)

== Installation ==

1. Visit **Plugins** > **Add New**
1. **Search** for `WC REST Payment`
1. **Activate** WC REST Payment from your **Plugins** page
1. **Read** the documentation to [Get Started](https://docs.wc-rest-payment.com?utm_source=readme&utm_medium=plugin&utm_campaign=readme)

== Frequently Asked Questions ==

= Do you have a demo? =

Yes! You can check out our [Demo Shop](https://wc-rest-payment.com/demo?utm_source=readme&utm_medium=plugin&utm_campaign=readme).

If you need assistance, feel free to email us at wc-rest-payment@sk8.tech.

= What version of WordPress is required? =

For security, we always recommend the latest version of WordPress.
For native REST API support, we recommend you use WordPress 4.7+.
For WordPress version lower than 4.7, you will need to install [WordPress REST API (Version 2)](https://wordpress.org/plugins/rest-api/).

= What version of WooCommerce is required? =

WooCommerce v2.2+

= Is it secure? =

It is, if security is implemented properly.

Please see the [Security Documentation](https://docs.wc-rest-payment.com/security?utm_source=readme&utm_medium=plugin&utm_campaign=readme).

= There's a bug, what do I do? =

Please email us at wc-rest-payment@sk8.tech.

== Screenshots ==

1. An sample REST API POST request to process payment using [WC REST Payment](https://wordpress.org/plugins/wc-rest-payment/).

== Changelog ==

= 1.4.1 =

* Compatible & Safe to Update
* Security Fix
* Added Documentation

= 1.4.0 =

* Compatible & Safe to Update
* Added More Payment Gateways
* Direct Bank Transfer
* Cheque
* Cash on Delivery
* PayPal Standard
* Added API Endpoints

= 1.3.0 =

* Compatible & Safe to Update
* Added Dependency Check
* Improve Error Message
* Added API Endpoints
* Updated 'process_payment' code to latest

= 1.2.0 =

* Safe to Update
* Bug Fixes
* Added Plugin Analytics

= 1.1.0 =
* Added REST API endpoint for [PayPal Express Checkout](https://woocommerce.com/products/woocommerce-gateway-paypal-express-checkout/) Payment Gateway.
* Restructured plugin directory for future development.

= 1.0.0 =
* Initial Release.
* REST API endpoint for [Stripe](https://woocommerce.com/products/stripe/) Payment Gateway.

== Upgrade Notice ==

= 1.4.0 =

* The old `wc/v2/payment` REST API Endpoint has been removed from README,
* The old `wc/v2/payment` stop working in v2.0.
* New Docs can be found at https://docs.wc-rest-payment.com?utm_source=readme&utm_medium=plugin&utm_campaign=readme

= 1.3.0 = 

* The old `wc/v2/payment` REST API Endpoint is deprecated, and will be removed in v2.0. 
* Please start updating to new API Endpoint ASAP
 
== Contact Us ==

Based in Sydney, [SK8Tech](https://sk8.tech?utm_source=readme&utm_medium=plugin&utm_campaign=wc-rest-payment) is a innovative company providing IT services to SMEs, including [Web Design](https://sk8.tech/web-design?utm_source=readme&utm_medium=plugin&utm_campaign=wc-rest-payment), App Development and more.