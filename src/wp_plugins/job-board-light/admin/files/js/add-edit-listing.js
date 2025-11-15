"use strict";


jQuery( document ).ready(function() { 
		if(realpro_data.theme_name=='divi'){
			jQuery('.collapse').collapse();
		}
});	
		
function iv_update_post(){
	tinyMCE.triggerSave();	
	var ajaxurl = realpro_data.ajaxurl;
	var loader_image = realpro_data.loading_image;
				jQuery('#update_message').html(loader_image);
				var search_params={
					"action"  : 	"jobboard_update_wp_post",	
					"form_data":	jQuery("#new_post").serialize(), 
					"_wpnonce":  	realpro_data.dirwpnonce,
				};
				jQuery.ajax({					
					url : ajaxurl,					 
					dataType : "json",
					type : "post",
					data : search_params,
					success : function(response){
						if(response.code=='success'){
								var url = realpro_data.permalink+"?&profile=all-post"; 						
								jQuery(location).attr('href',url);	
						}
					
						
					}
				});
	
	}
	
function iv_save_post (){
	tinyMCE.triggerSave();	
	var ajaxurl = realpro_data.ajaxurl;
	var loader_image = realpro_data.loading_image;
				jQuery('#update_message').html(loader_image);
				var search_params={
					"action"  : 	"jobboard_save_wp_post",	
					"form_data":	jQuery("#new_post").serialize(), 
					"_wpnonce":  	realpro_data.dirwpnonce,
				};
				jQuery.ajax({					
					url : ajaxurl,					 
					dataType : "json",
					type : "post",
					data : search_params,
					success : function(response){
						if(response.code=='success'){
								var url = realpro_data.permalink+"?&profile=all-post";    						
								jQuery(location).attr('href',url);	
						}
					
						
					}
				});
	
	}
function add_plan_field(){
	var main_plan_div =jQuery('#plan').html(); 
	jQuery('#plans').append('<div class="clearfix"></div><hr>'+main_plan_div+'');
}
function plan_post_image(planthis){	
				var image_gallery_frame;             
                image_gallery_frame = wp.media.frames.downloadable_file = wp.media({
                    // Set the title of the modal.
                    title: realpro_data.Set_plan_Image,
                    button: {
                        text: realpro_data.Set_plan_Image,
                    },
                    multiple: false,
                    displayUserSettings: true,
                });                
                image_gallery_frame.on( 'select', function() {
                    var selection = image_gallery_frame.state().get('selection');
                    selection.map( function( attachment ) {
                        attachment = attachment.toJSON();
                        if ( attachment.id ) {		
													
							jQuery(planthis).html('<img  class="img-responsive"  src="'+attachment.sizes.thumbnail.url+'"><input type="hidden" name="plan_image_id[]" id="plan_image_id[]" value="'+attachment.id+'">');
							
							
						}
					});                   
                });               
				image_gallery_frame.open(); 				
	}	
function  remove_post_image	(profile_image_id){
	jQuery('#'+profile_image_id).html('');
	jQuery('#feature_image_id').val(''); 
	jQuery('#post_image_edit').html('<button type="button" onclick="edit_post_image(\'post_image_div\');"  class="btn btn-xs green-haze">Add</button>');  

}
function  remove_event_image	(profile_image_id){
	jQuery('#'+profile_image_id).html('');
	jQuery('#event_image_id').val(''); 
	jQuery('#event_image_edit').html('<button type="button" onclick="event_post_image(\'event_image_div\');"  class="btn btn-xs green-haze">Add</button>');  

}
function  remove_deal_image	(profile_image_id){
	jQuery('#'+profile_image_id).html('');
	jQuery('#deal_image_id').val(''); 
	jQuery('#deal_image_edit').html('<button type="button" onclick="deal_post_image(\'deal_image_div\');"  class="btn btn-xs green-haze">Add</button>');  

}	
 function edit_post_image(profile_image_id){	
				var image_gallery_frame;

             
                image_gallery_frame = wp.media.frames.downloadable_file = wp.media({
                    // Set the title of the modal.
                    title: realpro_data.Set_Feature_Image,
                    button: {
                        text: realpro_data.Set_Feature_Image, 
                    },
                    multiple: false,
                    displayUserSettings: true,
                });                
                image_gallery_frame.on( 'select', function() {
                    var selection = image_gallery_frame.state().get('selection');
                    selection.map( function( attachment ) {
                        attachment = attachment.toJSON();
                        if ( attachment.id ) {
							jQuery('#'+profile_image_id).html('<img  class="img-responsive"  src="'+attachment.sizes.thumbnail.url+'">');
							jQuery('#feature_image_id').val(attachment.id ); 
							jQuery('#post_image_edit').html('<button type="button" onclick="remove_post_image(\'post_image_div\');"  class="btn btn-xs green-haze">X</button>');  
						   
						}
					});
                   
                });               
				image_gallery_frame.open(); 
				
	}
