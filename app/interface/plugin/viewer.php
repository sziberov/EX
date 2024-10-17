<script src="/app/plugin/viewer.js"></script>
<div _viewer="closed">
	<div _grid="h spaced" __header onmousedown="Viewer.move(event);">
		<div _grid="h stacked">
			<button icon_="play" onclick="Viewer.togglePlayback();">
				<div></div>
				<div></div>
			</button>
			<div __title>...</div>
		</div>
		<div _grid="h stacked">
			<button icon_="minimize" onclick="Viewer.toggleMinimize();"></button>
			<button icon_="maximize" onclick="Viewer.toggleMaximize();">
				<div></div>
				<div></div>
				<div></div>
				<div></div>
			</button>
			<button icon_="close" onclick="Viewer.toggleClose();"></button>
		</div>
	</div>
	<div __content>
		<img
			onclick="Viewer.navigateNext();"
			onload="Viewer.hideLoading(); Viewer.enableShowing();"
		>
		<video
			nocontrols
			preload="auto"
			onclick="Viewer.togglePlayback();"
			ondblclick="Viewer.toggleMaximize();"
			onloadstart="Viewer.disableShowing();"
			onwaiting="Viewer.showLoading();"
			onstalled="Viewer.showLoading();"
			oncanplay="Viewer.hideLoading();"
			onplay="Viewer.enableShowing();"
			onplaying="Viewer.enableShowing();"
			ontimeupdate="Viewer.updateTime();"
			onprogress="Viewer.updateTime(true);"
			onpause="Viewer.disableShowing();"
			onended="Viewer.disableShowing();"
			onerror="Viewer.disableShowing();"
			onvolumechange="Viewer.updateVolume();"
			<?= !empty($object->poster) ? 'poster="/get/'.$object->poster->id.'"' : ''; ?>
		>
	</div>
	<div _grid="h spaced" __footer>
		<div _progress="time" onmouseenter="Viewer.seek(event);">
			<div></div>
		</div>
		<div _grid="h">
			<button icon_="play" onclick="Viewer.togglePlayback();">
				<div></div>
				<div></div>
			</button>
			<div __time>0:00 / 0:00</div>
		</div>
		<div _grid="h">
			<!--<div _grid="h" __volume>-->
				<button icon_="volume" onclick="Viewer.toggleVolume();">
					<div></div>
					<div></div>
					<div></div>
					<div></div>
				</button>
				<div _progress="volume" onmouseenter="Viewer.seek(event);" onmousewheel="Viewer.scrollVolume(event);">
					<div style="width: 100%;"></div>
				</div>
			<!--</div>-->
			<button icon_="previous" onclick="Viewer.navigatePrevious();" disabled_></button>
			<button icon_="next" onclick="Viewer.navigateNext();" disabled_></button>
		</div>
	</div>
	<button icon_="resize" onmousedown="Viewer.resize(event);"></button>
</div>