;(function(){

/**
 * Please note that when passing in custom templates for
 * listItemTemplate and orderByTemplate to keep the classes as
 * they are used in the code at other locations as well.
 */

var defaults = {
  items              : [{a:2,b:1,c:2},{a:2,b:2,c:1},{a:1,b:1,c:1},{a:3,b:3,c:1}],
  facets             : {'a': 'Title A', 'b': 'Title B', 'c': 'Title C'},
  resultSelector     : '#results',
  facetSelector      : '#facets',
  facetContainer     : '<div class=facetsearch id=<%= id %> ></div>',
  facetTitleTemplate : '<h3 class=facettitle><%= title %><div> <span class="down"><i class="fas fa-angle-down"></i></span> <span class="up"><i class="fas fa-angle-up"></i></span></div> </h3>',
  facetListContainer : '<div class=facetlist></div>',
  listItemTemplate   : '<div class=facetitem id="<%= id %>"><span class="tick"></span><input class="" type="checkbox" id="<%= id %>"> <%= name %> <span class=facetitemcount>(<%= count %>)</span></div>',
  bottomContainer    : '<div class=bottomline></div>',
  orderByTemplate    : '<div class="orderby dropdown"><button class="btn dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'+dirpro_data.Sortby+'</button><ul class="dropdown-menu" aria-labelledby="dropdownMenuButton"><% _.each(options, function(value, key) { %>'+
                       '<li class="orderbyitem dropdown-item" id=orderby_<%= key %>>'+
                       '<%= value %> </li> <% }); %></ul></div><div class=bg></div>',
  countTemplate      : '<div class=facettotalcount><%= count %> '+dirpro_data.Results+'</div>',
  deselectTemplate   : '<div class=deselectstartover>'+dirpro_data.Deselect+'</div>',
  resultTemplate     : '<div class=facetresultbox><%= name %></div>',
  noResults          : '<div class=results>'+dirpro_data.nolisting+'</div>',
  orderByOptions     : {'a': 'by A', 'b': 'by B', 'RANDOM': 'by random',},
  state              : {
                         orderBy : false,
                         filters : {}
                       },
  showMoreTemplate   : '<a id=showmorebutton>'+dirpro_data.loadmore+'</a>',
  enablePagination   : true,
  paginationCount    : dirpro_data.perpage,
}

/**
 * This is the first function / variable that gets exported into the
 * jQuery namespace. Pass in your own settings (see above) to initialize
 * the faceted search
 */
var settings = {};
jQuery.facetelize = function(usersettings) {
  jQuery.extend(settings, defaults, usersettings);
  settings.currentResults = [];
  settings.facetStore     = {};
  jQuery(settings.facetSelector).data("settings", settings);
  initFacetCount();
  filter();
  order();
  createFacetUI();
  updateResults();
}

/**
 * This is the second function / variable that gets exported into the
 * jQuery namespace. Use it to update everything if you messed with
 * the settings object
 */
jQuery.facetUpdate = function() {
  filter();
  order();
  updateFacetUI();
  updateResults();
}

/**
 * The following section contains the logic of the faceted search
 */

/**
 * initializes all facets and their individual filters
 */

function initFacetCount() {
  _.each(settings.facets, function(facettitle, facet) {
    settings.facetStore[facet] = {};
  });
  _.each(settings.items, function(item) {
   // intialize the count to be zero
    _.each(settings.facets, function(facettitle, facet) {
      if (jQuery.isArray(item[facet])) {
        _.each(item[facet], function(facetitem) {
          settings.facetStore[facet][facetitem] = settings.facetStore[facet][facetitem] || {count: 0, id: _.uniqueId("facet_")}
        });
      } else {
        if (item[facet] !== undefined) {
          settings.facetStore[facet][item[facet]] = settings.facetStore[facet][item[facet]] || {count: 0, id: _.uniqueId("facet_")}
        }
      }
    });
  });
  // sort it:
  _.each(settings.facetStore, function(facet, facettitle) {
    var sorted = _.keys(settings.facetStore[facettitle]).sort();
    if (settings.facetSortOption && settings.facetSortOption[facettitle]) {
      sorted = _.union(settings.facetSortOption[facettitle], sorted);
    }
    var sortedstore = {};
    _.each(sorted, function(el) {
      sortedstore[el] = settings.facetStore[facettitle][el];
    });
    settings.facetStore[facettitle] = sortedstore;
  });
}

/**
 * resets the facet count
 */
function resetFacetCount() {
  _.each(settings.facetStore, function(items, facetname) {
    _.each(items, function(value, itemname) {
      settings.facetStore[facetname][itemname].count = 0;
    });
  });
}

/**
 * Filters all items from the settings according to the currently
 * set filters and stores the results in the settings.currentResults.
 * The number of items in each filter from each facet is also updated
 */
function filter() {
  // first apply the filters to the items
  settings.currentResults = _.select(settings.items, function(item) {
    var filtersApply = true;
    _.each(settings.state.filters, function(filter, facet) {
      if (jQuery.isArray(item[facet])) {
         var inters = _.intersect(item[facet], filter);
         if (inters.length == 0) {
           filtersApply = false;
         }
      } else {
        if (filter.length && _.indexOf(filter, item[facet]) == -1) {
          filtersApply = false;
        }
      }
    });
    return filtersApply;
  });
  // Update the count for each facet and item:
  // intialize the count to be zero
  resetFacetCount();
  // then reduce the items to get the current count for each facet
  _.each(settings.facets, function(facettitle, facet) {
    _.each(settings.currentResults, function(item) {
      if (jQuery.isArray(item[facet])) {
        _.each(item[facet], function(facetitem) {
          settings.facetStore[facet][facetitem].count += 1;
        });
      } else {
        if (item[facet] !== undefined) {
          settings.facetStore[facet][item[facet]].count += 1;
        }
      }
    });
  });
  // remove confusing 0 from facets where a filter has been set
  _.each(settings.state.filters, function(filters, facettitle) {
    _.each(settings.facetStore[facettitle], function(facet) {
      if (facet.count == 0 && settings.state.filters[facettitle].length){
	  facet.count = "+";}
    });
  });
  settings.state.shownResults = 0;
}

/**
 * Orders the currentResults according to the settings.state.orderBy variable
 */
function order() {
  if (settings.state.orderBy) {
    jQuery(".activeorderby").removeClass("activeorderby");
    jQuery('#orderby_'+settings.state.orderBy).addClass("activeorderby");
    settings.currentResults = _.sortBy(settings.currentResults, function(item) {
      if (settings.state.orderBy == 'RANDOM') {
        return Math.random()*10000;
      } else {
        return item[settings.state.orderBy];
      }
    });
  }
}

/**
 * The given facetname and filtername are activated or deactivated
 * depending on what they were beforehand. This causes the items to
 * be filtered again and the UI is updated accordingly.
 */
function toggleFilter(key, value) {
  settings.state.filters[key] = settings.state.filters[key] || [] ;
  if (_.indexOf(settings.state.filters[key], value) == -1) {
    settings.state.filters[key].push(value);
  } else {
    settings.state.filters[key] = _.without(settings.state.filters[key], value);
    if (settings.state.filters[key].length == 0) {
      delete settings.state.filters[key];
    }
  }
  filter();
}

/**
 * The following section contains the presentation of the faceted search
 */

/**
 * This function is only called once, it creates the facets ui.
 */
function createFacetUI() {
  var itemtemplate  = _.template(settings.listItemTemplate);
  var titletemplate = _.template(settings.facetTitleTemplate);
  var containertemplate = _.template(settings.facetContainer);

  jQuery(settings.facetSelector).html("");
  _.each(settings.facets, function(facettitle, facet) {
    var facetHtml     = jQuery(containertemplate({id: facet}));
    var facetItem     = {title: facettitle};
    var facetItemHtml = jQuery(titletemplate(facetItem));

    facetHtml.append(facetItemHtml);
    var facetlist = jQuery(settings.facetListContainer);
    _.each(settings.facetStore[facet], function(filter, filtername){
      var item = {id: filter.id, name: filtername, count: filter.count};
      var filteritem  = jQuery(itemtemplate(item));
      if (_.indexOf(settings.state.filters[facet], filtername) >= 0) {
        filteritem.addClass("activefacet");
        filteritem.addClass("tick");
      }
      facetlist.append(filteritem);
    });
    facetHtml.append(facetlist);
    jQuery(settings.facetSelector).append(facetHtml);
  });
  // add the click event handler to each facet item:
  jQuery('.facetitem').on("click", function(event){
	
    var filter = getFilterById(this.id);
    toggleFilter(filter.facetname, filter.filtername);
    jQuery(settings.facetSelector).trigger("facetedsearchfacetclick", filter);
    order();
    updateFacetUI();
    updateResults();
  });
  // Append total result count
  var bottom = jQuery(settings.bottomContainer);
  countHtml = _.template(settings.countTemplate, {count: settings.currentResults.length});
  jQuery(bottom).append(countHtml);
  // generate the "order by" options:
  var ordertemplate = _.template(settings.orderByTemplate);
  var itemHtml = jQuery(ordertemplate({'options': settings.orderByOptions}));
  jQuery(bottom).append(itemHtml);
  jQuery(settings.facetSelector).append(bottom);
  jQuery('.orderbyitem').each(function(){
    var id = this.id.substr(8);
    if (settings.state.orderBy == id) {
      jQuery(this).addClass("activeorderby");
    }
  });
  // add the click event handler to each "order by" item:
  jQuery('.orderbyitem').on("click", function(event){
    var id = this.id.substr(8);
    settings.state.orderBy = id;
    jQuery(settings.facetSelector).trigger("facetedsearchorderby", id);
    settings.state.shownResults = 0;
    order();
    updateResults();
  });
  // Append deselect filters button
  var deselect = jQuery(settings.deselectTemplate).on("click", function(event){
    settings.state.filters = {};
    jQuery.facetUpdate();
  });
  jQuery(bottom).append(deselect);
  jQuery(settings.facetSelector).trigger("facetuicreated");
}

/**
 * get a facetname and filtername by the unique id that is created in the beginning
 */
function getFilterById(id) {
  var result = false;
  _.each(settings.facetStore, function(facet, facetname) {
    _.each(facet, function(filter, filtername){
      if (filter.id == id) {
        result =  {'facetname': facetname, 'filtername': filtername};
      }
    });
  });
  return result;
}

/**
 * This function is only called whenever a filter has been added or removed
 * It adds a class to the active filters and shows the correct number for each
 */
function updateFacetUI() {
  var itemtemplate = _.template(settings.listItemTemplate);
  _.each(settings.facetStore, function(facet, facetname) {
    _.each(facet, function(filter, filtername){
      var item = {id: filter.id, name: filtername, count: filter.count};
      var filteritem  = jQuery(itemtemplate(item)).html();
      jQuery("#"+filter.id).html(filteritem);
      if (settings.state.filters[facetname] && _.indexOf(settings.state.filters[facetname], filtername) >= 0) {
        jQuery("#"+filter.id).addClass("activefacet");
        jQuery("#"+filter.id).addClass("tick");
      } else {
        jQuery("#"+filter.id).removeClass("activefacet");
        jQuery("#"+filter.id).removeClass("tick");
      }
    });
  });
  countHtml = _.template(settings.countTemplate, {count: settings.currentResults.length});
  jQuery(settings.facetSelector + ' .facettotalcount').replaceWith(countHtml);
}

/**
 * Updates the the list of results according to the filters that have been set
 */
function updateResults() {
  jQuery(settings.resultSelector).html(settings.currentResults.length == 0 ? settings.noResults : "");
  showMoreResults();
}

var moreButton;
function showMoreResults() {	
  var showNowCount =
      settings.enablePagination ?
      Math.min(settings.currentResults.length - settings.state.shownResults, settings.paginationCount) :
      settings.currentResults.length;
  var itemHtml = "";
  var template = _.template(settings.resultTemplate);
  for (var i = settings.state.shownResults; i < settings.state.shownResults + showNowCount; i++) {
    var item = jQuery.extend(settings.currentResults[i], {
      totalItemNr    : i,
      batchItemNr    : i - settings.state.shownResults,
      batchItemCount : showNowCount
    });
    var itemHtml = itemHtml + template(item);
  }
  jQuery(settings.resultSelector).append(itemHtml);
  if (!moreButton) {
    moreButton = jQuery(settings.showMoreTemplate).click(showMoreResults);
    jQuery(settings.resultSelector).after(moreButton);
  }
  if (settings.state.shownResults == 0) {
    moreButton.show();
  }
  settings.state.shownResults += showNowCount;
  if (settings.state.shownResults == settings.currentResults.length) {
    jQuery(moreButton).hide();
  }
  jQuery(settings.resultSelector).trigger("facetedsearchresultupdate");
}

})();

