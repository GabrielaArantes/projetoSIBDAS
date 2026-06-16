<?php
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_logged();
start_session();
?>

<?php
$sucesso = '';
$erro = '';
$fornecedor = null;

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
    $ligacao = null;

    if (!$fornecedor) {
        header("Location: listar.php");
        exit;
    }
} catch (PDOException $err) {
    $erro = "Erro ao carregar fornecedor.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $ligacao = new PDO(
            "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8",
            MYSQL_USERNAME,
            MYSQL_PASSWORD
        );
        $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $ligacao->prepare("UPDATE fornecedor SET nome=?, nif=?, telefone=?, email=?, morada=?, website=?, pessoa_contacto=?, telefone_contacto=?, tipo=?, observacoes=? WHERE id=?");
        $stmt->execute([
            $_POST['nome_empresa'],
            $_POST['nif'],
            $_POST['telefone'],
            $_POST['email'],
            $_POST['morada'],
            $_POST['website'],
            $_POST['pessoa_contacto'],
            $_POST['telefone_contacto'],
            $_POST['tipo_fornecedor'],
            $_POST['observacoes'],
            $id
        ]);

        $ligacao = null;
        $sucesso = "Fornecedor atualizado com sucesso!";

        $ligacao = new PDO(
            "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8",
            MYSQL_USERNAME,
            MYSQL_PASSWORD
        );
        $stmt = $ligacao->prepare("SELECT * FROM fornecedor WHERE id = ?");
        $stmt->execute([$id]);
        $fornecedor = $stmt->fetch(PDO::FETCH_OBJ);
        $ligacao = null;

    } catch (PDOException $err) {
        $erro = "Erro ao atualizar: " . $err->getMessage();
    }
}
?>

<?php include __DIR__ . '/../includes/header.php'; ?>

<body class="pagprivada">

    <?php include __DIR__ . '/../includes/nav.php'; ?>

    <main class="conteudo">

        <h1 class="mb-4">Editar Fornecedor</h1>

        <?php if (!empty($sucesso)) : ?>
            <div class="alert alert-success"><?= $sucesso ?></div>
        <?php endif; ?>
        <?php if (!empty($erro)) : ?>
            <div class="alert alert-danger"><?= $erro ?></div>
        <?php endif; ?>

        <form class="shadow p-4 rounded" style="max-width: 800px;" method="POST" action="editar.php?id=<?= $id ?>">

            <div class="row mb-3">
                <div class="col">
                    <label class="form-label">Nome da Empresa <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="nome_empresa" value="<?= $fornecedor->nome ?? '' ?>" required>
                </div>
                <div class="col">
                    <label class="form-label">NIF <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" name="nif" value="<?= $fornecedor->nif ?? '' ?>" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col">
                    <label class="form-label">Telefone <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="telefone" value="<?= $fornecedor->telefone ?? '' ?>" required>
                </div>
                <div class="col">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" name="email" value="<?= $fornecedor->email ?? '' ?>" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Morada</label>
                <input type="text" class="form-control" name="morada" value="<?= $fornecedor->morada ?? '' ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Website</label>
                <input type="text" class="form-control" name="website" value="<?= $fornecedor->website ?? '' ?>">
            </div>

            <hr>

            <div class="row mb-3">
                <div class="col">
                    <label class="form-label">Pessoa de Contacto</label>
                    <input type="text" class="form-control" name="pessoa_contacto" value="<?= $fornecedor->pessoa_contacto ?? '' ?>">
                </div>
                <div class="col">
                    <label class="form-label">Telefone da Pessoa de Contacto</label>
                    <input type="text" class="form-control" name="telefone_contacto" value="<?= $fornecedor->telefone_contacto ?? '' ?>">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Tipo de Fornecedor <span class="text-danger">*</span></label>
                <select class="form-select" name="tipo_fornecedor" required>
                    <option value="">Selecione...</option>
                    <?php foreach (['Fabricante', 'Distribuidor / Fornecedor Comercial', 'Assistência Técnica', 'Fornecedor de Consumíveis'] as $tipo) : ?>
                        <option value="<?= $tipo ?>" <?= ($fornecedor->tipo ?? '') === $tipo ? 'selected' : '' ?>><?= $tipo ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Observações</label>
                <textarea class="form-control" rows="4" name="observacoes"><?= $fornecedor->observacoes ?? '' ?></textarea>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <a href="listar.php" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-left"></i> Voltar
                </a>
                <button type="submit" class="btn btn-warning">
                    <i class="fa-solid fa-pen"></i> Guardar Alterações
                </button>
            </div>

        </form>

    </main>
    <?php include __DIR__ . '/../includes/footer.php'; ?>