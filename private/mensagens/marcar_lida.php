<?php
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_role(['Administrador'], '/private/dashboard/dashboard.php');
start_session();
?>

<?php
$idEncrypted = $_GET['id'] ?? null;
$id = aes_decrypt($idEncrypted);

if (!$id || !is_numeric($id)) {
    header("Location: listar.php");
    exit;
}

$id = (int)$id;

try {
    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8",
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Verificar o estado atual da mensagem
    $stmt = $ligacao->prepare("SELECT mensagem_lida FROM mensagem_contacto WHERE id = ?");
    $stmt->execute([$id]);
    $lidaAtual = $stmt->fetchColumn();

    if ($lidaAtual === false) {
        header("Location: listar.php");
        exit;
    }

    // Alternar: se está lida (1) passa a não lida (0), caso contrário passa a lida (1)
    $novaLida = ($lidaAtual == 1) ? 0 : 1;

    $stmt = $ligacao->prepare("UPDATE mensagem_contacto SET mensagem_lida = ? WHERE id = ?");
    $stmt->execute([$novaLida, $id]);

    $ligacao = null;

    header("Location: listar.php");
    exit;
} catch (PDOException $err) {
    echo "<p class='text-danger'>Erro: " . $err->getMessage() . "</p>";
    exit;
}