(function($) {
	$.infiniteVideoSlicer = function(el, videos, options) {
		// To avoid scope issues, use 'base' instead of 'this'
		// to reference this class from internal events and functions.
		var base = this;

		// Access to jQuery and DOM versions of element
		base.$el = $(el);
		base.el = el;
		base.isStarted = false;

		// Add a reverse reference to the DOM object
		base.$el.data("infiniteVideoSlicer", base);
		
		base.init = function() {
			if( typeof( videos ) === "undefined" || videos === null ) videos = [];
			
			base.results = $("<textarea />").attr('id', 'results');
			base.cinema = $("<div />").attr('id', 'cinema');
			base.list = $("<ul />").attr('id', 'videos');
			base.$el.append(base.list);
			base.$el.append(base.cinema);
			base.$el.append(base.results);
			base.videos = videos;
			base.options = $.extend({},$.infiniteVideoSlicer.defaultOptions, options);
			
			for(var x in base.videos) {
				var video = base.videos[x];
				base.list.append(
					$("<li>").html("<b>" + video.id + ") </b>" + video.url)
						.bind('click',{video: video}, function(ev) {
							base.unload(base.active);
							base.load(ev.data.video);
						}
				));
			}
			$(document).keypress(function(e) {
				if(!base.active) return;
				switch(e.charCode) {
					case 113: // q
					case 119: // w
						if(base.isStarted)
							base.stop();
						else
							base.start();
						break;
					case 101: // e
						if(base.isStarted)
							base.stop();
						base.start();
						break;
				}
			});
		};
		base.load = function(data) {
			base.results.append("DELETE FROM clips WHERE clips.id = " + data.id + ";\n");
			var url = data.url;
			var $div = $("<div />");
			id = "video";
			$div.attr('id', id);
			$div.addClass('video');
			base.cinema.append($div)
			var video = Popcorn.youtube(
				'#' + id,
				url);
			video.play();
			video.ivs_data = data;
			base.active = video;
			base.start();
		}
		
		base.unload = function(video) {
			if(!video)
				return;
			
			var data = video.iv_data;
			video.destroy();
			$('#' + video.iv_id).remove();
			if(base.isStarted) base.stop();
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
		
		base.start = function() {
			base.isStarted = true;
			base.results.append("INSERT INTO clips (id, video_id, start, stop) VALUES (0, " + base.active.ivs_data.id + "," + base.active.currentTime());
		}
		
		base.stop = function(video) {
			base.isStarted = false;
			base.results.append("," + base.active.currentTime() + ");\n");
		}

		base.stopstart = function() {
			base.stop();
			base.start();
		}
		
		// Run initializer
		base.init();
	}

	$.infiniteVideoSlicer.defaultOptions = {
	};

	$.fn.infiniteVideoSlicer = function(videos, options){
		return this.each(function(){
			(new $.infiniteVideoSlicer(this, videos, options));
		});
	};

})(jQuery);