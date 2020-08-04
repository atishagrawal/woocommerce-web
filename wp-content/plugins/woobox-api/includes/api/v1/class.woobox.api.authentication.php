<?php

class Woobox_Api_Authentication
{
    private $tokenurl;

    private $validatetokenurl;

    private $response = array();

    public function _construct()
    {

        $this->tokenurl = $this->woobox_get_token_url();
        $this->validatetokenurl = $this->woobox_get_validate_token_url();
    }

    public function woobox_validate_request($user)
    {
        //return $user;
        $data = $this->woobox_generate_token($user);


        if ($data['response']['code'] == 200)
        {
            $token = (array)json_decode($data['body'], true);

            $valdata = $this->woobox_validate_token($token['token']);
            //$valdata = $this->woobox_validate_token('l;l;l;');
            if ($valdata['response']['code'] == 200)
            {
                return $this->response = array(
                    "user_id" => $token['user_id'],
                    "code" => "Authorized Request",
                    "status" => 200,
                    "message" => "valid token"
                );
            }
            else
            {
                $error = (array)json_decode($valdata['body'], true);

                return $this->response = array(
                    "code" => $error['code'],
                    "message" => utf8_encode($error['message']) ,
                    "data" => array(
                        "status" => $error['data']['status']
                    )
                );

            }
        }
        else
        {
            $error = (array)json_decode($data['body'], true);

            return $this->response = array(
                "code" => $error['code'],
                "message" => utf8_encode($error['message']) ,
                "data" => array(
                    "status" => $error['data']['status']
                )
            );
        }

    }

    private function woobox_get_token_url()
    {
        return get_home_url() . "/wp-json/jwt-auth/v1/token";
    }
    private function woobox_get_validate_token_url()
    {
        return get_home_url() . "/wp-json/jwt-auth/v1/token/validate";
    }

    private function woobox_generate_token($data)
    {

        return $response = wp_remote_post($this->woobox_get_token_url() , array(
            'body' => $data

        ));

    }

    public function woobox_validate_token($token)
    {
        $response = wp_remote_post($this->woobox_get_validate_token_url() , array(
            'body' => null,
            'headers' => array(
                'Authorization' => 'Bearer ' . $token,
            )

        ));

        return $response;
    }

    public function woobox_validate_social($data)
    {
        $data = $this->woobox_generate_token($data);
        return $data['body'];
    }
}
?>