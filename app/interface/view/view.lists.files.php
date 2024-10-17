<div _table="list" wide_ switch_ style="--columns: minmax(192px, auto) repeat(2, minmax(96px, max-content));" data-sync="files_list">
	<div header_>
		<div>
			<div _grid="v stacked">
				<select data-sync-ref="list">
					<option value="files_list"><?= D['string_files']; ?></option>
				</select>
				<div _grid="h" style="font-weight: normal;">
					<div><?= D['string_count']; ?><div _badge><?= $object->files_count; ?></div></div>
					<div><?= D['string_size']; ?><div _badge><?= template_formatSize($object->files_size); ?></div></div>
					<? if($object->files_length > 0) { ?>
						<div><?= D['string_length']; ?><div _badge><?= template_formatLength($object->files_length); ?></div></div>
					<? } ?>
					<a href="/filelist/<?= $object->id; ?>.urls"><?= D['link_file_list']; ?></a>
					<? if($object->files_length > 0) { ?>
						<a href="/playlist/<?= $object->id; ?>.m3u"><b><?= D['link_play_list']; ?></b></a>
						<a href="/playlist/<?= $object->id; ?>.xspf">XSPF</a>
					<? } ?>
				</div>
			</div>
		</div>
		<? if($object->files_length > 0) { ?>
			<div></div>
			<div>
				<button onclick="Viewer.toggleFirstMediaClose();"><?= D['button_play']; ?></button>
			</div>
		<? } ?>
	</div>
	<? $i = 1; foreach($object->uploads as $upload) {
		$file = $upload->file;

		if(!in_array($file->size, $file->upload_offsets)) {
			continue;
		}

		$file_title = $upload->title;
		$file_title_truncated = mb_strimwidth(pathinfo($file_title, PATHINFO_FILENAME), 0, 96, '...');
		$extension = pathinfo($file_title, PATHINFO_EXTENSION);
		$mime_type = $file->mime_type ?? '';
		$width = $file->width ?? null;
		$height = $file->height ?? null;
		$length = $file->length ?? null;
		$latitude = $file->latitude ?? null;
		$longitude = $file->longitude ?? null;
	?>
		<div
			<? if(!empty($mime_type)) { ?>
				data-viewer-id="<?= $upload->id; ?>"
				data-viewer-title="<?= $file_title_truncated; ?>"
				data-viewer-type="<?= strstr($mime_type, '/', true); ?>"
			<? } ?>
		>
			<div>
				<div _icon="floppy"></div>
				<div _grid="v stacked">
					<div fallback_><?= $i; ?></div>
					<a href="/get/<?= $upload->id; ?>" title="<?= e($file_title); ?>"><?= $file_title_truncated.(strlen($extension) > 0 ? '<div _badge>.'.$extension.'</div>' : ''); ?></a>
				</div>
			</div>
			<div _flex="v center">
				<? if(str_starts_with($mime_type, 'image/')) { ?>
					<a onclick="Viewer.toggleClose(this);" title="<?= e($file_title.' - '.$referred_title); ?>"><img _image src="/get/<?= $upload->id; ?>"></a>
				<? } ?>
				<? if(str_starts_with($mime_type, 'video/')) { ?>
					<button onclick="Viewer.toggleClose(this);"><?= D['button_play']/*.'<div _badge>currentPlaybackTime</div>'*/; ?></button>
				<? } ?>
			</div>
			<div _flex="v right" fallback_>
				<b><?= template_formatSize($file->size); ?></b>
				<div _flex="v right stacked">
					<div><?= template_formatTime($file->edit_time); ?></div>
					<a href="/search?query=md5:<?= $file->md5; ?>"><?= $file->md5; ?></a>
					<? if(!empty($mime_type)) { ?>
						<div><?= strtoupper(explode('/', $mime_type)[1]).(!empty($width) ? ': '.$width.'x'.$height : '').(!empty($length) ? ', '.template_formatLength($length) : ''); ?></div>
					<? } ?>
				</div>
				<div _grid="h">
					<a><img src="/app/image/file_button.svg"></img></a>
					<a _button href="/load/<?= $upload->id; ?>"><?= D['button_download'].($upload->downloads_count > 0 ? '<div _badge>'.$upload->downloads_count.'</div>' : ''); ?></a>
					<? if($file->size >= 1000000000) { ?>
						<a _button href="/torrent/<?= $upload->id; ?>"><?= D['button_torrent']; ?></a>
					<? } ?>
					<a _button href="/copy/<?= $upload->id; ?>"><?= D['button_copy']; ?></a>
				</div>
				<? if(!empty($latitude) && !empty($longitude)) { ?>
					<div _grid="h">
						<a _button href="http://maps.google.com/maps?q=<?= $latitude; ?>,<?= $longitude; ?>"><?= D['button_view_on_map']; ?></a>
					</div>
				<? } ?>
				<div _flex="h wrap">
					<? foreach($file->upload_offsets as $fs_id => $upload_offset) {
						if($upload_offset >= $file->size) { ?>
							<a href="/load/<?= $upload->id; ?>?fs_id=<?= $fs_id; ?>">FS<?= $fs_id; ?></a>
						<? } else { ?>
							<a disabled_>FS<?= $fs_id; ?></a>
						<? }
					} ?>
				</div>
			</div>
		</div>
	<? $i++; } ?>
</div>
<? include 'plugin/viewer.php'; ?>