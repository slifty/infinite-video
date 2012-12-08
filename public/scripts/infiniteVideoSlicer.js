(function($) {
	$.infiniteVideoSlicer = function(el, videos, options) {
		// To avoid scope issues, use 'base' instead of 'this'
		// to reference this class from internal events and functions.
		var base = this;

		// Access to jQuery and DOM versions of element
		base.$el = $(el);
		base.el = el;
		base.isStarted = false;
		base.cur = 0;

		// Add a reverse reference to the DOM object
		base.$el.data("infiniteVideoSlicer", base);
		
		base.init = function() {
			if( typeof( videos ) === "undefined" || videos === null ) videos = [];
			
			base.results = $("<textarea />").attr('id', 'results').attr('disabled','true');
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
						.bind('click',{video: video, index: x}, function(ev) {
							base.load(ev.data.video);
							base.cur = ev.data.index;
						}
				));
			}
			$(document).keypress(function(e) {
				if(!base.active) return;
				switch(e.charCode) {
					case 101: // e
						if(base.isStarted)
							base.stop();
						else
							base.start();
						break;
					case 113: // q
					case 119: // w
						if(base.isStarted)
							base.stop();
						base.start();
						break;
					case 112: // p
						base.restart();
						break;
					case 91: // [
						base.prev();
						break;
					case 93: // ]
						base.next();
						break;
				}
			});
		};
		base.load = function(data) {
			if(base.active) base.unload(base.active);
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
		
		base.restart = function() {
			var data = base.active.ivs_data;
			base.load(data);
		}
		
		base.start = function() {
			if(base.isStarted) base.stop();
			base.isStarted = true;
			base.results.append("INSERT INTO clips (id, video_id, start, stop) VALUES (0, " + base.active.ivs_data.id + "," + base.active.currentTime());
			base.results.scrollTop(base.results[0].scrollHeight);
		}
		
		base.stop = function(video) {
			if(!base.isStarted) return;
			base.isStarted = false;
			base.results.append("," + base.active.currentTime() + ");\n");
			base.results.scrollTop(base.results[0].scrollHeight);
		}

		base.stopstart = function() {
			base.stop();
			base.start();
		}
		
		base.prev = function() {
			base.cur = Math.max(0, base.cur - 1);
			base.load(base.videos[base.cur]);
		}
		
		base.next = function() {
			base.cur = Math.min(base.videos.length, base.cur + 1);
			base.load(base.videos[base.cur]);
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