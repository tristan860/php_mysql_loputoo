<?php
require 'db.php';
require 'inc/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject = trim($_POST['subject']);
    $desc = trim($_POST['description']);
    $cat = $_POST['category'];

    // Kontroll topeltpileti vältimiseks
    $check = $pdo->prepare("SELECT COUNT(*) FROM tickets WHERE user_id = ? AND subject = ? AND created_at > NOW() - INTERVAL 10 MINUTE");
    $check->execute([$_SESSION['user_id'], $subject]);
    if ($check->fetchColumn() > 0) {
        echo "Sarnane pilet on juba esitatud.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO tickets (user_id, category, subject, description) VALUES (?, ?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $cat, $subject, $desc]);
        echo "Pilet esitatud!";
    }
}
?>

<form method="post">
    <input name="subject" placeholder="Teema">
    <textarea name="description" placeholder="Kirjeldus"></textarea>
    <select name="category">
        <option>IT</option>
        <option>Personal</option>
        <option>Haldus</option>
    </select>
    <button type="submit">Esita pilet</button>
</form>
