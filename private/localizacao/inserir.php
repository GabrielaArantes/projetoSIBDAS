<?php
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_logged();
start_session();
?>

<?php
$sucesso = '';
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $ligacao = new PDO(
            "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8",
            MYSQL_USERNAME,
            MYSQL_PASSWORD
        );
        $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $ligacao->prepare("INSERT INTO localizacao (edificio, piso, servico, sala) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $_POST['edificio'],
            $_POST['piso'],
            $_POST['servico'],
            $_POST['sala']
        ]);

        $ligacao = null;
        $sucesso = "Localização inserida com sucesso!";

    } catch (PDOException $err) {
        $erro = "Erro ao inserir: " . $err->getMessage();
    }
}
?>

<?php include __DIR__ . '/../includes/header.php'; ?>

<body class="pagprivada">

    <?php include __DIR__ . '/../includes/nav.php'; ?>

    <main class="conteudo">

        <h1 class="mb-4">Inserir Localização</h1>

        <?php if (!empty($sucesso)) : ?>
            <div class="alert alert-success"><?= $sucesso ?></div>
        <?php endif; ?>
        <?php if (!empty($erro)) : ?>
            <div class="alert alert-danger"><?= $erro ?></div>
        <?php endif; ?>

        <form class="shadow p-4 rounded" style="max-width: 850px;" method="POST" action="inserir.php">

            <div class="row mb-3">
                <div class="col">
                    <label class="form-label">Edifício</label>
                    <input type="text" class="form-control" name="edificio" placeholder="Ex: Edifício A">
                </div>
                <div class="col">
                    <label class="form-label">Piso</label>
                    <input type="text" class="form-control" name="piso" placeholder="Ex: Piso 2">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Serviço / Departamento</label>
                <input type="text" class="form-control" name="servico" placeholder="Ex: Cardiologia">
            </div>

            <div class="mb-3">
                <label class="form-label">Sala / Gabinete</label>
                <input type="text" class="form-control" name="sala" placeholder="Ex: Sala 203">
            </div>

            <div class="d-flex justify-content-between mt-4">
                <a href="listar.php" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-left"></i> Voltar
                </a>
                <button type="submit" class="btn btn-success">
                    <i class="fa-solid fa-check"></i> Guardar
                </button>
            </div>

        </form>

    </main>
    <?php include __DIR__ . '/../includes/footer.php'; ?>