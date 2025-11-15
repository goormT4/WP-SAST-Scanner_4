"use strict";
var ajaxurl = jobboard1.ajaxurl;
var loader_image = jobboard1.loading_image;
jQuery(function(){
	jQuery(".nav-tabs a").on("click", function(){
		jQuery(this).tab('show');
	});
});
function contact_close(){
	jQuery.colorbox.close();
}

jQuery(window).on('load',function(){
	if (jQuery("#deadline")[0]){	
		jQuery( "#deadline" ).datepicker({ dateFormat: 'dd-mm-yy' });
	}
});
jQuery(window).on('load',function(){
	if (jQuery(".epinputdate")[0]){	
		jQuery( ".epinputdate" ).datepicker( );
	}
});

function job_candidate_schedule(h_id){
	
	var search_params={
		"action"  : 	"jobboard_candidate_schedule",
		"form_data": 	jQuery("#candidate-meeting-form").serialize(),
		"_wpnonce":  	jobboard1.dirwpnonce,
	};
	jQuery.ajax({
		url : ajaxurl,
		dataType : "json",
		type : "post",
		data : search_params,
		success : function(response){
			jQuery('#update_message').html('');
			if (response.msg=="success") {	
						
				 if (response.already_meeting=="yes") {
					
				 }else{
					jQuery("#schedulebuttonall"+h_id).addClass("shortlisted");
					jQuery("#schedulebuttonshort"+h_id).addClass("shortlisted");
					jQuery("#scheduledeletedbutton"+h_id).addClass("shortlisted");
					
					var tablemeeting = jQuery('#candidate-meeting').DataTable();	
					if ( jQuery( '#all_'+h_id ).length ) {
						var full_tr =jQuery('#all_'+h_id).html();	
					}
					if ( jQuery( '#shortlisted_'+h_id ).length ) {
						var full_tr =jQuery('#shortlisted_'+h_id).html();	
					}									
					tablemeeting.row.add( [							
							full_tr,
					] ).node().id = 'meeting_'+h_id;
					tablemeeting.draw( false );
				 }
				 jQuery.colorbox.close();
				
				} else {
				alert('Try later');
			}
		}
	});
}

