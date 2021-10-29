<?php
include_once 'config.php';
include_once 'functions.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = "Что-то пошло не так!";
    header("Location: /profile.php");
    die;
}

if (is_your_link($_GET['id'], $_SESSION['user']['id'])) {
    delete_link($_GET['id']);
    $_SESSION['success'] = "Ссылка успешно удалена!";
    header("Location: /profile.php");
    die;
}

$_SESSION['error'] = "Что-то пошло не так!";
header("Location: /profile.php");
die;