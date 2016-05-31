(function($){

	// Number of seconds in every time division
	var days	= 24*60*60,
		hours	= 60*60,
		minutes	= 60;
	// Creating the plugin
	$.fn.countdown = function(prop){

		var options = $.extend({
			callback	: function(){},
			timestamp	: 0,
            diff_date   : 125
		},prop);

		var left, d, h, m, s, positions;

		// Initialize the plugin
		//init(this, options);

		positions = this.find('.position');

		(function tick(){
			//alert(Math.floor((options.timestamp -(new Date().getTime())/ 1000)+diff_date));

			//if(!options.diff_date)
             //   options.diff_date=125;
			// Time left
			left = Math.floor((options.timestamp -(new Date().getTime())/ 1000)+options.diff_date);
			//alert(left / days);

			if(left < 0){
				left = 0;
			}

			// Number of days left
			d = Math.floor(left / days);
			//updateDuo(0, 1, d);
			left -= d*days;

			// Number of hours left
			h = Math.floor(left / hours);
			//updateDuo(2, 3, h);
			left -= h*hours;

			// Number of minutes left
			m = Math.floor(left / minutes);
			//updateDuo(4, 5, m);
			left -= m*minutes;
			
			var min=m
			if(min <10)
				min='0'+min;

			// Number of seconds left
			s = left;
			
			var sec=s;
			if(sec <10)
				sec='0'+sec;
			//updateDuo(6, 7, s);

			// Calling an optional user supplied callback			
			options.callback(d, h, min, sec);

			// Scheduling another call of this function in 1s

			this.setTimeout(tick, 1000);
		})();


		return this;
	};
   /* The two helper functions go here */
   
   $.fn.countup = function(prop){

		var options = $.extend({
			callback	: function(){},
			timestamp	: 0,
            diff_date   : 125
		},prop);

		var left, d, h, m, s, positions;

		// Initialize the plugin
		//init(this, options);

		positions = this.find('.position');

		(function tick(){
			//alert(Math.floor((options.timestamp -(new Date().getTime())/ 1000)+diff_date));

			//if(!options.diff_date)
             //   options.diff_date=125;
			// Time left
			left = Math.floor((((new Date().getTime())/ 1000)-options.timestamp)+options.diff_date);
			//alert(left);

			if(left < 0){
				left = 0;
			}

			// Number of days left
			d = Math.floor(left / days);
			//updateDuo(0, 1, d);
			left -= d*days;

			// Number of hours left
			h = Math.floor(left / hours);
			//updateDuo(2, 3, h);
			left -= h*hours;

			// Number of minutes left
			m = Math.floor(left / minutes);
			//updateDuo(4, 5, m);
			left -= m*minutes;
			
			var min=m
			if(min <10)
				min='0'+min;

			// Number of seconds left
			s = left;
			
			var sec=s;
			if(sec <10)
				sec='0'+sec;
			//updateDuo(6, 7, s);

			// Calling an optional user supplied callback

			options.callback(d, h, min, sec);

			// Scheduling another call of this function in 1s

			this.setTimeout(tick, 1000);
		})();


		return this;
	};
})(jQuery);

	/* The two helper functions go here */
