<?php
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_logged();
start_session();

$equipamentos = [];
try {
    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8",
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $equipamentos = $ligacao->query("SELECT id, nome FROM equipamento ORDER BY nome")->fetchAll(PDO::FETCH_OBJ);
    $ligacao = null;
} catch (PDOException $err) {
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $tipo        = $_POST["tipo"]        ?? "";
    $nome        = $_POST["nome"]        ?? "";
    $data        = $_POST["data"]        ?? "";
    $validade    = $_POST["validade"]    ?? "";
    $equipamento = $_POST["equipamento"] ?? "";

    $erros = [];
    $erro_sistema = "";

    $tipo = trim($tipo);
    $nome = trim($nome);
    $data = trim($data);

    if (empty($tipo)) $erros[] = "O Tipo de Documento é obrigatório.";
    if (empty($nome)) $erros[] = "O Nome do Documento é obrigatório.";

    if (empty($data)) {
        $erros[] = "A Data do Documento é obrigatória.";
    } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data)) {
        $erros[] = "Formato de data inválido. Use AAAA-MM-DD.";
    } else {
        $partes = explode('-', $data);
        if (!checkdate((int)$partes[1], (int)$partes[2], (int)$partes[0]))
            $erros[] = "Data do Documento inválida.";
    }

    if (!empty($validade) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $validade))
        $erros[] = "Formato de data de validade inválido. Use AAAA-MM-DD.";

    if (empty($equipamento)) $erros[] = "O Equipamento Associado é obrigatório.";
}
?>

<?php include __DIR__ . '/../includes/header.php'; ?>

<body class="pagprivada">

    <?php include __DIR__ . '/../includes/nav.php'; ?>

    <main class="conteudo">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Inserir Documentação</h1>
            <a href="listar.php" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Voltar
            </a>
        </div>

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

        <div class="shadow p-4 rounded bg-white" style="max-width: 700px; margin: auto;">

            <form method="POST" action="inserir.php" enctype="multipart/form-data" novalidate>

                <div class="mb-3">
                    <label class="form-label">Tipo de Documento <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="tipo"
                        value="<?= htmlspecialchars($_POST['tipo'] ?? '') ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nome do Documento <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="nome"
                        value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Data do Documento <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="data_doc" name="data"
                        value="<?= htmlspecialchars($_POST['data'] ?? '') ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Data de Validade (opcional)</label>
                    <input type="text" class="form-control" id="data_validade" name="validade"
                        value="<?= htmlspecialchars($_POST['validade'] ?? '') ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Equipamento Associado <span class="text-danger">*</span></label>
                    <select class="form-select" name="equipamento" required>
                        <option value="">Selecione...</option>
                        <?php foreach ($equipamentos as $eq) : ?>
                            <option value="<?= $eq->id ?>" <?= (($_POST['equipamento'] ?? '') == $eq->id) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($eq->nome) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Ficheiro <span class="text-danger">*</span></label>
                    <input type="file" class="form-control" name="ficheiro" accept=".pdf,.jpg,.png,.doc,.docx" required>
                </div>
                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-success px-4">
                        <i class="fa-solid fa-floppy-disk me-2"></i>Guardar
                    </button>
                </div>

            </form>

        </div>

    </main>
    <?php include __DIR__ . '/../includes/footer.php'; ?>