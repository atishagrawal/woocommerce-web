<?php
/**
 * html code for settings tab
 */
?>
<section id="content2" class="tab_section">
	<div class="tab_inner_container">
		<form method="post" id="wc_ast_settings_form" action="" enctype="multipart/form-data">
			<?php #nonce?>
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
				<?php $this->get_html( $this->get_settings_data() );?>	
				<table class="form-table">
					<tbody>
						<tr valign="top">						
							<td class="button-column">
								<div class="submit">								
									<button name="save" class="button-primary woocommerce-save-button btn_ast2 btn_large" type="submit" value="Save changes"><?php _e( 'Save Changes', 'woo-advanced-shipment-tracking' ); ?></button>
									<div class="spinner"></div>								
									<?php wp_nonce_field( 'wc_ast_settings_form', 'wc_ast_settings_form_nonce' );?>
									<input type="hidden" name="action" value="wc_ast_settings_form_update">
								</div>	
							</td>
						</tr>
					</tbody>
				</table>
			</div>
				
			<div class="outer_form_table">		
				<table class="form-table heading-table">
					<tbody>
						<tr valign="top">
							<td>
								<h3 style=""><?php _e( 'Tracking Info Display', 'woo-advanced-shipment-tracking' ); ?></h3>
							</td>
						</tr>
					</tbody>
				</table>
				<table class="form-table">
					<tbody>
						<tr valign="top">
							<td>
								<p style=""><?php _e( 'Use a customizer with a preview to customize the tracking info display on customer order emails and my-account.', 'woo-advanced-shipment-tracking' ); ?></p>		
								<a href="<?php echo wcast_initialise_customizer_settings::get_customizer_url('ast_tracking_display_panel','settings') ?>" class="button-primary btn_ast2 btn_large launch_customizer_btn"><?php _e( 'Launch Customizer', 'woo-advanced-shipment-tracking' ); ?> <span class="dashicons dashicons-welcome-view-site"></span></a>
							</td>						
						</tr>
					</tbody>
				</table>
			</div>						
		</form>
		
		<form method="post" id="wc_ast_order_status_form" action="" enctype="multipart/form-data">
			<div class="outer_form_table custom_order_status_section">
					<table class="form-table heading-table">
						<tbody>
							<tr valign="top">
								<td>
									<h3 style=""><?php _e( 'Order Statuses', 'woo-advanced-shipment-tracking' ); ?></h3>
								</td>
							</tr>
						</tbody>
					</table>				
					<table class="form-table order-status-table">
						<tbody>							
							<tr valign="top" class="delivered_row <?php if(!get_option('wc_ast_status_delivered')){echo 'disable_row'; } ?>">
								<td class="forminp" style="width: 30px;">
									<span class="mdl-list__item-secondary-action">
										<label class="mdl-switch mdl-js-switch mdl-js-ripple-effect" for="wc_ast_status_delivered">
											<input type="hidden" name="wc_ast_status_delivered" value="0"/>
											<input type="checkbox" id="wc_ast_status_delivered" name="wc_ast_status_delivered" class="mdl-switch__input order_status_toggle" <?php if(get_option('wc_ast_status_delivered')){echo 'checked'; } ?> value="1"/>
										</label>
									</span>
								</td>
								<td class="forminp status-label-column">
									<span class="order-label wc-delivered">
										<?php 
										if(get_option('wc_ast_status_delivered')){
											_e( wc_get_order_status_name( 'delivered' ), 'woo-advanced-shipment-tracking' );	
										} else{
											_e( 'Delivered', 'woo-advanced-shipment-tracking' );
										} ?>
									</span>
								</td>								
								<td class="forminp">							
									<?php
									$wcast_enable_delivered_email = get_option('woocommerce_customer_delivered_order_settings');
									if($wcast_enable_delivered_email['enabled'] == 'yes' || $wcast_enable_delivered_email['enabled'] == 1){
										$delivered_checked = 'checked';
									} else{
										$delivered_checked = '';									
									}
									?>
									<fieldset>
										<input class="input-text regular-input color_input" type="text" name="wc_ast_status_label_color" id="wc_ast_status_label_color" style="" value="<?php echo get_option('wc_ast_status_label_color')?>" placeholder="">
										<select class="select custom_order_color_select" id="wc_ast_status_label_font_color" name="wc_ast_status_label_font_color">	
											<option value="#fff" <?php if(get_option('wc_ast_status_label_font_color') == '#fff'){ echo 'selected'; }?>><?php _e( 'Light Font', 'woo-advanced-shipment-tracking' ); ?></option>
											<option value="#000" <?php if(get_option('wc_ast_status_label_font_color') == '#000'){ echo 'selected'; }?>><?php _e( 'Dark Font', 'woo-advanced-shipment-tracking' ); ?></option>
										</select>
										<label class="send_email_label">
											<input type="hidden" name="wcast_enable_delivered_email" value="0"/>
											<input type="checkbox" name="wcast_enable_delivered_email" id="wcast_enable_delivered_email" <?php echo $delivered_checked; ?> value="1" class="enable_order_status_email_input"><?php _e( 'Send Email', 'woo-advanced-shipment-tracking' ); ?>
										</label>
										<a class='settings_edit' href="<?php echo wcast_initialise_customizer_email::get_customizer_url('customer_delivered_email'); ?>"><?php _e( 'Edit', 'woocommerce' ) ?></a>
									</fieldset>
								</td>
							</tr>
							<tr valign="top" class="partial_shipped_row <?php if(!get_option('wc_ast_status_partial_shipped')){echo 'disable_row'; } ?>">	
								<td class="forminp" style="width: 30px;">
									<span class="mdl-list__item-secondary-action">
										<label class="mdl-switch mdl-js-switch mdl-js-ripple-effect" for="wc_ast_status_partial_shipped">
											<input type="hidden" name="wc_ast_status_partial_shipped" value="0"/>
											<input type="checkbox" id="wc_ast_status_partial_shipped" name="wc_ast_status_partial_shipped" class="mdl-switch__input order_status_toggle" <?php if(get_option('wc_ast_status_partial_shipped')){echo 'checked'; } ?> value="1"/>
										</label>
									</span>
								</td>
								<td class="forminp status-label-column">
									<span class="order-label wc-partially-shipped">
										<?php 
										if(get_option('wc_ast_status_partial_shipped')){
											_e( wc_get_order_status_name( 'partial-shipped' ), 'woo-advanced-shipment-tracking' );	
										} else{
											_e( 'Partially Shipped', 'woo-advanced-shipment-tracking' );
										} ?>								
									</span>
								</td>												
								<td class="forminp">								
									<?php
									$wcast_enable_partial_shipped_email = get_option('woocommerce_customer_partial_shipped_order_settings');
									if($wcast_enable_partial_shipped_email['enabled'] == 'yes' || $wcast_enable_partial_shipped_email['enabled'] == 1){
										$partial_checked = 'checked';
									} else{
										$partial_checked = '';									
									}
									?>
									<fieldset>
										<input class="input-text regular-input color_input" type="text" name="wc_ast_status_partial_shipped_label_color" id="wc_ast_status_partial_shipped_label_color" style="" value="<?php echo get_option('wc_ast_status_partial_shipped_label_color')?>" placeholder="">
										<select class="select custom_order_color_select" id="wc_ast_status_partial_shipped_label_font_color" name="wc_ast_status_partial_shipped_label_font_color">									
											<option value="#fff" <?php if(get_option('wc_ast_status_partial_shipped_label_font_color') == '#fff'){ echo 'selected'; }?>><?php _e( 'Light Font', 'woo-advanced-shipment-tracking' ); ?></option>
											<option value="#000" <?php if(get_option('wc_ast_status_partial_shipped_label_font_color') == '#000'){ echo 'selected'; }?>><?php _e( 'Dark Font', 'woo-advanced-shipment-tracking' ); ?></option>
										</select>
										<label class="send_email_label">
											<input type="hidden" name="wcast_enable_partial_shipped_email" value="0"/>
											<input type="checkbox" name="wcast_enable_partial_shipped_email" id="wcast_enable_partial_shipped_email"class="enable_order_status_email_input"  <?php echo $partial_checked; ?> value="1"><?php _e( 'Send Email', 'woo-advanced-shipment-tracking' ); ?></label>
											<a class='settings_edit' href="<?php echo wcast_initialise_customizer_email::get_customizer_url('customer_partial_shipped_email'); ?>"><?php _e( 'Edit', 'woocommerce' ) ?></a>
									</fieldset>
								</td>
							</tr>
							<tr valign="top" class="updated_tracking_row <?php if(!get_option('wc_ast_status_updated_tracking')){echo 'disable_row'; } ?>">		
								<td class="forminp" style="width: 30px;">
									<span class="mdl-list__item-secondary-action">
										<label class="mdl-switch mdl-js-switch mdl-js-ripple-effect" for="wc_ast_status_updated_tracking">
											<input  type="hidden" name="wc_ast_status_updated_tracking" value="0"/>
											<input type="checkbox" id="wc_ast_status_updated_tracking" name="wc_ast_status_updated_tracking" class="mdl-switch__input order_status_toggle" <?php if(get_option('wc_ast_status_updated_tracking')){echo 'checked'; } ?> value="1"/>
										</label>
									</span>
								</td>
								<td class="forminp status-label-column">
									<span class="order-label wc-updated-tracking">
										<?php 
										if(get_option('wc_ast_status_updated_tracking')){
											_e( wc_get_order_status_name( 'updated-tracking' ), 'woo-advanced-shipment-tracking' );	
										} else{
											_e( 'Updated Tracking', 'woo-advanced-shipment-tracking' );
										} ?>								
									</span>
								</td>						
								<td class="forminp">							
									<?php
									$wcast_enable_updated_tracking_email = get_option('woocommerce_customer_updated_tracking_order_settings');
									if($wcast_enable_updated_tracking_email['enabled'] == 'yes' || $wcast_enable_updated_tracking_email['enabled'] == 1){
										$updated_tracking_checked = 'checked';
									} else{
										$updated_tracking_checked = '';									
									}
									?>
									<fieldset>
										<input class="input-text regular-input color_input" type="text" name="wc_ast_status_updated_tracking_label_color" id="wc_ast_status_updated_tracking_label_color" style="" value="<?php echo get_option('wc_ast_status_updated_tracking_label_color')?>" placeholder="">
										<select class="select custom_order_color_select" id="wc_ast_status_updated_tracking_label_font_color" name="wc_ast_status_updated_tracking_label_font_color">									
											<option value="#fff" <?php if(get_option('wc_ast_status_updated_tracking_label_font_color') == '#fff'){ echo 'selected'; }?>><?php _e( 'Light Font', 'woo-advanced-shipment-tracking' ); ?></option>
											<option value="#000" <?php if(get_option('wc_ast_status_updated_tracking_label_font_color') == '#000'){ echo 'selected'; }?>><?php _e( 'Dark Font', 'woo-advanced-shipment-tracking' ); ?></option>
										</select>
										<label class="send_email_label">
											<input  type="hidden" name="wcast_enable_updated_tracking_email" value="0"/>
											<input type="checkbox" name="wcast_enable_updated_tracking_email" id="wcast_enable_updated_tracking_email" class="enable_order_status_email_input" <?php echo $updated_tracking_checked; ?> value="1"><?php _e( 'Send Email', 'woo-advanced-shipment-tracking' ); ?>
										</label>
										<a class='settings_edit' href="<?php echo wcast_initialise_customizer_email::get_customizer_url('customer_updated_tracking_email'); ?>"><?php _e( 'Edit', 'woocommerce' ) ?></a>
									</fieldset>
								</td>
							</tr>
							<?php do_action("ast_orders_status_column_end"); ?>	
						</tbody>
					</table>	
					<?php wp_nonce_field( 'wc_ast_order_status_form', 'wc_ast_order_status_form_nonce' );?>	
					<input type="hidden" name="action" value="wc_ast_custom_order_status_form_update">					
					<p class="description-below-table"><?php echo sprintf(__('<strong>Note:</strong> - If you use the custom order status, when you deactivate the plugin, you must register the order status, otherwise these orders will not display on your orders admin. You can find more information and the code <a href="%s" target="blank">snippet</a> to use in functions.php here.', 'woo-advanced-shipment-tracking'), 'https://www.zorem.com/docs/woocommerce-advanced-shipment-tracking/plugin-settings/#code-snippets'); ?></p>
			</div>
		</form>	
	</div>	
	<?php include 'zorem_admin_sidebar.php';?>
</section>