jQuery(function(){
    var status = 0;
    jQuery(document).on("click", "#category .facettitle",function(e){
        
        var id = "category";
        if(status === 0 ){
            jQuery("#"+id+" .facetlist").css("display", "none");
            jQuery("#"+id+" .facettitle .down").css("visibility", "hidden");
            jQuery("#"+id+" .facettitle .up").css("visibility", "visible");
            status = 1;
        }
        else{
            jQuery("#"+id+" .facetlist").css("display", "block");
            jQuery("#"+id+" .facettitle .down").css("visibility", "visible");
            jQuery("#"+id+" .facettitle .up").css("visibility", "hidden");
            status = 0;
        }

     });
})


jQuery(function(){
    var status = 0;
    jQuery(document).on("click", "#location .facettitle",function(e){
      
        var id = "location";
        if(status === 0 ){
            jQuery("#"+id+" .facetlist").css("display", "none");
            jQuery("#"+id+" .facettitle .down").css("visibility", "hidden");
            jQuery("#"+id+" .facettitle .up").css("visibility", "visible");
            status = 1;
        }
        else{
            jQuery("#"+id+" .facetlist").css("display", "block");
            jQuery("#"+id+" .facettitle .down").css("visibility", "visible");
            jQuery("#"+id+" .facettitle .up").css("visibility", "hidden");
            status = 0;
        }

     });
})

