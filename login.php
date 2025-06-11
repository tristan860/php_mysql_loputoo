<?php
require 'db.php';
session_start();

if (isset($_COOKIE['remember'])) {
    $_SESSION['user_id'] = $_COOKIE['remember'];
    header("Location: dashboard.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $pass = $_POST['password'];
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($pass, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        if (!empty($_POST['remember'])) {
            setcookie('remember', $user['id'], time() + 14400); // 4h
        }
        header("Location: dashboard.php");
        exit;
    } else {
        echo "Vale e-post või parool.";
    }
}
?>

<form method="post">
    <input name="email" placeholder="E-post">
    <input name="password" type="password" placeholder="Parool">
    <label><input type="checkbox" name="remember"> Mäleta mind</label>
    <button type="submit">Logi sisse</button>
</form>
