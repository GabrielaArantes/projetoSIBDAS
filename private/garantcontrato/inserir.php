<?php
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_role(['Administrador', 'Técnico'], '/private/{modulo}/listar.php');
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

    $erros = [];
    $erro_sistema = "";

    $tipo   = trim($tipo);
    $inicio = trim($inicio);
    $fim    = trim($fim);

    if (empty($equipamento)) $erros[] = "O Equipamento Associado é obrigatório.";
    if (empty($tipo))        $erros[] = "O Tipo de contrato é obrigatório.";

    if (!empty($inicio) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $inicio))
        $erros[] = "Formato de data de início inválido. Use AAAA-MM-DD.";

    if (!empty($fim) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fim))
        $erros[] = "Formato de data de fim inválido. Use AAAA-MM-DD.";

    if (!empty($inicio) && !empty($fim) && $fim < $inicio)
        $erros[] = "A data de fim não pode ser anterior à data de início.";

    if (empty($erros)) {
        $entidade = ucwords(strtolower($entidade));
    }

    // 4. Guardar na base de dados
    if (empty($erros)) {
        try {
            $ligacao = new PDO(
                "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8",
                MYSQL_USERNAME,
                MYSQL_PASSWORD
            );
            $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = "INSERT INTO garantia_contrato (id_equipamento, data_inicio, data_fim, tipo_contrato, entidade_responsavel, periodicidade, observacoes)
                    VALUES (:id_equipamento, :inicio, :fim, :tipo, :entidade, :periodicidade, :observacoes)";
            $stmt = $ligacao->prepare($sql);
            $stmt->execute([
                ':id_equipamento' => $equipamento,
                ':inicio'         => $inicio ?: null,
                ':fim'            => $fim    ?: null,
                ':tipo'           => $tipo,
                ':entidade'       => $entidade,
                ':periodicidade'  => $periodicidade,
                ':observacoes'    => $observacoes
            ]);

            $ligacao = null;
            header("Location: listar.php");
            exit;

        } catch (PDOException $err) {
            $erro_sistema = "Erro ao gravar os dados: " . $err->getMessage();
        }
        $ligacao = null;
    }
}
?>

<?php include __DIR__ . '/../includes/header.php'; ?>

<body class="pagprivada">

    <?php include __DIR__ . '/../includes/nav.php'; ?>

    <main class="conteudo">

        <h1 class="mb-4">Inserir Garantia / Contrato</h1>

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
                <label class="form-label">Data de início</label>
                <input type="text" id="data_inicio" class="form-control" name="inicio"
                    value="<?= htmlspecialchars($_POST['inicio'] ?? '') ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Data de fim</label>
                <input type="text" id="data_fim" class="form-control" name="fim"
                    value="<?= htmlspecialchars($_POST['fim'] ?? '') ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Tipo de contrato <span class="text-danger">*</span></label>
                <select class="form-select" name="tipo" required>
                    <option value="">Selecione...</option>
                    <?php foreach (['Garantia', 'Contrato de Manutenção', 'Assistência Técnica'] as $op) : ?>
                        <option value="<?= $op ?>" <?= (($_POST['tipo'] ?? '') == $op) ? 'selected' : '' ?>><?= $op ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Entidade responsável</label>
                <input type="text" class="form-control" name="entidade"
                    value="<?= htmlspecialchars($_POST['entidade'] ?? '') ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Periodicidade</label>
                <select class="form-select" name="periodicidade">
                    <option value="">Selecione...</option>
                    <?php foreach (['Mensal', 'Trimestral', 'Semestral', 'Anual'] as $op) : ?>
                        <option value="<?= $op ?>" <?= (($_POST['periodicidade'] ?? '') == $op) ? 'selected' : '' ?>><?= $op ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Observações</label>
                <textarea class="form-control" name="observacoes" rows="4"><?= htmlspecialchars($_POST['observacoes'] ?? '') ?></textarea>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <a href="listar.php" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-success">Guardar</button>
            </div>

        </form>

    </main>

    <script>
        flatpickr("#data_inicio", {
            dateFormat: "Y-m-d"
        });
        flatpickr("#data_fim", {
            dateFormat: "Y-m-d"
        });
    </script>

    <?php include __DIR__ . '/../includes/footer.php'; ?>