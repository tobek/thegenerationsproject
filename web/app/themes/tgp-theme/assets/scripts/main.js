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
        var $container = $('.js-events-container');
        var height = $container.height();
        $container.css({
          'height': height,
          'overflow-y': 'scroll'
        });

        // Add margin to events so it's scrollable within container
        var lastEventHeight = $('.js-events-container .js-post:last-child').height();
        $('.js-events').css('margin-bottom', height - lastEventHeight - 30);


        $('.js-timeline-node').on('mouseenter', function(event) {
          event.preventDefault();
          var yearMonth = $(this).data('yearMonth');
          var $eventTimestamps = $('.js-events-container [data-year-month="' + yearMonth + '"]');
          var $events = $eventTimestamps.parents('.js-post');
          var $event = $eventTimestamps.first().parents('.js-post');

          var currentScroll = $container.scrollTop();
          var eventOffset = $event.position().top;
          $container.animate({ scrollTop: currentScroll + eventOffset }, 250);

          $container.addClass('is-scrolled');
          $events.addClass('is-active');
        });

        $('.js-timeline-node').on('mouseleave', function(event) {
          $container.removeClass('is-scrolled');
          $container.find('.js-post').removeClass('is-active');
        });
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