function candidate_shortlisted_delete(h_id,divename){ 
	var search_params={
		"action"  : 	"jobboard_candidate_shortlisted",
		"data": "id="+h_id+"&shortlisted=remove",
		"_wpnonce":  	jobboard1.dirwpnonce,
	};
	jQuery.ajax({
		url : ajaxurl,
		dataType : "json",
		type : "post",
		data : search_params,
		success : function(response){
			jQuery('#update_message').html('');
			if (response.msg=="success") {
				jQuery('#shortlist'+divename+h_id).html('');
				  
					// For all
					var divename2='all';
					jQuery('#shortlistall'+h_id).html('<button class="btn btn-light " data-toggle="tooltip"  onclick="candidate_shortlisted('+ h_id +',\''+ divename2 +'\')" title="'+ jobboard1.makeShortListed +'"><i class="fas fa-user-check"></i></button>');
					
					// For shortlist
					var divename2='only';
					jQuery('#shortlistonly'+h_id).html('<button class="btn btn-light " data-toggle="tooltip"  onclick="candidate_shortlisted('+ h_id +',\''+ divename2 +'\')" title="'+ jobboard1.makeShortListed +'"><i class="fas fa-user-check"></i></button>');
					
					// For Meeting  
					var divename2='meeting';
					jQuery('#shortlistmeeting'+h_id).html('<button class="btn btn-light " data-toggle="tooltip"  onclick="candidate_shortlisted('+ h_id +',\''+ divename2 +'\')" title="'+ jobboard1.makeShortListed +'"><i class="fas fa-user-check"></i></button>');
					
					
					if (jQuery("#shortlisted_"+h_id)[0]){
						jQuery('#shortlist'+divename+h_id).html('<button class="btn btn-light " data-toggle="tooltip"  onclick="candidate_shortlisted('+ h_id +',\''+ divename +'\')" title="'+ jobboard1.makeShortListed +'"><i class="fas fa-user-check"></i></button>');
					}
					
				  	if (jQuery("#shortlisted_"+h_id)[0]){
						jQuery("#shortlisted_"+h_id).fadeOut(500, function(){ jQuery(this).remove();});
					}
					
					
				} else {
					alert('Try later');
			}
		}
	});
	//shortlisted class
}
function candidate_shortlisted(h_id,divename){ 
	var search_params={
		"action"  : 	"jobboard_candidate_shortlisted",
		"data": "id="+h_id,
		"_wpnonce":  	jobboard1.dirwpnonce,
	};
	jQuery.ajax({
		url : ajaxurl,
		dataType : "json",
		type : "post",
		data : search_params,
		success : function(response){
			jQuery('#update_message').html('');
			if (response.msg=="success") {				  
					
					 
					 // For all
					var divename2='all';
					jQuery('#shortlistall'+h_id).html('<button class="btn btn-light shortlisted" data-toggle="tooltip"  onclick="candidate_shortlisted_delete('+ h_id +',\''+ divename2 +'\')" title="'+ jobboard1.ShortListed +'"><i class="fas fa-user-check"></i></button>');
					
					// For shortlist
					var divename2='only';
					jQuery('#shortlistonly'+h_id).html('<button class="btn btn-light shortlisted" data-toggle="tooltip"  onclick="candidate_shortlisted_delete('+ h_id +',\''+ divename2 +'\')" title="'+ jobboard1.ShortListed +'"><i class="fas fa-user-check"></i></button>');
					
					// For Meeting  
					var divename2='meeting';
					jQuery('#shortlistmeeting'+h_id).html('<button class="btn btn-light shortlisted" data-toggle="tooltip"  onclick="candidate_shortlisted_delete('+ h_id +',\''+ divename2 +'\')" title="'+ jobboard1.ShortListed +'"><i class="fas fa-user-check"></i></button>');
					 
					 
					 
					    var t = jQuery('#candidates-shortlisted').DataTable();						
						var full_tr =jQuery('#'+divename+'_'+h_id).html();	
						t.row.add( [							
								full_tr,
						] ).node().id = 'shortlisted_'+h_id;
					t.draw( false );
				} else {
				alert('Try later');
			}
		}
	});
}
function candidate_reject_delete(h_id,divename){	
	var search_params={
		"action"  : 	"jobboard_candidate_reject",
		"data": "id="+h_id+"&reject=remove",
		"_wpnonce":  	jobboard1.dirwpnonce,
	};
	jQuery.ajax({
		url : ajaxurl,
		dataType : "json",
		type : "post",
		data : search_params,
		success : function(response){
			jQuery('#update_message').html('');
			if (response.msg=="success") { 
				 
				 jQuery('#rejectall'+h_id).html('<button class="btn btn-light" data-toggle="tooltip"  onclick="candidate_reject('+ h_id +',\''+ divename +'\')" title="'+ jobboard1.MakeReject +'"><i class="fas fa-user-times"></i></button>');
				 
				 if (jQuery("#deleted_"+h_id)[0]){
					jQuery("#deleted_"+h_id).fadeOut(500, function(){ jQuery(this).remove();});
				 }
				 
				} else {
				alert('Try later');
			}
		}
	});
}
function candidate_reject(h_id,divename){	
	var search_params={
		"action"  : 	"jobboard_candidate_reject",
		"data": "id="+h_id,
		"_wpnonce":  	jobboard1.dirwpnonce,
	};
	jQuery.ajax({
		url : ajaxurl,
		dataType : "json",
		type : "post",
		data : search_params,
		success : function(response){
			jQuery('#update_message').html('');
			if (response.msg=="success") {
						
				 var divename2='all';
				 jQuery('#rejectall'+h_id).html('<button class="btn btn-light shortlisted" data-toggle="tooltip"  onclick="candidate_reject_delete('+ h_id +',\''+ divename2 +'\')" title="'+ jobboard1.Rejected +'"><i class="fas fa-user-times"></i></button>');
				 // Add reject table
												
						var t = jQuery('#candidatedeletedtable').DataTable();						
						var full_tr =jQuery('#'+divename+'_'+h_id).html();	
						t.row.add( [							
								full_tr,
						] ).node().id = 'deleted_'+h_id;
						t.draw( false );
				 
				 // Remove Shortlist & Meeting table
						if (jQuery("#meeting_"+h_id)[0]){
							jQuery("#meeting_"+h_id).fadeOut(500, function(){ jQuery(this).remove();});
						}	
						if (jQuery("#shortlisted_"+h_id)[0]){
							jQuery("#shortlisted_"+h_id).fadeOut(500, function(){ jQuery(this).remove();});
						}
				 
				  
				 
				 
				} else {
				alert('Try later');
			}
		}
	});
}
function job_applied_delete_myaccount(h_id,divename){		
	var search_params={
		"action" 	: 	"jobboard_applied_delete",
		"data"	 	: 	"id="+h_id,
		"_wpnonce"	: jobboard1.contact,
	};
	jQuery.ajax({
		url : ajaxurl,
		dataType : "json",
		type : "post",
		data : search_params,
		success : function(response){
			jQuery('#update_message').html('');
			if (response.msg=="success") {
				jQuery("#"+divename+"_"+h_id).fadeOut(500, function(){ jQuery(this).remove();});
				} else {
				alert('Try later');
			}
		}
	});
}
function job_bookmark_delete_myaccount(h_id,divename){		
	var search_params={
		"action" 	: 	"jobboard_save_un_favorite",
		"data"	 	: 	"id="+h_id,
		"_wpnonce"	: jobboard1.contact,
	};
	jQuery.ajax({
		url : ajaxurl,
		dataType : "json",
		type : "post",
		data : search_params,
		success : function(response){
			jQuery('#update_message').html('');
			if (response.msg=="success") {
				jQuery("#"+divename+"_"+h_id).fadeOut(500, function(){ jQuery(this).remove();});
				} else {
				alert('Try later');
			}
		}
	});
}
function delete_message_myaccount(h_id,divename){		
	var search_params={
		"action" 	: 	"jobboard_message_delete",
		"data"	 	: 	"id="+h_id,
		"_wpnonce"	: jobboard1.dirwpnonce,
	};
	jQuery.ajax({
		url : ajaxurl,
		dataType : "json",
		type : "post",
		data : search_params,
		success : function(response){
			jQuery('#update_message').html('');
			if (response.msg=="success") {
				jQuery("#"+divename+"_"+h_id).fadeOut(500, function(){ jQuery(this).remove();});
				} else {
				alert('Try later');
			}
		}
	});
}
function company_bookmark_delete_myaccount(h_id,divename){		
	var search_params={
		"action" 	: 	"jobboard_employer_bookmark_delete",
		"data"	 	: 	"id="+h_id,
		"_wpnonce"	: jobboard1.dirwpnonce,
	};
	jQuery.ajax({
		url : ajaxurl,
		dataType : "json",
		type : "post",
		data : search_params,
		success : function(response){
			jQuery('#update_message').html('');
			if (response.msg=="success") {
				jQuery("#"+divename+"_"+h_id).fadeOut(500, function(){ jQuery(this).remove();});
				} else {
				alert('Try later');
			}
		}
	});
}
function candidate_bookmark_delete_myaccount(h_id,divename){	
	
	var search_params={
		"action" 	: 	"jobboard_profile_bookmark_delete",
		"data"	 	: 	"id="+h_id,
		"_wpnonce"	: jobboard1.dirwpnonce,
	};
	jQuery.ajax({
		url : ajaxurl,
		dataType : "json",
		type : "post",
		data : search_params,
		success : function(response){
			jQuery('#update_message').html('');
			if (response.msg=="success") {
				jQuery("#"+divename+"_"+h_id).fadeOut(500, function(){ jQuery(this).remove();});
				} else {
				alert('Try later');
			}
		}
	});
}
function candidate_delete(h_id,divename){	
	var search_params={
		"action" 	: 	"jobboard_candidate_delete",
		"data"	 	: 	"id="+h_id,
		"_wpnonce"	: jobboard1.dirwpnonce,
	};
	jQuery.ajax({
		url : ajaxurl,
		dataType : "json",
		type : "post",
		data : search_params,
		success : function(response){
			jQuery('#update_message').html('');
			if (response.msg=="success") {
				jQuery("#"+divename+"_"+h_id).fadeOut(500, function(){ jQuery(this).remove();});
				
				} else {
					alert('Try later');
			}
		}
	});
}

