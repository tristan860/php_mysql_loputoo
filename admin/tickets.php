<?php
require '../db.php';
require '../inc/auth.php';

// Kontrollime, kas kasutaja on töötaja
$stmt = $pdo->prepare("SELECT is_staff FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$is_staff = $stmt->fetchColumn();

if (!$is_staff) {
    die("Ligipääs keelatud.");
}

$stmt = $pdo->query("SELECT tickets.*, users.first_name, users.last_name FROM tickets JOIN users ON tickets.user_id = users.id ORDER BY created_at DESC");
$tickets = $stmt->fetchAll();
?>

<h2>Kõik piletid</h2>
<?php foreach ($tickets as $t): ?>
    <div>
        <strong><?= htmlspecialchars($t['subject']) ?></strong> - <?= htmlspecialchars($t['first_name'] . ' ' . $t['last_name']) ?>
        [<?= $t['status'] ?>]
        - <a href="reply_ticket.php?id=<?= $t['id'] ?>">Halda</a>
    </div>
<?php endforeach; ?>
