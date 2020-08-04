 var access_token = jQuery('#access_token');
 var access_token_secret = jQuery('#access_token_secret');
 var oauth_verifier = jQuery('#oauth_verifier'); 

 var temp_token_error = jQuery('#temp_token_error');
 var token_error = jQuery('#token_error');
       
jQuery(document).ready(function(){
    jQuery('#check-button').click(function(e){

        var site_url = jQuery('#woobox_site_url').val();
        var authorize_url = jQuery('#authorize_url');
        var temp_token = jQuery('#temp_token');
        var temp_token_secret = jQuery('#temp_token_secret');

        var client_key = jQuery('#client_key_new').val();
        var client_secret = jQuery('#client_secret_new').val();

        var access_token = jQuery('#access_token');
        var access_token_secret = jQuery('#access_token_secret');
        var oauth_verifier = jQuery('#oauth_verifier'); 

        var temp_token_error = jQuery('#temp_token_error');
         var token_error = jQuery('#token_error');
 

        console.log(client_key);
        console.log(client_secret);
        console.log(site_url);
 

        var randomString = function(length) {
            var text = "";
            var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
            for(var i = 0; i < length; i++) {
                text += possible.charAt(Math.floor(Math.random() * possible.length));
            }
            return text;
        }
        
        

        // console.log(client_key);
        // console.log(client_secret);

        var httpMethod = 'GET';
       // var surl = 'http://localhost/woobox/wp-admin/admin-ajax.php';
       var surl = site_url+'/oauth1/request';
        var timestamp = Math.round((new Date()).getTime() / 1000);

        var parameters = {
		oauth_consumer_key : client_key,		
		oauth_nonce : randomString(6),
		oauth_timestamp : timestamp,
		oauth_signature_method : 'HMAC-SHA1'
		
		
	};
	var consumerSecret = client_secret;
	var tokenSecret = '';
	// generates a RFC 3986 encoded, BASE64 encoded HMAC-SHA1 hash
	encodedSignature = oauthSignature.generate(httpMethod, surl, parameters, consumerSecret),
	// generates a BASE64 encode HMAC-SHA1 hash
	signature = oauthSignature.generate(httpMethod, surl, parameters, consumerSecret, tokenSecret,
		{ encodeSignature: false});
    console.log(parameters);
    console.log(signature);
        jQuery.ajax({

          type : "GET",          
          url : request_token.ajaxurl,
          data : {
                    action: "woobox_token_request_ajax", 
                    client_key : client_key,
                    client_secret:client_secret,
                    parameters : parameters,
                    encodedSignature : encodedSignature,
                    signature : signature,
                    surl : url
                    
                },

                success: function(response) {
                    
                    console.log(response);
                    var res = JSON.parse(response);
                    //console.log(res);
                    if(res['status'] == 200)
                    {
                        var auth_url = '';
                        temp_token_error.hide();
                        temp_token.val(res['auth_token']);
                        temp_token_secret.val(res['auth_token_secret']);
                        
                        auth_url += '<div class="alert alert-info">';
                        auth_url += '<blockquote><p> Please Click On Link To Get Your Verfication Token.';
                        auth_url += 'After Click Link Copy Verification Token And Paste In (Verification token) Textbox.';
                        auth_url += '<p> Then Click On (Get Verification Token) To Get Your  (Access token) And (Access token secret).</p></blockquote>';
                        auth_url += '<a target="_blank" href="'+site_url+'/oauth1/authorize?oauth_token='+res['auth_token']+'">Get Verification Token</a>';
                        auth_url += '</div>';


                      authorize_url.html(auth_url);
                    }
                    else
                    {
                        temp_token_error.html('<label class="alert alert-danger">Please Try Again.</label>');
                    }
                    
                    
                },
                error: function (error) {
                    //alert(error);
                     console.log(eval(error));
                }     

                

        });
        e.preventDefault(); 


      });

      jQuery('#final-button').click(function(e){

        var site_url = jQuery('#woobox_site_url').val();
        
        var temp_token = jQuery('#temp_token');
        var temp_token_secret = jQuery('#temp_token_secret');

        var client_key = jQuery('#client_key_new').val();
        var client_secret = jQuery('#client_secret_new').val();

        var access_token = jQuery('#access_token');
        var access_token_secret = jQuery('#access_token_secret');
        var oauth_verifier = jQuery('#oauth_verifier'); 

         var temp_token_error = jQuery('#temp_token_error');
         var token_error = jQuery('#token_error');
 


        var randomString = function(length) {
            var text = "";
            var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
            for(var i = 0; i < length; i++) {
                text += possible.charAt(Math.floor(Math.random() * possible.length));
            }
            return text;
        }
        
        

        // console.log(client_key);
        // console.log(client_secret);

        var httpMethod = 'GET';
       // var surl = 'http://localhost/woobox/wp-admin/admin-ajax.php';
       var surl = site_url+'/oauth1/access';
        var timestamp = Math.round((new Date()).getTime() / 1000);

        var parameters = {
        oauth_consumer_key : client_key,
        oauth_token : temp_token.val(),
        //oauth_token_secret : temp_token_secret.val(),
        oauth_nonce : randomString(6),
        oauth_timestamp : timestamp,
		oauth_signature_method : 'HMAC-SHA1',
        oauth_version : '1.0',
        oauth_verifier: oauth_verifier.val()
	    };
	var consumerSecret = client_secret;
	var tokenSecret = temp_token_secret.val();

	signature = oauthSignature.generate(httpMethod, surl, parameters, consumerSecret, tokenSecret, { encodeSignature: false});


         // console.log(signature, httpMethod, surl, parameters, consumerSecret, tokenSecret, { encodeSignature: false});
        jQuery.ajax({

          type : "GET",          
          url : request_token.ajaxurl,
          data : {
                    action: "woobox_token_request_ajax_final",
                    client_key : client_key,
                    client_secret:client_secret,
                    parameters : parameters,
                    // encodedSignature : encodedSignature,
                    signature : signature,
                    oauth_verifier : oauth_verifier.val(),
                    //surl : url
                    
                },

                success: function(response) {
                    
                    console.log(response);
                    var res = JSON.parse(response);
                    console.log(res);
                    if(res['status'] == 200)
                    {
                       var auth_url = '';
                        access_token.val(res['auth_token']);
                        access_token_secret.val(res['auth_token_secret']);
                        

                        auth_url += '<div class="alert alert-info">';
                        auth_url += '<blockquote><h5> Congratulation ! </h5>';
                        auth_url += '<p>You Have Successfully Get Your Access Token And Access token secret</p>';
                        auth_url += '<p>Use Below Credentials To Access Your Api.</p>';
                        auth_url += '<ul>';
                        auth_url += '<li>Client Key</li>';
                        auth_url += '<li> Client Secret</li>';
                        auth_url += '<li> Access token</li>';
                        auth_url += '<li> Access token secret</li>';
                        auth_url += '</ul>';
                       
                        auth_url += '</div>';
                        token_error.html(auth_url);


                      //authorize_url.html('<a target="_blank" href="'+site_url+'/oauth1/authorize?oauth_token='+res['auth_token']+'">CLICK HERE</a>')
                    }
                    else
                    {
                        token_error.html('<label class="alert alert-danger">Verification Token Is Expire Please Click On (Get Temporary Token) To Get Your Verification token.</label>');
                    }
                    
                    
                },
                error: function (error) {
                    //alert(error);
                     console.log(eval(error));
                }     

                

        });
        e.preventDefault(); 


      });




});



// function myfunction()
// {
//     alert('call');
// }

