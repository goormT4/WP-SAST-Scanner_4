"use strict";
var ajaxurl = profile_data.ajaxurl;
var loader_image = profile_data.loading_image;



jQuery(window).on('load',function(){
		if (jQuery("#all_fieldsdatatable")[0]){	
		jQuery('#all_fieldsdatatable').show();
		var oTable = jQuery('#all_fieldsdatatable').dataTable({
			"pageLength": 25,			
			"language": {
				"sProcessing": 		profile_data.sProcessing ,
				"sSearch": 			'',
				"searchPlaceholder" : profile_data.sSearch,
				"lengthMenu":		profile_data.lengthMenu ,
				"zeroRecords": 		profile_data.zeroRecords,
				"info": 			profile_data.info,
				"infoEmpty": 		profile_data.infoEmpty,
				"infoFiltered":		profile_data.infoFiltered ,
				
				"oPaginate": {
					"sFirst":   	profile_data.sFirst,
					"sLast":    	profile_data.sLast,
					"sNext":   		profile_data.sNext ,
					"sPrevious":	profile_data.sPrevious,
				},
			},
			}
		);
		
	}
});

function iv_add_field(){		
		var wpdatatable = jQuery('#all_fieldsdatatable').DataTable();		
		var roleall = jQuery('#roleall_0').html();
		var inputtypell= jQuery('#inputtypell_0').html();		
		wpdatatable.row.add( [
            '<input type="text" class="form-control" name="meta_name[]" id="meta_name[]" value="profile-field'+profile_data.pi+'">',
            '<input type="text" class="form-control" name="meta_label[]" id="meta_label[]" value="profile-field'+profile_data.pi+'" >',
            inputtypell,
            '<textarea class="form-control" rows="3" name="field_type_value[]" id="field_type_value[]" ></textarea>',
            roleall,
			'<input type="checkbox" name="signup'+profile_data.pi+'" id="signup'+profile_data.pi+'" value="yes" >',
			'<input type="checkbox" name="srequire'+profile_data.pi+'" id="srequire'+profile_data.pi+'" value="yes"   class="text-center">',
			'<input type="checkbox" name="srequire'+profile_data.pi+'" id="srequire'+profile_data.pi+'" value="yes"   class="text-center">',
		'<button class="btn btn-danger btn-xs" onclick="return iv_remove_field('+profile_data.pi+');">Delete</button>',
        ] ).draw();	
		
	
	profile_data.pi=profile_data.pi+1	
	
	
}
function update_profile_fields(){
	var search_params = {
		"action": 		"jobboard_update_profile_signup_fields",
		"form_data":	jQuery("#profile_fields").serialize(),
		"_wpnonce":  	profile_data.adminnonce,
	};
	jQuery.ajax({
		url: profile_data.ajaxurl,
		dataType: "json",
		type: "post",
		data: search_params,
		success: function(response) {              		
			jQuery('#success_message_profile').html('<div class="alert alert-info alert-dismissable"><a class="panel-close close" data-dismiss="alert">x</a>'+response.code +'.</div>');
			var url = profile_data.redirecturl;    						
			jQuery(location).attr('href',url);	
		}
	});
}

function iv_remove_field(div_id){		
	
	jQuery("#wpdatatablefield_"+div_id).fadeOut(500, function(){ jQuery(this).remove();});
}
function iv_add_menu(){	
	jQuery('#custom_menu_div').append('<div class="row form-group " id="menu_'+profile_data.pii+'"><div class=" col-sm-3"> <input type="text" class="form-control" name="menu_title[]" id="menu_title[]" value="" placeholder="Enter Menu Title "> </div>	<div  class=" col-sm-7"><input type="text" class="form-control" name="menu_link[]" id="menu_link[]" value="" placeholder="Enter Menu Link.  Example  http://www.google.com"></div><div  class=" col-sm-2"><button class="btn btn-danger btn-xs" onclick="return iv_remove_menu('+profile_data.pii+');">Delete</button>');
	profile_data.pii=profile_data.pii+1;		
}
function iv_remove_menu(div_id){		
	jQuery("#menu_"+div_id).remove();
}	