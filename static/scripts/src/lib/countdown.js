/**
 * Sets up the countDown timer, attaches to .count-down tag and calculates the remaining
 * time from today - data-todate attribute.
 *
 * @class countDown
 * @return {Object} init() function to bootstrap the countDown class
 */
define( [ 'module', 'require', 'exports', 'jquery' ], function( module, require, exports, $ ){
  // console.debug( module.id, 'loading' );

	var countDown = {

		toDate: null,
		element: null,

		day: 0,
		hour: 0,
		minute: 0,
		second: 0,

		timer: null,
		_s: 1000,
		_m: 0,
		_h: 0,
		_d: 0,

		/**
		 * Get the day, hour, minute, second elements
		 *
		 * @method getElements
		 * @return void
		 */
		getElements: function(){
			this.day = $( '.days', this.element );
			this.hour = $( '.hours', this.element );
			this.minute = $( '.minutes', this.element );
			this.second = $( '.seconds', this.element );
		},

		/**
		 * Get the 'to date' from the count-down element
		 *
		 * @method getToDate
		 * @return void
		 */
		getToDate: function(){
			this.toDate = new Date( this.element.data( 'todate' ) );
		},

		/**
		 * Calculate the remaining time to the end date
		 *
		 * @method calcRemaining
		 * @return void
		 */
		calcRemaining: function(){
			var now = new Date();
			var distance = this.toDate - now;

			if( distance < 0 ){
				this.day.html( '0' );
				this.hour.html( '0' );
				this.minute.html( '0' );
				this.second.html( '0' );

				clearInterval( this.timer );

				return false;
			}

			var seconds = Math.floor( (distance % countDown._m) / countDown._s );
			var minutes = Math.floor( (distance % countDown._h) / countDown._m );
			var hours = Math.floor( (distance % countDown._d) / countDown._h );
			var days = Math.floor( distance / countDown._d );

			this.day.html( days );
			this.hour.html( hours );
			this.minute.html( minutes );
			this.second.html( seconds );
		},

		/**
		 * Set up an interval to animate the count down.
		 *
		 * @method startTimer
		 * @return void
		 */
		startTimer: function(){
			this._s = 1000;
			this._m = this._s * 60;
			this._h = this._m * 60;
			this._d = this._h * 24;

			var that = this;
			this.timer = setInterval( function(){
				return that.calcRemaining();
			}, 1000 );
		}
	};

	return {

		init: function(){

			countDown.element = $( '.countdown .timer' );

			countDown.getElements();
			countDown.getToDate();
			countDown.startTimer();
		}
	}
} );
