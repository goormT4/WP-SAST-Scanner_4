jQuery( function() { 
	jQuery.widget( "custom.catcomplete", jQuery.ui.autocomplete, {
	  _create: function() {
		this._super();
		this.widget().menu( "option", "items", "> :not(.ui-autocomplete-category)" );
	  },
	  _renderMenu: function( ul, items ) {
		var that = this,
		  currentCategory = "";
		jQuery.each( items, function( index, item ) {
		  var li;
		  if ( item.category != currentCategory ) {
			ul.append( "<li class='ui-autocomplete-category'>" + item.category + "</li>" );
			currentCategory = item.category;
		  }
		  li = that._renderItemData( ul, item );
		  if ( item.category ) {
			li.attr( "aria-label", item.category + " : " + item.label );
		  }
		});
	  }
	});	
	  
 
	var cache = {};
	jQuery( "#dirsearch" ).catcomplete({
		  delay: 0,
		  minLength: 1,	
		  async: true,	
		  source: function( request, response ) {
				var term = request.term;
				if ( term in cache ) {
				  response( cache[ term ] );
				  return;
				}		 
				jQuery.getJSON( slider_data.ajaxurl+'?action=get_unique_dirslider_search_field1', request, function( data, status, xhr ) {
					cache[ term ] = data;
					response( data );
				});
			},
		  select: function(e, ui) {
			// if the cateogry is in your response, on select, your item will have a category job.			
			jQuery( "#dirsearchtype" ).val(ui.item.category);
		  }

		});
		

	var cache = {};
	jQuery( "#location" ).catcomplete({
		  delay: 0,
		  minLength: 1,		  
		  async: true,	
		   source: function( request, response ) {
				var term = request.term;
				if ( term in cache ) {
				  response( cache[ term ] );
				  return;
				}		 
				jQuery.getJSON( slider_data.ajaxurl+'?action=get_unique_dirslider_search_field2', request, function( data, status, xhr ) {
					cache[ term ] = data;
					response( data );
				});
			},
		  select: function(e, ui) {
				// if the cateogry is in your response, on select, your item will have a category job.		
			jQuery( "#locationtype" ).val(ui.item.category);

		  }

		});
  } );