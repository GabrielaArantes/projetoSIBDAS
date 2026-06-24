<?php
// Processa o formulário de login: valida credenciais e inicia a sessão do utilizador
session_start();

// Apenas aceita pedidos POST
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: ../public/login.php');
    return;
}

require_once __DIR__ . '/includes/funcoes.php';

// Recolhe os dados submetidos no formulário
$username = isset($_POST['text_username']) ? $_POST['text_username'] : '';
$password = isset($_POST['text_password']) ? $_POST['text_password'] : '';

$validation_errors = [];

// Valida o formato do email e o comprimento dos campos
if (!filter_var($username, FILTER_VALIDATE_EMAIL)) {
    $validation_errors[] = 'O username tem que ser um email válido.';
}

if (strlen($username) < 5 || strlen($username) > 50) {
    $validation_errors[] = 'O username deve ter entre 5 e 50 caracteres.';
}

if (strlen($password) < 6 || strlen($password) > 12) {
    $validation_errors[] = 'A password deve ter entre 6 e 12 caracteres.';
}

// Se há erros de validação, redireciona de volta ao login
if (!empty($validation_errors)) {
    $_SESSION['validation_errors'] = $validation_errors;
    header('Location: ../public/login.php');
    return;
}

// Verifica as credenciais na base de dados (email guardado encriptado com AES)
try {
    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8",
        MYSQL_USERNAME,
        MYSQL_PASSWORD,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // Procura o agente cujo email desencriptado corresponde ao username introduzido
    $comando = $ligacao->prepare(
        "SELECT *, AES_DECRYPT(email, :chave) AS email_decifrado
         FROM agents
         WHERE AES_DECRYPT(email, :chave2) = :u
         AND agent_ativo = 1"
    );
    $comando->execute([
        ':chave'  => MYSQL_AES_KEY,
        ':chave2' => MYSQL_AES_KEY,
        ':u'      => $username
    ]);
    $agente = $comando->fetch(PDO::FETCH_OBJ);

    // Verifica se o utilizador existe e se a password está correta (password_verify com hash bcrypt)
    if (!$agente || !password_verify($password, $agente->password)) {
        registar_log('LOGIN_FALHOU', 'Tentativa de login falhada para o email: ' . $username);
        $_SESSION['server_error'] = 'Login inválido';
        header('Location: ../public/login.php');
        return;
    }

    // Atualiza a data do último login
    $stmt = $ligacao->prepare("UPDATE agents SET last_login = NOW() WHERE id = ?");
    $stmt->execute([$agente->id]);

    // Regista o login bem-sucedido no log do sistema
    registar_log('LOGIN_OK', 'Login efetuado com sucesso pelo agente: ' . $agente->nome, $agente->id);

    // Guarda os dados essenciais do utilizador na sessão
    $_SESSION['utilizador'] = $agente->nome;
    $_SESSION['email'] = $agente->email_decifrado;
    $_SESSION['perfil'] = $agente->perfil;
    $_SESSION['agente_id'] = $agente->id;

    $ligacao = null;
} catch (PDOException $e) {
    registar_log('ERRO_BD', 'Erro ao ligar à base de dados durante o login: ' . $e->getMessage());
    $_SESSION['server_error'] = 'Erro ao ligar à base de dados.';
    header('Location: ../public/login.php');
    return;
}

// Redireciona para o dashboard após login bem-sucedido
header('Location: ' . BASE_URL . '/private/dashboard/dashboard.php');
exit;