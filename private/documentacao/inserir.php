<?php
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_role(['Administrador', 'Técnico'], '/private/documentacao/listar.php');
start_session();
require_once __DIR__ . '/../includes/validacoes.php';

$equipamentos    = [];
$tipos_documento = get_tipos_documento();
$erros           = [];
$erro_sistema    = "";

try {
    $pdo          = get_pdo();
    $equipamentos = $pdo->query("SELECT id, nome FROM equipamento ORDER BY nome")->fetchAll();
} catch (PDOException $err) {}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $erros = array_merge(
        validar_nome($_POST['nome'] ?? ''),
        validar_select_obrigatorio($_POST['id_tipo'] ?? '', 'Tipo de Documento'),
        validar_data($_POST['data'] ?? '', 'Data do Documento'),
        validar_select_obrigatorio($_POST['equipamento'] ?? '', 'Equipamento Associado')
    );

    if (empty($_FILES['ficheiro']['name'])) $erros[] = "O Ficheiro é obrigatório.";

    $nomeFicheiro = '';
    $nomeOriginal = '';

    if (empty($erros)) {
        $nomeOriginal = $_FILES['ficheiro']['name'];
        $extensao     = strtolower(pathinfo($nomeOriginal, PATHINFO_EXTENSION));
        $nomeFicheiro = uniqid('doc_') . '.' . $extensao;
        $destino      = __DIR__ . '/../../assets/uploads/documentos/' . $nomeFicheiro;
        if (!move_uploaded_file($_FILES['ficheiro']['tmp_name'], $destino))
            $erros[] = "Erro ao guardar o ficheiro. Tente novamente.";
    }

    if (empty($erros)) {
        try {
            $pdo  = get_pdo();
            $stmt = $pdo->prepare(
                "INSERT INTO documento (id_equipamento, tipo, id_tipo_documento, nome, data_documento, data_validade, ficheiro, ficheiro_nome_original)
                 VALUES (?, (SELECT nome FROM tipos_documento WHERE id=?), ?, ?, ?, ?, ?, ?)"
            );
            $stmt->execute([
                $_POST['equipamento'],
                $_POST['id_tipo'], $_POST['id_tipo'],
                $_POST['nome'], $_POST['data'],
                $_POST['validade'] ?: null,
                $nomeFicheiro, $nomeOriginal
            ]);

            $agente_id = $_SESSION['agente_id'] ?? null;
            registar_log('DADOS_ALTERADOS', 'Documento inserido: ' . ($_POST['nome'] ?? ''), $agente_id);

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
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Inserir Documentação</h1>
        <a href="listar.php" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Voltar</a>
    </div>

    <?php if (!empty($erros)) : ?>
        <div class="alert alert-danger"><strong>Foram encontrados os seguintes erros:</strong>
            <ul class="mb-0"><?php foreach ($erros as $erro) : ?><li><?= htmlspecialchars($erro) ?></li><?php endforeach; ?></ul>
        </div>
    <?php endif; ?>
    <?php if (!empty($erro_sistema)) : ?>
        <div class="alert alert-danger"><strong>Erro:</strong> <?= htmlspecialchars($erro_sistema) ?></div>
    <?php endif; ?>

    <div class="shadow p-4 rounded bg-white" style="max-width: 700px; margin: auto;">
        <form method="POST" action="inserir.php" enctype="multipart/form-data" novalidate>

            <div class="mb-3">
                <label class="form-label">Tipo de Documento <span class="text-danger">*</span></label>
                <select class="form-select" name="id_tipo" required>
                    <option value="">Selecione o tipo...</option>
                    <?php foreach ($tipos_documento as $op) : ?>
                        <option value="<?= $op->id ?>" <?= (($_POST['id_tipo'] ?? '') == $op->id) ? 'selected' : '' ?>><?= htmlspecialchars($op->nome) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Nome do Documento <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="nome" value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Data do Documento <span class="text-danger">*</span></label>
                <input type="text" id="data_doc" class="form-control" name="data" value="<?= htmlspecialchars($_POST['data'] ?? '') ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Data de Validade (opcional)</label>
                <input type="text" id="data_validade" class="form-control" name="validade" value="<?= htmlspecialchars($_POST['validade'] ?? '') ?>">
            </div>

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
                <label class="form-label">Ficheiro <span class="text-danger">*</span></label>
                <input type="file" class="form-control" name="ficheiro" accept=".pdf,.jpg,.png,.doc,.docx" required>
            </div>

            <div class="d-flex justify-content-end mt-4">
                <button type="submit" class="btn btn-success px-4"><i class="fa-solid fa-floppy-disk me-2"></i>Guardar</button>
            </div>
        </form>
    </div>
</main>

<script>
    flatpickr("#data_doc", { dateFormat: "Y-m-d" });
    flatpickr("#data_validade", { dateFormat: "Y-m-d" });
</script>
<?php include __DIR__ . '/../includes/footer.php'; ?>