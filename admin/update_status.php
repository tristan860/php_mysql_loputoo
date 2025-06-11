<?php
require '../db.php';
require '../inc/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ticket_id = $_POST['ticket_id'];
    $status = $_POST['status'];

    $stmt = $pdo->prepare("UPDATE tickets SET status = ? WHERE id = ?");
    $stmt->execute([$status, $ticket_id]);

    header("Location: reply_ticket.php?id=$ticket_id");
}
?>