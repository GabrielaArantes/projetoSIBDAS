<?php
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_logged();
start_session();
?>

<?php
$sucesso = '';
$erro = '';
$equipamento = null;

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id === 0) {
    header("Location: listar.php");
    exit;
}

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $ligacao = new PDO(
            "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8",
            MYSQL_USERNAME,
            MYSQL_PASSWORD
        );
        $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Atualizar localização
        $stmt = $ligacao->prepare("UPDATE localizacao SET edificio=?, piso=?, servico=?, sala=? WHERE id=?");
        $stmt->execute([
            $_POST['local_edificio'],
            $_POST['local_piso'],
            $_POST['local_servico'],
            $_POST['local_sala'],
            $equipamento->id_localizacao
        ]);

        // Atualizar equipamento
        $stmt = $ligacao->prepare("UPDATE equipamento SET codigo_interno=?, nome=?, categoria=?, marca=?, modelo=?, num_serie=?, fabricante=?, data_aquisicao=?, ano_fabrico=?, custo=?, tipo_entrada=?, estado=?, criticidade=?, observacoes=? WHERE id=?");
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
            $_POST['custo_aquisicao'],
            $_POST['tipo_entrada'],
            $_POST['estado'],
            $_POST['criticidade'],
            $_POST['observacoes'],
            $id
        ]);

        $ligacao = null;
        $sucesso = "Equipamento atualizado com sucesso!";

        // Recarregar dados atualizados
        $ligacao = new PDO(
            "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8",
            MYSQL_USERNAME,
            MYSQL_PASSWORD
        );
        $stmt = $ligacao->prepare("SELECT e.*, l.edificio, l.piso, l.servico, l.sala FROM equipamento e LEFT JOIN localizacao l ON e.id_localizacao = l.id WHERE e.id = ?");
        $stmt->execute([$id]);
        $equipamento = $stmt->fetch(PDO::FETCH_OBJ);
        $ligacao = null;

    } catch (PDOException $err) {
        $erro = "Erro ao atualizar: " . $err->getMessage();
    }
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

        <form method="POST" action="editar.php?id=<?= $id ?>" class="shadow p-4 rounded bg-white" style="max-width: 900px; margin: auto;">

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
                        <input type="text" class="form-control mb-2" name="codigo_interno" value="<?= $equipamento->codigo_interno ?? '' ?>" required>

                        <label>Designação <span class="text-danger">*</span></label>
                        <input type="text" class="form-control mb-2" name="designacao" value="<?= $equipamento->nome ?? '' ?>" required>

                        <label>Categoria / Grupo <span class="text-danger">*</span></label>
                        <select name="categoria" class="form-select mb-2" required>
                            <option value="">Selecione a categoria</option>
                            <?php foreach (['Monitorização', 'Suporte de vida', 'Terapia', 'Diagnóstico', 'Laboratório', 'Esterilização', 'Reabilitação'] as $cat) : ?>
                                <option value="<?= $cat ?>" <?= ($equipamento->categoria ?? '') === $cat ? 'selected' : '' ?>><?= $cat ?></option>
                            <?php endforeach; ?>
                        </select>

                        <label>Marca <span class="text-danger">*</span></label>
                        <input type="text" class="form-control mb-2" name="marca" value="<?= $equipamento->marca ?? '' ?>" required>

                        <label>Modelo <span class="text-danger">*</span></label>
                        <input type="text" class="form-control mb-2" name="modelo" value="<?= $equipamento->modelo ?? '' ?>" required>

                        <label>Número de Série <span class="text-danger">*</span></label>
                        <input type="text" class="form-control mb-2" name="numero_serie" value="<?= $equipamento->num_serie ?? '' ?>" required>

                        <label>Fabricante <span class="text-danger">*</span></label>
                        <input type="text" class="form-control mb-2" name="fabricante" value="<?= $equipamento->fabricante ?? '' ?>" required>

                        <label>Data de Aquisição <span class="text-danger">*</span></label>
                        <input type="date" class="form-control mb-2" name="data_aquisicao" value="<?= $equipamento->data_aquisicao ?? '' ?>" required>

                        <label>Ano de Fabrico <span class="text-danger">*</span></label>
                        <input type="number" class="form-control mb-2" name="ano_fabrico" value="<?= $equipamento->ano_fabrico ?? '' ?>" min="1900" max="2100" required>

                        <label>Custo de Aquisição (€)</label>
                        <input type="number" class="form-control mb-2" name="custo_aquisicao" value="<?= $equipamento->custo ?? '' ?>" min="0">

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
                        <textarea class="form-control mb-2" name="observacoes"><?= $equipamento->observacoes ?? '' ?></textarea>
                    </div>
                </div>

                <div class="tab-pane fade" id="fornecedor" role="tabpanel">
                    <div class="p-3 border rounded bg-light">
                        <h5 class="fw-bold mb-3">Fornecedor</h5>
                        <label>Nome</label>
                        <input type="text" class="form-control mb-2" name="fornecedor_nome">
                        <label>Email</label>
                        <input type="email" class="form-control mb-2" name="fornecedor_email">
                        <label>Telefone</label>
                        <input type="text" class="form-control mb-2" name="fornecedor_telefone">
                        <label>Morada</label>
                        <input type="text" class="form-control mb-2" name="fornecedor_morada">
                        <label>Observações</label>
                        <textarea class="form-control mb-2" name="fornecedor_observacoes"></textarea>
                    </div>
                </div>

                <div class="tab-pane fade" id="localizacao" role="tabpanel">
                    <div class="p-3 border rounded bg-light">
                        <h5 class="fw-bold mb-3">Localização</h5>
                        <label>Edifício</label>
                        <input type="text" class="form-control mb-2" name="local_edificio" value="<?= $equipamento->edificio ?? '' ?>">
                        <label>Piso</label>
                        <input type="text" class="form-control mb-2" name="local_piso" value="<?= $equipamento->piso ?? '' ?>">
                        <label>Serviço / Departamento</label>
                        <input type="text" class="form-control mb-2" name="local_servico" value="<?= $equipamento->servico ?? '' ?>">
                        <label>Sala / Gabinete</label>
                        <input type="text" class="form-control mb-2" name="local_sala" value="<?= $equipamento->sala ?? '' ?>">
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