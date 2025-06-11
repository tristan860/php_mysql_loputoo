<?php
require 'db.php';
require 'inc/auth.php';

$stmt = $pdo->prepare("SELECT * FROM tickets WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$tickets = $stmt->fetchAll();

foreach ($tickets as $t) {
    echo "<p><strong>{$t['subject']}</strong> [{$t['status']}] - <a href='view_ticket.php?id={$t['id']}'>Ava</a></p>";
}
?>
