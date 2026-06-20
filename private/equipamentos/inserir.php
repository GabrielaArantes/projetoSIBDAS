<?php
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_logged();
start_session();

$fornecedores = [];
try {
    $ligacaoForn = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8",
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );
    $ligacaoForn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $fornecedores = $ligacaoForn->query("SELECT id, nome, tipo FROM fornecedor ORDER BY nome")->fetchAll(PDO::FETCH_OBJ);
    $ligacaoForn = null;
} catch (PDOException $err) {
}

// Carregar lista de localizações já existentes, para o select do formulário
$localizacoes = [];
try {
    $ligacaoLoc = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8",
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );
    $ligacaoLoc->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $localizacoes = $ligacaoLoc->query("SELECT id, edificio, piso, servico, sala FROM localizacao ORDER BY edificio, piso, servico, sala")->fetchAll(PDO::FETCH_OBJ);
    $ligacaoLoc = null;
} catch (PDOException $err) {
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $codigo_interno    = $_POST["codigo_interno"]          ?? "";
    $designacao        = $_POST["designacao"]              ?? "";
    $categoria         = $_POST["categoria"]              ?? "";
    $marca             = $_POST["marca"]                  ?? "";
    $modelo            = $_POST["modelo"]                 ?? "";
    $numero_serie      = $_POST["numero_serie"]           ?? "";
    $fabricante        = $_POST["fabricante"]             ?? "";
    $data_aquisicao    = $_POST["data_aquisicao"]         ?? "";
    $ano_fabrico       = $_POST["ano_fabrico"]            ?? "";
    $custo_aquisicao   = $_POST["custo_aquisicao"]        ?? "";
    $tipo_entrada      = $_POST["tipo_entrada"]           ?? "";
    $estado            = $_POST["estado"]                 ?? "";
    $criticidade       = $_POST["criticidade"]            ?? "";
    $observacoes       = $_POST["observacoes"]            ?? "";
    $id_localizacao_escolhida = $_POST["localizacao"]     ?? "";
    $fornecedores_escolhidos = $_POST["fornecedores"] ?? [];
    $garantia_inicio   = $_POST["garantia_inicio"]        ?? "";
    $garantia_fim      = $_POST["garantia_fim"]           ?? "";
    $garantia_tipo     = $_POST["garantia_tipo"]          ?? "";
    $garantia_entidade = $_POST["garantia_entidade"]      ?? "";
    $garantia_period   = $_POST["garantia_periodicidade"] ?? "";
    $garantia_obs      = $_POST["garantia_observacoes"]   ?? "";

    $erros = [];
    $erro_sistema = "";

    $codigo_interno  = trim($codigo_interno);
    $designacao      = trim($designacao);
    $marca           = trim($marca);
    $modelo          = trim($modelo);
    $numero_serie    = trim($numero_serie);
    $fabricante      = trim($fabricante);
    $data_aquisicao  = trim($data_aquisicao);
    $ano_fabrico     = trim($ano_fabrico);
    $custo_aquisicao = trim($custo_aquisicao);

    if (empty($codigo_interno))
        $erros[] = "O Código Interno é obrigatório.";

    if (empty($designacao))
        $erros[] = "A Designação do equipamento é obrigatória.";
    elseif (preg_match('/\d/', $designacao))
        $erros[] = "A Designação não pode conter números.";

    if (empty($categoria))    $erros[] = "A Categoria é obrigatória.";
    if (empty($marca))        $erros[] = "A Marca é obrigatória.";
    if (empty($modelo))       $erros[] = "O Modelo é obrigatório.";
    if (empty($numero_serie)) $erros[] = "O Número de Série é obrigatório.";
    if (empty($fabricante))   $erros[] = "O Fabricante é obrigatório.";

    if (empty($data_aquisicao)) {
        $erros[] = "A Data de Aquisição é obrigatória.";
    } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data_aquisicao)) {
        $erros[] = "Formato de Data de Aquisição inválido. Use AAAA-MM-DD.";
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

    if (empty($tipo_entrada)) $erros[] = "O Tipo de Entrada é obrigatório.";
    if (empty($estado))       $erros[] = "O Estado atual é obrigatório.";
    if (empty($criticidade))  $erros[] = "A Criticidade é obrigatória.";

    if (empty($id_localizacao_escolhida)) $erros[] = "A Localização é obrigatória.";


    if (!empty($garantia_inicio) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $garantia_inicio))
        $erros[] = "Formato de data de início de garantia inválido. Use AAAA-MM-DD.";

    if (!empty($garantia_fim) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $garantia_fim))
        $erros[] = "Formato de data de fim de garantia inválido. Use AAAA-MM-DD.";

    if (empty($erros)) {
        $codigo_interno    = strtoupper($codigo_interno);
        $designacao        = ucwords(strtolower($designacao));
        $marca             = ucwords(strtolower($marca));
        $fabricante        = ucwords(strtolower($fabricante));
        $garantia_entidade = ucwords(strtolower($garantia_entidade));
    }

    // 4. Guardar na base de dados
    if (empty($erros)) {
        try {
            $ligacao = new PDO(
                "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8",
                MYSQL_USERNAME,
                MYSQL_PASSWORD
            );
            $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Usar a localização escolhida no select (já existente na tabela localizacao)
            $id_localizacao = !empty($id_localizacao_escolhida) ? (int)$id_localizacao_escolhida : null;

            // Inserir equipamento
            $sql = "INSERT INTO equipamento (codigo_interno, nome, categoria, marca, modelo, num_serie, fabricante, data_aquisicao, ano_fabrico, custo, tipo_entrada, estado, criticidade, observacoes, id_localizacao)
                    VALUES (:codigo_interno, :nome, :categoria, :marca, :modelo, :num_serie, :fabricante, :data_aquisicao, :ano_fabrico, :custo, :tipo_entrada, :estado, :criticidade, :observacoes, :id_localizacao)";
            $stmt = $ligacao->prepare($sql);
            $stmt->execute([
                ':codigo_interno' => $codigo_interno,
                ':nome'           => $designacao,
                ':categoria'      => $categoria,
                ':marca'          => $marca,
                ':modelo'         => $modelo,
                ':num_serie'      => $numero_serie,
                ':fabricante'     => $fabricante,
                ':data_aquisicao' => $data_aquisicao,
                ':ano_fabrico'    => $ano_fabrico,
                ':custo'          => $custo_aquisicao,
                ':tipo_entrada'   => $tipo_entrada,
                ':estado'         => $estado,
                ':criticidade'    => $criticidade,
                ':observacoes'    => $observacoes,
                ':id_localizacao' => $id_localizacao
            ]);
            $id_equipamento = $ligacao->lastInsertId();

            // Associar os fornecedores escolhidos (já existentes) ao equipamento
            if (!empty($fornecedores_escolhidos)) {
                $sql = "INSERT INTO equipamento_fornecedor (id_equipamento, id_fornecedor) VALUES (:id_eq, :id_forn)";
                $stmt = $ligacao->prepare($sql);
                foreach ($fornecedores_escolhidos as $idForn) {
                    $stmt->execute([':id_eq' => $id_equipamento, ':id_forn' => (int)$idForn]);
                }
            }

            // Inserir garantia (só se datas preenchidas)
            if (!empty($garantia_inicio) || !empty($garantia_fim)) {
                $sql = "INSERT INTO garantia_contrato (id_equipamento, data_inicio, data_fim, tipo_contrato, entidade_responsavel, periodicidade, observacoes)
                        VALUES (:id_eq, :inicio, :fim, :tipo, :entidade, :periodicidade, :obs)";
                $stmt = $ligacao->prepare($sql);
                $stmt->execute([
                    ':id_eq'         => $id_equipamento,
                    ':inicio'        => $garantia_inicio ?: null,
                    ':fim'           => $garantia_fim    ?: null,
                    ':tipo'          => $garantia_tipo,
                    ':entidade'      => $garantia_entidade,
                    ':periodicidade' => $garantia_period,
                    ':obs'           => $garantia_obs
                ]);
            }

            $ligacao = null;
            header("Location: listar.php");
            exit;
        } catch (PDOException $err) {
            $erro_sistema = "Erro ao gravar os dados: " . $err->getMessage();
        }
        $ligacao = null;
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
                <strong>Erro:</strong>
                <p><?= htmlspecialchars($erro_sistema) ?></p>
            </div>
        <?php endif; ?>

        <form action="inserir.php" method="POST" enctype="multipart/form-data" novalidate class="shadow p-4 rounded bg-white"
            style="max-width: 900px; margin: auto;">

            <ul class="nav nav-tabs mb-4" id="equipTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="dados-tab" data-bs-toggle="tab" data-bs-target="#dados"
                        type="button" role="tab">Dados do Equipamento</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="fornecedor-tab" data-bs-toggle="tab" data-bs-target="#fornecedor"
                        type="button" role="tab">Fornecedor</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="localizacao-tab" data-bs-toggle="tab" data-bs-target="#localizacao"
                        type="button" role="tab">Localização</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="garantia-tab" data-bs-toggle="tab" data-bs-target="#garantia"
                        type="button" role="tab">Garantia / Contrato</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="docs-tab" data-bs-toggle="tab" data-bs-target="#docs"
                        type="button" role="tab">Documentação</button>
                </li>
            </ul>

            <div class="tab-content" id="equipTabsContent">

                <!-- Tab: Dados do Equipamento -->
                <div class="tab-pane fade show active" id="dados" role="tabpanel">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Código Interno de Inventário</label>
                        <input type="text" name="codigo_interno" class="form-control"
                            value="<?= htmlspecialchars($_POST['codigo_interno'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Designação do Equipamento</label>
                        <input type="text" name="designacao" class="form-control"
                            value="<?= htmlspecialchars($_POST['designacao'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Categoria / Grupo</label>
                        <select name="categoria" class="form-select" required>
                            <option value="">Selecione a categoria</option>
                            <?php foreach (['Monitorização', 'Suporte de vida', 'Terapia', 'Diagnóstico', 'Laboratório', 'Esterilização', 'Reabilitação'] as $op) : ?>
                                <option value="<?= $op ?>" <?= (($_POST['categoria'] ?? '') == $op) ? 'selected' : '' ?>><?= $op ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Marca</label>
                        <input type="text" name="marca" class="form-control"
                            value="<?= htmlspecialchars($_POST['marca'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Modelo</label>
                        <input type="text" name="modelo" class="form-control"
                            value="<?= htmlspecialchars($_POST['modelo'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Número de Série</label>
                        <input type="text" name="numero_serie" class="form-control"
                            value="<?= htmlspecialchars($_POST['numero_serie'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Fabricante</label>
                        <input type="text" name="fabricante" class="form-control"
                            value="<?= htmlspecialchars($_POST['fabricante'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Data de Aquisição</label>
                        <input type="text" id="data_aquisicao" name="data_aquisicao" class="form-control"
                            value="<?= htmlspecialchars($_POST['data_aquisicao'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Ano de Fabrico</label>
                        <input type="number" name="ano_fabrico" class="form-control" min="1900" max="2100"
                            value="<?= htmlspecialchars($_POST['ano_fabrico'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Custo de Aquisição (€)</label>
                        <input type="number" name="custo_aquisicao" class="form-control" min="0"
                            value="<?= htmlspecialchars($_POST['custo_aquisicao'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tipo de Entrada</label>
                        <select name="tipo_entrada" class="form-select" required>
                            <option value="">Selecione o tipo</option>
                            <?php foreach (['Compra', 'Doação', 'Aluguer', 'Empréstimo'] as $op) : ?>
                                <option value="<?= $op ?>" <?= (($_POST['tipo_entrada'] ?? '') == $op) ? 'selected' : '' ?>><?= $op ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Estado Atual</label>
                        <select name="estado" class="form-select" required>
                            <option value="">Selecione o estado</option>
                            <?php foreach (['Ativo', 'Em manutenção', 'Inativo', 'Em calibração', 'Em quarentena', 'Abatido'] as $op) : ?>
                                <option value="<?= $op ?>" <?= (($_POST['estado'] ?? '') == $op) ? 'selected' : '' ?>><?= $op ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Criticidade</label>
                        <select name="criticidade" class="form-select" required>
                            <option value="">Selecione a criticidade</option>
                            <?php foreach (['Baixa', 'Média', 'Alta', 'Suporte de vida'] as $op) : ?>
                                <option value="<?= $op ?>" <?= (($_POST['criticidade'] ?? '') == $op) ? 'selected' : '' ?>><?= $op ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Observações</label>
                        <textarea name="observacoes" class="form-control" rows="4"><?= htmlspecialchars($_POST['observacoes'] ?? '') ?></textarea>
                    </div>
                </div>

                <!-- Tab: Fornecedor -->
                <div class="tab-pane fade" id="fornecedor" role="tabpanel">
                    <div class="p-3 border rounded bg-light">
                        <h5 class="fw-bold mb-3">Fornecedores associados</h5>
                        <p class="text-muted small">Um equipamento pode ter vários fornecedores associados (ex: fabricante, distribuidor, assistência técnica).</p>

                        <?php if (empty($fornecedores)) : ?>
                            <p class="text-muted">Ainda não existem fornecedores registados. <a href="../fornecedores/listar.php">Adicionar fornecedor</a>.</p>
                        <?php else : ?>
                            <?php foreach ($fornecedores as $forn) : ?>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="fornecedores[]" value="<?= $forn->id ?>"
                                        id="forn_<?= $forn->id ?>"
                                        <?= in_array($forn->id, $_POST['fornecedores'] ?? []) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="forn_<?= $forn->id ?>">
                                        <?= htmlspecialchars($forn->nome) ?>
                                        <span class="text-muted">(<?= htmlspecialchars($forn->tipo ?? 'Tipo não definido') ?>)</span>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                            <p class="text-muted small mt-2">
                                Caso queira outro fornecedor, pode adicioná-lo em <a href="../fornecedores/listar.php">Fornecedores</a>.
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
                <!-- Tab: Localização -->
                <div class="tab-pane fade" id="localizacao" role="tabpanel">
                    <div class="p-3 border rounded bg-light">
                        <h5 class="fw-bold mb-3">Localização do equipamento</h5>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Localização <span class="text-danger">*</span></label>
                            <select class="form-select" name="localizacao" required>
                                <option value="">Selecione uma localização...</option>
                                <?php foreach ($localizacoes as $loc) : ?>
                                    <option value="<?= $loc->id ?>" <?= (($_POST['localizacao'] ?? '') == $loc->id) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($loc->edificio) ?> - <?= htmlspecialchars($loc->piso) ?> - <?= htmlspecialchars($loc->servico) ?> - <?= htmlspecialchars($loc->sala) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">
                                Não encontra a localização pretendida? Crie-a primeiro em
                                <a href="../localizacao/inserir.php">Localização → Adicionar</a>.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab: Garantia -->
                <div class="tab-pane fade" id="garantia" role="tabpanel">
                    <div class="p-3 border rounded bg-light">
                        <h5 class="fw-bold mb-3">Garantia / Contrato associado</h5>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Data de início da garantia</label>
                            <input type="text" id="garantia_inicio" class="form-control" name="garantia_inicio"
                                value="<?= htmlspecialchars($_POST['garantia_inicio'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Data de fim da garantia</label>
                            <input type="text" id="garantia_fim" class="form-control" name="garantia_fim"
                                value="<?= htmlspecialchars($_POST['garantia_fim'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tipo de contrato</label>
                            <select class="form-select" name="garantia_tipo">
                                <option value="">Selecione...</option>
                                <?php foreach (['Garantia', 'Contrato de Manutenção', 'Assistência Técnica'] as $op) : ?>
                                    <option value="<?= $op ?>" <?= (($_POST['garantia_tipo'] ?? '') == $op) ? 'selected' : '' ?>><?= $op ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Entidade responsável</label>
                            <input type="text" class="form-control" name="garantia_entidade"
                                value="<?= htmlspecialchars($_POST['garantia_entidade'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Periodicidade</label>
                            <select class="form-select" name="garantia_periodicidade">
                                <option value="">Selecione...</option>
                                <?php foreach (['Mensal', 'Trimestral', 'Semestral', 'Anual'] as $op) : ?>
                                    <option value="<?= $op ?>" <?= (($_POST['garantia_periodicidade'] ?? '') == $op) ? 'selected' : '' ?>><?= $op ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Observações</label>
                            <textarea class="form-control" rows="4" name="garantia_observacoes"><?= htmlspecialchars($_POST['garantia_observacoes'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Tab: Documentação -->
                <div class="tab-pane fade" id="docs" role="tabpanel">
                    <div class="p-3 border rounded bg-light">
                        <h5 class="fw-bold mb-3">Documentação associada</h5>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tipo de documento</label>
                            <select class="form-select" name="doc_tipo">
                                <option value="">Selecione...</option>
                                <?php foreach (['Manual', 'Certificado', 'Relatório Técnico', 'Outro'] as $op) : ?>
                                    <option value="<?= $op ?>" <?= (($_POST['doc_tipo'] ?? '') == $op) ? 'selected' : '' ?>><?= $op ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Descrição</label>
                            <input type="text" class="form-control" name="doc_descricao"
                                value="<?= htmlspecialchars($_POST['doc_descricao'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Ficheiros</label>
                            <input type="file" class="form-control" name="documentos[]" multiple>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Observações</label>
                            <textarea class="form-control" rows="3" name="doc_observacoes"><?= htmlspecialchars($_POST['doc_observacoes'] ?? '') ?></textarea>
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
        flatpickr("#data_aquisicao", {
            dateFormat: "Y-m-d"
        });
        flatpickr("#garantia_inicio", {
            dateFormat: "Y-m-d"
        });
        flatpickr("#garantia_fim", {
            dateFormat: "Y-m-d"
        });
    </script>
    <?php include __DIR__ . '/../includes/footer.php'; ?>