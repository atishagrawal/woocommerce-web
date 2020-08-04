<section id="content_trackship_dashboard" class="inner_tab_section">
	<div class="tab_inner_container">
		<div class="outer_form_table trackship_status_section"></div>
		<?php
		$trackship = WC_Advanced_Shipment_Tracking_Trackship::get_instance();
		$completed_order_with_tracking = $trackship->completed_order_with_tracking();
		$completed_order_with_zero_balance = $trackship->completed_order_with_zero_balance();							
		$completed_order_with_do_connection = $trackship->completed_order_with_do_connection();
		if($completed_order_with_tracking > 0 || $completed_order_with_zero_balance > 0 || $completed_order_with_do_connection > 0){
		$total_orders = $completed_order_with_tracking + $completed_order_with_zero_balance + $completed_order_with_do_connection;	
		?>
		<div class="trackship-notice">
			<p><?php echo sprintf(__('We noticed that you have <a href="javascript:void(0);" class="tool_link">%s</a> orders from the last 30 days that were not sent to TrackShip, you can bulk send these order for tracking on the <a href="javascript:void(0);" class="tool_link">Get Shipment Status</a> tool', 'woo-advanced-shipment-tracking'),$total_orders ); ?></p>
		</div>		
		<?php } ?>
		<ul class="trackship_dashboard_ul">
			<li>
				<label><?php _e( 'TrackShip Connection Status', 'woo-advanced-shipment-tracking' ); ?></label><br>
				<a href="https://trackship.info/my-account/?utm_source=wpadmin&utm_medium=sidebar&utm_campaign=upgrade" target="_blank" class="api_connected"><?php _e( 'Connected', 'woo-advanced-shipment-tracking' ); ?><span class="dashicons dashicons-yes"></span></a>	
			</li>
			<li>
				<label><?php _e( 'Subscription Plan', 'woo-advanced-shipment-tracking' ); ?></label><br>
				<strong class="trackship_dashboard_li_strong"><?php if(isset($plan_data->subscription_plan))echo $plan_data->subscription_plan; ?></strong>	
			</li>
			<li>
				<label><?php _e( 'Shipment Trackers Balance', 'woo-advanced-shipment-tracking' ); ?></label><br>
				<strong class="trackship_dashboard_li_strong"><?php echo get_option('trackers_balance'); ?></strong>	
			</li>
		</ul>
		<div class="trackship_doc_div">
			<a class="trackship_doc_link" href="https://trackship.info/docs/overview/?utm_source=wpadmin&utm_medium=ts_settings&utm_campaign=docs" class="" style="margin-right: 10px;" target="blank"><?php _e( 'Documentation', 'woo-advanced-shipment-tracking' ); ?></a>
			<a class="trackship_doc_link" href="https://trackship.info/my-account/?utm_source=wpadmin&utm_medium=ts_settings&utm_campaign=dashboard" class="" target="blank"><?php _e( 'TrackShip Dashboard', 'woo-advanced-shipment-tracking' ); ?></a>
		</div>				
		
		<div class="outer_form_table">
			<table class="form-table heading-table">
				<tbody>
					<tr valign="top">
						<td>
							<h3 style=""><?php _e( 'General Settings', 'woo-advanced-shipment-tracking' ); ?></h3>
						</td>					
					</tr>
				</tbody>
			</table>		
			<?php $this->get_html_2( $trackship->get_trackship_general_data() ); ?>
			<table class="form-table">
				<tbody>
					<tr valign="top">						
						<td class="button-column">
							<div class="submit">								
								<button name="save" class="button-primary woocommerce-save-button btn_ast2 btn_large" type="submit" value="Save changes"><?php _e( 'Save Changes', 'woo-advanced-shipment-tracking' ); ?></button>
								<div class="spinner"></div>								
								<?php wp_nonce_field( 'wc_ast_trackship_form', 'wc_ast_trackship_form_nonce' );?>
								<input type="hidden" name="action" value="wc_ast_trackship_form_update">
							</div>	
						</td>
					</tr>
				</tbody>
			</table>
		</div>				
	</div>
	<?php include 'zorem_admin_sidebar.php'; ?>	
</section>