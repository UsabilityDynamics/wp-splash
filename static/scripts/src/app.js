/**
 * Splash
 *
 */
require(['skrollr', 'jquery', 'udx.utility.imagesloaded' ], function(skrollr, jQuery, imagesloaded){
  console.log( 'ready' );

  skrollr.init();

  // Setup variables
  var $window =jQuery(window);
  var $slide =jQuery('.homeSlide');
  var $body =jQuery('body');

  imagesloaded( $body, function() {
    // console.log( 'have images' );

    setTimeout(function() {

      // Resize sections
      adjustWindow();

      // Fade in sections
      $body.removeClass('loading').addClass('loaded');

    }, 800);

  });

  function adjustWindow() {

    // Get window size
    var winH = $window.height();
    var winW = $window.width();

    // Keep minimum height 550
    if(winH <= 550) {
      winH = 550;
    }

    // Init Skrollr for 768 and up
    if( winW >= 768) {

      // Init Skrollr
      var s = skrollr.init({
        forceHeight: false
      });

      // Resize our slides
      $slide.height(winH);

      s.refresh($('.homeSlide'));

    } else {
      skrollr.init().destroy();
    }

    // Check for touch
    if( 'object' === typeof Modernizr ) {
      skrollr.init().destroy();
    }

  }

  function initAdjustWindow() {
    return {
      match : function() {
        adjustWindow();
      },
      unmatch : function() {
        adjustWindow();
      }
    };
  }

  if( 'function' === typeof enquire.register ) {
    enquire.register( "screen and (min-width : 768px)", initAdjustWindow(), false ); // .listen(100);
  }


});

window.enquire=function(t){"use strict";function i(t,i){var n,s=0,e=t.length;for(s;e>s&&(n=i(t[s],s),n!==!1);s++);}function n(t){return"[object Array]"===Object.prototype.toString.apply(t)}function s(t){return"function"==typeof t}function e(t){this.options=t,!t.deferSetup&&this.setup()}function o(i,n){this.query=i,this.isUnconditional=n,this.handlers=[],this.mql=t(i);var s=this;this.listener=function(t){s.mql=t,s.assess()},this.mql.addListener(this.listener)}function r(){if(!t)throw Error("matchMedia not present, legacy browsers require a polyfill");this.queries={},this.browserIsIncapable=!t("only all").matches}return e.prototype={setup:function(){this.options.setup&&this.options.setup(),this.initialised=!0},on:function(){!this.initialised&&this.setup(),this.options.match&&this.options.match()},off:function(){this.options.unmatch&&this.options.unmatch()},destroy:function(){this.options.destroy?this.options.destroy():this.off()},equals:function(t){return this.options===t||this.options.match===t}},o.prototype={addHandler:function(t){var i=new e(t);this.handlers.push(i),this.mql.matches&&i.on()},removeHandler:function(t){var n=this.handlers;i(n,function(i,s){return i.equals(t)?(i.destroy(),!n.splice(s,1)):void 0})},clear:function(){i(this.handlers,function(t){t.destroy()}),this.mql.removeListener(this.listener),this.handlers.length=0},assess:function(){var t=this.mql.matches||this.isUnconditional?"on":"off";i(this.handlers,function(i){i[t]()})}},r.prototype={register:function(t,e,r){var h=this.queries,a=r&&this.browserIsIncapable;return h[t]||(h[t]=new o(t,a)),s(e)&&(e={match:e}),n(e)||(e=[e]),i(e,function(i){h[t].addHandler(i)}),this},unregister:function(t,i){var n=this.queries[t];return n&&(i?n.removeHandler(i):(n.clear(),delete this.queries[t])),this}},new r}(window.matchMedia);