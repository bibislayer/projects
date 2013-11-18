jQuery(document).ready(function(){
"use strict";
	// main menu
	var example = jQuery('#main-menu').superfish({
	});
	//mobile menu
	jQuery('#mobile-menu > span').click(function () {
        var mobile_menu = jQuery('#toggle-view-menu');
        if (mobile_menu.is(':hidden')) {
            mobile_menu.slideDown('300');
            jQuery(this).children('span').html('-');
        } else {
            mobile_menu.slideUp('300');
            jQuery(this).children('span').html('+');
        }
    jQuery(this).toggleClass('active');
    });

	jQuery('#toggle-view-menu li').click(function () {
        var text = jQuery(this).children('div.menu-panel');
        if (text.is(':hidden')) {
            text.slideDown('300');
            jQuery(this).children('span').html('-');
        } else {
            text.slideUp('300');
            jQuery(this).children('span').html('+');
        }
    });

	// accordion
	if (jQuery('.accordion-defaul').length > 0) {
		jQuery( ".accordion-defaul" ).accordion({ active: 0 },{ collapsible: true },{ heightStyle: "content" });
	};
	if (jQuery('.accordion-style-1').length > 0) {
		jQuery( ".accordion-style-1" ).accordion({ active: 0 },{ collapsible: true }),{ heightStyle: "content" };
	};
	
	if (jQuery('.accordion-style-2'.length > 0)) {
		jQuery( ".accordion-style-2" ).accordion({ active: 0 },{ collapsible: true },{ heightStyle: "content" });
		jQuery(".accordion-style-2 .widget-title span").removeClass("icon-list") .html("+");
		jQuery(".accordion-style-2 .ui-accordion-header-active span").html("-");
		jQuery(".accordion-style-2 .widget-title") .click(function(){
			if (jQuery(this) .next() .is(":hidden")) {
				jQuery(".accordion-style-2 .widget-title").find("span").html("-");
				jQuery(this).find("span").html("+");
			}
			else {
				jQuery(".accordion-style-2 .widget-title").find("span").html("+");
				jQuery(this).find("span").html("-");
			}
		});
	};
	

	// toggle
	if (jQuery('.toggle').length > 0) {
		jQuery('.toggle .widget-title span').removeClass("icon-list") .html("+");
		jQuery('.toggle .widget-title') .next() .hide();
		jQuery('.toggle .widget-title').click(function(){
		if (jQuery(this).next() .is(':hidden')) {
		  jQuery(this).next().slideDown('slow');
		  jQuery(this).find('span').html('-');
		}
		else {
		  jQuery(this).next() .slideUp('slow');
		  jQuery(this).find('span').html('+');
		};
		});
	};	
	
	

	// hover	
	jQuery('.item > a .mask') .hover(function(){
		jQuery(this).find('span').stop(true,true).animate({bottom:'50%',marginBottom:'-20px'});
	  },function(){
	  	jQuery(this).find('span').stop(true,true).animate({bottom:'-100px'});
	});
	// tab
	jQuery( ".widget-tab-news" ).tabs({ show: { effect: "blind", duration: 1000 } });

	jQuery( ".tabs" ).tabs();
	
	// flick
	if (jQuery('#flickr-feed-1 ul').length > 0) {
		jQuery('#flickr-feed-1 ul').jflickrfeed({
		    limit: 10,
		    qstrings: {
		      id: '78715597@N07'
		    },
		    itemTemplate:
		      '<li class="flickr-badge-image">' +
		      '<a href="{{link}}" title="{{title}}">' +
		      '<img src="{{image_s}}" alt="{{title}}" width="50px" height="50px" />' +
		      '</a>' +
		      '</li>'
		  });
	};
	

	// tweet
	if (jQuery('#tweets').length > 0) {
		jQuery('#tweets').tweetable({
		  username: 'kopasoft ',
		  time: true,
		  rotate: false,
		  speed: 4000,
		  limit: 1,
		  replies: false,
		  position: 'append',
		  failed: "Sorry, twitter is currently unavailable for this user.",
		  loading: "Loading tweets...",
		  html5: true,
		  onComplete:function(jQueryul){
		    jQuery('time').timeago();
		  }
		});
	};
	

	// hide label form
	jQuery('.form-contact .form-control, .form-comment .form-control') .click(function(){
		jQuery(this).parent().find('.kp-label').hide();
	});
	jQuery('.form-contact .form-control, .form-comment .form-control') .focus(function(){
		jQuery(this).parent().find('.kp-label').hide();		
	});

	// validate form
	if(jQuery(".form-contact").length > 0){
	    jQuery('.form-contact').validate({
	    // Add requirements to each of the fields
	    rules: {
	      name: {
	        required: true,
	        minlength: 2
	      },
	      email: {
	      required: true,
	      email: true
	      },
	      message: {
	      required: true,
	      minlength: 10
	      }
	    },
	    // Specify what error messages to display
	    // when the user does something horrid
	    messages: {
	      name: {
	        required: "Please enter your name.",
	        minlength: jQuery.format("At least {0} characters required.")
	      },
	      email: {
	      required: "Please enter your email.",
	      email: "Please enter a valid email."
	      },
	      url: {
	      required: "Please enter your url.",
	      url: "Please enter a valid url."
	      },
	      message: {
	      required: "Please enter a message.",
	      minlength: jQuery.format("At least {0} characters required.")
	      }
	    },
	    
	    // Use Ajax to send everything to processForm.php
	    submitHandler: function(form) {
	      jQuery("#input-submit").attr("value", "Sending...");
	      jQuery(form).ajaxSubmit({
		        success: function(responseText, statusText, xhr, jQueryform) {
		          jQuery("#response").html(responseText).hide().slideDown("fast");
		          jQuery("#input-submit").attr("value", "Submit");
		        }
	      });
	      return false;
	    }
		});
	}
	if(jQuery(".form-comment").length > 0){
	    jQuery('.form-comment').validate({
	    // Add requirements to each of the fields
	    rules: {
	      name: {
	        required: true,
	        minlength: 2
	      },
	      email: {
	      required: true,
	      email: true
	      },
	      message: {
	      required: true,
	      minlength: 10
	      }
	    },
	    // Specify what error messages to display
	    // when the user does something horrid
	    messages: {
	      name: {
	        required: "Please enter your name.",
	        minlength: jQuery.format("At least {0} characters required.")
	      },
	      email: {
	      required: "Please enter your email.",
	      email: "Please enter a valid email."
	      },
	      url: {
	      required: "Please enter your url.",
	      url: "Please enter a valid url."
	      },
	      message: {
	      required: "Please enter a message.",
	      minlength: jQuery.format("At least {0} characters required.")
	      }
	    },
	    
	    // Use Ajax to send everything to processForm.php
	    submitHandler: function(form) {
	      jQuery("#input-submit").attr("value", "Sending...");
	      jQuery(form).ajaxSubmit({
		        success: function(responseText, statusText, xhr, jQueryform) {
		          jQuery("#response").html(responseText).hide().slideDown("fast");
		          jQuery("#input-submit").attr("value", "Submit");
		        }
	      });
	      return false;
	    }
		});
	}

	// back to top
	jQuery(".back-to-top").hide();
	jQuery(function () 
	{jQuery(window).scroll(function () {
		if ( jQuery(this).scrollTop() > 100 ) { jQuery('.back-to-top').fadeIn(); }

		else { jQuery('.back-to-top').fadeOut();}
	});       
	jQuery('.back-to-top').click(function () 
	{jQuery('body,html').animate({scrollTop: 0}, 800);return false;});});

	//css
	jQuery('#sidebar .widget:even').addClass('first');
	jQuery('.col-area-4 .widget:first').addClass('first');
	jQuery('<span class="pull-left">+</span>')
	.prependTo('#bottom-sidebar .widget_categories li, #bottom-sidebar .widget_pages li,#bottom-sidebar .widget_meta li,#bottom-sidebar .widget_pages li,#bottom-sidebar .widget_recent_entries li,#bottom-sidebar .widget_recent_comments li,#bottom-sidebar .widget_archive li');

	
});


