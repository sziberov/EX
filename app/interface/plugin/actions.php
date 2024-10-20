<?
	// Outer: object, allow_advanced_control

	$allow_advanced_control ??= Session::getSetting('allow_advanced_control');

	/*
	$rows = [
		[
			[
				['/link1', 'Button 1', ['badge' => 1]],
				['/link2', 'Button 2'],
				['/link3', 'Button 3', ['badge' => 3]]
			],
			[
				['/link4', 'Button 4'],
				['/link5', 'Button 5', ['badge' => 5, 'action' => 'alert();']]
			]
		],
		[
			[
				['/link6', 'Button 6', ['toggled' => true]]
			]
		]
	];
	*/

	ob_start();

	if($object->type_id == 1) include 'actions_group.php';
	if($object->type_id == 2) include 'actions_user.php';
	if($object->type_id == 3) include 'actions_plain.php';
	if($object->type_id == 4) include 'actions_shared.php';

	$post_actions_content = ob_get_clean();

	foreach($rows as $row) {
		foreach($row as $columns) { ?>
			<div _grid="h">
				<? foreach($columns as $column) {
					$url = $column[0];
					$title = $column[1];
					$action = isset($column[2]['action']) ? 'onclick="'.$column[2]['action'].'"' : '';
					$toggled = !empty($column[2]['toggled']) ? 'toggled_' : '';
					$badge = isset($column[2]['badge']) ? '<div _badge>'.$column[2]['badge'].'</div>' : '';
				?>
					<a _button href="<?= $url; ?>" <?= $action.' '.$toggled; ?>><?= $title.$badge; ?></a>
				<? } ?>
			</div>
		<? } ?>
		<div>&nbsp;</div>
	<? }

	echo $post_actions_content;
?>