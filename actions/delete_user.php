<?php
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../includes/auth.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /admin/users.php');
    exit;
}
verify_csrf();

$userId = (int)($_POST['user_id'] ?? 0);

// Trava dupla: não permite excluir admins nem a própria conta.
$stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role = 'user' AND id <> ?");
$stmt->execute([$userId, (int)$_SESSION['user_id']]);

$_SESSION['flash'] = $stmt->rowCount() > 0
    ? 'Conta removida.'
    : 'Não foi possível remover esta conta.';
header('Location: /admin/users.php');
exit;