jQuery(window).load(function(){
	// carosel
	if (jQuery('.carousel-1').length > 0) {
		jQuery('.carousel-1').carouFredSel({
			width: '100%',
			height: 'auto',
			scroll:1,
			auto: true
		});
	};
	
	if (jQuery('.carousel-2').length > 0) {
		jQuery('.carousel-2').imagesLoaded(function() {
	        
					jQuery('.carousel-2').carouFredSel({
					width: '100%',
					height: 'auto',
					prev: '#prev2',
					next: '#next2',
					scroll:1,
					auto: false
				});
				// add element in carosel
				var _element = jQuery('.carousel-2 .thumb-bottom .item-content') .html();
				jQuery('.carousel-2 .thumb-bottom .item-content').remove();
				jQuery('<div class="item-content"></div>') .prependTo('.carousel-2 .thumb-bottom .item');
				jQuery(_element).prependTo('.carousel-2 .thumb-bottom .item-content');
			
	    });
    };
	
	
	if (jQuery('.carousel-3').length > 0) {
		jQuery('.carousel-3').carouFredSel({
			responsive: true,
			width: '100%',
			scroll: 1,
			prev: '#prev3',
			next: '#next3',
			auto:false,
			items: {
				width: 600,
				height:'auto',
				visible: {
					min: 1,
					max: 6
				}
			}
		});
	};
	
	if (jQuery('.carousel-4').length > 0) {
		jQuery('.carousel-4').carouFredSel({
			width: '100%',
			height: 'auto',
			prev: '#prev4',
			next: '#next4',
			scroll:1,
			auto: false
		});
	};
	// gallery
	if (jQuery('.kp-gallery-carousel').length > 0) {
		jQuery('.kp-gallery-carousel').flexslider({
		    animation: "slide",
		    controlNav: false,
		    slideshow: false,
		    itemWidth: 126,
		    asNavFor: '.kp-gallery-slider'
		  });
		  
		  jQuery('.kp-gallery-slider').flexslider({
		    animation: "slide",
		    controlNav: false,
		    slideshow: false,
		    sync: ".kp-gallery-carousel",
		    start: function(slider){
		      jQuery('body').removeClass('loading');
		    }
		  });
	};
})