/**
 * Splash
 *
 */
require( [ 'module', 'require', 'exports' ], function( module, require, exports ) {
  console.log( 'ready' );

  module.exports = {
    body: document.body.className.split( ' ' ),
    height: window.screen.availHeight,
    width: window.screen.availWidth,
    reveal: undefined
  };

});
