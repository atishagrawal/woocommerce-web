<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_Advanced_Shipment_Tracking_Front {

	/**
	 * Instance of this class.
	 *
	 * @var object Class Instance
	 */
	private static $instance;
	
	/**
	 * Initialize the main plugin function
	*/
    public function __construct() {
		global $wpdb;
		$this->table = $wpdb->prefix."woo_shippment_provider";
		if( is_multisite() ){
			if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
				require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
			}
			if ( is_plugin_active_for_network( 'woo-advanced-shipment-tracking/woocommerce-advanced-shipment-tracking.php' ) ) {
				$main_blog_prefix = $wpdb->get_blog_prefix(BLOG_ID_CURRENT_SITE);			
				$this->table = $main_blog_prefix."woo_shippment_provider";	
			} else{
				$this->table = $wpdb->prefix."woo_shippment_provider";
			}			
		} else{
			$this->table = $wpdb->prefix."woo_shippment_provider";	
		}
		
		$this->init();	
    }
	
	/**
	 * Get the class instance
	 *
	 * @return WC_Advanced_Shipment_Tracking_Actions
	*/
	public static function get_instance() {

		if ( null === self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}
	
	/*
	* init from parent mail class
	*/
	public function init(){
		add_shortcode( 'wcast-track-order', array( $this, 'woo_track_order_function') );
		
		add_action( 'wp_enqueue_scripts', array( $this, 'front_styles' ));		
		
		add_action( 'wp_ajax_nopriv_get_tracking_info', array( $this, 'get_tracking_info_fun') );
		add_action( 'wp_ajax_get_tracking_info', array( $this, 'get_tracking_info_fun') );
		
	}	
			
	/**
	 * Include front js and css
	*/
	public function front_styles(){		
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		wp_register_script( 'jquery-blockui', WC()->plugin_url() . '/assets/js/jquery-blockui/jquery.blockUI' . $suffix . '.js', array( 'jquery' ), '2.70', true );
		wp_register_script( 'front-js', wc_advanced_shipment_tracking()->plugin_dir_url().'assets/js/front.js', array( 'jquery' ), wc_advanced_shipment_tracking()->version );
		wp_localize_script( 'front-js', 'zorem_ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
		
		wp_register_style( 'front_style',  wc_advanced_shipment_tracking()->plugin_dir_url() . 'assets/css/front.css', array(), wc_advanced_shipment_tracking()->version );		
		
		$action = (isset($_REQUEST["action"])?$_REQUEST["action"]:"");
		if($action == 'preview_tracking_page'){
			wp_enqueue_style( 'front_style' );
			wp_enqueue_script( 'front-js' );	
		}		
	}
	
	public function woo_track_order_function(){	
		wp_enqueue_style( 'front_style' );
		wp_enqueue_script( 'jquery-blockui' );
		wp_enqueue_script( 'front-js' );	
		global $wpdb;
		$wc_ast_api_key = get_option('wc_ast_api_key');	
		$primary_color = get_option('wc_ast_select_primary_color');
		$success_color = get_option('wc_ast_select_success_color');
		$warning_color = get_option('wc_ast_select_warning_color');
		$border_color = get_option('wc_ast_select_border_color');
		$hide_tracking_events = get_option('wc_ast_hide_tracking_events');
		$hide_tracking_provider_image = get_option('wc_ast_hide_tracking_provider_image');	
		$tracking_page_layout = get_option('wc_ast_select_tracking_page_layout','t_layout_1');			
		?>
		<style>		
		<?php if($primary_color){ ?>		
			body .tracker-progress-bar-with-dots .secondary .dot {
				border-color: <?php echo $primary_color; ?>;
			}			
			body .tracking-number{
				color: <?php echo $primary_color; ?> !important;
			}
			body .tracking-detail.tracking-layout-2{
				color: <?php echo $primary_color; ?>;
			}
			body .tracking-detail .tracking-details{
				color: <?php echo $primary_color; ?>;
			}
		<?php } ?>	
		<?php if($border_color){ ?>
			body .col.tracking-detail{
				border: 1px solid <?php echo $border_color; ?>;
			}
		<?php }	 ?>
		</style>
		<?php 
		if(!$wc_ast_api_key){
			?>
			<p><a href="https://trackship.info/" target="blank">TrackShip</a> is not active.</p>
			<?php
			return;
		}
		if(isset($_GET['order_id']) &&  isset($_GET['order_key'])){
			
			$order_id = wc_clean($_GET['order_id']);
			$order = wc_get_order( $order_id );
			$order_key = $order->get_order_key();
		
			if($order_key != $_GET['order_key']){
				return;
			}
			
			if(empty($order)){
				return;
			}
			
			if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
				$tracking_items = get_post_meta( $order_id, '_wc_shipment_tracking_items', true );			
			} else {				
				$tracking_items = $order->get_meta( '_wc_shipment_tracking_items', true );			
			}
			$shipment_status = get_post_meta( $order_id, "shipment_status", true);			
			if(!$tracking_items){
				unset($order_id);
			}			
		}
	
	?>
	
		<?php 	
		if(!isset($order_id)){
		ob_start();		
		?>
			<div class="track-order-section">
				<form method="post" class="order_track_form">			
					<p><?php echo apply_filters( 'ast_tracking_page_front_text', __( 'To track your order please enter your Order ID in the box below and press the "Track" button. This was given to you on your receipt and in the confirmation email you should have received.', 'woo-advanced-shipment-tracking' ) ); ?></p>
					<p class="form-row form-row-first"><label for="order_id"><?php echo apply_filters( 'ast_tracking_page_front_order_label', __( 'Order ID', 'woocommerce' ) ); ?></label> <input class="input-text" type="text" name="order_id" id="order_id" value="" placeholder="<?php _e( 'Found in your order confirmation email.', 'woo-advanced-shipment-tracking' ); ?>"></p>
					<p class="form-row form-row-last"><label for="order_email"><?php echo apply_filters( 'ast_tracking_page_front_order_email_label', __( 'Order Email', 'woo-advanced-shipment-tracking' ) ); ?></label> <input class="input-text" type="text" name="order_email" id="order_email" value="" placeholder="<?php _e( 'Found in your order confirmation email.', 'woo-advanced-shipment-tracking' ); ?>"></p>				
					<div class="clear"></div>
					<input type="hidden" name="action" value="get_tracking_info">
					<p class="form-row"><button type="submit" class="button" name="track" value="Track"><?php echo apply_filters( 'ast_tracking_page_front_track_label', __( 'Track', 'woo-advanced-shipment-tracking' ) ); ?></button></p>
					<div class="track_fail_msg" style="display:none;color: red;"></div>	
				</form>
			</div>
		<?php 
		
		$form = ob_get_clean();	
		return $form;
		
		} else{
			ob_start();												
		
			$num = 1;
			$total_trackings = sizeof($tracking_items);	
		
		foreach($tracking_items as $key => $item){
		
			$tracking_number = $item['tracking_number'];
			$trackship_url = 'https://trackship.info';
			$tracking_provider = $item['tracking_provider'];
			$results = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $this->table WHERE ts_slug = %s", $tracking_provider ) );
			$tracking_provider = $results->provider_name;
			$custom_provider_name = $results->custom_provider_name;
			$custom_thumb_id = $results->custom_thumb_id;
			
			/*** Update in 2.7.9
			* Date - 20/01/2020
			* Remove api call code after three month - get_tracking_info
			***/
			if( isset($shipment_status[$key]['tracking_events'])){					
				$tracker = new \stdClass();
				$tracker->ep_status = $shipment_status[$key]['status'];							
				$tracker->tracking_detail = json_encode($shipment_status[$key]['tracking_events']);
				if(isset($shipment_status[$key]['tracking_destination_events'])){
					$tracker->tracking_destination_events = json_encode($shipment_status[$key]['tracking_destination_events']);
				}
				$tracker->est_delivery_date = $shipment_status[$key]['est_delivery_date'];				
				$decoded_data = true;								
				
			} else {								
				/*** Update in 2.4.1 
				* Change URL
				* Add User Key
				***/
				$url = $trackship_url.'/wp-json/tracking/get_tracking_info';		
				$args['body'] = array(
					'tracking_number' => $tracking_number,
					'order_id' => $order_id,
					'domain' => get_home_url(),
					'user_key' => $wc_ast_api_key,
				);	
				$response = wp_remote_post( $url, $args );
				
				if ( is_wp_error( $response ) ) {
					
				} else{
					$data = $response['body'];				
					$decoded_data = json_decode($data);								
					
					$tracker = new \stdClass();
					$tracker->ep_status = '';
					if(!empty($decoded_data)){
						$tracker = $decoded_data[0];
					}	
				}						
			}
			
			$tracking_detail_org = '';	
			$trackind_detail_by_status_rev = '';
			
			if(isset($tracker->tracking_detail) && $tracker->tracking_detail != 'null'){						
				$tracking_detail_org = json_decode($tracker->tracking_detail);						
				$trackind_detail_by_status_rev = array_reverse($tracking_detail_org);	
			}
			$tracking_details_by_date = array();
			foreach((array)$trackind_detail_by_status_rev as $key => $details){
				if(isset($details->datetime)){		
					$date = date('Y-m-d', strtotime($details->datetime));
					$tracking_details_by_date[$date][] = $details;
				}
			}
			
			$tracking_destination_detail_org = '';	
			$trackind_destination_detail_by_status_rev = '';
			
			if(isset($tracker->tracking_destination_events) && $tracker->tracking_destination_events != 'null'){						
				$tracking_destination_detail_org = json_decode($tracker->tracking_destination_events);	
					
				$trackind_destination_detail_by_status_rev = array_reverse($tracking_destination_detail_org);	
			}
			
			$tracking_destination_details_by_date = array();
			
			foreach((array)$trackind_destination_detail_by_status_rev as $key => $details){
				if(isset($details->datetime)){		
					$date = date('Y-m-d', strtotime($details->datetime));
					$tracking_destination_details_by_date[$date][] = $details;
				}
			}	
		
		if(!empty($decoded_data)){										
			if($tracking_page_layout == 't_layout_1'){ ?>
		
			<div class="tracking-detail col">			
				<?php if($total_trackings > 1 ){ ?>
				<p class="shipment_heading"><?php 				
				echo sprintf(__("Shipment - %s (out of %s)", 'woo-advanced-shipment-tracking'), $num , $total_trackings); ?></p>
				<?php } 
				echo $this->tracking_page_header($order_id,$tracking_provider,$custom_provider_name, $custom_thumb_id,  $tracking_number,$tracker);
				
				if($tracker->ep_status == 'pending_trackship' || $tracker->ep_status == 'INVALID_TRACKING_NUM' || $tracker->ep_status == 'carrier_unsupported' || $tracker->ep_status == 'invalid_user_key' || $tracker->ep_status == 'wrong_shipping_provider' || $tracker->ep_status == 'deleted' || $tracker->ep_status == 'pending'){
				} elseif(isset($tracker->ep_status)){
					echo $this->layout1_progress_bar($tracker);
				} 
				
				if( !empty($trackind_detail_by_status_rev) && $hide_tracking_events != 1  ){
					echo $this->layout1_tracking_details( $trackind_detail_by_status_rev, $tracking_details_by_date, $trackind_destination_detail_by_status_rev, $tracking_destination_details_by_date );
				} ?>	
			</div>
			<?php } else{ 											
			?>
			<div class="tracking-detail tracking-layout-2 col">
				<?php if($total_trackings > 1 ){ ?>
					<p class="shipment_heading"><?php echo sprintf(__("Shipment - %s (out of %s)", 'woo-advanced-shipment-tracking'), $num , $total_trackings); ?></p>
				<?php } 			
			echo $tracking_header = $this->tracking_page_header($order_id,$tracking_provider,$custom_provider_name, $custom_thumb_id,$tracking_number,$tracker); 			
			if($tracker->ep_status == 'pending_trackship' || $tracker->ep_status == 'INVALID_TRACKING_NUM' || $tracker->ep_status == 'carrier_unsupported' || $tracker->ep_status == 'invalid_user_key' || $tracker->ep_status == 'wrong_shipping_provider' || $tracker->ep_status == 'deleted' || $tracker->ep_status == 'pending'){
			} elseif(isset($tracker->ep_status)){
				echo $this->layout2_progress_bar($tracker);
			}									
			
			if( !empty($trackind_detail_by_status_rev) && $hide_tracking_events != 1  ){				
				echo $this->layout2_tracking_details( $trackind_detail_by_status_rev, $tracking_details_by_date, $trackind_destination_detail_by_status_rev, $tracking_destination_details_by_date );	
			} ?>
		
		</div>	
			<?php } } else{ ?>
			<div class="tracking-detail col">
				<h1 class="shipment_status_heading text-secondary text-center"><?php _e( 'Tracking&nbsp;#&nbsp;'.$tracking_number, 'woo-advanced-shipment-tracking' ); ?></h1>
				<h3 class="text-center"><?php _e( 'Tracking details not found in TrackShip', 'woo-advanced-shipment-tracking' ); ?></h3>
			</div>
		<?php } 
		$num++;
		}		
		
		$remove_trackship_branding =  get_option('wc_ast_remove_trackship_branding');
		
		if($remove_trackship_branding != 1){ ?> 
			<div class="trackship_branding">
				<p>Shipment Tracking info by <a href="https://trackship.info" title="TrackShip" target="blank">TrackShip</a></p>
			</div>
		<?php }
		$form = ob_get_clean();	
		return $form;		
		}		
	}
	
	public function get_tracking_info_fun(){
		wp_enqueue_style( 'front_style' );
		wp_enqueue_script( 'jquery-blockui' );
		wp_enqueue_script( 'front-js' );
		global $wpdb;
		$wc_ast_api_key = get_option('wc_ast_api_key');	
		$primary_color = get_option('wc_ast_select_primary_color');
		$success_color = get_option('wc_ast_select_success_color');
		$warning_color = get_option('wc_ast_select_warning_color');
		$border_color = get_option('wc_ast_select_border_color');
		$hide_tracking_events = get_option('wc_ast_hide_tracking_events');
		$hide_tracking_provider_image = get_option('wc_ast_hide_tracking_provider_image');	
		$tracking_page_layout = get_option('wc_ast_select_tracking_page_layout','t_layout_1');		
		?>
		<style>		
		<?php if($primary_color){ ?>		
		body .tracker-progress-bar-with-dots .secondary .dot {
			border-color: <?php echo $primary_color; ?>;
		}
		body .text-secondary{
			color: <?php echo $primary_color; ?> !important;
		}
		body .progress-bar.bg-secondary:before{
			background-color: <?php echo $primary_color; ?>;
		}
		body .tracking-number{
			color: <?php echo $primary_color; ?> !important;
		}	
		body .tracking-detail.tracking-layout-2{
			color: <?php echo $primary_color; ?>;
		}
		body .tracking-detail .tracking-details{
			color: <?php echo $primary_color; ?>;
		}
		<?php } ?>	
		<?php if($border_color){ ?>
		body .col.tracking-detail{
			border: 1px solid <?php echo $border_color; ?>;
		}
		<?php }	 ?>
		</style>
		<?php
		if(!$wc_ast_api_key){
			return;
		}
		
		$order_id = wc_clean($_POST['order_id']);		
		$email = sanitize_email($_POST['order_email']);
		
		$wast = WC_Advanced_Shipment_Tracking_Actions::get_instance();
		$order_id = $wast->get_formated_order_id($order_id);
		
		$order = wc_get_order( $order_id );
		if(empty($order)){
			ob_start();		
		?>
			<div class="track-order-section">
				<form method="post" class="order_track_form">			
					<p><?php echo apply_filters( 'ast_tracking_page_front_text', __( 'To track your order please enter your Order ID in the box below and press the "Track" button. This was given to you on your receipt and in the confirmation email you should have received.', 'woo-advanced-shipment-tracking' ) ); ?></p>
					<p class="form-row form-row-first"><label for="order_id"><?php echo apply_filters( 'ast_tracking_page_front_order_label', __( 'Order ID', 'woocommerce' ) ); ?></label> <input class="input-text" type="text" name="order_id" id="order_id" value="" placeholder="<?php _e( 'Found in your order confirmation email.', 'woo-advanced-shipment-tracking' ); ?>"></p>
					<p class="form-row form-row-last"><label for="order_email"><?php echo apply_filters( 'ast_tracking_page_front_order_email_label', __( 'Order Email', 'woo-advanced-shipment-tracking' ) ); ?></label> <input class="input-text" type="text" name="order_email" id="order_email" value="" placeholder="<?php _e( 'Found in your order confirmation email.', 'woo-advanced-shipment-tracking' ); ?>"></p>				
					<div class="clear"></div>
					<input type="hidden" name="action" value="get_tracking_info">
					<p class="form-row"><button type="submit" class="button" name="track" value="Track"><?php echo apply_filters( 'ast_tracking_page_front_track_label', __( 'Track', 'woo-advanced-shipment-tracking' ) ); ?></button></p>
					<div class="track_fail_msg" style="display:block;color: red;"><?php _e( 'Order not found.', 'woo-advanced-shipment-tracking' ); ?></div>	
				</form>
			</div>
		<?php 
		
		$form = ob_get_clean();
			echo $form;exit;
			exit;
		}
		
		$wast = WC_Advanced_Shipment_Tracking_Actions::get_instance();
		$order_id = $wast->get_formated_order_id($order_id);									
		$order_email = $order->get_billing_email();
		
		if(strtolower($order_email) != strtolower($email)){
			ob_start();		
		?>
			<div class="track-order-section">
				<form method="post" class="order_track_form">			
					<p><?php echo apply_filters( 'ast_tracking_page_front_text', __( 'To track your order please enter your Order ID in the box below and press the "Track" button. This was given to you on your receipt and in the confirmation email you should have received.', 'woo-advanced-shipment-tracking' ) ); ?></p>
					<p class="form-row form-row-first"><label for="order_id"><?php echo apply_filters( 'ast_tracking_page_front_order_label', __( 'Order ID', 'woocommerce' ) ); ?></label> <input class="input-text" type="text" name="order_id" id="order_id" value="" placeholder="<?php _e( 'Found in your order confirmation email.', 'woo-advanced-shipment-tracking' ); ?>"></p>
					<p class="form-row form-row-last"><label for="order_email"><?php echo apply_filters( 'ast_tracking_page_front_order_email_label', __( 'Order Email', 'woo-advanced-shipment-tracking' ) ); ?></label> <input class="input-text" type="text" name="order_email" id="order_email" value="" placeholder="<?php _e( 'Found in your order confirmation email.', 'woo-advanced-shipment-tracking' ); ?>"></p>				
					<div class="clear"></div>
					<input type="hidden" name="action" value="get_tracking_info">
					<p class="form-row"><button type="submit" class="button" name="track" value="Track"><?php echo apply_filters( 'ast_tracking_page_front_track_label', __( 'Track', 'woo-advanced-shipment-tracking' ) ); ?></button></p>
					<div class="track_fail_msg" style="display:none;color: red;"></div>	
				</form>
			</div>
		<?php 
		
		$form = ob_get_clean();	
		echo $form;exit;
		}
		
		if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
			$tracking_items = get_post_meta( $order_id, '_wc_shipment_tracking_items', true );			
		} else {			
			$tracking_items = $order->get_meta( '_wc_shipment_tracking_items', true );			
		} 
		
		$shipment_status = get_post_meta( $order_id, "shipment_status", true);
		if(!$tracking_items){
			ob_start();		
			?>
			<div class="track-order-section">
				<form method="post" class="order_track_form">			
					<p><?php echo apply_filters( 'ast_tracking_page_front_text', __( 'To track your order please enter your Order ID in the box below and press the "Track" button. This was given to you on your receipt and in the confirmation email you should have received.', 'woo-advanced-shipment-tracking' ) ); ?></p>
					<p class="form-row form-row-first"><label for="order_id"><?php echo apply_filters( 'ast_tracking_page_front_order_label', __( 'Order ID', 'woocommerce' ) ); ?></label> <input class="input-text" type="text" name="order_id" id="order_id" value="" placeholder="<?php _e( 'Found in your order confirmation email.', 'woo-advanced-shipment-tracking' ); ?>"></p>
					<p class="form-row form-row-last"><label for="order_email"><?php echo apply_filters( 'ast_tracking_page_front_order_email_label', __( 'Order Email', 'woo-advanced-shipment-tracking' ) ); ?></label> <input class="input-text" type="text" name="order_email" id="order_email" value="" placeholder="<?php _e( 'Found in your order confirmation email.', 'woo-advanced-shipment-tracking' ); ?>"></p>				
					<div class="clear"></div>
					<input type="hidden" name="action" value="get_tracking_info">
					<p class="form-row"><button type="submit" class="button" name="track" value="Track"><?php echo apply_filters( 'ast_tracking_page_front_track_label', __( 'Track', 'woo-advanced-shipment-tracking' ) ); ?></button></p>
					<div class="track_fail_msg" style="display:block;color: red;"><?php _e( 'Tracking details not found.', 'woo-advanced-shipment-tracking' ); ?></div>	
				</form>
			</div>
		<?php 
		
		$form = ob_get_clean();
			echo $form;exit;
			exit;
		}
		
		$num = 1;
		$total_trackings = sizeof($tracking_items);	
		
		foreach($tracking_items as $key => $item){
			
			$tracking_number = $item['tracking_number'];
			$trackship_url = 'https://trackship.info';
			$tracking_provider = $item['tracking_provider'];
			$results = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $this->table WHERE ts_slug = %s", $tracking_provider ) );
			$tracking_provider = $results->provider_name;
			$custom_provider_name = $results->custom_provider_name;
			$custom_thumb_id = $results->custom_thumb_id;
			
			/*** Update in 2.7.9
			* Date - 20/01/2020
			* Remove api call code after three month - get_tracking_info
			***/
			if( isset($shipment_status[$key]['tracking_events'])){					
				$tracker = new \stdClass();
				$tracker->ep_status = $shipment_status[$key]['status'];							
				$tracker->tracking_detail = json_encode($shipment_status[$key]['tracking_events']);
				$tracker->tracking_destination_events = json_encode($shipment_status[$key]['tracking_destination_events']);
				$tracker->est_delivery_date = $shipment_status[$key]['est_delivery_date'];				
				$decoded_data = true;								
				
			} else {								
				/*** Update in 2.4.1 
				* Change URL
				* Add User Key
				***/
				$url = $trackship_url.'/wp-json/tracking/get_tracking_info';		
				$args['body'] = array(
					'tracking_number' => $tracking_number,
					'order_id' => $order_id,
					'domain' => get_home_url(),
					'user_key' => $wc_ast_api_key,
				);	
				$response = wp_remote_post( $url, $args );
				$data = $response['body'];				
				$decoded_data = json_decode($data);								
				
				$tracker = new \stdClass();
				$tracker->ep_status = '';
				if(!empty($decoded_data)){
					$tracker = $decoded_data[0];
				}			
			}
			
			$tracking_detail_org = '';	
			$trackind_detail_by_status_rev = '';
			
			if(isset($tracker->tracking_detail) && $tracker->tracking_detail != 'null'){						
				$tracking_detail_org = json_decode($tracker->tracking_detail);						
				$trackind_detail_by_status_rev = array_reverse($tracking_detail_org);	
			}	
			$tracking_details_by_date = array();
			foreach((array)$trackind_detail_by_status_rev as $key => $details){
				if(isset($details->datetime)){		
					$date = date('Y-m-d', strtotime($details->datetime));
					$tracking_details_by_date[$date][] = $details;
				}
			}

			$tracking_destination_detail_org = '';	
			$trackind_destination_detail_by_status_rev = '';
			
			if(isset($tracker->tracking_destination_events) && $tracker->tracking_destination_events != 'null'){						
				$tracking_destination_detail_org = json_decode($tracker->tracking_destination_events);	
					
				$trackind_destination_detail_by_status_rev = array_reverse($tracking_destination_detail_org);	
			}
			
			$tracking_destination_details_by_date = array();
			
			foreach((array)$trackind_destination_detail_by_status_rev as $key => $details){
				if(isset($details->datetime)){		
					$date = date('Y-m-d', strtotime($details->datetime));
					$tracking_destination_details_by_date[$date][] = $details;
				}
			}			
		
		if(!empty($decoded_data)){										
			if($tracking_page_layout == 't_layout_1'){ ?>
		
			<div class="tracking-detail col">			
				<?php if($total_trackings > 1 ){ ?>
				<p class="shipment_heading"><?php 				
				echo sprintf(__("Shipment - %s (out of %s)", 'woo-advanced-shipment-tracking'), $num , $total_trackings); ?></p>
				<?php } 
				echo $this->tracking_page_header($order_id,$tracking_provider,$custom_provider_name, $custom_thumb_id,$tracking_number,$tracker);
				
				if($tracker->ep_status == 'pending_trackship' || $tracker->ep_status == 'INVALID_TRACKING_NUM' || $tracker->ep_status == 'carrier_unsupported' || $tracker->ep_status == 'invalid_user_key' || $tracker->ep_status == 'wrong_shipping_provider' || $tracker->ep_status == 'deleted' || $tracker->ep_status == 'pending'){
				} elseif(isset($tracker->ep_status)){
					echo $this->layout1_progress_bar($tracker);
				} 
				
				if( !empty($trackind_detail_by_status_rev) && $hide_tracking_events != 1  ){
					echo $this->layout1_tracking_details( $trackind_detail_by_status_rev, $tracking_details_by_date, $trackind_destination_detail_by_status_rev, $tracking_destination_details_by_date );
				} ?>	
			</div>
			<?php } else{ 											
			?>
			<div class="tracking-detail tracking-layout-2 col">
				<?php if($total_trackings > 1 ){ ?>
					<p class="shipment_heading"><?php echo sprintf(__("Shipment - %s (out of %s)", 'woo-advanced-shipment-tracking'), $num , $total_trackings); ?></p>
				<?php } 			
			echo $tracking_header = $this->tracking_page_header($order_id,$tracking_provider,$custom_provider_name, $custom_thumb_id,$tracking_number,$tracker); 			
			if($tracker->ep_status == 'pending_trackship' || $tracker->ep_status == 'INVALID_TRACKING_NUM' || $tracker->ep_status == 'carrier_unsupported' || $tracker->ep_status == 'invalid_user_key' || $tracker->ep_status == 'wrong_shipping_provider' || $tracker->ep_status == 'deleted' || $tracker->ep_status == 'pending'){
			} elseif(isset($tracker->ep_status)){
				echo $this->layout2_progress_bar($tracker);
			}			
		
			if( !empty($trackind_detail_by_status_rev) && $hide_tracking_events != 1  ){
				echo $this->layout2_tracking_details( $trackind_detail_by_status_rev, $tracking_details_by_date, $trackind_destination_detail_by_status_rev, $tracking_destination_details_by_date );
			} ?>
		
		</div>	
			<?php } } else{ ?>
			<div class="tracking-detail col">
				<h1 class="shipment_status_heading text-secondary text-center"><?php _e( 'Tracking&nbsp;#&nbsp;'.$tracking_number, 'woo-advanced-shipment-tracking' ); ?></h1>
				<h3 class="text-center"><?php _e( 'Tracking details not found in TrackShip', 'woo-advanced-shipment-tracking' ); ?></h3>
			</div>
		<?php } 
		$num++;	
		}
		
		$remove_trackship_branding =  get_option('wc_ast_remove_trackship_branding');
		
		if($remove_trackship_branding != 1){ ?>
		
		<div class="trackship_branding">
			<p>Shipment Tracking info by <a href="https://trackship.info" title="TrackShip" target="blank">TrackShip</a></p>
		</div>	
		
		<?php }
		
		exit; 
	}
	
	public function tracking_page_header($order_id,$tracking_provider,$custom_provider_name = NULL, $custom_thumb_id = 0,$tracking_number,$tracker){
		
		if($tracker->est_delivery_date){	
			$unixTimestamp = strtotime($tracker->est_delivery_date);				
			$day = date("l", $unixTimestamp);
		}
		
		$hide_tracking_provider_image = get_option('wc_ast_hide_tracking_provider_image');
		$upload_dir   = wp_upload_dir();	
		$ast_directory = $upload_dir['baseurl'] . '/ast-shipping-providers/';
		$ast_base_directory = $upload_dir['basedir'] . '/ast-shipping-providers/';
		
		if($custom_thumb_id != 0){
			$image_attributes = wp_get_attachment_image_src( $custom_thumb_id , array('60','60') );
			$src = $image_attributes[0];
		} else if(!file_exists($ast_base_directory.''.sanitize_title($tracking_provider).'.png')){
			$src = wc_advanced_shipment_tracking()->plugin_dir_url().'assets/shipment-provider-img/'.sanitize_title($tracking_provider).'.png?v='.wc_advanced_shipment_tracking()->version;
		} else{
			$src = $ast_directory.''.sanitize_title($tracking_provider).'.png?v='.wc_advanced_shipment_tracking()->version;
		}
		
		if($custom_provider_name != NULL){
			$provider_name = $custom_provider_name;	
		} else{
			$provider_name = $tracking_provider;	
		}		 
		
		$ast = WC_Advanced_Shipment_Tracking_Actions::get_instance();
		$order_id = $ast->get_custom_order_number($order_id);		
		?>		
		<div class="tracking-header tracking-desktop-header">
			<div class="col-md col-md-6">					
				<span class="tracking-number"><?php _e( 'Order', 'woocommerce' ); ?>: <strong>#<?php echo apply_filters( 'ast_order_number_filter', $order_id); ?></strong></span><br>
				<span class="tracking-number"><span class="header_tracking_provider">
					<?php echo apply_filters( 'ast_provider_title', esc_html( $provider_name )); ?>:</span> 
					<strong><?php echo $tracking_number; ?></strong>
				</span>					
				<h1 class="shipment_status_heading <?php if($tracker->ep_status == "delivered" || $tracker->ep_status == "available_for_pickup") { echo 'text-success'; } elseif($tracker->ep_status == "return_to_sender" || $tracker->ep_status == "failure") { echo 'text-warning'; } else{ echo 'text-secondary'; } ?>">
					<?php echo apply_filters("trackship_status_filter",$tracker->ep_status);?>
				</h1>
				<span class="tracking-number">
					<?php _e( 'Est. Delivery Date', 'woo-advanced-shipment-tracking' ); ?>: <strong>
					<?php 
					if($tracker->est_delivery_date){
						echo $day; ?>, <?php echo  date('M d', strtotime($tracker->est_delivery_date));
					} else{
						echo 'N/A';
					} ?></strong>				
				</span>
			</div>
			<div class="col-md col-md-6 provider-image-div" style="<?php if($hide_tracking_provider_image == 1) { echo 'display:none'; };  ?>">
				<div class="text-right">
					<img class="provider_image" src="<?php echo $src; ?>">
				</div>
			</div>
		</div>
		<div class="tracking-header tracking-mobile-header">
			<div class="d-flex align-items-center header_top1">
				<div class="header_top_left" style="<?php if($hide_tracking_provider_image == 1) { echo 'display:none'; };  ?>">
					<img class="provider_image" src="<?php echo $src; ?>">
				</div>
				<div class="header_top_right">						
					<span class="tracking-number"><span class="header_tracking_provider"><?php echo apply_filters( 'ast_provider_title', esc_html( $provider_name )); ?>:</span> <strong><?php echo $tracking_number; ?></strong></span><br>
					<span class="tracking-number"><?php _e( 'Order', 'woocommerce' ); ?>: <strong>#<?php echo apply_filters( 'ast_order_number_filter', $order_id); ?></strong></span>
				</div>
			</div>
			<div class="col-md col-md-6 header_top2">														
				<h1 class="shipment_status_heading <?php if($tracker->ep_status == "delivered") { echo 'text-success'; } elseif($tracker->ep_status == "return_to_sender" || $tracker->ep_status == "failure") { echo 'text-warning'; } else{ echo 'text-secondary'; } ?>">
					<?php echo apply_filters("trackship_status_filter",$tracker->ep_status);?>
				</h1>
				<span class="tracking-number">
					<?php _e( 'Est. Delivery Date', 'woo-advanced-shipment-tracking' ); ?>: <strong>
					<?php 
					if($tracker->est_delivery_date){
						echo $day; ?>, <?php echo  date('M d', strtotime($tracker->est_delivery_date));
					} else{
						echo 'N/A';
					} ?></strong>				
				</span>
			</div>
		</div>
	<?php }
	
	public function layout1_progress_bar($tracker){
		if($tracker->ep_status == "unknown"){ $state0_class = 'unknown'; } else{ $state0_class = 'pre_transit'; }		
				
		if($tracker->ep_status == "on_hold" ){ 
			$state1_class = 'on_hold'; 
		} else{
			$state1_class = 'in_transit';
		}
		
		if($tracker->ep_status == "return_to_sender" ){ 
			$state2_class = 'return_to_sender'; 
		} elseif($tracker->ep_status == "failure"){
			$state2_class = 'failure';
		} elseif($tracker->ep_status == "available_for_pickup"){
			$state2_class = 'available_for_pickup';
		} else{
			$state2_class = 'out_for_delivery';
		}
		?>
		<div class="status-section desktop-section">
			<div class="tracker-progress-bar tracker-progress-bar-with-dots">
				<div class="progress">
					<div class="progress-bar <?php if($tracker->ep_status == "delivered") { echo 'bg-success'; } elseif($tracker->ep_status == "return_to_sender" || $tracker->ep_status == "failure"){ echo 'bg-warning'; } else{ echo 'bg-success';} ?>" style="<?php if($tracker->ep_status == "in_transit" || $tracker->ep_status == "on_hold") { echo 'width:33%;'; } elseif($tracker->ep_status == "out_for_delivery" || $tracker->ep_status == "available_for_pickup" || $tracker->ep_status == "return_to_sender" || $tracker->ep_status == "failure"){ echo 'width:67%';} elseif($tracker->ep_status == "delivered") { echo 'width:100%'; } ?>"></div>
				</div>
				<div class="<?php if($tracker->ep_status == "delivered") { echo 'success'; } elseif($tracker->ep_status == "return_to_sender" || $tracker->ep_status == "failure" || $tracker->ep_status == "unknown") { echo 'warning'; } else{ echo 'secondary';} ?>">
					<span class="dot state-0 <?php echo $state0_class; echo ' ';  if($tracker->ep_status =="pre_transit" || $tracker->ep_status =="unknown"){ echo ' current-state'; } else{ echo 'past-state';} ?>"></span>						
					
					<span class="dot state-1 <?php echo $state1_class; echo ' ';  if($tracker->ep_status == "in_transit" || $tracker->ep_status == "on_hold"){ echo 'current-state'; } elseif($tracker->ep_status == "pre_transit" || $tracker->ep_status =="unknown"){ echo 'future-state'; } else{ echo 'past-state'; } ?>"></span>
					
					<span class="dot state-2 <?php echo $state2_class; echo ' ';  if($tracker->ep_status == "out_for_delivery" || $tracker->ep_status == "available_for_pickup" || $tracker->ep_status == "failure" || $tracker->ep_status == "return_to_sender"){ echo ' current-state'; } elseif($tracker->ep_status == "pre_transit" || $tracker->ep_status =="unknown" || $tracker->ep_status == "in_transit" || $tracker->ep_status == "on_hold"){ echo ' future-state'; } else{ echo ' past-state'; } ?>"></span>
					
					<span class="dot state-3 <?php echo 'delivered'; echo ' '; if($tracker->ep_status == "delivered"){ echo 'current-state'; } elseif($tracker->ep_status == "pre_transit" || $tracker->ep_status =="unknown" || $tracker->ep_status == "in_transit"|| $tracker->ep_status == "on_hold" || $tracker->ep_status == "out_for_delivery" || $tracker->ep_status == "available_for_pickup" || $tracker->ep_status == "return_to_sender" || $tracker->ep_status == "failure"){ echo 'future-state'; } ?>"></span>
					
				</div>
			</div>
		</div>
		<div class="status-section mobile-section">
			<div class="tracker-progress-bar tracker-progress-bar-with-dots">
				<div class="progress <?php if($tracker->ep_status == "delivered") { echo 'bg-success'; } elseif($tracker->ep_status == "return_to_sender" || $tracker->ep_status == "failure"){ echo 'bg-warning'; } else{ echo 'bg-secondary';} ?>" style="<?php if($tracker->ep_status == "in_transit" || $tracker->ep_status == "on_hold") { echo 'height:33%;'; } elseif($tracker->ep_status == "out_for_delivery" || $tracker->ep_status == "available_for_pickup" || $tracker->ep_status == "return_to_sender" || $tracker->ep_status == "failure"){ echo 'height:67%';} elseif($tracker->ep_status == "delivered") { echo 'height:100%'; } ?>">
					<div class="progress-bar" style=""></div>
				</div>
				<div style="background-color: transparent;" class="<?php if($tracker->ep_status == "delivered") { echo 'success'; } elseif($tracker->ep_status == "return_to_sender" || $tracker->ep_status == "failure" || $tracker->ep_status == "unknown") { echo 'warning'; } else{ echo 'secondary';} ?>">
					<div class="dot-div">							
						<span class="dot state-0 <?php echo $state0_class?> <?php if($tracker->ep_status =="pre_transit" || $tracker->ep_status =="unknown"){ echo ' current-state'; } else{ echo 'past-state';} ?>"></span>
						<span class="state-label <?php if($tracker->ep_status =="pre_transit" || $tracker->ep_status =="unknown"){ echo 'current-state'; } else{ echo 'past-state';} ?>">
						<?php 
							if($tracker->ep_status == "unknown"){
								echo apply_filters("trackship_status_filter",'unknown');								
							} else{
								echo apply_filters("trackship_status_filter",'pre_transit');	
							}	
						?>						
						</span>
					</div>
			
					<div class="dot-div">	
						<span class="dot state-1 in_transit <?php if($tracker->ep_status == "in_transit" || $tracker->ep_status == "on_hold"){ echo 'current-state'; } elseif($tracker->ep_status == "pre_transit" || $tracker->ep_status =="unknown"){ echo 'future-state'; } else{ echo 'past-state'; } ?>"></span>
						<span class="state-label state-1 <?php if($tracker->ep_status == "in_transit" || $tracker->ep_status == "on_hold"){ echo 'current-state'; } elseif($tracker->ep_status == "pre_transit" || $tracker->ep_status =="unknown"){ echo 'future-state'; } else{ echo 'past-state'; } ?>">
							<?php //echo apply_filters("trackship_status_filter",'in_transit'); ?>						
							<?php
								if($tracker->ep_status == "on_hold"){
									echo apply_filters("trackship_status_filter",'on_hold');								
								} else {
									echo apply_filters("trackship_status_filter",'in_transit');								
								} 
							?>
						</span>
					</div>
					
					<div class="dot-div">
						<span class="dot state-2 <?php echo $state2_class; if($tracker->ep_status == "out_for_delivery" || $tracker->ep_status == "available_for_pickup" || $tracker->ep_status == "failure" || $tracker->ep_status == "return_to_sender"){ echo ' current-state'; } elseif($tracker->ep_status == "pre_transit" || $tracker->ep_status =="unknown" || $tracker->ep_status == "in_transit" || $tracker->ep_status == "on_hold"){ echo ' future-state'; } else{ echo ' past-state'; } ?>"></span>
						<span class="state-label state-2 <?php if($tracker->ep_status == "out_for_delivery" || $tracker->ep_status == "available_for_pickup" || $tracker->ep_status == "failure" || $tracker->ep_status == "return_to_sender"){ echo 'current-state'; } elseif($tracker->ep_status == "pre_transit" || $tracker->ep_status =="unknown" || $tracker->ep_status == "in_transit" || $tracker->ep_status == "on_hold"){ echo 'future-state'; } else{ echo ' past-state'; } ?>">
							<?php
								if($tracker->ep_status == "return_to_sender"){
									echo apply_filters("trackship_status_filter",'return_to_sender');								
								} elseif($tracker->ep_status == "failure"){
									echo apply_filters("trackship_status_filter",'failure');								
								} else{
									echo apply_filters("trackship_status_filter",'out_for_delivery');
								}
							?>						
						</span>
					</div>
					
					<div class="dot-div">	
						<span class="dot state-3 delivered <?php if($tracker->ep_status == "delivered"){ echo 'current-state'; } elseif($tracker->ep_status == "pre_transit" || $tracker->ep_status =="unknown" || $tracker->ep_status == "in_transit" || $tracker->ep_status == "on_hold" || $tracker->ep_status == "out_for_delivery" || $tracker->ep_status == "available_for_pickup" || $tracker->ep_status == "return_to_sender" || $tracker->ep_status == "failure"){ echo 'future-state'; }?>"></span>
						<span class="state-label state-3 <?php if($tracker->ep_status == "delivered"){ echo 'current-state'; } elseif($tracker->ep_status == "pre_transit" || $tracker->ep_status =="unknown" || $tracker->ep_status == "in_transit" || $tracker->ep_status == "on_hold" || $tracker->ep_status == "out_for_delivery" || $tracker->ep_status == "available_for_pickup" || $tracker->ep_status == "return_to_sender" || $tracker->ep_status == "failure"){ echo 'future-state'; }?>">
						<?php echo apply_filters("trackship_status_filter",'delivered'); ?>
						</span>
					</div>		
				</div>
			</div>
		</div>
	<?php }
	
	public function layout2_progress_bar($tracker){
		if($tracker->ep_status == 'pending_trackship' || $tracker->ep_status == 'INVALID_TRACKING_NUM' || $tracker->ep_status == 'carrier_unsupported' || $tracker->ep_status == 'invalid_user_key' || $tracker->ep_status == 'wrong_shipping_provider' || $tracker->ep_status == 'deleted' || $tracker->ep_status == 'pending' || $tracker->ep_status == 'unknown' || $tracker->ep_status == 'pre_transit'){
				$width = '0';
				$progress_bar_class = 'bg-secondary';
			} else if($tracker->ep_status == 'in_transit' || $tracker->ep_status == 'on_hold'){
				$width = '33%';
				$progress_bar_class = 'bg-secondary';
			} else if($tracker->ep_status == 'out_for_delivery'){
				$width = '67%';
				$progress_bar_class = 'bg-secondary';
			} else if($tracker->ep_status == 'available_for_pickup'){
				$width = '67%';
				$progress_bar_class = 'bg-success';
			} else if($tracker->ep_status == 'return_to_sender'){
				$width = '67%';
				$progress_bar_class = 'bg-warning';
			} else if($tracker->ep_status == 'delivered'){
				$width = '100%';
				$progress_bar_class = 'bg-success';
			} 			
			?>
			<div class="tracker-progress-bar tracker-progress-bar-flat">
				<div class="progress">
					<div class="progress-bar <?php echo $progress_bar_class; ?>" style="width: <?php echo $width; ?>;"></div>
				</div>
			</div>	
	<?php }
	
	public function layout1_tracking_details($trackind_detail_by_status_rev,$tracking_details_by_date,$trackind_destination_detail_by_status_rev, $tracking_destination_details_by_date){ 		
		?>
		<div class="tracking-details" style="">
			<div class="shipment_progress_heading_div">	               				
				<h4 class="tracking-number h4-heading text-uppercase"><?php _e( 'Tracking Details', 'woo-advanced-shipment-tracking' ); ?></h4>					
			</div>
			<?php if(!empty($tracking_details_by_date)){ ?>
			<div class="tracking_details_desktop">				
				<?php if(!empty($tracking_destination_details_by_date)){ ?>
				<div class="tracking_destination_details_by_date">
					<h4 style=""><?php _e( 'Destination Details', 'woo-advanced-shipment-tracking' ); ?></h4>					
					<?php 
					$a = 1;
					foreach($tracking_destination_details_by_date as $date => $date_details){
						if($a > 1)break;
						foreach($date_details as $key => $value){
						?>
						<div class="tracking_group_by_date d-flex mb-3">					
							<div class="d-md-flex w-100">
								<div class="date text-uppercase font-weight-demi-bold"><?php echo date_i18n( get_option( 'date_format' ), strtotime($date) ); //date( 'F j, Y', strtotime($date)); ?> <?php echo date_i18n( get_option( 'time_format' ), strtotime($value->datetime) )?></div>									
								<div class="message font-weight-demi-bold mr-md-3"><?php echo $value->message; ?></div>
								<div class="location text-uppercase ml-auto"><?php echo $value->tracking_location->city; ?></div>
							</div>						
						</div>	
					<?php } $a++; } ?>					
					<div class="old-details" style="">
						<?php 				
						$a = 1;					
						foreach($tracking_destination_details_by_date as $date => $date_details){ 						
							if($a == 1){
								$a++;
								continue;							
							} 
						foreach($date_details as $key => $value){ ?>					
							<div class="tracking_group_by_date d-flex mb-3">					
								<div class="d-md-flex w-100">
									<div class="date text-uppercase font-weight-demi-bold"><?php echo date_i18n( get_option( 'date_format' ), strtotime($date) );?> <?php echo date_i18n( get_option( 'time_format' ), strtotime($value->datetime) )?></div>									
									<div class="message font-weight-demi-bold mr-md-3"><?php echo $value->message; ?></div>
									<div class="location text-uppercase ml-auto"><?php echo $value->tracking_location->city; ?></div>
								</div>							
							</div>					
						<?php } ?> 						
						<?php } ?>
					</div>					
				</div>
				<?php } ?>
				
				<div class="tracking_details_by_date">
					<?php if(!empty($tracking_destination_details_by_date)){ ?>
						<h4 class="" style=""><?php _e( 'Origin Details', 'woo-advanced-shipment-tracking' ); ?></h4>
					<?php } 
					$a = 1;
					foreach($tracking_details_by_date as $date => $date_details){
						if($a > 1)break;
						foreach($date_details as $key => $value){
						?>
						<div class="tracking_group_by_date d-flex mb-3">					
							<div class="d-md-flex w-100">
								<div class="date text-uppercase font-weight-demi-bold"><?php echo date_i18n( get_option( 'date_format' ), strtotime($date) );?> <?php echo date_i18n( get_option( 'time_format' ), strtotime($value->datetime) ); ?></div>
								<div class="message font-weight-demi-bold mr-md-3"><?php echo $value->message; ?></div>
								<div class="location text-uppercase ml-auto"><?php echo $value->tracking_location->city; ?></div>
							</div>						
						</div>	
					<?php } $a++; } ?>					
					<div class="old-details" style="">
						<?php 				
						$a = 1;					
						foreach($tracking_details_by_date as $date => $date_details){ 						
							if($a == 1){
								$a++;
								continue;							
							} ?>						
						<?php foreach($date_details as $key => $value){ ?>					
							<div class="tracking_group_by_date d-flex mb-3">					
								<div class="d-md-flex w-100">
									<div class="date text-uppercase font-weight-demi-bold"><?php echo date_i18n( get_option( 'date_format' ), strtotime($date) ); ?> <?php echo date_i18n( get_option( 'time_format' ), strtotime($value->datetime) ) ?></div>									
									<div class="message font-weight-demi-bold mr-md-3"><?php echo $value->message; ?></div>
									<div class="location text-uppercase ml-auto"><?php echo $value->tracking_location->city; ?></div>
								</div>							
							</div>					
						<?php } ?> 						
						<?php } ?>
					</div>					
				</div>
				<a class="view_old_details" href="javaScript:void(0);" style="display: inline;"><?php _e( 'view more', 	'woo-advanced-shipment-tracking' ); ?></a>
				<a class="hide_old_details" href="javaScript:void(0);" style="display: none;"><?php _e( 'view less', 		'woo-advanced-shipment-tracking' ); ?></a>
			</div>
			
			<div class="tracking_details_mobile">
				
				<?php if(!empty($tracking_destination_details_by_date)){ ?>
				<div class="tracking_destination_details_by_date">
					<?php if(!empty($tracking_destination_details_by_date)){ ?>
						<h4 class="" style=""><?php _e( 'Destination Details', 'woo-advanced-shipment-tracking' ); ?></h4>
					<?php }
					$a = 1;
					foreach($tracking_destination_details_by_date as $date => $date_details){
						if($a > 1)break;
						foreach($date_details as $key => $value){ ?>
							<div class="d-flex mb-3 tracking_details_mobile_row">								
								<div class="d-md-flex w-100">							
									<div class="time mr-md-2"><span class="text-uppercase"><?php echo date_i18n( get_option( 'date_format' ), strtotime($date) ); //date( 'F j, Y', strtotime($date)); ?></span> <?php echo date_i18n( get_option( 'time_format' ), strtotime($value->datetime) ) //date( 'g:i a', strtotime($value->datetime)); ?></div>
									<div class="message font-weight-demi-bold mr-md-3"><?php echo $value->message; ?></div>
									<div class="location text-uppercase text-md-right ml-auto"><?php echo $value->tracking_location->city; ?></div>
								</div>						
							</div>	
						<?php }
					$a++;
					}
					?>				
					<div class="old-details" style="">
						<?php 				
						$a = 1;					
						foreach($tracking_destination_details_by_date as $date => $date_details){ 						
							if($a == 1){
								$a++;
								continue;							
							}
							foreach($date_details as $key => $value){
						?>
						<div class="d-flex mb-3 tracking_details_mobile_row">					
							<div class="d-md-flex w-100">								
								<div class="time mr-md-2"><span class="text-uppercase"><?php echo date_i18n( get_option( 'date_format' ), strtotime($date) ); //date( 'F j, Y', strtotime($date)); ?></span> <?php echo date_i18n( get_option( 'time_format' ), strtotime($value->datetime) ) //date( 'g:i a', strtotime($value->datetime)); ?></div>
								<div class="message font-weight-demi-bold mr-md-3"><?php echo $value->message; ?></div>
								<div class="location text-uppercase text-md-right ml-auto"><?php echo $value->tracking_location->city; ?></div>
							</div>							
						</div>
						<?php }	} ?>
					</div>					
				</div>
				<?php } ?>
				
				<div class="tracking_details_by_date">
					<?php if(!empty($tracking_destination_details_by_date)){ ?>
						<h4 class="" style=""><?php _e( 'Origin Details', 'woo-advanced-shipment-tracking' ); ?></h4>
					<?php }
					$a = 1;
					foreach($tracking_details_by_date as $date => $date_details){
						if($a > 1)break;
						foreach($date_details as $key => $value){ ?>
							<div class="d-flex mb-3 tracking_details_mobile_row">								
								<div class="d-md-flex w-100">							
									<div class="time mr-md-2"><span class="text-uppercase"><?php echo date_i18n( get_option( 'date_format' ), strtotime($date) ); //date( 'F j, Y', strtotime($date)); ?></span> <?php echo date_i18n( get_option( 'time_format' ), strtotime($value->datetime) ) //date( 'g:i a', strtotime($value->datetime)); ?></div>
									<div class="message font-weight-demi-bold mr-md-3"><?php echo $value->message; ?></div>
									<div class="location text-uppercase text-md-right ml-auto"><?php echo $value->tracking_location->city; ?></div>
								</div>						
							</div>	
						<?php }
					$a++;
					}
					?>				
					<div class="old-details" style="">
						<?php 				
						$a = 1;					
						foreach($tracking_details_by_date as $date => $date_details){ 						
							if($a == 1){
								$a++;
								continue;							
							}
							foreach($date_details as $key => $value){
						?>
						<div class="d-flex mb-3 tracking_details_mobile_row">					
							<div class="d-md-flex w-100">								
								<div class="time mr-md-2"><span class="text-uppercase"><?php echo date_i18n( get_option( 'date_format' ), strtotime($date) ); //date( 'F j, Y', strtotime($date)); ?></span> <?php echo date_i18n( get_option( 'time_format' ), strtotime($value->datetime) ) //date( 'g:i a', strtotime($value->datetime)); ?></div>
								<div class="message font-weight-demi-bold mr-md-3"><?php echo $value->message; ?></div>
								<div class="location text-uppercase text-md-right ml-auto"><?php echo $value->tracking_location->city; ?></div>
							</div>							
						</div>
						<?php }	} ?>
					</div>						
				</div>
				<a class="view_old_details" href="javaScript:void(0);" style="display: inline;"><?php _e( 'view more', 	'woo-advanced-shipment-tracking' ); ?></a>
				<a class="hide_old_details" href="javaScript:void(0);" style="display: none;"><?php _e( 'view less', 		'woo-advanced-shipment-tracking' ); ?></a>	
			</div>
			
			<?php } ?>
		</div>
	<?php }
	
	public function layout2_tracking_details( $trackind_detail_by_status_rev, $tracking_details_by_date, $trackind_destination_detail_by_status_rev, $tracking_destination_details_by_date){ 		
	?>
		<div class="tracking-details">
			<div class="shipment_progress_heading_div">	               				
				<h4 class="tracking-number h4-heading text-uppercase" style=""><?php _e( 'Tracking Details', 'woo-advanced-shipment-tracking' ); ?></h4>					
			</div>
			<div class="tracking_details_desktop">
				
				<?php if(!empty($tracking_destination_details_by_date)){ ?>				
				
				<div class="tracking_destination_details_by_date">
					<h4 style=""><?php _e( 'Destination Details', 'woo-advanced-shipment-tracking' ); ?></h4>	
					<?php 				
					$b = 1;					
					foreach($tracking_destination_details_by_date as $date => $date_details){
						if($b > 1)break;						
					?>
						<div class="tracking_group_by_date">
							<div class="date text-uppercase font-weight-bold mb-3"><?php echo date_i18n( get_option( 'date_format' ), strtotime($date) ); //date( 'F j, Y', strtotime($date)); ?></div>
							<?php foreach($date_details as $key => $value){ ?>
							<div class="d-flex mb-3">								
								<div class="d-md-flex w-100">							
									<div class="time mr-md-2"><?php echo date_i18n( get_option( 'time_format' ), strtotime($value->datetime) ) //date( 'g:i a', strtotime($value->datetime)); ?></div>
									<div class="message font-weight-demi-bold mr-md-3"><?php echo $value->message; ?></div>
									<div class="location text-uppercase text-md-right ml-auto"><?php echo $value->tracking_location->city; ?></div>
								</div>						
							</div>
							<?php } ?>	
						</div>
					<?php $b++; } ?>	
					<div class="old-details">
						<?php 				
						$b = 1;					
						foreach($tracking_destination_details_by_date as $date => $date_details){ 						
							if($b == 1){
								$b++;
								continue;							
							} ?>
						<div class="tracking_group_by_date">
							<div class="date text-uppercase font-weight-bold mb-3"><?php echo date_i18n( get_option( 'date_format' ), strtotime($date) ); //date( 'F j, Y', strtotime($date)); ?></div>
							<?php foreach($date_details as $key => $value){ ?>							
							<div class="d-flex mb-3">					
								<div class="d-md-flex w-100">								
									<div class="time text-gray-300 mr-md-2 text-success"><?php echo date_i18n( get_option( 'time_format' ), strtotime($value->datetime) ) //date( 'g:i a', strtotime($value->datetime)); ?></div>
									<div class="message font-weight-demi-bold mr-md-3"><?php echo $value->message; ?></div>
									<div class="location text-uppercase text-md-right ml-auto"><?php echo $value->tracking_location->city; ?></div>
								</div>							
							</div>
							<?php } ?>	
						</div>
						<?php } ?>
					</div>					
				</div>		
				
				<?php } ?>								
				
				<div class="tracking_details_by_date">
					<?php if(!empty($tracking_destination_details_by_date)){ ?>
						<h4 class="" style=""><?php _e( 'Origin Details', 'woo-advanced-shipment-tracking' ); ?></h4>
					<?php }
					
					$a = 1;					
					foreach($tracking_details_by_date as $date => $date_details){
						if($a > 1)break;						
					?>
						<div class="tracking_group_by_date">
							<div class="date text-uppercase font-weight-bold mb-3"><?php echo date_i18n( get_option( 'date_format' ), strtotime($date) );  ?></div>
							<?php foreach($date_details as $key => $value){ ?>
							<div class="d-flex mb-3">								
								<div class="d-md-flex w-100">							
									<div class="time mr-md-2"><?php echo date_i18n( get_option( 'time_format' ), strtotime($value->datetime) ) ?></div>
									<div class="message font-weight-demi-bold mr-md-3"><?php echo $value->message; ?></div>
									<div class="location text-uppercase text-md-right ml-auto"><?php echo $value->tracking_location->city; ?></div>
								</div>						
							</div>
							<?php } ?>	
						</div>
					<?php $a++; } ?>	
					<div class="old-details" style="">
						<?php 				
						$a = 1;					
						foreach($tracking_details_by_date as $date => $date_details){ 						
							if($a == 1){
								$a++;
								continue;							
							} ?>
						<div class="tracking_group_by_date">
							<div class="date text-uppercase font-weight-bold mb-3"><?php echo date_i18n( get_option( 'date_format' ), strtotime($date) ); //date( 'F j, Y', strtotime($date)); ?></div>
							<?php foreach($date_details as $key => $value){ ?>							
							<div class="d-flex mb-3">					
								<div class="d-md-flex w-100">								
									<div class="time text-gray-300 mr-md-2 text-success"><?php echo date_i18n( get_option( 'time_format' ), strtotime($value->datetime) ) //date( 'g:i a', strtotime($value->datetime)); ?></div>
									<div class="message font-weight-demi-bold mr-md-3"><?php echo $value->message; ?></div>
									<div class="location text-uppercase text-md-right ml-auto"><?php echo $value->tracking_location->city; ?></div>
								</div>							
							</div>
							<?php } ?>	
						</div>
						<?php } ?>
					</div>					
				</div>	
				<a class="view_old_details" href="javaScript:void(0);" style="display: inline;"><?php _e( 'view more', 'woo-advanced-shipment-tracking' ); ?></a>
				<a class="hide_old_details" href="javaScript:void(0);" style="display: none;"><?php _e( 'view less', 		'woo-advanced-shipment-tracking' ); ?></a>	
			</div>						
			
			<div class="tracking_details_mobile">
				<?php if(!empty($tracking_destination_details_by_date)){ ?>	
				<div class="tracking_destination_details_by_date">				
					<h4 class="" style=""><?php _e( 'Destination Details', 'woo-advanced-shipment-tracking' ); ?></h4>
				<?php 
				$a = 1;
				foreach($tracking_destination_details_by_date as $date => $date_details){
					if($a > 1)break;
					foreach($date_details as $key => $value){ ?>
						<div class="d-flex mb-3 tracking_details_mobile_row">								
							<div class="d-md-flex w-100">							
								<div class="time text-gray-300 mr-md-2 text-success"><span class="text-uppercase"><?php echo date( 'F j, Y', strtotime($date)); ?></span> <?php echo date_i18n( get_option( 'time_format' ), strtotime($value->datetime) ) //date( 'g:i a', strtotime($value->datetime)); ?></div>
								<div class="message font-weight-demi-bold mr-md-3"><?php echo $value->message; ?></div>
								<div class="location text-uppercase text-md-right ml-auto text-gray-300"><?php echo $value->tracking_location->city; ?></div>
							</div>						
						</div>	
					<?php }
				$a++;
				}
				?>				
					<div class="old-details" style="">
						<?php 				
						$a = 1;					
						foreach($tracking_destination_details_by_date as $date => $date_details){ 						
							if($a == 1){
								$a++;
								continue;							
							}
							foreach($date_details as $key => $value){
						?>
						<div class="d-flex mb-3 tracking_details_mobile_row">					
							<div class="d-md-flex w-100">								
								<div class="time text-gray-300 mr-md-2 text-success"><span class="text-uppercase"><?php echo date_i18n( get_option( 'date_format' ), strtotime($date) ); //date( 'F j, Y', strtotime($date)); ?></span> <?php echo date_i18n( get_option( 'time_format' ), strtotime($value->datetime) ) //date( 'g:i a', strtotime($value->datetime)); ?></div>
								<div class="message font-weight-demi-bold mr-md-3"><?php echo $value->message; ?></div>
								<div class="location text-uppercase text-md-right ml-auto text-gray-300"><?php echo $value->tracking_location->city; ?></div>
							</div>							
						</div>
						<?php }	} ?>
					</div>					
				</div>
				<?php } ?>
				
				<div class="tracking_details_by_date">
				<?php if(!empty($tracking_destination_details_by_date)){ ?>
						<h4 class="" style=""><?php _e( 'Origin Details', 'woo-advanced-shipment-tracking' ); ?></h4>
					<?php }
				$a = 1;
				foreach($tracking_details_by_date as $date => $date_details){
					if($a > 1)break;
					foreach($date_details as $key => $value){ ?>
						<div class="d-flex mb-3 tracking_details_mobile_row">								
							<div class="d-md-flex w-100">							
								<div class="time text-gray-300 mr-md-2 text-success"><span class="text-uppercase"><?php echo date( 'F j, Y', strtotime($date)); ?></span> <?php echo date_i18n( get_option( 'time_format' ), strtotime($value->datetime) ) //date( 'g:i a', strtotime($value->datetime)); ?></div>
								<div class="message font-weight-demi-bold mr-md-3"><?php echo $value->message; ?></div>
								<div class="location text-uppercase text-md-right ml-auto text-gray-300"><?php echo $value->tracking_location->city; ?></div>
							</div>						
						</div>	
					<?php }
				$a++;
				}
				?>				
					<div class="old-details" style="">
						<?php 				
						$a = 1;					
						foreach($tracking_details_by_date as $date => $date_details){ 						
							if($a == 1){
								$a++;
								continue;							
							}
							foreach($date_details as $key => $value){
						?>
						<div class="d-flex mb-3 tracking_details_mobile_row">					
							<div class="d-md-flex w-100">								
								<div class="time text-gray-300 mr-md-2 text-success"><span class="text-uppercase"><?php echo date_i18n( get_option( 'date_format' ), strtotime($date) ); //date( 'F j, Y', strtotime($date)); ?></span> <?php echo date_i18n( get_option( 'time_format' ), strtotime($value->datetime) ) //date( 'g:i a', strtotime($value->datetime)); ?></div>
								<div class="message font-weight-demi-bold mr-md-3"><?php echo $value->message; ?></div>
								<div class="location text-uppercase text-md-right ml-auto text-gray-300"><?php echo $value->tracking_location->city; ?></div>
							</div>							
						</div>
						<?php }	} ?>
					</div>					
				</div>
				<a class="view_old_details" href="javaScript:void(0);" style="display: inline;"><?php _e( 'view more', 'woo-advanced-shipment-tracking' ); ?></a>
				<a class="hide_old_details" href="javaScript:void(0);" style="display: none;"><?php _e( 'view less', 	'woo-advanced-shipment-tracking' ); ?></a>	
			</div>			
		</div>
	<?php } 
	
	/**
	 * convert string to date
	*/
	public static function convertString ($date) 
    { 
        // convert date and time to seconds 
        $sec = strtotime($date); 
  
        // convert seconds into a specific format 
        $date = date("m/d/Y H:i", $sec); 
  
        // print final date and time 
        return $date; 
    } 
	
	public static function preview_tracking_page(){
		$action = (isset($_REQUEST["action"])?$_REQUEST["action"]:"");
		if($action != 'preview_tracking_page')return;		
		wp_head();
		
		$wc_ast_api_key = get_option('wc_ast_api_key');	
		$primary_color = get_option('wc_ast_select_primary_color');	
		$border_color = get_option('wc_ast_select_border_color');
		$hide_tracking_provider_image = get_option('wc_ast_hide_tracking_provider_image');
		$hide_tracking_events = get_option('wc_ast_hide_tracking_events');
		$tracking_page_layout = get_option('wc_ast_select_tracking_page_layout','t_layout_1');	
		
		$upload_dir   = wp_upload_dir();	
		$ast_directory = $upload_dir['baseurl'] . '/ast-shipping-providers/';
		$ast_base_directory = $upload_dir['basedir'] . '/ast-shipping-providers/';
		?>
		
		<style>	
			html{
				background-color:#fff;
			}
			<?php if($primary_color){ ?>
			.bg-secondary{
				background-color:<?php echo $primary_color; ?>;
			}
			.tracker-progress-bar-with-dots .secondary .dot {
				border-color: <?php echo $primary_color; ?>;
			}
			.text-secondary{
				color: <?php echo $primary_color; ?>;
			}
			.progress-bar.bg-secondary:before{
				background-color: <?php echo $primary_color; ?>;
			}
			.tracking-number{
				color: <?php echo $primary_color; ?>;
			}
			.tracking-detail .tracking-number{
				color: <?php echo $primary_color; ?>;
			}			
			.tracking-detail.tracking-layout-2{
				color: <?php echo $primary_color; ?>;
			}
			<?php }
			if($border_color){ ?>
			.col.tracking-detail{
				border: 1px solid <?php echo $border_color; ?>;
			}
			<?php }	?>
		</style>		
		
		<div class="tracking-detail tracking-layout-1 col" style="<?php if($tracking_page_layout != 't_layout_1'){ echo 'display:none;'; } ?>"> 
			<div class="tracking-header tracking-desktop-header">
				<div class="col-md col-md-6">					
					<span class="tracking-number"><?php _e( 'Order', 'woocommerce' ); ?>: <strong>#4542</strong></span><br>
					<span class="tracking-number">UPS: <strong>6A17149676461</strong></span>					
					<h1 class="shipment_status_heading text-success">Out For Delivery</h1>
					<span class="tracking-number"><?php _e( 'Est. Delivery Date', 'woo-advanced-shipment-tracking' ); ?>: <strong>Monday, Dec 23</strong></span>
				</div>
				<div class="col-md col-md-6 provider-image-div" style="<?php if($hide_tracking_provider_image == 1) { echo 'display:none'; };  ?>">
					<div class="text-right">
						<img src="<?php echo wc_advanced_shipment_tracking()->plugin_dir_url()?>assets/shipment-provider-img/ups.png">	
					</div>
				</div>
			</div>
			<div class="tracking-header tracking-mobile-header">
				<div class="d-flex align-items-center header_top1">
					<div class="header_top_left"><img src="<?php echo wc_advanced_shipment_tracking()->plugin_dir_url()?>assets/shipment-provider-img/ups.png"></div>
					<div class="header_top_right">						
						<span class="tracking-number">UPS: <strong>6A17149676461</strong></span><br>
						<span class="tracking-number"><?php _e( 'Order', 'woocommerce' ); ?>: <strong>#4542</strong></span>
					</div>
				</div>
				<div class="col-md col-md-6 header_top2">														
					<h1 class="shipment_status_heading text-success">Out For Delivery</h1>
					<span class="tracking-number"><?php _e( 'Est. Delivery Date', 'woo-advanced-shipment-tracking' ); ?>: <strong>Monday, Dec 23</strong></span>
				</div>
			</div>
			<div class="status-section desktop-section">
				<div class="tracker-progress-bar tracker-progress-bar-with-dots">
					<div class="progress">
						<div class="progress-bar bg-success bg-success" style="width:67%"></div>
					</div>
					<div style="background-color: transparent;" class="success">
						<span class="dot state-0 pre_transit past-state"></span>						
						
						<span class="dot state-1 in_transit past-state"></span>
						
						<span class="dot state-2 out_for_delivery current-state"></span>
						
						<span class="dot state-3 delivered future-state"></span>
						
					</div>
				</div>
			</div>
			<div class="status-section mobile-section">
				<div class="tracker-progress-bar tracker-progress-bar-with-dots">
					<div class="progress bg-success" style="height: 62%;">
						<div class="progress-bar" style=""></div>
					</div>
					<div style="background-color: transparent;" class="success">
						<div class="dot-div">							
							<span class="dot state-0 pre_transit past-state"></span>
							<span class="state-label past-state">Pre Transit</span>
						</div>
						<div class="dot-div">	
							<span class="dot state-1 in_transit past-state"></span>
							<span class="state-label state-1 past-state">In Transit</span>
						</div>
						<div class="dot-div">
							<span class="dot state-2 out_for_delivery current-state"></span>
							<span class="state-label state-2  past-state">Out for delivery</span>
						</div>
						<div class="dot-div">	
							<span class="dot state-3 delivered future-state"></span>
							<span class="state-label state-3 current-state">Delivered</span>
						</div>
					</div>
				</div>
			</div>				
			
			<div class="tracking-details" style="<?php if($hide_tracking_events == 1){ echo 'display:none'; } ?>">
				<div class="shipment_progress_heading_div">	               				
					<h4 class="tracking-number h4-heading text-uppercase" style="">Tracking Details</h4>					
				</div>
				<div class="tracking_details_desktop">
					<div class="tracking_group_by_date">
						<div class="d-flex mb-3">					
							<div class="d-md-flex w-100">
								<div class="date text-uppercase font-weight-demi-bold">December 23, 2019</div>
								<div class="time text-gray-300 mr-md-2 text-success">11:52am</div>
								<div class="message font-weight-demi-bold mr-md-3">Out for delivery</div>
								<div class="location text-uppercase text-md-right ml-auto text-gray-300">DAWSON CREEK,BC</div>
							</div>						
						</div>
						<div class="d-flex mb-3">					
							<div class="d-md-flex w-100">
								<div class="date text-uppercase font-weight-demi-bold">December 23, 2019</div>
								<div class="time text-gray-300 mr-md-2 text-success">08:55am</div>
								<div class="message font-weight-demi-bold mr-md-3">Notice card left indicating where and when to pickup item</div>
								<div class="location text-uppercase text-md-right ml-auto text-gray-300">DAWSON CREEK,BC</div>
							</div>						
						</div>
					</div>
					<div class="old-details" style="">
						<div class="tracking_group_by_date">
							<div class="d-flex mb-3">					
								<div class="d-md-flex w-100">
									<div class="date text-uppercase font-weight-demi-bold">December 22, 2019</div>
									<div class="time text-gray-300 mr-md-2 text-success">11:52am</div>
									<div class="message font-weight-demi-bold mr-md-3">In Transit</div>
									<div class="location text-uppercase text-md-right ml-auto text-gray-300">DAWSON CREEK,BC</div>
								</div>							
							</div>
							<div class="d-flex mb-3">					
								<div class="d-md-flex w-100">
									<div class="date text-uppercase font-weight-demi-bold">December 22, 2019</div>
									<div class="time text-gray-300 mr-md-2 text-success">08:55am</div>
									<div class="message font-weight-demi-bold mr-md-3">Notice card left indicating where and when to pickup item</div>
									<div class="location text-uppercase text-md-right ml-auto text-gray-300">DAWSON CREEK,BC</div>
								</div>							
							</div>
						</div>
					</div>
				</div>
				<div class="tracking_details_mobile">										
					<div class="d-flex mb-3 tracking_details_mobile_row">								
						<div class="d-md-flex w-100">							
							<div class="time text-gray-300 mr-md-2 text-success"><span class="text-uppercase">December</span> 23, 2019 11:52am</div>
							<div class="message font-weight-demi-bold mr-md-3">Out for delivery</div>
							<div class="location text-uppercase text-md-right ml-auto text-gray-300">DAWSON CREEK,BC</div>
						</div>						
					</div>
					<div class="d-flex mb-3 tracking_details_mobile_row">					
						<div class="d-md-flex w-100">							
							<div class="time text-gray-300 mr-md-2 text-success"><span class="text-uppercase">December</span> 23, 2019 08:55am</div>
							<div class="message font-weight-demi-bold mr-md-3">Notice card left indicating where and when to pickup item</div>
							<div class="location text-uppercase text-md-right ml-auto text-gray-300">DAWSON CREEK,BC</div>
						</div>						
					</div>					
					<div class="old-details" style="">
						<div class="tracking_group_by_date">							
							<div class="d-flex mb-3 tracking_details_mobile_row">					
								<div class="d-md-flex w-100">								
									<div class="time text-gray-300 mr-md-2 text-success"><span class="text-uppercase">December</span> 22, 2019 11:52am</div>
									<div class="message font-weight-demi-bold mr-md-3">In Transit</div>
									<div class="location text-uppercase text-md-right ml-auto text-gray-300">DAWSON CREEK,BC</div>
								</div>							
							</div>
							<div class="d-flex mb-3 tracking_details_mobile_row">					
								<div class="d-md-flex w-100">								
									<div class="time text-gray-300 mr-md-2 text-success"><span class="text-uppercase">December</span> 22, 2019 08:55am</div>
									<div class="message font-weight-demi-bold mr-md-3">Notice card left indicating where and when to pickup item</div>
									<div class="location text-uppercase text-md-right ml-auto text-gray-300">DAWSON CREEK,BC</div>
								</div>							
							</div>
						</div>
					</div>
				</div>
				<a class="view_old_details" href="javaScript:void(0);" style="display: inline;"><?php _e( 'view more', 'woo-advanced-shipment-tracking' ); ?></a>
				<a class="hide_old_details" href="javaScript:void(0);" style="display: none;"><?php _e( 'view less', 		'woo-advanced-shipment-tracking' ); ?></a>
			</div>						
		</div>		
		
		<div class="tracking-detail tracking-layout-2 col" style="<?php if($tracking_page_layout != 't_layout_2'){ echo 'display:none;'; } ?>">
			<div class="tracking-header tracking-desktop-header">
				<div class="col-md col-md-6">					
					<span class="tracking-number"><?php _e( 'Order', 'woocommerce' ); ?>: <strong>#4542</strong></span><br>
					<span class="tracking-number">UPS: <strong>6A17149676461</strong></span>					
					<h1 class="shipment_status_heading text-success">Out For Delivery</h1>
					<span class="tracking-number"><?php _e( 'Est. Delivery Date', 'woo-advanced-shipment-tracking' ); ?>: <strong>Monday, Dec 23</strong></span>
				</div>
				<div class="col-md col-md-6 provider-image-div" style="<?php if($hide_tracking_provider_image == 1) { echo 'display:none'; };  ?>">
					<div class="text-right">
						<img src="<?php echo $ast_directory;?>ups.png">	
					</div>
				</div>
			</div>
			<div class="tracking-header tracking-mobile-header">
				<div class="d-flex align-items-center header_top1">
					<div class="header_top_left"><img src="<?php echo $ast_directory;?>ups.png"></div>
					<div class="header_top_right">						
						<span class="tracking-number">UPS: <strong>6A17149676461</strong></span><br>
						<span class="tracking-number"><?php _e( 'Order', 'woocommerce' ); ?>: <strong>#4542</strong></span>
					</div>
				</div>
				<div class="col-md col-md-6 header_top2">														
					<h1 class="shipment_status_heading text-success">Out For Delivery</h1>
					<span class="tracking-number"><?php _e( 'Est. Delivery Date', 'woo-advanced-shipment-tracking' ); ?>: <strong>Monday, Dec 23</strong></span>
				</div>
			</div>
			
			<div class="tracker-progress-bar tracker-progress-bar-flat">
				<div class="progress">
					<div class="progress-bar bg-success" style="width: 67%;"></div>
				</div>
			</div>																		
			<div class="tracking-details" style="<?php if($hide_tracking_events == 1){ echo 'display:none'; } ?>">
				<div class="shipment_progress_heading_div">	               				
					<h4 class="tracking-number h4-heading text-uppercase" style="">Tracking Details</h4>					
				</div>
				<div class="tracking_details_desktop">
					<div class="tracking_group_by_date">
						<div class="date text-uppercase font-weight-bold mb-3">December 23, 2019</div>
						<div class="d-flex mb-3">								
							<div class="d-md-flex w-100">							
								<div class="time text-gray-300 mr-md-2 text-success">11:52am</div>
								<div class="message font-weight-demi-bold mr-md-3">Out for delivery</div>
								<div class="location text-uppercase text-md-right ml-auto text-gray-300">DAWSON CREEK,BC</div>
							</div>						
						</div>
						<div class="d-flex mb-3">					
							<div class="d-md-flex w-100">							
								<div class="time text-gray-300 mr-md-2 text-success">08:55am</div>
								<div class="message font-weight-demi-bold mr-md-3">Notice card left indicating where and when to pickup item</div>
								<div class="location text-uppercase text-md-right ml-auto text-gray-300">DAWSON CREEK,BC</div>
							</div>						
						</div>
					</div>
					<div class="old-details" style="">
						<div class="tracking_group_by_date">
							<div class="date text-uppercase font-weight-bold mb-3">December 22, 2019</div>
							<div class="d-flex mb-3">					
								<div class="d-md-flex w-100">								
									<div class="time text-gray-300 mr-md-2 text-success">11:52am</div>
									<div class="message font-weight-demi-bold mr-md-3">In Transit</div>
									<div class="location text-uppercase text-md-right ml-auto text-gray-300">DAWSON CREEK,BC</div>
								</div>							
							</div>
							<div class="d-flex mb-3">					
								<div class="d-md-flex w-100">								
									<div class="time text-gray-300 mr-md-2 text-success">08:55am</div>
									<div class="message font-weight-demi-bold mr-md-3">Notice card left indicating where and when to pickup item</div>
									<div class="location text-uppercase text-md-right ml-auto text-gray-300">DAWSON CREEK,BC</div>
								</div>							
							</div>
						</div>
					</div>
				</div>
				<div class="tracking_details_mobile">										
					<div class="d-flex mb-3 tracking_details_mobile_row">								
						<div class="d-md-flex w-100">							
							<div class="time text-gray-300 mr-md-2 text-success"><span class="text-uppercase">December</span> 23, 2019 11:52am</div>
							<div class="message font-weight-demi-bold mr-md-3">Out for delivery</div>
							<div class="location text-uppercase text-md-right ml-auto text-gray-300">DAWSON CREEK,BC</div>
						</div>						
					</div>
					<div class="d-flex mb-3 tracking_details_mobile_row">					
						<div class="d-md-flex w-100">							
							<div class="time text-gray-300 mr-md-2 text-success"><span class="text-uppercase">December</span> 23, 2019 08:55am</div>
							<div class="message font-weight-demi-bold mr-md-3">Notice card left indicating where and when to pickup item</div>
							<div class="location text-uppercase text-md-right ml-auto text-gray-300">DAWSON CREEK,BC</div>
						</div>						
					</div>					
					<div class="old-details" style="">
						<div class="tracking_group_by_date">							
							<div class="d-flex mb-3 tracking_details_mobile_row">					
								<div class="d-md-flex w-100">								
									<div class="time text-gray-300 mr-md-2 text-success"><span class="text-uppercase">December</span> 22, 2019 11:52am</div>
									<div class="message font-weight-demi-bold mr-md-3">In Transit</div>
									<div class="location text-uppercase text-md-right ml-auto text-gray-300">DAWSON CREEK,BC</div>
								</div>							
							</div>
							<div class="d-flex mb-3 tracking_details_mobile_row">					
								<div class="d-md-flex w-100">								
									<div class="time text-gray-300 mr-md-2 text-success"><span class="text-uppercase">December</span> 22, 2019 08:55am</div>
									<div class="message font-weight-demi-bold mr-md-3">Notice card left indicating where and when to pickup item</div>
									<div class="location text-uppercase text-md-right ml-auto text-gray-300">DAWSON CREEK,BC</div>
								</div>							
							</div>
						</div>
					</div>
				</div>
				<a class="view_old_details" href="javaScript:void(0);" style="display: inline;"><?php _e( 'view more', 'woo-advanced-shipment-tracking' ); ?></a>
				<a class="hide_old_details" href="javaScript:void(0);" style="display: none;"><?php _e( 'view less', 		'woo-advanced-shipment-tracking' ); ?></a>
			</div>		
		</div>	
		<?php
		$remove_trackship_branding =  get_option('wc_ast_remove_trackship_branding');
		?>		
		<div class="trackship_branding"  style="<?php if($remove_trackship_branding == 1){ echo 'display:none'; }?>">
			<p>Shipment Tracking info by <a href="https://trackship.info" title="TrackShip" target="blank">TrackShip</a></p>
		</div>
		<?php 				
		//wp_footer();				
		exit;
	}
}