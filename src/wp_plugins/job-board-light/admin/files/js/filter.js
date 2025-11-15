"use strict";			
setTimeout(function(){
	(function($, window, document, undefined) {
				'use strict';
				// init cubeportfolio
				jQuery('#js-grid-meet-the-'+realpro_filter.rand_div).cubeportfolio({
					defaultFilter: '*',
					filters: '#js-filters-meet-the-'+realpro_filter.rand_div,
					layoutMode: 'grid',       
					animationType: 'sequentially',
					gapHorizontal: 50,
					gapVertical: 40,
					gridAdjustment: 'responsive',
					 mediaQueries: [{
						width: 1500,
						cols: realpro_filter.grid_col1500,
					}, {
						width: 1100,
						cols: realpro_filter.grid_col1100,
					}, {
						width: 992,
						cols: realpro_filter.grid_col768,
					},
					{
						width: 800,
						cols: realpro_filter.grid_col768,
					},
					{
						width: 768,
						cols: realpro_filter.grid_col768,
					},
					{
						width: 480,
						cols: realpro_filter.grid_col480,
					},
					{
						width: 320,
						cols: realpro_filter.grid_col375,
					}],
					caption: 'fadeIn',
					displayType: 'lazyLoading',
					displayTypeSpeed: 100,
					
					
				});
			})(jQuery, window, document);
				},1000);
				
setTimeout(function(){ 
	jQuery('#js-grid-meet-the-'+realpro_filter.rand_div).cubeportfolio('layout');	
}, 1000); 				
	