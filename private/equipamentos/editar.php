<?php
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_role(['Administrador', 'Técnico'], '/private/equipamentos/listar.php');
start_session();
require_once __DIR__ . '/../includes/validacoes.php';

if (!in_array($_SERVER['REQUEST_METHOD'], ['GET', 'POST'])) {
    header('Location: ' . BASE_URL . '/public/login.php');
    exit;
}

$sucesso = '';
$erro = '';
$erros = [];
$equipamento = null;

$idEncrypted = $_GET['id'] ?? null;
$id = aes_decrypt($idEncrypted);
if (!$id || !is_numeric($id)) { header("Location: listar.php"); exit; }
$id = (int)$id;

// Carregar lookups
$categorias     = get_categorias();
$estados        = get_estados();
$criticidades   = get_criticidades();
$tipos_entrada  = get_tipos_entrada();
$tipos_doc      = get_tipos_documento();
$tipos_contrato = get_tipos_contrato();
$periodicidades = get_periodicidades();

$localizacoes = [];
$fornecedores = [];
$fornecedoresAssociados = [];

try {
    $pdo = get_pdo();
    $localizacoes = $pdo->query("SELECT id, edificio, piso, servico, sala FROM localizacao WHERE localizacao_ativo = 1 ORDER BY edificio, piso, servico, sala")->fetchAll();
    $fornecedores = $pdo->query("SELECT id, nome, id_tipo_fornecedor FROM fornecedor WHERE fornecedor_ativo = 1 ORDER BY nome")->fetchAll();
    $tipos_forn = [];
    foreach (get_tipos_fornecedor() as $t) $tipos_forn[$t->id] = $t->nome;
    $stmt = $pdo->prepare("SELECT id_fornecedor FROM equipamento_fornecedor WHERE id_equipamento = ?");
    $stmt->execute([$id]);
    $fornecedoresAssociados = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $err) {
    $erro = "Erro ao carregar dados.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $erros = array_merge(
        validar_designacao($_POST['designacao'] ?? ''),
        validar_marca($_POST['marca'] ?? ''),
        validar_modelo($_POST['modelo'] ?? ''),
        validar_numero_serie($_POST['numero_serie'] ?? ''),
        validar_fabricante($_POST['fabricante'] ?? ''),
        validar_select_obrigatorio($_POST['id_categoria'] ?? '', 'Categoria / Grupo'),
        validar_select_obrigatorio($_POST['id_tipo_entrada'] ?? '', 'Tipo de Entrada'),
        validar_select_obrigatorio($_POST['id_estado'] ?? '', 'Estado Atual'),
        validar_select_obrigatorio($_POST['id_criticidade'] ?? '', 'Criticidade'),
        validar_select_obrigatorio($_POST['localizacao'] ?? '', 'Localização')
    );

    if (empty($erros)) {
        try {
            $pdo = get_pdo();
            $custoAquisicao = ($_POST['custo_aquisicao'] === '') ? null : $_POST['custo_aquisicao'];
            $fornecedoresEscolhidos = $_POST['fornecedores'] ?? [];

            $stmt = $pdo->prepare(
                "UPDATE equipamento SET
                    codigo_interno=?, nome=?,
                    categoria=(SELECT nome FROM categorias_equipamento WHERE id=?), id_categoria=?,
                    marca=?, modelo=?, num_serie=?, fabricante=?, data_aquisicao=?, ano_fabrico=?, custo=?,
                    tipo_entrada=(SELECT nome FROM tipos_entrada WHERE id=?), id_tipo_entrada=?,
                    estado=(SELECT nome FROM estados_equipamento WHERE id=?), id_estado=?,
                    criticidade=(SELECT nome FROM criticidades WHERE id=?), id_criticidade=?,
                    observacoes=?, id_localizacao=?
                 WHERE id=?"
            );
            $stmt->execute([
                $_POST['codigo_interno'], $_POST['designacao'],
                $_POST['id_categoria'], $_POST['id_categoria'],
                $_POST['marca'], $_POST['modelo'], $_POST['numero_serie'], $_POST['fabricante'],
                $_POST['data_aquisicao'], $_POST['ano_fabrico'], $custoAquisicao,
                $_POST['id_tipo_entrada'], $_POST['id_tipo_entrada'],
                $_POST['id_estado'], $_POST['id_estado'],
                $_POST['id_criticidade'], $_POST['id_criticidade'],
                $_POST['observacoes'], (int)$_POST['localizacao'],
                $id
            ]);

            $stmt = $pdo->prepare("DELETE FROM equipamento_fornecedor WHERE id_equipamento = ?");
            $stmt->execute([$id]);
            if (!empty($fornecedoresEscolhidos)) {
                $stmt = $pdo->prepare("INSERT INTO equipamento_fornecedor (id_equipamento, id_fornecedor) VALUES (?, ?)");
                foreach ($fornecedoresEscolhidos as $idForn) $stmt->execute([$id, (int)$idForn]);
            }

            $sucesso = "Equipamento atualizado com sucesso!";
            $agente_id = $_SESSION['agente_id'] ?? null;
            registar_log('DADOS_ALTERADOS', 'Equipamento editado (id: ' . $id . '): ' . ($_POST['designacao'] ?? ''), $agente_id);
            $fornecedoresAssociados = $fornecedoresEscolhidos;

        } catch (PDOException $err) {
            $erro = "Erro ao atualizar: " . $err->getMessage();
        }

        // Upload de documento
        if (!empty($_FILES['documentos']['name'][0])) {
            $doc_tipo_id   = $_POST['doc_tipo'] ?? '';
            $doc_descricao = trim($_POST['doc_descricao'] ?? '');
            foreach ($_FILES['documentos']['name'] as $i => $nomeOriginal) {
                if (empty($nomeOriginal)) continue;
                $extensao     = strtolower(pathinfo($nomeOriginal, PATHINFO_EXTENSION));
                $nomeFicheiro = uniqid('doc_') . '.' . $extensao;
                $destino      = __DIR__ . '/../../assets/uploads/documentos/' . $nomeFicheiro;
                if (move_uploaded_file($_FILES['documentos']['tmp_name'][$i], $destino)) {
                    try {
                        $pdo2 = get_pdo();
                        $stmt = $pdo2->prepare(
                            "INSERT INTO documento (id_equipamento, tipo, id_tipo_documento, nome, data_documento, ficheiro, ficheiro_nome_original)
                             VALUES (?, (SELECT nome FROM tipos_documento WHERE id=?), ?, ?, CURDATE(), ?, ?)"
                        );
                        $stmt->execute([$id, $doc_tipo_id, $doc_tipo_id, $doc_descricao ?: $nomeOriginal, $nomeFicheiro, $nomeOriginal]);
                    } catch (PDOException $err) {
                        $erro = "Erro ao guardar o documento: " . $err->getMessage();
                    }
                }
            }
        }
    }
}

