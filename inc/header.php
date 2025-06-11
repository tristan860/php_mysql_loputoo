<?php
require_once 'auth.php';
require_once __DIR__ . '/../db.php';

// Hangi kasutaja info
$stmt = $pdo->prepare("SELECT first_name, is_staff FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="et">
<head>
    <meta charset="UTF-8">
    <title>Helpdesk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">
    <nav class="mb-4 d-flex justify-content-between">
        <div>
            <a href="/dashboard.php" class="btn btn-outline-primary">Avaleht</a>
            <?php if ($user['is_staff']): ?>
                <a href="/admin/tickets.php" class="btn btn-outline-warning">Admin</a>
            <?php endif; ?>
        </div>
        <div>
            Tere, <?= htmlspecialchars($user['first_name']) ?>! 
            <a href="/logout.php" class="btn btn-outline-danger btn-sm">Logi välja</a>
        </div>
    </nav>