function candidate_email_popup(user_id){	
	var contactform =jobboard1.ajaxurl+'?action=jobboard_candidate_email_popup&user_id='+user_id;
	jQuery.colorbox({href: contactform,opacity:"0.70",closeButton:false,});
}
function job_email_popup(dir_id){ 
	
	var contactform =jobboard1.ajaxurl+'?action=jobboard_listing_contact_popup&dir_id='+dir_id;
	
	jQuery.colorbox({href: contactform,opacity:"0.70",closeButton:false,})
}
function candidate_meeting_popup(user_id){	
	var contactform =jobboard1.ajaxurl+'?action=jobboard_candidate_meeting_popup&user_id='+user_id;
	jQuery.colorbox({href: contactform,opacity:"0.70",closeButton:false,
				onComplete:function(){		
				//DATEPICKER
				jQuery('#meeting_date').datetimepicker();				
			},
		
		});	
}

jQuery(document).ready(function($) {
if (jQuery("#candidate-meeting")[0]){
		jQuery('#candidate-meeting').show();
		var oTablecandidate = jQuery('#candidate-meeting').dataTable({
			"language": {
				"sProcessing": 		jobboard1.sProcessing ,
				"sSearch": 			'',
				"searchPlaceholder" : jobboard1.sSearch,
				"lengthMenu":		jobboard1.lengthMenu ,
				"zeroRecords": 		jobboard1.zeroRecords,
				"info": 			jobboard1.info,
				"infoEmpty": 		jobboard1.infoEmpty,
				"infoFiltered":		jobboard1.infoFiltered ,
				
				"oPaginate": {
					"sFirst":   	jobboard1.sFirst,
					"sLast":    	jobboard1.sLast,
					"sNext":   		jobboard1.sNext ,
					"sPrevious":	jobboard1.sPrevious,
				},
			}
			
		});
		
	}
});

