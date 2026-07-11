<?php
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../includes/auth.php';
require_login();

$flash = $_SESSION['flash'] ?? '';
unset($_SESSION['flash']);

// Mesmos livros do acervo, mas só faz sentido pegar os que têm cópia disponível.
$sql = "
    SELECT b.id, b.title, b.author,
           (b.total_copies - COALESCE(active.qtd, 0)) AS available
    FROM books b
    LEFT JOIN (
        SELECT book_id, COUNT(*) AS qtd FROM loans
        WHERE returned_at IS NULL GROUP BY book_id
    ) active ON active.book_id = b.id
    ORDER BY b.title
";
$books = $pdo->query($sql)->fetchAll();

$pageTitle = 'Emprestar';
require __DIR__ . '/../includes/header.php';
?>
<h1>Pegar livro emprestado</h1>
<?php if ($flash): ?><p class="alert info"><?= e($flash) ?></p><?php endif; ?>
<table class="table">
    <thead><tr><th>Título</th><th>Autor</th><th>Disponíveis</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($books as $b): ?>
        <tr>
            <td><?= e($b['title']) ?></td>
            <td><?= e($b['author']) ?></td>
            <td><?= (int)$b['available'] ?></td>
            <td>
                <?php if ((int)$b['available'] > 0): ?>
                    <form method="post" action="/actions/borrow_action.php" class="inline">
                        <?= csrf_field() ?>
                        <input type="hidden" name="book_id" value="<?= (int)$b['id'] ?>">
                        <button type="submit">Pegar emprestado</button>
                    </form>
                <?php else: ?>
                    <span class="badge off">Esgotado</span>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
    <?php if (!$books): ?>
        <tr><td colspan="4" class="muted">Nenhum livro cadastrado.</td></tr>
    <?php endif; ?>
    </tbody>
</table>
<?php require __DIR__ . '/../includes/footer.php'; ?>
