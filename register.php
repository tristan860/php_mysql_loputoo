<?php
require 'db.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fname = trim($_POST['first_name']);
    $lname = trim($_POST['last_name']);
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $idcode = preg_match('/^\d{11}$/', $_POST['id_code']) ? $_POST['id_code'] : false;
    $pass = $_POST['password'];

    if (!$fname || !$lname || !$email || !$idcode || strlen($pass) < 6) {
        $errors[] = "Palun täida kõik väljad korrektselt.";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = "Selline e-posti aadress on juba registreeritud.";
        } else {
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, personal_code, password_hash) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$fname, $lname, $email, $idcode, $hash]);
            header("Location: login.php");
            exit;
        }
    }
}
?>

<form method="post">
    <input name="first_name" placeholder="Eesnimi">
    <input name="last_name" placeholder="Perekonnanimi">
    <input name="id_code" placeholder="Isikukood">
    <input name="email" placeholder="Email">
    <input name="password" type="password" placeholder="Parool">
    <button type="submit">Registreeru</button>
</form>
<?php foreach ($errors as $e) echo "<p>$e</p>"; ?>
