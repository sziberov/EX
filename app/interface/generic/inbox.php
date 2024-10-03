<title>[title_inbox]</title>
<div _grid="h spaced">
	<div _title>[title_inbox]</div>
	<div _flex="h">
		<button onclick="Hash.set('outbox')">[button_outbox]</button>
	</div>
</div>
<module title="objects" variables="filters=user_id\\=[user_id]&type=\\inbox;navigation=true;mode=list"></module>
<div _grid="h">
	<button>[button_delete_all]</button>
</div>