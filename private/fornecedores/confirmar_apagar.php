<?php
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_role(['Administrador', 'Técnico'], '/private/fornecedores/listar.php');
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

    // Verificar o estado de atividade atual do fornecedor
    $stmt = $ligacao->prepare("SELECT fornecedor_ativo FROM fornecedor WHERE id = ?");
    $stmt->execute([$id]);
    $ativoAtual = $stmt->fetchColumn();

    if ($ativoAtual === false) {
        header("Location: listar.php");
        exit;
    }

    // Alternar: se está ativo (1) passa a inativo (0), caso contrário passa a ativo (1)
    $novoAtivo = ($ativoAtual == 1) ? 0 : 1;

    $stmt = $ligacao->prepare("UPDATE fornecedor SET fornecedor_ativo = ? WHERE id = ?");
    $stmt->execute([$novoAtivo, $id]);

    $ligacao = null;

    header("Location: listar.php");
    exit;
} catch (PDOException $err) {
    echo "<p class='text-danger'>Erro: " . $err->getMessage() . "</p>";
    exit;
}