<?php
// Ponto de entrada: redireciona conforme o estado de login.
require __DIR__ . '/includes/auth.php';

if (!is_logged_in()) {
    header('Location: /login.php');
} elseif (is_admin()) {
    header('Location: /admin/dashboard.php');
} else {
    header('Location: /user/dashboard.php');
}
exit;
