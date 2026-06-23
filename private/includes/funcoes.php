<?php
require_once __DIR__ . '/../../config/config.php';

// Inicia a sessão se ainda não estiver iniciada
function start_session()
{
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
}

function check_session()
{
    return isset($_SESSION['utilizador']);
}

function redirect_if_not_logged($redirect_to = '/public/login.php')
{
    start_session();
    if (!check_session()) {
        header("Location: " . BASE_URL . $redirect_to);
        exit;
    }
}

function logout_and_redirect($redirect_to = '/public/login.php')
{
    start_session();
    session_unset();
    session_destroy();
    header("Location: " . BASE_URL . $redirect_to);
    exit;
}

function redirect_if_not_role(array $perfis_permitidos, $redirect_to = '/private/dashboard/dashboard.php')
{
    start_session();
    redirect_if_not_logged();

    $perfil = $_SESSION['perfil'] ?? null;

    if (!$perfil || !in_array($perfil, $perfis_permitidos)) {
        $_SESSION['server_error'] = 'Não tem permissões para aceder a essa funcionalidade.';
        header("Location: " . BASE_URL . $redirect_to);
        exit;
    }
}

function aes_encrypt(string $value) {
    return bin2hex(openssl_encrypt(
        $value,
        OPENSSL_METHOD,
        OPENSSL_KEY,
        OPENSSL_RAW_DATA,
        OPENSSL_IV
    ));
}

function aes_decrypt(mixed $value) {
    if (!is_string($value) || strlen($value) % 2 !== 0) return false;

    return openssl_decrypt(
        hex2bin($value),
        OPENSSL_METHOD,
        OPENSSL_KEY,
        OPENSSL_RAW_DATA,
        OPENSSL_IV
    );
}

function registar_log(string $tipo_evento, string $descricao, ?int $agente_id = null)
{
    try {
        $ligacao = new PDO(
            "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8",
            MYSQL_USERNAME,
            MYSQL_PASSWORD,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );

        $ip = $_SERVER['REMOTE_ADDR'] ?? null;

        $stmt = $ligacao->prepare(
            "INSERT INTO logs (tipo_evento, descricao, agente_id, ip)
             VALUES (:tipo, :descricao, :agente_id, :ip)"
        );
        $stmt->execute([
            ':tipo'      => $tipo_evento,
            ':descricao' => $descricao,
            ':agente_id' => $agente_id,
            ':ip'        => $ip
        ]);
    } catch (PDOException $e) {
        // Falha silenciosa
    }
}

// ============================================================
// Funções auxiliares para carregar tabelas de lookup da BD
// ============================================================

function get_pdo(): PDO
{
    return new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8",
        MYSQL_USERNAME,
        MYSQL_PASSWORD,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ]
    );
}

function get_lookup(string $tabela): array
{
    try {
        $pdo = get_pdo();
        return $pdo->query("SELECT id, nome FROM `$tabela` ORDER BY id")->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

function get_categorias(): array       { return get_lookup('categorias_equipamento'); }
function get_estados(): array          { return get_lookup('estados_equipamento'); }
function get_criticidades(): array     { return get_lookup('criticidades'); }
function get_tipos_entrada(): array    { return get_lookup('tipos_entrada'); }
function get_tipos_fornecedor(): array { return get_lookup('tipos_fornecedor'); }
function get_tipos_documento(): array  { return get_lookup('tipos_documento'); }
function get_tipos_contrato(): array   { return get_lookup('tipos_contrato'); }
function get_periodicidades(): array   { return get_lookup('periodicidades'); }
function get_perfis(): array           { return get_lookup('perfis'); }