<?php
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_logged();
start_session();
?>

<?php
$sucesso = '';
$erro = '';
$documento = null;
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
    $stmt = $ligacao->prepare("SELECT * FROM documento WHERE id = ?");
    $stmt->execute([$id]);
    $documento = $stmt->fetch(PDO::FETCH_OBJ);
    $equipamentos = $ligacao->query("SELECT id, nome FROM equipamento ORDER BY nome")->fetchAll(PDO::FETCH_OBJ);
    $ligacao = null;

    if (!$documento) {
        header("Location: listar.php");
        exit;
    }
} catch (PDOException $err) {
    $erro = "Erro ao carregar documento.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $ligacao = new PDO(
            "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8",
            MYSQL_USERNAME,
            MYSQL_PASSWORD
        );
        $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $ligacao->prepare("UPDATE documento SET tipo=?, nome=?, data_documento=?, data_validade=?, id_equipamento=? WHERE id=?");
        $stmt->execute([
            $_POST['tipo'],
            $_POST['nome'],
            $_POST['data'],
            $_POST['validade'] ?: null,
            $_POST['equipamento'],
            $id
        ]);

        $ligacao = null;
        $sucesso = "Documento atualizado com sucesso!";

        $ligacao = new PDO(
            "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8",
            MYSQL_USERNAME,
            MYSQL_PASSWORD
        );
        $stmt = $ligacao->prepare("SELECT * FROM documento WHERE id = ?");
        $stmt->execute([$id]);
        $documento = $stmt->fetch(PDO::FETCH_OBJ);
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

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Editar Documentação</h1>
            <a href="listar.php" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Voltar
            </a>
        </div>

        <?php if (!empty($sucesso)) : ?>
            <div class="alert alert-success"><?= $sucesso ?></div>
        <?php endif; ?>
        <?php if (!empty($erro)) : ?>
            <div class="alert alert-danger"><?= $erro ?></div>
        <?php endif; ?>

        <div class="shadow p-4 rounded bg-white" style="max-width: 700px; margin: auto;">

            <form method="POST" action="editar.php?id=<?= $id ?>" enctype="multipart/form-data">

                <div class="mb-3">
                    <label class="form-label">Tipo de Documento <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="tipo" value="<?= $documento->tipo ?? '' ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nome do Documento <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="nome" value="<?= $documento->nome ?? '' ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Data do Documento <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" name="data" value="<?= $documento->data_documento ?? '' ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Data de Validade (opcional)</label>
                    <input type="date" class="form-control" name="validade" value="<?= $documento->data_validade ?? '' ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Equipamento Associado <span class="text-danger">*</span></label>
                    <select class="form-select" name="equipamento" required>
                        <option value="">Selecione...</option>
                        <?php foreach ($equipamentos as $eq) : ?>
                            <option value="<?= $eq->id ?>" <?= ($documento->id_equipamento ?? '') == $eq->id ? 'selected' : '' ?>><?= $eq->nome ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <?php if (!empty($documento->ficheiro)) : ?>
                <div class="mb-3">
                    <label class="form-label">Ficheiro Atual</label>
                    <p class="text-muted"><?= $documento->ficheiro ?></p>
                </div>
                <?php endif; ?>

                <div class="mb-3">
                    <label class="form-label">Substituir Ficheiro (opcional)</label>
                    <input type="file" class="form-control" name="ficheiro" accept=".pdf,.jpg,.png,.doc,.docx">
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-warning px-4">
                        <i class="fa-solid fa-floppy-disk me-2"></i>Guardar Alterações
                    </button>
                </div>

            </form>

        </div>

    </main>
    <?php include __DIR__ . '/../includes/footer.php'; ?>