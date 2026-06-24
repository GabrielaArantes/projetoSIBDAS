<?php
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_role(['Administrador', 'Técnico'], '/private/garantcontrato/listar.php');
start_session();
require_once __DIR__ . '/../includes/validacoes.php';

if (!in_array($_SERVER['REQUEST_METHOD'], ['GET', 'POST'])) {
    header('Location: ' . BASE_URL . '/public/login.php');
    exit;
}

$sucesso = '';
$erro    = '';
$erros   = [];
$gc      = null;

$idEncrypted = $_GET['id'] ?? null;
$id          = aes_decrypt($idEncrypted);
if (!$id || !is_numeric($id)) { header("Location: listar.php"); exit; }
$id = (int)$id;

$equipamentos   = [];
$tipos_contrato = get_tipos_contrato();
$periodicidades = get_periodicidades();

try {
    $pdo          = get_pdo();
    $equipamentos = $pdo->query("SELECT id, nome FROM equipamento ORDER BY nome")->fetchAll();
} catch (PDOException $err) {
    $erro = "Erro ao carregar lista de equipamentos.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $entidade = ucwords(strtolower($_POST['entidade'] ?? ''));
    $inicio   = trim($_POST['inicio'] ?? '');
    $fim      = trim($_POST['fim']    ?? '');

    $erros = array_merge(
        validar_select_obrigatorio($_POST['equipamento'] ?? '', 'Equipamento Associado'),
        validar_select_obrigatorio($_POST['id_tipo']     ?? '', 'Tipo de Contrato')
    );

    if (!empty($inicio) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $inicio))
        $erros[] = "Formato de data de início inválido.";
    if (!empty($fim) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fim))
        $erros[] = "Formato de data de fim inválido.";
    if (!empty($inicio) && $inicio > date('Y-m-d'))
        $erros[] = "A data de início não pode ser uma data futura.";
    if (!empty($inicio) && !empty($fim) && $fim <= $inicio)
        $erros[] = "A data de fim deve ser posterior à data de início.";

    if (empty($erros)) {
        try {
            $pdo  = get_pdo();
            $stmt = $pdo->prepare(
                "UPDATE garantia_contrato SET
                    id_equipamento=?, data_inicio=?, data_fim=?, tem_contrato=?,
                    tipo_contrato=(SELECT nome FROM tipos_contrato WHERE id=?), id_tipo_contrato=?,
                    entidade_responsavel=?,
                    periodicidade=(SELECT nome FROM periodicidades WHERE id=?), id_periodicidade=?,
                    observacoes=?
                 WHERE id=?"
            );
            $stmt->execute([
                $_POST['equipamento'],
                $inicio ?: null,
                $fim    ?: null,
                isset($_POST['tem_contrato']) ? 1 : 0,
                $_POST['id_tipo'], $_POST['id_tipo'],
                $entidade,
                $_POST['id_periodicidade'] ?: null, $_POST['id_periodicidade'] ?: null,
                $_POST['observacoes'],
                $id
            ]);

            $sucesso   = "Garantia/Contrato atualizado com sucesso!";
            $agente_id = $_SESSION['agente_id'] ?? null;
            registar_log('DADOS_ALTERADOS', 'Garantia/Contrato editado (id: ' . $id . ')', $agente_id);

        } catch (PDOException $err) {
            $erro = "Erro ao atualizar: " . $err->getMessage();
        }
    }
}

try {
    $pdo  = get_pdo();
    $stmt = $pdo->prepare("SELECT * FROM garantia_contrato WHERE id = ?");
    $stmt->execute([$id]);
    $gc = $stmt->fetch();
    if (!$gc) { header("Location: listar.php"); exit; }
} catch (PDOException $err) {
    $erro = "Erro ao carregar garantia/contrato.";
}
?>
<?php include __DIR__ . '/../includes/header.php'; ?>
<body class="pagprivada">
<?php include __DIR__ . '/../includes/nav.php'; ?>

<main class="conteudo">
    <h1 class="mb-4">Editar Garantia / Contrato</h1>

    <?php if (!empty($sucesso)) : ?><div class="alert alert-success"><?= $sucesso ?></div><?php endif; ?>
    <?php if (!empty($erro))    : ?><div class="alert alert-danger"><?= $erro ?></div><?php endif; ?>
    <?php if (!empty($erros))   : ?>
        <div class="alert alert-danger">
            <?php foreach ($erros as $e) : ?><div><?= htmlspecialchars($e) ?></div><?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form class="shadow p-4 rounded" style="max-width: 850px;" method="POST" action="editar.php?id=<?= $idEncrypted ?>" novalidate autocomplete="off">
        <div class="mb-3">
            <label class="form-label">Equipamento Associado <span class="text-danger">*</span></label>
            <select class="form-select" name="equipamento" required>
                <option value="">Selecione...</option>
                <?php foreach ($equipamentos as $eq) : ?>
                    <option value="<?= $eq->id ?>" <?= ($gc->id_equipamento ?? '') == $eq->id ? 'selected' : '' ?>><?= htmlspecialchars($eq->nome) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Data de início</label>
            <input type="date" class="form-control" name="inicio" value="<?= htmlspecialchars($gc->data_inicio ?? '') ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Data de fim</label>
            <input type="date" class="form-control" name="fim" value="<?= htmlspecialchars($gc->data_fim ?? '') ?>">
        </div>
        <div class="mb-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="tem_contrato" id="tem_contrato" value="1" <?= ($gc->tem_contrato ?? 0) ? 'checked' : '' ?>>
                <label class="form-check-label" for="tem_contrato">Tem contrato de manutenção associado</label>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Tipo de contrato <span class="text-danger">*</span></label>
            <select class="form-select" name="id_tipo" required>
                <option value="">Selecione...</option>
                <?php foreach ($tipos_contrato as $op) : ?>
                    <option value="<?= $op->id ?>" <?= ($gc->id_tipo_contrato ?? '') == $op->id ? 'selected' : '' ?>><?= htmlspecialchars($op->nome) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Entidade responsável</label>
            <input type="text" class="form-control" name="entidade" value="<?= htmlspecialchars($gc->entidade_responsavel ?? '') ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Periodicidade</label>
            <select class="form-select" name="id_periodicidade">
                <option value="">Selecione...</option>
                <?php foreach ($periodicidades as $op) : ?>
                    <option value="<?= $op->id ?>" <?= ($gc->id_periodicidade ?? '') == $op->id ? 'selected' : '' ?>><?= htmlspecialchars($op->nome) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Observações</label>
            <textarea class="form-control" name="observacoes" rows="4"><?= htmlspecialchars($gc->observacoes ?? '') ?></textarea>
        </div>
        <div class="d-flex justify-content-between mt-4">
            <a href="listar.php" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-warning">Guardar Alterações</button>
        </div>
    </form>
</main>
<?php include __DIR__ . '/../includes/footer.php'; ?>