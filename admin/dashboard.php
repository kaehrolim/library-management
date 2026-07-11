<?php
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../includes/auth.php';
require_admin();

// Métricas para o painel (insights).
$totalBooks   = (int)$pdo->query('SELECT COUNT(*) FROM books')->fetchColumn();
$totalCopies  = (int)$pdo->query('SELECT COALESCE(SUM(total_copies),0) FROM books')->fetchColumn();
$totalUsers   = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn();
$activeLoans  = (int)$pdo->query('SELECT COUNT(*) FROM loans WHERE returned_at IS NULL')->fetchColumn();

// Livros mais emprestados (top 5).
$topBooks = $pdo->query("
    SELECT b.title, COUNT(l.id) AS total
    FROM loans l JOIN books b ON b.id = l.book_id
    GROUP BY b.id, b.title
    ORDER BY total DESC
    LIMIT 5
")->fetchAll();

$pageTitle = 'Painel';
require __DIR__ . '/../includes/header.php';
?>
<h1>Painel administrativo</h1>

<div class="cards">
    <div class="card stat"><span class="num"><?= $totalBooks ?></span><span>Títulos</span></div>
    <div class="card stat"><span class="num"><?= $totalCopies ?></span><span>Cópias no acervo</span></div>
    <div class="card stat"><span class="num"><?= $totalUsers ?></span><span>Leitores</span></div>
    <div class="card stat"><span class="num"><?= $activeLoans ?></span><span>Empréstimos ativos</span></div>
</div>

<h2>Livros mais emprestados</h2>
<table class="table">
    <thead><tr><th>Título</th><th>Empréstimos</th></tr></thead>
    <tbody>
    <?php foreach ($topBooks as $t): ?>
        <tr><td><?= e($t['title']) ?></td><td><?= (int)$t['total'] ?></td></tr>
    <?php endforeach; ?>
    <?php if (!$topBooks): ?>
        <tr><td colspan="2" class="muted">Nenhum empréstimo registrado ainda.</td></tr>
    <?php endif; ?>
    </tbody>
</table>
<?php require __DIR__ . '/../includes/footer.php'; ?>
