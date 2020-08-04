=== Advanced Shipment Tracking for WooCommerce  ===
Contributors: zorem
Tags: WooCommerce, delivery, shipping, shipment tracking, tracking
Requires at least: 5.0
Tested up to: 5.4
Requires PHP: 7.0
Stable tag: 4.0.1
License: GPLv2 
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add shipment tracking information to your WooCommerce orders and provide your customers with an easy way to track their orders. 

== Description ==

AST is easy to set up, manage and maintain,the plugin provides powerful features that allow WooCommerce stores to better manage and automate their post-shipping workflows, increase productivity and provide their customers with a superior post-purchase experience.

AST allows store admins to add tracking information to orders from the WooCommerce orders admin, with a CSV file importer, with the Shipment Tracking Rest API endpoint or by providing built in compatibility with other plugins and shipping services. AST lets you set your preferred shipping providers from a list of 250+ shipping providers (carriers), each with a pre-set tracking link. You can fully customize the display of the tracking information on the order emails and on customer accounts. Create custom order Statuses and more..

https://www.youtube.com/watch?v=QOVbwfgXQdU

==Key Features==

* **Add Shipment Tracking Information to Orders**
Easily add shipment tracking information to your orders from the main orders admin page or from a single order admin page.
* **Bulk Upload from CSV**
AST Provides an easy interface to import tracking information into multiple orders from a CSV file.

* **REST API Shipment Tracking Endpoint**
Full support for the WooCommerce REST API to update the tracking information in your orders from external shipping and drop-shipping services.

* **Streamline your Post-Shipping Workflows**
AST lets you set Custom Order Statuses to better manage your orders, Set custom order status to Delivered, Partially Shipped, or Updated Tracking, depending on your fulfilment process. You can enable a customizable emails for the custom order statuses

* **Shipping Providers List**
Select your preferred shipping provider from a list of 250+ shipping providers with a predefined tracking link, you can enable/disable providers, edit the shipping providers names and images in case you want to white label the display for customers you can also, set a default provider when adding tracking information to order, add custom shipping providers and more..

* **Sync Shipping Providers List**
You can sync the shipping providers list  sync the list with TrackShip API to keep your list UP-To-Date with the latest changes in the shipping providers information and tracking link.

* **Customize The Tracking Info Display**
Take full control of the tracking display on the order emails and customer account, customize the layout, texts, fonts, colors, and more.

* **Premium Add-ons**
AST offers premium add-ons to save you time on customer inquiries and increase productivity:

== PREMIUM ADD-ONS ==

