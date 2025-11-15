"use strict";
var active_payment_gateway= dirpro_data.iv_gateway; 


(function($) {
			var select = jQuery(".card-expiry-year"),
			year = new Date().getFullYear();
			for (var i = 0; i < 12; i++) {
				select.append(jQuery("<option value='"+(i + year)+"' "+(i === 0 ? "selected" : "")+">"+(i + year)+"</option>"))
			}
})(jQuery);
jQuery(window).on('load',function(){
	if (jQuery(".epinputdate")[0]){	
		jQuery( ".epinputdate" ).datepicker( );
	}
});

function epluginrecaptchaSubmit(token){
			
		
	var formc = jQuery("#jobboard_registration");
	if (jQuery.trim(jQuery("#iv_member_user_name",formc).val()) == "" || jQuery.trim(jQuery("#iv_member_email",formc).val()) == "" || jQuery.trim(jQuery("#iv_member_password",formc).val()) == "") {	
		jQuery("#errormessage").show();
		jQuery("#errormessage").html(dirpro_data.errormessage);
	}else{
		jQuery("#loading-3").show();				
		jQuery("#loading").html(dirpro_data.loader_image);	
		
		if( dirpro_data.iv_gateway=='stripe'){
			Stripe.createToken({						
				number: jQuery('#card_number').val(),							
				cvc: jQuery('#card_cvc').val(),							
				exp_month: jQuery('#card_month').val(),							
				exp_year: jQuery('#card_year').val(),
			
			}, stripeResponseHandler);
						
		}else{ /* Else for paypal */
				
				jQuery('#jobboard_registration').submit();			
									
		}
	
	}
	
}

(function($) {	

	jQuery(document).ready(function($) {
			
						jQuery.validate({
							form : '#jobboard_registration',
							modules : 'security',		
												
							onSuccess : function() {
							
									jQuery("#loading-3").show();
									jQuery("#loading").html(dirpro_data.loader_image);
									
									if(active_payment_gateway=='stripe'){									
											 Stripe.createToken({
												number: jQuery('#card_number').val(),
												cvc: jQuery('#card_cvc').val(),
												exp_month: jQuery('#card_month').val(),
												exp_year: jQuery('#card_year').val(),
												
											}, stripeResponseHandler);
										
										return false;
										
									}else{ // Else for paypal
										
										return true; // false Will stop the submission of the form
									}
								
							},
							
					  })
 
	 })
	 
	 
	 // this identifies your website in the createToken call below	
	 		if(dirpro_data.iv_gateway=='stripe'){
				Stripe.setPublishableKey(dirpro_data.stripe_publishable);
			}
			function stripeResponseHandler(status, response) {
				if (response.error) {				
					jQuery("#payment-errors").html('<div class="alert alert-info alert-dismissable"><a class="panel-close close" data-dismiss="alert">x</a>'+response.error.message +'.</div> ');
					jQuery("#loading-3").hide();
					
					
				} else {
					var form$ = jQuery("#jobboard_registration");
					// token contains id, last4, and card type
					var token = response['id'];
					// insert the token into the form so it gets submitted to the server
					form$.append("<input type='hidden' name='stripeToken' value='" + token + "' />");
					// and submit
					form$.get(0).submit();
				}
			}
})(jQuery);	
jQuery(document).ready(function() {
    jQuery('#coupon_name').on('keyup change', function() {				
		var ajaxurl =dirpro_data.ajaxurl;
		var search_params={
			"action"  				: "jobboard_check_coupon",	
			"coupon_code" 		:	jQuery("#coupon_name").val(),
			"package_id" 			: jQuery("#package_id").val(),
			"package_amount" 	: dirpro_data.package_amount,
			"api_currency" 		: dirpro_data.api_currency,
			"_wpnonce"				: dirpro_data.dirwpnonce,
			
		};
		jQuery('#coupon-result').html(dirpro_data.loader_image2);
		jQuery.ajax({					
			url : ajaxurl,					 
			dataType : "json",
			type : "post",
			data : search_params,
			success : function(response){
				if(response.code=='success'){							
					jQuery('#coupon-result').html(dirpro_data.right_icon);							
					
				}else{
					jQuery('#coupon-result').html(dirpro_data.wrong_16x16);
				}
				
				jQuery('#total').html('<label class="control-label">'+response.gtotal +'</label>');
				jQuery('#discount').html('<label class="control-label">'+response.dis_amount +'</label>');
			}
		});
	});
});

jQuery(function(){	
	jQuery('#package_sel').on('change', function (e) {
		var optionSelected = jQuery("option:selected", this);
		var pack_id = this.value;
		
		jQuery("#package_id").val(pack_id);
								
		var ajaxurl = dirpro_data.ajaxurl;
		var search_params={
		"action"  			: "jobboard_check_package_amount",	
		"coupon_code" 		:jQuery("#coupon_name").val(),
		"package_id" 		: pack_id,
		"package_amount" 	:dirpro_data.package_amount,
		"api_currency" 		:dirpro_data.api_currency,
		"_wpnonce"				: dirpro_data.signup,
		};
		jQuery.ajax({					
			url : ajaxurl,					 
			dataType : "json",
			type : "post",
			data : search_params,
			success : function(response){
				if(response.code=='success'){							
					jQuery('#coupon-result').html(dirpro_data.right_icon);
				}else{
						jQuery('#coupon-result').html(dirpro_data.wrong_16x16);
				}
				jQuery('#p_amount').html(response.p_amount);							
				jQuery('#total').html(response.gtotal);
				jQuery('#discount').html(response.dis_amount);
			}
			});
		});	
	});	

function show_coupon(){
				jQuery("#coupon-div").show();
                 jQuery("#show_hide_div").html('<label for="text" class="col-md-4 col-xs-4 col-sm-4 control-label"></label><div class="col-md-8 col-xs-8 col-sm-8 " ><button type="button" onclick="hide_coupon();"  class="btn btn-default center">'+dirpro_data.HideCoupon+'</button></div>');
}
function hide_coupon(){
				 jQuery("#coupon-div").hide();
                 jQuery("#show_hide_div").html('<label for="text" class="col-md-4 col-xs-4 col-sm-4 control-label"></label><div class="col-md-8 col-xs-8 col-sm-8 " ><button type="button" onclick="show_coupon();"  class="btn btn-default center">'+dirpro_data.Havecoupon+'</button></div>');
}