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
			base.videos = videos; // a list of video data arrays that have not been loaded as videos
			base.readyQueue = []; // a list of videos that are loaded and ready to play
			base.isPlaying = false;
			
			base.options = $.extend({},$.infiniteVideo.defaultOptions, options);
		};
		
		base.load = function(data) {
			if(!data)
				return;
			
			if(typeof(data) == "number") {
				for(var x=0 ; x<data ; x++) {
					base.load(base.videos.shift());
				}
				return;
			}
			
			var url = data.url;
			var $div = $("<div />");
			var id = "infiniteVideo" + base.id + "_" + base.loadedCount;
			$div.attr('id', id);
			$div.addClass('video');
			base.$el.append($div)
			var video = Popcorn.youtube(
				'#' + id,
				url+'&controls=0');
			video.on('loadedmetadata', function(e) {
				base.queue(this);
			});
			video.on('seeked', function(e) {
				base.ready(this);
			});
			video.on('timeupdate', function(e) {
				if(this.iv_endTime == 0) return;
				if(this.currentTime() >= this.iv_endTime && this.iv_payload) {
					this.iv_payload = false;
					base.stop(this);
					base.next();
				}
			});
			video.iv_endTime = 0;
			video.iv_data = data;
			video.iv_id = id;
			video.iv_payload = true;
			base.loadedCount++;
		}
		
		base.unload = function(video) {
			var data = video.iv_data;
			video.destroy();
			$('#' + video.iv_id).remove();
			base.videos.push(data);
		}
		
		base.next = function() {
			if(base.readyQueue.length < 2 || base.isPlaying)
				return;
			var video = base.readyQueue.shift();
			if(video.readyState() < 2) {
				base.readyQueue.push(video);
				base.next();
			} else {
				base.play(video);
			}
		}
		
		base.stop = function(video) {
			base.isPlaying = false;
			$('#'+video.iv_id).removeClass('active');
			setTimeout(function() {
				video.pause();
				base.unload(video);
			},400);
		}
		
		base.play = function(video) {
			base.isPlaying = true;
			$('#'+video.iv_id).addClass('active');
			video.play();
			base.load(1);
		}
		
		base.ready = function(video) {
			base.readyQueue.push(video);
			base.next();
		}
		
		base.queue = function(video) {
			var duration = video.duration();
			if(duration < 5) return;
			var start = Math.floor((duration - 10) * Math.random());
			video.iv_endTime = start + 10;
			video.pause(start);
		}
		
		base.start = function() {
			base.load(10); // Load 10 videos to start
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