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

    $equipamento   = $_POST["equipamento"]   ?? "";
    $inicio        = $_POST["inicio"]        ?? "";
    $fim           = $_POST["fim"]           ?? "";
    $tipo          = $_POST["tipo"]          ?? "";
    $entidade      = $_POST["entidade"]      ?? "";
    $periodicidade = $_POST["periodicidade"] ?? "";
    $observacoes   = $_POST["observacoes"]   ?? "";

    echo "<p><strong>Dados recebidos:</strong> Equipamento ID: $equipamento | Tipo: $tipo | Início: $inicio | Fim: $fim</p>";
}
?>

<?php include __DIR__ . '/../includes/header.php'; ?>

<body class="pagprivada">

    <?php include __DIR__ . '/../includes/nav.php'; ?>

    <main class="conteudo">

        <h1 class="mb-4">Inserir Garantia / Contrato</h1>

        <?php if (!empty($sucesso)) : ?>
            <div class="alert alert-success"><?= $sucesso ?></div>
        <?php endif; ?>
        <?php if (!empty($erro)) : ?>
            <div class="alert alert-danger"><?= $erro ?></div>
        <?php endif; ?>

        <form class="shadow p-4 rounded" style="max-width: 850px;" method="POST" action="inserir.php">

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
                <label class="form-label">Data de início</label>
                <input type="date" class="form-control" name="inicio">
            </div>

            <div class="mb-3">
                <label class="form-label">Data de fim</label>
                <input type="date" class="form-control" name="fim">
            </div>

            <div class="mb-3">
                <label class="form-label">Tipo de contrato</label>
                <select class="form-select" name="tipo">
                    <option value="">Selecione...</option>
                    <option>Garantia</option>
                    <option>Contrato de Manutenção</option>
                    <option>Assistência Técnica</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Entidade responsável</label>
                <input type="text" class="form-control" name="entidade">
            </div>

            <div class="mb-3">
                <label class="form-label">Periodicidade</label>
                <select class="form-select" name="periodicidade">
                    <option value="">Selecione...</option>
                    <option>Mensal</option>
                    <option>Trimestral</option>
                    <option>Semestral</option>
                    <option>Anual</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Observações</label>
                <textarea class="form-control" name="observacoes" rows="4"></textarea>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <a href="listar.php" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-success">Guardar</button>
            </div>

        </form>

    </main>
    <?php include __DIR__ . '/../includes/footer.php'; ?>