jQuery(document).ready(function($) {
if (jQuery("#candidatedeletedtable")[0]){
		jQuery('#candidatedeletedtable').show();
		var oTablecandidate = jQuery('#candidatedeletedtable').dataTable({
			"language": {
				"sProcessing": 		jobboard1.sProcessing ,
				"sSearch": 			'',
				"searchPlaceholder" : jobboard1.sSearch,
				"lengthMenu":		jobboard1.lengthMenu ,
				"zeroRecords": 		jobboard1.zeroRecords,
				"info": 			jobboard1.info,
				"infoEmpty": 		jobboard1.infoEmpty,
				"infoFiltered":		jobboard1.infoFiltered ,
				
				"oPaginate": {
					"sFirst":   	jobboard1.sFirst,
					"sLast":    	jobboard1.sLast,
					"sNext":   		jobboard1.sNext ,
					"sPrevious":	jobboard1.sPrevious,
				},
			}
			
		});
		
	}
});
jQuery(document).ready(function($) {
if (jQuery("#candidates-shortlisted")[0]){
		jQuery('#candidates-shortlisted').show();
		var oTablecandidate = jQuery('#candidates-shortlisted').DataTable({
			"language": {
				"sProcessing": 		jobboard1.sProcessing ,
				"sSearch": 			'',
				"searchPlaceholder" : jobboard1.sSearch,
				"lengthMenu":		jobboard1.lengthMenu ,
				"zeroRecords": 		jobboard1.zeroRecords,
				"info": 			jobboard1.info,
				"infoEmpty": 		jobboard1.infoEmpty,
				"infoFiltered":		jobboard1.infoFiltered ,
				
				"oPaginate": {
					"sFirst":   	jobboard1.sFirst,
					"sLast":    	jobboard1.sLast,
					"sNext":   		jobboard1.sNext ,
					"sPrevious":	jobboard1.sPrevious,
				},
			}
			
		});
		
	}
});


