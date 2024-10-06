<?
	if(!empty($_POST)) {
		$login = $_POST['login'];
		$password = $_POST['password'];
		$password_repeat = $_POST['password_repeat'];
		$email = $_POST['email'];
		$captcha = $_POST['captcha'];

		if(empty($login)) {
			$error = D['error_login_is_not_set'];
		} else
		if(!preg_match('/^[a-z0-9\-\_]+$/i', $login)) {
			$error = D['error_login_is_misspelled'];
		} else
		//if(in_array($login, $logins)) {
		//	$error = D['error_login_is_already_in_use'];
		//} else
		if(empty($password)) {
			$error = D['error_password_is_not_set'];
		} else
		if(strlen($password) < 6) {
			$error = D['error_password_is_misspelled'];
		} else
		if($password != $password_repeat) {
			$error = D['error_passwords_are_different'];
		} else
		if(empty($email)) {
			$error = D['error_email_is_not_set'];
		} else
		if($email != filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$error = D['error_email_is_misspelled'];
		} else
		//if(in_array(strtolower(($email), $emails)) {
		//	$error = D['error_email_is_already_in_use'];
		//} else
		if(empty($captcha)) {
			$error = D['error_captcha_is_not_set'];
		}

		Object_::createUserID($login, $password, $email);

		exit(header("Location: /user/$login"));
	}

	$page_title = D['title_registration'];
?>
<form _table style="--columns: repeat(2, minmax(0, max-content));" method="post">
	<div>
		<div></div>
		<div _title centered_><?= D['title_registration']; ?></div>
	</div>
	<? if(!empty($error)) { ?>
		<div>
			<div></div>
			<div spanned_>
				<div _icon="error"></div>
				<div _title="small"><?= $error; ?></div>
			</div>
		</div>
	<? } ?>
	<div>
		<div><?= D['string_login']; ?></div>
		<div>
			<input name="login" type="text" value="<?= $login ?? ''; ?>">
			<button><?= D['button_check']; ?></button>
		</div>
	</div>
	<div>
		<div><?= D['string_password']; ?></div>
		<div>
			<input name="password" type="password" value="<?= $password ?? ''; ?>">
		</div>
	</div>
	<div>
		<div><?= D['string_password_repeat']; ?></div>
		<div>
			<input name="password_repeat" type="password" value="<?= $password_repeat ?? ''; ?>">
		</div>
	</div>
	<div>
		<div><?= D['string_email']; ?></div>
		<div>
			<input size_="big" name="email" type="text" value="<?= $email ?? ''; ?>">
		</div>
	</div>
	<div>
		<div><?= D['string_captcha']; ?></div>
		<div _flex="v left">
			<div _captcha="ABCdef123"></div>
			<input size_="big" name="captcha" type="text">
		</div>
	</div>
	<div>
		<div><?= D['string_agreement']; ?></div>
		<div style="max-width: 256px;"><?= D['string_agreement_description']; ?></div>
	</div>
	<div>
		<div></div>
		<div>
			<button primary_ type="submit"><?= D['button_register']; ?></button>
		</div>
	</div>
</form>