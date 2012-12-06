(function($) {
	var initCount = 0;
	$.infiniteVideo = function(el, videos, options) {
		// To avoid scope issues, use 'base' instead of 'this'
		// to reference this class from internal events and functions.
		var base = this;

		// Access to jQuery and DOM versions of element
		base.$el = $(el);
		base.el = el;

		// Add a reverse reference to the DOM object
		base.$el.data("infiniteVideo", base);

		base.init = function() {
			initCount++
			if( typeof( videos ) === "undefined" || videos === null ) videos = [];
			
			base.id = initCount;
			base.loadedCount = 0;
			base.videos = videos;
			base.bufferingQueue = [];
			base.readyQueue = [];
			base.isPlaying = false;
			
			base.options = $.extend({},$.infiniteVideo.defaultOptions, options);
		};
		
		base.preload = function(url) {
			var $div = $("<div />");
			var id = "infiniteVideo" + base.id + "_" + base.loadedCount;
			$div.attr('id', id);
			$div.addClass('video');
			base.$el.append($div)
			var youtubeVideo = Popcorn.youtube(
				'#' + id,
				url);
			youtubeVideo.on('loadedmetadata', function(e) {
				base.queue(this);
			});
			youtubeVideo.on('seeked', function(e) {
				base.ready(this);
			});
			youtubeVideo.on('timeupdate', function(e) {
				if(this.iv_endTime == 0) return;
				if(this.currentTime() >= this.iv_endTime)
					base.stop(this);
			});
			youtubeVideo.iv_endTime = 0;
			
			base.loadedCount++;
		}
		
		base.next = function() {
			if(base.readyQueue.length < 2 || base.isPlaying)
				return;
			
			var video = base.readyQueue.shift();
			
			console.log(video.readyState());
			if(video.readyState() < 2){
				base.readyQueue.push(video);
				base.next();
			} else {
				base.play(video);
			}
		}
		
		base.stop = function(video) {
			video.pause();
			base.isPlaying = false;
			console.log("STOPPING");
			base.next();
			base.queue(video);
		}
		
		base.play = function(video) {
			base.isPlaying = true;
			console.log("STARTING");
			video.play();
		}
		
		base.ready = function(video) {
			base.readyQueue.push(video);
			base.next();
		}
		
		base.queue = function(video) {
			var duration = video.duration();
			if(duration < 5) return;
			var start = Math.floor((duration - 5) * Math.random());
			video.iv_endTime = start + 5;
			video.pause(start);
		}
		
		base.start = function(paramaters) {
			base.preload(videos[0].url);
			base.preload(videos[1].url);
			base.preload(videos[2].url);
			base.preload(videos[3].url);
			base.preload(videos[4].url);
		};
		
		// Run initializer
		base.init();
		
		// RELEASE THE KRACKEN
		base.start();
	};

	$.infiniteVideo.defaultOptions = {
	};

	$.fn.infiniteVideo = function(videos, options){
		return this.each(function(){
			(new $.infiniteVideo(this, videos, options));
		});
	};

})(jQuery);