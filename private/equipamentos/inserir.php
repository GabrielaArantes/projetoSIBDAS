<?php
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_role(['Administrador', 'Técnico'], '/private/equipamentos/listar.php');
start_session();

$fornecedores = [];
try {
    $pdo = get_pdo();
    $fornecedores = $pdo->query("SELECT id, nome, id_tipo_fornecedor FROM fornecedor WHERE fornecedor_ativo = 1 ORDER BY nome")->fetchAll();
    // Buscar nome do tipo para cada fornecedor
    $tipos_forn = [];
    foreach (get_tipos_fornecedor() as $t) $tipos_forn[$t->id] = $t->nome;
} catch (PDOException $err) {}

$localizacoes = [];
try {
    $pdo = get_pdo();
    $localizacoes = $pdo->query("SELECT id, edificio, piso, servico, sala FROM localizacao WHERE localizacao_ativo = 1 ORDER BY edificio, piso, servico, sala")->fetchAll();
} catch (PDOException $err) {}

$categorias    = get_categorias();
$estados       = get_estados();
$criticidades  = get_criticidades();
$tipos_entrada = get_tipos_entrada();
$tipos_doc     = get_tipos_documento();
$tipos_contrato = get_tipos_contrato();
$periodicidades = get_periodicidades();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $codigo_interno    = trim($_POST["codigo_interno"] ?? "");
    $designacao        = trim($_POST["designacao"] ?? "");
    $id_categoria      = $_POST["id_categoria"] ?? "";
    $marca             = trim($_POST["marca"] ?? "");
    $modelo            = trim($_POST["modelo"] ?? "");
    $numero_serie      = trim($_POST["numero_serie"] ?? "");
    $fabricante        = trim($_POST["fabricante"] ?? "");
    $data_aquisicao    = trim($_POST["data_aquisicao"] ?? "");
    $ano_fabrico       = trim($_POST["ano_fabrico"] ?? "");
    $custo_aquisicao   = trim($_POST["custo_aquisicao"] ?? "");
    $id_tipo_entrada   = $_POST["id_tipo_entrada"] ?? "";
    $id_estado         = $_POST["id_estado"] ?? "";
    $id_criticidade    = $_POST["id_criticidade"] ?? "";
    $observacoes       = $_POST["observacoes"] ?? "";
    $id_localizacao_escolhida = $_POST["localizacao"] ?? "";
    $fornecedores_escolhidos  = $_POST["fornecedores"] ?? [];
    $garantia_inicio   = $_POST["garantia_inicio"] ?? "";
    $garantia_fim      = $_POST["garantia_fim"] ?? "";
    $id_garantia_tipo  = $_POST["id_garantia_tipo"] ?? "";
    $garantia_entidade = $_POST["garantia_entidade"] ?? "";
    $id_garantia_period = $_POST["id_garantia_periodicidade"] ?? "";
    $garantia_obs      = $_POST["garantia_observacoes"] ?? "";

    $erros = [];
    $erro_sistema = "";

    if (empty($codigo_interno)) $erros[] = "O Código Interno é obrigatório.";
    if (empty($designacao))     $erros[] = "A Designação do equipamento é obrigatória.";
    elseif (preg_match('/\d/', $designacao)) $erros[] = "A Designação não pode conter números.";
    if (empty($id_categoria))   $erros[] = "A Categoria é obrigatória.";
    if (empty($marca))          $erros[] = "A Marca é obrigatória.";
    if (empty($modelo))         $erros[] = "O Modelo é obrigatório.";
    if (empty($numero_serie))   $erros[] = "O Número de Série é obrigatório.";
    if (empty($fabricante))     $erros[] = "O Fabricante é obrigatório.";

    if (empty($data_aquisicao)) {
        $erros[] = "A Data de Aquisição é obrigatória.";
    } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data_aquisicao)) {
        $erros[] = "Formato de Data de Aquisição inválido.";
    } else {
        $partes = explode('-', $data_aquisicao);
        if (!checkdate((int)$partes[1], (int)$partes[2], (int)$partes[0]))
            $erros[] = "Data de Aquisição inválida.";
    }

    if (empty($ano_fabrico)) {
        $erros[] = "O Ano de Fabrico é obrigatório.";
    } elseif (!preg_match('/^\d{4}$/', $ano_fabrico) || (int)$ano_fabrico < 1900 || (int)$ano_fabrico > 2100) {
        $erros[] = "Ano de Fabrico inválido.";
    }

    if ($custo_aquisicao === '') {
        $erros[] = "O Custo de Aquisição é obrigatório.";
    } elseif (!is_numeric($custo_aquisicao) || (float)$custo_aquisicao < 0) {
        $erros[] = "O Custo de Aquisição deve ser um número positivo.";
    }

    if (empty($id_tipo_entrada))  $erros[] = "O Tipo de Entrada é obrigatório.";
    if (empty($id_estado))        $erros[] = "O Estado atual é obrigatório.";
    if (empty($id_criticidade))   $erros[] = "A Criticidade é obrigatória.";
    if (empty($id_localizacao_escolhida)) $erros[] = "A Localização é obrigatória.";

    if (!empty($garantia_inicio) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $garantia_inicio))
        $erros[] = "Formato de data de início de garantia inválido.";
    if (!empty($garantia_fim) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $garantia_fim))
        $erros[] = "Formato de data de fim de garantia inválido.";

    if (empty($erros)) {
        $codigo_interno    = strtoupper($codigo_interno);
        $designacao        = ucwords(strtolower($designacao));
        $marca             = ucwords(strtolower($marca));
        $fabricante        = ucwords(strtolower($fabricante));
        $garantia_entidade = ucwords(strtolower($garantia_entidade));
    }

    if (empty($erros)) {
        try {
            $pdo = get_pdo();
            $id_localizacao = !empty($id_localizacao_escolhida) ? (int)$id_localizacao_escolhida : null;

            $stmt = $pdo->prepare(
                "INSERT INTO equipamento (codigo_interno, nome, categoria, id_categoria, marca, modelo, num_serie, fabricante,
                    data_aquisicao, ano_fabrico, custo, tipo_entrada, id_tipo_entrada, estado, id_estado,
                    criticidade, id_criticidade, observacoes, id_localizacao)
                 VALUES (?, ?, (SELECT nome FROM categorias_equipamento WHERE id=?), ?, ?, ?, ?, ?, ?, ?, ?,
                    (SELECT nome FROM tipos_entrada WHERE id=?), ?,
                    (SELECT nome FROM estados_equipamento WHERE id=?), ?,
                    (SELECT nome FROM criticidades WHERE id=?), ?, ?, ?)"
            );
            $stmt->execute([
                $codigo_interno, $designacao, $id_categoria, $id_categoria,
                $marca, $modelo, $numero_serie, $fabricante,
                $data_aquisicao, $ano_fabrico, $custo_aquisicao,
                $id_tipo_entrada, $id_tipo_entrada,
                $id_estado, $id_estado,
                $id_criticidade, $id_criticidade,
                $observacoes, $id_localizacao
            ]);
            $id_equipamento = $pdo->lastInsertId();

            if (!empty($fornecedores_escolhidos)) {
                $stmt = $pdo->prepare("INSERT INTO equipamento_fornecedor (id_equipamento, id_fornecedor) VALUES (?, ?)");
                foreach ($fornecedores_escolhidos as $idForn) {
                    $stmt->execute([$id_equipamento, (int)$idForn]);
                }
            }

            if (!empty($garantia_inicio) || !empty($garantia_fim)) {
                $stmt = $pdo->prepare(
                    "INSERT INTO garantia_contrato (id_equipamento, data_inicio, data_fim, tipo_contrato, id_tipo_contrato,
                        entidade_responsavel, periodicidade, id_periodicidade, observacoes)
                     VALUES (?, ?, ?, (SELECT nome FROM tipos_contrato WHERE id=?), ?,
                        ?, (SELECT nome FROM periodicidades WHERE id=?), ?, ?)"
                );
                $stmt->execute([
                    $id_equipamento,
                    $garantia_inicio ?: null,
                    $garantia_fim    ?: null,
                    $id_garantia_tipo, $id_garantia_tipo,
                    $garantia_entidade,
                    $id_garantia_period, $id_garantia_period,
                    $garantia_obs
                ]);
            }

            $agente_id = $_SESSION['agente_id'] ?? null;
            registar_log('DADOS_ALTERADOS', 'Equipamento inserido: ' . $designacao . ' (código: ' . $codigo_interno . ')', $agente_id);

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
        <a href="listar.php" class="btn btn-secondary"><i class="fa-solid fa-arrow-left me-2"></i>Voltar</a>
    </div>

    <?php if (!empty($erros)) : ?>
        <div class="alert alert-danger"><strong>Foram encontrados os seguintes erros:</strong>
            <ul class="mb-0"><?php foreach ($erros as $erro) : ?><li><?= htmlspecialchars($erro) ?></li><?php endforeach; ?></ul>
        </div>
    <?php endif; ?>
    <?php if (!empty($erro_sistema)) : ?>
        <div class="alert alert-danger"><strong>Erro:</strong> <p><?= htmlspecialchars($erro_sistema) ?></p></div>
    <?php endif; ?>

    <form action="inserir.php" method="POST" enctype="multipart/form-data" novalidate class="shadow p-4 rounded bg-white" style="max-width: 900px; margin: auto;">

        <ul class="nav nav-tabs mb-4" id="equipTabs" role="tablist">
            <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#dados" type="button" role="tab">Dados do Equipamento</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#fornecedor" type="button" role="tab">Fornecedor</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#localizacao" type="button" role="tab">Localização</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#garantia" type="button" role="tab">Garantia / Contrato</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#docs" type="button" role="tab">Documentação</button></li>
        </ul>

        <div class="tab-content" id="equipTabsContent">

            <div class="tab-pane fade show active" id="dados" role="tabpanel">
                <div class="mb-3">
                    <label class="form-label fw-bold">Código Interno de Inventário</label>
                    <input type="text" name="codigo_interno" class="form-control" value="<?= htmlspecialchars($_POST['codigo_interno'] ?? '') ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Designação do Equipamento</label>
                    <input type="text" name="designacao" class="form-control" value="<?= htmlspecialchars($_POST['designacao'] ?? '') ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Categoria / Grupo</label>
                    <select name="id_categoria" class="form-select" required>
                        <option value="">Selecione a categoria</option>
                        <?php foreach ($categorias as $op) : ?>
                            <option value="<?= $op->id ?>" <?= (($_POST['id_categoria'] ?? '') == $op->id) ? 'selected' : '' ?>><?= htmlspecialchars($op->nome) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Marca</label>
                    <input type="text" name="marca" class="form-control" value="<?= htmlspecialchars($_POST['marca'] ?? '') ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Modelo</label>
                    <input type="text" name="modelo" class="form-control" value="<?= htmlspecialchars($_POST['modelo'] ?? '') ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Número de Série</label>
                    <input type="text" name="numero_serie" class="form-control" value="<?= htmlspecialchars($_POST['numero_serie'] ?? '') ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Fabricante</label>
                    <input type="text" name="fabricante" class="form-control" value="<?= htmlspecialchars($_POST['fabricante'] ?? '') ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Data de Aquisição</label>
                    <input type="text" id="data_aquisicao" name="data_aquisicao" class="form-control" value="<?= htmlspecialchars($_POST['data_aquisicao'] ?? '') ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Ano de Fabrico</label>
                    <input type="number" name="ano_fabrico" class="form-control" min="1900" max="2100" value="<?= htmlspecialchars($_POST['ano_fabrico'] ?? '') ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Custo de Aquisição (€)</label>
                    <input type="number" name="custo_aquisicao" class="form-control" min="0" value="<?= htmlspecialchars($_POST['custo_aquisicao'] ?? '') ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Tipo de Entrada</label>
                    <select name="id_tipo_entrada" class="form-select" required>
                        <option value="">Selecione o tipo</option>
                        <?php foreach ($tipos_entrada as $op) : ?>
                            <option value="<?= $op->id ?>" <?= (($_POST['id_tipo_entrada'] ?? '') == $op->id) ? 'selected' : '' ?>><?= htmlspecialchars($op->nome) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Estado Atual</label>
                    <select name="id_estado" class="form-select" required>
                        <option value="">Selecione o estado</option>
                        <?php foreach ($estados as $op) : ?>
                            <?php if ($op->nome === 'Inativo') continue; ?>
                            <option value="<?= $op->id ?>" <?= (($_POST['id_estado'] ?? '') == $op->id) ? 'selected' : '' ?>><?= htmlspecialchars($op->nome) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Criticidade</label>
                    <select name="id_criticidade" class="form-select" required>
                        <option value="">Selecione a criticidade</option>
                        <?php foreach ($criticidades as $op) : ?>
                            <option value="<?= $op->id ?>" <?= (($_POST['id_criticidade'] ?? '') == $op->id) ? 'selected' : '' ?>><?= htmlspecialchars($op->nome) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Observações</label>
                    <textarea name="observacoes" class="form-control" rows="4"><?= htmlspecialchars($_POST['observacoes'] ?? '') ?></textarea>
                </div>
            </div>

            <div class="tab-pane fade" id="fornecedor" role="tabpanel">
                <div class="p-3 border rounded bg-light">
                    <h5 class="fw-bold mb-3">Fornecedores associados</h5>
                    <p class="text-muted small">Um equipamento pode ter vários fornecedores associados. Apenas fornecedores ativos são apresentados.</p>
                    <?php if (empty($fornecedores)) : ?>
                        <p class="text-muted">Ainda não existem fornecedores ativos registados. <a href="../fornecedores/listar.php">Adicionar fornecedor</a>.</p>
                    <?php else : ?>
                        <?php foreach ($fornecedores as $forn) : ?>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="fornecedores[]" value="<?= $forn->id ?>" id="forn_<?= $forn->id ?>" <?= in_array($forn->id, $_POST['fornecedores'] ?? []) ? 'checked' : '' ?>>
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
                    <label class="form-label fw-bold">Localização <span class="text-danger">*</span></label>
                    <select class="form-select" name="localizacao" required>
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

            <div class="tab-pane fade" id="garantia" role="tabpanel">
                <div class="p-3 border rounded bg-light">
                    <h5 class="fw-bold mb-3">Garantia / Contrato associado</h5>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Data de início da garantia</label>
                        <input type="text" id="garantia_inicio" class="form-control" name="garantia_inicio" value="<?= htmlspecialchars($_POST['garantia_inicio'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Data de fim da garantia</label>
                        <input type="text" id="garantia_fim" class="form-control" name="garantia_fim" value="<?= htmlspecialchars($_POST['garantia_fim'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tipo de contrato</label>
                        <select class="form-select" name="id_garantia_tipo">
                            <option value="">Selecione...</option>
                            <?php foreach ($tipos_contrato as $op) : ?>
                                <option value="<?= $op->id ?>" <?= (($_POST['id_garantia_tipo'] ?? '') == $op->id) ? 'selected' : '' ?>><?= htmlspecialchars($op->nome) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Entidade responsável</label>
                        <input type="text" class="form-control" name="garantia_entidade" value="<?= htmlspecialchars($_POST['garantia_entidade'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Periodicidade</label>
                        <select class="form-select" name="id_garantia_periodicidade">
                            <option value="">Selecione...</option>
                            <?php foreach ($periodicidades as $op) : ?>
                                <option value="<?= $op->id ?>" <?= (($_POST['id_garantia_periodicidade'] ?? '') == $op->id) ? 'selected' : '' ?>><?= htmlspecialchars($op->nome) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Observações</label>
                        <textarea class="form-control" rows="4" name="garantia_observacoes"><?= htmlspecialchars($_POST['garantia_observacoes'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="docs" role="tabpanel">
                <div class="p-3 border rounded bg-light">
                    <h5 class="fw-bold mb-3">Documentação associada</h5>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tipo de documento</label>
                        <select class="form-select" name="doc_tipo">
                            <option value="">Selecione...</option>
                            <?php foreach ($tipos_doc as $op) : ?>
                                <option value="<?= $op->id ?>" <?= (($_POST['doc_tipo'] ?? '') == $op->id) ? 'selected' : '' ?>><?= htmlspecialchars($op->nome) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Descrição</label>
                        <input type="text" class="form-control" name="doc_descricao" value="<?= htmlspecialchars($_POST['doc_descricao'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Ficheiros</label>
                        <input type="file" class="form-control" name="documentos[]" multiple>
                    </div>
                </div>
            </div>

        </div>

        <button type="submit" class="btn btn-success w-100 mt-4">
            <i class="fa-solid fa-check me-2"></i>Guardar Equipamento
        </button>

    </form>

</main>

<script>
    flatpickr("#data_aquisicao", { dateFormat: "Y-m-d" });
    flatpickr("#garantia_inicio", { dateFormat: "Y-m-d" });
    flatpickr("#garantia_fim", { dateFormat: "Y-m-d" });
</script>
<?php include __DIR__ . '/../includes/footer.php'; ?>