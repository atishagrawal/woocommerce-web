<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_Local_Pickup_admin {

	/**
	 * Get the class instance
	 *
	 * @since  1.0
	 * @return WC_Local_pickup_Admin
	*/
	public static function get_instance() {

		if ( null === self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Instance of this class.
	 *
	 * @var object Class Instance
	*/
	private static $instance;
	
	/**
	 * Initialize the main plugin function
	 * 
	 * @since  1.0
	*/
	public function __construct() {
		$this->init();		
	}
	
	/*
	 * init function
	 *
	 * @since  1.0
	*/
	public function init(){
		
		//adding hooks
		
		add_action('admin_menu', array( $this, 'register_woocommerce_menu' ), 99 );
		
		//ajax save admin api settings
		add_action( 'wp_ajax_wclp_setting_form_update', array( $this, 'wclp_setting_form_update_callback') );		
		add_action( 'wp_ajax_wclp_location_form_update', array( $this, 'wclp_location_form_update_callback') );
		
		// Register new status
		add_action( 'init', array( $this, 'register_pickup_order_status') );
		
		// Add to list of WC Order statuses
		add_filter( 'wc_order_statuses', array( $this, 'add_pickup_to_order_statuses') );
		add_filter( 'bulk_actions-edit-shop_order', array( $this, 'add_bulk_actions_change_order_status'), 50, 1 );				
		
		// Add to custom email for WC Order statuses
		add_filter( 'woocommerce_email_before_order_table', array( $this, 'add_location_address_detail_emails' ), 2, 4 );
		
		// Add Addition content for processing email
		add_filter( 'woocommerce_email_before_order_table', array( $this, 'add_addional_content_on_processing_email' ), 1, 4 );
		add_filter( 'woocommerce_admin_order_actions', array( $this, 'add_local_pickup_order_status_actions_button'), 100, 2 );
		
		add_action( 'woocommerce_view_order', array( $this, 'add_location_address_detail_order' ), 10, 2 );
		add_action( 'woocommerce_order_details_after_order_table', array( $this, 'add_location_address_detail_order' ), 10, 2 );
		add_action( 'admin_footer', array( $this, 'footer_function'),1 );
		add_action( 'init', array( $this, 'plugin_update_check'));	
		
		add_action( 'wp_ajax_wclp_update_state_dropdown', array( $this, 'wclp_update_state_dropdown_fun') );
		add_action( 'wp_ajax_wclp_update_work_hours_list', array( $this, 'wclp_update_work_hours_list_fun') );
		
		add_filter( 'woocommerce_valid_order_statuses_for_order_again', array( $this, 'add_reorder_button_pickup'), 50, 1 );
		
	}
	
	/*
	* Admin Menu add function
	* WC sub menu 
	*/
	public function register_woocommerce_menu() {
		add_submenu_page( 'woocommerce', 'Local Pickup', 'Local Pickup', 'manage_options', 'local_pickup', array( $this, 'woocommerce_local_pickup_page_callback' ) ); //woocommerce_local_pickup_page_callback
	}
	
	/*
	* callback for Advanced Local Pickup page
	*/
	public function woocommerce_local_pickup_page_callback(){		
		$tab = isset( $_GET['tab'] ) ? $_GET['tab'] : '';
		?>
        <div class="main-title">
			<img class="wclp-plugin-logo" src="<?php echo wc_local_pickup()->plugin_dir_url()?>assets/images/alp-logo.png">
		</div>
		<div class="woocommerce wclp_admin_layout">
            <div class="wclp_admin_content">
                <input id="tab1" type="radio" name="tabs" class="wclp_tab_input" data-tab="settings" checked>
            	<label for="tab1" class="wclp_tab_label first_label"><?php _e('Settings', 'woocommerce'); ?></label>
				<input id="tab2" type="radio" name="tabs" class="wclp_tab_input" data-tab="locations" <?php if(isset($_GET['tab']) && $_GET['tab'] == 'locations'){ echo 'checked'; } ?>>
            	<label for="tab2" class="wclp_tab_label"><?php _e('Pickup Locations', 'advanced-local-pickup-for-woocommerce'); ?></label>
				
				<div class="wclp_nav_doc_section">					
						<a target="blank" href="https://www.zorem.com/docs/advanced-local-pickup-for-woocommerce/"><?php _e('Documentation', 'advanced-local-pickup-for-woocommerce'); ?></a>
                </div>
                <?php require_once( 'views/wclp_setting_tab.php' ); ?>
				<?php require_once( 'views/wclp_locations_tab.php' ); ?>
            </div>
        </div>
        <div id="wclp-toast-example" aria-live="assertive" aria-atomic="true" aria-relevant="text" class="mdl-snackbar mdl-js-snackbar">
            <div class="mdl-snackbar__text"></div>
            <button type="button" class="mdl-snackbar__action"></button>
        </div>
        <?php
	}
	
	
	/*
	* settings form save for Setting tab
	*/
	function wclp_setting_form_update_callback(){			
		
		if ( ! empty( $_POST ) && check_admin_referer( 'wclp_setting_form_action', 'wclp_setting_form_nonce_field' ) ) {
			
			update_option( 'wclp_ready_pickup_status_label_color', sanitize_text_field( $_POST[ 'wclp_ready_pickup_status_label_color' ] ));
			update_option( 'wclp_ready_pickup_status_label_font_color', sanitize_text_field( $_POST[ 'wclp_ready_pickup_status_label_font_color' ] ));
			update_option( 'wclp_pickup_status_label_color', sanitize_text_field( $_POST[ 'wclp_pickup_status_label_color' ] ));
			update_option( 'wclp_pickup_status_label_font_color', sanitize_text_field( $_POST['wclp_pickup_status_label_font_color'] ));			
			if(isset($_POST['wclp_processing_additional_content'])){
				update_option( 'wclp_processing_additional_content',  sanitize_text_field( $_POST['wclp_processing_additional_content'] ) );
			}
						
			$wclp_show_pickup_instruction_opt = array(
				'display_in_processing_email' => sanitize_text_field($_POST['wclp_show_pickup_instruction']['display_in_processing_email']),
				'display_in_order_received_page' => sanitize_text_field($_POST['wclp_show_pickup_instruction']['display_in_order_received_page']),
				'display_in_order_details_page' => sanitize_text_field($_POST['wclp_show_pickup_instruction']['display_in_order_details_page']),
			);			
			update_option( 'wclp_show_pickup_instruction', $wclp_show_pickup_instruction_opt);
			
			$wclp_enable_pickup_email = get_option('woocommerce_customer_pickup_order_settings');									
			
			if($_POST['wclp_enable_pickup_email'] == 1){
				update_option( 'customizer_pickup_order_settings_enabled', sanitize_text_field( $_POST['wclp_enable_pickup_email'] ));
				$enabled = 'yes';
			} else{
				update_option( 'customizer_pickup_order_settings_enabled', sanitize_text_field( '' ));	
				$enabled = 'no';
			}
			
			$opt = array(
				'enabled' => $enabled,
				'subject' => $wclp_enable_pickup_email['subject'],
				'heading' => $wclp_enable_pickup_email['heading'],
				'additional_content' => $wclp_enable_pickup_email['additional_content'],
				'recipient' => $wclp_enable_pickup_email['recipient'],
				'email_type' => $wclp_enable_pickup_email['email_type'],
			);
			update_option( 'woocommerce_customer_pickup_order_settings', wc_clean( $opt ) );
			
			$wclp_enable_ready_pickup_email = get_option('woocommerce_customer_ready_pickup_order_settings');									
			if($_POST['wclp_enable_ready_pickup_email'] == 1){
				update_option( 'woocommerce_customer_ready_pickup_order_enabled', sanitize_text_field( $_POST['wclp_enable_ready_pickup_email'] ));
				$enabled = 'yes';
			} else{
				update_option( 'woocommerce_customer_ready_pickup_order_enabled', sanitize_text_field( '' ));	
				$enabled = 'no';
			}
			
			$opt = array(
				'enabled' => $enabled,
				'subject' => $wclp_enable_ready_pickup_email['subject'],
				'heading' => $wclp_enable_ready_pickup_email['heading'],
				'additional_content' => $wclp_enable_ready_pickup_email['additional_content'],
				'recipient' => $wclp_enable_ready_pickup_email['recipient'],
				'email_type' => $wclp_enable_ready_pickup_email['email_type'],
			);
			update_option( 'woocommerce_customer_ready_pickup_order_settings', wc_clean( $opt ) );						
			echo json_encode( array('success' => 'true') );die();
	
		}
	}
	
	/*
	* settings form save for Setting tab
	*/
	function wclp_location_form_update_callback(){			
		
		if ( ! empty( $_POST ) && check_admin_referer( 'wclp_location_form_action', 'wclp_location_form_nonce_field' ) ) {				
			update_option( 'wclp_store_name', sanitize_text_field( $_POST['wclp_store_name'] ));
			update_option( 'wclp_store_address', sanitize_text_field( $_POST['wclp_store_address'] ));
			update_option( 'wclp_store_address_2', sanitize_text_field( $_POST['wclp_store_address_2'] ));
			update_option( 'wclp_store_city', sanitize_text_field( $_POST['wclp_store_city'] ));
			update_option( 'wclp_default_single_country', sanitize_text_field( $_POST['wclp_default_single_country'] ));
			update_option( 'wclp_default_single_state', sanitize_text_field( $_POST['wclp_default_single_state'] ));
			update_option( 'wclp_store_postcode', sanitize_text_field( $_POST['wclp_store_postcode'] ));
			update_option( 'wclp_default_time_format', sanitize_text_field( $_POST['wclp_default_time_format'] ));
			update_option( 'wclp_store_days', wc_clean($_POST['wclp_store_days']) );			
			update_option( 'wclp_store_instruction', sanitize_text_field( $_POST['wclp_store_instruction'] ));			
			echo json_encode( array('success' => 'true') );die();
		}
	}
	
	// Register new status
	function register_pickup_order_status() {
		register_post_status( 'wc-ready-pickup', array(
			'label'                     => __( 'Ready for Pickup', 'advanced-local-pickup-for-woocommerce' ),
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Ready for Pickup (%s)', 'Ready for Pickup (%s)' )
		) );
		
		register_post_status( 'wc-pickup', array(
			'label'                     => __( 'Picked up', 'advanced-local-pickup-for-woocommerce' ),
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Picked up (%s)', 'Picked up (%s)' )
		) );
	}
	
	// Add to list of WC Order statuses
	function add_pickup_to_order_statuses( $order_statuses ) {
	 
		$new_order_statuses = array();
	 
		// add new order status after processing
		foreach ( $order_statuses as $key => $status ) {
	 
			$new_order_statuses[ $key ] = $status;
	 
			if ( 'wc-processing' === $key ) {
				$new_order_statuses['wc-ready-pickup'] = __( 'Ready for Pickup', 'advanced-local-pickup-for-woocommerce' );
			}
			if ( 'wc-processing' === $key ) {
				$new_order_statuses['wc-pickup'] = __( 'Picked up', 'advanced-local-pickup-for-woocommerce' );
			}
		}
	 
		return $new_order_statuses;
	}
	
	// Add bulk action change status to custom order status
	function add_bulk_actions_change_order_status($bulk_actions){
		$bulk_actions['mark_ready-pickup'] = __( 'Change status to Ready for pickup', 'advanced-local-pickup-for-woocommerce' );
		$bulk_actions['mark_pickup'] = __( 'Change status to Picked up', 'advanced-local-pickup-for-woocommerce' );
		return $bulk_actions;		
	}				
	
	function add_location_address_detail_order($order_id){		
		
		$wclp_show_pickup_instruction = get_option('wclp_show_pickup_instruction');
		////IF display location details not enabel then @return;		
		if ( is_checkout() && !empty( is_wc_endpoint_url('order-received') ) ) {			
			if(!isset($wclp_show_pickup_instruction['display_in_order_received_page'])) return;
			if( $wclp_show_pickup_instruction['display_in_order_received_page'] != '1' ) return;
		} else{
			if(!isset($wclp_show_pickup_instruction['display_in_order_details_page'])) return;
			if( $wclp_show_pickup_instruction['display_in_order_details_page'] != '1' ) return; 
		}			
		
		$order = wc_get_order($order_id);
		
		// Iterating through order shipping items
		foreach( $order->get_items( 'shipping' ) as $item_id => $shipping_item_obj ){			
			$shipping_method = $shipping_item_obj->get_method_id();						
		}
		
		//IF  dshipping method is not local pickup then @return;
		if( !isset($shipping_method ) ) return;
		if( isset($shipping_method ) && $shipping_method != 'local_pickup' ) return;
		
		$woocommerce_default_country = get_option('woocommerce_default_country');	
		$split_country = explode( ":", $woocommerce_default_country );
		
		if(isset($split_country[0])){
			$woocommerce_country = $split_country[0];	
		} else{
			$woocommerce_country = '';
		}
		
		if(isset($split_country[1])){
			$woocommerce_state = $split_country[1];
		} else{
			$woocommerce_state   = '';
		}		
		
		$store_country = get_option( 'wclp_default_single_country', $woocommerce_country );
		$store_state = get_option( 'wclp_default_single_state', $woocommerce_state );				
				
		$w_day = get_option( 'wclp_store_days' );
		
		$wclp_default_time_format = get_option('wclp_default_time_format','24');
		if($wclp_default_time_format == '12'){
			foreach($w_day as $key=>$val){	
				$val['wclp_store_hour'] = date('h:i a', strtotime($val['wclp_store_hour']));
				$val['wclp_store_hour_end'] = date('h:i a', strtotime($val['wclp_store_hour_end']));
				$w_day[$key] = $val;				
			}	
		}			
		
		require_once( 'views/wclp_location_details_order_details.php' );		
	}	
	
	public function add_location_address_detail_emails($order, $sent_to_admin, $plain_text, $email) {		
		//IF display location details not enabel then @return;
		$wclp_show_pickup_instruction = get_option('wclp_show_pickup_instruction');
		
		if(!isset($wclp_show_pickup_instruction['display_in_processing_email']) && $email->id == 'customer_processing_order') return;
		if( $wclp_show_pickup_instruction['display_in_processing_email'] != '1'  && $email->id == 'customer_processing_order') return; 		
		
		$order_id = $order->get_data()['id'];
		$order = wc_get_order($order_id);		
		
		// Iterating through order shipping items
		foreach( $order->get_items( 'shipping' ) as $item_id => $shipping_item_obj ){			
			$shipping_method = $shipping_item_obj->get_method_id();						
		}
				
		//IF  dshipping method is not local pickup then @return;
		if( !isset($shipping_method ) ) return;
		if( isset($shipping_method ) && $shipping_method != 'local_pickup' ) return;
		
		$woocommerce_default_country = get_option('woocommerce_default_country');	
		$split_country = explode( ":", $woocommerce_default_country );
		
		if(isset($split_country[0])){
			$woocommerce_country = $split_country[0];	
		} else{
			$woocommerce_country = '';
		}
		
		if(isset($split_country[1])){
			$woocommerce_state = $split_country[1];
		} else{
			$woocommerce_state   = '';
		}		
		
		$store_country = get_option( 'wclp_default_single_country', $woocommerce_country );
		$store_state = get_option( 'wclp_default_single_state', $woocommerce_state );	
				
		$w_day = get_option( 'wclp_store_days' );
		
		$wclp_default_time_format = get_option('wclp_default_time_format','24');
		if($wclp_default_time_format == '12'){
			foreach($w_day as $key=>$val){	
				$val['wclp_store_hour'] = date('h:i a', strtotime($val['wclp_store_hour']));
				$val['wclp_store_hour_end'] = date('h:i a', strtotime($val['wclp_store_hour_end']));
				$w_day[$key] = $val;				
			}	
		}
		
		if ( $email->id == 'customer_ready_pickup_order' || $email->id == 'customer_processing_order' ) { 
			require_once( 'views/wclp_location_details_email.php' );
	   }
		
	}
	
	function add_addional_content_on_processing_email($order, $sent_to_admin, $plain_text, $email){
		if( $email->id != 'customer_processing_order' ) return;
		
		// Iterating through order shipping items
		foreach( $order->get_items( 'shipping' ) as $item_id => $shipping_item_obj ){			
			$shipping_method = $shipping_item_obj->get_method_id();						
		}
				
		//IF  dshipping method is not local pickup then @return;
		if( !isset($shipping_method ) ) return;
		if( isset($shipping_method ) && $shipping_method != 'local_pickup' ) return;
		
		$settings = $this->wclp_general_setting_fields_func();		
		$addional_content = get_option('wclp_processing_additional_content',$settings['wclp_processing_additional_content']['default']);
		echo '<p>'._e($addional_content, 'advanced-local-pickup-for-woocommerce').'</p>';
	}
	
	/**
	 *
	 * Get times as option-list.
	 *
	 * @return string List of times
	 */
	function get_times( $default = '19:00', $interval = '+30 minutes' ) {

		$output[] = array();
		unset($output[0]);
		$current = strtotime( '00:00' );
		$end = strtotime( '23:59' );

		while( $current <= $end ) {
			$time = date( 'H:i', $current );
			$sel = ( $time == $default ) ? ' selected' : '';
		
			$output[date( 'h:i A', $current )] .= date( 'h:i A', $current );
			$current = strtotime( $interval, $current );
		}

		return $output;
	}
	
	function wclp_location_setting_fields_func() {
		
		$send_time_array = array();
		
		for ( $hour = 0; $hour < 24; $hour++ ) {
			for ( $min = 0; $min < 60; $min = $min + 30 ) {
				$this_time = date( 'H:ia', strtotime( "$hour:$min" ) );
				$send_time_array[ $this_time ] = $this_time;
			}	
		}
		
		global $woocommerce;
		$countries_obj   = new WC_Countries();				
		$countries   = $countries_obj->__get('countries');		
		$default_country = $countries_obj->get_base_country();
		$wclp_default_single_country = get_option('wclp_default_single_country','');
		$default_county_states = $countries_obj->get_states( $wclp_default_single_country );		
		
		$days = array(
			'monday' => __( 'Monday', 'advanced-local-pickup-for-woocommerce'),
			'tuesday' => __( 'Tuesday', 'advanced-local-pickup-for-woocommerce' ),
			'wednesday' => __( 'Wednesday', 'advanced-local-pickup-for-woocommerce' ),
			'thursday' => __( 'Thursday', 'advanced-local-pickup-for-woocommerce' ),
			'friday' => __( 'Friday', 'advanced-local-pickup-for-woocommerce' ),
			'saturday' => __( 'Saturday', 'advanced-local-pickup-for-woocommerce' ),
			'sunday' => __( 'Sunday', 'advanced-local-pickup-for-woocommerce' ),
		);
		
		$time_format = array(
			'12' => __( '12 hour','advanced-local-pickup-for-woocommerce' ),
			'24' => __( '24 hour','advanced-local-pickup-for-woocommerce' ),			
		);
		
		$settings = array(

			'wclp_store_name' => array(
				'title'    => __( 'Location Name', 'woocommerce' ),
				'tooltip'     => __( 'The street address for your business location.', 'woocommerce' ),
				'id'       => 'wclp_store_name',
				'default'  => '',
				'show'	   => true,
				'type'     => 'text',
				'desc_tip' => true,
			),
			
			'wclp_store_address' => array(
				'title'    => __( 'Address line 1', 'woocommerce' ),
				'tooltip'     => __( 'The street address for your business location.', 'woocommerce' ),
				'id'       => 'wclp_store_address',
				'default'  => 'woocommerce_store_address',
				'show'	   => true,
				'type'     => 'text',
				'desc_tip' => true,
			),
			'wclp_store_address_2' => array(
				'title'    => __( 'Address line 2', 'woocommerce' ),
				'tooltip'     => __( 'An additional, optional address line for your business location.', 'woocommerce' ),
				'id'       => 'wclp_store_address_2',
				'default'  => 'woocommerce_store_address_2',
				'show'	   => true,
				'type'     => 'text',
				'desc_tip' => true,
			),
			'wclp_store_city' => array(
				'title'    => __( 'City', 'woocommerce' ),
				'tooltip'     => __( 'The city in which your business is located.', 'woocommerce' ),
				'id'       => 'wclp_store_city',
				'default'  => 'woocommerce_store_city',
				'show'	   => true,
				'type'     => 'text',
				'desc_tip' => true,
			),			
			'wclp_default_single_country' => array(
				'title'    => __( 'Country', 'woocommerce' ),
				'tooltip'     => __( 'The country, if any, in which your business is located.', 'woocommerce' ),
				'id'       => 'wclp_default_single_country',
				'default'  => '',
				'show'	   => true,
				'type'     => 'dropdown',
				'options'   => $countries,
			),
			'wclp_default_single_state' => array(
				'title'    => __( 'State', 'woocommerce' ),
				'tooltip'     => __( 'The state, if any, in which your business is located.', 'woocommerce' ),
				'id'       => 'wclp_default_single_state',
				'default'  => '',
				'show'	   => true,
				'type'     => 'dropdown',
				'options'   => $default_county_states,
			),
			'wclp_store_postcode' => array(
				'title'    => __( 'Postcode / ZIP', 'woocommerce' ),
				'tooltip'     => __( 'The postal code, if any, in which your business is located.', 'woocommerce' ),
				'id'       => 'wclp_store_postcode',
				'css'      => 'min-width:50px;',
				'default'  => 'woocommerce_store_postcode',
				'show'	   => true,
				'type'     => 'text',
				'desc_tip' => true,
			),	
			'wclp_default_time_format' => array(
				'title'    => __( 'Time Format', 'woocommerce' ),
				'tooltip'     => __( 'Time format which you want to use in work hours', 'woocommerce' ),
				'id'       => 'wclp_default_time_format',
				'default'  => '',
				'show'	   => true,
				'type'     => 'dropdown',
				'options'   => $time_format,
			),			
			'wclp_store_days' => array(
				'type'		=> 'pickup_day_time',
				'tooltip'     => __( 'the select for working days of your store.','advanced-local-pickup-for-woocommerce'),
				'title'		=> __( 'Work Hours', 'advanced-local-pickup-for-woocommerce' ),
				'show'		=> true,
				'options'   => $days,
				'id'		=> 'wclp_store_days'
			),
			'wclp_store_instruction' => array(
				'title'    => __( 'Special Instruction', 'advanced-local-pickup-for-woocommerce' ),
				'tooltip'     => __( 'The special instruction for your store.', 'woocommerce' ),
				'id'       => 'wclp_store_instruction',
				'css'      => 'min-width:50px;',
				'default'  => '',
				'show'	   => true,
				'type'     => 'textarea',
				'desc_tip' => true,
			),			
		);
		return $settings;		
	}
	
	public function wclp_update_state_dropdown_fun(){		
		$country = wc_clean($_POST['country']);
		$countries_obj   = new WC_Countries();
		$default_county_states = $countries_obj->get_states( $country );
		if(empty($default_county_states)){
			echo json_encode( array('state' => 'empty') );die();
		} else{
			ob_start();
			?>
			<option value="<?php echo $key?>"><?php _e('Select', 'woocommerce'); ?></option>
			<?php 
			foreach((array)$default_county_states as $key => $val ){?>																
				<option value="<?php echo $key?>"><?php echo $val?></option>
            <?php }
			$html = ob_get_clean();			
			echo json_encode( array('state' => $html) );die();
		}	
		echo json_encode( array('state' => 'empty') );die();		
	}
	
	public function wclp_update_work_hours_list_fun(){
		$wclp_default_time_format = wc_clean($_POST['hour_format']);
		$settings = $this->wclp_location_setting_fields_func();		
		ob_start();
		?>
		<div class="pickup_hours_div">
		<?php
		foreach((array)$settings['wclp_store_days']['options'] as $key => $val ){									
		
		$multi_checkbox_data = get_option('wclp_store_days');		
		
		if(isset($multi_checkbox_data[$key]['checked']) && $multi_checkbox_data[$key]['checked'] == 1){
			$checked="checked";
		} else{
			$checked="";
		}
		
		$send_time_array = array();										
		for ( $hour = 0; $hour < 24; $hour++ ) {
			for ( $min = 0; $min < 60; $min = $min + 30 ) {
				$this_time = date( 'H:i', strtotime( "$hour:$min" ) );
				$send_time_array[ $this_time ] = $this_time;
			}	
		}
		
		?>
		<div class="wplp_pickup_duration">
			<label class="" for="<?php echo $key?>">
				<input type="checkbox" id="<?php echo $key?>" name="wclp_store_days[<?php echo $key?>][checked]" class="pickup_days_checkbox"  <?php echo $checked; ?> value="1"/>
				<span class="pickup_days_lable"><?php _e($val, 'advanced-local-pickup-for-woocommerce'); ?></span>	
				<fieldset class="wclp_pickup_time_fieldset">
					<select class="select wclp_pickup_time_select" name="wclp_store_days[<?php echo $key?>][wclp_store_hour]"> 
						<option value="" ><?php _e('Select', 'woocommerce'); ?></option>
						<?php foreach((array)$send_time_array as $key1 => $val1 ){
							if($wclp_default_time_format == '12'){
								$val1 = date('h:i a', strtotime($val1));
							}
						?>
						<option value="<?php echo $key1?>" <?php if(isset($multi_checkbox_data[$key]['wclp_store_hour']) && $multi_checkbox_data[$key]['wclp_store_hour'] == $key1){ echo 'selected'; }?>><?php echo $val1?></option>
						<?php } ?>
					</select>
					<span> - </span>
					<select class="select wclp_pickup_time_select" name="wclp_store_days[<?php echo $key?>][wclp_store_hour_end]">    <option value="" ><?php _e('Select', 'woocommerce'); ?></option>
						<?php foreach((array)$send_time_array as $key2 => $val2 ){
							if($wclp_default_time_format == '12'){
								$val2 = date('h:i a', strtotime($val2));
							}
							?>			
							<option value="<?php echo $key2?>" <?php if(isset($multi_checkbox_data[$key]['wclp_store_hour_end']) && $multi_checkbox_data[$key]['wclp_store_hour_end'] == $key2){ echo 'selected'; }?>><?php echo $val2?></option>
						<?php } ?>
					</select>
				</fieldset>	
			</label>																		
		</div>												
		<?php }	?>
		</div>
		<?php
		$html = ob_get_clean();	
		//echo '<pre>';print_r($html);echo '</pre>';exit;		
		echo json_encode( array('pickup_hours_div' => $html) );die();
	}
	
	function wclp_general_setting_fields_func() {		
		$show_pickup_instraction_option = array( 
			"display_in_processing_email" => __( 'Processing order email', 'advanced-local-pickup-for-woocommerce' ),
			"display_in_order_received_page" => __( 'Order received page', 'advanced-local-pickup-for-woocommerce' ),
			"display_in_order_details_page" => __( 'Customer account > order history > order details page', 'advanced-local-pickup-for-woocommerce' ),			
		);
		$settings = array(						
			'wclp_show_pickup_instruction' => array(
				'title'    => __( 'Display pickup instruction on', 'advanced-local-pickup-for-woocommerce' ),				
				'id'       => 'wclp_show_pickup_instruction',
				'css'      => 'min-width:50px;',
				'default'  => '',
				'show'	   => true,
				'type'     => 'multiple_checkbox',
				'options'  => $show_pickup_instraction_option,
				'class'	   => '',
				'desc_tip' => true,
			),
			'wclp_processing_additional_content' => array(
				'title'    => __( 'Additional content on processing email in case of local pickup orders', 'advanced-local-pickup-for-woocommerce' ),
				'tooltip'  => __( 'Additional content on processing email in case of local pickup orders', 'advanced-local-pickup-for-woocommerce' ),
				'id'       => 'wclp_processing_additional_content',
				'css'      => 'min-width:50px;',
				'default'  => __( "You will receive an email when your order is ready for pickup.", 'advanced-local-pickup-for-woocommerce' ),
				'show'	   => true,
				'type'     => 'textarea',
				'class'	   => '',
				'desc_tip' => true,
			),
		);
		return $settings;
		
	}
	
	/*
	* get html of fields
	*/
	public function get_html( $arrays ){
		
		$checked = '';
		?>
		<table class="form-table">
			<tbody>
            	<?php foreach( (array)$arrays as $id => $array ){
					if($array['show']){	
					?>
                	<?php if($array['type'] == 'title'){ ?>
                		<tr valign="top titlerow">
                        	<th colspan="2"><h3 style="margin:0;"><?php echo $array['title']?></h3>
							<p class="description"><?php echo (isset($array['desc']))? $array['desc']: ''?></p>
							</th>
                        </tr>    	
                    <?php continue;} ?>
				<tr valign="top" class="<?php //echo $array['class'];?>">
					<?php if($array['type'] != 'desc'){ ?>										
					<th>
						<label for=""><?php echo $array['title']?><?php if(isset($array['title_link'])){ echo $array['title_link']; } ?>
							<?php if( isset($array['tooltip']) ){?>
                            	<span class="woocommerce-help-tip tipTip" title="<?php echo $array['tooltip']?>"></span>
                            <?php } ?>
                        </label>
					</th>
					<?php } ?>
					<td class="forminp"  <?php if($array['type'] == 'desc'){ ?> colspan=2 <?php } ?>>
                    	<?php if( $array['type'] == 'checkbox' ){								
																						
								if(get_option($array['id'])){
									$checked = 'checked';
								} else{
									$checked = '';
								} 
							
							if(isset($array['disabled']) && $array['disabled'] == true){
								$disabled = 'disabled';
								$checked = '';
							} else{
								$disabled = '';
							}							
							?>
						<?php if($array['class'] == 'toggle'){?>
						<span class="mdl-list__item-secondary-action">
							<label class="mdl-switch mdl-js-switch mdl-js-ripple-effect" for="<?php echo $id?>">
								<input type="hidden" name="<?php echo $id?>" value="0"/>
								<input type="checkbox" id="<?php echo $id?>" name="<?php echo $id?>" class="mdl-switch__input" <?php echo $checked ?> value="1" <?php echo $disabled; ?>/>
							</label><p class="description"><?php echo (isset($array['desc']))? $array['desc']: ''?></p>
						</span>
						<?php } else { ?>
							<span class="checkbox">
								<label class="checkbx-label" for="<?php echo $id?>">
									<input type="hidden" name="<?php echo $id?>" value="0"/>
									<input type="checkbox" id="<?php echo $id?>" name="<?php echo $id?>" class="checkbox-input" <?php echo $checked ?> value="1" <?php echo $disabled; ?>/>
								</label><p class="description"><?php echo (isset($array['desc']))? $array['desc']: ''?></p>
							</span>
						<?php } ?>
						<?php } elseif( $array['type'] == 'textarea' ){ ?>
									<fieldset>
										<textarea rows="3" cols="20" class="input-text regular-input" type="textarea" name="<?php echo $id?>" id="<?php echo $id?>" style="" placeholder="<?php if(!empty($array['placeholder'])){echo $array['placeholder'];} ?>"><?php echo get_option($array['id'],$array['default']); ?></textarea>
									</fieldset>
                        <?php }  elseif( isset( $array['type'] ) && $array['type'] == 'dropdown' ){?>
                        	<?php
								if( isset($array['multiple']) ){
									$multiple = 'multiple';
									$field_id = $array['multiple'];
								} else {
									$multiple = '';
									$field_id = $id;
								}
							?>
                        	<fieldset>
								<select class="select select2" id="<?php echo $field_id?>" name="<?php echo $id?>" <?php echo $multiple;?>> 
									<option value=""><?php _e('Select', 'woocommerce'); ?></option>
									<?php 
									if(!empty($array['options'])){
										foreach((array)$array['options'] as $key => $val ){?>											
											<?php
												$selected = '';
												if( isset($array['multiple']) ){
													if (in_array($key, (array)$this->data->$field_id ))$selected = 'selected';
												} else {
													if( get_option($array['id']) == (string)$key )$selected = 'selected';
												}
											
											?>
											<option value="<?php echo $key?>" <?php echo $selected?> ><?php echo $val?></option>
                                    <?php } }  ?><p class="description"><?php echo (isset($array['desc']))? $array['desc']: ''?></p>
								</select>
							</fieldset>
                        <?php }
						
						elseif( $array['type'] == 'multiple_checkbox' ) { ?>
						
							<?php
								$op = 1;	
								foreach((array)$array['options'] as $key => $val ){									
										$multi_checkbox_data = get_option($id);
										if(isset($multi_checkbox_data[$key]) && $multi_checkbox_data[$key] == 1){
											$checked="checked";
										} else{
											$checked="";
										}?>
								<span class="wplp_multiple_checkbox">
									<label class="" for="<?php echo $key?>">
										<input type="hidden" name="<?php echo $id?>[<?php echo $key?>]" value="0"/>
										<input type="checkbox" id="<?php echo $key?>" name="<?php echo $id?>[<?php echo $key?>]" class=""  <?php echo $checked; ?> value="1"/>
										<span class="multiple_label"><?php echo $val; ?></span>	
										</br>
									</label>																		
								</span>												
						<?php } 
						
						}
						elseif( $array['type'] == 'pickup_day_time' ) { ?>
							<div class="pickup_hours_div">
							<?php
								
								foreach((array)$array['options'] as $key => $val ){									
										
										$multi_checkbox_data = get_option($id);
										$wclp_default_time_format = get_option('wclp_default_time_format','24');
										
										if(isset($multi_checkbox_data[$key]['checked']) && $multi_checkbox_data[$key]['checked'] == 1){
											$checked="checked";
										} else{
											$checked="";
										}
										
										$send_time_array = array();										
										for ( $hour = 0; $hour < 24; $hour++ ) {
											for ( $min = 0; $min < 60; $min = $min + 30 ) {
												$this_time = date( 'H:i', strtotime( "$hour:$min" ) );
												$send_time_array[ $this_time ] = $this_time;
											}	
										}
										?>
								<div class="wplp_pickup_duration">
									<label class="" for="<?php echo $key?>">
										<input type="checkbox" id="<?php echo $key?>" name="<?php echo $id?>[<?php echo $key?>][checked]" class="pickup_days_checkbox"  <?php echo $checked; ?> value="1"/>
										<span class="pickup_days_lable"><?php _e($val, 'advanced-local-pickup-for-woocommerce'); ?></span>	
										<fieldset class="wclp_pickup_time_fieldset">
											<select class="select wclp_pickup_time_select" name="<?php echo $id?>[<?php echo $key?>][wclp_store_hour]"> 
												<option value="" ><?php _e('Select', 'woocommerce'); ?></option>
												<?php foreach((array)$send_time_array as $key1 => $val1 ){
													if($wclp_default_time_format == '12'){
														$val1 = date('h:i a', strtotime($val1));
													}
												?>
												<option value="<?php echo $key1?>" <?php if(isset($multi_checkbox_data[$key]['wclp_store_hour']) && $multi_checkbox_data[$key]['wclp_store_hour'] == $key1){ echo 'selected'; }?>><?php echo $val1?></option>
												<?php } ?>
											</select>
											<span> - </span>
											<select class="select wclp_pickup_time_select" name="<?php echo $id?>[<?php echo $key?>][wclp_store_hour_end]">    <option value="" ><?php _e('Select', 'woocommerce'); ?></option>
												<?php foreach((array)$send_time_array as $key2 => $val2 ){
													if($wclp_default_time_format == '12'){
														$val2 = date('h:i a', strtotime($val2));
													}
													?>			
													<option value="<?php echo $key2?>" <?php if(isset($multi_checkbox_data[$key]['wclp_store_hour_end']) && $multi_checkbox_data[$key]['wclp_store_hour_end'] == $key2){ echo 'selected'; }?>><?php echo $val2?></option>
												<?php } ?>
											</select>
										</fieldset>	
									</label>																		
								</div>												
						<?php }  ?>
						</div>
						<?php	
						}
						elseif( $array['type'] == 'single_select_country' ) { ?>
						
						<?php
							$country_setting = get_option($id, get_option('woocommerce_default_country'));
							if ( strstr( $country_setting, ':' ) ) {
								$country_setting = explode( ':', $country_setting );
								$country         = current( $country_setting );
								$state           = end( $country_setting );
							} else {
								$country = $country_setting;
								$state   = '*';
							}
							?>
								<fieldset>
									<select name="<?php echo esc_attr( $array['id'] ); ?>" style="" data-placeholder="<?php esc_attr_e( 'Choose a country / region&hellip;', 'woocommerce' ); ?>" aria-label="<?php esc_attr_e( 'Country / Region', 'woocommerce' ); ?>" class="wc-enhanced-select">
										<?php WC()->countries->country_dropdown_options( $country, $state ); ?>
									</select>
								</fieldset>
						<?php }
						else { ?>
                                                    
                        	<fieldset>
                                <input class="input-text regular-input " type="text" name="<?php echo $id?>" id="<?php echo $id?>" style="" value="<?php echo get_option($array['id'], get_option($array['default']))?>" placeholder="<?php if(!empty($array['placeholder'])){echo $array['placeholder'];} ?>">
                            </fieldset>
                        <?php } ?>
                        
					</td>
				</tr>
			<?php } } ?>
			</tbody>
		</table>
		<?php 
	}
	
	/*
	* get html of fields
	*/
	public function get_html2( $arrays ){
		
		$checked = '';
		?>
		<table class="form-table html-layout-2">
			<tbody>
            	<?php foreach( (array)$arrays as $id => $array ){
					if($array['show']){	
					?>                	
				<tr valign="top" class="html2_title_row">
					<?php if($array['type'] != 'desc'){ ?>										
					<th>
						<label for=""><?php echo $array['title']?><?php if(isset($array['title_link'])){ echo $array['title_link']; } ?>
							<?php if( isset($array['tooltip']) ){?>
                            	<span class="woocommerce-help-tip tipTip" title="<?php echo $array['tooltip']?>"></span>
                            <?php } ?>
                        </label>
					</th>
					<?php } ?>
				</tr>
				<tr>	
					<td class="forminp"  <?php if($array['type'] == 'desc'){ ?> colspan=2 <?php } ?>>
                    	<?php if( $array['type'] == 'checkbox' ){								
																						
								if(get_option($array['id'])){
									$checked = 'checked';
								} else{
									$checked = '';
								} 
							
							if(isset($array['disabled']) && $array['disabled'] == true){
								$disabled = 'disabled';
								$checked = '';
							} else{
								$disabled = '';
							}							
							?>
						<?php if($array['class'] == 'toggle'){?>
						<span class="mdl-list__item-secondary-action">
							<label class="mdl-switch mdl-js-switch mdl-js-ripple-effect" for="<?php echo $id?>">
								<input type="hidden" name="<?php echo $id?>" value="0"/>
								<input type="checkbox" id="<?php echo $id?>" name="<?php echo $id?>" class="mdl-switch__input" <?php echo $checked ?> value="1" <?php echo $disabled; ?>/>
							</label><p class="description"><?php echo (isset($array['desc']))? $array['desc']: ''?></p>
						</span>
						<?php } else { ?>
							<span class="checkbox">
								<label class="checkbx-label" for="<?php echo $id?>">
									<input type="hidden" name="<?php echo $id?>" value="0"/>
									<input type="checkbox" id="<?php echo $id?>" name="<?php echo $id?>" class="checkbox-input" <?php echo $checked ?> value="1" <?php echo $disabled; ?>/>
								</label><p class="description"><?php echo (isset($array['desc']))? $array['desc']: ''?></p>
							</span>
						<?php } ?>
						<?php } elseif( $array['type'] == 'textarea' ){ ?>
									<fieldset>
										<textarea rows="3" cols="20" class="input-text regular-input" type="textarea" name="<?php echo $id?>" id="<?php echo $id?>" style="" placeholder="<?php if(!empty($array['placeholder'])){echo $array['placeholder'];} ?>"><?php echo get_option($array['id'],$array['default']); ?></textarea>
									</fieldset>
                        <?php }  elseif( isset( $array['type'] ) && $array['type'] == 'dropdown' ){?>
                        	<?php
								if( isset($array['multiple']) ){
									$multiple = 'multiple';
									$field_id = $array['multiple'];
								} else {
									$multiple = '';
									$field_id = $id;
								}
							?>
                        	<fieldset>
								<select class="select select2" id="<?php echo $field_id?>" name="<?php echo $id?>" <?php echo $multiple;?> style="width:150px;"> <?php foreach((array)$array['options'] as $key => $val ){?>
                                    	<?php
											$selected = '';
											if( isset($array['multiple']) ){
												if (in_array($key, (array)$this->data->$field_id ))$selected = 'selected';
											} else {
												if( get_option($array['id']) == (string)$key )$selected = 'selected';
											}
                                        
										?>
										<option value="<?php echo $key?>" <?php echo $selected?> ><?php echo $val?></option>
                                    <?php } ?><p class="description"><?php echo (isset($array['desc']))? $array['desc']: ''?></p>
								</select>
							</fieldset>
                        <?php }
						
						elseif( $array['type'] == 'multiple_checkbox' ) { ?>
						
							<?php
								$op = 1;	
								foreach((array)$array['options'] as $key => $val ){									
										$multi_checkbox_data = get_option($id);
										if(isset($multi_checkbox_data[$key]) && $multi_checkbox_data[$key] == 1){
											$checked="checked";
										} else{
											$checked="";
										}?>
								<span class="wplp_multiple_checkbox">
									<label class="" for="<?php echo $key?>">
										<input type="hidden" name="<?php echo $id?>[<?php echo $key?>]" value="0"/>
										<input type="checkbox" id="<?php echo $key?>" name="<?php echo $id?>[<?php echo $key?>]" class=""  <?php echo $checked; ?> value="1"/>
										<span class="multiple_label"><?php echo $val; ?></span>	
										</br>
									</label>																		
								</span>												
						<?php } 
						
						}
						elseif( $array['type'] == 'pickup_day_time' ) { ?>
						
							<?php
								$op = 1;	
								foreach((array)$array['options'] as $key => $val ){									
										
										$multi_checkbox_data = get_option($id);										
										if(isset($multi_checkbox_data[$key]['checked']) && $multi_checkbox_data[$key]['checked'] == 1){
											$checked="checked";
										} else{
											$checked="";
										}
										
										$send_time_array = array();										
										for ( $hour = 0; $hour < 24; $hour++ ) {
											for ( $min = 0; $min < 60; $min = $min + 30 ) {
												$this_time = date( 'H:ia', strtotime( "$hour:$min" ) );
												$send_time_array[ $this_time ] = $this_time;
											}	
										}
										?>
								<div class="wplp_pickup_duration">
									<label class="" for="<?php echo $key?>">
										<input type="checkbox" id="<?php echo $key?>" name="<?php echo $id?>[<?php echo $key?>][checked]" class="pickup_days_checkbox"  <?php echo $checked; ?> value="1"/>
										<span class="pickup_days_lable"><?php echo $val; ?></span>	
										<fieldset class="wclp_pickup_time_fieldset">
											<select class="select wclp_pickup_time_select" name="<?php echo $id?>[<?php echo $key?>][wclp_store_hour]"> 
												<option value="" ><?php _e('Select', 'woocommerce'); ?></option>
												<?php foreach((array)$send_time_array as $key1 => $val1 ){ ?>
												<option value="<?php echo $key1?>" <?php if(isset($multi_checkbox_data[$key]['wclp_store_hour']) && $multi_checkbox_data[$key]['wclp_store_hour'] == $key1){ echo 'selected'; }?>><?php echo $val1?></option>
												<?php } ?>
											</select>
											<span> - </span>
											<select class="select wclp_pickup_time_select" name="<?php echo $id?>[<?php echo $key?>][wclp_store_hour_end]">    <option value="" ><?php _e('Select', 'woocommerce'); ?></option>
												<?php foreach((array)$send_time_array as $key2 => $val2 ){?>			
													<option value="<?php echo $key2?>" <?php if(isset($multi_checkbox_data[$key]['wclp_store_hour_end']) && $multi_checkbox_data[$key]['wclp_store_hour_end'] == $key2){ echo 'selected'; }?>><?php echo $val2?></option>
												<?php } ?>
											</select>
										</fieldset>	
									</label>																		
								</div>												
						<?php } 
						
						}
						elseif( $array['type'] == 'single_select_country' ) { ?>
						
						<?php
							$country_setting = get_option($id, get_option('woocommerce_default_country'));
							if ( strstr( $country_setting, ':' ) ) {
								$country_setting = explode( ':', $country_setting );
								$country         = current( $country_setting );
								$state           = end( $country_setting );
							} else {
								$country = $country_setting;
								$state   = '*';
							}
							?>
								<fieldset>
									<select name="<?php echo esc_attr( $array['id'] ); ?>" style="" data-placeholder="<?php esc_attr_e( 'Choose a country / region&hellip;', 'woocommerce' ); ?>" aria-label="<?php esc_attr_e( 'Country / Region', 'woocommerce' ); ?>" class="wc-enhanced-select">
										<?php WC()->countries->country_dropdown_options( $country, $state ); ?>
									</select>
								</fieldset>
						<?php }
						else { ?>
                                                    
                        	<fieldset>
                                <input class="input-text regular-input " type="text" name="<?php echo $id?>" id="<?php echo $id?>" style="" value="<?php echo get_option($array['id'], get_option($array['default']))?>" placeholder="<?php if(!empty($array['placeholder'])){echo $array['placeholder'];} ?>">
                            </fieldset>
                        <?php } ?>
                        
					</td>
				</tr>
			<?php } } ?>
			</tbody>
		</table>
		<?php 
	}
	
	/*
     * get_zorem_pluginlist
     * 
     * return array
    */
    public function get_zorem_pluginlist(){
		
        if ( !empty( $this->zorem_pluginlist ) ) return $this->zorem_pluginlist;
        
        if ( false === ( $plugin_list = get_transient( 'zorem_pluginlist' ) ) ) {
            
            $response = wp_remote_get( 'https://www.zorem.com/wp-json/pluginlist/v1/' );
            
            if ( is_array( $response ) && ! is_wp_error( $response ) ) {
                $body    = $response['body']; // use the content
                $plugin_list = json_decode( $body );
                set_transient( 'zorem_pluginlist', $plugin_list, 60*60*24 );
            } else {
                $plugin_list = array();
            }
        }
        return $this->zorem_pluginlist = $plugin_list;
    }
	
	/*
	* change style of available for pickup and picked up order label
	*/	
	function footer_function(){
		if ( !is_plugin_active( 'woocommerce-order-status-manager/woocommerce-order-status-manager.php' ) ) {
			$rfp_bg_color = get_option('wclp_ready_pickup_status_label_color','#365EA6');
			$rfp_color = get_option('wclp_ready_pickup_status_label_font_color','#fff');
			
			$pu_bg_color = get_option('wclp_pickup_status_label_color','#f1a451');
			$pu_color = get_option('wclp_pickup_status_label_font_color','#fff');						
			?>
			<style>
			.order-status.status-ready-pickup,.order-status-table .order-label.wc-ready-pickup{
				background: <?php echo $rfp_bg_color; ?>;
				color: <?php echo $rfp_color; ?>;
			}						
			.order-status.status-pickup,.order-status-table .order-label.wc-pickup{
				background: <?php echo $pu_bg_color; ?>;
				color: <?php echo $pu_color; ?>;
			}	
			</style>
			<?php
		}
	}
	
	/*
	* database update
	*/
	public function plugin_update_check(){				
		if(version_compare(get_option( 'wclp_local_pickup', '1.0' ),'1.1', '<') ){			
			$pickup_day_time = get_option('wclp_store_days');
			if(empty($pickup_day_time)){				
				$pickup_day_time_array = array(
					'monday' => array(
									'checked' => 1,
									'wclp_store_hour' => '09:00am',
									'wclp_store_hour_end' => '18:00pm',									
								),
					'tuesday' => array(
									'checked' => 1,
									'wclp_store_hour' => '09:00am',
									'wclp_store_hour_end' => '18:00pm',									
								),
					'wednesday' => array(
									'checked' => 1,
									'wclp_store_hour' => '09:00am',
									'wclp_store_hour_end' => '18:00pm',									
								),
					'thursday' => array(
									'checked' => 1,
									'wclp_store_hour' => '09:00am',
									'wclp_store_hour_end' => '18:00pm',									
								),
					'friday' => array(
									'checked' => 1,
									'wclp_store_hour' => '09:00am',
									'wclp_store_hour_end' => '18:00pm',									
								),				
				);
				update_option( 'wclp_store_days', $pickup_day_time_array);			
			}			
			update_option( 'wclp_local_pickup', '1.1');	
		}	
		
		if(version_compare(get_option( 'wclp_local_pickup', '1.0' ),'1.2', '<') ){		
			$wclp_show_pickup_instraction = get_option('wclp_show_pickup_instruction');
			$opt = array(
				'display_in_processing_email' => get_option('wclp_show_address_email'),
				'display_in_order_received_page' => get_option('wclp_show_address_order_received'),
				'display_in_order_details_page' => get_option('wclp_show_address_order_my_account'),
			);
			update_option( 'wclp_show_pickup_instruction', wc_clean($opt));
			update_option( 'wclp_local_pickup', '1.2');	
		}
		
		if(version_compare(get_option( 'wclp_local_pickup', '1.0' ),'1.3', '<') ){	
			$pickup_day_time = get_option('wclp_store_days');
			foreach($pickup_day_time as $day => $time){
				$pickup_day_time[$day]['wclp_store_hour'] = str_replace("am","",$pickup_day_time[$day]['wclp_store_hour']);
				$pickup_day_time[$day]['wclp_store_hour'] = str_replace("pm","",$pickup_day_time[$day]['wclp_store_hour']);
				$pickup_day_time[$day]['wclp_store_hour_end'] = str_replace("am","",$pickup_day_time[$day]['wclp_store_hour_end']);
				$pickup_day_time[$day]['wclp_store_hour_end'] = str_replace("pm","",$pickup_day_time[$day]['wclp_store_hour_end']);
			}
			
			$country_code = get_option( 'wclp_default_country', get_option('woocommerce_default_country') );		
			$split_country = explode( ":", $country_code );
			
			if(isset($split_country[0])){
				$store_country = $split_country[0];	
			} else{
				$store_country = '';
			}
			
			if(isset($split_country[1])){
				$store_state = $split_country[1];
			} else{
				$store_state   = '';
			}
			
			update_option( 'wclp_default_single_country', wc_clean($store_country));
			update_option( 'wclp_default_single_state', wc_clean($store_state));			
			update_option( 'wclp_store_days', wc_clean($pickup_day_time));
			update_option( 'wclp_local_pickup', '1.3');		
		}
		
		if(version_compare(get_option( 'wclp_local_pickup', '1.0' ),'1.4', '<') ){			
			update_option( 'wclp_default_time_format', '24');	
			update_option( 'wclp_local_pickup', '1.4');	
		}
	}
	
	/*
	* Add action button in order list to change order status from processing to ready for pickup and ready for pickup to Picked Up
	*/
	public function add_local_pickup_order_status_actions_button($actions, $order){			
		
		// Iterating through order shipping items
		foreach( $order->get_items( 'shipping' ) as $item_id => $shipping_item_obj ){			
			$shipping_method = $shipping_item_obj->get_method_id();						
		}
				
		//IF  dshipping method is not local pickup then @return;
		if( !isset($shipping_method ) ) return $actions;
		if( isset($shipping_method ) && $shipping_method != 'local_pickup' ) return $actions;
		
		?>
		<style>
		.widefat .column-wc_actions a.ready_for_pickup_icon.button::after{
			content: "";
			width: 20px;
			height: 20px;
			background: url("<?php echo wc_local_pickup()->plugin_dir_url()?>assets/images/rady_for_pickup_icon.png") no-repeat;
			background-size: contain;			
			top: 3px;
			left: 2px;
		}
		.widefat .column-wc_actions a.picked_up_icon.button::after{
			content: "";
			width: 20px;
			height: 20px;
			background: url("<?php echo wc_local_pickup()->plugin_dir_url()?>assets/images/picked_up_icon.png") no-repeat;
			background-size: contain;			
			top: 3px;
			left: 2px;
		}		
		</style>
		<?php
		if ( $order->has_status( array( 'processing' ) ) ) {
			// Get Order ID (compatibility all WC versions)
			$order_id = method_exists( $order, 'get_id' ) ? $order->get_id() : $order->id;
			// Set the action button
			$actions['ready_for_pickup'] = array(
				'url'       => wp_nonce_url( admin_url( 'admin-ajax.php?action=woocommerce_mark_order_status&status=ready-pickup&order_id=' . $order_id ), 'woocommerce-mark-order-status' ),
				'name'      => __( 'Mark order as ready for pickup', 'advanced-local-pickup-for-woocommerce' ),
				'action'    => "ready_for_pickup_icon", // keep "view" class for a clean button CSS
			);
		}

		if ( $order->has_status( array( 'ready-pickup' ) ) ) {
			// Get Order ID (compatibility all WC versions)
			$order_id = method_exists( $order, 'get_id' ) ? $order->get_id() : $order->id;
			// Set the action button
			$actions['pickup'] = array(
				'url'       => wp_nonce_url( admin_url( 'admin-ajax.php?action=woocommerce_mark_order_status&status=pickup&order_id=' . $order_id ), 'woocommerce-mark-order-status' ),
				'name'      => __( 'Mark order as picked up', 'advanced-local-pickup-for-woocommerce' ),
				'action'    => "picked_up_icon", // keep "view" class for a clean button CSS
			);
		}	
				
		return $actions;
	}	
	
	/*
	* add order again button for pickup order status	
	*/
	function add_reorder_button_pickup( $statuses ){
		$statuses[] = 'pickup';
		return $statuses;	
	}
	
	public function get_option_value_from_array($array,$key,$default_value){		
		$array_data = get_option($array);	
		$value = '';
		
		if(isset($array_data[$key])){
			$value = $array_data[$key];	
		}					
		
		if($value == ''){
			$value = $default_value;
		}
		return $value;
	}
}