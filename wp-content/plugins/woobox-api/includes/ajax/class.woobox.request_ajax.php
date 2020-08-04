<?php
add_action("wp_ajax_woobox_token_request_ajax", "woobox_token_request_ajax");
add_action("wp_ajax_nopriv_woobox_token_request_ajax", "woobox_token_request_ajax");

function woobox_token_request_ajax()
{

    $para = $_REQUEST['parameters'];
    $consumer_key = $para['oauth_consumer_key'];
    $oauth_nonce = $para['oauth_nonce'];
    $oauth_timestamp = $para['oauth_timestamp'];
    $oauth_signature_method = $para['oauth_signature_method'];
    $oauth_signature = $_REQUEST['encodedSignature'];
    $signature = $_REQUEST['signature'];

    $params = '?oauth_consumer_key=' . $consumer_key . '&oauth_signature_method=' . $oauth_signature_method . '&oauth_timestamp=' . $oauth_timestamp . '&oauth_nonce=' . $oauth_nonce . '&oauth_signature=' . $oauth_signature . '';

    $response = wp_remote_get(get_home_url() . '/oauth1/request'.$params, array(
        'body' => null,
        

    ));

    // $response = wp_remote_get(get_site_url() . '/oauth1/request/', array(
    //     'body' => null,
    //     'headers' => array(
    //         'Authorization' => 'OAuth oauth_consumer_key="' . $consumer_key . '",oauth_signature_method="' . $oauth_signature_method . '",oauth_timestamp="' . $oauth_timestamp . '",oauth_nonce="' . $oauth_nonce . '",oauth_signature="' . $signature . '"',
    //     )

    // ));
    $obj = (array) json_decode(json_encode($response), true);

   

    if ($obj['response']['code'] == 200)
    {
        delete_option('client_key');
        delete_option('client_secret');
        $res = array();
        $ex = explode("&", $obj['body']);

        $auth_token = str_replace('oauth_token=', '', $ex[0]);

        $auth_token_secret = str_replace('oauth_token_secret=', '', $ex[1]);

        $res['auth_token'] = trim(preg_replace('/\s\s+/', '', $auth_token));
        $res['auth_token_secret'] = trim(preg_replace('/\s\s+/', '', $auth_token_secret));
        $res['status'] = 200;
        $res['message'] = "Temporary Token Create Successfully";

        update_option('client_key', $consumer_key);
        update_option('client_secret', $_REQUEST['client_secret']);

        echo json_encode($res);

    }
    else
    {
        $res = array();
        $res['status'] = 100;
        $res['message'] = "Somethig Went Wrong Please Refresh Page And Try Again.";

        echo json_encode(get_site_url() . '/oauth1/request'.$params);
    }

    die();

}

add_action("wp_ajax_woobox_token_request_ajax_final", "woobox_token_request_ajax_final");
add_action("wp_ajax_nopriv_woobox_token_request_ajax_final", "woobox_token_request_ajax_final");

function woobox_token_request_ajax_final()
{

    $para = $_REQUEST['parameters'];
    $consumer_key = $para['oauth_consumer_key'];
    $oauth_token = $para['oauth_token'];
    $oauth_nonce = $para['oauth_nonce'];
    $oauth_timestamp = $para['oauth_timestamp'];
    $oauth_signature_method = $para['oauth_signature_method'];    
    $signature = $_REQUEST['signature'];

    $params = '?oauth_consumer_key=' . $consumer_key . '&oauth_token=' . $oauth_token . '&oauth_signature_method=' . $oauth_signature_method . '&oauth_timestamp=' . $oauth_timestamp . '&oauth_nonce=' . $oauth_nonce . '&oauth_version=1.0&oauth_signature=' . $signature . '';

   

    $response = wp_remote_get(get_home_url() . '/oauth1/access'.$params.'&oauth_verifier=' . $_REQUEST['oauth_verifier'], array(
        'body' => null,
        
    ));

    // $response = wp_remote_get(get_site_url() . '/oauth1/access?oauth_verifier=' . $_REQUEST['oauth_verifier'], array(
    //     'body' => null,
    //     'headers' => array(
    //         'Authorization' => 'OAuth oauth_consumer_key="' . $consumer_key . '",oauth_token="' . $oauth_token . '",oauth_signature_method="' . $oauth_signature_method . '",oauth_timestamp="' . $oauth_timestamp . '",oauth_nonce="' . $oauth_nonce . '",oauth_version="1.0",oauth_signature="' . $signature . '"',
    //     )
    // ));


    //echo json_encode($response);
    if ($response['response']['code'] == 200)
    {
        delete_option('auth_token');
        delete_option('auth_token_secret');

        $res = array();
        $ex = explode("&", $response['body']);

        $auth_token = str_replace('oauth_token=', '', $ex[0]);

        $auth_token_secret = str_replace('oauth_token_secret=', '', $ex[1]);

        $res['auth_token'] = trim(preg_replace('/\s\s+/', '', $auth_token));
        $res['auth_token_secret'] = trim(preg_replace('/\s\s+/', '', $auth_token_secret));
        $res['status'] = 200;
        $res['message'] = "Access Token Create Successfully";

        update_option('auth_token', $res['auth_token']);
        update_option('auth_token_secret', $res['auth_token_secret']);

        echo json_encode($res);

    }
    else
    {
        $res = array();
        $res['status'] = 100;
        $res['message'] = "Somethig Went Wrong Please Refresh Page And Try Again.";

        echo json_encode(get_site_url() . '/oauth1/access/'.$params.'&oauth_verifier=' . $_REQUEST['oauth_verifier']);
    }

    die();

}

?>