"use strict";
var ajaxurl = admindata.ajaxurl;
var loader_image = admindata.loading_image;

"use strict";


jQuery(window).on('load',function(){
	if (jQuery(".epinputdate")[0]){	
		jQuery( ".epinputdate" ).datepicker( );
	}
});
jQuery(window).on('load',function(){
	
	if (jQuery("#user-data")[0]){
		jQuery('#user-data').show();
		var oTable = jQuery('#user-data').dataTable();
		oTable.fnSort( [ [1,'DESC'] ] );
	}
});
function iv_update_mailchamp_settings(){
	var search_params={
		"action"  : 	"jobboard_update_mailchamp_setting",	
		"form_data":	jQuery("#mailchimp_settings").serialize(), 
		"_wpnonce": 	admindata.settings,
	};
	jQuery.ajax({					
		url : ajaxurl,					 
		dataType : "json",
		type : "post",
		data : search_params,
		success : function(response){
			jQuery('#update_message').html('<div class="alert alert-info alert-dismissable"><a class="panel-close close" data-dismiss="alert">x</a>'+response.msg +'.</div>');
			location.reload();
		}
	});
}

function iv_create_home_page(){
	var search_params={
		"action"  : 	"jobboard_add_home_page",			
		"_wpnonce": 	admindata.settings,
	};
	jQuery.ajax({					
		url : ajaxurl,					 
		dataType : "json",
		type : "post",
		data : search_params,
		success : function(response){
			jQuery('#addhome-succcess').html(': '+response.msg );			
		}
	});
}
function delete_feature_field(id_delete){
	jQuery('#feature_'+id_delete).remove();
}
function add_feature_field(){
	var main_feature_div =jQuery('#pac_feature').html();
	jQuery('#pac_feature_all').append('<div class="clearfix"></div><hr/>'+main_feature_div+'');
}
var current_progress = 0;	
function iv_import_medo(){
			 var interval = setInterval(function() {
					current_progress += 10;
					jQuery("#dynamic")
					.css("width", current_progress + "%")
					.attr("aria-valuenow", current_progress)
					.text(current_progress + "% Complete");
					if (current_progress >= 90)
						clearInterval(interval);
				}, 1000);
	var search_params={
			"action"  : "jobboard_import_data",
			"_wpnonce": 	admindata.settings,
		};
		jQuery.ajax({					
			url : ajaxurl,					 
			dataType : "json",
			type : "post",
			data : search_params,
			success : function(response){
					current_progress = 90;
					jQuery("#dynamic")
					.css("width", current_progress + "%")
					.attr("aria-valuenow", current_progress)
					.text(current_progress + "% Complete");
					jQuery('#cptlink12').show(1000);
					jQuery('#importbutton').hide(500); 
			}
		})
	}

function update_user_setting() {
				// New Block For Ajax*****
			var search_params={
				"action"  : 	"jobboard_update_user_settings",	
				"form_data":	jQuery("#user_form_iv").serialize(), 
				"_wpnonce": 	admindata.settings,
			};
			jQuery.ajax({					
				url : ajaxurl,					 
				dataType : "json",
				type : "post",
				data : search_params,
				success : function(response){
					var url = admindata.wp_jobboard_ADMINPATH+"admin.php?page=wp-iv_user-directory-admin&message=success";    						
					jQuery(location).attr('href',url);
				}
			});
}
jQuery(function() {		
    jQuery('#package_sel').on("change", function () {
        this.form.submit();
    });
		jQuery(function() {
			jQuery( "#exp_date" ).datepicker({ dateFormat: 'dd-mm-yy' });
		});		
});
function update_stripe_setting() {
				// New Block For Ajax*****
				var ajaxurl = admindata.ajaxurl;
				var search_params={
					"action"  : 	"jobboard_update_stripe_settings",	
					"form_data":	jQuery("#stripe_form_iv").serialize(), 
					"_wpnonce": 	admindata.paymentgateway,
				};
				jQuery.ajax({					
					url : ajaxurl,					 
					dataType : "json",
					type : "post",
					data : search_params,
					success : function(response){
							jQuery('#iv-loading').html('<div class="col-md-12 alert alert-success">Update Successfully. <a class="btn btn-success btn-xs" href="?page=wp-jobboard-payment-settings"> Go Payment Setting Page</aa></div>');
						
					}
				});
				
	}
