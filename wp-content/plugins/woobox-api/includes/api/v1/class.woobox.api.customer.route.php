<?php

add_action('rest_api_init', function ()
{
    $namespace = 'woobox-api';
    $base = 'customer';

    register_rest_route($namespace . '/api/v1/' . $base, 'social_login/', array(
        'methods' => WP_REST_Server::ALLMETHODS,
        'callback' => 'woobox_get_customer_by_social'
    ));

    register_rest_route($namespace . '/api/v1/' . $base, 'add-address/', array(
        'methods' => WP_REST_Server::ALLMETHODS,
        'callback' => 'woobox_set_miltiple_address'
    ));

    register_rest_route($namespace . '/api/v1/' . $base, 'get-address/', array(
        'methods' => WP_REST_Server::ALLMETHODS,
        'callback' => 'woobox_get_miltiple_address'
    ));

    register_rest_route($namespace . '/api/v1/' . $base, 'delete-address/', array(
        'methods' => WP_REST_Server::ALLMETHODS,
        'callback' => 'woobox_delete_miltiple_address'
    ));
    
    register_rest_route($namespace . '/api/v1/' . $base, 'save-profile-image/', array(
        'methods' => WP_REST_Server::ALLMETHODS,
        'callback' => 'woobox_save_profile_image'
    ));

});

function woobox_get_customer_by_social($request)
{
   
    $header = $request->get_headers();
    $parameters = $request->get_params();
    $email = $parameters['email'];
    $password = $parameters['accessToken'];

    $user = get_user_by('email', $email);
    $res = '';

    $address = array(
        'first_name' => $parameters['firstName'],
        'last_name'  => $parameters['lastName'],            
        'email'      => $email
        
    );
    
    if (!$user ) 
    {
        
        $user = wp_create_user( $email, $password, $email );
        update_user_meta( $user, "billing_first_name", $address['first_name'] );
        update_user_meta( $user, "billing_last_name", $address['last_name']);
        update_user_meta( $user, "billing_email", $address['email'] );

        update_user_meta( $user, "shipping_first_name", $address['first_name'] );
        update_user_meta( $user, "shipping_last_name", $address['last_name']);

        update_user_meta( $user, 'first_name', trim( $address['first_name'] ) );
        update_user_meta( $user, 'last_name', trim( $address['last_name'] ) );

        $u = new WP_User( $user);
        $u->set_role( 'customer' );
        $validate = new Woobox_Api_Authentication();
        
        $res = $validate->woobox_validate_social("username=".$email."&password=".$password);

    }
    else
    {
        $validate = new Woobox_Api_Authentication();
        wp_set_password( $password, $user->ID);

        $u = new WP_User( $user);
        $u->set_role( 'customer' );

        $res = $validate->woobox_validate_social("username=".$email."&password=".$password);
        
    }
    $response = new WP_REST_Response(json_decode($res,true));
    $response->set_status(200);

    return $response;

}

function woobox_set_miltiple_address($request)
{
    global $wpdb;
    $header = $request->get_headers();
    $parameters = $request->get_params();

    
    
    if (empty($header['token'][0]))
    {
        return new WP_Error('token_missing', 'Token Required', array(
            'status' => 404
        ));
    }

    $validate = new Woobox_Api_Authentication();
    $response = $validate->woobox_validate_token($header['token'][0]);
    $userid = $header['id'][0];
    

    $res = (array)  json_decode($response['body'], true);

    if ($res['data']['status'] != 200)
    {
        return $res;
    }

    
   $insdata = array();

    if(isset($parameters['first_name']))
    {
        
        $insdata['first_name'] = $parameters['first_name'];;
        
    } 

    if(isset($parameters['last_name']))
    {
        
        $insdata['last_name'] = $parameters['last_name'];;
        
    } 

    if(isset($parameters['company']))
    {
        
        $insdata['company'] = $parameters['company'];;
        
    } 

    if(isset($parameters['address_1']))
    {
        
        $insdata['address_1'] = $parameters['address_1'];;
        
    } 

    if(isset($parameters['address_2']))
    {
        
        $insdata['address_2'] = $parameters['address_2'];;
        
    } 

    if(isset($parameters['state']))
    {
        
        $insdata['state'] = $parameters['state'];;
        
    } 

    if(isset($parameters['city']))
    {
        
        $insdata['city'] = $parameters['city'];;
        
    } 

    if(isset($parameters['country']))
    {
        
        $insdata['country'] = $parameters['country'];;
        
    }

    if(isset($parameters['postcode']))
    {
        
        $insdata['postcode'] = $parameters['postcode'];;
        
    }

    if(isset($parameters['contact']))
    {
        
        $insdata['contact'] = $parameters['contact'];;
        
    } 

    $insdata['user_id'] = $userid;
    $insdata['created_at'] = current_time('mysql');

    $table = $wpdb->prefix . 'iqonic_multiple_address';

    if(isset($parameters['ID']))
    {
       
     $cond = array(
            "ID" => $parameters['ID']
        );

        $res = $wpdb->update($table, $insdata, $cond);
    
        
    } 
    else
    {
       $res = $wpdb->insert($table, $insdata); 
    }

    
    if($res > 0)
    {

        $response = new WP_REST_Response(array(
            "code" => "success",
            "message" => "Address Saved",
            "data" => array(
                "status" => 200
            )
        )   
        );
    return $response;
    }
    else
    {
        return woobox_throw_error("Address Not Saved");
    }
 
 
}

