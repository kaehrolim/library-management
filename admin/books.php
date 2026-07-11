<?php
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../includes/auth.php';
require_admin();

$flash = $_SESSION['flash'] ?? '';
unset($_SESSION['flash']);

$books = $pdo->query('SELECT id, title, author, release_date, total_copies FROM books ORDER BY title')->fetchAll();

$pageTitle = 'Livros';
require __DIR__ . '/../includes/header.php';
?>
<h1>Gerenciar livros</h1>
<?php if ($flash): ?><p class="alert info"><?= e($flash) ?></p><?php endif; ?>

<!-- Mini-formulário: substitui a necessidade de escrever SQL na mão.
     Cada campo corresponde a uma coluna do banco. -->
<div class="card">
    <h2>Adicionar livro</h2>
    <form method="post" action="/actions/add_book.php" class="grid-form">
        <?= csrf_field() ?>
        <label>Título<input type="text" name="title" required></label>
        <label>Autor<input type="text" name="author" required></label>
        <label>Data de lançamento<input type="date" name="release_date"></label>
        <label>Quantidade de cópias<input type="number" name="total_copies" min="1" value="1" required></label>
        <button type="submit">Adicionar</button>
    </form>
</div>

<h2>Acervo</h2>
<table class="table">
    <thead><tr><th>Título</th><th>Autor</th><th>Lançamento</th><th>Cópias</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($books as $b): ?>
        <tr>
            <td><?= e($b['title']) ?></td>
            <td><?= e($b['author']) ?></td>
            <td><?= e($b['release_date']) ?></td>
            <td><?= (int)$b['total_copies'] ?></td>
            <td>
                <form method="post" action="/actions/delete_book.php" class="inline"
                      onsubmit="return confirm('Excluir este livro? Empréstimos vinculados também serão removidos.');">
                    <?= csrf_field() ?>
                    <input type="hidden" name="book_id" value="<?= (int)$b['id'] ?>">
                    <button type="submit" class="danger">Excluir</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    <?php if (!$books): ?>
        <tr><td colspan="5" class="muted">Nenhum livro cadastrado.</td></tr>
    <?php endif; ?>
    </tbody>
</table>
<?php require __DIR__ . '/../includes/footer.php'; ?>