function iv_update_payment_settings_terms() {
				// New Block For Ajax*****
				var search_params={
					"action"  : 	"jobboard_update_payment_setting",	
					"form_data":	jQuery("#payment_settings").serialize(), 
					"_wpnonce": 	admindata.settings,	
				};
				jQuery.ajax({					
					url : ajaxurl,					 
					dataType : "json",
					type : "post",
					data : search_params,
					success : function(response){
						jQuery('#update_message').html('<div class="alert alert-info alert-dismissable"><a class="panel-close close" data-dismiss="alert">x</a>'+response.msg +'.</div>');
					}
				});
	}
function iv_update_page_settings(){
				var search_params={
					"action"  : 	"jobboard_update_page_setting",	
					"form_data":	jQuery("#page_settings").serialize(), 
					"_wpnonce": 	admindata.settings,	
				};
				jQuery.ajax({					
					url : ajaxurl,					 
					dataType : "json",
					type : "post",
					data : search_params,
					success : function(response){
					jQuery('#update_message').html('<div class="alert alert-info alert-dismissable"><a class="panel-close close" data-dismiss="alert">x</a>'+response.msg +'.</div>');
					}
				});
}
function iv_update_email_settings(){
				var search_params={
					"action"  : 	"jobboard_update_email_setting",	
					"form_data":	jQuery("#email_settings").serialize(), 
					"_wpnonce": 	admindata.settings,	
				};
				jQuery.ajax({					
					url : ajaxurl,					 
					dataType : "json",
					type : "post",
					data : search_params,
					success : function(response){
							jQuery('#update_message').html('<div class="alert alert-info alert-dismissable"><a class="panel-close close" data-dismiss="alert">x</a>'+response.msg +'.</div>');
							jQuery('#email-success').html('<div class="alert alert-info alert-dismissable"><a class="panel-close close" data-dismiss="alert">x</a>'+response.msg +'.</div>');
					}
				});
}			
function iv_update_account_settings(){
				var search_params={
					"action"  : 	"jobboard_update_account_setting",	
					"form_data":	jQuery("#account_settings").serialize(),
					"_wpnonce": 	admindata.settings,						
				};
				jQuery.ajax({					
					url : ajaxurl,					 
					dataType : "json",
					type : "post",
					data : search_params,
					success : function(response){
						jQuery('#update_message').html('<div class="alert alert-info alert-dismissable"><a class="panel-close close" data-dismiss="alert">x</a>'+response.msg +'.</div>');
					}
				});
}
function iv_update_protected_settings(){
var search_params={
		"action"  : 	"jobboard_update_protected_setting",	
		"form_data":	jQuery("#protected_settings").serialize(), 
		"_wpnonce": 	admindata.settings,	
	};
	jQuery.ajax({					
		url : ajaxurl,					 
		dataType : "json",
		type : "post",
		data : search_params,
		success : function(response){
			jQuery('#update_message').html('<div class="alert alert-info alert-dismissable"><a class="panel-close close" data-dismiss="alert">x</a>'+response.msg +'.</div>');
		}
	})
}
function protect_select_all(sel_name) {
	   if(jQuery("#"+sel_name+"_all").prop("checked") == true){			
			jQuery("."+sel_name).prop("checked",jQuery("#"+sel_name+"_all").prop("checked"));
		}else{
			jQuery("."+sel_name).prop("checked", false);
		}
}

