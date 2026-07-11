<?php
require __DIR__ . '/config/database.php';
require __DIR__ . '/includes/auth.php';

if (is_logged_in()) {
    header('Location: /user/dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($name === '' || $email === '' || $password === '') {
        $error = 'Preencha todos os campos.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'E-mail inválido.';
    } elseif (strlen($password) < 6) {
        $error = 'A senha deve ter pelo menos 6 caracteres.';
    } else {
        // Verifica duplicidade de e-mail.
        $check = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $check->execute([$email]);

        if ($check->fetch()) {
            $error = 'Este e-mail já está cadastrado.';
        } else {
            // Senha nunca é salva em texto puro: gera hash bcrypt.
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare(
                'INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, "user")'
            );
            $stmt->execute([$name, $email, $hash]);

            // Loga automaticamente após cadastro.
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int)$pdo->lastInsertId();
            $_SESSION['name']    = $name;
            $_SESSION['role']    = 'user';

            header('Location: /user/dashboard.php');
            exit;
        }
    }
}

$pageTitle = 'Cadastro';
require __DIR__ . '/includes/header.php';
?>
<div class="card auth-card">
    <h1>Criar conta</h1>
    <?php if ($error): ?><p class="alert error"><?= e($error) ?></p><?php endif; ?>
    <form method="post" action="/register.php">
        <?= csrf_field() ?>
        <label>Nome
            <input type="text" name="name" required autofocus>
        </label>
        <label>E-mail
            <input type="email" name="email" required>
        </label>
        <label>Senha (mín. 6 caracteres)
            <input type="password" name="password" required>
        </label>
        <button type="submit">Cadastrar</button>
    </form>
    <p class="muted">Já tem conta? <a href="/login.php">Entrar</a></p>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
