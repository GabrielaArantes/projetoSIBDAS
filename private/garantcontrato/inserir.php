<?php
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_role(['Administrador', 'Técnico'], '/private/garantcontrato/listar.php');
start_session();
require_once __DIR__ . '/../includes/validacoes.php';

$equipamentos   = [];
$tipos_contrato = get_tipos_contrato();
$periodicidades = get_periodicidades();
$erros          = [];
$erro_sistema   = "";

try {
    $pdo          = get_pdo();
    $equipamentos = $pdo->query("SELECT id, nome FROM equipamento ORDER BY nome")->fetchAll();
} catch (PDOException $err) {}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $entidade = ucwords(strtolower($_POST["entidade"] ?? ""));

    $erros = array_merge(
        validar_select_obrigatorio($_POST['equipamento'] ?? '', 'Equipamento Associado'),
        validar_select_obrigatorio($_POST['id_tipo'] ?? '', 'Tipo de Contrato')
    );

    $inicio = trim($_POST["inicio"] ?? "");
    $fim    = trim($_POST["fim"]    ?? "");

    if (!empty($inicio) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $inicio))
        $erros[] = "Formato de data de início inválido.";
    if (!empty($fim) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fim))
        $erros[] = "Formato de data de fim inválido.";
    if (!empty($inicio) && !empty($fim) && $fim < $inicio)
        $erros[] = "A data de fim não pode ser anterior à data de início.";

    if (empty($erros)) {
        try {
            $pdo  = get_pdo();
            $stmt = $pdo->prepare(
                "INSERT INTO garantia_contrato (id_equipamento, data_inicio, data_fim, tem_contrato,
                    tipo_contrato, id_tipo_contrato, entidade_responsavel,
                    periodicidade, id_periodicidade, observacoes)
                 VALUES (?, ?, ?, ?, (SELECT nome FROM tipos_contrato WHERE id=?), ?,
                    ?, (SELECT nome FROM periodicidades WHERE id=?), ?, ?)"
            );
            $stmt->execute([
                $_POST['equipamento'],
                $inicio ?: null,
                $fim    ?: null,
                isset($_POST['tem_contrato']) ? 1 : 0,
                $_POST['id_tipo'], $_POST['id_tipo'],
                $entidade,
                $_POST['id_periodicidade'] ?: null, $_POST['id_periodicidade'] ?: null,
                $_POST['observacoes']
            ]);

            $agente_id = $_SESSION['agente_id'] ?? null;
            registar_log('DADOS_ALTERADOS', 'Garantia/Contrato inserido para equipamento id: ' . $_POST['equipamento'], $agente_id);

            header("Location: listar.php");
            exit;
        } catch (PDOException $err) {
            $erro_sistema = "Erro ao gravar os dados: " . $err->getMessage();
        }
    }
}
?>
<?php include __DIR__ . '/../includes/header.php'; ?>
<body class="pagprivada">
<?php include __DIR__ . '/../includes/nav.php'; ?>

<main class="conteudo">
    <h1 class="mb-4">Inserir Garantia / Contrato</h1>

    <?php if (!empty($erros)) : ?>
        <div class="alert alert-danger"><strong>Foram encontrados os seguintes erros:</strong>
            <ul class="mb-0"><?php foreach ($erros as $erro) : ?><li><?= htmlspecialchars($erro) ?></li><?php endforeach; ?></ul>
        </div>
    <?php endif; ?>
    <?php if (!empty($erro_sistema)) : ?>
        <div class="alert alert-danger"><strong>Erro:</strong> <?= htmlspecialchars($erro_sistema) ?></div>
    <?php endif; ?>

    <form class="shadow p-4 rounded" style="max-width: 850px;" method="POST" action="inserir.php" novalidate>
        <div class="mb-3">
            <label class="form-label">Equipamento Associado <span class="text-danger">*</span></label>
            <select class="form-select" name="equipamento" required>
                <option value="">Selecione...</option>
                <?php foreach ($equipamentos as $eq) : ?>
                    <option value="<?= $eq->id ?>" <?= (($_POST['equipamento'] ?? '') == $eq->id) ? 'selected' : '' ?>><?= htmlspecialchars($eq->nome) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Data de início</label>
            <input type="text" id="data_inicio" class="form-control" name="inicio" value="<?= htmlspecialchars($_POST['inicio'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Data de fim</label>
            <input type="text" id="data_fim" class="form-control" name="fim" value="<?= htmlspecialchars($_POST['fim'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="tem_contrato" id="tem_contrato" value="1" <?= isset($_POST['tem_contrato']) ? 'checked' : '' ?>>
                <label class="form-check-label" for="tem_contrato">Tem contrato de manutenção associado</label>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Tipo de contrato <span class="text-danger">*</span></label>
            <select class="form-select" name="id_tipo" required>
                <option value="">Selecione...</option>
                <?php foreach ($tipos_contrato as $op) : ?>
                    <option value="<?= $op->id ?>" <?= (($_POST['id_tipo'] ?? '') == $op->id) ? 'selected' : '' ?>><?= htmlspecialchars($op->nome) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Entidade responsável</label>
            <input type="text" class="form-control" name="entidade" value="<?= htmlspecialchars($_POST['entidade'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Periodicidade</label>
            <select class="form-select" name="id_periodicidade">
                <option value="">Selecione...</option>
                <?php foreach ($periodicidades as $op) : ?>
                    <option value="<?= $op->id ?>" <?= (($_POST['id_periodicidade'] ?? '') == $op->id) ? 'selected' : '' ?>><?= htmlspecialchars($op->nome) ?></option>
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
    flatpickr("#data_inicio", { dateFormat: "Y-m-d" });
    flatpickr("#data_fim",    { dateFormat: "Y-m-d" });
</script>
<?php include __DIR__ . '/../includes/footer.php'; ?>