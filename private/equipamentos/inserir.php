<?php
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_role(['Administrador', 'Técnico'], '/private/equipamentos/listar.php');
start_session();
require_once __DIR__ . '/../includes/validacoes.php';

// Carregar lookups
$categorias     = get_categorias();
$estados        = get_estados();
$criticidades   = get_criticidades();
$tipos_entrada  = get_tipos_entrada();
$tipos_doc      = get_tipos_documento();
$tipos_contrato = get_tipos_contrato();
$periodicidades = get_periodicidades();

$fornecedores = [];
$localizacoes = [];

try {
    $pdo = get_pdo();
    $tipos_forn = [];
    foreach (get_tipos_fornecedor() as $t) $tipos_forn[$t->id] = $t->nome;
    $fornecedores = $pdo->query("SELECT id, nome, id_tipo_fornecedor FROM fornecedor WHERE fornecedor_ativo = 1 ORDER BY nome")->fetchAll();
    $localizacoes = $pdo->query("SELECT id, edificio, piso, servico, sala FROM localizacao WHERE localizacao_ativo = 1 ORDER BY edificio, piso, servico, sala")->fetchAll();
} catch (PDOException $err) {
}

$erros = [];
$erro_sistema = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $codigo_interno          = $_POST["codigo_interno"]          ?? "";
    $id_localizacao_escolhida = $_POST["localizacao"]            ?? "";
    $fornecedores_escolhidos = $_POST["fornecedores"]            ?? [];
    $garantia_inicio         = $_POST["garantia_inicio"]         ?? "";
    $garantia_fim            = $_POST["garantia_fim"]            ?? "";
    $id_garantia_tipo        = $_POST["id_garantia_tipo"]        ?? "";
    $id_garantia_per         = $_POST["id_garantia_periodicidade"] ?? "";
    $garantia_entidade       = ucwords(strtolower($_POST["garantia_entidade"] ?? ""));
    $garantia_obs            = $_POST["garantia_observacoes"]    ?? "";
    $tem_contrato            = isset($_POST['tem_contrato']) ? 1 : 0;

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
        validar_select_obrigatorio($id_localizacao_escolhida, 'Localização')
    );

    if (empty($fornecedores_escolhidos)) $erros[] = 'É obrigatório associar pelo menos um fornecedor.';

    if (empty($erros)) {
        try {
            $pdo = get_pdo();
            $custoAquisicao = ($_POST['custo_aquisicao'] === '') ? null : $_POST['custo_aquisicao'];

            $stmt = $pdo->prepare(
                "INSERT INTO equipamento (codigo_interno, nome,
                    categoria, id_categoria,
                    marca, modelo, num_serie, fabricante, data_aquisicao, ano_fabrico, custo,
                    tipo_entrada, id_tipo_entrada,
                    estado, id_estado,
                    criticidade, id_criticidade,
                    observacoes, id_localizacao)
                 VALUES (?, ?,
                    (SELECT nome FROM categorias_equipamento WHERE id=?), ?,
                    ?, ?, ?, ?, ?, ?, ?,
                    (SELECT nome FROM tipos_entrada WHERE id=?), ?,
                    (SELECT nome FROM estados_equipamento WHERE id=?), ?,
                    (SELECT nome FROM criticidades WHERE id=?), ?,
                    ?, ?)"
            );
            $stmt->execute([
                strtoupper(trim($codigo_interno)),
                $_POST['designacao'],
                $_POST['id_categoria'], $_POST['id_categoria'],
                $_POST['marca'], $_POST['modelo'], $_POST['numero_serie'], $_POST['fabricante'],
                $_POST['data_aquisicao'], $_POST['ano_fabrico'], $custoAquisicao,
                $_POST['id_tipo_entrada'], $_POST['id_tipo_entrada'],
                $_POST['id_estado'], $_POST['id_estado'],
                $_POST['id_criticidade'], $_POST['id_criticidade'],
                $_POST['observacoes'],
                (int)$id_localizacao_escolhida
            ]);
            $id_equipamento = $pdo->lastInsertId();

            // Associar fornecedores
            $stmt = $pdo->prepare("INSERT INTO equipamento_fornecedor (id_equipamento, id_fornecedor) VALUES (?, ?)");
            foreach ($fornecedores_escolhidos as $idForn) {
                $stmt->execute([$id_equipamento, (int)$idForn]);
            }

            // Inserir garantia (só se datas preenchidas)
            if (!empty($garantia_inicio) || !empty($garantia_fim)) {
                $stmt = $pdo->prepare(
                    "INSERT INTO garantia_contrato (id_equipamento, data_inicio, data_fim, tem_contrato,
                        tipo_contrato, id_tipo_contrato, entidade_responsavel,
                        periodicidade, id_periodicidade, observacoes)
                     VALUES (?, ?, ?, ?, (SELECT nome FROM tipos_contrato WHERE id=?), ?,
                        ?, (SELECT nome FROM periodicidades WHERE id=?), ?, ?)"
                );
                $stmt->execute([
                    $id_equipamento,
                    $garantia_inicio ?: null,
                    $garantia_fim    ?: null,
                    $tem_contrato,
                    $id_garantia_tipo ?: null, $id_garantia_tipo ?: null,
                    $garantia_entidade,
                    $id_garantia_per ?: null, $id_garantia_per ?: null,
                    $garantia_obs
                ]);
            }

            // Processar upload de documento
            if (!empty($_FILES['documentos']['name'][0])) {
                $doc_tipo_id   = $_POST['doc_tipo'] ?? '';
                $doc_descricao = trim($_POST['doc_descricao'] ?? '');
                foreach ($_FILES['documentos']['name'] as $i => $nomeOriginal) {
                    if (empty($nomeOriginal)) continue;
                    $extensao     = strtolower(pathinfo($nomeOriginal, PATHINFO_EXTENSION));
                    $nomeFicheiro = uniqid('doc_') . '.' . $extensao;
                    $destino      = __DIR__ . '/../../assets/uploads/documentos/' . $nomeFicheiro;
                    if (move_uploaded_file($_FILES['documentos']['tmp_name'][$i], $destino)) {
                        $stmt = $pdo->prepare(
                            "INSERT INTO documento (id_equipamento, tipo, id_tipo_documento, nome, data_documento, ficheiro, ficheiro_nome_original)
                             VALUES (?, (SELECT nome FROM tipos_documento WHERE id=?), ?, ?, CURDATE(), ?, ?)"
                        );
                        $stmt->execute([$id_equipamento, $doc_tipo_id, $doc_tipo_id, $doc_descricao ?: $nomeOriginal, $nomeFicheiro, $nomeOriginal]);
                    }
                }
            }

            $agente_id = $_SESSION['agente_id'] ?? null;
            registar_log('DADOS_ALTERADOS', 'Equipamento inserido: ' . ($_POST['designacao'] ?? ''), $agente_id);

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

