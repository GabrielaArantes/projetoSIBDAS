<?php
// Mostra os detalhes de um fornecedor e os equipamentos associados
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_logged();
start_session();
?>

<?php
$erro = '';
$fornecedor = null;
$equipamentos = [];

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

    $stmt = $ligacao->prepare("SELECT * FROM fornecedor WHERE id = ?");
    $stmt->execute([$id]);
    $fornecedor = $stmt->fetch(PDO::FETCH_OBJ);

    if (!$fornecedor) {
        header("Location: listar.php");
        exit;
    }

    // Equipamentos associados
    $stmt = $ligacao->prepare("SELECT e.* FROM equipamento e INNER JOIN equipamento_fornecedor ef ON e.id = ef.id_equipamento WHERE ef.id_fornecedor = ?");
    $stmt->execute([$id]);
    $equipamentos = $stmt->fetchAll(PDO::FETCH_OBJ);

    $ligacao = null;
} catch (PDOException $err) {
    $erro = "Erro ao carregar fornecedor.";
}
?>

<?php include __DIR__ . '/../includes/header.php'; ?>

<body class="pagprivada">

    <?php include __DIR__ . '/../includes/nav.php'; ?>

    <main class="conteudo">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>
                Detalhes do Fornecedor
                <?php if ($fornecedor) : ?>
                    <?php if ($fornecedor->fornecedor_ativo == 1) : ?>
                        <span class="badge bg-success">Ativo</span>
                    <?php else : ?>
                        <span class="badge bg-secondary">Inativo</span>
                    <?php endif; ?>
                <?php endif; ?>
            </h1>
            <a href="editar.php?id=<?= urlencode($idEncrypted) ?>" class="btn btn-warning">
                <i class="fa-solid fa-pen"></i> Editar
            </a>
        </div>

        <?php if (!empty($erro)) : ?>
            <div class="alert alert-danger"><?= $erro ?></div>
        <?php else : ?>

        <div class="shadow p-4 rounded mb-4" style="max-width: 900px;">

            <h4 class="mb-3">Informações Gerais</h4>

            <div class="row mb-3">
                <div class="col">
                    <strong>Nome da Empresa:</strong>
                    <p><?= htmlspecialchars($fornecedor->nome) ?></p>
                </div>
                <div class="col">
                    <strong>NIF:</strong>
                    <p><?= $fornecedor->nif ? htmlspecialchars($fornecedor->nif) : '-' ?></p>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col">
                    <strong>Telefone:</strong>
                    <p><?= $fornecedor->telefone ? htmlspecialchars($fornecedor->telefone) : '-' ?></p>
                </div>
                <div class="col">
                    <strong>Email:</strong>
                    <p><?= $fornecedor->email ? htmlspecialchars($fornecedor->email) : '-' ?></p>
                </div>
            </div>

            <div class="mb-3">
                <strong>Morada:</strong>
                <p><?= $fornecedor->morada ? htmlspecialchars($fornecedor->morada) : '-' ?></p>
            </div>

            <div class="mb-3">
                <strong>Website:</strong>
                <p><?= $fornecedor->website ? htmlspecialchars($fornecedor->website) : '-' ?></p>
            </div>

            <hr>

            <h4 class="mb-3">Contacto</h4>

            <div class="row mb-3">
                <div class="col">
                    <strong>Pessoa de Contacto:</strong>
                    <p><?= $fornecedor->pessoa_contacto ? htmlspecialchars($fornecedor->pessoa_contacto) : '-' ?></p>
                </div>
                <div class="col">
                    <strong>Telefone da Pessoa de Contacto:</strong>
                    <p><?= $fornecedor->telefone_contacto ? htmlspecialchars($fornecedor->telefone_contacto) : '-' ?></p>
                </div>
            </div>

            <div class="mb-3">
                <strong>Tipo de Fornecedor:</strong>
                <p><?= $fornecedor->tipo ? htmlspecialchars($fornecedor->tipo) : '-' ?></p>
            </div>

            <div class="mb-3">
                <strong>Observações:</strong>
                <p><?= $fornecedor->observacoes ? htmlspecialchars($fornecedor->observacoes) : '-' ?></p>
            </div>

        </div>

        <div class="shadow p-4 rounded" style="max-width: 900px;">
            <h4 class="mb-3">Equipamentos Associados</h4>

            <?php if (count($equipamentos) > 0) : ?>
            <table class="table table-bordered table-striped">
                <thead class="table-success">
                    <tr>
                        <th>Nome do Equipamento</th>
                        <th>Categoria</th>
                        <th>Estado</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($equipamentos as $eq) : ?>
                    <tr>
                        <td><?= htmlspecialchars($eq->nome) ?></td>
                        <td><?= htmlspecialchars($eq->categoria) ?></td>
                        <td><?= htmlspecialchars($eq->estado) ?></td>
                        <td>
                            <a href="../equipamentos/detalhes.php?id=<?= aes_encrypt($eq->id) ?>" class="btn btn-primary btn-sm">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else : ?>
                <p class="text-muted">Nenhum equipamento associado.</p>
            <?php endif; ?>
        </div>

        <?php endif; ?>

        <div class="mt-4">
            <a href="listar.php" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Voltar
            </a>
        </div>

    </main>
    <?php include __DIR__ . '/../includes/footer.php'; ?>