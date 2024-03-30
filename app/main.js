$(() => {
	window.Header = class {
		static selector = 'header';

		static menu(a) {
			$(this.selector+` [__menu] a`).removeAttr('current_');
			$(this.selector+` [__menu] a[href="${a}"]`).attr('current_', '');
		}

		static get(a) {
			$(a).attr('disabled_', '').closest(this.selector+' [__get]').attr('__get', 'wrong');
			setTimeout(() =>
				$(a).removeAttr('disabled_').closest(this.selector+' [__get]').attr('__get', '')
			, 500);
		}

		static getInput(a) {
			a.value = a.value.replaceAll(/[^0-9]/g, '');
		}
	}

	window.Footer = class {
		static selector = 'footer';

		static menu(a) {
			$(this.selector+` a`).removeAttr('current_');
			$(this.selector+` a[href="${a}"]`).attr('current_', '');
		}
	}

	window.Viewer = class {
		static attribute = '_viewer';
		static fileID;

		static toggleMinimize() {
			let state = $('['+this.attribute+']').attr(this.attribute).split(' ').filter(v => v !== '');

			if(state.includes('minimized')) {
				state = state.filter(v => v !== 'minimized');
			} else {
				state.push('minimized');
			}

			$('['+this.attribute+']').attr(this.attribute, state.join(' '));
		}

		static toggleClose(type, fileID) {
			let state = $('['+this.attribute+']').attr(this.attribute).split(' ').filter(v => v !== ''),
				closed = state.includes('closed'),
				fileIDChanged = fileID !== undefined && fileID !== this.fileID;

			if(closed || fileIDChanged) {
				if(closed) {
					state = state.filter(v => v !== 'closed' && v !== 'minimized');
				}
				if(type !== undefined) {
					state = state.filter(v => v !== 'image' && v !== 'video');
					state.push(type);
				}
				if(fileIDChanged) {
					this.fileID = fileID;
				}
			} else {
				state.push('closed');
			}

			$('['+this.attribute+']').attr(this.attribute, state.join(' '));
		}
	}

	Header.menu(window.location.pathname);
	Footer.menu(window.location.pathname);

	document.title = $('title').last().text() ?? '. . .';

	$('a:has(img[_image])').on('mousemove', function(e) {
		let coordinates = this.getBoundingClientRect();

		this.style.setProperty('--x', e.clientX-coordinates.x);
		this.style.setProperty('--y', e.clientY-coordinates.y);
	});

	$('[data-switch-ref]').on('click', function() {
		$('[data-switch="'+this.dataset.switchRef+'"]').attr('switch_', function(i, a) {
			return a !== 'current' ? 'current' : '';
		});
	});

	$('select[data-sync-ref]').on('change', function() {
		let value = this.value;

		if(value === '') {
			return;
		}

		$('select[data-sync-ref="'+this.dataset.syncRef+'"]').html(this.innerHTML).val(value);

		for(let option of this.options) {
			$('[data-sync="'+option.value+'"]').attr('switch_', function() {
				return this.dataset.sync === value ? 'current' : '';
			});
		}
	});

	$(document).ready(() => {
	    $('select[data-sync-ref]').change();
	});
});