<?php
class Woobox_Api
{
    private $plugin_name, $version;
    public function __construct()
    {
        $this->plugin_name = 'woobox-api';
        $this->version = '1.0';
        $this->load_dependancies();
        $this->set_locale();
    }

    private function load_dependancies()
    {

        require_once WOO_DIR . 'includes/api/helperfunction/helperfunction.php';


        require_once WOO_DIR . 'includes/api/v1/class.woobox.api.authentication.php';
        require_once WOO_DIR . 'includes/db/class.woobox.db.php';
        require_once WOO_DIR . 'includes/api/v1/class.woobox.api.wishlist.route.php';
        require_once WOO_DIR . 'includes/class-woobox-api-i18n.php';
        require_once WOO_DIR . 'includes/api/v1/class.woobox.api.cart.route.php';
        require_once WOO_DIR . 'includes/api/v1/class.woobox.api.slider.route.php';
        require_once WOO_DIR . 'includes/api/v1/class.woobox.api.woocommerce.route.php';
        require_once WOO_DIR . 'includes/api/v1/class.woobox.api.customer.route.php';
        

       // require_once WOO_DIR . 'includes/api/v1/class.woobox.api.payment.route.php';

       
        require_once WOO_DIR . 'includes/ajax/class.woobox.request_ajax.php';
        
        require_once WOO_DIR . 'includes/notification/class.sendnotification.php';

        require_once WOO_DIR . 'includes/custom-filed_wc/woobox_custom_filed_wc.php';
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Woobox_Api_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     */
    private function set_locale()
    {
        // $plugin_i18n = new Woobox_Api_i18n();
        // $plugin_i18n->set_domain($this->get_plugin_name());
        // add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
        
    }

    public function get_plugin_name()
    {
        return $this->plugin_name;
    }
}
?>