// Carregar equipamento atual
try {
    $pdo = get_pdo();
    $stmt = $pdo->prepare("SELECT e.*, l.edificio, l.piso, l.servico, l.sala FROM equipamento e LEFT JOIN localizacao l ON e.id_localizacao = l.id WHERE e.id = ?");
    $stmt->execute([$id]);
    $equipamento = $stmt->fetch();
    if (!$equipamento) { header("Location: listar.php"); exit; }
} catch (PDOException $err) {
    $erro = "Erro ao carregar equipamento.";
}
?>
<?php include __DIR__ . '/../includes/header.php'; ?>
<body class="pagprivada">
<?php include __DIR__ . '/../includes/nav.php'; ?>

<main class="conteudo p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Editar Equipamento</h1>
        <a href="listar.php" class="btn btn-secondary"><i class="fa-solid fa-arrow-left me-2"></i>Voltar</a>
    </div>

    <?php if (!empty($sucesso)) : ?><div class="alert alert-success"><?= $sucesso ?></div><?php endif; ?>
    <?php if (!empty($erro)) : ?><div class="alert alert-danger"><?= $erro ?></div><?php endif; ?>
    <?php if (!empty($erros)) : ?><div class="alert alert-danger"><?php foreach ($erros as $e) : ?><div><?= htmlspecialchars($e) ?></div><?php endforeach; ?></div><?php endif; ?>

    <form method="POST" action="editar.php?id=<?= $idEncrypted ?>" enctype="multipart/form-data" novalidate autocomplete="off" class="shadow p-4 rounded bg-white" style="max-width: 900px; margin: auto;">

        <ul class="nav nav-tabs mb-4" role="tablist">
            <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#dados" type="button" role="tab">Dados</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#fornecedor" type="button" role="tab">Fornecedor</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#localizacao" type="button" role="tab">Localização</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#garantia" type="button" role="tab">Garantia / Contrato</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#docs" type="button" role="tab">Documentação</button></li>
        </ul>

        <div class="tab-content">

            <div class="tab-pane fade show active" id="dados" role="tabpanel">
                <div class="p-3 border rounded bg-light">
                    <h5 class="fw-bold mb-3">Dados do Equipamento</h5>

                    <label>Código Interno <span class="text-danger">*</span></label>
                    <input type="text" class="form-control mb-2" name="codigo_interno" value="<?= htmlspecialchars($equipamento->codigo_interno ?? '') ?>" required>

                    <label>Designação <span class="text-danger">*</span></label>
                    <input type="text" class="form-control mb-2" name="designacao" value="<?= htmlspecialchars($equipamento->nome ?? '') ?>" required>

                    <label>Categoria / Grupo <span class="text-danger">*</span></label>
                    <select name="id_categoria" class="form-select mb-2" required>
                        <option value="">Selecione a categoria</option>
                        <?php foreach ($categorias as $op) : ?>
                            <option value="<?= $op->id ?>" <?= ($equipamento->id_categoria ?? '') == $op->id ? 'selected' : '' ?>><?= htmlspecialchars($op->nome) ?></option>
                        <?php endforeach; ?>
                    </select>

                    <label>Marca <span class="text-danger">*</span></label>
                    <input type="text" class="form-control mb-2" name="marca" value="<?= htmlspecialchars($equipamento->marca ?? '') ?>" required>

                    <label>Modelo <span class="text-danger">*</span></label>
                    <input type="text" class="form-control mb-2" name="modelo" value="<?= htmlspecialchars($equipamento->modelo ?? '') ?>" required>

                    <label>Número de Série <span class="text-danger">*</span></label>
                    <input type="text" class="form-control mb-2" name="numero_serie" value="<?= htmlspecialchars($equipamento->num_serie ?? '') ?>" required>

                    <label>Fabricante <span class="text-danger">*</span></label>
                    <input type="text" class="form-control mb-2" name="fabricante" value="<?= htmlspecialchars($equipamento->fabricante ?? '') ?>" required>

                    <label>Data de Aquisição <span class="text-danger">*</span></label>
                    <input type="date" class="form-control mb-2" name="data_aquisicao" value="<?= htmlspecialchars($equipamento->data_aquisicao ?? '') ?>" required>

                    <label>Ano de Fabrico <span class="text-danger">*</span></label>
                    <input type="number" class="form-control mb-2" name="ano_fabrico" value="<?= htmlspecialchars($equipamento->ano_fabrico ?? '') ?>" min="1900" max="2100" required>

                    <label>Custo de Aquisição (€)</label>
                    <input type="number" class="form-control mb-2" name="custo_aquisicao" value="<?= htmlspecialchars($equipamento->custo ?? '') ?>" min="0">

                    <label>Tipo de Entrada <span class="text-danger">*</span></label>
                    <select name="id_tipo_entrada" class="form-select mb-2" required>
                        <option value="">Selecione o tipo</option>
                        <?php foreach ($tipos_entrada as $op) : ?>
                            <option value="<?= $op->id ?>" <?= ($equipamento->id_tipo_entrada ?? '') == $op->id ? 'selected' : '' ?>><?= htmlspecialchars($op->nome) ?></option>
                        <?php endforeach; ?>
                    </select>

                    <label>Estado Atual <span class="text-danger">*</span></label>
                    <select name="id_estado" class="form-select mb-2" required>
                        <option value="">Selecione o estado</option>
                        <?php foreach ($estados as $op) : ?>
                            <?php if ($op->nome === 'Inativo') continue; ?>
                            <option value="<?= $op->id ?>" <?= ($equipamento->id_estado ?? '') == $op->id ? 'selected' : '' ?>><?= htmlspecialchars($op->nome) ?></option>
                        <?php endforeach; ?>
                    </select>

                    <label>Criticidade <span class="text-danger">*</span></label>
                    <select name="id_criticidade" class="form-select mb-2" required>
                        <option value="">Selecione a criticidade</option>
                        <?php foreach ($criticidades as $op) : ?>
                            <option value="<?= $op->id ?>" <?= ($equipamento->id_criticidade ?? '') == $op->id ? 'selected' : '' ?>><?= htmlspecialchars($op->nome) ?></option>
                        <?php endforeach; ?>
                    </select>

                    <label>Observações</label>
                    <textarea class="form-control mb-2" name="observacoes"><?= htmlspecialchars($equipamento->observacoes ?? '') ?></textarea>
                </div>
            </div>

            <div class="tab-pane fade" id="fornecedor" role="tabpanel">
                <div class="p-3 border rounded bg-light">
                    <h5 class="fw-bold mb-3">Fornecedores associados</h5>
                    <?php if (empty($fornecedores)) : ?>
                        <p class="text-muted">Ainda não existem fornecedores ativos. <a href="../fornecedores/listar.php">Adicionar fornecedor</a>.</p>
                    <?php else : ?>
                        <?php foreach ($fornecedores as $forn) : ?>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="fornecedores[]" value="<?= $forn->id ?>" id="forn_<?= $forn->id ?>" <?= in_array($forn->id, $fornecedoresAssociados) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="forn_<?= $forn->id ?>">
                                    <?= htmlspecialchars($forn->nome) ?>
                                    <span class="text-muted">(<?= htmlspecialchars($tipos_forn[$forn->id_tipo_fornecedor] ?? 'Tipo não definido') ?>)</span>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="tab-pane fade" id="localizacao" role="tabpanel">
                <div class="p-3 border rounded bg-light">
                    <h5 class="fw-bold mb-3">Localização do equipamento</h5>
                    <label>Localização <span class="text-danger">*</span></label>
                    <select class="form-select mb-2" name="localizacao" required>
                        <option value="">Selecione uma localização...</option>
                        <?php foreach ($localizacoes as $loc) : ?>
                            <option value="<?= $loc->id ?>" <?= ($equipamento->id_localizacao ?? '') == $loc->id ? 'selected' : '' ?>>
                                <?= htmlspecialchars($loc->edificio) ?> - <?= htmlspecialchars($loc->piso) ?> - <?= htmlspecialchars($loc->servico) ?> - <?= htmlspecialchars($loc->sala) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="tab-pane fade" id="garantia" role="tabpanel">
                <div class="p-3 border rounded bg-light">
                    <h5 class="fw-bold mb-3">Garantia / Contrato</h5>
                    <label>Data de início</label>
                    <input type="date" class="form-control mb-2" name="garantia_inicio">
                    <label>Data de fim</label>
                    <input type="date" class="form-control mb-2" name="garantia_fim">
                    <label>Tipo de contrato</label>
                    <select class="form-select mb-2" name="id_garantia_tipo">
                        <option value="">Selecione...</option>
                        <?php foreach ($tipos_contrato as $op) : ?>
                            <option value="<?= $op->id ?>"><?= htmlspecialchars($op->nome) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label>Entidade responsável</label>
                    <input type="text" class="form-control mb-2" name="garantia_entidade">
                    <label>Periodicidade</label>
                    <select class="form-select mb-2" name="id_garantia_periodicidade">
                        <option value="">Selecione...</option>
                        <?php foreach ($periodicidades as $op) : ?>
                            <option value="<?= $op->id ?>"><?= htmlspecialchars($op->nome) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label>Observações</label>
                    <textarea class="form-control mb-2" name="garantia_observacoes"></textarea>
                </div>
            </div>

            <div class="tab-pane fade" id="docs" role="tabpanel">
                <div class="p-3 border rounded bg-light">
                    <h5 class="fw-bold mb-3">Adicionar Documentação</h5>
                    <p class="text-muted small">Os documentos adicionados aqui ficam associados a este equipamento e aparecem no módulo de Documentação.</p>
                    <label>Tipo de Documento</label>
                    <select class="form-select mb-2" name="doc_tipo">
                        <option value="">Selecione...</option>
                        <?php foreach ($tipos_doc as $op) : ?>
                            <option value="<?= $op->id ?>"><?= htmlspecialchars($op->nome) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label>Descrição / Nome do Documento</label>
                    <input type="text" class="form-control mb-2" name="doc_descricao">
                    <label>Ficheiros</label>
                    <input type="file" class="form-control mb-2" name="documentos[]" multiple accept=".pdf,.jpg,.png,.doc,.docx">
                </div>
            </div>

        </div>

        <div class="d-flex justify-content-end mt-4">
            <button type="submit" class="btn btn-success px-4">
                <i class="fa-solid fa-floppy-disk me-2"></i>Guardar Alterações
            </button>
        </div>

    </form>
</main>
<?php include __DIR__ . '/../includes/footer.php'; ?>