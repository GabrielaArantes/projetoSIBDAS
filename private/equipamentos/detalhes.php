<?php
// Mostra os detalhes completos de um equipamento organizado em separadores
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_logged();
start_session();
?>

<?php
$erro = '';
$equipamento = null;
$fornecedor = null;
$garantia = null;
$documentos = [];

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

    // Equipamento + Localização
    $stmt = $ligacao->prepare("SELECT e.*, l.edificio, l.piso, l.servico, l.sala FROM equipamento e LEFT JOIN localizacao l ON e.id_localizacao = l.id WHERE e.id = ?");
    $stmt->execute([$id]);
    $equipamento = $stmt->fetch(PDO::FETCH_OBJ);

    if (!$equipamento) {
        header("Location: listar.php");
        exit;
    }

    // Fornecedor
    $stmt = $ligacao->prepare("SELECT f.* FROM fornecedor f INNER JOIN equipamento_fornecedor ef ON f.id = ef.id_fornecedor WHERE ef.id_equipamento = ? LIMIT 1");
    $stmt->execute([$id]);
    $fornecedor = $stmt->fetch(PDO::FETCH_OBJ);

    // Garantia
    $stmt = $ligacao->prepare("SELECT * FROM garantia_contrato WHERE id_equipamento = ? LIMIT 1");
    $stmt->execute([$id]);
    $garantia = $stmt->fetch(PDO::FETCH_OBJ);

    // Documentos
    $stmt = $ligacao->prepare("SELECT * FROM documento WHERE id_equipamento = ?");
    $stmt->execute([$id]);
    $documentos = $stmt->fetchAll(PDO::FETCH_OBJ);

    $ligacao = null;
} catch (PDOException $err) {
    $erro = "Erro ao carregar equipamento.";
}
?>

<?php include __DIR__ . '/../includes/header.php'; ?>

