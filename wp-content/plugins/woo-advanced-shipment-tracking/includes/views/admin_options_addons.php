<?php
/**
 * html code for tools tab
 */
?>
<section id="content6" class="tab_section">
	<div class="d_table" style="">
		<div class="tab_inner_container">	
			<form method="post" id="wc_ast_addons_form" class="addons_inner_container" action="" enctype="multipart/form-data"> 
				<div class="ast_addons_section">
					<div class="outer_form_table">
						<table class="form-table heading-table">
							<tbody>
								<tr valign="top" class="addons_header ts_addons_header">
									<td>
										<img src="<?php echo wc_advanced_shipment_tracking()->plugin_dir_url()?>assets/images/ts-banner.jpg">
									</td>
								</tr>
								<tr valign="top">
									<td>
										<h3 style=""><?php _e( 'TrackShip', 'woo-advanced-shipment-tracking' ); ?></h3>
									</td>					
								</tr>
							</tbody>
						</table>					
						<table class="form-table">
							<tbody>						
								<tr style="height: 140px;">
									<td>
										<?php 
										$wc_ast_api_key = get_option('wc_ast_api_key');
										if($wc_ast_api_key){
											echo '<p>';
											_e( 'You are now connected with TrackShip! TrackShip makes it effortless to automate your post shipping operations and get tracking and delivery status updates directly in the WooCommerce admin.', 'woo-advanced-shipment-tracking' ); 
											echo '</p>';
										} else{ ?>
											<p style="margin-top: 4px;"><?php _e( 'TracksShip is a premium shipment tracking API flatform that fully integrates with WooCommerce with the Advanced Shipment Tracking. TrackShip automates the order management workflows, reduces customer inquiries, reduces time spent on customer service, and improves the post-purchase experience and satisfaction of your customers.', 'woo-advanced-shipment-tracking' ); ?></p>
											<p style="margin-top: 4px;"><?php _e( 'You must have account TracksShip and connect your store in order to activate these advanced features:', 'woo-advanced-shipment-tracking' ); ?></p>
										<?php } ?>													
									</td>																
								</tr>
								<tr>
									<td class="forminp button-column">
										<?php if($wc_ast_api_key){ ?>
										<fieldset>
											<a href="https://my.trackship.info/" target="_blank" class="button-primary btn_green2 btn_large">
												<span class=""><label><?php _e( 'Connected', 'woo-advanced-shipment-tracking' ); ?></label><span class="dashicons dashicons-yes"></span></span>
											</a>
										</fieldset>					
										<?php } else{ ?>
										<fieldset>
											<a href="https://trackship.info/?utm_source=wpadmin&utm_campaign=tspage" target="_blank" class="button-primary btn_ast2 btn_large"><?php _e( 'SIGNUP NOW', 'woo-advanced-shipment-tracking' ); ?></a>
										</fieldset>		
										<?php } ?>		
									</td>
								</tr>
							</tbody>
						</table>
					</div>	
				</div>
				
				<?php do_action('ast_addons_section_html_after_trackship');?>									
				
				<div class="ast_addons_section">	
					<div class="outer_form_table">
						<table class="form-table heading-table">
							<tbody>
								<tr valign="top" class="addons_header tracking_item_addons_header">
									<td>
										<img src="<?php echo wc_advanced_shipment_tracking()->plugin_dir_url()?>assets/images/Tracking-Per-Item-addon.jpg">
									</td>
								</tr>
								<tr valign="top">
									<td>
										<h3 style="">Tracking Per Item Add-on</h3>
									</td>					
								</tr>
							</tbody>
						</table>
						<?php if ( !class_exists( 'ast_woo_advanced_shipment_tracking_by_products' ) ) { ?>	
						<table class="form-table">
							<tbody>						
								<tr style="height: 140px;">
									<td>
										<p style="margin-top: 4px;"><?php _e( 'The Tracking Per Item add-on extends the AST plugin and allows you to attach tracking numbers to specific line items and to line item quantities.', 'woo-advanced-shipment-tracking' ); ?></p>
									</td>
								</tr>
							</tbody>
						</table>	
						<table class="form-table">
							<tbody>
								<tr valign="top">						
									<td class="button-column">
										<div class="submit">																
											<a href="https://www.zorem.com/shop/tracking-per-item-ast-add-on/" target="blank" class="button-primary btn_ast2 btn_large"><?php _e( 'Get This Add-on >', 'woo-advanced-shipment-tracking' ); ?></a>	
										</div>	
									</td>
								</tr>
							</tbody>
						</table>							
						<?php } else{ 
							do_action('tracking_per_item_addon_license_form'); ?>							
							<?php
						} ?>
					</div>	
				</div>
		
				<div class="ast_addons_section">
					<div class="outer_form_table">
						<table class="form-table heading-table">
							<tbody>
								<tr valign="top" class="addons_header">
									<td>
										<img src="<?php echo wc_advanced_shipment_tracking()->plugin_dir_url()?>assets/images/smswoo.png">
									</td>
								</tr>
								<tr valign="top">
									<td>
										<h3 style="">SMS for WooCommerce</h3>
									</td>					
								</tr>
							</tbody>
						</table>
							
						<table class="form-table">
							<tbody>						
								<tr style="height: 140px;">
									<td>
										<p style="margin-top: 4px;"><?php _e( 'Keep your customers happy by offering automated SMS text messages with order updates via Twilio/Nexmo. Send SMS updates to customers when their order status is updated. You can also manually send SMS messages through the Edit Order screen.', 'woo-advanced-shipment-tracking' ); ?></p>
									</td>
								</tr>
							</tbody>
						</table>	
						<table class="form-table">
							<tbody>
								<tr valign="top">						
									<td class="button-column">
										<div class="submit">	
											<?php 
											if( function_exists('SMSWOO') ){
												if ( SMSWOO()->license->get_license_status() ){ ?>
													<button name="save" class="button-primary btn_ast2 btn_large" type="button"><?php _e('Active','woo-advanced-shipment-tracking');?></button>
												<?php } else{ ?>
													<a href="https://www.zorem.com/products/sms-for-woocommerce/" target="blank" class="button-primary btn_ast2 btn_large"><?php _e( 'Get This Add-on >', 'woo-advanced-shipment-tracking' ); ?></a>
												<?php }} else{ ?>	
													<a href="https://www.zorem.com/products/sms-for-woocommerce/" target="blank" class="button-primary btn_ast2 btn_large"><?php _e( 'Get This Add-on >', 'woo-advanced-shipment-tracking' ); ?></a>	
											<?php } ?>
										</div>	
									</td>
								</tr>
							</tbody>
						</table>
					</div>	
				</div>
				<?php do_action('ast_addons_section_html');?>	
			</form>
		</div>
<?php include 'zorem_admin_addons_sidebar.php'; ?>
	</div>
</section>