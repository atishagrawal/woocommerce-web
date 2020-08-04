<?php 
	
function woobox_get_product_helper($id,$num_pages = '',$i='')
{
		global $product;
		$product = wc_get_product($id);
        $array['num_pages'] = $num_pages;
        $array['srno'] = $i;
        
        $array['pro_id'] = $product->get_id();
        $array['categories'] = $product->get_category_ids();

        $array['name'] = $product->get_name();

        $array['type'] = $product->get_type();
        $array['slug'] = $product->get_slug();
        $array['date_created'] = $product->get_date_created();
        $array['date_modified'] = $product->get_date_modified();
        $array['status'] = $product->get_status();
        $array['featured'] = $product->get_featured();
        $array['catalog_visibility'] = $product->get_catalog_visibility();
        $array['description'] = $product->get_description();
        $array['short_description'] = $product->get_short_description();
        $array['sku'] = $product->get_sku();

        $array['virtual'] = $product->get_virtual();
        $array['permalink'] = get_permalink($product->get_id());
        $array['price'] = $product->get_price();
        $array['regular_price'] = $product->get_regular_price();
        $array['sale_price'] = $product->get_sale_price();
        $array['brand'] = $product->get_attribute('brand');
        $array['size'] = $product->get_attribute('size');
        $array['color'] = $product->get_attribute('color');
        
        $array['weight_attribute'] = $product->get_attribute('weight');

        $array['tax_status'] = $product->get_tax_status();
        $array['tax_class'] = $product->get_tax_class();
        $array['manage_stock'] = $product->get_manage_stock();
        $array['stock_quantity'] = $product->get_stock_quantity();
        $array['stock_status'] = $product->get_stock_status();
        $array['backorders'] = $product->get_backorders();
        $array['sold_individually'] = $product->get_sold_individually();
        $array['get_purchase_note'] = $product->get_purchase_note();
        $array['shipping_class_id'] = $product->get_shipping_class_id();

        $array['weight'] = $product->get_weight();
        $array['length'] = $product->get_length();
        $array['width'] = $product->get_width();
        $array['height'] = $product->get_height();
        $array['dimensions'] = html_entity_decode($product->get_dimensions());

        // Get Linked Products
        $array['upsell_ids'] = $product->get_upsell_ids();
        $array['cross_sell_ids'] = $product->get_cross_sell_ids();
        $array['parent_id'] = $product->get_parent_id();

        $array['reviews_allowed'] = $product->get_reviews_allowed();
        $array['rating_counts'] = $product->get_rating_counts();
        $array['average_rating'] = $product->get_average_rating();
        $array['review_count'] = $product->get_review_count();

        $thumb = wp_get_attachment_image_src($product->get_image_id() , "thumbnail");
        $full = wp_get_attachment_image_src($product->get_image_id() , "full");
        $array['thumbnail'] = $thumb[0];
        $array['full'] = $full[0];
        $gallery = array();
        foreach ($product->get_gallery_image_ids() as $img_id)
        {
            $g = wp_get_attachment_image_src($img_id, "full");
            $gallery[] = $g[0];
        }
        $array['gallery'] = $gallery;
        $gallery = array();

        
        return $array;


}

function woobox_throw_error($msg)
{
     $response = new WP_REST_Response(array(
        "code" => "Error",
        "message" => $msg,
        "data" => array(
            "status" => 404
        )
    )
);
    $response->set_status(404);
    return $response;
}

function allow_payment_without_login( $allcaps, $caps, $args ) {
    // Check we are looking at the WooCommerce Pay For Order Page
    if ( !isset( $caps[0] ) || $caps[0] != 'pay_for_order' )
        return $allcaps;
    // Check that a Key is provided
    if ( !isset( $_GET['key'] ) )
        return $allcaps;

    // Find the Related Order
    $order = wc_get_order( $args[2] );
    if( !$order )
        return $allcaps; # Invalid Order

    // Get the Order Key from the WooCommerce Order
    $order_key = $order->get_order_key();
    // Get the Order Key from the URL Query String
    $order_key_check = $_GET['key'];

    // Set the Permission to TRUE if the Order Keys Match
    $allcaps['pay_for_order'] = ( $order_key == $order_key_check );

    return $allcaps;
}
add_filter( 'user_has_cap', 'allow_payment_without_login', 10, 3 );

function get_enable_category($arr)
{
    $a = (array) $arr;   

    $term_meta = get_option("enable_" . $a['term_id']);

    if(!empty($term_meta['enable']))
    {
        return $a;
    }
    
}

function get_category_child($arr)
{
    $a = (array) $arr;
    if($a)
    {
        $child_terms_ids = get_term_children( $a['term_id'], 'product_cat' );
        
        $temp = array_map('get_enable_subcategory',$child_terms_ids);

        // $temp = array_filter($temp,function($var)
        // {
        //     return $var !== null;
        // });
        
        $a['subcategory'] = woobox_filter_array($temp);    
        
        return $a;
    }
}

function woobox_attach_category_image($arr)
{
    $a = (array) $arr;
    if($a)
    {
        $thumb_id = get_woocommerce_term_meta( $a['term_id'], 'thumbnail_id', true );
        $term_img = wp_get_attachment_url(  $thumb_id );

        if($term_img)
        {
            $a['image'] = $term_img;    
        }
        else
        {
            $a['image'] = "";       
        }
        return $a;
    }
}

function get_enable_subcategory($arr)
{
    $a = (array) $arr;
    foreach($a as $val)
    {
        $term_meta = get_option("enable_" . $val);
        if($term_meta)
        {
            return $val;
        }
    }
}

function woobox_filter_array($arr)
{
    $res = array();
    foreach($arr as $key=>$val)
    {
        if($val != null)
        {
            array_push($res,$val);
        }
    }
    return $res;

}
?>