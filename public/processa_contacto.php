<?php
session_start();
require_once __DIR__ . '/../config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php#contacto');
    exit;
}

$nome      = trim($_POST['nome'] ?? '');
$email     = trim($_POST['email'] ?? '');
$telemovel = trim($_POST['telemovel'] ?? '');
$mensagem  = trim($_POST['mensagem'] ?? '');

$erros = [];

if (empty($nome)) {
    $erros[] = 'O Nome é obrigatório.';
}

if (empty($email)) {
    $erros[] = 'O Email é obrigatório.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $erros[] = 'O endereço de email não é válido.';
}

if (empty($telemovel)) {
    $erros[] = 'O Telemóvel é obrigatório.';
} elseif (!preg_match('/^[29]\d{8}$/', $telemovel)) {
    $erros[] = 'O Telemóvel deve ter 9 dígitos e começar por 9 ou 2.';
}

if (empty($mensagem)) {
    $erros[] = 'A Mensagem é obrigatória.';
}

if (!empty($erros)) {
    $_SESSION['contacto_erros'] = $erros;
    header('Location: index.php#contacto');
    exit;
}

try {
    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8",
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $ligacao->prepare("INSERT INTO mensagem_contacto (nome, email, telemovel, mensagem) VALUES (:nome, :email, :telemovel, :mensagem)");
    $stmt->execute([
        ':nome'      => $nome,
        ':email'     => $email,
        ':telemovel' => $telemovel,
        ':mensagem'  => $mensagem
    ]);

    $ligacao = null;

    $_SESSION['contacto_sucesso'] = 'Mensagem enviada com sucesso! Entraremos em contacto consigo brevemente.';
    header('Location: index.php#contacto');
    exit;

} catch (PDOException $err) {
    $_SESSION['contacto_erros'] = ['Erro ao enviar a mensagem. Tente novamente mais tarde.'];
    header('Location: index.php#contacto');
    exit;
}