<section id="content_tracking_page" class="inner_tab_section">
	<div class="tab_inner_container">	
		<div class="outer_form_table">	
			<table class="form-table heading-table">
				<tbody>
					<tr valign="top">
						<td>
							<h3 style=""><?php _e( 'Tracking Page', 'woo-advanced-shipment-tracking' ); ?></h3>
						</td>
						<td style="width: 40px;">
							<span class="mdl-list__item-secondary-action">
								<label class="mdl-switch mdl-js-switch mdl-js-ripple-effect" for="wc_ast_use_tracking_page">
									<input type="hidden" name="wc_ast_use_tracking_page" value="0"/>
									<input type="checkbox" id="wc_ast_use_tracking_page" name="wc_ast_use_tracking_page" class="mdl-switch__input" <?php if(get_option('wc_ast_use_tracking_page') == 1){ echo 'checked'; } ?> value="1"/>
								</label>
							</span>								
						</td>							
					</tr>
				</tbody>
			</table>
			<table class="form-table tracking_page_table">
				<tbody>
					<tr valign="top" class="">
						<th scope="row" class="titledesc" style="width: 30%;">
							<label><?php _e( 'Select Tracking Page', 'woo-advanced-shipment-tracking' ); ?></label>						
						</th>
						<td>
							<?php $page_list = wp_list_pluck( get_pages(), 'post_title', 'ID' ); ?>
							<select class="select select2" id="wc_ast_trackship_page_id" name="wc_ast_trackship_page_id">
								<?php
									foreach($page_list as $page_id => $page_name){ ?>
										<option <?php if(get_option('wc_ast_trackship_page_id') == $page_id){ echo 'selected'; }?> value="<?php echo $page_id; ?>"><?php echo $page_name; ?></option>
									<?php } ?>
							</select>
							<fieldset style="margin-top: 10px;">							
								<span class="tracking_page_desc"><?php _e( 'Note - If you select a different page than the Shipment Tracking page, add the [wcast-track-order] shortcode to the selected page content.', 'woo-advanced-shipment-tracking' ); ?> <a href="https://www.zorem.com/docs/woocommerce-advanced-shipment-tracking/integration/" target="blank"><?php _e( 'more info', 'woo-advanced-shipment-tracking' ); ?></a></span>
							</fieldset>
						</td>	
					</tr>					
				</tbody>
			</table>
			<table class="form-table tracking_page_table tracking-layout-table">
				<tbody>						
					<tr valign="top" class="">
						<th scope="row" class="titledesc" style="width: 55%;vertical-align: top;">
							<h3 style="margin: 0"><?php _e( 'Tracking Page Layout', 'woo-advanced-shipment-tracking' ); ?></h3>
							<span class="select_t_layout_section">
								<input type="radio" name="wc_ast_select_tracking_page_layout" id="t_layout_1" value="t_layout_1" class="radio-img" <?php if(get_option('wc_ast_select_tracking_page_layout','t_layout_1') == 't_layout_1'){ echo 'checked'; } ?>/>
								<label for="t_layout_1">
									<img src="<?php echo wc_advanced_shipment_tracking()->plugin_dir_url()?>assets/images/t_layout_1.jpg"/>
								</label>
							</span>
							<span class="select_t_layout_section">
								<input type="radio" name="wc_ast_select_tracking_page_layout" id="t_layout_2" value="t_layout_2" <?php if(get_option('wc_ast_select_tracking_page_layout','t_layout_1') == 't_layout_2'){ echo 'checked'; } ?> class="radio-img" />
								<label for="t_layout_2">
									<img src="<?php echo wc_advanced_shipment_tracking()->plugin_dir_url()?>assets/images/t_layout_2.jpg"/>
								</label>
							</span>
						</th>
						<td class="forminp" style="padding-top: 40px;">
							<div>
								<label class="tracking_color_label">
									<span style="margin-right: 20px;"><?php _e( 'Text Color', 'woo-advanced-shipment-tracking' ); ?></span>
									<span class="">
										<input class="input-text regular-input" type="text" name="wc_ast_select_primary_color" id="wc_ast_select_primary_color" style="" value="<?php echo get_option('wc_ast_select_primary_color')?>" >
									</span>
								</label></br></br>
								<label class="tracking_color_label">
									<span style="margin-right: 3px;"><?php _e( 'Border color', 'woo-advanced-shipment-tracking' ); ?></span>
									<span class="">
										<input class="input-text regular-input" type="text" name="wc_ast_select_border_color" id="wc_ast_select_border_color" style="" value="<?php echo get_option('wc_ast_select_border_color')?>" >
									</span>
								</label>									
							</div>	
							</br>								
							<div class="tracking_layout_options_div">
								<label>
									<input type="hidden" name="wc_ast_hide_tracking_provider_image" value="0"/>
									<input type="checkbox" name="wc_ast_hide_tracking_provider_image" value="1" id="wc_ast_hide_tracking_provider_image" <?php if(get_option('wc_ast_hide_tracking_provider_image') == 1){ echo 'checked'; } ?>>
									<?php _e( 'Hide Shipping Provider Image', 'woo-advanced-shipment-tracking' ); ?>
								</label>
								<label>
									<input type="hidden" name="wc_ast_hide_tracking_events" value="0"/>
									<input type="checkbox" name="wc_ast_hide_tracking_events" value="1" id="wc_ast_hide_tracking_events" <?php if(get_option('wc_ast_hide_tracking_events') == 1){ echo 'checked'; } ?>>
									<?php _e( 'Hide tracking event details', 'woo-advanced-shipment-tracking' ); ?>
								</label>
								<label>
									<input type="hidden" name="wc_ast_remove_trackship_branding" value="0"/>
									<input type="checkbox" name="wc_ast_remove_trackship_branding" value="1" id="wc_ast_remove_trackship_branding" <?php if(get_option('wc_ast_remove_trackship_branding') == 1){ echo 'checked'; } ?>>
									<?php _e( 'Remove TrackShip branding', 'woo-advanced-shipment-tracking' ); ?>
								</label>
							</div>	
						</td>
					</tr>	
					<tr valign="top">						
						<td class="button-column" colspan="2">
							<div class="submit">								
								<button name="save" class="button-primary woocommerce-save-button btn_ast2 btn_large" type="submit" value="Save changes"><?php _e( 'Save Changes', 'woo-advanced-shipment-tracking' ); ?></button>
								<div class="spinner"></div>								
								<input type="hidden" name="action" value="wc_ast_trackship_form_update">
							</div>	
						</td>
					</tr>
					<tr valign="top" class="tracking_page_table tracking_preview_tr">
						<td colspan="2">
							<h3 style="margin: 0"><strong><?php _e( 'Preview', 'woo-advanced-shipment-tracking' ); ?></strong></h3>
							<iframe id="tracking_preview_iframe" class="tracking_preview_iframe" src="<?php echo get_home_url(); ?>?action=preview_tracking_page" class="tracking-preview-link"></iframe>						
						</td>
					</tr>
				</tbody>
			</table>
			<table class="form-table tracking-layout-table tracking_save_table">
				<tbody>	
					<tr valign="top">						
						<td class="button-column" colspan="2">
							<div class="submit">								
								<button name="save" class="button-primary woocommerce-save-button btn_ast2 btn_large" type="submit" value="Save changes"><?php _e( 'Save Changes', 'woo-advanced-shipment-tracking' ); ?></button>
								<div class="spinner"></div>								
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