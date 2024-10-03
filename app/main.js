$(() => {
	window.Header = class {
		static selector = 'header';
		static viewURL = '/view';

		static menu(a) {
			$(this.selector+` [__menu] a`).removeAttr('current_').filter(`[href="${a}"]`).attr('current_', '');
		}

		static get(a) {
			fetch(this.viewURL, {
				method: 'HEAD'
			}).then(response => {
				if(response.ok) {
					window.location.href = this.viewURL;
				} else {
					$(a).attr('disabled_', '').closest(this.selector+' [__get]').attr('__get', 'wrong');
					setTimeout(() =>
						$(a).removeAttr('disabled_').closest(this.selector+' [__get]').attr('__get', '')
					, 500);
				}
			});
		}

		static getInput(a) {
			a.value = a.value.replaceAll(/[^0-9]/g, '');
			this.viewURL = '/'+a.value;
		}
	}

	window.Footer = class {
		static selector = 'footer';

		static menu(a) {
			$(this.selector+` a`).removeAttr('current_').filter(`[href="${a}"]`).attr('current_', '');
		}
	}

	window.Editor = class {
		static selector = 'textarea[name="description"]';

		static addBB(key, text, value) {
			let editor = $(this.selector)[0],
				start = editor.selectionStart,
				end = editor.selectionEnd,
				selectedText = '';

			if(start !== end) {
				selectedText = editor.value.substring(start, end);
			}

			let insertText = selectedText || (text ?? ''),
				bbOpen = '['+key+(value ? '='+value : '')+']',
				bbClose = '[/'+key+']',
				finalText = bbOpen+insertText+bbClose;

			editor.setRangeText(finalText, start, end, 'end');
			editor.dispatchEvent(new Event('input', { bubbles: true }));
			editor.focus();
		}
	}

	window.Viewer = class {
		static attribute = '_viewer';
		static uploadID;

		static toggleMinimize() {
			let state = $('['+this.attribute+']').attr(this.attribute).split(' ').filter(v => v !== '');

			if(state.includes('minimized')) {
				state = state.filter(v => v !== 'minimized');
				$('['+this.attribute+'~="video"] video')[0]?.play();
			} else {
				state.push('minimized');
				$('['+this.attribute+'~="video"] video')[0]?.pause();
			}

			$('['+this.attribute+']').attr(this.attribute, state.join(' '));
		}

		static toggleClose(element) {
			let state = $('['+this.attribute+']').attr(this.attribute).split(' ').filter(v => v !== ''),
				closed = state.includes('closed'),
				element_ = element != null ? $(element).parents('[data-viewer-id]') : null,
				uploadID = element_?.attr('data-viewer-id'),
				uploadIDChanged = uploadID != null && uploadID !== this.uploadID,
				type = element_?.attr('data-viewer-type');

			if(closed || uploadIDChanged) {
				if(closed) {
					state = state.filter(v => v !== 'closed' && v !== 'minimized');
				}
				if(type != null) {
					state = state.filter(v => v !== 'image' && v !== 'video');
					state.push(type);
				}
				if(uploadIDChanged) {
					this.uploadID = uploadID;
					$('['+this.attribute+'] '+type.replace('image', 'img')).attr('src', '/get/'+uploadID);
					$('['+this.attribute+'~="video"] video')[0]?.play();
				}
			} else {
				state.push('closed');
				if($('['+this.attribute+'~="video"] video')[0] != null) {
					$('['+this.attribute+'~="video"] video')[0].pause();
					$('['+this.attribute+'~="video"] video')[0].currentTime = 0;
				}
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

	$('a[href-alt]').on('click keydown', function(e) {
		if(
			e.type === 'click' && e.altKey ||
			e.type === 'keydown' && e.altKey && (e.which === 13 || e.key === 'Enter')
		) {
			e.preventDefault();

			window.location.href = $(this).attr('href-alt');
		}
	});

	$('[data-switch-ref]').on('click keydown', function() {
		$('[data-switch="'+this.dataset.switchRef+'"]').attr('switch_', (i, a) => a !== 'current' ? 'current' : '');
	});

	$('select[data-sync-ref]').on('change', function() {
		let syncRef = this.dataset.syncRef,
			value = this.value,
			values = [],
			html = '';

		$('select[data-sync-ref="'+syncRef+'"]').each(function() {
			for(let option of this.options) {
				if(!values.includes(option.value)) {
					values.push(option.value);
					html += '<option value="'+option.value+'"'+(option.value === value ? ' selected' : '')+'>'+option.text+'</option>';

					$('[data-sync="'+option.value+'"]').attr('switch_', function() {
						return this.dataset.sync === value ? 'current' : '';
					});
				}
			}
		}).html(html).val(value);
	});

	$('select[data-sync-ref]').each(function() {
		this.selectedIndex = this.options.length-1;

		$(this).change();
	});
});
