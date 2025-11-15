"use strict";
var count = 0;
document.querySelector(".filter").addEventListener("click", function(){
	if(count===0){
		document.querySelector("#facets").style.display="block";
		count=1;
	}
	else{
		document.querySelector("#facets").style.display="none";
		count=0;
	}
});
jQuery(function(){
	var settings='';
	var item_template =
	'<div class="item">' +
		'<div class="dirpro-list-img">'+
		'<a href="<%= obj.link %>">' +
		'<img class="img img-fluid" src="<%= obj.imageURL %>">' +
		'</a>'+
		'</div>' +
	'<div class="list-content p-2 mt-2"><% if (obj.featured) {  %> <span class="feature-text">'+dirpro_data.featured+'</span><% } %>' +
	'<a href="<%= obj.link %>"><h4 class="name"><%= obj.title %></h4></a>' +
	'<p class="category  m-0 p-0">' +
	'<span class="p-1 mt-1">'+dirpro_data.Posted+'  <%= obj.p_date %></span>'+
	'</p>' +
	'<p class="category mt-1 m-0 p-0">' +		
	'<% if(obj.job_type !==""){ %><span class="time"><i class="far fa-clock"></i><span class="p-1"><%= obj.job_type %></span></span><% } %>'+
	'</p>' +						
	'<% if(obj.deadline !==""){ %><p class="category m-0 p-0 mt-1">' + 
	'<span class="time"><i class="fas fa-hourglass-end"></i><span class="p-2">'+dirpro_data.deadline+' : <%= obj.deadline %></span></span>'+
	'</p><% } %>' +
	'<% if (obj.favourites=="yes") {  %><p class="fav_icon btn-added-favourites jobbookmark" id="jobbookmark<%= obj.id %>" title="'+dirpro_data.Added_to_Favorites+'" ><i class="far fa-star "></i></p><% }else{ %>'+
	'<p class="fav_icon btn-add-favourites jobbookmark" id="jobbookmark<%= obj.id %>" title="'+dirpro_data.Add_to_Favorites+'"><i class="far fa-star  "></i></p><% } %>'+
	'<p class="client-contact">' +
	'<span class="p-1"><button class="email" onclick="apply_popup(<%= obj.id %>)" >'+dirpro_data.apply+'</button></span>' +	
	'</p>' +
	'</div>' +
	'<div class="clearboth"></div>' +
	'</div>';
	settings = {
		items            : jQuery.parseJSON(dirpro_data.dirpro_items),
		facets           : jQuery.parseJSON(dirpro_data.facets_json),
		resultSelector   : '#results',
		facetSelector    : '#facets',
		resultTemplate   : item_template,
		paginationCount  : dirpro_data.perpage,
		orderByOptions   :  {'title':dirpro_data.title ,'category': dirpro_data.category, 'RANDOM': dirpro_data.random},
		facetSortOption  : {'continent': ["North America", "South America"]}
	}
	jQuery.facetelize(settings);
});
function show_phonenumber(phone,id){
	jQuery("#"+id).replaceWith(phone);
}
function contact_close(){
	jQuery.colorbox.close();
}
function call_popup(dir_id){ 
	var contactform = dirpro_data.wp_jobboard_URLPATH+'/template/listing/contact_popup.php?&dir_id='+dir_id;
	jQuery.colorbox({href: contactform,opacity:"0.70",closeButton:false,})
}
function contact_send_message_iv(){
	var formc = jQuery("#message-pop");
	if (jQuery.trim(jQuery("#email_address",formc).val()) == "" || jQuery.trim(jQuery("#name",formc).val()) == "" || jQuery.trim(jQuery("#message-content",formc).val()) == "") {
		alert(dirpro_data.message);
        } else {
		var ajaxurl = dirpro_data.ajaxurl;
		var loader_image =  dirpro_data.loading_image;
		jQuery('#update_message_popup').html(loader_image);
		var search_params={
			"action"  : 	"jobboard_message_send",
			"form_data":	jQuery("#message-pop").serialize(),
			"_wpnonce":  	 dirpro_data.contact,
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