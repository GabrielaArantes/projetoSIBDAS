<?php
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_logged();
start_session();
?>

<?php
$sucesso = '';
$erro = '';
$gc = null;
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
    $stmt = $ligacao->prepare("SELECT * FROM garantia_contrato WHERE id = ?");
    $stmt->execute([$id]);
    $gc = $stmt->fetch(PDO::FETCH_OBJ);
    $equipamentos = $ligacao->query("SELECT id, nome FROM equipamento ORDER BY nome")->fetchAll(PDO::FETCH_OBJ);
    $ligacao = null;

    if (!$gc) {
        header("Location: listar.php");
        exit;
    }
} catch (PDOException $err) {
    $erro = "Erro ao carregar garantia/contrato.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $ligacao = new PDO(
            "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8",
            MYSQL_USERNAME,
            MYSQL_PASSWORD
        );
        $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $ligacao->prepare("UPDATE garantia_contrato SET id_equipamento=?, data_inicio=?, data_fim=?, tipo_contrato=?, entidade_responsavel=?, periodicidade=?, observacoes=? WHERE id=?");
        $stmt->execute([
            $_POST['equipamento'],
            $_POST['inicio'] ?: null,
            $_POST['fim'] ?: null,
            $_POST['tipo'],
            $_POST['entidade'],
            $_POST['periodicidade'],
            $_POST['observacoes'],
            $id
        ]);

        $ligacao = null;
        $sucesso = "Garantia/Contrato atualizado com sucesso!";

        $ligacao = new PDO(
            "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8",
            MYSQL_USERNAME,
            MYSQL_PASSWORD
        );
        $stmt = $ligacao->prepare("SELECT * FROM garantia_contrato WHERE id = ?");
        $stmt->execute([$id]);
        $gc = $stmt->fetch(PDO::FETCH_OBJ);
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

        <h1 class="mb-4">Editar Garantia / Contrato</h1>

        <?php if (!empty($sucesso)) : ?>
            <div class="alert alert-success"><?= $sucesso ?></div>
        <?php endif; ?>
        <?php if (!empty($erro)) : ?>
            <div class="alert alert-danger"><?= $erro ?></div>
        <?php endif; ?>

        <form class="shadow p-4 rounded" style="max-width: 850px;" method="POST" action="editar.php?id=<?= $id ?>">

            <div class="mb-3">
                <label class="form-label">Equipamento Associado <span class="text-danger">*</span></label>
                <select class="form-select" name="equipamento" required>
                    <option value="">Selecione...</option>
                    <?php foreach ($equipamentos as $eq) : ?>
                        <option value="<?= $eq->id ?>" <?= ($gc->id_equipamento ?? '') == $eq->id ? 'selected' : '' ?>><?= $eq->nome ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Data de início</label>
                <input type="date" class="form-control" name="inicio" value="<?= $gc->data_inicio ?? '' ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Data de fim</label>
                <input type="date" class="form-control" name="fim" value="<?= $gc->data_fim ?? '' ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Tipo de contrato</label>
                <select class="form-select" name="tipo">
                    <option value="">Selecione...</option>
                    <?php foreach (['Garantia', 'Contrato de Manutenção', 'Assistência Técnica'] as $tipo) : ?>
                        <option value="<?= $tipo ?>" <?= ($gc->tipo_contrato ?? '') === $tipo ? 'selected' : '' ?>><?= $tipo ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Entidade responsável</label>
                <input type="text" class="form-control" name="entidade" value="<?= $gc->entidade_responsavel ?? '' ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Periodicidade</label>
                <select class="form-select" name="periodicidade">
                    <option value="">Selecione...</option>
                    <?php foreach (['Mensal', 'Trimestral', 'Semestral', 'Anual'] as $per) : ?>
                        <option value="<?= $per ?>" <?= ($gc->periodicidade ?? '') === $per ? 'selected' : '' ?>><?= $per ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Observações</label>
                <textarea class="form-control" name="observacoes" rows="4"><?= $gc->observacoes ?? '' ?></textarea>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <a href="listar.php" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-warning">Guardar Alterações</button>
            </div>

        </form>

    </main>
    <?php include __DIR__ . '/../includes/footer.php'; ?>