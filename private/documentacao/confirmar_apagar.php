<?php
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_role(['Administrador', 'Técnico'], '/private/documentacao/listar.php');
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

    $stmt = $ligacao->prepare("SELECT documento_ativo FROM documento WHERE id = ?");
    $stmt->execute([$id]);
    $ativoAtual = $stmt->fetchColumn();

    if ($ativoAtual === false) {
        header("Location: listar.php");
        exit;
    }

    $novoAtivo = ($ativoAtual == 1) ? 0 : 1;

    $stmt = $ligacao->prepare("UPDATE documento SET documento_ativo = ? WHERE id = ?");
    $stmt->execute([$novoAtivo, $id]);

    $ligacao = null;

    header("Location: listar.php");
    exit;
} catch (PDOException $err) {
    echo "<p class='text-danger'>Erro: " . $err->getMessage() . "</p>";
    exit;
}