<?php
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../includes/auth.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /admin/books.php');
    exit;
}
verify_csrf();

$bookId = (int)($_POST['book_id'] ?? 0);

// ON DELETE CASCADE no schema remove empréstimos vinculados automaticamente.
$stmt = $pdo->prepare('DELETE FROM books WHERE id = ?');
$stmt->execute([$bookId]);

$_SESSION['flash'] = $stmt->rowCount() > 0 ? 'Livro excluído.' : 'Livro não encontrado.';
header('Location: /admin/books.php');
exit;
