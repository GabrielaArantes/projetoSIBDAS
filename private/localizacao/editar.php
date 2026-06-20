<?php
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_logged();
start_session();
require_once __DIR__ . '/../includes/validacoes.php';

if (!in_array($_SERVER['REQUEST_METHOD'], ['GET', 'POST'])) {
    header('Location: ' . BASE_URL . '/public/login.php');
    exit;
}

$sucesso = '';
$erro = '';
$erros = [];
$localizacao = null;

$idEncrypted = $_GET['id'] ?? null;
$id = aes_decrypt($idEncrypted);

if (!$id || !is_numeric($id)) {
    header("Location: listar.php");
    exit;
}

$id = (int)$id;

// 1. Tratar primeiro a submissão do formulário (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $erros = array_merge(
        validar_edificio($_POST['edificio'] ?? ''),
        validar_piso($_POST['piso'] ?? ''),
        validar_servico($_POST['servico'] ?? '')
    );

    if (empty($erros)) {
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

        } catch (PDOException $err) {
            $erro = "Erro ao atualizar: " . $err->getMessage();
        }
    }
}

// 2. Obter os dados atuais da localização (GET, ou para mostrar o formulário após o POST)
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
        <?php if (!empty($erros)) : ?>
            <div class="alert alert-danger">
                <?php foreach ($erros as $e) : ?>
                    <div><?= htmlspecialchars($e) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form class="shadow p-4 rounded" style="max-width: 850px;" method="POST" action="editar.php?id=<?= $idEncrypted ?>" novalidate autocomplete="off">

            <div class="row mb-3">
                <div class="col">
                    <label class="form-label">Edifício <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="edificio" value="<?= htmlspecialchars($localizacao->edificio ?? '') ?>" required>
                </div>
                <div class="col">
                    <label class="form-label">Piso <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="piso" value="<?= htmlspecialchars($localizacao->piso ?? '') ?>" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Serviço / Departamento <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="servico" value="<?= htmlspecialchars($localizacao->servico ?? '') ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Sala / Gabinete</label>
                <input type="text" class="form-control" name="sala" value="<?= htmlspecialchars($localizacao->sala ?? '') ?>">
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