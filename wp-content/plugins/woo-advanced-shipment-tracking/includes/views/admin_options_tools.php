<?php
/**
 * html code for tools tab
 */
?>
<section id="content_tools" class="inner_tab_section">
	<div class="tab_inner_container">		
		<div class="d_table" style="">
			<div class="tab_inner_container">
				<div class="outer_form_table get_shipment_status_tool">				
					<table class="form-table heading-table">
						<tbody>
							<tr valign="top">
								<td>
									<h3 style=""><?php _e( 'Get Shipment Status', 'woo-advanced-shipment-tracking' ); ?></h3>
								</td>					
							</tr>
						</tbody>
					</table>
					<table class="form-table">
						<tbody>						
							<tr>
								<td>
									<p><?php _e( 'You can send all your orders from the last 30 days to get shipment status from TrackShip:', 'woo-advanced-shipment-tracking' ); ?></p>
								</td>
							</tr>
						</tbody>
					</table>								
					<?php 
					$trackship = WC_Advanced_Shipment_Tracking_Trackship::get_instance();
					$this->get_html( $trackship->get_trackship_bulk_actions_data() ); ?>							
				</div>
			</div>	
		</div>		
	</div>
	<?php include 'zorem_admin_sidebar.php'; ?>	
</section>