function event_post_image(profile_image_id){	
				var image_gallery_frame;

              
                image_gallery_frame = wp.media.frames.downloadable_file = wp.media({
                    // Set the title of the modal.
                    title: realpro_data.Set_Event_Image,
                    button: {
                        text: realpro_data.Set_Event_Image,
                    },
                    multiple: false,
                    displayUserSettings: true,
                });                
                image_gallery_frame.on( 'select', function() {
                    var selection = image_gallery_frame.state().get('selection');
                    selection.map( function( attachment ) {
                        attachment = attachment.toJSON();
                        if ( attachment.id ) {
							jQuery('#'+profile_image_id).html('<img  class="img-responsive"  src="'+attachment.sizes.thumbnail.url+'">');
							jQuery('#event_image_id').val(attachment.id ); 
							jQuery('#event_image_edit').html('<button type="button" onclick="event_post_image(\'event_image_div\');"  class="btn btn-xs green-haze">Edit</button> &nbsp;<button type="button" onclick="remove_event_image(\'event_image_div\');"  class="btn btn-xs green-haze">Remove</button>');  
						   
						}
					});
                   
                });               
				image_gallery_frame.open(); 
				
	}
function deal_post_image(profile_image_id){	
				var image_gallery_frame;

              
                image_gallery_frame = wp.media.frames.downloadable_file = wp.media({
                    // Set the title of the modal.
                    title: realpro_data.Set_plan_Image,
                    button: {
                        text: realpro_data.Set_plan_Image,
                    },
                    multiple: false,
                    displayUserSettings: true,
                });                
                image_gallery_frame.on( 'select', function() {
                    var selection = image_gallery_frame.state().get('selection');
                    selection.map( function( attachment ) {
                        attachment = attachment.toJSON();
                        if ( attachment.id ) {
							jQuery('#'+profile_image_id).html('<img  class="img-responsive"  src="'+attachment.sizes.thumbnail.url+'">');
							jQuery('#deal_image_id').val(attachment.id ); 
							jQuery('#deal_image_edit').html('<button type="button" onclick="deal_post_image(\'deal_image_div\');"  class="btn btn-xs green-haze">Edit</button> &nbsp;<button type="button" onclick="remove_deal_image(\'deal_image_div\');"  class="btn btn-xs green-haze">Remove</button>');  
						   
						}
					});
                   
                });               
				image_gallery_frame.open(); 
				
	}			
 function edit_gallery_image(profile_image_id){
				
				var image_gallery_frame;
				var hidden_field_image_ids = jQuery('#gallery_image_ids').val();
              
                image_gallery_frame = wp.media.frames.downloadable_file = wp.media({
                    // Set the title of the modal.
                    title: realpro_data.Gallery_Images,
                    button: {
                        text: realpro_data.Gallery_Images,
                    },
                    multiple: true,
                    displayUserSettings: true,
                });                
                image_gallery_frame.on( 'select', function() {
                    var selection = image_gallery_frame.state().get('selection');
                    selection.map( function( attachment ) {
                        attachment = attachment.toJSON();
                        console.log(attachment);
                        if ( attachment.id ) {
							jQuery('#'+profile_image_id).append('<div id="gallery_image_div'+attachment.id+'" class="col-md-3"><img  class="img-responsive"  src="'+attachment.sizes.thumbnail.url+'"><button type="button" onclick="remove_gallery_image(\'gallery_image_div'+attachment.id+'\', '+attachment.id+');"  class="btn btn-xs btn-danger">Remove</button> </div>');
							
							hidden_field_image_ids=hidden_field_image_ids+','+attachment.id ;
							jQuery('#gallery_image_ids').val(hidden_field_image_ids); 
							
							
						   
						}
					});
                   
                });               
				image_gallery_frame.open(); 

 }			

function  remove_gallery_image(img_remove_div,rid){	
	var hidden_field_image_ids = jQuery('#gallery_image_ids').val();	
	hidden_field_image_ids =hidden_field_image_ids.replace(rid, '');	
	jQuery('#'+img_remove_div).remove();
	jQuery('#gallery_image_ids').val(hidden_field_image_ids); 
	

}	
function add_public_facilities(){
	var main_opening_div =jQuery('#day-row1').html(); 
	jQuery('#public_facilities_div').append('<div class="clearfix"></div><div class=" row form-group" >'+main_opening_div+'</div>');

}	
jQuery(document).ready(function() {
	jQuery("input[name$='contact_source']").on("click", function (){
		var rvalue = jQuery(this).val();
		
		if(rvalue=='new_value'){jQuery("#new_contact_div" ).show();}
		if(rvalue=='user_info'){jQuery("#new_contact_div" ).hide();}
		
		
	});
});	
function plan_delete_(id_delete){
	
	jQuery('#plan_delete_'+id_delete).remove();
	
}
function remove_facilities(div_id){
	jQuery('#old_facilities'+div_id).remove();
}		