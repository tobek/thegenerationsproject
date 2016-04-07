/* ========================================================================
 * DOM-based Routing
 * Based on http://goo.gl/EUTi53 by Paul Irish
 *
 * Only fires on body classes that match. If a body class contains a dash,
 * replace the dash with an underscore when adding it to the object below.
 *
 * .noConflict()
 * The routing is enclosed within an anonymous function so that you can
 * always reference jQuery with $, even when in .noConflict() mode.
 * ======================================================================== */

(function($) {

  // Use this variable to set up the common and page specific functions. If you
  // rename this variable, you will also need to rename the namespace below.
  var Sage = {
    // All pages
    'common': {
      init: function() {
        // JavaScript to be fired on all pages

        // Horrible no good hack to make clicking on a slideshow take you to any link in slideshow caption
        $('#gslideshow').on('click', '.cycle-slideshow:not(".carousel-pager"), .gss-captions', function(event) {
          if ($(event.target).parents('.gss-pager, .gss-nav').length) {
            return;
          }

          var $slideshow = $(this).parents('#gslideshow');
          var $activeImage = $slideshow.find('img.cycle-slide-active');
          var link = $activeImage.data('linkTo');
          if (link) {
            window.location.href = link;
          }
        });

        var navSelector = '.banner .menu-main-menu-container';
        var $nav = $(navSelector);
        $('.js-toggle-mobile-menu').on('click', function() {
          if ($nav.hasClass('is-visible')) {
            $nav.removeClass('is-visible');
            $(document).off('click.tgpMobileMenu');
          }
          else {
            $nav.addClass('is-visible');

            setTimeout(function() {
              $(document).on('click.tgpMobileMenu', function(event) {
                if (! $(event.target).parents(navSelector).addBack(navSelector).length) {
                  event.preventDefault();

                  $nav.removeClass('is-visible');

                  $(document).off('click.tgpMobileMenu');
                }
              });
            }, 10);
          }
        });

        $('.menu-main-menu-container .menu-item-has-children').on('click', function(event) {
          if ($nav.hasClass('is-visible')) {
            // Mobile menu is open

            if ($(event.target).parents('.sub-menu').addBack('.sub-menu').length) {
              // They clicked on submenu
              return;
            }

            if ($(event.currentTarget).is('.current-menu-ancestor')) {
              // Let user click on top-level page of an already-expanded section.
              return;
            }

            event.preventDefault();
            $(event.currentTarget).toggleClass('is-active');
          }
        });
      },
      finalize: function() {
        // JavaScript to be fired on all pages, after page specific JS is fired
      }
    },
    // Home page
    'home': {
      init: function() {
        // JavaScript to be fired on the home page
      },
      finalize: function() {
        // JavaScript to be fired on the home page, after the init JS
      }
    },
    // Events Timeline page, note the change from events-timeline to events_timeline.
    'events_timeline': {
      init: function() {
        var connectTimeout;
        var $container = $('.js-events-container');
        var $timeline = $('.js-timeline');

        var height = $container.height();
        $container.css({
          'height': height,
          'overflow-y': 'scroll'
        });

        function scrollToEvent(event) {
          event.preventDefault();
          var $this = $(this);

          var yearMonth = $this.data('yearMonth');
          var $eventTimestamps = $('.js-events-container [data-year-month="' + yearMonth + '"]');
          var $events = $eventTimestamps.parents('.js-post');
          var $event = $eventTimestamps.first().parents('.js-post');

          var currentScroll = $container.scrollTop();
          var eventOffset = $event.position().top;

          $container.animate({ scrollTop: currentScroll + eventOffset }, 250, function() {
            svgConnect.connect($this.find('.letter'), $event.find('header'));
          });

          $container.off('scroll', svgConnect.reset);
          clearTimeout(connectTimeout);
          connectTimeout = setTimeout(function() {
            $container.one('scroll', svgConnect.reset);
            $timeline.one('scroll', svgConnect.reset);
          }, 500);

          $container.addClass('is-scrolled');
          $events.addClass('is-active');
        }

        function leaveEvent(event) {
            $container.removeClass('is-scrolled');
            $container.find('.js-post').removeClass('is-active');
        }

        // Add margin to events so it's scrollable within container
        var lastEventHeight = $('.js-events-container .js-post:last-child').height();
        $('.js-events').css('margin-bottom', height - lastEventHeight - 30);

        if (isTouchDevice()) {
          $('.js-timeline-node').on('click', scrollToEvent);
          $('.js-timeline-node').on('click', function() {
            // Wait a moment so that the scrolLToEvent scroll doesn't fire this
            setTimeout(function() {
              $container.one('scroll', leaveEvent);
            }, 300);
          });
        }
        else {
          $('.js-timeline-node').on('mouseenter', scrollToEvent);

          $('.js-timeline-node').on('mouseleave', leaveEvent);
        }
      }
    }
  };

  // The routing fires all common scripts, followed by the page specific scripts.
  // Add additional events for more control over timing e.g. a finalize event
  var UTIL = {
    fire: function(func, funcname, args) {
      var fire;
      var namespace = Sage;
      funcname = (funcname === undefined) ? 'init' : funcname;
      fire = func !== '';
      fire = fire && namespace[func];
      fire = fire && typeof namespace[func][funcname] === 'function';

      if (fire) {
        namespace[func][funcname](args);
      }
    },
    loadEvents: function() {
      // Fire common init JS
      UTIL.fire('common');

      // Fire page-specific init JS, and then finalize JS
      $.each(document.body.className.replace(/-/g, '_').split(/\s+/), function(i, classnm) {
        UTIL.fire(classnm);
        UTIL.fire(classnm, 'finalize');
      });

      // Fire common finalize JS
      UTIL.fire('common', 'finalize');
    }
  };

  // Load Events
  $(document).ready(UTIL.loadEvents);

})(jQuery); // Fully reference jQuery after this point.

function isTouchDevice() {
  return 'ontouchstart' in window || navigator.maxTouchPoints;
}
