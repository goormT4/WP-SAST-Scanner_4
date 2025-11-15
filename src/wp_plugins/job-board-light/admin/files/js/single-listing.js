"use strict";
var ajaxurl = jobboard_data.ajaxurl;
var loader_image =jobboard_data.loading_image;
var paged =1;
jQuery( document ).ready(function() { 
	var isLogged =jobboard_data.current_user_id;
	jQuery(".jobbookmark").on('click', function(e){
		if (isLogged=="0") {
			alert(jobboard_data.Please_login);
			} else { 
			var not_bookmark_yet = jQuery(this).closest('.btn-add-favourites').attr("id");
			var alreay_bookmark = jQuery(this).closest('.btn-added-favourites').attr("id");
			if (typeof not_bookmark_yet === "undefined") { 		
				// Allready bookmarked 				
					var not_bookmark_yet_id = jQuery(this).closest('.btn-added-favourites').attr("id");	
					var p_id= not_bookmark_yet_id.replace("jobbookmark", '');						
					var search_params={
						"action"  : 	"jobboard_save_un_favorite",
						"data": "id=" + p_id,
						"_wpnonce":  	jobboard_data.contact,
					};					
					jQuery.ajax({
						url : ajaxurl,
						dataType : "json",
						type : "post",
						data : search_params,
						success : function(response){						
							if (response.msg=="success") {
								jQuery("#"+alreay_bookmark).removeClass('btn-added-favourites').addClass('btn-add-favourites',{duration:1000});
								jQuery('#'+alreay_bookmark).prop('title', jobboard_data.Add_to_Favorites);			
							}
						}
					});	
				
				}else{					
					// not_bookmark_yet jobbookmark	
					var not_bookmark_yet_id = jQuery(this).closest('.btn-add-favourites').attr("id");			
					var p_id= not_bookmark_yet_id.replace("jobbookmark", '');	
					var search_params={
						"action"  : 	"jobboard_save_favorite",
						"data": "id=" + p_id,
						"_wpnonce":  	jobboard_data.contact,
					};					
					jQuery.ajax({
						url : ajaxurl,
						dataType : "json",
						type : "post",
						data : search_params,
						success : function(response){						
							if (response.msg=="success") {
								jQuery("#"+not_bookmark_yet).removeClass('btn-add-favourites').addClass('btn-added-favourites',{duration:1000});
								jQuery('#'+not_bookmark_yet).prop('title', jobboard_data.Added_to_Favorites);			
							}
						}
					});	
				
			}
			
			}
			
		});
	
});
function contact_close(){
	jQuery.colorbox.close();
}
function call_popup_agent_info(dir_id){
	var contactform = jobboard_data.wp_jobboard_URLPATH+'/template/listing/agent-info.php?&dir_id='+dir_id;
	jQuery.colorbox({href: contactform,opacity:"0.70",closeButton:false,});
}
function call_popup_claim(dir_id){
	var contactform = jobboard_data.wp_jobboard_URLPATH+'/template/listing/claim.php?&dir_id='+dir_id;
	jQuery.colorbox({href: contactform,opacity:"0.70",closeButton:false,});
}
function call_popup(dir_id){
	var contactform = jobboard_data.wp_jobboard_URLPATH+'/template/listing/contact_popup.php?&dir_id='+dir_id;				
	jQuery.colorbox({href: contactform,opacity:"0.70",closeButton:false,});
}
function apply_popup(dir_id){
	// for directi link var contactform = jobboard_data.wp_jobboard_URLPATH+'/template/listing/contact_popup.php?&dir_id='+dir_id;	
	var contactform =jobboard_data.ajaxurl+'?action=jobboard_apply_popup&dir_id='+dir_id;
	jQuery.colorbox({href: contactform,opacity:"0.70",closeButton:false,});
}

function job_apply_user(){
	
	var ajaxurl = jobboard_data.ajaxurl;
	var loader_image = jobboard_data.loading_image;
	jQuery('#message_popupjob_apply_user').html(loader_image);
	var search_params={
		"action"  :  "jobboard_apply_submit_login",
		"form_data": jQuery("#apply-pop2").serialize(),
		"_wpnonce": jobboard_data.listing,
	};
	jQuery.ajax({
		url : ajaxurl,
		dataType : "json",
		type : "post",
		data : search_params,
		success : function(response){
			if (response.code=="success") {
			 jQuery("#apply-pop2").hide();
			jQuery('#message_popupjob_apply_user').html('<div class="col-md-12 alert alert-info alert-dismissable"><h4>'+response.msg +'.</h4></div>');
			jQuery("#apply-pop2")[0].reset();
			}
		}
	});
}
function job_apply_nonlogin(){ 	 
	var formc = jQuery("#apply-pop");
	var ajaxurl = jobboard_data.ajaxurl;
	var loader_image =jobboard_data.loading_image;
	
	
	if (jQuery.trim(jQuery("#email_address",formc).val()) == "" || jQuery.trim(jQuery("#canname",formc).val()) == "" || jQuery.trim(jQuery("#cover-content",formc).val()) == "" || jQuery.trim(jQuery("#finalresume",formc).val()) == ""  ) {				
		jQuery('#update_message_popup80').html(jobboard_data.Please_put_your_message);
		} else {
		
		jQuery('#update_message_popup80').html(loader_image);
		
		var applyformdata = new FormData();
		var form_data_serialize = jQuery("#apply-pop").serialize();
		applyformdata.append("form_data", form_data_serialize);			
		var file = jQuery(document).find('input[type="file"]');		
		var individual_file = file[0].files[0];
		applyformdata.append("file", individual_file);			
		applyformdata.append('action', 'jobboard_apply_submit_nonlogin');
		applyformdata.append('_wpnonce', jobboard_data.listing);	
		
		jQuery.ajax({
			url : ajaxurl,
			dataType : "json",
			type : "POST",			
			contentType: false,
			processData: false,
			data : applyformdata,
			success : function(response){
				if (response.code=="success") {
						jQuery("#apply-pop").trigger('reset');
						jQuery("#apply-pop").hide();
						jQuery('#update_message_popup80').html('<div class="col-md-12 alert alert-info alert-dismissable"><h4>'+response.msg +'.</h4></div>');
						
				}
			}
		});
	}
}

