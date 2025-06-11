<?php
require 'db.php';
require 'inc/auth.php';
require 'inc/header.php';

$ticket_id = $_GET['id'] ?? 0;

// Kontrollime, kas pilet kuulub sisseloginud kasutajale
$stmt = $pdo->prepare("SELECT * FROM tickets WHERE id = ? AND user_id = ?");
$stmt->execute([$ticket_id, $_SESSION['user_id']]);
$ticket = $stmt->fetch();

if (!$ticket) {
    die("<p class='text-danger'>Piletit ei leitud või sul puudub sellele ligipääs.</p></body></html>");
}

// Kommentaari lisamine
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    $comment = trim($_POST['comment']);
    if (!empty($comment)) {
        $stmt = $pdo->prepare("INSERT INTO comments (ticket_id, user_id, content) VALUES (?, ?, ?)");
        $stmt->execute([$ticket_id, $_SESSION['user_id'], $comment]);

        // Kui pilet oli "Suletud", siis avame uuesti
        if ($ticket['status'] === 'Suletud') {
            $stmt = $pdo->prepare("UPDATE tickets SET status = 'Töös' WHERE id = ?");
            $stmt->execute([$ticket_id]);
        }

        header("Location: view_ticket.php?id=$ticket_id");
        exit;
    }
}

// Kommentaaride kuvamine
$stmt = $pdo->prepare("SELECT c.*, u.first_name, u.last_name FROM comments c JOIN users u ON c.user_id = u.id WHERE ticket_id = ? ORDER BY created_at ASC");
$stmt->execute([$ticket_id]);
$comments = $stmt->fetchAll();
?>

<h2><?= htmlspecialchars($ticket['subject']) ?> (<?= $ticket['status'] ?>)</h2>
<p><strong>Kategooria:</strong> <?= htmlspecialchars($ticket['category']) ?></p>
<p><?= nl2br(htmlspecialchars($ticket['description'])) ?></p>

<h4>Kommentaarid</h4>
<?php foreach ($comments as $c): ?>
    <div class="border p-2 mb-2">
        <strong><?= htmlspecialchars($c['first_name'] . ' ' . $c['last_name']) ?></strong>
        <small class="text-muted"><?= $c['created_at'] ?></small>
        <p><?= nl2br(htmlspecialchars($c['content'])) ?></p>
    </div>
<?php endforeach; ?>

<?php if ($ticket['status'] !== 'Lahendatud'): ?>
    <h4>Lisa kommentaar</h4>
    <form method="post">
        <textarea name="comment" class="form-control mb-2" rows="3" required></textarea>
        <button type="submit" class="btn btn-primary">Saada</button>
    </form>
<?php else: ?>
    <p class="text-muted">Pilet on lahendatud. Kui soovid selle uuesti avada, lisa kommentaar.</p>
<?php endif; ?>

</body></html>
