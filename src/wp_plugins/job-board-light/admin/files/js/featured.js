"use strict";			
setTimeout(function(){
	(function($, window, document, undefined) {
				'use strict';
				// init cubeportfolio
				jQuery('#js-grid-featured-'+jobboard_featured.rand_div).cubeportfolio({
					defaultFilter: '*',
					filters: '#js-filters-meet-the-'+jobboard_featured.rand_div,
					layoutMode: 'grid',       
					animationType: 'sequentially',
					gapHorizontal: 50,
					gapVertical: 40,
					gridAdjustment: 'responsive',
					 mediaQueries: [{
						width: 1500,
						cols: jobboard_featured.grid_col1500,
					}, {
						width: 1100,
						cols: jobboard_featured.grid_col1100,
					}, {
						width: 992,
						cols: jobboard_featured.grid_col768,
					},
					{
						width: 800,
						cols: jobboard_featured.grid_col768,
					},
					{
						width: 768,
						cols: jobboard_featured.grid_col768,
					},
					{
						width: 480,
						cols: jobboard_featured.grid_col480,
					},
					{
						width: 320,
						cols: jobboard_featured.grid_col375,
					}],
					caption: 'fadeIn',
					displayType: 'lazyLoading',
					displayTypeSpeed: 100,
					
					
				});
			})(jQuery, window, document);
				},1000);
setTimeout(function(){ 
	jQuery('#js-grid-featured-'+jobboard_featured.rand_div).cubeportfolio('layout');	
}, 2000); 				
	