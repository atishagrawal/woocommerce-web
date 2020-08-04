<?php

add_action('rest_api_init', function ()
{
    $namespace = 'woobox-api';
    $base = 'slider';

    register_rest_route($namespace . '/api/v1/' . $base, 'get-slider/', array(
        'methods' => WP_REST_Server::ALLMETHODS,
        'callback' => 'woobox_get_slider'
    ));

    register_rest_route($namespace . '/api/v1/blog', 'get-blog/', array(
        'methods' => WP_REST_Server::ALLMETHODS,
        'callback' => 'woobox_get_blog'
    ));

});



function woobox_get_slider($request)
{
    
    

    global $app_opt_name;
    $woobox_option = get_option('woobox_app_options');
    
    $array = array();
    $master = array();

    

    if (isset($woobox_option['opt-slides']) && !empty($woobox_option['opt-slides']))
    {
        foreach ($woobox_option['opt-slides'] as $slide)
        {
            
            $array['image'] = $slide['image'];
            $array['thumb'] = $slide['thumb'];
            
            $array['url'] = $slide['url'];

            if (!empty($slide['image']))
            {
                array_push($master, $array);
            }

        }

        
        
    }

    $response = new WP_REST_Response($master);
    $response->set_status(200);

    return $response;

}

function woobox_get_blog($request)
{
   
   
    $masterarray = array();
    $array = array();

    $parameters = $request->get_params();

    $page = 1;

    if(isset($parameters['page']))
    {
        $page = $parameters['page'];
    }

    $args['post_type'] = 'post';
    $args['post_status'] = 'publish';
    $args['posts_per_page'] = 10;
    $args['paged'] = $page;
    
    $wp_query = new \WP_Query($args);

    $num_pages = 1;
    $num_pages = $wp_query->max_num_pages;  

    $out = '';

    global $post;
    if($wp_query->have_posts()) 
    {   
        while ( $wp_query->have_posts() ) 
        {
                $wp_query->the_post();
                $full_image = wp_get_attachment_image_src( get_post_thumbnail_id( $wp_query->ID  ), "full" );
            $array['num_pages'] = $num_pages;
            if($full_image[0])
            {
                $array['image'] = $full_image[0];    
            }
            else
            {
                $array['image'] = '';       
            }
            $array['image'] = $full_image[0];
            $array['title'] = get_the_title();
            $array['description'] = esc_html(get_the_content());
            $array['publish_date'] = get_the_date();

            array_push($masterarray, $array);

            $array = array();

        }
    }

     $response = new WP_REST_Response($masterarray);

        $response->set_status(200);
        return $response;
        
}

?>