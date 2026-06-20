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
$equipamento = null;
$localizacoes = [];
$fornecedores = [];
$fornecedoresAssociados = [];

$idEncrypted = $_GET['id'] ?? null;
$id = aes_decrypt($idEncrypted);

if (!$id || !is_numeric($id)) {
    header("Location: listar.php");
    exit;
}

$id = (int)$id;

// Carregar lista de localizações existentes (necessária tanto para mostrar o form como após o POST)
try {
    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8",
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $localizacoes = $ligacao->query("SELECT id, edificio, piso, servico, sala FROM localizacao ORDER BY edificio, piso, servico, sala")->fetchAll(PDO::FETCH_OBJ);
    $ligacao = null;
} catch (PDOException $err) {
    $erro = "Erro ao carregar lista de localizações.";
}

// Carregar lista de fornecedores existentes
try {
    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8",
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $fornecedores = $ligacao->query("SELECT id, nome, tipo FROM fornecedor ORDER BY nome")->fetchAll(PDO::FETCH_OBJ);
    $ligacao = null;
} catch (PDOException $err) {
    $erro = "Erro ao carregar lista de fornecedores.";
}

// Carregar fornecedores já associados a este equipamento
try {
    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8",
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $ligacao->prepare("SELECT id_fornecedor FROM equipamento_fornecedor WHERE id_equipamento = ?");
    $stmt->execute([$id]);
    $fornecedoresAssociados = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $ligacao = null;
} catch (PDOException $err) {
    $erro = "Erro ao carregar fornecedores associados.";
}

// 1. Tratar primeiro a submissão do formulário (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $erros = array_merge(
        validar_designacao($_POST['designacao'] ?? ''),
        validar_marca($_POST['marca'] ?? ''),
        validar_modelo($_POST['modelo'] ?? ''),
        validar_numero_serie($_POST['numero_serie'] ?? ''),
        validar_fabricante($_POST['fabricante'] ?? ''),
        validar_select_obrigatorio($_POST['categoria'] ?? '', 'Categoria / Grupo'),
        validar_select_obrigatorio($_POST['tipo_entrada'] ?? '', 'Tipo de Entrada'),
        validar_select_obrigatorio($_POST['estado'] ?? '', 'Estado Atual'),
        validar_select_obrigatorio($_POST['criticidade'] ?? '', 'Criticidade'),
        validar_select_obrigatorio($_POST['localizacao'] ?? '', 'Localização')
    );

    if (empty($erros)) {
        try {
            $ligacao = new PDO(
                "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8",
                MYSQL_USERNAME,
                MYSQL_PASSWORD
            );
            $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $custoAquisicao = ($_POST['custo_aquisicao'] === '') ? null : $_POST['custo_aquisicao'];
            $idLocalizacaoEscolhida = (int)$_POST['localizacao'];
            $fornecedoresEscolhidos = $_POST['fornecedores'] ?? [];

            $stmt = $ligacao->prepare("UPDATE equipamento SET codigo_interno=?, nome=?, categoria=?, marca=?, modelo=?, num_serie=?, fabricante=?, data_aquisicao=?, ano_fabrico=?, custo=?, tipo_entrada=?, estado=?, criticidade=?, observacoes=?, id_localizacao=? WHERE id=?");
            $stmt->execute([
                $_POST['codigo_interno'],
                $_POST['designacao'],
                $_POST['categoria'],
                $_POST['marca'],
                $_POST['modelo'],
                $_POST['numero_serie'],
                $_POST['fabricante'],
                $_POST['data_aquisicao'],
                $_POST['ano_fabrico'],
                $custoAquisicao,
                $_POST['tipo_entrada'],
                $_POST['estado'],
                $_POST['criticidade'],
                $_POST['observacoes'],
                $idLocalizacaoEscolhida,
                $id
            ]);

            // Atualizar associações de fornecedores: apaga todas e recria com as escolhidas agora
            $stmt = $ligacao->prepare("DELETE FROM equipamento_fornecedor WHERE id_equipamento = ?");
            $stmt->execute([$id]);

            if (!empty($fornecedoresEscolhidos)) {
                $stmt = $ligacao->prepare("INSERT INTO equipamento_fornecedor (id_equipamento, id_fornecedor) VALUES (?, ?)");
                foreach ($fornecedoresEscolhidos as $idForn) {
                    $stmt->execute([$id, (int)$idForn]);
                }
            }

            $ligacao = null;
            $sucesso = "Equipamento atualizado com sucesso!";

            // Recarregar fornecedores associados, para refletir a alteração no formulário
            $fornecedoresAssociados = $fornecedoresEscolhidos;

        } catch (PDOException $err) {
            $erro = "Erro ao atualizar: " . $err->getMessage();
        }
    }
}

