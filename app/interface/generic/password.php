<?
	$page_title = D['title_password'];
?>
<form _table style="--columns: repeat(2, minmax(0, max-content));" method="post">
	<div>
		<div></div>
		<div _title centered_><?= D['title_password']; ?></div>
	</div>
	<div>
		<div><?= D['string_email']; ?></div>
		<div>
			<input size_="big" name="email" type="text">
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
		<div></div>
		<div>
			<button primary_ type="submit"><?= D['button_request']; ?></button>
		</div>
	</div>
</form>