<?php
// WP_REST_Server::READABLE = 'GET'
// WP_REST_Server::CREATABLE = 'POST'
// WP_REST_Server::EDITABLE = 'POST, PUT, PATCH'
// WP_REST_Server::DELETABLE = 'DELETE'
// WP_REST_Server::ALLMETHODS = 'GET, POST, PUT, PATCH, DELETE'


// use Automattic\WooCommerce\Client;


add_action('rest_api_init', function ()
{
    $namespace = 'woobox-api';
    $base = 'wishlist';

    register_rest_route($namespace . '/api/v1/' . $base, 'add-wishlist/', array(
        'methods' => WP_REST_Server::ALLMETHODS,
        'callback' => 'woobox_add_wishlist'
    ));

    register_rest_route($namespace . '/api/v1/' . $base, 'get-wishlist/', array(
        'methods' => WP_REST_Server::ALLMETHODS,
        'callback' => 'woobox_get_wishlist'
    ));

    register_rest_route($namespace . '/api/v1/' . $base, 'test-api/', array(
        'methods' => WP_REST_Server::ALLMETHODS,
        'callback' => 'woobox_test_api'
    ));

    register_rest_route($namespace . '/api/v1/' . $base, 'delete-wishlist/', array(
        'methods' => WP_REST_Server::ALLMETHODS,
        'callback' => 'woobox_delete_wishlist'
    ));

});

function woobox_jwt_auth_function($data, $user)
{
    $data['user_role'] = $user->roles;
    $data['user_id'] = $user->ID;
    $data['avatar'] = get_avatar_url($user->ID);
    return $data;
}
add_filter('jwt_auth_token_before_dispatch', 'woobox_jwt_auth_function', 10, 2);

function woobox_test_api($request)
{

    $header = $request->get_headers('username');
    $user['username'] = $header['username'][0];
    $user['password'] = $header['password'][0];

    $validate = new Woobox_Api_Authentication();
    $res = $validate->woobox_validate_token($header['token'][0]);

    return $res;

}
function woobox_get_wishlist($request)
{

    $header = $request->get_headers();
    if (empty($header['token'][0]))
    {
        return new WP_Error('token_missing', 'Token Required', array(
            'status' => 404
        ));
    }
    if (empty($header['id'][0]))
    {
        return new WP_Error('User missing', 'User id Required', array(
            'status' => 404
        ));
    }

    $validate = new Woobox_Api_Authentication();
    $response = $validate->woobox_validate_token($header['token'][0]);
    $userid = '';

    $userid = $header['id'][0];

    $res = (array) json_decode($response['body'], true);

    if ($res['data']['status'] != 200)
    {
        return $res;
    }
    global $wpdb;
    global $product;
    $masterarray = array();
    $datarray = array();

    $wishlist_items = $wpdb->get_results("SELECT * FROM 
                                                {$wpdb->prefix}iqonic_wishlist_product 
                                                    where 
                                                        user_id=" . $userid . "", OBJECT);

    if (empty($wishlist_items))
    {
        return new WP_Error('empty_wishlist', 'no product available', array(
            'status' => 404
        ));
    }
    else
    {
        $result = json_decode(json_encode($wishlist_items) , True);

        foreach ($result as $items)
        {
            $products = wc_get_product($items['pro_id']);

            if (!$products)
            {
                continue;
            }
            $datarray['pro_id'] = $products->get_id();
            $datarray['name'] = $products->get_name();
            $datarray['sku'] = $products->get_sku();
            $datarray['price'] = $products->get_price();
            $datarray['regular_price'] = $products->get_regular_price();
            $datarray['sale_price'] = $products->get_sale_price();
            $datarray['stock_quantity'] = $products->get_stock_quantity();

            $thumb = wp_get_attachment_image_src($products->get_image_id() , "thumbnail");
            $full = wp_get_attachment_image_src($products->get_image_id() , "full");
            $datarray['thumbnail'] = $thumb[0];
            $datarray['full'] = $full[0];

            foreach ($products->get_gallery_image_ids() as $img_id)
            {
                $g = wp_get_attachment_image_src($img_id, "full");
                $gallery[] = $g[0];
            }
            $datarray['gallery'] = $gallery;
            $gallery = array();

            $datarray['created_at'] = $items['created_at'];
            
            

            $masterarray[] = $datarray;
            $datarray = array();
        }

    }

    if (empty($masterarray))
    {
        return new WP_Error('empty_wishlist', 'no product available', array(
            'status' => 404
        ));
    }

    $response = new WP_REST_Response($masterarray);
    $response->set_status(200);
    return $response;

}

//add_action( 'woocommerce_init', 'add_wishlist' );
function woobox_add_wishlist($request)
{
    global $wpdb;

    $parameters = $request->get_params();

    $header = $request->get_headers();

    if (empty($header['token'][0]))
    {
        return new WP_Error('token_missing', 'Token Required', array(
            'status' => 404
        ));
    }

    // $user['username'] = $header['username'][0];
    // $user['password'] = $header['password'][0];
    

    $validate = new Woobox_Api_Authentication();
    $response = $validate->woobox_validate_token($header['token'][0]);
    $userid = $header['id'][0];

    $res = (array) json_decode($response['body'], true);

    if ($res['data']['status'] != 200)
    {
        return $res;
    }

    $wishlist_items = $wpdb->get_results("SELECT * FROM 
                {$wpdb->prefix}iqonic_wishlist_product 
                    where 
                        user_id=" . $userid . " AND pro_id =" . $parameters['pro_id'] . "", OBJECT);

    if (!empty($wishlist_items))
    {
        return new WP_Error('Already In list', 'Product Already in Wishlist', array(
            'status' => 403
        ));
    }

    $table = $wpdb->prefix . 'iqonic_wishlist_product';
     
    $insdata['user_id'] = $userid;
    $insdata['created_at'] = current_time('mysql');
    $insdata['pro_id'] = $parameters['pro_id'];
        
        
    

    $wpdb->insert($table, $insdata);

    $response = new WP_REST_Response(array(
        "code" => "success",
        "message" => "Product Succesfully Added To Wishlist",
        "data" => array(
            "status" => 200
        )
    )
);
    $response->set_status(200);
    return $response;

}
function woobox_delete_wishlist($request)
{
    global $wpdb;

    $parameters = $request->get_params();

    $header = $request->get_headers();

    if (empty($header['token'][0]))
    {
        return new WP_Error('token_missing', 'Token Required', array(
            'status' => 404
        ));
    }

    $validate = new Woobox_Api_Authentication();
    $response = $validate->woobox_validate_token($header['token'][0]);
    $userid = $header['id'][0];

    $res = (array) json_decode($response['body'], true);

    if ($res['data']['status'] != 200)
    {
        return $res;
    }

    $wishlist_items = $wpdb->get_results("DELETE FROM 
                {$wpdb->prefix}iqonic_wishlist_product 
                    where 
                        user_id=" . $userid . " AND pro_id =" . $parameters['pro_id'] . "", OBJECT);

    $response = new WP_REST_Response(array(
        "code" => "success",
        "message" => "Product Deleted From Wishlist",
        "data" => array(
            "status" => 200
        )
    )
);
    $response->set_status(200);
    return $response;

}

?>