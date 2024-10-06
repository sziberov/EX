<?
	if($page != 'login') {
		http_response_code(302);
		$error = D['error_login_required'];
	}

	if(!empty($_POST)) {
		$login = $_POST['login'];
		$password = $_POST['password'];
		$logged_in = Session::login($login, $password);

		if($logged_in) {
			$location = $page == 'login' ? '/' : $_SERVER['REQUEST_URI'];

			exit(header('Location: '.$location));
		}

		$error = D['error_login_is_incorrect'];
	}

	$page_title = D['title_login'];
?>
<form _table style="--columns: repeat(3, minmax(0, max-content));" method="post">
	<div>
		<div></div>
		<div _title centered_><?= D['title_login']; ?></div>
		<div></div>
	</div>
	<? if(!empty($error)) { ?>
		<div>
			<div></div>
			<div spanned_>
				<div _icon="error"></div>
				<div _title="small"><?= $error; ?></div>
			</div>
			<div></div>
		</div>
	<? } ?>
	<div>
		<div><?= D['string_login']; ?></div>
		<div>
			<input name="login" type="text" value="<?= $login ?? 'system'; ?>">
		</div>
		<div centered_>
			<a _button href="/registration"><?= D['button_registration']; ?></a>
		</div>
	</div>
	<div>
		<div><?= D['string_password']; ?></div>
		<div>
			<input name="password" type="password" value="<?= $password ?? 'system'; ?>">
		</div>
		<div centered_>
			<a _button href="/password"><?= D['button_password_recovery']; ?></a>
		</div>
	</div>
	<div>
		<div><?= D['string_type']; ?></div>
		<div _flex="v stacked left">
			<label _check>
				<input name="permanent" type="checkbox">
				<div></div>
				<div><?= D['string_permanent']; ?></div>
			</label>
			<label _check>
				<input name="ip_independent" type="checkbox">
				<div></div>
				<div><?= D['string_ip_independent']; ?></div>
			</label>
		</div>
		<div></div>
	</div>
	<div>
		<div></div>
		<div>
			<button primary_ type="submit"><?= D['button_login']; ?></button>
		</div>
		<div></div>
	</div>
</form>