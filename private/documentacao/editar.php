<?php
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_role(['Administrador', 'Técnico'], '/private/documentacao/listar.php');
start_session();
require_once __DIR__ . '/../includes/validacoes.php';

if (!in_array($_SERVER['REQUEST_METHOD'], ['GET', 'POST'])) {
    header('Location: ' . BASE_URL . '/public/login.php');
    exit;
}

$sucesso = '';
$erro = '';
$erros = [];
$documento = null;

$idEncrypted = $_GET['id'] ?? null;
$id = aes_decrypt($idEncrypted);
if (!$id || !is_numeric($id)) { header("Location: listar.php"); exit; }
$id = (int)$id;

$equipamentos    = [];
$tipos_documento = get_tipos_documento();

try {
    $pdo = get_pdo();
    $equipamentos = $pdo->query("SELECT id, nome FROM equipamento ORDER BY nome")->fetchAll();
} catch (PDOException $err) {
    $erro = "Erro ao carregar lista de equipamentos.";
}

// Carregar documento atual antes do POST (para o ficheiro)
try {
    $pdo  = get_pdo();
    $stmt = $pdo->prepare("SELECT * FROM documento WHERE id = ?");
    $stmt->execute([$id]);
    $documentoAtual = $stmt->fetch();
    if (!$documentoAtual) { header("Location: listar.php"); exit; }
} catch (PDOException $err) {
    $erro = "Erro ao carregar documento.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $erros = array_merge(
        validar_nome($_POST['nome'] ?? ''),
        validar_select_obrigatorio($_POST['id_tipo'] ?? '', 'Tipo de Documento'),
        validar_data($_POST['data'] ?? '', 'Data do Documento'),
        validar_select_obrigatorio($_POST['equipamento'] ?? '', 'Equipamento Associado')
    );

    $nomeFicheiro = $documentoAtual->ficheiro ?? '';
    $nomeOriginal = $documentoAtual->ficheiro_nome_original ?? '';

    if (empty($erros) && !empty($_FILES['ficheiro']['name'])) {
        $novoNomeOriginal = $_FILES['ficheiro']['name'];
        $extensao         = strtolower(pathinfo($novoNomeOriginal, PATHINFO_EXTENSION));
        $novoNomeFicheiro = uniqid('doc_') . '.' . $extensao;
        $destino          = __DIR__ . '/../../assets/uploads/documentos/' . $novoNomeFicheiro;
        if (!move_uploaded_file($_FILES['ficheiro']['tmp_name'], $destino)) {
            $erros[] = "Erro ao guardar o ficheiro.";
        } else {
            $nomeFicheiro = $novoNomeFicheiro;
            $nomeOriginal = $novoNomeOriginal;
        }
    }

    if (empty($erros)) {
        try {
            $pdo  = get_pdo();
            $stmt = $pdo->prepare(
                "UPDATE documento SET
                    tipo=(SELECT nome FROM tipos_documento WHERE id=?), id_tipo_documento=?,
                    nome=?, data_documento=?, data_validade=?, id_equipamento=?,
                    ficheiro=?, ficheiro_nome_original=?
                 WHERE id=?"
            );
            $stmt->execute([
                $_POST['id_tipo'], $_POST['id_tipo'],
                $_POST['nome'], $_POST['data'], $_POST['validade'] ?: null,
                $_POST['equipamento'], $nomeFicheiro, $nomeOriginal, $id
            ]);

            $sucesso   = "Documento atualizado com sucesso!";
            $agente_id = $_SESSION['agente_id'] ?? null;
            registar_log('DADOS_ALTERADOS', 'Documento editado (id: ' . $id . '): ' . ($_POST['nome'] ?? ''), $agente_id);

        } catch (PDOException $err) {
            $erro = "Erro ao atualizar: " . $err->getMessage();
        }
    }
}

// Recarregar documento
try {
    $pdo  = get_pdo();
    $stmt = $pdo->prepare("SELECT * FROM documento WHERE id = ?");
    $stmt->execute([$id]);
    $documento = $stmt->fetch();
    if (!$documento) { header("Location: listar.php"); exit; }
} catch (PDOException $err) {
    $erro = "Erro ao carregar documento.";
}
?>
<?php include __DIR__ . '/../includes/header.php'; ?>
<body class="pagprivada">
<?php include __DIR__ . '/../includes/nav.php'; ?>

<main class="conteudo">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Editar Documentação</h1>
        <a href="listar.php" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Voltar</a>
    </div>

    <?php if (!empty($sucesso)) : ?><div class="alert alert-success"><?= $sucesso ?></div><?php endif; ?>
    <?php if (!empty($erro)) : ?><div class="alert alert-danger"><?= $erro ?></div><?php endif; ?>
    <?php if (!empty($erros)) : ?><div class="alert alert-danger"><?php foreach ($erros as $e) : ?><div><?= htmlspecialchars($e) ?></div><?php endforeach; ?></div><?php endif; ?>

    <div class="shadow p-4 rounded bg-white" style="max-width: 700px; margin: auto;">
        <form method="POST" action="editar.php?id=<?= $idEncrypted ?>" enctype="multipart/form-data" novalidate autocomplete="off">

            <div class="mb-3">
                <label class="form-label">Tipo de Documento <span class="text-danger">*</span></label>
                <select class="form-select" name="id_tipo" required>
                    <option value="">Selecione o tipo...</option>
                    <?php foreach ($tipos_documento as $op) : ?>
                        <option value="<?= $op->id ?>" <?= ($documento->id_tipo_documento ?? '') == $op->id ? 'selected' : '' ?>><?= htmlspecialchars($op->nome) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Nome do Documento <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="nome" value="<?= htmlspecialchars($documento->nome ?? '') ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Data do Documento <span class="text-danger">*</span></label>
                <input type="date" class="form-control" name="data" value="<?= htmlspecialchars($documento->data_documento ?? '') ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Data de Validade (opcional)</label>
                <input type="date" class="form-control" name="validade" value="<?= htmlspecialchars($documento->data_validade ?? '') ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Equipamento Associado <span class="text-danger">*</span></label>
                <select class="form-select" name="equipamento" required>
                    <option value="">Selecione...</option>
                    <?php foreach ($equipamentos as $eq) : ?>
                        <option value="<?= $eq->id ?>" <?= ($documento->id_equipamento ?? '') == $eq->id ? 'selected' : '' ?>><?= htmlspecialchars($eq->nome) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <?php if (!empty($documento->ficheiro)) : ?>
            <div class="mb-3">
                <label class="form-label">Ficheiro Atual</label>
                <p class="text-muted">
                    <?= htmlspecialchars($documento->ficheiro_nome_original ?? $documento->ficheiro) ?>
                    <a href="<?= BASE_URL ?>/assets/uploads/documentos/<?= rawurlencode($documento->ficheiro) ?>" target="_blank" class="btn btn-outline-primary btn-sm ms-2">
                        <i class="fa-solid fa-file"></i> Abrir Ficheiro
                    </a>
                </p>
            </div>
            <?php endif; ?>

            <div class="mb-3">
                <label class="form-label">Substituir Ficheiro (opcional)</label>
                <input type="file" class="form-control" name="ficheiro" accept=".pdf,.jpg,.png,.doc,.docx">
            </div>

            <div class="d-flex justify-content-end mt-4">
                <button type="submit" class="btn btn-warning px-4"><i class="fa-solid fa-floppy-disk me-2"></i>Guardar Alterações</button>
            </div>
        </form>
    </div>
</main>
<?php include __DIR__ . '/../includes/footer.php'; ?>