<?php
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_logged();
start_session();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $edificio = $_POST["edificio"] ?? "";
    $piso     = $_POST["piso"]     ?? "";
    $servico  = $_POST["servico"]  ?? "";
    $sala     = $_POST["sala"]     ?? "";

    echo "<p><strong>Dados recebidos:</strong> Edifício: $edificio | Piso: $piso | Serviço: $servico | Sala: $sala</p>";

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