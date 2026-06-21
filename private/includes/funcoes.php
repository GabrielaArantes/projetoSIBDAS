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

// Verifica se o perfil da sessão atual está dentro dos perfis permitidos.
// Se não estiver, redireciona (por defeito para o dashboard) e termina o script.
// Uso: redirect_if_not_role(['Administrador', 'Técnico']);
function redirect_if_not_role($perfis_permitidos, $redirect_to = '/private/dashboard/dashboard.php')
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


function aes_encrypt($value) {
    return bin2hex(openssl_encrypt(
        $value,
        OPENSSL_METHOD,
        OPENSSL_KEY,
        OPENSSL_RAW_DATA,
        OPENSSL_IV
    ));
}

function aes_decrypt($value) {
    if (!is_string($value) || strlen($value) % 2 !== 0) return false; // proteção básica

    return openssl_decrypt(
        hex2bin($value),
        OPENSSL_METHOD,
        OPENSSL_KEY,
        OPENSSL_RAW_DATA,
        OPENSSL_IV
    );
}