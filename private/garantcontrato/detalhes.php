<?php
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_logged();
start_session();
?>

<?php
$erro = '';
$gc = null;

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id === 0) {
    header("Location: listar.php");
    exit;
}

try {
    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8",
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $ligacao->prepare("SELECT g.*, e.nome AS nome_equipamento FROM garantia_contrato g LEFT JOIN equipamento e ON g.id_equipamento = e.id WHERE g.id = ?");
    $stmt->execute([$id]);
    $gc = $stmt->fetch(PDO::FETCH_OBJ);

    if (!$gc) {
        header("Location: listar.php");
        exit;
    }

    $ligacao = null;
} catch (PDOException $err) {
    $erro = "Erro ao carregar garantia/contrato.";
}
?>

<?php include __DIR__ . '/../includes/header.php'; ?>

<body class="pagprivada">

    <?php include __DIR__ . '/../includes/nav.php'; ?>

    <main class="conteudo">

        <h1 class="mb-4">Detalhes da Garantia e Contrato</h1>

        <?php if (!empty($erro)) : ?>
            <div class="alert alert-danger"><?= $erro ?></div>
        <?php else : ?>

        <div class="shadow p-4 rounded" style="max-width: 850px;">

            <div class="mb-3">
                <strong>Equipamento:</strong>
                <p><?= $gc->nome_equipamento ?: '-' ?></p>
            </div>

            <div class="mb-3">
                <strong>Tipo de contrato:</strong>
                <p><?= $gc->tipo_contrato ?: '-' ?></p>
            </div>

            <div class="mb-3">
                <strong>Entidade responsável:</strong>
                <p><?= $gc->entidade_responsavel ?: '-' ?></p>
            </div>

            <div class="mb-3">
                <strong>Periodicidade:</strong>
                <p><?= $gc->periodicidade ?: '-' ?></p>
            </div>

            <div class="mb-3">
                <strong>Data de início:</strong>
                <p><?= $gc->data_inicio ?: '-' ?></p>
            </div>

            <div class="mb-3">
                <strong>Data de fim:</strong>
                <p><?= $gc->data_fim ?: '-' ?></p>
            </div>

            <div class="mb-3">
                <strong>Observações:</strong>
                <p><?= $gc->observacoes ?: '-' ?></p>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <a href="listar.php" class="btn btn-secondary">Voltar</a>
                <a href="editar.php?id=<?= $id ?>" class="btn btn-warning">Editar</a>
            </div>

        </div>

        <?php endif; ?>

    </main>
    <?php include __DIR__ . '/../includes/footer.php'; ?>