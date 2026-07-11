<?php
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../includes/auth.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /admin/books.php');
    exit;
}
verify_csrf();

$title  = trim($_POST['title'] ?? '');
$author = trim($_POST['author'] ?? '');
$date   = trim($_POST['release_date'] ?? '');
$copies = max(1, (int)($_POST['total_copies'] ?? 1));

// Data vazia vira NULL; data preenchida é validada no formato YYYY-MM-DD.
$release = ($date !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) ? $date : null;

if ($title === '' || $author === '') {
    $_SESSION['flash'] = 'Título e autor são obrigatórios.';
} else {
    $stmt = $pdo->prepare(
        'INSERT INTO books (title, author, release_date, total_copies) VALUES (?, ?, ?, ?)'
    );
    $stmt->execute([$title, $author, $release, $copies]);
    $_SESSION['flash'] = 'Livro adicionado ao acervo.';
}

header('Location: /admin/books.php');
exit;
