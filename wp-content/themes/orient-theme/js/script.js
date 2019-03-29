/**
 * Toggle the snapping drawer on mobile
 */
jQuery('.mobile-top-bar__drawer-toggle').click(function(e){
	e.preventDefault();
	jQuery('.mobile-drawer').toggleClass('mobile-drawer--open');
	jQuery('.mobile-top-bar').toggleClass('mobile-top-bar--drawer-open')
	jQuery('.mobile-top-bar').find('svg').toggleClass('js-hidden')
});

/**
 * Make the search bar appear when we click the search button
 */
jQuery('.search-icon').click(function(e){
	e.preventDefault();
	jQuery('.search-icon').find('svg').toggleClass('js-hidden');
	jQuery('.desktop-search-bar').toggleClass('desktop-search-bar--open');
	jQuery('.drawer-content').toggleClass('drawer-content--search-open');
	jQuery('#searchInput').focus();
});

/**
 * "More" menu on home page desktop navigation bar
 */

jQuery('.more-menu-toggle').click(function(e){
	e.preventDefault();
})

var mouseoutCoords = {};

// @TODO: This can be triggered several times when the menu is hovered over,
// meaning the mousemove listener can be instantiated more than once. Doesn't
// seem to have any side effects but we'll see
jQuery('.more-menu-toggle').on('mouseover click', (function(e){
	e.preventDefault();
	jQuery('.more-menu-toggle__chevron-down').addClass('js-hidden');
	jQuery('.more-menu-toggle__chevron-up').removeClass('js-hidden');
	jQuery('.more-menu').removeClass('js-hidden');
	jQuery('.more-menu').addClass('menu-show');

	var hoverArea = jQuery('.more-menu').find('.hover-area');
	hoverArea.show();
	mouseoutCoords.top    = hoverArea.offset().top;
	mouseoutCoords.left   = hoverArea.offset().left;
	mouseoutCoords.height = hoverArea.height();
	mouseoutCoords.width  = hoverArea.width();
	hoverArea.hide();

	setTimeout(unHoverMoreMenu, 500);
}));

function unHoverMoreMenu(e) {
	// jQuery('body').off('mousemove');
	jQuery('body').on('mousemove', {'dimensions': mouseoutCoords}, function(e){
		var dimensions = e.data.dimensions;
		if(e.pageY < dimensions.top  || e.pageY > dimensions.top + dimensions.height ||
		   e.pageX < dimensions.left || e.pageX > dimensions.left + dimensions.width) {
			jQuery('body').off('mousemove');
			jQuery('.more-menu-toggle__chevron-down').removeClass('js-hidden');
			jQuery('.more-menu-toggle__chevron-up').addClass('js-hidden');
			jQuery('.more-menu').removeClass('menu-show');
			jQuery('.more-menu').addClass('js-hidden');
		}
	});
}

/**
 * Section submenus on home page navigation bar
 */
jQuery('.home-nav__section-links a').on('mouseover', (function(e) {
	jQuery('.section-menu').removeClass('js-hidden');
	jQuery('.section-menu').addClass('menu-show');
	jQuery('.section-menu').attr('data-active-section', jQuery(this).attr('data-section'))

	jQuery('.section-menu__content').addClass('js-hidden');
	jQuery('[data-section="' + jQuery(this).attr('data-section') + '"]').removeClass('js-hidden')

	var hoverArea = jQuery('.section-menu').find('.hover-area');
	hoverArea.show();
	mouseoutCoords.top    = hoverArea.offset().top;
	mouseoutCoords.left   = hoverArea.offset().left;
	mouseoutCoords.height = hoverArea.height();
	mouseoutCoords.width  = hoverArea.width();
	hoverArea.hide();

	setTimeout(unHoverSectionMenu, 500);
}));

function unHoverSectionMenu(e) {
	jQuery('body').on('mousemove', {'dimensions': mouseoutCoords}, function(e){
		var dimensions = e.data.dimensions;
		if(e.pageY < dimensions.top  || e.pageY > dimensions.top + dimensions.height ||
		   e.pageX < dimensions.left || e.pageX > dimensions.left + dimensions.width) {
			jQuery('body').off('mousemove');
			jQuery('.section-menu').removeClass('menu-show');
			jQuery('.section-menu').addClass('js-hidden');
		}
	});
}

jQuery(document).ready(function() {
    jQuery('.carousel').slick({
      dots: false,
      adaptiveHeight: true,
    });
});

if(jQuery('.content aside').length) {
	var stickySidebar = jQuery('.content aside').offset().top;

	jQuery(window).scroll(function() {
		if (jQuery(window).scrollTop() > stickySidebar) {
			var aside = jQuery('.content aside');
			if(!aside.hasClass("affix")) {
				jQuery('.content aside').css('width', aside.width());
				aside.css('left', aside.offset().left);
				jQuery('.content aside').addClass('affix');
			}
		}
		else {
			jQuery('.content aside').removeClass('affix');
		}
	});
}

var checkValidCommentSubmission = function() {
    var text = jQuery('#comment').val();
    text = text.trim()
    var count = text.split(/[\s]+/).length
    jQuery("#wordcount").html(count + "/200 words");
    
    if (count > 200) {
        jQuery('#submit').prop("disabled",true);
        jQuery('#wordcount').addClass("over-the-limit");
    } else {
        jQuery('#submit').prop("disabled",false);
        jQuery('#wordcount').removeClass("over-the-limit");
    }
}

jQuery(document).ready(function() {
	jQuery('#comment').keyup(checkValidCommentSubmission)
});


/**
 * Make anything with the carousel class a carousel.
 */

setTimeout(function() {
	jQuery('.random-box').fadeOut();
}, 10000 );
