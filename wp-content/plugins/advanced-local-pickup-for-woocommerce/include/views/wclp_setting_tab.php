<section id="wclp_content1" class="wclp_tab_section">
    <div class="wclp_tab_inner_container">
        <form method="post" id="wclp_setting_tab_form">
            <div class="wclp_outer_form_table">
				<table class="form-table heading-table">
					<tbody>
						<tr valign="top">
							<td>
								<h3 style=""><?php _e( 'General Settings', 'advanced-local-pickup-for-woocommerce' ); ?></h3>
							</td>
						</tr>
					</tbody>
				</table>
				<?php $this->get_html2( $this->wclp_general_setting_fields_func() ); ?>
				<table class="form-table">
					<tbody>
						<tr valign="top">						
							<td class="button-column">
								<div class="submit wclp-btn">
									<button name="save" class="wclp-save button-primary woocommerce-save-button" type="submit" value="Save changes"><?php _e( 'Save Changes', 'advanced-local-pickup-for-woocommerce' ); ?></button>
									<div class="spinner workflow_spinner" style="float:none"></div>
									<input type="hidden" name="action" value="wclp_setting_form_update">
								</div>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			
			<div class="wclp_outer_form_table">
				<table class="form-table heading-table">
					<tbody>
						<tr valign="top">
							<td>
								<h3 style=""><?php _e( 'Local Pickup Order Statuses', 'advanced-local-pickup-for-woocommerce' ); ?></h3>
							</td>
						</tr>
					</tbody>
				</table>
				<table class="form-table order-status-table">
					<tbody>
						<tr valign="top" class="">
							<td class="forminp status-label-column">
								<span class="order-label wc-ready-pickup" style="background:<?php echo get_option('wclp_ready_pickup_status_label_color');?>;color:<?php echo get_option('wclp_ready_pickup_status_label_font_color');?>">
									<?php _e( 'Ready for pickup', 'advanced-local-pickup-for-woocommerce' ); ?>
								</span>
							</td>								
							<td class="forminp">							
								<?php
								$wclp_enable_ready_pickup_email = get_option('woocommerce_customer_ready_pickup_order_settings');
								if($wclp_enable_ready_pickup_email['enabled'] == 'yes' || $wclp_enable_ready_pickup_email['enabled'] == 1){
									$ready_pickup_checked = 'checked';
								} else{
									$ready_pickup_checked = '';									
								}
								?>
								<fieldset>
									<input class="input-text regular-input " type="text" name="wclp_ready_pickup_status_label_color" id="wclp_ready_pickup_status_label_color" style="" value="<?php echo get_option('wclp_ready_pickup_status_label_color')?>" placeholder="">
									<select class="select" id="wclp_ready_pickup_status_label_font_color" name="wclp_ready_pickup_status_label_font_color">	
										<option value="#fff" <?php if(get_option('wclp_ready_pickup_status_label_font_color') == '#fff'){ echo 'selected'; }?>><?php _e( 'Light Font', 'advanced-local-pickup-for-woocommerce' ); ?></option>
										<option value="#000" <?php if(get_option('wclp_ready_pickup_status_label_font_color') == '#000'){ echo 'selected'; }?>><?php _e( 'Dark Font', 'advanced-local-pickup-for-woocommerce' ); ?></option>
									</select>
									<label class="send_email_label">
										<input type="hidden" name="wclp_enable_ready_pickup_email" value="0"/>
										<input type="checkbox" name="wclp_enable_ready_pickup_email" id="wclp_enable_ready_pickup_email" <?php echo $ready_pickup_checked; ?> value="1"><?php _e( 'Send Email', 'advanced-local-pickup-for-woocommerce' ); ?>
									</label>
									<a class='settings_edit' href="<?php echo wclp_ready_pickup_customizer_email::get_customizer_url('customer_ready_pickup_email'); ?>"><?php _e( 'Edit', 'woocommerce' ) ?></a>
								</fieldset>
							</td>
						</tr>					
						<tr valign="top" class="">
							<td class="forminp status-label-column">
								<span class="order-label wc-pickup" style="background:<?php echo get_option('wclp_pickup_status_label_color');?>;color:<?php echo get_option('wclp_pickup_status_label_font_color');?>">
									<?php _e( 'Picked up', 'advanced-local-pickup-for-woocommerce' ); ?>
								</span>
							</td>								
							<td class="forminp">							
								<?php
								$wclp_enable_pickup_email = get_option('woocommerce_customer_pickup_order_settings');
								
								if($wclp_enable_pickup_email['enabled'] == 'yes' || $wclp_enable_pickup_email['enabled'] == 1){
									$pickup_checked = 'checked';
								} else{
									$pickup_checked = '';									
								}
								?>
								<fieldset>
									<input class="input-text regular-input " type="text" name="wclp_pickup_status_label_color" id="wclp_pickup_status_label_color" style="" value="<?php echo get_option('wclp_pickup_status_label_color')?>" placeholder="">
									<select class="select" id="wclp_pickup_status_label_font_color" name="wclp_pickup_status_label_font_color">	
										<option value="#fff" <?php if(get_option('wclp_pickup_status_label_font_color') == '#fff'){ echo 'selected'; }?>><?php _e( 'Light Font', 'advanced-local-pickup-for-woocommerce' ); ?></option>
										<option value="#000" <?php if(get_option('wclp_pickup_status_label_font_color') == '#000'){ echo 'selected'; }?>><?php _e( 'Dark Font', 'advanced-local-pickup-for-woocommerce' ); ?></option>
									</select>
									<label class="send_email_label">
										<input type="hidden" name="wclp_enable_pickup_email" value="0"/>
										<input type="checkbox" name="wclp_enable_pickup_email" id="wclp_enable_pickup_email" <?php echo $pickup_checked; ?> value="1"><?php _e( 'Send Email', 'advanced-local-pickup-for-woocommerce' ); ?>
									</label>
									<a class='settings_edit' href="<?php echo wclp_pickup_customizer_email::get_customizer_url('customer_pickup_email'); ?>"><?php _e( 'Edit', 'woocommerce' ) ?></a>
								</fieldset>
							</td>
						</tr>
					</tbody>
				</table>
				<table class="form-table">
					<tbody>
						<tr valign="top">
							<td>
								<p class=""><strong>Note:</strong> - If you use the custom order status, when you deactivate the plugin, you must register the order status, otherwise these orders will not display on your orders admin. You can find more information and the code <a href="https://www.zorem.com/docs/woocommerce-advanced-shipment-tracking/plugin-settings/#code-snippets" target="blank">snippet</a> to use in functions.php here.</p>
							</td>
						</tr>
					</tbody>
				</table>				
				<table class="form-table">
					<tbody>
						<tr valign="top">						
							<td class="button-column">
								<div class="submit wclp-btn">
									<button name="save" class="wclp-save button-primary woocommerce-save-button" type="submit" value="Save changes"><?php _e( 'Save Changes', 'advanced-local-pickup-for-woocommerce' ); ?></button>
									<div class="spinner workflow_spinner" style="float:none"></div>									
									<?php wp_nonce_field( 'wclp_setting_form_action', 'wclp_setting_form_nonce_field' ); ?>
									<input type="hidden" name="action" value="wclp_setting_form_update">
								</div>
							</td>
						</tr>
					</tbody>
				</table>	
			</div>		
        </form>	
    </div> 
	<?php include 'wclp_admin_sidebar.php';?>	
</section>