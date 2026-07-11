<?php
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../includes/auth.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /user/returns.php');
    exit;
}
verify_csrf();

$loanId = (int)($_POST['loan_id'] ?? 0);
$userId = (int)$_SESSION['user_id'];

// A cláusula user_id = ? é essencial: impede que um usuário devolva o
// empréstimo de outro adulterando o loan_id (proteção contra IDOR).
$stmt = $pdo->prepare(
    'UPDATE loans SET returned_at = NOW()
     WHERE id = ? AND user_id = ? AND returned_at IS NULL'
);
$stmt->execute([$loanId, $userId]);

$_SESSION['flash'] = $stmt->rowCount() > 0
    ? 'Livro devolvido. Obrigado!'
    : 'Não foi possível devolver este empréstimo.';

header('Location: /user/returns.php');
exit;
