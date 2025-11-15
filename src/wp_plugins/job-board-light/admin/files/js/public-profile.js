 "use strict";
jQuery(document).ready(function(){
	 jQuery('#candidatebookmark').on('click', function(e){ 
	   var isLogged =jobboard1.current_user_id;
	   var p_id =jQuery("#profileID").val();
	   
		if (isLogged=="0") {
			alert(jobboard1.Please_login);
		} else {
			  if(jQuery('#candidatebookmark').hasClass('btn-added-favourites')){
				var search_params={
						"action" 	: 	"jobboard_profile_bookmark_delete",
						"data"	 	: 	"id="+p_id,
						"_wpnonce"	: jobboard1.dirwpnonce,
					};
					jQuery.ajax({
						url : jobboard1.ajaxurl,
						dataType : "json",
						type : "post",
						data : search_params,
						success : function(response){						
							if (response.msg=="success") {
								jQuery("#candidatebookmark").removeClass('btn-added-favourites').addClass('btn-light btn-add-favourites');
								jQuery('#candidatebookmark').prop('title', jobboard1.Add_to_Boobmark);		
							}
						}
					});
							
			  }else if(jQuery('#candidatebookmark').hasClass('btn-add-favourites')){			
						
					var search_params={
						"action" 	: 	"jobboard_profile_bookmark",
						"data"	 	: 	"id="+p_id,
						"_wpnonce"	: jobboard1.dirwpnonce,
					};
					jQuery.ajax({
						url : jobboard1.ajaxurl,
						dataType : "json",
						type : "post",
						data : search_params,
						success : function(response){						
							if (response.msg=="success") {
								jQuery("#candidatebookmark").removeClass('btn-light btn-add-favourites').addClass('btn-added-favourites');
								jQuery('#candidatebookmark').prop('title', jobboard1.Added_to_Boobmark);		
							}
						}
					});
			  } 
	  
		}
	 });
	 jQuery('#employerbookmark').on('click', function(e){ 
	   var isLogged =jobboard1.current_user_id;
	   var p_id =jQuery("#profileID").val();
	   
		if (isLogged=="0") {
			alert(jobboard1.Please_login);
		} else {
			  if(jQuery('#employerbookmark').hasClass('btn-added-favourites')){
				var search_params={
						"action" 	: 	"jobboard_employer_bookmark_delete",
						"data"	 	: 	"id="+p_id,
						"_wpnonce"	: jobboard1.dirwpnonce,
					};
					jQuery.ajax({
						url : jobboard1.ajaxurl,
						dataType : "json",
						type : "post",
						data : search_params,
						success : function(response){						
							if (response.msg=="success") {
								jQuery("#employerbookmark").removeClass('btn-added-favourites').addClass('btn-light btn-add-favourites');
								jQuery('#employerbookmark').prop('title', jobboard1.Add_to_Boobmark);		
							}
						}
					});
							
			  }else if(jQuery('#employerbookmark').hasClass('btn-add-favourites')){			
						
					var search_params={
						"action" 	: 	"jobboard_employer_bookmark",
						"data"	 	: 	"id="+p_id,
						"_wpnonce"	: jobboard1.dirwpnonce,
					};
					jQuery.ajax({
						url : jobboard1.ajaxurl,
						dataType : "json",
						type : "post",
						data : search_params,
						success : function(response){						
							if (response.msg=="success") {
								jQuery("#employerbookmark").removeClass('btn-light btn-add-favourites').addClass('btn-added-favourites');
								jQuery('#employerbookmark').prop('title', jobboard1.Added_to_Boobmark);		
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
function candidate_email_popup(user_id){	
		var contactform =jobboard1.ajaxurl+'?action=jobboard_candidate_email_popup&user_id='+user_id;
		jQuery.colorbox({href: contactform,opacity:"0.70",closeButton:false,});
}

