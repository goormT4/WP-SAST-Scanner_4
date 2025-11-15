(function (jQuery, window) {
  jQuery(function () {
    // remove href = # when click menu in device
    var jQuerymainMenu = jQuery('.main-menu');
    jQuerymainMenu.on('click', 'a', function (e) {
      var jQuerythis = jQuery(this);

      if (jQuerythis.attr('href') == '#' || jQuerythis.attr('href') == '') {
        e.preventDefault();
      }
    }); // menu mobile

    if (jQuery(window).width() < 992) {
      jQuery('.btn-toggle').bind('click', function () {
        jQuery(this).addClass('active');
        jQuery('.menu-mobile-wrap').addClass('open');
      });
    } else {
      jQuery('.btn-toggle').removeClass('active');
      jQuery('.menu-mobile-wrap').removeClass('open');
    }

    jQuery('.menu-mobile-wrap .btn-menu-close').bind('click', function () {
      jQuery('.btn-toggle').removeClass('active');
      jQuery('.menu-mobile-wrap').removeClass('open');
    });
    var heightProfileMenu = jQuery('.menu-mobile-profile').height();
    var heightWin = jQuery(window).height();
    jQuery('.menu-mobile').css('max-height', heightWin - heightProfileMenu + 'px');
    jQuery(window).on('resize', function () {
      // menu mobile
      if (jQuery(window).width() < 992) {
        jQuery('.btn-toggle').bind('click', function () {
          jQuery(this).addClass('active');
          jQuery('.menu-mobile-wrap').addClass('open');
        });
      } else {
        jQuery('.btn-toggle').removeClass('active');
        jQuery('.menu-mobile-wrap').removeClass('open');
      }

      var heightProfileMenu = jQuery('.menu-mobile-profile').height();
      var heightWin = jQuery(window).height();
      jQuery('.menu-mobile').css('max-height', heightWin - heightProfileMenu + 'px');
    }); // search box

    jQuery('.btn-toggle-search').bind('click', function (e) {
      e.stopPropagation();
      jQuery(this).parent().find('.search-form').addClass('show');
      return false;
    });
    jQuery('.header-box .search-form').bind('click', function (e) {
      e.stopPropagation();
    });
    jQuery('html, body').bind('click', function () {
      jQuery('.header-box .search-form').removeClass('show');
    }); //filter job mobile

    jQuery('.toggle-sidebar-left').bind('click', function () {
      jQuery('.sidebar-left').addClass('show');
    });
    jQuery('.btn-close-sidebar-left').bind('click', function () {
      jQuery('.sidebar-left').removeClass('show');
    }); // custom scroll

    jQuery('.scroller').mCustomScrollbar({
      axis: 'y',
      theme: '3d'
    }); // filter salary

    jQuery('#slider-range').slider({
      range: true,
      min: 0,
      max: 90000,
      values: [0, 15000],
      slide: function (event, ui) {
        jQuery('#amount').val('jQuery' + ui.values[0] + ' - jQuery' + ui.values[1]);
      }
    });
    jQuery('#amount').val(jQuery('#slider-range').slider('values', 0) + 'jQuery' + ' - ' + jQuery('#slider-range').slider('values', 1) + 'jQuery'); // hide filter screen 1920

    jQuery('.hide-filter label').bind('click', function () {
      jQuery('body').toggleClass('collapse-filter');
    }); // slide for testmonials

    // jQuery('.testmonials').slick({
    //   autoplay: true,
    //   slidesToShow: 1,
    //   slidesToScroll: 1,
    //   arrows: false,
    //   dots: false
    // }); // custom select search

    jQuery('.smart-search-list').each(function () {
      var jQuerythis = jQuery(this),
          numberOfOptions = jQuery(this).children('option').length;
      jQuerythis.addClass('select-hidden');
      jQuerythis.wrap('<div class="smart-search-category"></div>');
      jQuerythis.after('<div class="smart-search-category-styled"></div>');
      var jQuerystyledSelect = jQuerythis.next('div.smart-search-category-styled');
      jQuerystyledSelect.text(jQuerythis.children('option').eq(0).text());
      var jQuerylist = jQuery('<ul />', {
        'class': 'select-options'
      }).insertAfter(jQuerystyledSelect);

      for (var i = 0; i < numberOfOptions; i++) {
        jQuery('<li />', {
          text: jQuerythis.children('option').eq(i).text(),
          rel: jQuerythis.children('option').eq(i).val()
        }).appendTo(jQuerylist);
      }

      var jQuerylistItems = jQuerylist.children('li');
      jQuerystyledSelect.click(function (e) {
        e.stopPropagation();
        jQuery('div.smart-search-category-styled.active').not(this).each(function () {
          jQuery(this).removeClass('active').next('ul.select-options').hide();
        });
        jQuery(this).toggleClass('active').next('ul.select-options').toggle();
      });
      jQuerylistItems.bind('click', function (e) {
        e.stopPropagation();
        jQuerystyledSelect.text(jQuery(this).text()).removeClass('active');
        jQuerythis.val(jQuery(this).attr('rel'));
        jQuerylist.hide();
      });
      jQuery(document).bind('click', function () {
        jQuerystyledSelect.removeClass('active');
        jQuerylist.hide();
      });
    }); // masonry
    // var jQuerymasonry = jQuery('.masonry').isotope({
    //   layoutMode: 'packery',
    //   itemSelector: '.product-thumnail'
    // });

    var jQuerycontainer = jQuery('.masonry').isotope({
      itemSelector: '.masonry-item'
    });
    jQuerycontainer.imagesLoaded().progress(function () {
      jQuerycontainer.isotope('layout');
    });
    jQuery('#filters button').bind('click', function () {
      var selector = jQuery(this).attr('data-filter'); // jQuery('#filters button').removeClass('is-checked');

      jQuery(this).addClass('is-checked');
      jQuerycontainer.isotope({
        filter: selector
      });
      return false;
    }); // toggle slidebar admin

    jQuery('.toggle-sidebar-admin').bind('click', function () {
      jQuery(this).toggleClass('active');
      jQuery('.sidebar-admin').toggleClass('show');
    }); // chat

    jQuery('.msg-contact-item').bind('click', function () {
      jQuery('.employer-messages').addClass('conversation-mb');
      return false;
    });
    jQuery('.back-view').bind('click', function () {
      jQuery('.employer-messages').removeClass('conversation-mb');
    }); // popupvideo

    jQuery('.popup-video').magnificPopup({
      disableOn: 700,
      type: 'iframe',
      mainClass: 'mfp-fade',
      removalDelay: 160,
      preloader: false,
      fixedContentPos: false
    });
  });
})(jQuery, window);
//# sourceMappingURL=main.js.map