function woobox_get_miltiple_address($request)
{
    global $wpdb;
    $header = $request->get_headers();
    $parameters = $request->get_params();
    
    if (empty($header['token'][0]))
    {
        return new WP_Error('token_missing', 'Token Required', array(
            'status' => 404
        ));
    }

    $validate = new Woobox_Api_Authentication();
    $response = $validate->woobox_validate_token($header['token'][0]);
    $userid = $header['id'][0];

    $res = (array)  json_decode($response['body'], true);

    if ($res['data']['status'] != 200)
    {
        return $res;
    }

    $address = $wpdb->get_results("SELECT * FROM 
                                                {$wpdb->prefix}iqonic_multiple_address 
                                                    where 
                                                        user_id=" . $userid . "", OBJECT);
    $result = json_decode(json_encode($address) , True);

    $response = new WP_REST_Response($result);
    $response->set_status(200);
    return $response;

    
}

function woobox_delete_miltiple_address($request)
{
    global $wpdb;
    $header = $request->get_headers();
    $parameters = $request->get_params();
    
    if (empty($header['token'][0]))
    {
        return new WP_Error('token_missing', 'Token Required', array(
            'status' => 404
        ));
    }

    $validate = new Woobox_Api_Authentication();
    $response = $validate->woobox_validate_token($header['token'][0]);
    $userid = $header['id'][0];

    $res = (array)  json_decode($response['body'], true);

    if ($res['data']['status'] != 200)
    {
        return $res;
    }

    $res = $wpdb->get_results("DELETE FROM 
                {$wpdb->prefix}iqonic_multiple_address 
                    where 
                    user_id=" . $userid . " AND ID =" . $parameters['ID'] . "", OBJECT);
    if($res > 0)
    {

        $response = new WP_REST_Response(array(
            "code" => "success",
            "message" => "Address Deleted Successfully",
            "data" => array(
                "status" => 200
            )
        )
        );
        return $response;
    }

    else
    {
        return woobox_throw_error("Address Not Delted");
    }

    
}
/**
 * Save the image on the server.
 */
function woobox_save_profile_image( $request ) {

    $header = $request->get_headers();
    $parameters = $request->get_params();
    
    if (empty($header['token'][0]))
    {
        return new WP_Error('token_missing', 'Token Required', array(
            'status' => 404
        ));
    }

    $validate = new Woobox_Api_Authentication();
    $response = $validate->woobox_validate_token($header['token'][0]);
    $userid = $header['id'][0];

    $res = (array)  json_decode($response['body'], true);

    if ($res['data']['status'] != 200)
    {
        return $res;
    }

    $userid = $header['id'][0];

    $base64_img = $parameters['base64_img']; 
    $type      = explode(';', $base64_img)[0];
    $extension = explode('/', $type)[1];
    
    $title = $parameters['title']; 
    // Upload dir.
    $upload_dir  = wp_upload_dir();
    $upload_path = str_replace( '/', DIRECTORY_SEPARATOR, $upload_dir['path'] ) . DIRECTORY_SEPARATOR;

    $img             = str_replace( 'data:image/'.$extension.';base64,', '', $base64_img );
    $img             = str_replace( ' ', '+', $img );
    $decoded         = base64_decode( $img );
    $filename        = '.'.$extension;
    $file_type       = 'image/'.$extension;
    $hashed_filename = md5( $filename . microtime() ) . $filename;

    // Save the image in the uploads directory.
    $upload_file = file_put_contents( $upload_path . $hashed_filename, $decoded );

    $attachment = array(
        'post_mime_type' => $file_type,
        'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $hashed_filename ) ),
        'post_content'   => '',
        'post_status'    => 'inherit',
        'guid'           => $upload_dir['url'] . '/' . basename( $hashed_filename )
    );

    $attach_id = wp_insert_attachment( $attachment, $upload_dir['path'] . '/' . $hashed_filename );

    // Regenerate Thumbnail
    global $wpdb;
    $images = $wpdb->get_results( "SELECT ID FROM $wpdb->posts WHERE post_type = 'attachment' AND post_mime_type LIKE 'image/%' AND ID ='$attach_id'" );

    foreach ( $images as $image ) {
        $id = $image->ID;
        $fullsizepath = get_attached_file( $id );

        if ( false === $fullsizepath || !file_exists($fullsizepath) )
            return;

        if ( wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $fullsizepath ) ) )
        {
           // return true;
        }
        
    }
    $url = wp_get_attachment_url( $attach_id );

    $update = update_user_meta( $userid, 'woobox_profile_image', $url );

    
    if(!is_wp_error($update))
    {
        
        $img = get_user_meta( $userid, 'woobox_profile_image' ) ;  
        $data = array();
        $data['profile_image'] = $img[0]; 
        $data['code'] = 200;        
        $data['message'] = "Profile Picture Saved";        
        
        $response = new WP_REST_Response($data);
        $response->set_status(200);
        return $response;
    }
    else
    {
        return woobox_throw_error("Profile Picture Not Save");
    }
}
?>