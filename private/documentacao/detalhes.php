<?php
// Mostra os detalhes de um documento e permite abrir o ficheiro
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_logged();
start_session();
?>

<?php
$erro = '';
$documento = null;

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

    $stmt = $ligacao->prepare("SELECT d.*, e.nome AS nome_equipamento FROM documento d LEFT JOIN equipamento e ON d.id_equipamento = e.id WHERE d.id = ?");
    $stmt->execute([$id]);
    $documento = $stmt->fetch(PDO::FETCH_OBJ);

    if (!$documento) {
        header("Location: listar.php");
        exit;
    }

    $ligacao = null;
} catch (PDOException $err) {
    $erro = "Erro ao carregar documento.";
}
?>

<?php include __DIR__ . '/../includes/header.php'; ?>

<body class="pagprivada">

    <?php include __DIR__ . '/../includes/nav.php'; ?>

    <main class="conteudo">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Detalhes da Documentação</h1>
            <div class="d-flex gap-2">
                <a href="listar.php" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-left"></i> Voltar
                </a>
                <a href="editar.php?id=<?= urlencode($idEncrypted) ?>" class="btn btn-warning">
                    <i class="fa-solid fa-pen"></i> Editar
                </a>
            </div>
        </div>

        <?php if (!empty($erro)) : ?>
            <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
        <?php else : ?>

        <div class="shadow p-4 rounded bg-white" style="max-width: 700px; margin: auto;">

            <h5 class="fw-bold mb-3">Informação do Documento</h5>

            <p><strong>Tipo:</strong> <?= $documento->tipo ? htmlspecialchars($documento->tipo) : '-' ?></p>
            <p><strong>Nome:</strong> <?= $documento->nome ? htmlspecialchars($documento->nome) : '-' ?></p>
            <p><strong>Data do Documento:</strong> <?= $documento->data_documento ? date('d/m/Y', strtotime($documento->data_documento)) : '-' ?></p>
            <p><strong>Data de Validade:</strong> <?= $documento->data_validade ? date('d/m/Y', strtotime($documento->data_validade)) : '-' ?></p>
            <p><strong>Equipamento Associado:</strong> <?= $documento->nome_equipamento ? htmlspecialchars($documento->nome_equipamento) : '-' ?></p>
            <p>
                <strong>Ficheiro:</strong>
                <?php if (!empty($documento->ficheiro)) : ?>
                    <?= htmlspecialchars($documento->ficheiro_nome_original ?? $documento->ficheiro) ?>
                    <a href="<?= BASE_URL ?>/assets/uploads/documentos/<?= rawurlencode($documento->ficheiro) ?>" target="_blank" class="btn btn-outline-primary btn-sm ms-2">
                        <i class="fa-solid fa-file"></i> Abrir Ficheiro
                    </a>
                <?php else : ?>
                    <span class="text-muted">Sem ficheiro</span>
                <?php endif; ?>
            </p>

        </div>

        <?php endif; ?>

    </main>
    <?php include __DIR__ . '/../includes/footer.php'; ?>