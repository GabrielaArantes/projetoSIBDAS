<?php
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_logged();
start_session();
?>

<?php
$erro = '';
$fornecedor = null;
$equipamentos = [];

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
            <h1>Detalhes do Fornecedor</h1>
            <a href="editar.php?id=<?= $id ?>" class="btn btn-warning">
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
                    <p><?= $fornecedor->nome ?></p>
                </div>
                <div class="col">
                    <strong>NIF:</strong>
                    <p><?= $fornecedor->nif ?: '-' ?></p>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col">
                    <strong>Telefone:</strong>
                    <p><?= $fornecedor->telefone ?: '-' ?></p>
                </div>
                <div class="col">
                    <strong>Email:</strong>
                    <p><?= $fornecedor->email ?: '-' ?></p>
                </div>
            </div>

            <div class="mb-3">
                <strong>Morada:</strong>
                <p><?= $fornecedor->morada ?: '-' ?></p>
            </div>

            <div class="mb-3">
                <strong>Website:</strong>
                <p><?= $fornecedor->website ?: '-' ?></p>
            </div>

            <hr>

            <h4 class="mb-3">Contacto</h4>

            <div class="row mb-3">
                <div class="col">
                    <strong>Pessoa de Contacto:</strong>
                    <p><?= $fornecedor->pessoa_contacto ?: '-' ?></p>
                </div>
                <div class="col">
                    <strong>Telefone da Pessoa de Contacto:</strong>
                    <p><?= $fornecedor->telefone_contacto ?: '-' ?></p>
                </div>
            </div>

            <div class="mb-3">
                <strong>Tipo de Fornecedor:</strong>
                <p><?= $fornecedor->tipo ?: '-' ?></p>
            </div>

            <div class="mb-3">
                <strong>Observações:</strong>
                <p><?= $fornecedor->observacoes ?: '-' ?></p>
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
                        <td><?= $eq->nome ?></td>
                        <td><?= $eq->categoria ?></td>
                        <td><?= $eq->estado ?></td>
                        <td>
                            <a href="../equipamentos/detalhes.php?id=<?= $eq->id ?>" class="btn btn-primary btn-sm">
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