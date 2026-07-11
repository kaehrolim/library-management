<?php
/**
 * Cabeçalho compartilhado. Espera a variável $pageTitle definida antes do include.
 * Também monta a barra de navegação conforme o perfil do usuário logado.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$pageTitle = $pageTitle ?? 'Biblioteca';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?> · Biblioteca</title>
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body>
<header class="topbar">
    <div class="brand">📚 Biblioteca Escolar</div>
    <?php if (isset($_SESSION['user_id'])): ?>
        <nav class="nav">
            <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
                <a href="/admin/dashboard.php">Painel</a>
                <a href="/admin/books.php">Livros</a>
                <a href="/admin/users.php">Usuários</a>
            <?php else: ?>
                <a href="/user/dashboard.php">Acervo</a>
                <a href="/user/borrow.php">Emprestar</a>
                <a href="/user/returns.php">Meus empréstimos</a>
            <?php endif; ?>
            <span class="who">
                <?= htmlspecialchars($_SESSION['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                (<?= htmlspecialchars($_SESSION['role'] ?? '', ENT_QUOTES, 'UTF-8') ?>)
            </span>
            <a class="logout" href="/logout.php">Sair</a>
        </nav>
    <?php endif; ?>
</header>
<main class="container">
