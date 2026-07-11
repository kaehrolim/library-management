<?php
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../includes/auth.php';
require_login();

$flash = $_SESSION['flash'] ?? '';
unset($_SESSION['flash']);

// Empréstimos ativos DESTE usuário (returned_at IS NULL).
$stmt = $pdo->prepare("
    SELECT l.id AS loan_id, b.title, b.author, l.borrowed_at
    FROM loans l
    JOIN books b ON b.id = l.book_id
    WHERE l.user_id = ? AND l.returned_at IS NULL
    ORDER BY l.borrowed_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$loans = $stmt->fetchAll();

$pageTitle = 'Meus empréstimos';
require __DIR__ . '/../includes/header.php';
?>
<h1>Meus empréstimos ativos</h1>
<?php if ($flash): ?><p class="alert info"><?= e($flash) ?></p><?php endif; ?>
<table class="table">
    <thead><tr><th>Título</th><th>Autor</th><th>Pego em</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($loans as $l): ?>
        <tr>
            <td><?= e($l['title']) ?></td>
            <td><?= e($l['author']) ?></td>
            <td><?= e($l['borrowed_at']) ?></td>
            <td>
                <form method="post" action="/actions/return_action.php" class="inline">
                    <?= csrf_field() ?>
                    <input type="hidden" name="loan_id" value="<?= (int)$l['loan_id'] ?>">
                    <button type="submit" class="secondary">Devolver</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    <?php if (!$loans): ?>
        <tr><td colspan="4" class="muted">Você não tem livros emprestados.</td></tr>
    <?php endif; ?>
    </tbody>
</table>
<?php require __DIR__ . '/../includes/footer.php'; ?>