jQuery(document).ready(function($) {
if (jQuery("#candidate-manageall")[0]){	
	jQuery('#candidate-manageall').show();
		var oTablecandidate = jQuery('#candidate-manageall').dataTable({	
			"language": {
				"sProcessing": 		jobboard1.sProcessing ,
				"sSearch": 			'',
				"searchPlaceholder" : jobboard1.sSearch,
				"lengthMenu":		jobboard1.lengthMenu ,
				"zeroRecords": 		jobboard1.zeroRecords,
				"info": 			jobboard1.info,
				"infoEmpty": 		jobboard1.infoEmpty,
				"infoFiltered":		jobboard1.infoFiltered ,	
				
				"oPaginate": {
					"sFirst":   	jobboard1.sFirst,
					"sLast":    	jobboard1.sLast,
					"sNext":   		jobboard1.sNext ,
					"sPrevious":	jobboard1.sPrevious,
				},
			},
		
			
			"columnDefs": [
            {
                "targets": [ 0 ],
                "visible": false,
                "searchable": false

            },			
			],
		});
		oTablecandidate.fnSort( [ [0,'DESC'] ] );			
		jQuery('#dropdown1').on('change', function () {				
			oTablecandidate.fnFilter(jQuery('#dropdown1').val());			
              
		} );
	}
});

