<?php
$alp = new WC_Local_Pickup_admin;
$wclp_ready_pickup_customizer_email = new wclp_ready_pickup_customizer_email();

$location_box_heading = $alp->get_option_value_from_array('pickup_instruction_display_settings','location_box_heading','');

$location_box_padding = $alp->get_option_value_from_array('pickup_instruction_display_settings','location_box_padding',$wclp_ready_pickup_customizer_email->defaults['location_box_padding']);

$location_box_border_color = $alp->get_option_value_from_array('pickup_instruction_display_settings','location_box_border_color',$wclp_ready_pickup_customizer_email->defaults['location_box_border_color']);

$location_box_background_color = $alp->get_option_value_from_array('pickup_instruction_display_settings','location_box_background_color',$wclp_ready_pickup_customizer_email->defaults['location_box_background_color']);

if(!empty(get_option( 'wclp_store_days'))){ 	
	$n = 0;
	$new_array = [];
	$previousValue = [];
	
	foreach($w_day as $day=>$value){				
		if(isset($value['checked']) && $value['checked'] == 1){																	
			if($value != $previousValue){
				$n++;
			}
			$new_array[$n][$day] = $value;					
			$previousValue = $value;
		}								
	}
}
?>
<h2 class="local_pickup_email_title"><?php echo $location_box_heading; ?></h2>
<?php if(!empty(get_option( 'wclp_store_instruction'))){ ?>
	<p><?php echo get_option( 'wclp_store_instruction'); ?></p>				
<?php } ?>
<div class="wclp_order_address">
	<div class="wclp_location_box <?php if(!empty($new_array)){	echo 'wclp_location_box1'; }?>">
		<div class="wclp_location_box_heading">
			<?php _e('Address', 'advanced-local-pickup-for-woocommerce'); ?>
		</div>
		<div class="wclp_location_box_content">
			<p class="wclp_pickup_adress_p"><?php echo get_option( 'wclp_store_name' ); ?></p>
			<p class="wclp_pickup_adress_p"><?php echo get_option( 'wclp_store_address', get_option('woocommerce_store_address') ); ?></p>
			<p class="wclp_pickup_adress_p"><?php echo get_option( 'wclp_store_address_2', get_option('woocommerce_store_address_2') ); ?></p>
			<p class="wclp_pickup_adress_p"><?php echo get_option( 'wclp_store_city', get_option('woocommerce_store_city')); echo ' ';echo get_option( 'wclp_store_postcode', get_option('woocommerce_store_postcode') ); ?></p>
			<?php if($store_country){ ?><p class="wclp_pickup_adress_p"><?php if($store_state != ''){ echo WC()->countries->get_states( $store_country )[$store_state];echo ', ';}echo WC()->countries->countries[$store_country]; ?></p>
			<?php } ?>
		</div>
	</div>				
<?php if(!empty(get_option( 'wclp_store_days'))){ 			
	if(!empty($new_array)){
?>	
	<div class="wclp_location_box">
		<div class="wclp_location_box_heading">
			<?php _e('Work Hours', 'advanced-local-pickup-for-woocommerce'); ?>
		</div>
		<div class="wclp_location_box_content">
			<?php
				
				foreach($new_array as $key => $data){					
					if(count($data) == 1){							
						if(reset($data)['wclp_store_hour'] != '' && reset($data)['wclp_store_hour_end'] != ''){
							reset($data);
							?>		
							<p class="wclp_work_hours_p"><?php echo __(ucfirst(key($data)), 'advanced-local-pickup-for-woocommerce'); ?>
							<span>: </span>	
							<?php echo reset($data)['wclp_store_hour'].' '; echo ' - '; echo reset($data)['wclp_store_hour_end']; ?></p>								
					<?php } } ?>						
					<?php
					if(count($data) == 2){
						if(reset($data)['wclp_store_hour'] != '' && reset($data)['wclp_store_hour_end'] != ''){
						reset($data);
						$array_key_first = key($data);
						end($data);
						$array_key_last = key($data);
						?>
							<p class="wclp_work_hours_p"><?php echo __(ucfirst($array_key_first), 'advanced-local-pickup-for-woocommerce'); ?><span> - </span><?php echo __(ucfirst($array_key_last), 'advanced-local-pickup-for-woocommerce'); ?>
							<span>: </span>		
							<?php echo reset($data)['wclp_store_hour'].' '; echo ' - '; echo reset($data)['wclp_store_hour_end']; ?></p>
					<?php } } ?>									
					<?php 
					if(count($data) > 2){ 
						if(reset($data)['wclp_store_hour'] != '' && reset($data)['wclp_store_hour_end'] != ''){
						reset($data);
						$array_key_first = key($data);
						end($data);
						$array_key_last = key($data);		
						?>
							<p class="wclp_work_hours_p">
							<?php echo __(ucfirst($array_key_first), 'advanced-local-pickup-for-woocommerce'); ?> <?php echo __(' to', 'advanced-local-pickup-for-woocommerce'); ?> <?php echo __(ucfirst($array_key_last), 'advanced-local-pickup-for-woocommerce'); ?>								
							<span>: </span>	
							<?php echo reset($data)['wclp_store_hour'].' '; echo ' - '; echo reset($data)['wclp_store_hour_end']; ?></p>
							
					<?php 										
					} }	
				}
			?>	
		</div>		
	</div>
<?php } } ?>
</div>
<style>
	.wclp_order_address{
		margin: 10px 0;
		display: table;
		width: 100%;
	}
	.local_pickup_email_title{
		margin-bottom: 10px !important;
	}
	.wclp_location_box{
		display: table-cell;
		width:50%;	
		border: 1px solid <?php echo $location_box_border_color; ?> !important;
	}
	.wclp_location_box1{
		border-right: 0 !important;
	}
	.wclp_location_box_heading {								
		border-bottom: 1px solid <?php echo $location_box_border_color; ?> !important;
		border-top:0 !important;
		border-left:0 !important;
		border-right:0 !important;
		padding: <?php echo $location_box_padding; ?>px;		
		font-weight: bold;
	}
	.wclp_location_box_content{												
		padding: <?php echo $location_box_padding; ?>px;		
	}
	.wclp_work_hours_p{
		margin: 0 !important;
		line-height: 20px;
	}
	.wclp_pickup_adress_p{
		margin: 0 !important;
		line-height: 20px;
	}
	.wclp_order_address{
		background: <?php echo $location_box_background_color; ?>; 
	}
</style>