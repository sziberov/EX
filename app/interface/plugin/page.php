<?
	[$page_type, $page_module] = route_getPageInterface($page);

	ob_start();

	if($page_type == 'request') {
		include "$page_type/$page_module.php";
	} else
	if($page == '') {
		include 'generic/files.php';
	} else
	if(!empty($page_type) && !empty($page_module)) {
		include "$page_type/$page_module.php";
	} else {
		include 'view/view.php';
	}

	$page_content = ob_get_clean();

	if(!empty($page_content)) { ?>
		<!DOCTYPE html>
		<html lang="<?= $language; ?>">
			<?
				include 'head.php';
				include 'body.php';
			?>
		</html>
	<? }
?>