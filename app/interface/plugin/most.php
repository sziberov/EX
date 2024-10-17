<?
	$most = [
		D['title_most_popular'],
		D['title_most_visited'],
		D['title_most_commented'],
		D['title_most_recommended']
	];

	foreach($most as $most_id => $title) {
		$entities = Entity::search('public_objects o', 'Object_', null, Object_::getMostSearchCondition($most_id), 12)['entities'];

		include 'most.objects.php';
	}
?>