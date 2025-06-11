<?php
require '../db.php';
require '../inc/auth.php';

$ticket_id = $_GET['id'] ?? 0;

// Kontrollime, kas kasutaja on töötaja
$stmt = $pdo->prepare("SELECT is_staff FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
if (!$stmt->fetchColumn()) {
    die("Ligipääs keelatud.");
}

$stmt = $pdo->prepare("SELECT tickets.*, users.first_name, users.last_name FROM tickets JOIN users ON tickets.user_id = users.id WHERE tickets.id = ?");
$stmt->execute([$ticket_id]);
$ticket = $stmt->fetch();

if (!$ticket) {
    die("Piletit ei leitud.");
}

// Kommentaaride lisamine
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    $text = trim($_POST['comment']);
    $stmt = $pdo->prepare("INSERT INTO comments (ticket_id, user_id, content) VALUES (?, ?, ?)");
    $stmt->execute([$ticket_id, $_SESSION['user_id'], $text]);
    header("Location: reply_ticket.php?id=$ticket_id");
    exit;
}

// Kõik kommentaarid
$stmt = $pdo->prepare("SELECT c.*, u.first_name, u.last_name FROM comments c JOIN users u ON c.user_id = u.id WHERE c.ticket_id = ? ORDER BY created_at ASC");
$stmt->execute([$ticket_id]);
$comments = $stmt->fetchAll();
?>

<h2><?= htmlspecialchars($ticket['subject']) ?> (<?= $ticket['status'] ?>)</h2>
<p><?= nl2br(htmlspecialchars($ticket['description'])) ?></p>
<p>Esitaja: <?= htmlspecialchars($ticket['first_name'] . ' ' . $ticket['last_name']) ?></p>

<h3>Muuda staatust</h3>
<form method="post" action="update_status.php">
    <input type="hidden" name="ticket_id" value="<?= $ticket_id ?>">
    <select name="status">
        <option <?= $ticket['status'] === 'Uus' ? 'selected' : '' ?>>Uus</option>
        <option <?= $ticket['status'] === 'Töös' ? 'selected' : '' ?>>Töös</option>
        <option <?= $ticket['status'] === 'Lahendatud' ? 'selected' : '' ?>>Lahendatud</option>
        <option <?= $ticket['status'] === 'Suletud' ? 'selected' : '' ?>>Suletud</option>
    </select>
    <button type="submit">Uuenda</button>
</form>

<h3>Kommentaarid</h3>
<?php foreach ($comments as $c): ?>
    <div>
        <strong><?= htmlspecialchars($c['first_name'] . ' ' . $c['last_name']) ?></strong> (<?= $c['created_at'] ?>):<br>
        <?= nl2br(htmlspecialchars($c['content'])) ?>
    </div>
<?php endforeach; ?>

<h3>Lisa vastus</h3>
<form method="post">
    <textarea name="comment" rows="4" cols="50"></textarea><br>
    <button type="submit">Saada</button>
</form>
