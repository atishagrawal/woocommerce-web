<?php
/**
 * html code for trackship tab
 */

?>
<section id="content3" class="tab_section">
	<div class="d_table" style="">
		<div class="tab_inner_container">
			<?php $wc_ast_api_key = get_option('wc_ast_api_key'); 
				if($wc_ast_api_key){
			?>
			<input id="tab_trackship_dashboard" type="radio" name="inner_tabs" class="inner_tab_input" data-tab="trackship-dashboard" checked>
			<label for="tab_trackship_dashboard" class="inner_tab_label"><?php _e('Dashboard', 'woocommerce'); ?></label>
			
			<input id="tab_tracking_page" type="radio" name="inner_tabs" class="inner_tab_input" data-tab="tracking-page" <?php if(isset($_GET['tab']) && $_GET['tab'] == 'tracking-page'){ echo 'checked'; } ?>>
			<label for="tab_tracking_page" class="inner_tab_label tracking_page_label"><?php _e('Tracking Page', 'woo-advanced-shipment-tracking'); ?></label>
			
			<input id="tab_status_notifications" type="radio" name="inner_tabs" class="inner_tab_input" data-tab="notifications" <?php if(isset($_GET['tab']) && $_GET['tab'] == 'notifications'){ echo 'checked'; } ?>>
			<label for="tab_status_notifications" class="inner_tab_label"><?php _e('Email Notifications', 'woo-advanced-shipment-tracking'); ?></label>			
			
			<input id="tab_tools" type="radio" name="inner_tabs" class="inner_tab_input" data-tab="tools" <?php if(isset($_GET['tab']) && $_GET['tab'] == 'tools'){ echo 'checked'; } ?>>
			<label for="tab_tools" class="inner_tab_label"><?php _e('Tools', 'woo-advanced-shipment-tracking'); ?></label>			
			<?php } ?>						
			
			<form method="post" id="wc_ast_trackship_form" action="" enctype="multipart/form-data">				
				<?php 				
				if($wc_ast_api_key){								
					$url = 'https://my.trackship.info/wp-json/tracking/get_user_plan';								
					$args['body'] = array(
						'user_key' => $wc_ast_api_key,				
					);
					$response = wp_remote_post( $url, $args );
					if ( is_wp_error( $response ) ) {
						
					} else{
						$plan_data = json_decode($response['body']);					
					}					
					
					require_once( 'admin_trackship_dashboard.php' );
					require_once( 'admin_tracking_page_settings.php' );
					require_once( 'admin_status_notifications.php' );	
					require_once( 'admin_options_tools.php' );	
				
					} else{ ?>
					
					<div class="section-content trackship_section">
						<div class="trackship-upsell-overlay">
							<div class="trackship-upsell-top">
								<h3><img src="<?php echo wc_advanced_shipment_tracking()->plugin_dir_url()?>assets/images/trackship-logo.png" class="trackship_logo"></h3>
								<p class="trackship-upsell-subtitle">TracksShip is a premium shipment tracking API flatform that fully integrates with WooCommerce with the Advanced Shipment Tracking. TrackShip automates the order management workflows, reduces customer inquiries, reduces time spent on customer service, and improves the post-purchase experience and satisfaction of your customers.</p>
								<p class="trackship-upsell-subtitle">You must have account TracksShip and connect your store in order to activate these advanced features:</p>
							</div>
							<div class="trackship-upsell-content">
								<ul>
									<li>Automatically track your shipments with 100+ shipping providers.</li>
									<li>Display Shipment Status and latest shipment status, update date and est. delivery date on WooCommerce orders admin.</li>
									<li>Option to manually get shipment tracking updates for orders.</li>
									<li>Automatically change order status to Delivered once the shipment is delivered to your customers.</li>
									<li>Option to filter orders with invalid tracking numbers or by shipment status event in orders admin</li>
									<li>Send personalized emails to notify the customer when their shipments are In Transit, Out For Delivery, Delivered or have an exception.</li>
									<li>Direct customers to a Tracking page on your store.</li>
								</ul>
								<div class="text-center"><a href="https://trackship.info/?utm_source=wpadmin&utm_campaign=tspage" target="_blank" class="button-primary btn_green2 btn_large">SIGNUP NOW</a></div>
							</div>
						</div>
					</div>
				<?php }
			?>			
			</form>
		</div>
	</div>
</section>