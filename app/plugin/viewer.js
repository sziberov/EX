$(() => {
	window.Viewer = class {
		static attribute = '_viewer';
		static ID;
		static controlsTimeoutID;
		static seekElement;

		static getStates() {
			return $('['+this.attribute+']').attr(this.attribute).split(' ').filter(v => v !== '');
		}

		static setStates(states) {
			$('['+this.attribute+']').attr(this.attribute, states.join(' '));
		}

		static addState(state) {
			let states = this.getStates();

			if(!states.includes(state)) {
				states.push(state);
			}

			this.setStates(states);
		}

		static removeState(state) {
			this.setStates(this.getStates().filter(v => v !== state));
		}

		static atState(state) {
			return $('['+this.attribute+'~="'+state+'"]').length > 0;
		}

		static enableFullscreen() {
			return $('['+this.attribute+']')[0].requestFullscreen({
				navigationUI: 'hide'
			});
		}

		static disableFullscreen() {
			if(document.fullscreenElement != null) {
				document.exitFullscreen();
			}
		}

		static toggleMinimize() {
			if(!this.atState('minimized')) {
				if(this.atState('maximized')) {
					this.disableFullscreen();
				}

				this.addState('minimized');
			} else {
				this.removeState('minimized');
				this.rearrange();
			}
		}

		static toggleMaximize() {
			if(!this.atState('maximized')) {
				if(this.atState('minimized')) {
					this.removeState('minimized');
				}

				this.addState('maximized');
				this.enableFullscreen().then(() => {
					$(document).on('fullscreenchange.viewer-fullscreen', () => {
						if(document.fullscreenElement == null) {
							$(document).off('fullscreenchange.viewer-fullscreen');
							this.removeState('maximized');
							this.disableFullscreen();
						}
					});
				});
			} else {
				this.disableFullscreen();
			}
		}

		static toggleClose(element) {
			let closed = this.atState('closed'),
				minimized = this.atState('minimized'),
				element_ = $(element).closest('[data-viewer-id]'),
				ID = element_.attr('data-viewer-id'),
				IDChanged = ID != null && ID !== this.ID;

			if(IDChanged) {
				this.disableShowing();
				this.stopPlayback();
				this.setSettings(ID, element_.attr('data-viewer-title'), element_.attr('data-viewer-type'));
			}

			if(minimized) {
				this.removeState('minimized');
			}
			if(element == null && this.atState('maximized')) {
				this.disableFullscreen();
			}

			if(closed || IDChanged || element != null && minimized) {
				if(closed) {
					this.removeState('closed');
				}
				if(!this.atState('showing')) {
					this.enableShowing();
					this.loadTime();
				}

				this.rearrange();
				this.startPlayback();
			} else {
				this.addState('closed');
				this.disableShowing();
				this.stopPlayback();
			}
		}

		static toggleFirstMediaClose() {
			let element,
				type;

			if(this.ID != null) {
				element = $('[data-viewer-id="'+this.ID+'"]');
				type = element.attr('data-viewer-type');
			}
			if(type !== 'video' && type !== 'audio') {
				element = $('[data-viewer-type="video"], [data-viewer-type="audio"]').first();
			}

			if(element.length > 0) {
				this.toggleClose(element[0]);
			}
		}

		static setSettings(ID, title, type) {
			this.ID = ID;

			this.removeState('image');
			this.removeState('video');
			this.removeState('audio');

			if(ID == null || type !== 'image' && type !== 'video' && type !== 'audio') {
				this.hideLoading();
				$('['+this.attribute+'] [__title]').text('...');
				$('['+this.attribute+']').find('img, video').attr('src', '');
			} else {
				this.addState(type);
				this.showLoading();
				$('['+this.attribute+'] [__title]').text(title);
				$('['+this.attribute+']').find(type === 'image' ? 'img' : 'video').attr('src', '/get/'+ID);
			}

			this.navigateCurrent();
		}

		static resetSettings() {
			this.setSettings();
		}

		static setPlayback(mode) {
			let video = $('['+this.attribute+'~="video"], ['+this.attribute+'~="audio"]').find('video')[0];

			if(video == null) {
				return;
			}

			if(video.paused) {
				if([undefined, 'start'].includes(mode)) {
					video.play().catch(() => {});
				}
			} else {
				if([undefined, 'pause', 'stop'].includes(mode)) {
					video.pause();

					if(mode === 'stop') {
						video.currentTime = 0;
					}
				}
			}
		}

		static togglePlayback() {
			this.setPlayback();
		}

		static startPlayback() {
			this.setPlayback('start');
		}

		static pausePlayback() {
			this.setPlayback('pause');
		}

		static stopPlayback() {
			this.setPlayback('stop');
		}

		static toggleVolume() {
			let video = $('['+this.attribute+'~="video"], ['+this.attribute+'~="audio"]').find('video')[0];

			if(video == null) {
				return;
			}

			if(!video.muted) {
				video.muted = true;
				this.addState('muted');
			} else {
				video.muted = false;
				this.removeState('muted');
			}
		}

		static navigate(mode) {
			let current = $('[data-viewer-id="'+this.ID+'"]'),
				type = current.attr('data-viewer-type'),
				previous = current.prev('[data-viewer-type="'+type+'"]')[0],
				next = current.next('[data-viewer-type="'+type+'"]')[0];

			if(mode === 'previous') {
				this.toggleClose(previous);
			} else
			if(mode === 'next') {
				this.toggleClose(next);
			} else {
				if(previous != null) {
					$('['+this.attribute+'] button[icon_="previous"]').removeAttr('disabled_');
				} else {
					$('['+this.attribute+'] button[icon_="previous"]').attr('disabled_', '');
				}
				if(next != null) {
					$('['+this.attribute+'] button[icon_="next"]').removeAttr('disabled_');
				} else {
					$('['+this.attribute+'] button[icon_="next"]').attr('disabled_', '');
				}
			}
		}

		static navigatePrevious() {
			this.navigate('previous');
		}

		static navigateCurrent() {
			this.navigate();
		}

		static navigateNext() {
			this.navigate('next');
		}

		static formatLength(seconds) {
			let date = new Date(0);

			date.setSeconds(seconds);

			let h = date.getUTCHours(),
				m = date.getUTCMinutes(),
				s = date.getUTCSeconds();

			return (h > 0 ? h+':' : '')+(m < 10 && h > 0 ? '0'+m : m)+':'+(s < 10 ? '0'+s : s);
		}

		static saveTime(ID, seconds) {
			if(seconds > 0) {
				localStorage.setItem('viewer_time_'+ID, seconds);
			} else {
				localStorage.removeItem('viewer_time_'+ID);
			}
		}

		static getTime(ID) {
			return localStorage.getItem('viewer_time_'+ID) || 0;
		}

		static updatePlayButton(ID, seconds) {
			let button = $('[data-viewer-id="'+ID+'"] button[onclick="Viewer.toggleClose(this);"]');

			if(button.length == 0) {
				return;
			}

			let currentTime = seconds ?? this.getTime(ID),
				badge = button.find('[_badge]');

			if(currentTime > 0) {
				if(badge.length == 0) {
					badge = $('<div _badge>').appendTo(button);
				}

				badge.text(this.formatLength(currentTime));
			} else
			if(badge.length > 0) {
				badge.remove();
			}
		}

		static loadTime() {
			let video = $('['+this.attribute+'~="video"], ['+this.attribute+'~="audio"]').find('video')[0];

			if(video == null) {
				return;
			}

			video.currentTime = this.getTime(this.ID) ?? 0;
		}

		static preloadEachTime() {
			$('[data-viewer-id]').filter('[data-viewer-type="video"], [data-viewer-type="audio"]').each(function() {
				let ID = $(this).attr('data-viewer-id');

				Viewer.updatePlayButton(ID);
			});
		}

		static updateTime(bufferOnly) {
			let video = $('['+this.attribute+'~="video"], ['+this.attribute+'~="audio"]').find('video')[0];

			if(video == null) {
				return;
			}

			let progress = $('['+this.attribute+'] [_progress="time"]'),
				duration = video.duration || 0.0001;

			if(!bufferOnly) {
				let playback = progress.find(':first-child'),
					currentTime = video.currentTime,
					percents = currentTime/duration*100;

				playback.css('width', percents+'%');

				let preview = progress.find('[preview_]');

				if(preview.length > 0) {
					let previewLeft = preview.position().left/progress.width()*100,
						previewWidth = preview.width()/progress.width()*100,
						previewPercents = previewLeft+previewWidth;

					if(previewPercents < percents) {
						previewWidth = percents-previewLeft;
					} else {
						previewLeft = percents;
						previewWidth = previewPercents-percents;
					}

					preview.css({
						left: previewLeft+'%',
						width: previewWidth+'%'
					});
				}

				if(this.atState('showing')) {
					let seconds = currentTime > 1 && duration-currentTime > 1 ? currentTime : null;

					this.saveTime(this.ID, seconds);
					this.updatePlayButton(this.ID, seconds);
				}

				let time = $('['+this.attribute+'] [__time]'),
					nowTime = this.formatLength(currentTime),
					endTime = this.formatLength(duration);

				time.text(nowTime+' / '+endTime);
			}

			let buffered = progress.find('[buffered_]'),
				buffer = video.buffered;

			buffered.remove();

			for(let i = 0; i < buffer.length; i++) {
				let bufferedLeft = buffer.start(i)/duration*100,
					bufferedWidth = buffer.end(i)/duration*100-bufferedLeft;

				$('<div buffered_>').appendTo(progress).css({
					left: bufferedLeft+'%',
					width: bufferedWidth+'%'
				});
			}
		}

		static updateVolume() {
			let video = $('['+this.attribute+'~="video"], ['+this.attribute+'~="audio"]').find('video')[0];

			if(video == null) {
				return;
			}

			let progress = $('['+this.attribute+'] [_progress="volume"]'),
				value = progress.find(':first-child'),
				volume = video.volume,
				percents = volume*100;

			value.css('width', percents+'%');

			if(percents >= 33.3) {
				if(percents >= 66.6) {
					this.removeState('volume-medium');
				} else {
					this.addState('volume-medium');
				}

				this.removeState('volume-low');
			} else {
				this.addState('volume-low');
				this.removeState('volume-medium');
			}
		}

		static seek(e) {
			if(this.seekElement === e.target) {
				return;
			}

			let video = $('['+this.attribute+'~="video"], ['+this.attribute+'~="audio"]').find('video')[0];

			if(video == null) {
				return;
			}

			let progress = $(e.target),
				left = progress.offset().left,
				width = progress.width(),
				mode = progress.attr('_progress'),
				preview = progress.find('[preview_]'),
				id = Date.now(),
				mouseDown,
				duration = video.duration || 0.0001;

			if(mode === 'time' && preview.length == 0) {
				preview = $('<div preview_>').appendTo(progress);
			}

			$(document).on(`mousemove.viewer-seek-${id} mousedown.viewer-seek-${id} mouseup.viewer-seek-${id}`, (e) => {
				let cursorIn = progress[0] === e.target || $.contains(progress[0], e.target);

				if(this.seekElement == null && !cursorIn) {
					preview.remove();

					return $(document).off(`mousemove.viewer-seek-${id} mousedown.viewer-seek-${id} mouseup.viewer-seek-${id}`);
				}

				if(e.type === 'mousedown' && e.which === 1) {
					this.seekElement ??= progress[0]
					mouseDown = true;

					preview.hide();
				}

				let cursorLeft = e.clientX,
					cursorWidth = cursorLeft-left,
					cursorPercents = Math.max(0, Math.min(100, cursorWidth/width*100));

				if(e.type === 'mousemove') {
					if(mode === 'time') {
						if(cursorIn && !mouseDown) {
							let percents = video.currentTime/duration*100;

							preview.show()
								   .css({
									   left: Math.min(cursorPercents, percents)+'%',
									   width: Math.abs(cursorPercents-percents)+'%'
								   })
								   .attr('data-time', this.formatLength(cursorPercents/100*duration));
						} else {
							preview.hide();
						}
					}
				}

				if(this.seekElement === progress[0]) {
					e.preventDefault();

					if(mode === 'time') {
						video.currentTime = cursorPercents/100*duration;
					} else
					if(mode === 'volume') {
						video.volume = cursorPercents/100;
					}
				}

				if(e.type === 'mouseup' && e.which === 1) {
					this.seekElement = undefined;
					mouseDown = false;
				}
			});
		}

		static scrollVolume(e) {
			if(this.seekElement != null) {
				return;
			}

			let video = $('['+this.attribute+'~="video"], ['+this.attribute+'~="audio"]').find('video')[0];

			if(video == null) {
				return;
			}

			let direction = Math.max(-1, Math.min(1, e.wheelDelta || -e.detail)),
				step = 0.0625;

			e.preventDefault();

			video.volume = Math.max(0, Math.min(1, video.volume+step*direction));
		}

		static showLoading() {
			this.addState('loading');
		}

		static hideLoading() {
			this.removeState('loading');
		}

		static enableControlsAutohide() {
			if(this.controlsTimeoutID != null) {
				return;
			}

			$('['+this.attribute+']').on('mousemove.viewer-controls', () => {
				this.removeState('controlless');
				clearTimeout(this.controlsTimeoutID);

				this.controlsTimeoutID = setTimeout(() => {
					this.addState('controlless');
				}, 2500);
			}).trigger('mousemove.viewer-controls');
		}

		static disableControlsAutohide() {
			if(this.controlsTimeoutID == null) {
				return;
			}

			$('['+this.attribute+']').off('mousemove.viewer-controls');
			this.removeState('controlless');
			clearTimeout(this.controlsTimeoutID);

			this.controlsTimeoutID = undefined;
		}

		static enableShowing() {
			this.addState('showing');
			this.enableControlsAutohide();
		}

		static disableShowing() {
			this.removeState('showing');
			this.disableControlsAutohide();
		}

		static showError() {
			this.stopPlayback();
		}

		static showContextMenu(event) {
			event.preventDefault();
			// Логика отображения меню по правой кнопке мыши
			let contextMenu = $('<div>').attr('class', 'context-menu');
			contextMenu.append('<div>Данные для отладки</div>');
			contextMenu.append('<div>Версия плеера: 1.0</div>');
			$('body').append(contextMenu);
			contextMenu.css({ top: event.pageY, left: event.pageX });

			$(document).on('click', function() {
				contextMenu.remove();
			});
		}

		static debugInfo() {
			// Окно с данными для отладки
			let debugWindow = $('<div>').attr('class', 'debug-window');
			let videoElement = $('['+this.attribute+'~="video"] video')[0];
			if(videoElement) {
				debugWindow.append('<div>Источник: '+videoElement.src+'</div>');
				debugWindow.append('<div>Буфер: '+videoElement.buffered.end(0)+'</div>');
				debugWindow.append('<div>Громкость: '+videoElement.volume+'</div>');
				debugWindow.append('<div>Последнее событие: '+videoElement.readyState+'</div>');
			}
			$('body').append(debugWindow);
		}

		static setLayout(mode, e) {
			if(mode === 'move' && e.target.tagName.toLowerCase() === 'button') {
				return;
			}

			let viewer = $('['+Viewer.attribute+']:not(['+Viewer.attribute+'~="minimized"], ['+Viewer.attribute+'~="maximized"])');

			if(viewer.length == 0) {
				return;
			}

			let top = viewer.position().top,
				left = viewer.position().left,
				width = viewer.outerWidth(),
				height = viewer.outerHeight(),
				viewportWidth = document.documentElement.clientWidth,
				viewportHeight = document.documentElement.clientHeight;

			if(mode === 'move' || mode === 'resize') {
				let maxTop = viewportHeight-height,
					maxLeft = viewportWidth-width,
					maxWidth = viewportWidth-left,
					maxHeight = viewportHeight-top,
					cursorTop = e.clientY,
					cursorLeft = e.clientX;

				e.preventDefault();

				$(document).on(`mousemove.viewer-${mode} mouseup.viewer-${mode}`, function(e) {
					if(e.type === 'mouseup') {
						return $(this).off(`mousemove.viewer-${mode} mouseup.viewer-${mode}`);
					}

					let cursorHeight = e.clientY-cursorTop,
						cursorWidth = e.clientX-cursorLeft;

					if(mode === 'move') {
						let newTop = Math.min(Math.max(0, top+cursorHeight), maxTop),
							newLeft = Math.min(Math.max(0, left+cursorWidth), maxLeft);

						viewer.css({
							top: newTop+'px',
							left: newLeft+'px'
						});
					} else
					if(mode === 'resize') {
						let newWidth = Math.min(width+cursorWidth, maxWidth),
							newHeight = Math.min(height+cursorHeight, maxHeight);

						viewer.css({
							width: newWidth+'px',
							height: newHeight+'px'
						});
					}
				});
			} else
			if(mode === 'rearrange') {
				top = Math.max(0, Math.min(top, viewportHeight-height));
				left = Math.max(0, Math.min(left, viewportWidth-width));
				width = Math.min(width, viewportWidth);
				height = Math.min(height, viewportHeight);

				viewer.css({
					top: top+'px',
					left: left+'px',
					width: width+'px',
					height: height+'px'
				});
			}
		}

		static move(e) {
			this.setLayout('move', e);
		}

		static resize(e) {
			this.setLayout('resize', e);
		}

		static rearrange() {
			this.setLayout('rearrange');
		}
	}

	/*
	$('['+Viewer.attribute+']').on('contextmenu', function(e) {
		Viewer.showContextMenu(e);
	});
	*/

	if($('['+Viewer.attribute+']').length > 0) {
		Viewer.preloadEachTime();
		$(window).on('resize', () => Viewer.rearrange());
	}
});