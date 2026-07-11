<?php
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../includes/auth.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /user/borrow.php');
    exit;
}
verify_csrf();

$bookId = (int)($_POST['book_id'] ?? 0);
$userId = (int)$_SESSION['user_id'];

try {
    // Transação garante que a checagem de disponibilidade e a inserção
    // aconteçam de forma consistente.
    $pdo->beginTransaction();

    // Regra 1: usuário não pode pegar duas cópias do mesmo livro ao mesmo tempo.
    $dup = $pdo->prepare(
        'SELECT id FROM loans WHERE user_id = ? AND book_id = ? AND returned_at IS NULL'
    );
    $dup->execute([$userId, $bookId]);
    if ($dup->fetch()) {
        $pdo->rollBack();
        $_SESSION['flash'] = 'Você já está com este livro emprestado.';
        header('Location: /user/borrow.php');
        exit;
    }

    // Regra 2: precisa haver cópia disponível.
    $avail = $pdo->prepare("
        SELECT (b.total_copies -
               (SELECT COUNT(*) FROM loans WHERE book_id = b.id AND returned_at IS NULL)) AS available
        FROM books b WHERE b.id = ?
    ");
    $avail->execute([$bookId]);
    $row = $avail->fetch();

    if (!$row || (int)$row['available'] <= 0) {
        $pdo->rollBack();
        $_SESSION['flash'] = 'Livro indisponível no momento.';
        header('Location: /user/borrow.php');
        exit;
    }

    // Registra o empréstimo.
    $ins = $pdo->prepare('INSERT INTO loans (user_id, book_id) VALUES (?, ?)');
    $ins->execute([$userId, $bookId]);

    $pdo->commit();
    $_SESSION['flash'] = 'Empréstimo realizado com sucesso.';
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $_SESSION['flash'] = 'Erro ao processar o empréstimo.';
}

header('Location: /user/returns.php');
exit;
