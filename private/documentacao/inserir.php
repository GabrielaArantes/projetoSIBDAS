<?php
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_logged();
start_session();
?>

<?php
$sucesso = '';
$erro = '';
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
    $erro = "Erro ao carregar equipamentos.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $ligacao = new PDO(
            "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8",
            MYSQL_USERNAME,
            MYSQL_PASSWORD
        );
        $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $ligacao->prepare("INSERT INTO documento (id_equipamento, tipo, nome, data_documento, data_validade, ficheiro) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['equipamento'],
            $_POST['tipo'],
            $_POST['nome'],
            $_POST['data'],
            $_POST['validade'] ?: null,
            $_POST['ficheiro_nome'] ?? ''
        ]);

        $ligacao = null;
        $sucesso = "Documento inserido com sucesso!";

    } catch (PDOException $err) {
        $erro = "Erro ao inserir: " . $err->getMessage();
    }
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

        <?php if (!empty($sucesso)) : ?>
            <div class="alert alert-success"><?= $sucesso ?></div>
        <?php endif; ?>
        <?php if (!empty($erro)) : ?>
            <div class="alert alert-danger"><?= $erro ?></div>
        <?php endif; ?>

        <div class="shadow p-4 rounded bg-white" style="max-width: 700px; margin: auto;">

            <form method="POST" action="inserir.php" enctype="multipart/form-data">

                <div class="mb-3">
                    <label class="form-label">Tipo de Documento <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="tipo" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nome do Documento <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="nome" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Data do Documento <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" name="data" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Data de Validade (opcional)</label>
                    <input type="date" class="form-control" name="validade">
                </div>

                <div class="mb-3">
                    <label class="form-label">Equipamento Associado <span class="text-danger">*</span></label>
                    <select class="form-select" name="equipamento" required>
                        <option value="">Selecione...</option>
                        <?php foreach ($equipamentos as $eq) : ?>
                            <option value="<?= $eq->id ?>"><?= $eq->nome ?></option>
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