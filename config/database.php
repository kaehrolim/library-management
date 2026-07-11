<?php
/**
 * Conexão com o banco de dados via PDO.
 *
 * As credenciais são lidas de variáveis de ambiente para não ficarem
 * expostas no código-fonte (ver .env.example). Se não houver variáveis
 * definidas, usa os defaults de um ambiente local (XAMPP/WAMP).
 *
 * PDO + prepared statements = defesa padrão contra SQL injection.
 */

// Carrega .env simples, se existir (sem depender de biblioteca externa).
$envPath = __DIR__ . '/../.env';
if (is_readable($envPath)) {
    foreach (file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (str_starts_with(trim($line), '#')) {
            continue;
        }
        [$key, $value] = array_pad(explode('=', $line, 2), 2, '');
        $key = trim($key);
        if ($key !== '' && getenv($key) === false) {
            putenv("$key=" . trim($value));
        }
    }
}

$host = getenv('DB_HOST') ?: '127.0.0.1';
$port = getenv('DB_PORT') ?: '3306';
$db   = getenv('DB_NAME') ?: 'library_management';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,   // erros viram exceções
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,          // resultados como array associativo
    PDO::ATTR_EMULATE_PREPARES   => false,                    // prepared statements reais
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    // Em produção, logar em arquivo em vez de exibir o erro.
    http_response_code(500);
    exit('Erro ao conectar no banco de dados. Verifique as credenciais no .env.');
}
