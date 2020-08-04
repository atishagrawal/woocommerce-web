jQuery(document).ready(function(){
	"use strict";
	jQuery(".tipTip").tipTip();
	jQuery('#wclp_ready_pickup_status_label_color, #wclp_pickup_status_label_color').wpColorPicker();
	
	jQuery('#wclp_pickup_status_label_color').wpColorPicker({
		change: function(e, ui) {
			var color = ui.color.toString();			
			jQuery('.order-status-table .order-label.wc-pickup').css('background',color);
		}, 
	});
	
	jQuery('#wclp_ready_pickup_status_label_color').wpColorPicker({
		change: function(e, ui) {
			var color = ui.color.toString();			
			jQuery('.order-status-table .order-label.wc-ready-pickup').css('background',color);
		}, 
	});
});

jQuery(document).on("change", "#wclp_pickup_status_label_font_color", function(){
	var font_color = jQuery(this).val();
	jQuery('.order-status-table .order-label.wc-pickup').css('color',font_color);
});

jQuery(document).on("change", "#wclp_ready_pickup_status_label_font_color", function(){
	var font_color = jQuery(this).val();
	jQuery('.order-status-table .order-label.wc-ready-pickup').css('color',font_color);
});

jQuery(document).on("click", "#wclp_status_pickup", function(){
	if(jQuery(this).prop("checked") == true){
        jQuery(this).closest('tr').removeClass('disable_row');				
    } else{
		jQuery(this).closest('tr').addClass('disable_row');
	}	
});


/*ajex call for general tab form save*/	
jQuery(document).on("submit", "#wclp_setting_tab_form", function(){
	"use strict";
	jQuery("#wclp_setting_tab_form .spinner").addClass("active");
	var form = jQuery('#wclp_setting_tab_form');
	jQuery.ajax({
		url: ajaxurl,//csv_workflow_update,		
		data: form.serialize(),
		type: 'POST',
		dataType:"json",	
		success: function(response) {
			if( response.success === "true" ){
				jQuery("#wclp_setting_tab_form .spinner").removeClass("active");
				var snackbarContainer = document.querySelector('#wclp-toast-example');
				var data = {message: 'Setting saved successfully.'};
				snackbarContainer.MaterialSnackbar.showSnackbar(data);
			} else {
				//show error on front
			}
		},
		error: function(response) {
			console.log(response);			
		}
	});
	return false;
});

/*ajex call for general tab form save*/	
jQuery(document).on("submit", "#wclp_location_tab_form", function(){
	"use strict";
	jQuery(".alp_error_msg").remove();
	var validation = true;
	var days = [ 'saturday', 'friday', 'thursday', 'wednesday', 'tuesday', 'monday', 'sunday' ];		
	for ( var i = 0, l = days.length; i < l; i++ ) {		
		
		jQuery('select[name="wclp_store_days['+days[ i ]+'][wclp_store_hour]"]').css('border-color','#ddd');
		jQuery('select[name="wclp_store_days['+days[ i ]+'][wclp_store_hour_end]"]').css('border-color','#ddd');
		
		if(jQuery('#'+days[ i ]).prop("checked") == true){
			var wclp_store_hour = jQuery('select[name="wclp_store_days['+days[ i ]+'][wclp_store_hour]"] option:selected').val();
			var wclp_store_hour_end = jQuery('select[name="wclp_store_days['+days[ i ]+'][wclp_store_hour_end]"] option:selected').val();
			
			if(wclp_store_hour == ''){				
				jQuery('.btn_location_submit').after('<div class="alp_error_msg">Please select Working start working hours for '+days[ i ]+'</div>');
				jQuery('select[name="wclp_store_days['+days[ i ]+'][wclp_store_hour]"]').css('border-color','red');
				jQuery('.alp_error_msg').show();
				validation=false;
			}
			if(wclp_store_hour_end == ''){
				jQuery('select[name="wclp_store_days['+days[ i ]+'][wclp_store_hour_end]"]').css('border-color','red');
				jQuery('.btn_location_submit').after('<div class="alp_error_msg">Please select Working end working hours for '+days[ i ]+'</div>');
				jQuery('.alp_error_msg').show();
				validation=false;
			}
			if(wclp_store_hour != '' && wclp_store_hour_end != ''){
				var st = minFromMidnight(wclp_store_hour);
				var et = minFromMidnight(wclp_store_hour_end);
				if(st>=et){
					jQuery('select[name="wclp_store_days['+days[ i ]+'][wclp_store_hour]"]').css('border-color','red');
					jQuery('select[name="wclp_store_days['+days[ i ]+'][wclp_store_hour_end]"]').css('border-color','red');
					jQuery('.btn_location_submit').after('<div class="alp_error_msg">End time must be greater than start time for '+days[ i ]+'</div>');
					jQuery('.alp_error_msg').show();
					validation=false;
				}
			}
		}
	}
	
	if(validation == true){
		jQuery("#wclp_location_tab_form .spinner").addClass("active");
		var form = jQuery('#wclp_location_tab_form');
		jQuery.ajax({
			url: ajaxurl,
			data: form.serialize(),
			type: 'POST',
			dataType:"json",	
			success: function(response) {
				if( response.success === "true" ){
					jQuery("#wclp_location_tab_form .spinner").removeClass("active");
					var snackbarContainer = document.querySelector('#wclp-toast-example');
					var data = {message: 'Setting saved successfully.'};
					snackbarContainer.MaterialSnackbar.showSnackbar(data);
				} else {
					//show error on front
				}
			},
			error: function(response) {
				console.log(response);			
			}
		});
	}
	return false;
});

