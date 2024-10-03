<?
	// Outer: object, allow_advanced_control

	$allow_advanced_control ??= Session::getSetting('allow_advanced_control');

	/*
	$rows = [
		[
			[
				['/link1', 'Button 1', 1],
				['/link2', 'Button 2'],
				['/link3', 'Button 3', 3]
			],
			[
				['/link4', 'Button 4'],
				['/link5', 'Button 5', 5, 'alert();']
			]
		],
		[
			[
				['/link6', 'Button 6']
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
				<? foreach($columns as $column) { ?>
					<a _button href="<?= $column[0]; ?>" <?= isset($column[3]) ? 'onclick="'.$column[3].'"' : ''; ?>><?= $column[1].(isset($column[2]) ? '<div _badge>'.$column[2].'</div>' : ''); ?></a>
				<? } ?>
			</div>
		<? } ?>
		<div>&nbsp;</div>
	<? }

	echo $post_actions_content;
?>