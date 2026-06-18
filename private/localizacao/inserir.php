<?php
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_logged();
start_session();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $edificio = $_POST["edificio"] ?? "";
    $piso     = $_POST["piso"]     ?? "";
    $servico  = $_POST["servico"]  ?? "";
    $sala     = $_POST["sala"]     ?? "";

    $erros = [];
    $erro_sistema = "";

    $edificio = trim($edificio);
    $piso     = trim($piso);
    $servico  = trim($servico);
    $sala     = trim($sala);

    if (empty($edificio)) $erros[] = "O Edifício é obrigatório.";
    if (empty($piso))     $erros[] = "O Piso é obrigatório.";
    if (empty($servico))  $erros[] = "O Serviço / Departamento é obrigatório.";
}
?>

<?php include __DIR__ . '/../includes/header.php'; ?>

<body class="pagprivada">

    <?php include __DIR__ . '/../includes/nav.php'; ?>

    <main class="conteudo">

        <h1 class="mb-4">Inserir Localização</h1>

        <?php if (!empty($erros)) : ?>
            <div class="alert alert-danger" role="alert">
                <strong>Foram encontrados os seguintes erros:</strong>
                <ul class="mb-0">
                    <?php foreach ($erros as $erro) : ?>
                        <li><?= htmlspecialchars($erro) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if (!empty($erro_sistema)) : ?>
            <div class="alert alert-danger" role="alert">
                <strong>Erro:</strong>
                <p><?= htmlspecialchars($erro_sistema) ?></p>
            </div>
        <?php endif; ?>

        <form class="shadow p-4 rounded" style="max-width: 850px;" method="POST" action="inserir.php" novalidate>

            <div class="row mb-3">
                <div class="col">
                    <label class="form-label">Edifício <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="edificio"
                        value="<?= htmlspecialchars($_POST['edificio'] ?? '') ?>" required>
                </div>
                <div class="col">
                    <label class="form-label">Piso <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="piso"
                        value="<?= htmlspecialchars($_POST['piso'] ?? '') ?>" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Serviço / Departamento <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="servico"
                    value="<?= htmlspecialchars($_POST['servico'] ?? '') ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Sala / Gabinete</label>
                <input type="text" class="form-control" name="sala"
                    value="<?= htmlspecialchars($_POST['sala'] ?? '') ?>">
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