jQuery(function(){
    var status = 0;
    jQuery(document).on("click", "#feature .facettitle",function(e){
       
        var id = "feature";
        if(status === 0 ){
            jQuery("#"+id+" .facetlist").css("display", "block");
            jQuery("#"+id+" .facettitle .down").css("visibility", "visible");
            jQuery("#"+id+" .facettitle .up").css("visibility", "hidden");
            status = 1;
        }
        else{
            jQuery("#"+id+" .facetlist").css("display", "none");
            jQuery("#"+id+" .facettitle .down").css("visibility", "hidden");
            jQuery("#"+id+" .facettitle .up").css("visibility", "visible");
            status = 0;
        }

     });
})

jQuery(function(){
    var status = 0;
    jQuery(document).on("click", "#zipcode .facettitle",function(e){
      
        var id = "zipcode";
        if(status === 0 ){
            jQuery("#"+id+" .facetlist").css("display", "block");
            jQuery("#"+id+" .facettitle .down").css("visibility", "visible");
            jQuery("#"+id+" .facettitle .up").css("visibility", "hidden");
            status = 1;
        }
        else{
            jQuery("#"+id+" .facetlist").css("display", "none");
            jQuery("#"+id+" .facettitle .down").css("visibility", "hidden");
            jQuery("#"+id+" .facettitle .up").css("visibility", "visible");
            status = 0;
        }

     });
})


