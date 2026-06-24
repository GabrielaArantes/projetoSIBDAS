<?php
// Página de confirmação antes de desativar um fornecedor (soft-delete)
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_role(['Administrador', 'Técnico'], '/private/fornecedores/listar.php');
start_session();

$idEncrypted = $_GET['id'] ?? null;
$id = aes_decrypt($idEncrypted);

if (!$id || !is_numeric($id)) {
    header("Location: listar.php");
    exit;
}

$id = (int)$id;

// Carregar fornecedor atual
try {
    $pdo = get_pdo();
    $stmt = $pdo->prepare("SELECT * FROM fornecedor WHERE id = ?");
    $stmt->execute([$id]);
    $fornecedor = $stmt->fetch();
    if (!$fornecedor) { header("Location: listar.php"); exit; }
} catch (PDOException $err) {
    header("Location: listar.php"); exit;
}

$ativoAtual = $fornecedor->fornecedor_ativo;
$novoAtivo  = ($ativoAtual == 1) ? 0 : 1;

// Se é reativação, faz diretamente sem perguntas
if ($ativoAtual == 0) {
    try {
        $pdo = get_pdo();
        $stmt = $pdo->prepare("UPDATE fornecedor SET fornecedor_ativo = 1 WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: listar.php");
        exit;
    } catch (PDOException $err) {
        header("Location: listar.php"); exit;
    }
}

// É inativação — verificar equipamentos associados
$equipamentosAssociados = [];
try {
    $pdo = get_pdo();
    $stmt = $pdo->prepare(
        "SELECT e.id, e.nome FROM equipamento e
         JOIN equipamento_fornecedor ef ON e.id = ef.id_equipamento
         WHERE ef.id_fornecedor = ? AND e.equipamento_ativo = 1"
    );
    $stmt->execute([$id]);
    $equipamentosAssociados = $stmt->fetchAll();
} catch (PDOException $err) {}

// Carregar outros fornecedores ativos para substituição
$outrosFornecedores = [];
try {
    $pdo = get_pdo();
    $stmt = $pdo->prepare("SELECT id, nome FROM fornecedor WHERE fornecedor_ativo = 1 AND id != ? ORDER BY nome");
    $stmt->execute([$id]);
    $outrosFornecedores = $stmt->fetchAll();
} catch (PDOException $err) {}

// Processar o POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_substituto = $_POST['id_substituto'] ?? '';

    // Se há equipamentos associados, o fornecedor substituto é obrigatório
    if (!empty($equipamentosAssociados) && empty($id_substituto)) {
        $erro = "É obrigatório selecionar um fornecedor substituto para os equipamentos associados.";
    } else {
        try {
            $pdo = get_pdo();

            // Se há equipamentos associados e foi escolhido um substituto, substitui
            if (!empty($equipamentosAssociados) && !empty($id_substituto)) {
                foreach ($equipamentosAssociados as $eq) {
                    // Verifica se o substituto já está associado
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM equipamento_fornecedor WHERE id_equipamento = ? AND id_fornecedor = ?");
                    $stmt->execute([$eq->id, $id_substituto]);
                    if ($stmt->fetchColumn() == 0) {
                        $stmt = $pdo->prepare("INSERT INTO equipamento_fornecedor (id_equipamento, id_fornecedor) VALUES (?, ?)");
                        $stmt->execute([$eq->id, $id_substituto]);
                    }
                    // Remove a associação com o fornecedor a inativar
                    $stmt = $pdo->prepare("DELETE FROM equipamento_fornecedor WHERE id_equipamento = ? AND id_fornecedor = ?");
                    $stmt->execute([$eq->id, $id]);
                }
            }

            // Inativar o fornecedor
            $stmt = $pdo->prepare("UPDATE fornecedor SET fornecedor_ativo = 0 WHERE id = ?");
            $stmt->execute([$id]);

            header("Location: listar.php");
            exit;
        } catch (PDOException $err) {
            $erro = "Erro: " . $err->getMessage();
        }
    }
}
?>
<?php include __DIR__ . '/../includes/header.php'; ?>
<body class="pagprivada">
<?php include __DIR__ . '/../includes/nav.php'; ?>

<main class="conteudo">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Inativar Fornecedor</h1>
        <a href="listar.php" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Voltar</a>
    </div>

    <div class="shadow p-4 rounded bg-white" style="max-width: 700px; margin: auto;">

        <?php if (!empty($equipamentosAssociados)) : ?>
            <?php if (!empty($erro)) : ?>
                <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
            <?php endif; ?>
            <div class="alert alert-warning">
                <i class="fa-solid fa-triangle-exclamation me-2"></i>
                <strong>Atenção!</strong> O fornecedor <strong><?= htmlspecialchars($fornecedor->nome) ?></strong> está associado aos seguintes equipamentos:
                <ul class="mt-2 mb-0">
                    <?php foreach ($equipamentosAssociados as $eq) : ?>
                        <li><?= htmlspecialchars($eq->nome) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label fw-bold">Substituir por outro fornecedor <span class="text-danger">*</span></label>
                    <select class="form-select" name="id_substituto" required>
                        <option value="">Selecione um fornecedor substituto...</option>
                        <?php foreach ($outrosFornecedores as $f) : ?>
                            <option value="<?= $f->id ?>"><?= htmlspecialchars($f->nome) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="form-text">É obrigatório selecionar um fornecedor substituto pois os equipamentos listados têm de ter pelo menos um fornecedor associado.</div>
                </div>

                <div class="d-flex gap-2 justify-content-end mt-4">
                    <a href="listar.php" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-danger">
                        <i class="fa-solid fa-ban me-1"></i> Inativar Fornecedor
                    </button>
                </div>
            </form>

        <?php else : ?>
            <p>Tem a certeza que pretende inativar o fornecedor <strong><?= htmlspecialchars($fornecedor->nome) ?></strong>?</p>
            <p class="text-muted">O fornecedor não tem equipamentos associados ativos. Esta operação pode ser revertida.</p>

            <form method="POST">
                <div class="d-flex gap-2 justify-content-end mt-4">
                    <a href="listar.php" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-danger">
                        <i class="fa-solid fa-ban me-1"></i> Inativar Fornecedor
                    </button>
                </div>
            </form>
        <?php endif; ?>

    </div>
</main>
<?php include __DIR__ . '/../includes/footer.php'; ?>