<main class="conteudo p-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Inserir Equipamento</h1>
        <a href="listar.php" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left me-2"></i>Voltar
        </a>
    </div>

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
            <strong>Erro:</strong> <?= htmlspecialchars($erro_sistema) ?>
        </div>
    <?php endif; ?>

    <form action="inserir.php" method="POST" enctype="multipart/form-data" novalidate class="shadow p-4 rounded bg-white" style="max-width: 900px; margin: auto;">

        <ul class="nav nav-tabs mb-4" id="equipTabs" role="tablist">
            <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#dados" type="button" role="tab">Dados do Equipamento</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#fornecedor" type="button" role="tab">Fornecedor <span class="text-danger">*</span></button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#localizacao" type="button" role="tab">Localização</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#garantia" type="button" role="tab">Garantia / Contrato</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#docs" type="button" role="tab">Documentação</button></li>
        </ul>

        <div class="tab-content" id="equipTabsContent">

            <!-- Tab: Dados do Equipamento -->
            <div class="tab-pane fade show active" id="dados" role="tabpanel">
                <div class="p-3 border rounded bg-light">
                    <h5 class="fw-bold mb-3">Dados do Equipamento</h5>

                    <label>Código Interno de Inventário <span class="text-danger">*</span></label>
                    <input type="text" name="codigo_interno" class="form-control mb-2"
                        value="<?= htmlspecialchars($_POST['codigo_interno'] ?? '') ?>" required>

                    <label>Designação do Equipamento <span class="text-danger">*</span></label>
                    <input type="text" name="designacao" class="form-control mb-2"
                        value="<?= htmlspecialchars($_POST['designacao'] ?? '') ?>" required>

                    <label>Categoria / Grupo <span class="text-danger">*</span></label>
                    <select name="id_categoria" class="form-select mb-2" required>
                        <option value="">Selecione a categoria</option>
                        <?php foreach ($categorias as $op) : ?>
                            <option value="<?= $op->id ?>" <?= (($_POST['id_categoria'] ?? '') == $op->id) ? 'selected' : '' ?>><?= htmlspecialchars($op->nome) ?></option>
                        <?php endforeach; ?>
                    </select>

                    <label>Marca <span class="text-danger">*</span></label>
                    <input type="text" name="marca" class="form-control mb-2"
                        value="<?= htmlspecialchars($_POST['marca'] ?? '') ?>" required>

                    <label>Modelo <span class="text-danger">*</span></label>
                    <input type="text" name="modelo" class="form-control mb-2"
                        value="<?= htmlspecialchars($_POST['modelo'] ?? '') ?>" required>

                    <label>Número de Série <span class="text-danger">*</span></label>
                    <input type="text" name="numero_serie" class="form-control mb-2"
                        value="<?= htmlspecialchars($_POST['numero_serie'] ?? '') ?>" required>

                    <label>Fabricante <span class="text-danger">*</span></label>
                    <input type="text" name="fabricante" class="form-control mb-2"
                        value="<?= htmlspecialchars($_POST['fabricante'] ?? '') ?>" required>

                    <label>Data de Aquisição</label>
                    <input type="text" id="data_aquisicao" name="data_aquisicao" class="form-control mb-2"
                        value="<?= htmlspecialchars($_POST['data_aquisicao'] ?? '') ?>">

                    <label>Ano de Fabrico</label>
                    <input type="number" name="ano_fabrico" class="form-control mb-2" min="1900" max="2100"
                        value="<?= htmlspecialchars($_POST['ano_fabrico'] ?? '') ?>">

                    <label>Custo de Aquisição (€)</label>
                    <input type="number" name="custo_aquisicao" class="form-control mb-2" min="0"
                        value="<?= htmlspecialchars($_POST['custo_aquisicao'] ?? '') ?>">

                    <label>Tipo de Entrada <span class="text-danger">*</span></label>
                    <select name="id_tipo_entrada" class="form-select mb-2" required>
                        <option value="">Selecione o tipo</option>
                        <?php foreach ($tipos_entrada as $op) : ?>
                            <option value="<?= $op->id ?>" <?= (($_POST['id_tipo_entrada'] ?? '') == $op->id) ? 'selected' : '' ?>><?= htmlspecialchars($op->nome) ?></option>
                        <?php endforeach; ?>
                    </select>

                    <label>Estado Atual <span class="text-danger">*</span></label>
                    <select name="id_estado" class="form-select mb-2" required>
                        <option value="">Selecione o estado</option>
                        <?php foreach ($estados as $op) : ?>
                            <?php if ($op->nome === 'Inativo') continue; ?>
                            <option value="<?= $op->id ?>" <?= (($_POST['id_estado'] ?? '') == $op->id) ? 'selected' : '' ?>><?= htmlspecialchars($op->nome) ?></option>
                        <?php endforeach; ?>
                    </select>

                    <label>Criticidade <span class="text-danger">*</span></label>
                    <select name="id_criticidade" class="form-select mb-2" required>
                        <option value="">Selecione a criticidade</option>
                        <?php foreach ($criticidades as $op) : ?>
                            <option value="<?= $op->id ?>" <?= (($_POST['id_criticidade'] ?? '') == $op->id) ? 'selected' : '' ?>><?= htmlspecialchars($op->nome) ?></option>
                        <?php endforeach; ?>
                    </select>

                    <label>Observações</label>
                    <textarea name="observacoes" class="form-control mb-2" rows="4"><?= htmlspecialchars($_POST['observacoes'] ?? '') ?></textarea>
                </div>
            </div>

            <!-- Tab: Fornecedor -->
            <div class="tab-pane fade" id="fornecedor" role="tabpanel">
                <div class="p-3 border rounded bg-light">
                    <h5 class="fw-bold mb-3">Fornecedores associados</h5>
                    <p class="text-muted small">Um equipamento pode ter vários fornecedores associados (ex: fabricante, distribuidor, assistência técnica). Apenas fornecedores ativos são apresentados.</p>
                    <?php if (empty($fornecedores)) : ?>
                        <p class="text-muted">Ainda não existem fornecedores ativos. <a href="../fornecedores/listar.php">Adicionar fornecedor</a>.</p>
                    <?php else : ?>
                        <?php foreach ($fornecedores as $forn) : ?>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="fornecedores[]" value="<?= $forn->id ?>"
                                    id="forn_<?= $forn->id ?>"
                                    <?= in_array($forn->id, $_POST['fornecedores'] ?? []) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="forn_<?= $forn->id ?>">
                                    <?= htmlspecialchars($forn->nome) ?>
                                    <span class="text-muted">(<?= htmlspecialchars($tipos_forn[$forn->id_tipo_fornecedor] ?? 'Tipo não definido') ?>)</span>
                                </label>
                            </div>
                        <?php endforeach; ?>
                        <p class="text-muted small mt-2">Caso queira outro fornecedor, pode adicioná-lo em <a href="../fornecedores/listar.php">Fornecedores</a>.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Tab: Localização -->
            <div class="tab-pane fade" id="localizacao" role="tabpanel">
                <div class="p-3 border rounded bg-light">
                    <h5 class="fw-bold mb-3">Localização do equipamento</h5>
                    <label>Localização <span class="text-danger">*</span></label>
                    <select class="form-select mb-2" name="localizacao" required>
                        <option value="">Selecione uma localização...</option>
                        <?php foreach ($localizacoes as $loc) : ?>
                            <option value="<?= $loc->id ?>" <?= (($_POST['localizacao'] ?? '') == $loc->id) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($loc->edificio) ?> - <?= htmlspecialchars($loc->piso) ?> - <?= htmlspecialchars($loc->servico) ?> - <?= htmlspecialchars($loc->sala) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="form-text">Não encontra a localização? Crie-a primeiro em <a href="../localizacao/inserir.php">Localização → Adicionar</a>.</div>
                </div>
            </div>

            <!-- Tab: Garantia -->
            <div class="tab-pane fade" id="garantia" role="tabpanel">
                <div class="p-3 border rounded bg-light">
                    <h5 class="fw-bold mb-3">Garantia / Contrato associado</h5>
                    <p class="text-muted small">As garantias adicionadas aqui aparecem no módulo de Garantias e Contratos.</p>

                    <label>Data de início</label>
                    <input type="text" id="garantia_inicio" class="form-control mb-2" name="garantia_inicio"
                        value="<?= htmlspecialchars($_POST['garantia_inicio'] ?? '') ?>">

                    <label>Data de fim</label>
                    <input type="text" id="garantia_fim" class="form-control mb-2" name="garantia_fim"
                        value="<?= htmlspecialchars($_POST['garantia_fim'] ?? '') ?>">

                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="tem_contrato" id="tem_contrato" value="1"
                            <?= isset($_POST['tem_contrato']) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="tem_contrato">Tem contrato de manutenção associado</label>
                    </div>

                    <label>Tipo de contrato</label>
                    <select class="form-select mb-2" name="id_garantia_tipo">
                        <option value="">Selecione...</option>
                        <?php foreach ($tipos_contrato as $op) : ?>
                            <option value="<?= $op->id ?>" <?= (($_POST['id_garantia_tipo'] ?? '') == $op->id) ? 'selected' : '' ?>><?= htmlspecialchars($op->nome) ?></option>
                        <?php endforeach; ?>
                    </select>

                    <label>Entidade responsável</label>
                    <input type="text" class="form-control mb-2" name="garantia_entidade"
                        value="<?= htmlspecialchars($_POST['garantia_entidade'] ?? '') ?>">

                    <label>Periodicidade</label>
                    <select class="form-select mb-2" name="id_garantia_periodicidade">
                        <option value="">Selecione...</option>
                        <?php foreach ($periodicidades as $op) : ?>
                            <option value="<?= $op->id ?>" <?= (($_POST['id_garantia_periodicidade'] ?? '') == $op->id) ? 'selected' : '' ?>><?= htmlspecialchars($op->nome) ?></option>
                        <?php endforeach; ?>
                    </select>

                    <label>Observações</label>
                    <textarea class="form-control mb-2" name="garantia_observacoes"><?= htmlspecialchars($_POST['garantia_observacoes'] ?? '') ?></textarea>
                </div>
            </div>

            <!-- Tab: Documentação -->
            <div class="tab-pane fade" id="docs" role="tabpanel">
                <div class="p-3 border rounded bg-light">
                    <h5 class="fw-bold mb-3">Documentação associada</h5>
                    <p class="text-muted small">Os documentos adicionados aqui aparecem no módulo de Documentação.</p>

                    <label>Tipo de Documento</label>
                    <select class="form-select mb-2" name="doc_tipo">
                        <option value="">Selecione...</option>
                        <?php foreach ($tipos_doc as $op) : ?>
                            <option value="<?= $op->id ?>" <?= (($_POST['doc_tipo'] ?? '') == $op->id) ? 'selected' : '' ?>><?= htmlspecialchars($op->nome) ?></option>
                        <?php endforeach; ?>
                    </select>

                    <label>Descrição / Nome do Documento</label>
                    <input type="text" class="form-control mb-2" name="doc_descricao"
                        value="<?= htmlspecialchars($_POST['doc_descricao'] ?? '') ?>">

                    <label>Ficheiros</label>
                    <input type="file" class="form-control mb-2" name="documentos[]" multiple accept=".pdf,.jpg,.png,.doc,.docx">
                </div>
            </div>

        </div>

        <div class="d-flex justify-content-end mt-4">
            <button type="submit" class="btn btn-success px-4">
                <i class="fa-solid fa-check me-2"></i>Guardar Equipamento
            </button>
        </div>

    </form>
</main>

<script>
    flatpickr("#data_aquisicao", { dateFormat: "Y-m-d" });
    flatpickr("#garantia_inicio", { dateFormat: "Y-m-d" });
    flatpickr("#garantia_fim", { dateFormat: "Y-m-d" });
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>