function minFromMidnight(tm){
	var ampm= tm.substr(-2)
	var clk = tm.substr(0, 5);
	var m  = parseInt(clk.match(/\d+$/)[0], 10);
	var h  = parseInt(clk.match(/^\d+/)[0], 10);
	h += (ampm.match(/pm/i))? 12: 0;
	return h*60+m;
}

jQuery(document).on("click", ".wclp_tab_input", function(){
	"use strict";
	var tab = jQuery(this).data('tab');
	var url = window.location.protocol + "//" + window.location.host + window.location.pathname+"?page=local_pickup&tab="+tab;
	window.history.pushState({path:url},'',url);	
});
jQuery(document).on("click", ".pickup_days_checkbox", function(){
	if(jQuery(this).prop("checked") == true){
		jQuery(this).closest('.wplp_pickup_duration').find('.wclp_pickup_time_fieldset').show();
	} else{
		jQuery(this).closest('.wplp_pickup_duration').find('.wclp_pickup_time_fieldset').hide();
	}
});
jQuery(document).ready(function(){
	var pickup_days_checkbox = jQuery('.pickup_days_checkbox');
	jQuery(pickup_days_checkbox).each(function(){		
		if(jQuery(this).prop("checked") == true){
			jQuery(this).closest('.wplp_pickup_duration').find('.wclp_pickup_time_fieldset').show();
		} else{
			jQuery(this).closest('.wplp_pickup_duration').find('.wclp_pickup_time_fieldset').hide();
		}
	});
});


/*ajex call for general tab form save*/	
jQuery(document).on("change", "#wclp_default_single_country", function(){
	"use strict";
	
	var country = jQuery(this).val();
	var data = {
		action: 'wclp_update_state_dropdown',
		country: country,
	};		
	
	jQuery.ajax({
		url: ajaxurl,
		data: data,
		type: 'POST',
		dataType:"json",	
		success: function(response) {
			if(response.state != 'empty'){
				jQuery('#wclp_default_single_state').empty().append(response.state);				
				jQuery("#wclp_default_single_state").closest('tr').show();
			} else{
				jQuery('#wclp_default_single_state').empty();
				jQuery("#wclp_default_single_state").closest('tr').hide();
			}			
		},
		error: function(response) {
			console.log(response);			
		}
	});
	return false;
});

/*ajex call for general tab form save*/	
jQuery(document).on("change", "#wclp_default_time_format", function(){
	"use strict";
	jQuery("#wclp_location_tab_form").block({
		message: null,
		overlayCSS: {
			background: "#fff",
			opacity: .6
		}	
    });	
	var hour_format = jQuery(this).val();
	var data = {
		action: 'wclp_update_work_hours_list',
		hour_format: hour_format,
	};		
	
	jQuery.ajax({
		url: ajaxurl,
		data: data,
		type: 'POST',
		dataType:"json",	
		success: function(response) {
			if(response.pickup_hours_div){
				jQuery(".pickup_hours_div").replaceWith(response.pickup_hours_div);
				var pickup_days_checkbox = jQuery('.pickup_days_checkbox');
				jQuery(pickup_days_checkbox).each(function(){		
					if(jQuery(this).prop("checked") == true){
						jQuery(this).closest('.wplp_pickup_duration').find('.wclp_pickup_time_fieldset').show();
					} else{
						jQuery(this).closest('.wplp_pickup_duration').find('.wclp_pickup_time_fieldset').hide();
					}
				});
				jQuery("#wclp_location_tab_form").unblock();
			}
				
		},
		error: function(response) {
			console.log(response);			
		}
	});
	return false;
});