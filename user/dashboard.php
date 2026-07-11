<?php
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../includes/auth.php';
require_login();

// Disponível = total de cópias - empréstimos ativos (returned_at IS NULL).
$sql = "
    SELECT b.id, b.title, b.author, b.release_date, b.total_copies,
           (b.total_copies - COALESCE(active.qtd, 0)) AS available
    FROM books b
    LEFT JOIN (
        SELECT book_id, COUNT(*) AS qtd
        FROM loans
        WHERE returned_at IS NULL
        GROUP BY book_id
    ) active ON active.book_id = b.id
    ORDER BY b.title
";
$books = $pdo->query($sql)->fetchAll();

$pageTitle = 'Acervo';
require __DIR__ . '/../includes/header.php';
?>
<h1>Livros disponíveis</h1>
<table class="table">
    <thead>
        <tr><th>Título</th><th>Autor</th><th>Lançamento</th><th>Disponíveis</th></tr>
    </thead>
    <tbody>
    <?php foreach ($books as $b): ?>
        <tr>
            <td><?= e($b['title']) ?></td>
            <td><?= e($b['author']) ?></td>
            <td><?= e($b['release_date']) ?></td>
            <td>
                <?php if ((int)$b['available'] > 0): ?>
                    <span class="badge ok"><?= (int)$b['available'] ?></span>
                <?php else: ?>
                    <span class="badge off">Esgotado</span>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
    <?php if (!$books): ?>
        <tr><td colspan="4" class="muted">Nenhum livro cadastrado ainda.</td></tr>
    <?php endif; ?>
    </tbody>
</table>
<?php require __DIR__ . '/../includes/footer.php'; ?>
