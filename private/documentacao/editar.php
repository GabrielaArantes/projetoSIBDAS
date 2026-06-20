<?php
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_logged();
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
$equipamentos = [];

$idEncrypted = $_GET['id'] ?? null;
$id = aes_decrypt($idEncrypted);

if (!$id || !is_numeric($id)) {
    header("Location: listar.php");
    exit;
}

$id = (int)$id;

// Carregar lista de equipamentos (necessária tanto para mostrar o form como para reconstruir após o POST)
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
    $erro = "Erro ao carregar lista de equipamentos.";
}

// Carregar dados atuais do documento já aqui, para termos o ficheiro atual disponível antes do POST
try {
    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8",
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $ligacao->prepare("SELECT * FROM documento WHERE id = ?");
    $stmt->execute([$id]);
    $documentoAtual = $stmt->fetch(PDO::FETCH_OBJ);
    $ligacao = null;

    if (!$documentoAtual) {
        header("Location: listar.php");
        exit;
    }
} catch (PDOException $err) {
    $erro = "Erro ao carregar documento.";
}

// 1. Tratar primeiro a submissão do formulário (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $erros = array_merge(
        validar_nome($_POST['nome'] ?? ''),
        validar_tipo_documento($_POST['tipo'] ?? ''),
        validar_data($_POST['data'] ?? '', 'Data do Documento'),
        validar_select_obrigatorio($_POST['equipamento'] ?? '', 'Equipamento Associado')
    );

    // Processar o upload do ficheiro, só se um novo ficheiro tiver sido escolhido
    $nomeFicheiro = $documentoAtual->ficheiro ?? '';
    $nomeOriginal = $documentoAtual->ficheiro_nome_original ?? '';

    if (empty($erros) && !empty($_FILES['ficheiro']['name'])) {
        $novoNomeOriginal = $_FILES['ficheiro']['name'];
        $extensao = strtolower(pathinfo($novoNomeOriginal, PATHINFO_EXTENSION));
        $novoNomeFicheiro = uniqid('doc_') . '.' . $extensao;
        $destino = __DIR__ . '/../../assets/uploads/documentos/' . $novoNomeFicheiro;

        if (!move_uploaded_file($_FILES['ficheiro']['tmp_name'], $destino)) {
            $erros[] = "Erro ao guardar o ficheiro. Tente novamente.";
        } else {
            $nomeFicheiro = $novoNomeFicheiro;
            $nomeOriginal = $novoNomeOriginal;
        }
    }

    if (empty($erros)) {
        try {
            $ligacao = new PDO(
                "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8",
                MYSQL_USERNAME,
                MYSQL_PASSWORD
            );
            $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $ligacao->prepare("UPDATE documento SET tipo=?, nome=?, data_documento=?, data_validade=?, id_equipamento=?, ficheiro=?, ficheiro_nome_original=? WHERE id=?");
            $stmt->execute([
                $_POST['tipo'],
                $_POST['nome'],
                $_POST['data'],
                $_POST['validade'] ?: null,
                $_POST['equipamento'],
                $nomeFicheiro,
                $nomeOriginal,
                $id
            ]);

            $ligacao = null;
            $sucesso = "Documento atualizado com sucesso!";

        } catch (PDOException $err) {
            $erro = "Erro ao atualizar: " . $err->getMessage();
        }
    }
}

// 2. Obter os dados atuais do documento (GET, ou para mostrar o formulário após o POST)
try {
    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8",
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $ligacao->prepare("SELECT * FROM documento WHERE id = ?");
    $stmt->execute([$id]);
    $documento = $stmt->fetch(PDO::FETCH_OBJ);
    $ligacao = null;

    if (!$documento) {
        header("Location: listar.php");
        exit;
    }
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
        <?php if (!empty($erros)) : ?>
            <div class="alert alert-danger">
                <?php foreach ($erros as $e) : ?>
                    <div><?= htmlspecialchars($e) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="shadow p-4 rounded bg-white" style="max-width: 700px; margin: auto;">

            <form method="POST" action="editar.php?id=<?= $idEncrypted ?>" enctype="multipart/form-data" novalidate autocomplete="off">

                <div class="mb-3">
                    <label class="form-label">Tipo de Documento <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="tipo" value="<?= htmlspecialchars($documento->tipo ?? '') ?>" required>
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
                    <button type="submit" class="btn btn-warning px-4">
                        <i class="fa-solid fa-floppy-disk me-2"></i>Guardar Alterações
                    </button>
                </div>

            </form>

        </div>

    </main>
    <?php include __DIR__ . '/../includes/footer.php'; ?>