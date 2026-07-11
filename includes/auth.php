<?php
/**
 * Funções de sessão, autenticação, controle de acesso (RBAC) e CSRF.
 * Inclua este arquivo no topo de toda página que exige login.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/** Usuário está logado? */
function is_logged_in(): bool
{
    return isset($_SESSION['user_id']);
}

/** Usuário logado é admin? */
function is_admin(): bool
{
    return (($_SESSION['role'] ?? '') === 'admin');
}

/** Exige login; se não estiver logado, manda pro login. */
function require_login(): void
{
    if (!is_logged_in()) {
        header('Location: /login.php');
        exit;
    }
}

/** Exige perfil de admin; caso contrário, bloqueia o acesso. */
function require_admin(): void
{
    require_login();
    if (!is_admin()) {
        http_response_code(403);
        exit('Acesso negado. Esta área é restrita a administradores.');
    }
}

/** Escapa saída para prevenir XSS. Use SEMPRE ao imprimir dado do banco. */
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/* ------------------------------------------------------------------ */
/* Proteção CSRF: token por sessão, validado em todo formulário POST.  */
/* ------------------------------------------------------------------ */

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/** Campo hidden pronto para colar dentro do <form>. */
function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}

/** Valida o token recebido no POST; aborta se inválido. */
function verify_csrf(): void
{
    $stored = $_SESSION['csrf_token'] ?? '';
    $sent   = $_POST['csrf_token'] ?? '';
    // Rejeita se não há token na sessão (evita bypass por hash_equals('','')).
    if ($stored === '' || !hash_equals($stored, $sent)) {
        http_response_code(419);
        exit('Token de segurança inválido. Recarregue a página e tente novamente.');
    }
}
