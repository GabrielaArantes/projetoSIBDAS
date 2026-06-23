<?php
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_role(['Administrador', 'Técnico'], '/private/localizacao/listar.php');
start_session();

$idEncrypted = $_GET['id'] ?? null;
$id = aes_decrypt($idEncrypted);

if (!$id || !is_numeric($id)) {
    header("Location: listar.php");
    exit;
}

$id = (int)$id;

// Carregar localização atual
try {
    $pdo = get_pdo();
    $stmt = $pdo->prepare("SELECT * FROM localizacao WHERE id = ?");
    $stmt->execute([$id]);
    $localizacao = $stmt->fetch();
    if (!$localizacao) { header("Location: listar.php"); exit; }
} catch (PDOException $err) {
    header("Location: listar.php"); exit;
}

$ativoAtual = $localizacao->localizacao_ativo;

// Se é reativação, faz diretamente
if ($ativoAtual == 0) {
    try {
        $pdo = get_pdo();
        $stmt = $pdo->prepare("UPDATE localizacao SET localizacao_ativo = 1 WHERE id = ?");
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
        "SELECT id, nome FROM equipamento WHERE id_localizacao = ? AND equipamento_ativo = 1"
    );
    $stmt->execute([$id]);
    $equipamentosAssociados = $stmt->fetchAll();
} catch (PDOException $err) {}

// Carregar outras localizações ativas para substituição
$outrasLocalizacoes = [];
try {
    $pdo = get_pdo();
    $stmt = $pdo->prepare("SELECT id, edificio, piso, servico, sala FROM localizacao WHERE localizacao_ativo = 1 AND id != ? ORDER BY edificio, piso, servico");
    $stmt->execute([$id]);
    $outrasLocalizacoes = $stmt->fetchAll();
} catch (PDOException $err) {}

// Processar o POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_substituto = $_POST['id_substituto'] ?? '';

    // Se há equipamentos associados, a localização substituta é obrigatória
    if (!empty($equipamentosAssociados) && empty($id_substituto)) {
        $erro = "É obrigatório selecionar uma localização para transferir os equipamentos.";
    } else {
        try {
            $pdo = get_pdo();

        // Se há equipamentos associados e foi escolhida uma localização substituta
        if (!empty($equipamentosAssociados) && !empty($id_substituto)) {
            $stmt = $pdo->prepare("UPDATE equipamento SET id_localizacao = ? WHERE id_localizacao = ? AND equipamento_ativo = 1");
            $stmt->execute([$id_substituto, $id]);
        }

        // Inativar a localização
        $stmt = $pdo->prepare("UPDATE localizacao SET localizacao_ativo = 0 WHERE id = ?");
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
        <h1>Inativar Localização</h1>
        <a href="listar.php" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Voltar</a>
    </div>

    <div class="shadow p-4 rounded bg-white" style="max-width: 700px; margin: auto;">

        <?php if (!empty($equipamentosAssociados)) : ?>
            <?php if (!empty($erro)) : ?>
                <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
            <?php endif; ?>
            <div class="alert alert-warning">
                <i class="fa-solid fa-triangle-exclamation me-2"></i>
                <strong>Atenção!</strong> A localização <strong><?= htmlspecialchars($localizacao->edificio . ' - ' . $localizacao->servico) ?></strong> está associada aos seguintes equipamentos:
                <ul class="mt-2 mb-0">
                    <?php foreach ($equipamentosAssociados as $eq) : ?>
                        <li><?= htmlspecialchars($eq->nome) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label fw-bold">Transferir equipamentos para outra localização <span class="text-danger">*</span></label>
                    <select class="form-select" name="id_substituto" required>
                        <option value="">Selecione uma localização...</option>
                        <?php foreach ($outrasLocalizacoes as $loc) : ?>
                            <option value="<?= $loc->id ?>">
                                <?= htmlspecialchars($loc->edificio . ' - ' . $loc->piso . ' - ' . $loc->servico . ' - ' . $loc->sala) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="form-text">Selecione para onde os equipamentos listados serão transferidos. A localização é obrigatória em todos os equipamentos.</div>
                </div>

                <div class="d-flex gap-2 justify-content-end mt-4">
                    <a href="listar.php" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-danger">
                        <i class="fa-solid fa-ban me-1"></i> Inativar Localização
                    </button>
                </div>
            </form>

        <?php else : ?>
            <p>Tem a certeza que pretende inativar a localização <strong><?= htmlspecialchars($localizacao->edificio . ' - ' . $localizacao->servico) ?></strong>?</p>
            <p class="text-muted">Esta localização não tem equipamentos associados. Esta operação pode ser revertida.</p>

            <form method="POST">
                <div class="d-flex gap-2 justify-content-end mt-4">
                    <a href="listar.php" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-danger">
                        <i class="fa-solid fa-ban me-1"></i> Inativar Localização
                    </button>
                </div>
            </form>
        <?php endif; ?>

    </div>
</main>
<?php include __DIR__ . '/../includes/footer.php'; ?>