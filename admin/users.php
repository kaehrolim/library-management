<?php
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../includes/auth.php';
require_admin();

$flash = $_SESSION['flash'] ?? '';
unset($_SESSION['flash']);

// Lista todos os usuários; admins não podem ser excluídos por aqui.
$users = $pdo->query(
    'SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC'
)->fetchAll();

$pageTitle = 'Usuários';
require __DIR__ . '/../includes/header.php';
?>
<h1>Usuários cadastrados</h1>
<?php if ($flash): ?><p class="alert info"><?= e($flash) ?></p><?php endif; ?>
<table class="table">
    <thead><tr><th>Nome</th><th>E-mail</th><th>Perfil</th><th>Criado em</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($users as $u): ?>
        <tr>
            <td><?= e($u['name']) ?></td>
            <td><?= e($u['email']) ?></td>
            <td><span class="badge <?= $u['role'] === 'admin' ? 'ok' : '' ?>"><?= e($u['role']) ?></span></td>
            <td><?= e($u['created_at']) ?></td>
            <td>
                <?php if ($u['role'] !== 'admin' && (int)$u['id'] !== (int)$_SESSION['user_id']): ?>
                    <form method="post" action="/actions/delete_user.php" class="inline"
                          onsubmit="return confirm('Banir/excluir esta conta? Ação irreversível.');">
                        <?= csrf_field() ?>
                        <input type="hidden" name="user_id" value="<?= (int)$u['id'] ?>">
                        <button type="submit" class="danger">Banir</button>
                    </form>
                <?php else: ?>
                    <span class="muted">—</span>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php require __DIR__ . '/../includes/footer.php'; ?>