jQuery(function(){
    var status = 0;
    jQuery(document).on("click", "#local-area .facettitle",function(e){
       
        var id = "local-area";
        if(status === 0 ){
            jQuery("#"+id+" .facetlist").css("display", "block");
            jQuery("#"+id+" .facettitle .down").css("visibility", "visible");
            jQuery("#"+id+" .facettitle .up").css("visibility", "hidden");
            status = 1;
        }
        else{
            jQuery("#"+id+" .facetlist").css("display", "none");
            jQuery("#"+id+" .facettitle .down").css("visibility", "hidden");
            jQuery("#"+id+" .facettitle .up").css("visibility", "visible");
            status = 0;
        }

     });
})
jQuery(function(){
    var status = 0;
    jQuery(document).on("click", "#salaryrange .facettitle",function(e){
       
        var id = "salaryrange";
        if(status === 0 ){
            jQuery("#"+id+" .facetlist").css("display", "block");
            jQuery("#"+id+" .facettitle .down").css("visibility", "visible");
            jQuery("#"+id+" .facettitle .up").css("visibility", "hidden");
            status = 1;
        }
        else{
            jQuery("#"+id+" .facetlist").css("display", "none");
            jQuery("#"+id+" .facettitle .down").css("visibility", "hidden");
            jQuery("#"+id+" .facettitle .up").css("visibility", "visible");
            status = 0;
        }

     });
})
jQuery(function(){
    var status = 0;
    jQuery(document).on("click", "#jobtype .facettitle",function(e){
       
        var id = "jobtype";
        if(status === 0 ){
            jQuery("#"+id+" .facetlist").css("display", "block");
            jQuery("#"+id+" .facettitle .down").css("visibility", "visible");
            jQuery("#"+id+" .facettitle .up").css("visibility", "hidden");
            status = 1;
        }
        else{
            jQuery("#"+id+" .facetlist").css("display", "none");
            jQuery("#"+id+" .facettitle .down").css("visibility", "hidden");
            jQuery("#"+id+" .facettitle .up").css("visibility", "visible");
            status = 0;
        }

     });
})
jQuery(function(){
    var status = 0;
    jQuery(document).on("click", "#postdate .facettitle",function(e){
        
        var id = "postdate";
        if(status === 0 ){
            jQuery("#"+id+" .facetlist").css("display", "block");
            jQuery("#"+id+" .facettitle .down").css("visibility", "visible");
            jQuery("#"+id+" .facettitle .up").css("visibility", "hidden");
            status = 1;
        }
        else{
            jQuery("#"+id+" .facetlist").css("display", "none");
            jQuery("#"+id+" .facettitle .down").css("visibility", "hidden");
            jQuery("#"+id+" .facettitle .up").css("visibility", "visible");
            status = 0;
        }

     });
})
jQuery(function(){
    var status = 0;
    jQuery(document).on("click", "#postdeadline .facettitle",function(e){
        
        var id = "postdeadline";
        if(status === 0 ){
            jQuery("#"+id+" .facetlist").css("display", "block");
            jQuery("#"+id+" .facettitle .down").css("visibility", "visible");
            jQuery("#"+id+" .facettitle .up").css("visibility", "hidden");
            status = 1;
        }
        else{
            jQuery("#"+id+" .facetlist").css("display", "none");
            jQuery("#"+id+" .facettitle .down").css("visibility", "hidden");
            jQuery("#"+id+" .facettitle .up").css("visibility", "visible");
            status = 0;
        }

     });
})
jQuery(function(){
    var status = 0;
    jQuery(document).on("click", "#joblevel .facettitle",function(e){
        
        var id = "joblevel";
        if(status === 0 ){
            jQuery("#"+id+" .facetlist").css("display", "block");
            jQuery("#"+id+" .facettitle .down").css("visibility", "visible");
            jQuery("#"+id+" .facettitle .up").css("visibility", "hidden");
            status = 1;
        }
        else{
            jQuery("#"+id+" .facetlist").css("display", "none");
            jQuery("#"+id+" .facettitle .down").css("visibility", "hidden");
            jQuery("#"+id+" .facettitle .up").css("visibility", "visible");
            status = 0;
        }

     });
})
jQuery(function(){
    var status = 0;
    jQuery(document).on("click", "#gender .facettitle",function(e){
        
        var id = "gender";
        if(status === 0 ){
            jQuery("#"+id+" .facetlist").css("display", "block");
            jQuery("#"+id+" .facettitle .down").css("visibility", "visible");
            jQuery("#"+id+" .facettitle .up").css("visibility", "hidden");
            status = 1;
        }
        else{
            jQuery("#"+id+" .facetlist").css("display", "none");
            jQuery("#"+id+" .facettitle .down").css("visibility", "hidden");
            jQuery("#"+id+" .facettitle .up").css("visibility", "visible");
            status = 0;
        }

     });
})
jQuery(function(){
    var status = 0;
    jQuery(document).on("click", "#experiencerange .facettitle",function(e){
        
        var id = "experiencerange";
        if(status === 0 ){
            jQuery("#"+id+" .facetlist").css("display", "block");
            jQuery("#"+id+" .facettitle .down").css("visibility", "visible");
            jQuery("#"+id+" .facettitle .up").css("visibility", "hidden");
            status = 1;
        }
        else{
            jQuery("#"+id+" .facetlist").css("display", "none");
            jQuery("#"+id+" .facettitle .down").css("visibility", "hidden");
            jQuery("#"+id+" .facettitle .up").css("visibility", "visible");
            status = 0;
        }

     });
})
jQuery(function(){
    var status = 0;
    jQuery(document).on("click", "#educationalrequirements .facettitle",function(e){
        
        var id = "educationalrequirements";
        if(status === 0 ){
            jQuery("#"+id+" .facetlist").css("display", "block");
            jQuery("#"+id+" .facettitle .down").css("visibility", "visible");
            jQuery("#"+id+" .facettitle .up").css("visibility", "hidden");
            status = 1;
        }
        else{
            jQuery("#"+id+" .facetlist").css("display", "none");
            jQuery("#"+id+" .facettitle .down").css("visibility", "hidden");
            jQuery("#"+id+" .facettitle .up").css("visibility", "visible");
            status = 0;
        }

     });
})