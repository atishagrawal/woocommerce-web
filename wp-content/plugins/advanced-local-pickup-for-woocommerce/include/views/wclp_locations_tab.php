<section id="wclp_content2" class="wclp_tab_section">
    <div class="wclp_tab_inner_container">
        <form method="post" id="wclp_location_tab_form">            	
			<div class="wclp_outer_form_table">
				<table class="form-table heading-table">
					<tbody>
						<tr valign="top">
							<td>
								<h3 style=""><?php _e( 'Location Settings', 'advanced-local-pickup-for-woocommerce' ); ?></h3>
							</td>
						</tr>
					</tbody>
				</table>
				<?php $this->get_html( $this->wclp_location_setting_fields_func() ); ?>				
				<table class="form-table">
					<tbody>
						<tr valign="top">						
							<td class="button-column">
								<div class="submit wclp-btn">
									<button name="save" class="wclp-save button-primary woocommerce-save-button btn_location_submit" type="submit" value="Save changes"><?php _e( 'Save Changes', 'advanced-local-pickup-for-woocommerce' ); ?></button>
									<span class="alp_error_msg"></span>	
									<div class="spinner workflow_spinner" style="float:none"></div>									
									<?php wp_nonce_field( 'wclp_location_form_action', 'wclp_location_form_nonce_field' ); ?>
									<input type="hidden" name="action" value="wclp_location_form_update">
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