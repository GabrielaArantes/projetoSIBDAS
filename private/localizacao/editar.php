<?php
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_logged();
start_session();
?>

<?php
$sucesso = '';
$erro = '';
$localizacao = null;

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
    $stmt = $ligacao->prepare("SELECT * FROM localizacao WHERE id = ?");
    $stmt->execute([$id]);
    $localizacao = $stmt->fetch(PDO::FETCH_OBJ);
    $ligacao = null;

    if (!$localizacao) {
        header("Location: listar.php");
        exit;
    }
} catch (PDOException $err) {
    $erro = "Erro ao carregar localização.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $ligacao = new PDO(
            "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8",
            MYSQL_USERNAME,
            MYSQL_PASSWORD
        );
        $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $ligacao->prepare("UPDATE localizacao SET edificio=?, piso=?, servico=?, sala=? WHERE id=?");
        $stmt->execute([
            $_POST['edificio'],
            $_POST['piso'],
            $_POST['servico'],
            $_POST['sala'],
            $id
        ]);

        $ligacao = null;
        $sucesso = "Localização atualizada com sucesso!";

        $ligacao = new PDO(
            "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8",
            MYSQL_USERNAME,
            MYSQL_PASSWORD
        );
        $stmt = $ligacao->prepare("SELECT * FROM localizacao WHERE id = ?");
        $stmt->execute([$id]);
        $localizacao = $stmt->fetch(PDO::FETCH_OBJ);
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

        <h1 class="mb-4">Editar Localização</h1>

        <?php if (!empty($sucesso)) : ?>
            <div class="alert alert-success"><?= $sucesso ?></div>
        <?php endif; ?>
        <?php if (!empty($erro)) : ?>
            <div class="alert alert-danger"><?= $erro ?></div>
        <?php endif; ?>

        <form class="shadow p-4 rounded" style="max-width: 850px;" method="POST" action="editar.php?id=<?= $id ?>">

            <div class="row mb-3">
                <div class="col">
                    <label class="form-label">Edifício</label>
                    <input type="text" class="form-control" name="edificio" value="<?= $localizacao->edificio ?? '' ?>">
                </div>
                <div class="col">
                    <label class="form-label">Piso</label>
                    <input type="text" class="form-control" name="piso" value="<?= $localizacao->piso ?? '' ?>">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Serviço / Departamento</label>
                <input type="text" class="form-control" name="servico" value="<?= $localizacao->servico ?? '' ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Sala / Gabinete</label>
                <input type="text" class="form-control" name="sala" value="<?= $localizacao->sala ?? '' ?>">
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