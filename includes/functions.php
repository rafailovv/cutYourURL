<?php
include_once "config.php";

function get_url($page = '')
{
    return HOST . "/$page";
}

function db() {
    try {
        return new PDO("mysql:host=".DB_HOST."; dbname=".DB_NAME."; charset=utf8", DB_USER, DB_PASSWORD, [
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
    } catch(PDOException $e) {
        die($e->getMessage());
    }
}

function db_query($sql = '', $exec = false) {
    if (empty($sql)) return false;

    if ($exec) {
        return db()->exec($sql);
    }

    return db()->query($sql);
}

function get_users_count() {
    return db_query("SELECT COUNT(`id`) FROM `users`;")->fetchColumn();
}

function get_links_count() {
    return db_query("SELECT COUNT(`id`) FROM `links`;")->fetchColumn();
}

function get_views_count() {
    return db_query("SELECT SUM(`views`) FROM `links`;")->fetchColumn();
}

function get_user_info($login) {
    if (empty($login)) return [];

    return db_query("SELECT * FROM `users` WHERE `login` = '$login';")->fetch();
}

function get_link_info($url) {
    if (empty($url)) return [];

    return db_query("SELECT * FROM `links` WHERE `short_link` = '$url';")->fetch();
}

function update_views($url) {
    if (empty($url)) return [];

    return db_query("UPDATE `links` SET `views` = `views` + 1 WHERE `short_link` = '$url';", true);
}

function add_user($login, $password) {
    $password = password_hash($password, PASSWORD_DEFAULT);
    db_query("INSERT INTO `users` (`id`, `login`, `password`) VALUES (NULL, '$login', '$password');", true);
}

function register_user($auth_data) {
    if (empty($auth_data) || !isset($auth_data['login']) || empty($auth_data['login']) || !isset($auth_data['password']) || !isset($auth_data['password2'])) {
        return false;
    }
    
    $user = get_user_info($auth_data['login']);
    if (!empty($user)) {
        $_SESSION['error'] = "Пользователь ".$auth_data['login']." уже существует!";
        header("Location: register.php");
        die;
    }

    if ($auth_data['password'] !== $auth_data['password2']) {
        $_SESSION['error'] = "Пароли не совпадают!";
        header("Location: register.php");
        die;
    }

    if (add_user($auth_data['login'], $auth_data['password'])) {
        $_SESSION['success'] = "Регистрация прошла успешно!";
        header("Location: login.php");
        die;
    }
    return true;
}

function login($auth_data) {
    if (empty($auth_data) || !isset($auth_data['login']) || empty($auth_data['login']) || !isset($auth_data['password']) || empty($auth_data['password'])) {
        $_SESSION['error'] = "Логин и пароль не могут быть пустыми!";
        header("Location: login.php");
        die;
    }

    $user = get_user_info($auth_data['login']);
    if (empty($user)) {
        $_SESSION['error'] = "Логин или пароль неверен!";
        header("Location: login.php");
        die;
    }

    if (password_verify($auth_data['password'], $user['password'])) {
        $_SESSION['user'] = $user;
        header("Location: profile.php");
        die;
    } else {
        $_SESSION['error'] = "Логин или пароль неверен!";
        header("Location: login.php");
        die;
    }
}

function get_user_links($user_id) {
    if (empty($user_id)) return [];

    return db_query("SELECT * FROM `links` WHERE `user_id` = $user_id;")->fetchAll();
}

function is_your_link($link_id, $user_id) {
    $link = db_query("SELECT * FROM `links` WHERE `id` = $link_id;")->fetchAll();

    if ($link[0]['user_id'] !== $user_id) return false;

    return true;
}

function delete_link($link_id) {
    if (empty($link_id)) return false;

    return db_query("DELETE FROM `links` WHERE `id` = $link_id;", true);
}

function add_link($user_id, $link) {
    $short_link = generate_string();
    return db_query("INSERT INTO `links` (`id`, `user_id`, `long_link`, `short_link`, `views`) VALUES (NULL, '$user_id', '$link', '$short_link', 0);", true);
}

function generate_string($size = 6) {
    $new_string = str_shuffle(URL_CHARS);
    return substr($new_string, 0, 6);
}