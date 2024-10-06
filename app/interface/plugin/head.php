<head>
	<title><?= dictionary_getPageTitle(e($page_title ?? '')); ?></title>
	<meta name="description" content="<?= dictionary_getPageDescription(e($page_description ?? '')); ?>">
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
	<meta http-equiv="content-language" content="<?= $language; ?>">
	<link href="/app/image/favicon.png" rel="icon" type="image/png">
	<link href="/app/main.css" rel="stylesheet">
	<script src="/app/plugin/jquery-3.6.0.min.js"></script>
	<script src="/app/main.js"></script>
</head>