<footer>
	<section>
		<?
			$allow_advanced_control = Session::getSetting('allow_advanced_control');

			if(!empty($object) && ($object->access_level_id >= 4 || $object->access_level_id > 0 && $allow_advanced_control)) { ?>
				<a href="/visits/<?= $object->id; ?>" title="<?= D['link_hits_hosts_guests_tooltip']; ?>"><?= "$object->hits_count-$object->hosts_count/$object->guests_count"; ?></a>
			<? }
			if($allow_advanced_control) { ?>
				<div><?= D['string_stats_of']; ?></div>
				<a href="/users_stats"><?= D['link_users_stats']; ?></a>
				<a href="/objects_stats"><?= D['link_objects_stats']; ?></a>
				<a href="/banners_stats"><?= D['link_banners_stats']; ?></a>
				<a href="/fs_stats"><?= D['link_fs_stats']; ?></a>
				<a href="/moderation"><?= D['link_moderation']; ?></a>
				<a href="/claims"><?= D['link_claims']; ?></a>
			<? }
		?>
	</section>
	<section>
		<div>© <?= D['string_app_title']; ?></div>
		<a href="/contacts"><?= D['link_contacts']; ?></a>
		<a href="/copyright"><?= D['link_copyright']; ?></a>
		<form action="/language" method="get">
			<select name="l" onchange="this.form.submit();">
				<? foreach($languages as $language_) { ?>
					<option value="<?= $language_; ?>" <?= $language_ == $language ? 'selected' : ''; ?>><?= D['string_language_'.$language_]; ?></option>
				<? } ?>
			</select>
		</form>
	</section>
</footer>