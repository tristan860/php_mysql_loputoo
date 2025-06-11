<?php
require 'db.php';
require 'inc/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ticket_id = $_POST['ticket_id'];
    $text = trim($_POST['comment']);

    $stmt = $pdo->prepare("INSERT INTO comments (ticket_id, user_id, content) VALUES (?, ?, ?)");
    $stmt->execute([$ticket_id, $_SESSION['user_id'], $text]);

    echo "Kommentaar lisatud!";
}
?>

<form method="post">
    <input type="hidden" name="ticket_id" value="<?= $_GET['id'] ?>">
    <textarea name="comment"></textarea>
    <button type="submit">Lisa kommentaar</button>
</form>