function update_profile_fields(){
		var ajaxurl = admindata.ajaxurl;
		var search_params = {
			"action": 		"jobboard_update_profile_fields",
			"form_data":	jQuery("#profile_fields").serialize(),
			"_wpnonce": 	admindata.mymenu,	
		};
		jQuery.ajax({
			url: ajaxurl,
			dataType: "json",
			type: "post",
			data: search_params,
			success: function(response) {  
				jQuery('#messageprofile').html('<div class="alert alert-info alert-dismissable"><a class="panel-close close" data-dismiss="alert">x</a>'+response.code +'.</div>');
			}
		});
	}
function update_paypal_setting() {
				var ajaxurl = admindata.ajaxurl;
				// New Block For Ajax*****
				var search_params={
					"action"  : 	"jobboard_update_paypal_settings",	
					"form_data":	jQuery("#paypal_form_iv").serialize(), 
					"_wpnonce": admindata.paymentgateway,
				};
				jQuery.ajax({					
					url : ajaxurl,					 
					dataType : "json",
					type : "post",
					data : search_params,
					success : function(response){
						jQuery('#iv-loading').html('<div class="col-md-5 alert alert-success">Update Successfully. <a class="btn btn-success btn-xs" href="?page=wp-jobboard-payment-settings"> Go Payment Setting Page</aa></div>');
						
					}
				});
				
	}
function  iv_update_payment_gateways_settings (){
		var ajaxurl = admindata.ajaxurl;
		
		var search_params = {
			"action": "jobboard_gateway_settings_update",
			"payment_gateway": jQuery("input[name=payment_gateway]:checked").val(),	
			"_wpnonce": admindata.paymentgateway,
			
		};
		jQuery.ajax({
			url: ajaxurl,
			dataType: "json",
			type: "post",
			data: search_params,
			success: function(response) { 
				jQuery('#update_message').html('<div class="alert alert-info alert-dismissable"><a class="panel-close close" data-dismiss="alert">x</a>'+response.msg +'.</div>');					             		
			
			}
		});
}
function update_the_package() {
		var ajaxurl = admindata.ajaxurl;
		var loader_image = admindata.loading_image;
	jQuery("#loading").html(loader_image);
			// New Block For Ajax*****
			
			var search_params={
				"action"  : 	"jobboard_update_package",	
				"form_data":	jQuery("#package_form_iv").serialize(),
				"_wpnonce": admindata.packagenonce,			
				
			};
			jQuery.ajax({					
				url : ajaxurl,					 
				dataType : "json",
				type : "post",
				data : search_params,
				success : function(response){						
					var url = admindata.wp_jobboard_ADMINPATH+"admin.php?page=wp-jobboard-package-all&form_submit=success";    						
					jQuery(location).attr('href',url);
				}
			});
			
}
function save_the_package() {
		var ajaxurl = admindata.ajaxurl;
		var loader_image = admindata.loading_image;
		jQuery("#loading").html(loader_image);
	
				// New Block For Ajax*****
				var search_params={
					"action"  : 	"jobboard_save_package",	
					"form_data":	jQuery("#package_form_iv").serialize(),
					"_wpnonce": admindata.packagenonce,	
				};
				jQuery.ajax({					
					url : ajaxurl,					 
					dataType : "json",
					type : "post",
					data : search_params,
					success : function(response){
						var url = admindata.wp_jobboard_ADMINPATH+"admin.php?page=wp-jobboard-package-all&form_submit=success";    						
						jQuery(location).attr('href',url);	
					}
				});
				
	}	

jQuery(document).ready(function(){		

		jQuery('#package_recurring').on("click", function(){
			if(this.checked){				
				jQuery('#recurring_block').show();
			}else{				
				jQuery('#recurring_block').hide();
			}
		});
	
		jQuery('#package_enable_trial_period').on("click", function(){
			if(this.checked){				
				jQuery('#trial_block').show();
			}else{				
				jQuery('#trial_block').hide();
			}
		});
});		