function iv_submit_review(){
	var isLogged =jobboard_data.current_user_id;
	if (isLogged=="0") {
		alert(jobboard_data.Please_login);
		} else {
		var form = jQuery("#iv_review_form");
		if (jQuery.trim(jQuery("#review_comment", form).val()) == "") {
			alert(jobboard_data.Please_put_your_message);
			} else {
			var ajaxurl = jobboard_data.ajaxurl;
			var loader_image = jobboard_data.loading_image;
			jQuery('#rmessage').html(loader_image);
			var search_params={
				"action"  :  "iv_directories_save_user_review",
				"form_data": jQuery("#iv_review_form").serialize(),
				"_wpnonce": jobboard_data.listing,
			};
			jQuery.ajax({
				url : ajaxurl,
				dataType : "json",
				type : "post",
				data : search_params,
				success : function(response){
					jQuery('#rmessage').html('<div class="col-sm-7 alert alert-info alert-dismissable"><a class="panel-close close" data-dismiss="alert">x</a>'+response.msg +'.</div>');
					jQuery("#iv_review_form")[0].reset();
				}
			});
		}
	}
}
(function($) {
	$.fn.bcSwipe = function(settings) {
		var config = { threshold: 50 };
		if (settings) {
			$.extend(config, settings);
		}
		this.each(function() {
			var stillMoving = false;
			var start;
			if ('ontouchstart' in document.documentElement) {
				this.addEventListener('touchstart', onTouchStart, false);
			}
			function onTouchStart(e) {
				if (e.touches.length == 1) {
					start = e.touches[0].pageX;
					stillMoving = true;
					this.addEventListener('touchmove', onTouchMove, false);
				}
			}
			function onTouchMove(e) {
				if (stillMoving) {
					var x = e.touches[0].pageX;
					var difference = start - x;
					if (Math.abs(difference) >= config.threshold) {
						cancelTouch();
						if (difference > 0) {
							$(this).carousel('next');
						}
						else {
							$(this).carousel('prev');
						}
					}
				}
			}
			function cancelTouch() {
				this.removeEventListener('touchmove', onTouchMove);
				start = null;
				stillMoving = false;
			}
		});
		return this;
	};
})(jQuery);
jQuery('#carouselExampleControls').bcSwipe({ threshold: 50 });
jQuery('#similarPrppertycarousel').bcSwipe({ threshold: 50 });

function contact_send_message_iv(){
	var formc = jQuery("#message-pop");
	
	if (jQuery.trim(jQuery("#email_address",formc).val()) == "" || jQuery.trim(jQuery("#name",formc).val()) == "" || jQuery.trim(jQuery("#message-content",formc).val()) == "") {
		alert(jobboard_data.Please_put_your_message);
		} else {
		var ajaxurl = jobboard_data.ajaxurl;
		var loader_image =jobboard_data.loading_image;
		jQuery('#update_message_popup').html(loader_image);
		var search_params={
			"action"  : 	"jobboard_message_send",
			"form_data":	jQuery("#message-pop").serialize(),
			"_wpnonce":  	jobboard_data.contact,
		};
		jQuery.ajax({
			url : ajaxurl,
			dataType : "json",
			type : "post",
			data : search_params,
			success : function(response){
				jQuery('#update_message_popup').html(response.msg );
				jQuery("#message-pop").trigger('reset');
			}
		});
	}
}

function save_favorite(id) {  
	var isLogged =jobboard_data.current_user_id;
	if (isLogged=="0") {
		alert(jobboard_data.Please_login);
		} else {
		var ajaxurl = jobboard_data.ajaxurl;
		var search_params={
			"action"  : 	"jobboard_save_favorite",
			"data": "id=" + id,
			"_wpnonce":  	jobboard_data.contact,
		};
		jQuery.ajax({
			url : ajaxurl,
			dataType : "json",
			type : "post",
			data : search_params,
			success : function(response){
				jQuery("#fav_dir"+id).html('<a class="btn btn-added-favourites " data-toggle="tooltip" title="'+jobboard_data.Added_to_Favorites+'" href="javascript:;" onclick="save_unfavorite('+id+')" ><i class="far fa-star" ></i></a>');
			}
		});
	}
}
function save_unfavorite(id) {
	var isLogged =jobboard_data.current_user_id;
	if (isLogged=="0") {
		alert(jobboard_data.Please_login);
		} else {
		var ajaxurl = jobboard_data.ajaxurl;
		var search_params={
			"action"  : 	"jobboard_save_un_favorite",
			"data": "id=" + id,
			"_wpnonce":  	jobboard_data.contact,
		};
		jQuery.ajax({
			url : ajaxurl,
			dataType : "json",
			type : "post",
			data : search_params,
			success : function(response){
				jQuery("#fav_dir"+id).html('<a class="btn btn-light btn-add-favourites"  data-toggle="tooltip"  title="'+jobboard_data.Add_to_Favorites+'>" href="javascript:;" onclick="save_favorite('+id+')" ><i class="far fa-star"></i></a>');
			}
		});
	}
}
function isValidEmailAddress(emailAddress) {
	var pattern = new RegExp(/^((([a-z]|\d|[!#\$%&"\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&"\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i);
	return pattern.test(emailAddress);
}