**Tracking Per Item Add-on** - The Tracking per item add-on allows you to attach tracking numbers to specific order items and also to attach tracking numbers to different quantities of the same line item. [Get this Add-on](https://www.zorem.com/shop/tracking-per-item-ast-add-on/)

**TrackShip Integration** -  [TrackShip](https://trackship.info/) is a multi-carrier shipment tracking API that fully integrates into WooCommerce with the AST plugin. TrackShip automates the orders workflow, reduces customer inquiries and time spent on customer service, and keeps your customers informed on their shipment status at all times.

* Auto-track shipped orders with 200+ shipping providers
* Up-to-date shipment status and est. delivery date on your orders admin
* Automatically change the order status to Delivered once it’s delivered to your customers
* Send shipment status update emails to notify your customers when their shipments are Out For Delivery, Delivered or have an exception
* Direct customers to a Tracking Page on your store

You must have a [TrackShip](https://trackship.info/) account to activate these advanced features.

== Compatibility == 
The Advanced Shipment Tracking plugin is compatible with many other plugins such as shipping label plugins and services, email customizer plugins, Customer order number plugins, PDF invoices plugins,  multi vendor plugins, SMS plugins and more. Check out [AST's full list of plugins compatibility](https://www.zorem.com/docs/woocommerce-advanced-shipment-tracking/compatibility/). 

== Documentation ==
You can get more information, detailed tutorials and code snippets on the [ AST documentation](https://www.zorem.com/docs/woocommerce-advanced-shipment-tracking).

== Translations == 
The AST plugin is localized/ translatable by default, we added translation to the following languages: 
English - default, German (Deutsch), Hebrew, Hindi, Italian, Norwegian (Bokmål), Russian, Swedish, Turkish, Bulgarian, Danish Spanish (Spain), French (France), Greek, Português Brasil, Dutch (Nederlands)

If your language is not in this list and you  want us to include it in the plugin, you can send us [on our docs](https://www.zorem.com/docs/woocommerce-advanced-shipment-tracking/translations/#upload-your-language-files)
 the translation files (po/mo) and we will add them to the plugin files.

== Shipping Providers == 

The AST plugin supports more then 250 shipping providers (carriers) with pre-defined tracking link:

USPS, ePacket, Delhivery, Yun Express Tracking, UPS, Australia Post, FedEx, Aramex, DHL eCommerce, ELTA Courier, Colissimo, DHL Express, La Poste, DHLParcel NL, Purolator, 4px, Brazil Correios, Deutsche Post DHL, Bpost, DHL US, EMS, DPD.de, GLS, China Post, Loomis Express, DHL Express, DHL Express UK, Poste Maroc, PostNL International 3S, Royal Mail and many others..

== FAQ == 

= Where will my customer see the tracking info?
The tracking info and a tracking link to track the order on the shipping provider website will be added to the **Shipped** (Completed) order status emails.  We will also display the tracking info in my-account area for each order in the order history tab.

= Can I add multiple tracking numbers to orders?
Yes, you can add as many tracking numbers to orders and they will all be displayed to your customers. 

= Can I add a shipping provider that is not on your list?
Yes, you can add custom providers, choose your default shipment provider, Change the providers order in the list and enable only providers that are relevant to you.

= Can I design the display of Tracking info on WooCommerce emails?
Yes, you have full control over the design and display of the tracking info and you can customize it.

= can I track my order and send shipment status and delivery notifications to my customers?
Yes, you can sign up to [Trackship](https://trackship.info) and connect your store, TrackShip will auto-track your shipments and update your orders with shipment status and delivery updates to your WooCommerce store and automates your order management process, you can send shipment status notifications to your customers and direct them to tracking page on your store.

= How do I set the custom provider URL so it will direct exactly to the tracking number results?
You can add tracking number parameter in this format:
http://shippingprovider.com?tracking_number=%number% , %number% - this variable will hold the tracking number for the order.

= is it possible to import multiple tracking numbers to orders in bulk?
Yes, you can use our Bulk import option to import multiple tracking inumbers to orders, you need to add each tracking number is one row.

= is it possible to add tracking number to specific products?
Yes, you can use the [Tracking Per Item pro add-on](https://www.zorem.com/products/tracking-per-item-ast-add-on/) which add the option to attach tracking numbers to specific line items and even to attach tracking numbers to specific line item quantities.

=How do I use the Rest API to add/retrieve/delete tracking info to my orders?
you can use the AST shipment tracking endpoints to add, retrieve, delete tracking information for orders using WooCommerce REST API. for more information, check our [documentation](https://www.zorem.com/docs/woocommerce-advanced-shipment-tracking/rest-api-support/)

== Installation ==

1. Upload the folder `woo-advanced-shipment-tracking` to the `/wp-content/plugins/` folder
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Select default shipping provider from setting page and add tracking number in order page.

== Changelog ==

= 3.0.8 =
* Fix - Fixed fatal error when changing status to delivered
* Fix - Fixed email content issue in TrackShip late shipments email

= 3.0.7 =
* Enhancement - Updated the design of TrackShip tracking page
* Enhancement - Updated design of Shipping Providers edit image lightbox and change label
* Enhancement - Updated design of shipping providers list
* Enhancement - Change CSV Import label
* Enhancement - Improve search shipping providers functionality
* Enhancement - Updated design of Trackship tools
* Enhancement - Custom Order Status auto save on change enable/disable, Color, Font color and Enable email
* Enhancement - Updated design of TrackShip tracking page settings
* Enhancement - Updated Order status and shipment status email customizer
* Fix - Fixed jQuery.live() in shipping_row.js

= 3.0.6 =
* Enhancement - Added Pending Trckship option in shipment status filter in orders page
* Enhancement - Added option for Edit shipping provider name and image
* Enhancement - Trackship tracking page added functionality for show origin tracking details and destination tracking details
* Enhancement - Add Re-Order button in my accounr single order page for custom order page - Delivered, Partially Shipped, Updated Tracking
* Dev - Added parent class in paging class in shipping providers list page
* Dev - Added Error message instead of error code in shipment status box in orders list page and single order page for TrackShip
* Fix - Fixed issue with bulk import with Partially Shipped

= 3.0.5 =
* Fix - Fixed issue with custom order number generated by Booster for WooCommerce plugin
* Fix - Fixed issue with custom order number in TrackShip tracking page
* Dev - Moved Tracking Per Item add-on license from this plugin to Tracking Per Item add-on
* Enhancement - Change default background color of tracking display table to #f5f5f5 

= 3.0.4 =
* Fix - Fixed license error for Tracking Per Item Add-on User

= 3.0.3 =
* Dev - Removed Tracking Per Item Add-on license activation code AST and moved to Tracking Per Item Add-on

= 3.0.2 =
* Fix - Fixed error on Add Tracking button on orders page
* Fix - fixed issue with On Hold customizer
* Enhancement - Remove TrackShip shipment stats change text before tracking info table from all shipment status emails

= 3.0.1 =
* Fix - fixed delivered order status email customizer issue
* Fix - fixed TrackShip tracking page jQuery Block UI issue for some of the themes

= 3.0 =
* Enhancement - Updated CSV Upload page design in settings page
* Enhancement - Updated TrackShip dashboard page design
* Enhancement - Added On Hold Shipment status emails for TrackShip
* Enhancement - Redesign Shipping Providers List in settings page
* Enhancement - Added option for hard sync shipping providers in Sync Providers option
* Dev - Updated plugin code for better security and optimize
* Dev - Removed compatibility code for WC – APG SMS Notifications from plugin
* Dev - Added all shipping provider image under wp-content/uploads/ast-shipping-providers folder. So load shipping provider image from there
* Dev - Optimized all shipping provider image
* Dev - Added new functions for add tracking information and get tracking information
* Dev - Removed all kind of special character validation from adding tracking number 
* Fix - Fixed issue of set order status shipped from order details page when "mark order as shipped" without page refresh
* Localization - Updated Swedish, Turkish and French Translations

[For the complete changelog](https://www.zorem.com/docs/woocommerce-advanced-shipment-tracking/changelog/)