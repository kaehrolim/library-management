<?php
require __DIR__ . '/config/database.php';
require __DIR__ . '/includes/auth.php';

// Já logado? Manda pro painel certo.
if (is_logged_in()) {
    header('Location: ' . (is_admin() ? '/admin/dashboard.php' : '/user/dashboard.php'));
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Preencha e-mail e senha.';
    } else {
        // Prepared statement: e-mail entra como parâmetro, nunca concatenado.
        $stmt = $pdo->prepare('SELECT id, name, email, password, role FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // password_verify compara a senha digitada com o hash salvo.
        if ($user && password_verify($password, $user['password'])) {
            // Regenera o ID de sessão para evitar session fixation.
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name']    = $user['name'];
            $_SESSION['role']    = $user['role'];

            header('Location: ' . ($user['role'] === 'admin' ? '/admin/dashboard.php' : '/user/dashboard.php'));
            exit;
        }
        // Mensagem genérica: não revela se o e-mail existe (evita enumeração de usuários).
        $error = 'E-mail ou senha incorretos.';
    }
}

$pageTitle = 'Entrar';
require __DIR__ . '/includes/header.php';
?>
<div class="card auth-card">
    <h1>Entrar</h1>
    <?php if ($error): ?><p class="alert error"><?= e($error) ?></p><?php endif; ?>
    <form method="post" action="/login.php">
        <?= csrf_field() ?>
        <label>E-mail
            <input type="email" name="email" required autofocus>
        </label>
        <label>Senha
            <input type="password" name="password" required>
        </label>
        <button type="submit">Entrar</button>
    </form>
    <p class="muted">Não tem conta? <a href="/register.php">Cadastre-se</a></p>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