<body class="pagprivada">

    <?php include __DIR__ . '/../includes/nav.php'; ?>

    <main class="conteudo p-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>
                Detalhes do Equipamento
            </h1>
            <a href="listar.php" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left me-2"></i>Voltar
            </a>
        </div>

        <?php if (!empty($erro)) : ?>
            <div class="alert alert-danger"><?= $erro ?></div>
        <?php else : ?>

        <div class="shadow p-4 rounded bg-white" style="max-width: 900px; margin: auto;">

            <ul class="nav nav-tabs mb-4" id="equipTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#dados" role="tab">Dados</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#fornecedor" role="tab">Fornecedor</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#localizacao" role="tab">Localização</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#garantia" role="tab">Garantia / Contrato</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#docs" role="tab">Documentação</button>
                </li>
            </ul>

            <div class="tab-content">

                <div class="tab-pane fade show active" id="dados" role="tabpanel">
                    <div class="p-3 border rounded bg-light">
                        <h5 class="fw-bold mb-3">Dados do Equipamento</h5>
                        <p><strong>Código Interno:</strong> <?= htmlspecialchars($equipamento->codigo_interno) ?></p>
                        <p><strong>Designação:</strong> <?= htmlspecialchars($equipamento->nome) ?></p>
                        <p><strong>Categoria / Grupo:</strong> <?= htmlspecialchars($equipamento->categoria) ?></p>
                        <p><strong>Marca:</strong> <?= htmlspecialchars($equipamento->marca) ?></p>
                        <p><strong>Modelo:</strong> <?= htmlspecialchars($equipamento->modelo) ?></p>
                        <p><strong>Número de Série:</strong> <?= htmlspecialchars($equipamento->num_serie ?? '') ?></p>
                        <p><strong>Fabricante:</strong> <?= htmlspecialchars($equipamento->fabricante ?? '') ?></p>
                        <p><strong>Data de Aquisição:</strong> <?= $equipamento->data_aquisicao ? date('d/m/Y', strtotime($equipamento->data_aquisicao)) : '-' ?></p>
                        <p><strong>Ano de Fabrico:</strong> <?= $equipamento->ano_fabrico ? htmlspecialchars($equipamento->ano_fabrico) : '-' ?></p>
                        <p><strong>Custo de Aquisição:</strong> <?= $equipamento->custo ? number_format($equipamento->custo, 2, ',', '.') . ' €' : '-' ?></p>
                        <p><strong>Tipo de Entrada:</strong> <?= htmlspecialchars($equipamento->tipo_entrada ?? '') ?></p>
                        <p><strong>Estado Atual:</strong> <?= htmlspecialchars($equipamento->estado ?? '') ?></p>
                        <p><strong>Criticidade:</strong> <?= htmlspecialchars($equipamento->criticidade ?? '') ?></p>
                        <p><strong>Observações:</strong> <?= $equipamento->observacoes ? htmlspecialchars($equipamento->observacoes) : '-' ?></p>
                    </div>
                </div>

                <div class="tab-pane fade" id="fornecedor" role="tabpanel">
                    <div class="p-3 border rounded bg-light">
                        <h5 class="fw-bold mb-3">Fornecedor</h5>
                        <?php if ($fornecedor) : ?>
                            <p><strong>Nome:</strong> <?= htmlspecialchars($fornecedor->nome ?? '') ?></p>
                            <p><strong>NIF:</strong> <?= htmlspecialchars($fornecedor->nif ?? '') ?></p>
                            <p><strong>Email:</strong> <?= htmlspecialchars($fornecedor->email ?? '') ?></p>
                            <p><strong>Telefone:</strong> <?= htmlspecialchars($fornecedor->telefone ?? '') ?></p>
                            <p><strong>Morada:</strong> <?= $fornecedor->morada ? htmlspecialchars($fornecedor->morada) : '-' ?></p>
                            <p><strong>Tipo:</strong> <?= htmlspecialchars($fornecedor->tipo ?? '') ?></p>
                            <p><strong>Pessoa de Contacto:</strong> <?= $fornecedor->pessoa_contacto ? htmlspecialchars($fornecedor->pessoa_contacto) : '-' ?></p>
                        <?php else : ?>
                            <p class="text-muted">Nenhum fornecedor associado.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="tab-pane fade" id="localizacao" role="tabpanel">
                    <div class="p-3 border rounded bg-light">
                        <h5 class="fw-bold mb-3">Localização</h5>
                        <p><strong>Edifício:</strong> <?= $equipamento->edificio ? htmlspecialchars($equipamento->edificio) : '-' ?></p>
                        <p><strong>Piso:</strong> <?= $equipamento->piso ? htmlspecialchars($equipamento->piso) : '-' ?></p>
                        <p><strong>Serviço / Departamento:</strong> <?= $equipamento->servico ? htmlspecialchars($equipamento->servico) : '-' ?></p>
                        <p><strong>Sala / Gabinete:</strong> <?= $equipamento->sala ? htmlspecialchars($equipamento->sala) : '-' ?></p>
                    </div>
                </div>

                <div class="tab-pane fade" id="garantia" role="tabpanel">
                    <div class="p-3 border rounded bg-light">
                        <h5 class="fw-bold mb-3">Garantia / Contrato</h5>
                        <?php if ($garantia) : ?>
                            <p><strong>Data de início:</strong> <?= $garantia->data_inicio ? date('d/m/Y', strtotime($garantia->data_inicio)) : '-' ?></p>
                            <p><strong>Data de fim:</strong> <?= $garantia->data_fim ? date('d/m/Y', strtotime($garantia->data_fim)) : '-' ?></p>
                            <p><strong>Tipo de contrato:</strong> <?= $garantia->tipo_contrato ? htmlspecialchars($garantia->tipo_contrato) : '-' ?></p>
                            <p><strong>Entidade responsável:</strong> <?= $garantia->entidade_responsavel ? htmlspecialchars($garantia->entidade_responsavel) : '-' ?></p>
                            <p><strong>Periodicidade:</strong> <?= $garantia->periodicidade ? htmlspecialchars($garantia->periodicidade) : '-' ?></p>
                            <p><strong>Observações:</strong> <?= $garantia->observacoes ? htmlspecialchars($garantia->observacoes) : '-' ?></p>
                        <?php else : ?>
                            <p class="text-muted">Nenhuma garantia ou contrato associado.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="tab-pane fade" id="docs" role="tabpanel">
                    <div class="p-3 border rounded bg-light">
                        <h5 class="fw-bold mb-3">Documentação</h5>
                        <?php if (count($documentos) > 0) : ?>
                            <?php foreach ($documentos as $doc) : ?>
                                <div class="border rounded p-2 mb-2">
                                    <p class="mb-1"><strong>Tipo:</strong> <?= htmlspecialchars($doc->tipo) ?></p>
                                    <p class="mb-1"><strong>Nome:</strong> <?= htmlspecialchars($doc->nome) ?></p>
                                    <p class="mb-1"><strong>Data:</strong> <?= $doc->data_documento ? date('d/m/Y', strtotime($doc->data_documento)) : '-' ?></p>
                                    <p class="mb-0"><strong>Validade:</strong> <?= $doc->data_validade ? date('d/m/Y', strtotime($doc->data_validade)) : '-' ?></p>
                                </div>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <p class="text-muted">Nenhum documento associado.</p>
                        <?php endif; ?>
                    </div>
                </div>

            </div>

        </div>

        <?php endif; ?>

    </main>
    <?php include __DIR__ . '/../includes/footer.php'; ?>