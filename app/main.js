$(() => {
	window.Header = class {
		static selector = 'header';
		static viewURL = '/view';

		static menu(URL) {
			$(this.selector+` [__menu] a`).removeAttr('current_').filter(`[href="${URL}"]`).attr('current_', '');
		}

		static get(element) {
			fetch(this.viewURL, {
				method: 'HEAD'
			}).then(response => {
				if(response.ok) {
					window.location.href = this.viewURL;
				} else {
					$(element).attr('disabled_', '').closest(this.selector+' [__get]').attr('__get', 'wrong');
					setTimeout(() =>
						$(element).removeAttr('disabled_').closest(this.selector+' [__get]').attr('__get', '')
					, 500);
				}
			});
		}

		static getInput(element) {
			element.value = element.value.replaceAll(/[^0-9]/g, '');
			this.viewURL = '/'+element.value;
		}
	}

	window.Footer = class {
		static selector = 'footer';

		static menu(URL) {
			$(this.selector+` a`).removeAttr('current_').filter(`[href="${URL}"]`).attr('current_', '');
		}
	}

	Header.menu(window.location.pathname);
	Footer.menu(window.location.pathname);

	$(document).on('keydown', function(e) {
		if(e.ctrlKey) {
			let code = e.keyCode ? e.keyCode : e.which ? e.which : null,
				selector;

			switch(code) {
				case 37: selector = 'previous';	break;
				case 38: selector = 'up';		break;
				case 39: selector = 'next';		break;
				case 40: selector = 'down';		break;
			}

			if(selector != null) {
				let link = $('[data-navigate="'+selector+'"]').last().attr('href');

				if(link != null) {
					e.preventDefault();

					window.location.href = link;
				}
			}
		}
	});

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