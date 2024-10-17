$(() => {
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
});