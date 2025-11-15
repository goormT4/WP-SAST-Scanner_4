"use strict";
function loadBackupScript(callback) {
			var script;			
			script = document.createElement('script');
			script.onload = callback;  
			script.src =realpro_data2.cubeportfolio;
			document.head.appendChild(script);
}

loadBackupScript(function() { 			
		
			setTimeout(function(){
					(function($, window, document, undefined) {
				'use strict';

				// init cubeportfolio
				jQuery('#js-grid-meet-the-'+realpro_data2.rand_div).cubeportfolio({
					defaultFilter: '*',
					filters: '#js-filters-meet-the-'+realpro_data2.rand_div,
					layoutMode: 'grid',       
					animationType: 'sequentially',
					gapHorizontal: 50,
					gapVertical: 40,
					gridAdjustment: 'responsive',
					 mediaQueries: [{
						width: 1500,
						cols: 5,
					}, {
						width: 1100,
						cols: 3,
					}, {
						width: 992,
						cols: 3,
					},
					{
						width: 800,
						cols: 3,
					},
					{
						width: 768,
						cols: 3,
					},
					{
						width: 480,
						cols: 2,
					},
					{
						width: 320,
						cols: 1
					}],
					caption: 'fadeIn',
					displayType: 'lazyLoading',
					displayTypeSpeed: 100,

					// singlePage popup
					singlePageDelegate: '.cbp-singlePage',
					singlePageDeeplinking: true,
					singlePageStickyNavigation: true,
					singlePageCounter: '<div class="cbp-popup-singlePage-counter">{{current}} of {{total}}</div>',
					singlePageCallback: function(url, element) {					
						var t = this;

						$.ajax({
								url: url,
								type: 'GET',
								dataType: 'html',
								timeout: 10000
							})
							.done(function(result) {
								t.updateSinglePage(result);
							})
							.fail(function() {
								t.updateSinglePage('AJAX Error! Please refresh the page!');
							});
					},
				});
			})(jQuery, window, document);
					
				},1000);
		
 });		