jQuery(document).ready(function($) {
	if (jQuery("#candidate-bookmark")[0]){
		jQuery('#candidate-bookmark').show();
		var oTablecandidate = jQuery('#candidate-bookmark').dataTable({
			"language": {
				"sProcessing": 		jobboard1.sProcessing ,
				"sSearch": 			'',
				"searchPlaceholder" : jobboard1.sSearch,
				"lengthMenu":		jobboard1.lengthMenu ,
				"zeroRecords": 		jobboard1.zeroRecords,
				"info": 			jobboard1.info,
				"infoEmpty": 		jobboard1.infoEmpty,
				"infoFiltered":		jobboard1.infoFiltered ,
				
				"oPaginate": {
					"sFirst":   	jobboard1.sFirst,
					"sLast":    	jobboard1.sLast,
					"sNext":   		jobboard1.sNext ,
					"sPrevious":	jobboard1.sPrevious,
				},
			}
			
		});
		//oTablecandidate.fnSort( [ [1,'DESC'] ] );
	}
});
jQuery(document).ready(function($) {
	if (jQuery("#job-manage")[0]){
		jQuery('#job-manage').show();
		var oTable2 = jQuery('#job-manage').dataTable({
			"language": {
				"sProcessing": 		jobboard1.sProcessing ,
				"sSearch": 			'',
				"searchPlaceholder" : jobboard1.sSearch,
				"lengthMenu":			jobboard1.lengthMenu ,
				"zeroRecords": 		jobboard1.zeroRecords,
				"info": 					jobboard1.info,
				"infoEmpty": 			jobboard1.infoEmpty,
				"infoFiltered":		jobboard1.infoFiltered ,
				"oPaginate": {
					"sFirst":   	jobboard1.sFirst,
					"sLast":    	jobboard1.sLast,
					"sNext":   		jobboard1.sNext ,
					"sPrevious":	jobboard1.sPrevious,
				},
			}
		});
		oTable2.fnSort( [ [0,'DESC'] ] );
	}
});
jQuery(document).ready(function($) {
	if (jQuery("#candidates-manage")[0]){
		jQuery('#candidates-manage').show();
		var oTable2 = jQuery('#candidates-manage').dataTable({
			"language": {
				"sProcessing": 		jobboard1.sProcessing ,
				"sSearch": 			'',
				"searchPlaceholder" : jobboard1.sSearch,
				"lengthMenu":			jobboard1.lengthMenu ,
				"zeroRecords": 		jobboard1.zeroRecords,
				"info": 					jobboard1.info,
				"infoEmpty": 			jobboard1.infoEmpty,
				"infoFiltered":		jobboard1.infoFiltered ,
				"oPaginate": {
					"sFirst":   	jobboard1.sFirst,
					"sLast":    	jobboard1.sLast,
					"sNext":   		jobboard1.sNext ,
					"sPrevious":	jobboard1.sPrevious,
				},
			}
		});
		oTable2.fnSort( [ [1,'DESC'] ] );
	}
});
jQuery(document).ready(function($) {
	if (jQuery("#candidates-manage-mobile")[0]){
		jQuery('#candidates-manage-mobile').show();
		var oTable2 = jQuery('#candidates-manage-mobile').dataTable({
			"language": {
				"sProcessing": 		jobboard1.sProcessing ,
				"sSearch": 			'',
				"searchPlaceholder" : jobboard1.sSearch,
				"lengthMenu":			jobboard1.lengthMenu ,
				"zeroRecords": 		jobboard1.zeroRecords,
				"info": 					jobboard1.info,
				"infoEmpty": 			jobboard1.infoEmpty,
				"infoFiltered":		jobboard1.infoFiltered ,
				"oPaginate": {
					"sFirst":   	jobboard1.sFirst,
					"sLast":    	jobboard1.sLast,
					"sNext":   		jobboard1.sNext ,
					"sPrevious":	jobboard1.sPrevious,
				},
			}
		});
		oTable2.fnSort( [ [1,'DESC'] ] );
	}
});
jQuery(document).ready(function($) {
	if (jQuery("#tbl-epmplyer-bookmark")[0]){
		jQuery('#tbl-epmplyer-bookmark').show();
		var oTable2 = jQuery('.tbl-epmplyer-bookmark').dataTable({
			"language": {
				"sProcessing": 		jobboard1.sProcessing ,
				"sSearch": 			'',
				"searchPlaceholder" : jobboard1.sSearch,
				"lengthMenu":			jobboard1.lengthMenu ,
				"zeroRecords": 		jobboard1.zeroRecords,
				"info": 					jobboard1.info,
				"infoEmpty": 			jobboard1.infoEmpty,
				"infoFiltered":		jobboard1.infoFiltered ,
				"oPaginate": {
					"sFirst":   	jobboard1.sFirst,
					"sLast":    	jobboard1.sLast,
					"sNext":   		jobboard1.sNext ,
					"sPrevious":	jobboard1.sPrevious,
				},
			}
		});
		oTable2.fnSort( [ [1,'DESC'] ] );
	}
});
jQuery(document).ready(function($) {
	if (jQuery("#tbl-job-bookmark")[0]){
		jQuery('#tbl-job-bookmark').show();
		var oTable2 = jQuery('.tbl-job-bookmark').dataTable({
			"language": {
				"sProcessing": 		jobboard1.sProcessing ,
				"sSearch": 			'',
				"searchPlaceholder" : jobboard1.sSearch,
				"lengthMenu":			jobboard1.lengthMenu ,
				"zeroRecords": 		jobboard1.zeroRecords,
				"info": 					jobboard1.info,
				"infoEmpty": 			jobboard1.infoEmpty,
				"infoFiltered":		jobboard1.infoFiltered ,
				"oPaginate": {
					"sFirst":   	jobboard1.sFirst,
					"sLast":    	jobboard1.sLast,
					"sNext":   		jobboard1.sNext ,
					"sPrevious":	jobboard1.sPrevious,
				},
			}
		});
		oTable2.fnSort( [ [1,'DESC'] ] );
	}
});
jQuery(document).ready(function($) {
	if (jQuery("#alllistingdata")[0]){
		jQuery('#alllistingdata').show();
		var oTable2 = jQuery('#alllistingdata').dataTable({
			"language": {
				"sProcessing": 		jobboard1.sProcessing ,
				"sSearch": 			'',
				"searchPlaceholder" : jobboard1.sSearch,
				"lengthMenu":			jobboard1.lengthMenu ,
				"zeroRecords": 		jobboard1.zeroRecords,
				"info": 					jobboard1.info,
				"infoEmpty": 			jobboard1.infoEmpty,
				"infoFiltered":		jobboard1.infoFiltered ,
				"oPaginate": {
					"sFirst":   	jobboard1.sFirst,
					"sLast":    	jobboard1.sLast,
					"sNext":   		jobboard1.sNext ,
					"sPrevious":	jobboard1.sPrevious,
				},
			}
		});
		oTable2.fnSort( [ [1,'DESC'] ] );
	}
});
jQuery(document).ready(function($) {
	if (jQuery("#interest-user-data")[0]){
		jQuery(window).on('load',function(){
			jQuery('#interest-user-data').show();
			var oTable = jQuery('#interest-user-data').dataTable();
			oTable.fnSort( [ [1,'DESC'] ] );
		});
	}
	if (jQuery(".popup-contact")[0]){
		jQuery(".popup-contact").colorbox({transition:"None", width:"50%", height:"50%" ,opacity:"0.70"});
	}
});
jQuery(document).ready(function($) {
	jQuery('[href^=#tab]').on("click", function (e) {
		e.preventDefault()
		jQuery(this).tab('show')
	});
})
function edit_profile_image(profile_image_id){
	var image_gallery_frame;
	image_gallery_frame = wp.media.frames.downloadable_file = wp.media({
		// Set the title of the modal.
		title: jobboard1.SetImage	,
		button: {
			text: jobboard1.SetImage,
		},
		multiple: false,
		displayUserSettings: true,
	});
	image_gallery_frame.on( 'select', function() {
		var selection = image_gallery_frame.state().get('selection');
		selection.map( function( attachment ) {
			attachment = attachment.toJSON();
			if ( attachment.id ) {
				console.log(attachment.url);
				var ajaxurl = jobboard1.ajaxurl;
				var search_params = {
					"action": 	"jobboard_update_profile_pic",
					"attachment_thum": attachment.url,
					"profile_pic_url_1": attachment.url,
					"_wpnonce":  	jobboard1.dirwpnonce,
				};
				jQuery.ajax({
					url: ajaxurl,
					dataType: "json",
					type: "post",
					data: search_params,
					success: function(response) {
						if(response=='success'){
							jQuery('#'+profile_image_id).html('<img  class="img-circle img-responsive"  src="'+attachment.sizes.thumbnail.url+'">');
						}
					}
				});
			}
		});
	});
	image_gallery_frame.open();
}
function update_profile_setting (){
	if(typeof(tinyMCE) != "undefined") {
		tinyMCE.triggerSave();	
	}
	
	var ajaxurl =jobboard1.ajaxurl;
	var loader_image = jobboard1.loading_image;
	jQuery('#update_message').html(loader_image);
	var search_params={
		"action"  : 	"jobboard_update_profile_setting",
		"form_data":	jQuery("#profile_setting_form").serialize(),
		"_wpnonce":  	jobboard1.dirwpnonce,
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
function iv_update_hide_setting (){
	var ajaxurl =jobboard1.ajaxurl;
	var loader_image = jobboard1.loading_image;
	jQuery('#update_message_hide').html(loader_image);
	var search_params={
		"action"  : 	"jobboard_update_setting_hide",
		"form_data":	jQuery("#setting_hide_form").serialize(),
		"_wpnonce":  	jobboard1.dirwpnonce,
	};
	jQuery.ajax({
		url : ajaxurl,
		dataType : "json",
		type : "post",
		data : search_params,
		success : function(response){
			jQuery('#update_message_hide').html('<div class="alert alert-info alert-dismissable"><a class="panel-close close" data-dismiss="alert">x</a>'+response.msg +'.</div>');
		}
	});
}
function iv_update_fb (){
	var ajaxurl =jobboard1.ajaxurl;
	var loader_image = jobboard1.loading_image;
	jQuery('#update_message_fb').html(loader_image);
	var search_params={
		"action"  : 	"jobboard_update_setting_fb",
		"form_data":	jQuery("#setting_fb").serialize(),
		"_wpnonce":  	jobboard1.dirwpnonce,
	};
	jQuery.ajax({
		url : ajaxurl,
		dataType : "json",
		type : "post",
		data : search_params,
		success : function(response){
			jQuery('#update_message_fb').html('<div class="alert alert-info alert-dismissable"><a class="panel-close close" data-dismiss="alert">x</a>'+response.msg +'.</div>');
		}
	});
}
function iv_update_password (){
	var ajaxurl =jobboard1.ajaxurl;
	var loader_image = jobboard1.loading_image;
	jQuery('#update_message_pass').html(loader_image);
	var search_params={
		"action"  : 	"jobboard_update_setting_password",
		"form_data":	jQuery("#pass_word").serialize(),
		"_wpnonce":  	jobboard1.dirwpnonce,
	};
	jQuery.ajax({
		url : ajaxurl,
		dataType : "json",
		type : "post",
		data : search_params,
		success : function(response){
			jQuery('#update_message_pass').html('<div class="alert alert-info alert-dismissable"><a class="panel-close close" data-dismiss="alert">x</a>'+response.msg +'.</div>');
		}
	});
}

function send_message_iv(){
	if (jQuery.trim(jQuery("#message-content").val()) == "") {
		alert("Please put your message");
		} else {
		var ajaxurl =jobboard1.ajaxurl;
		var loader_image = jobboard1.loading_image;
		jQuery('#update_message_popup').html(loader_image);
		var search_params={
			"action"  : 	"jobboard_message_send",
			"form_data":	jQuery("#message-pop").serialize(),
			"_wpnonce":  	jobboard1.contact,
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
function iv_save_notification(){ 
		var ajaxurl =jobboard1.ajaxurl;
		var loader_image = jobboard1.loading_image;
		jQuery('#notification_message').html(loader_image);
		var search_params={
			"action"  : 	"jobboard_save_notification",
			"form_data":	jQuery("#nofification_form").serialize(),
			"_wpnonce":  	jobboard1.contact,
		};
		jQuery.ajax({
			url : ajaxurl,
			dataType : "json",
			type : "post",
			data : search_params,
			success : function(response){
				jQuery('#notification_message').html('<div class="alert alert-info alert-dismissable"><a class="panel-close close" data-dismiss="alert">x</a>'+response.msg +'.</div>');
			}
		});
}


function iv_cancel_membership_paypal (){
	if (confirm('Are you sure to cancel this Membership?')) {
		var ajaxurl =jobboard1.ajaxurl;
		var loader_image = jobboard1.loading_image;
		jQuery('#update_message_paypal').html(loader_image);
		var search_params={
			"action"  : 	"jobboard_cancel_paypal",
			"form_data":	jQuery("#paypal_cancel_form").serialize(),
			"_wpnonce":  	jobboard1.dirwpnonce,
		};
		jQuery.ajax({
			url : ajaxurl,
			dataType : "json",
			type : "post",
			data : search_params,
			success : function(response){
				if(response.code=='success'){
					jQuery('#paypal_cancel_div').hide();
					jQuery('#update_message_paypal').html('<div class="alert alert-info alert-dismissable"><a class="panel-close close" data-dismiss="alert">x</a>'+response.msg +'.</div>');
					}else{
					jQuery('#update_message_paypal').html('<div class="alert alert-info alert-dismissable"><a class="panel-close close" data-dismiss="alert">x</a>'+response.msg +'.</div>');
				}
			}
		});
	}
}
function iv_cancel_membership_stripe (){
	if (confirm('Are you sure to cancel this Membership?')) {
		var ajaxurl =jobboard1.ajaxurl;
		var loader_image = jobboard1.loading_image;
		jQuery('#update_message_stripe').html(loader_image);
		var search_params={
			"action"  : 	"jobboard_cancel_stripe",
			"form_data":	jQuery("#profile_cancel_form").serialize(),
			"_wpnonce":  	jobboard1.dirwpnonce,
		};
		jQuery.ajax({
			url : ajaxurl,
			dataType : "json",
			type : "post",
			data : search_params,
			success : function(response){
				jQuery('#stripe_cancel_div').hide();
				jQuery('#update_message_stripe').html('<div class="alert alert-info alert-dismissable"><a class="panel-close close" data-dismiss="alert">x</a>'+response.msg +'.</div>');
			}
		});
	}
}
jQuery(function(){
	jQuery('#package_sel').on('change', function (e) {
		var optionSelected = jQuery("option:selected", this);
		var pack_id = this.value;
		jQuery("#package_id").val(pack_id);
		var ajaxurl =jobboard1.ajaxurl;
		var loader_image = jobboard1.loading_image;
		var search_params={
			"action"  			: "jobboard_check_package_amount",
			"coupon_code" 		:jQuery("#coupon_name").val(),
			"package_id" 		: pack_id,
			"package_amount" 	:'',
			"api_currency" 		:jobboard1.currencyCode,
			"_wpnonce":  	 jobboard1.signup,
		};
		jQuery.ajax({
			url : ajaxurl,
			dataType : "json",
			type : "post",
			data : search_params,
			success : function(response){
				jQuery('#p_amount').html(response.p_amount);
			}
		});
	});
});