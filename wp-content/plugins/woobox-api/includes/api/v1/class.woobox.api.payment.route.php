<?php
// WP_REST_Server::READABLE = 'GET'
// WP_REST_Server::CREATABLE = 'POST'
// WP_REST_Server::EDITABLE = 'POST, PUT, PATCH'
// WP_REST_Server::DELETABLE = 'DELETE'
// WP_REST_Server::ALLMETHODS = 'GET, POST, PUT, PATCH, DELETE'


add_action('rest_api_init', function ()
{
    $namespace = 'woobox-api';
    $base = 'payment';

    register_rest_route($namespace . '/api/v1/' . $base, 'get-checkout-url/', array(
        'methods' => WP_REST_Server::ALLMETHODS,
        'callback' => 'woobox_get_checkout_url'
    ));

    register_rest_route($namespace . '/api/v1/' . $base, 'get-active-payment-gateway/', array(
        'methods' => WP_REST_Server::ALLMETHODS,
        'callback' => 'woobox_get_active_payment_gateway'
    ));
    
 });


function woobox_get_active_payment_gateway($request)
{
    $gateways = WC()->payment_gateways->get_available_payment_gateways();
    $enabled_gateways = [];
    $master = [];

    if( $gateways ) {
        foreach( $gateways as $gateway ) {

            if( $gateway->enabled == 'yes' ) {

                $enabled_gateways['id'] = $gateway->id;
                $enabled_gateways['method_title'] = $gateway->method_title;
                $enabled_gateways['method_description'] = $gateway->method_description;
                
                array_push($master,$enabled_gateways);

            }
        }
    }  

    $response = new WP_REST_Response($master);
    $response->set_status(200);    

    return $response;
}

?>