function iv_package_status_change(status_id,curr_status){
	status_id =status_id.trim();
	curr_status=curr_status.trim();
	var ajaxurl = admindata.ajaxurl;
	var search_params = {
		"action": 	"jobboard_update_package_status",
		"status_id": status_id,	
		"status_current":curr_status,
		"_wpnonce": admindata.packagenonce,
	};
	jQuery.ajax({
		url: ajaxurl,
		dataType: "json",
		type: "post",
		data: search_params,
		success: function(response) {   
			if(response.code=='success'){					
				jQuery("#status_"+status_id).html('<button class="btn btn-info btn-xs" onclick="return iv_package_status_change(\' '+status_id+' \' ,\' '+response.current_st+' \');">'+response.msg+'</button>');
			}
		}
	});
}	
function change_marker_image(cat_image_id){	
	var image_gallery_frame;
	image_gallery_frame = wp.media.frames.downloadable_file = wp.media({
		// Set the title of the modal.
		title: 'Marker Image',
		button: {
			text: 'Marker Image',
		},
		multiple: false,
		displayUserSettings: true,
	});                
	image_gallery_frame.on( 'select', function() {
		var selection = image_gallery_frame.state().get('selection');
		selection.map( function( attachment ) {
			attachment = attachment.toJSON();
			if ( attachment.id ) {							
				var ajaxurl = admindata.ajaxurl;
				var search_params = {
					"action": 	"jobboard_update_map_marker",
					"attachment_id": attachment.id,
					"category_id": cat_image_id,
					"_wpnonce": admindata.catimage,
				};
				jQuery.ajax({
					url: ajaxurl,
					dataType: "json",
					type: "post",
					data: search_params,
					success: function(response) {   
						if(response=='success'){					
							jQuery('#marker_'+cat_image_id).html('<img width="20px" src="'+attachment.url+'">');                              
						}
					}
				});									
			}
		});
	});               
	image_gallery_frame.open(); 
}
function change_cate_image(cat_image_id){	
	var image_gallery_frame;
	image_gallery_frame = wp.media.frames.downloadable_file = wp.media({
		// Set the title of the modal.
		title: 'Category Image',
		button: {
			text: 'Category Image',
		},
		multiple: false,
		displayUserSettings: true,
	});                
	image_gallery_frame.on( 'select', function() {
		var selection = image_gallery_frame.state().get('selection');
		selection.map( function( attachment ) {
			attachment = attachment.toJSON();
			if ( attachment.id ) {							
				var ajaxurl =admindata.ajaxurl;
				var search_params = {
					"action": 	"jobboard_update_cate_image",
					"attachment_id": attachment.id,
					"category_id": cat_image_id,
					"_wpnonce": admindata.catimage,
				};
				jQuery.ajax({
					url: ajaxurl,
					dataType: "json",
					type: "post",
					data: search_params,
					success: function(response) {   
						if(response=='success'){					
							jQuery('#cate_'+cat_image_id).html('<img width="100px" src="'+attachment.url+'">');
						}
					}
				});									
			}
		});
	});               
	image_gallery_frame.open(); 
}
function change_vip_image(){	 
	var image_gallery_frame;
	image_gallery_frame = wp.media.frames.downloadable_file = wp.media({
		// Set the title of the modal.
		title: 'VIP Image',
		button: {
			text: 'VIP Image',
		},
		multiple: false,
		displayUserSettings: true,
	});                
	image_gallery_frame.on( 'select', function() {
		var selection = image_gallery_frame.state().get('selection');
		selection.map( function( attachment ) {
			attachment = attachment.toJSON();
			if ( attachment.id ) {	
				var search_params = {
					"action": 	"iv_directories_update_vip_image",
					"attachment_id": attachment.id,		
					"_wpnonce": admindata.dirsetting,	
				};
				jQuery.ajax({
					url: ajaxurl,
					dataType: "json",
					type: "post",
					data: search_params,
					success: function(response) {   
						if(response=='success'){
							jQuery('#current_vip').html('<img width="40px" src="'+attachment.url+'">'); 
						}
					}
				});									
			}
		});
	});
	image_gallery_frame.open(); 
}
function iv_update_dir_setting(){
	var search_params={
		"action"  : 	"iv_update_dir_setting",	
		"form_data":	jQuery("#directory_settings").serialize(), 
		"_wpnonce": admindata.dirsetting,		
	};
	jQuery.ajax({					
		url : ajaxurl,					 
		dataType : "json",
		type : "post",
		data : search_params,
		success : function(response){
			jQuery('#update_message').html('<div class="alert alert-info alert-dismissable"><a class="panel-close close" data-dismiss="alert">x</a>'+response.msg +'.</div>');
			jQuery('#update_message49').html('<div class="alert alert-info alert-dismissable"><a class="panel-close close" data-dismiss="alert">x</a>'+response.msg +'.</div>');
		}
	})
}
function update_dir_fields(){
	var search_params = {
		"action": 		"jobboard_update_dir_fields",
		"form_data":	jQuery("#dir_fields").serialize(), 
		"_wpnonce": admindata.fields,	
	};
	jQuery.ajax({
		url: ajaxurl,
		dataType: "json",
		type: "post",
		data: search_params,
		success: function(response) {              		
			jQuery('#success_message-fields').html('<div class="alert alert-info alert-dismissable"><a class="panel-close close" data-dismiss="alert">x</a>'+response.code +'.</div>');
		}
	});
}
function iv_update_coupon() {	
	// New Block For Ajax*****
	var search_params={
		"action"  : 	"jobboard_update_coupon",	
		"form_data":	jQuery("#coupon_form_iv").serialize(), 
		"form_pac_ids": jQuery("#package_id").val(),
		"_wpnonce": admindata.coupon,
	};
	jQuery.ajax({					
		url : ajaxurl,					 
		dataType : "json",
		type : "post",
		data : search_params,
		success : function(response){
			var url = admindata.wp_jobboard_ADMINPATH+"admin.php?page=wp-jobboard-coupons-form&form_submit=success";
			jQuery(location).attr('href',url);
		}
	});
}
jQuery(function() {	
	if (jQuery("#start_date")[0]){
			jQuery( "#start_date" ).datepicker({ dateFormat: 'dd-mm-yy' });
	}	
	if (jQuery("#end_date")[0]){
		jQuery( "#end_date" ).datepicker({ dateFormat: 'dd-mm-yy' });
	}
	

});
function iv_create_coupon() {	
	// New Block For Ajax*****
	var search_params={
		"action"  : 	"jobboard_create_coupon",	
		"form_data":	jQuery("#coupon_form_iv").serialize(), 
		"form_pac_ids": jQuery("#package_id").val(),
		"_wpnonce": admindata.coupon,
	};
	jQuery.ajax({					
		url : ajaxurl,					 
		dataType : "json",
		type : "post",
		data : search_params,
		success : function(response){
			var url = admindata.wp_jobboard_ADMINPATH+"admin.php?page=wp-jobboard-coupons-form&form_submit=success";    						
			jQuery(location).attr('href',url);
		}
	});
}
function change_city_image(city_image_id){	
	var image_gallery_frame;
	image_gallery_frame = wp.media.frames.downloadable_file = wp.media({
		// Set the title of the modal.
		title: admindata.SetImage,
		button: {
			text: admindata.SetImage,
		},
		multiple: false,
		displayUserSettings: true,
	});                
	image_gallery_frame.on( 'select', function() {
		var selection = image_gallery_frame.state().get('selection');
		selection.map( function( attachment ) {
			attachment = attachment.toJSON();
			if ( attachment.id ) {							
				var ajaxurl = admindata.ajaxurl;
				var search_params = {
					"action": 	"jobboard_update_city_image",
					"attachment_id": attachment.id,
					"city_id": city_image_id,
					"_wpnonce": admindata.cityimage,
				};
				jQuery.ajax({
					url: ajaxurl,
					dataType: "json",
					type: "post",
					data: search_params,
					success: function(response) {   
						if(response=='success'){					
							jQuery('#city_'+city_image_id).html('<img width="100px" src="'+attachment.url+'">');                              
						}
					}
				});									
			}
		});
	});               
	image_gallery_frame.open(); 
}