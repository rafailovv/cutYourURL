<?php
include_once "./includes/functions.php";

if (isset($_SESSION['user']['id']) && !empty($_SESSION['user']['id'])) {
	header("Location: profile.php");
	die;
}

$error = '';
if (isset($_SESSION['error']) && !empty($_SESSION['error'])) {
	$error = $_SESSION['error'];
	$_SESSION['error'] = '';
}

$success = '';
if (isset($_SESSION['success']) && !empty($_SESSION['success'])) {
	$success = $_SESSION['success'];
	$_SESSION['success'] = '';
}

if (isset($_POST['login']) && !empty($_POST['login']) && isset($_POST['password']) && !empty($_POST['password'])) {
	login($_POST);
}

include_once "./includes/header.php";
?>

<main class="container">
	<? if (!empty($success)): ?>

		<div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
			<?= $success; ?>
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
		</div>

	<? endif; ?>

	<? if (!empty($error)): ?>

		<div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
			<?= $error; ?>
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
		</div>

	<? endif; ?>

	<div class="row mt-5">
		<div class="col">
			<h2 class="text-center">Вход в личный кабинет</h2>
			<p class="text-center">Если вы еще не зарегистрированы, то самое время <a href="<?= get_url("register.php"); ?>">зарегистрироваться</a></p>
		</div>
	</div>
	<div class="row mt-3">
		<div class="col-4 offset-4">
			<form action="" method="POST">
				<div class="mb-3">
					<label for="login-input" class="form-label">Логин</label>
					<input type="text" class="form-control is-valid" id="login-input" name="login" required>
				</div>
				<div class="mb-3">
					<label for="password-input" class="form-label">Пароль</label>
					<input type="password" class="form-control is-invalid" id="password-input" name="password" required>
				</div>
				<button type="submit" class="btn btn-primary">Войти</button>
			</form>
		</div>
	</div>
</main>

<?php include_once "./includes/footer.php"; ?>