// 2. Obter os dados atuais do equipamento (GET, ou para mostrar o formulário após o POST)
try {
    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8",
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $ligacao->prepare("SELECT e.*, l.edificio, l.piso, l.servico, l.sala FROM equipamento e LEFT JOIN localizacao l ON e.id_localizacao = l.id WHERE e.id = ?");
    $stmt->execute([$id]);
    $equipamento = $stmt->fetch(PDO::FETCH_OBJ);

    if (!$equipamento) {
        header("Location: listar.php");
        exit;
    }

    $ligacao = null;
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
            <a href="listar.php" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left me-2"></i>Voltar
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

        <form method="POST" action="editar.php?id=<?= $idEncrypted ?>" novalidate autocomplete="off" class="shadow p-4 rounded bg-white" style="max-width: 900px; margin: auto;">

            <ul class="nav nav-tabs mb-4" id="equipTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#dados" role="tab" type="button">Dados</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#fornecedor" role="tab" type="button">Fornecedor</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#localizacao" role="tab" type="button">Localização</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#garantia" role="tab" type="button">Garantia / Contrato</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#docs" role="tab" type="button">Documentação</button>
                </li>
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
                        <select name="categoria" class="form-select mb-2" required>
                            <option value="">Selecione a categoria</option>
                            <?php foreach (['Monitorização', 'Suporte de vida', 'Terapia', 'Diagnóstico', 'Laboratório', 'Esterilização', 'Reabilitação'] as $cat) : ?>
                                <option value="<?= $cat ?>" <?= ($equipamento->categoria ?? '') === $cat ? 'selected' : '' ?>><?= $cat ?></option>
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
                        <select name="tipo_entrada" class="form-select mb-2" required>
                            <option value="">Selecione o tipo</option>
                            <?php foreach (['Compra', 'Doação', 'Aluguer', 'Empréstimo'] as $tipo) : ?>
                                <option value="<?= $tipo ?>" <?= ($equipamento->tipo_entrada ?? '') === $tipo ? 'selected' : '' ?>><?= $tipo ?></option>
                            <?php endforeach; ?>
                        </select>

                        <label>Estado Atual <span class="text-danger">*</span></label>
                        <select name="estado" class="form-select mb-2" required>
                            <option value="">Selecione o estado</option>
                            <?php foreach (['Ativo', 'Em manutenção', 'Inativo', 'Em calibração', 'Em quarentena', 'Abatido'] as $est) : ?>
                                <option value="<?= $est ?>" <?= ($equipamento->estado ?? '') === $est ? 'selected' : '' ?>><?= $est ?></option>
                            <?php endforeach; ?>
                        </select>

                        <label>Criticidade <span class="text-danger">*</span></label>
                        <select name="criticidade" class="form-select mb-2" required>
                            <option value="">Selecione a criticidade</option>
                            <?php foreach (['Baixa', 'Média', 'Alta', 'Suporte de vida'] as $crit) : ?>
                                <option value="<?= $crit ?>" <?= ($equipamento->criticidade ?? '') === $crit ? 'selected' : '' ?>><?= $crit ?></option>
                            <?php endforeach; ?>
                        </select>

                        <label>Observações</label>
                        <textarea class="form-control mb-2" name="observacoes"><?= htmlspecialchars($equipamento->observacoes ?? '') ?></textarea>
                    </div>
                </div>

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
                                        <?= in_array($forn->id, $fornecedoresAssociados) ? 'checked' : '' ?>>
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
                        <div class="form-text">
                            Não encontra a localização pretendida? Crie-a primeiro em
                            <a href="../localizacao/inserir.php">Localização → Adicionar</a>.
                        </div>
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
                        <select class="form-select mb-2" name="garantia_tipo">
                            <option value="">Selecione...</option>
                            <option>Garantia</option>
                            <option>Contrato de Manutenção</option>
                            <option>Assistência Técnica</option>
                        </select>
                        <label>Entidade responsável</label>
                        <input type="text" class="form-control mb-2" name="garantia_entidade">
                        <label>Periodicidade</label>
                        <select class="form-select mb-2" name="garantia_periodicidade">
                            <option value="">Selecione...</option>
                            <option>Mensal</option>
                            <option>Trimestral</option>
                            <option>Semestral</option>
                            <option>Anual</option>
                        </select>
                        <label>Observações</label>
                        <textarea class="form-control mb-2" name="garantia_observacoes"></textarea>
                    </div>
                </div>

                <div class="tab-pane fade" id="docs" role="tabpanel">
                    <div class="p-3 border rounded bg-light">
                        <h5 class="fw-bold mb-3">Documentação</h5>
                        <label>Tipo</label>
                        <input type="text" class="form-control mb-2" name="doc_tipo">
                        <label>Descrição</label>
                        <input type="text" class="form-control mb-2" name="doc_descricao">
                        <label>Observações</label>
                        <textarea class="form-control mb-2" name="doc_observacoes"></textarea>
                        <label>Ficheiros</label>
                        <input type="file" class="form-control mb-2" name="documentos[]" multiple>
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