<?php
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_logged();
start_session();
?>

<?php
$erro = '';
$localizacao = null;
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

    $stmt = $ligacao->prepare("SELECT * FROM localizacao WHERE id = ?");
    $stmt->execute([$id]);
    $localizacao = $stmt->fetch(PDO::FETCH_OBJ);

    if (!$localizacao) {
        header("Location: listar.php");
        exit;
    }

    // Equipamentos nesta localização
    $stmt = $ligacao->prepare("SELECT * FROM equipamento WHERE id_localizacao = ?");
    $stmt->execute([$id]);
    $equipamentos = $stmt->fetchAll(PDO::FETCH_OBJ);

    $ligacao = null;
} catch (PDOException $err) {
    $erro = "Erro ao carregar localização.";
}
?>

<?php include __DIR__ . '/../includes/header.php'; ?>

<body class="pagprivada">

    <?php include __DIR__ . '/../includes/nav.php'; ?>

    <main class="conteudo">

        <h1 class="mb-4">
            Detalhes da Localização
            <?php if ($localizacao) : ?>
                <?php if ($localizacao->localizacao_ativo == 1) : ?>
                    <span class="badge bg-success">Ativo</span>
                <?php else : ?>
                    <span class="badge bg-secondary">Inativo</span>
                <?php endif; ?>
            <?php endif; ?>
        </h1>

        <?php if (!empty($erro)) : ?>
            <div class="alert alert-danger"><?= $erro ?></div>
        <?php else : ?>

        <div class="shadow p-4 rounded mb-4" style="max-width: 850px;">

            <h4 class="mb-3">
                <i class="fa-solid fa-location-dot me-2"></i>
                Localização
            </h4>

            <div class="mb-3">
                <strong>Edifício:</strong>
                <p><?= $localizacao->edificio ? htmlspecialchars($localizacao->edificio) : '-' ?></p>
            </div>

            <div class="mb-3">
                <strong>Piso:</strong>
                <p><?= $localizacao->piso ? htmlspecialchars($localizacao->piso) : '-' ?></p>
            </div>

            <div class="mb-3">
                <strong>Serviço / Departamento:</strong>
                <p><?= $localizacao->servico ? htmlspecialchars($localizacao->servico) : '-' ?></p>
            </div>

            <div class="mb-3">
                <strong>Sala / Gabinete:</strong>
                <p><?= $localizacao->sala ? htmlspecialchars($localizacao->sala) : '-' ?></p>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <a href="listar.php" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-left"></i> Voltar
                </a>
                <a href="editar.php?id=<?= urlencode($idEncrypted) ?>" class="btn btn-warning">
                    <i class="fa-solid fa-pen"></i> Editar
                </a>
            </div>

        </div>

        <div class="shadow p-4 rounded" style="max-width: 850px;">
            <h4 class="mb-3">Equipamentos nesta Localização</h4>

            <?php if (count($equipamentos) > 0) : ?>
            <table class="table table-bordered table-striped">
                <thead class="table-success">
                    <tr>
                        <th>Nome</th>
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
                <p class="text-muted">Nenhum equipamento nesta localização.</p>
            <?php endif; ?>
        </div>

        <?php endif; ?>

    </main>
    <?php include __DIR__ . '/../includes/footer.php'; ?>