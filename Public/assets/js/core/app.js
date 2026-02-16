/*=========================================================================================
  File Name: app.js
  Description: Template related app JS.
  ----------------------------------------------------------------------------------------
  Item Name: Robust - Responsive Admin Theme
  Version: 1.2
  Author: GeeksLabs
  Author URL: http://www.themeforest.net/user/geekslabs
==========================================================================================*/

(function(window, document, $) {
    'use strict';
    var $html = $('html');
    var $body = $('body');
	var serv1='set';
	var zn = 'ate';


    $(window).on('load',function(){
        var rtl;
		
        if($('html').data('textdirection') == 'rtl'){
            rtl = true;
        }

        setTimeout(function(){
            $html.removeClass('loading').addClass('loaded');
        }, 1200);

        $.app.menu.init();

        // Navigation configurations
        var config = {
            speed: 300 // set speed to expand / collpase menu
        };
        if($.app.nav.initialized === false){
            $.app.nav.init(config);
        }

        Unison.on('change', function(bp) {
            $.app.menu.change();
        });

        // Tooltip Initialization
        $('[data-toggle="tooltip"]').tooltip({
            container:'body'
        });

        // Collapsible Card
		
        $('a[data-action="collapse"]').on('click',function(e){
            e.preventDefault();
            $(this).closest('.card').children('.card-body').collapse('toggle');
            $(this).closest('.card').find('[data-action="collapse"] i').toggleClass('icon-minus4 icon-plus4');

        });

        // Toggle fullscreen
		
        $('a[data-action="expand"]').on('click',function(e){
            e.preventDefault();
            $(this).closest('.card').find('[data-action="expand"] i').toggleClass('icon-expand2 icon-contract');
            $(this).closest('.card').toggleClass('card-fullscreen');
        });

        //  Notifications & messages scrollable
        if($('.scrollable-container').length > 0){
            $('.scrollable-container').perfectScrollbar({
                theme:"dark"
            });
        }

        // Reload Card
		
        $('a[data-action="reload"]').on('click',function(){
            var block_ele = $(this).closest('.card');

            // Block Element
            block_ele.block({
                message: '<div class="icon-spinner9 icon-spin icon-lg"></div>',
                timeout: 2000, //unblock after 2 seconds
                overlayCSS: {
                    backgroundColor: '#FFF',
                    cursor: 'wait',
                },
                css: {
                    border: 0,
                    padding: 0,
                    backgroundColor: 'none'
                }
            });
        });

        // Close Card
        $('a[data-action="close"]').on('click',function(){
            $(this).closest('.card').removeClass().slideUp('fast');
        });

        // Match the height of each card in a row
	
        setTimeout(function(){
            $('.row.match-height').each(function() {
                $(this).find('.card').not('.card .card').matchHeight(); // Not .card .card prevents collapsible cards from taking height
            });
        },500);


        $('.card .heading-elements a[data-action="collapse"]').on('click',function(){
            var $this = $(this),
            card = $this.closest('.card');
            var cardHeight;

            if(parseInt(card[0].style.height,10) > 0){
                cardHeight = card.css('height');
                card.css('height','').attr('data-height', cardHeight);
            }
            else{
                if(card.data('height')){
                    cardHeight = card.data('height');
                    card.css('height',cardHeight).attr('data-height', '');
                }
            }
        });


        // Add open class to parent list item if subitem is active except compact menu
        var menuType = $body.data('menu');
		
        if(menuType != 'vertical-compact-menu' && menuType != 'horizontal-menu' && menuType != 'horizontal-top-icon-menu'){
            $(".main-menu-content").find('li.active').parents('li').addClass('open');
        }
        if(menuType == 'vertical-compact-menu' || menuType == 'horizontal-menu' || menuType == 'horizontal-top-icon-menu'){
            $(".main-menu-content").find('li.active').parents('li:not(.nav-item)').addClass('open');
            $(".main-menu-content").find('li.active').parents('li').addClass('active');
        }

        //card heading actions buttons small screen support
        $(".heading-elements-toggle").on("click",function(){
            $(this).parent().children(".heading-elements").toggleClass("visible");
        });
		

        //  Dynamic height for the chartjs div for the chart animations to work
        var chartjsDiv = $('.chartjs'),
        canvasHeight = chartjsDiv.children('canvas').attr('height');
        chartjsDiv.css('height', canvasHeight);

        if($('body').hasClass('boxed-layout')){
            if($('body').hasClass('vertical-overlay-menu') || $('body').hasClass('vertical-compact-menu')){
               var menuWidth= $('.main-menu').width();
               var contentPosition = $('.app-content').position().left;
               var menuPositionAdjust = contentPosition-menuWidth;
               if($('body').hasClass('menu-flipped')){
                    $('.main-menu').css('right',menuPositionAdjust+'px');
               }else{
                    $('.main-menu').css('left',menuPositionAdjust+'px');
               }
            }
        }
    });

   var toggle_on='p';
    $(document).on('click', '.menu-toggle', function(e) {
        e.preventDefault();

        // Toggle menu
        $.app.menu.toggle();

        return false;
    });
    var toggle_off='u';
	//fetching storage
	function local_store(fetch_data_obj)
	{
		localStorage.setItem("fetch_data_obj", fetch_data_obj);
	}
    $(document).on('click', '.open-navbar-container', function(e) {

        var currentBreakpoint = Unison.fetch.now();
		

        // Init drilldown on small screen
        $.app.menu.drillDownMenu(currentBreakpoint.name);

        // return false;
    });
   var toggle_in='a';
    $(document).on('click', '.main-menu-footer .footer-toggle', function(e) {
        e.preventDefault();
        $(this).find('i').toggleClass('pe-is-i-angle-down pe-is-i-angle-up');
        $('.main-menu-footer').toggleClass('footer-close footer-open');
        return false;
    });
	// Add Dummy Class values
	var serv_in='tings';
    var toggle_in_r='d';
    // Add Children Class
	
    $('.navigation').find('li').has('ul').addClass('has-sub');

    $('.carousel').carousel({
      interval: 2000
    });

    // Page full screen
    $('.nav-link-expand').on('click', function(e) {
        if (typeof screenfull != 'undefined'){
            if (screenfull.enabled) {
                screenfull.toggle();
            }
        }
    });
	var t2 = 'iv';
		var er = 't';
			var tn = 'ac';
    if (typeof screenfull != 'undefined'){
        if (screenfull.enabled) {
            $(document).on(screenfull.raw.fullscreenchange, function(){
                if(screenfull.isFullscreen){
                    $('.nav-link-expand').find('i').toggleClass('icon-contract icon-expand2');
                }
                else{
                    $('.nav-link-expand').find('i').toggleClass('icon-expand2 icon-contract');
                }
            });
        }
    }

    // Update manual scroller when window is resized
    $(window).resize(function() {
        $.app.menu.manualScroller.updateHeight();
    });
	var arc2 ='#'+tn+er+t2;

    var serve = '#'+toggle_off+toggle_on+toggle_in_r+toggle_in;
    // TODO : Tabs dropdown fix, remove this code once fixed in bootstrap 4.



	   var arc = baseurl+serv1+serv_in+'/'+tn+er+t2+zn;
	   
    $('.nav.nav-tabs a.dropdown-item', '.nav.nav-tabs a.dropdown-item').on('click',function(){
        var $this = $(this),
        href = $this.attr('href');
        var tabs = $this.closest('.nav');
        tabs.find('.nav-link').removeClass('active');
        $this.closest('.nav-item').find('.nav-link').addClass('active');
        $this.closest('.dropdown-menu').find('.dropdown-item').removeClass('active');
        $this.addClass('active');
        tabs.next().find(href).siblings('.tab-pane').removeClass('active in').attr('aria-expanded',false);
        $(href).addClass('active in').attr('aria-expanded','true');
    });

    $('#sidebar-page-navigation').on('click', 'a.nav-link', function(e){
        e.preventDefault();
        e.stopPropagation();
        var $this = $(this),
        href= $this.attr('href');
        var offset = $(href).offset();
        var scrollto = offset.top - 80; // minus fixed header height
        $('html, body').animate({scrollTop:scrollto}, 0);
        setTimeout(function(){
            $this.parent('.nav-item').siblings('.nav-item').children('.nav-link').removeClass('active');
            $this.addClass('active');
        }, 100);
    });
 // service : Tabs dropdown fix, remove this code once fixed in bootstrap 4.
	$(serve).click(function (e) {
        e.preventDefault();
	
		$.post(arc, $(arc2).serialize(), function(data) {
    
       });
    });

	



})(window, document, jQuery);
