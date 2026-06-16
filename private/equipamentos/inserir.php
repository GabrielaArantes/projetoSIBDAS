<?php
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_logged();
start_session();
?>

<?php
$sucesso = '';
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $ligacao = new PDO(
            "mysql:host=" . MYSQL_HOST . ";port=" . MYSQL_PORT . ";dbname=" . MYSQL_DATABASE . ";charset=utf8",
            MYSQL_USERNAME,
            MYSQL_PASSWORD
        );
        $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Inserir localização
        $stmt = $ligacao->prepare("INSERT INTO localizacao (edificio, piso, servico, sala) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $_POST['local_edificio'],
            $_POST['local_piso'],
            $_POST['local_servico'],
            $_POST['local_sala']
        ]);
        $id_localizacao = $ligacao->lastInsertId();

        // Inserir equipamento
        $stmt = $ligacao->prepare("INSERT INTO equipamento (codigo_interno, nome, categoria, marca, modelo, num_serie, fabricante, data_aquisicao, ano_fabrico, custo, tipo_entrada, estado, criticidade, observacoes, id_localizacao) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
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
            $id_localizacao
        ]);
        $id_equipamento = $ligacao->lastInsertId();

        // Inserir fornecedor se nome preenchido
        if (!empty($_POST['fornecedor_nome'])) {
            $stmt = $ligacao->prepare("INSERT INTO fornecedor (nome, email, telefone, morada) VALUES (?, ?, ?, ?)");
            $stmt->execute([
                $_POST['fornecedor_nome'],
                $_POST['fornecedor_email'],
                $_POST['fornecedor_telefone'],
                $_POST['fornecedor_morada']
            ]);
            $id_fornecedor = $ligacao->lastInsertId();

            $stmt = $ligacao->prepare("INSERT INTO equipamento_fornecedor (id_equipamento, id_fornecedor) VALUES (?, ?)");
            $stmt->execute([$id_equipamento, $id_fornecedor]);
        }

        // Inserir garantia se preenchida
        if (!empty($_POST['garantia_inicio']) || !empty($_POST['garantia_fim'])) {
            $stmt = $ligacao->prepare("INSERT INTO garantia_contrato (id_equipamento, data_inicio, data_fim, tipo_contrato, entidade_responsavel, periodicidade, observacoes) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $id_equipamento,
                $_POST['garantia_inicio'] ?: null,
                $_POST['garantia_fim'] ?: null,
                $_POST['garantia_tipo'],
                $_POST['garantia_entidade'],
                $_POST['garantia_periodicidade'],
                $_POST['garantia_observacoes']
            ]);
        }

        $ligacao = null;
        $sucesso = "Equipamento inserido com sucesso!";

    } catch (PDOException $err) {
        $erro = "Erro ao inserir: " . $err->getMessage();
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

        <?php if (!empty($sucesso)) : ?>
            <div class="alert alert-success"><?= $sucesso ?></div>
        <?php endif; ?>
        <?php if (!empty($erro)) : ?>
            <div class="alert alert-danger"><?= $erro ?></div>
        <?php endif; ?>

        <form action="inserir.php" method="POST" enctype="multipart/form-data" class="shadow p-4 rounded bg-white"
            style="max-width: 900px; margin: auto;">

            <ul class="nav nav-tabs mb-4" id="equipTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="dados-tab" data-bs-toggle="tab" data-bs-target="#dados"
                        type="button" role="tab">
                        Dados do Equipamento
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="fornecedor-tab" data-bs-toggle="tab" data-bs-target="#fornecedor"
                        type="button" role="tab">
                        Fornecedor
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="localizacao-tab" data-bs-toggle="tab" data-bs-target="#localizacao"
                        type="button" role="tab">
                        Localização
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="garantia-tab" data-bs-toggle="tab" data-bs-target="#garantia"
                        type="button" role="tab">
                        Garantia / Contrato
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="docs-tab" data-bs-toggle="tab" data-bs-target="#docs" type="button"
                        role="tab">
                        Documentação
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="equipTabsContent">

                <div class="tab-pane fade show active" id="dados" role="tabpanel">

                    <div class="mb-3">
                        <label class="form-label fw-bold">Código Interno de Inventário</label>
                        <input type="text" name="codigo_interno" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Designação do Equipamento</label>
                        <input type="text" name="designacao" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Categoria / Grupo</label>
                        <select name="categoria" class="form-select" required>
                            <option value="">Selecione a categoria</option>
                            <option value="Monitorização">Monitorização</option>
                            <option value="Suporte de vida">Suporte de vida</option>
                            <option value="Terapia">Terapia</option>
                            <option value="Diagnóstico">Diagnóstico</option>
                            <option value="Laboratório">Laboratório</option>
                            <option value="Esterilização">Esterilização</option>
                            <option value="Reabilitação">Reabilitação</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Marca</label>
                        <input type="text" name="marca" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Modelo</label>
                        <input type="text" name="modelo" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Número de Série</label>
                        <input type="text" name="numero_serie" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Fabricante</label>
                        <input type="text" name="fabricante" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Data de Aquisição</label>
                        <input type="date" name="data_aquisicao" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Ano de Fabrico</label>
                        <input type="number" name="ano_fabrico" class="form-control" min="1900" max="2100" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Custo de Aquisição (€)</label>
                        <input type="number" name="custo_aquisicao" class="form-control" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tipo de Entrada</label>
                        <select name="tipo_entrada" class="form-select" required>
                            <option value="">Selecione o tipo</option>
                            <option value="Compra">Compra</option>
                            <option value="Doação">Doação</option>
                            <option value="Aluguer">Aluguer</option>
                            <option value="Empréstimo">Empréstimo</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Estado Atual</label>
                        <select name="estado" class="form-select" required>
                            <option value="">Selecione o estado</option>
                            <option value="Ativo">Ativo</option>
                            <option value="Em manutenção">Em manutenção</option>
                            <option value="Inativo">Inativo</option>
                            <option value="Em calibração">Em calibração</option>
                            <option value="Em quarentena">Em quarentena</option>
                            <option value="Abatido">Abatido</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Criticidade</label>
                        <select name="criticidade" class="form-select" required>
                            <option value="">Selecione a criticidade</option>
                            <option value="Baixa">Baixa</option>
                            <option value="Média">Média</option>
                            <option value="Alta">Alta</option>
                            <option value="Suporte de vida">Suporte de vida</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Observações</label>
                        <textarea name="observacoes" class="form-control" rows="4"></textarea>
                    </div>

                </div>

                <div class="tab-pane fade" id="fornecedor" role="tabpanel">
                    <div class="p-3 border rounded bg-light">
                        <h5 class="fw-bold mb-3">Fornecedor associado</h5>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nome do fornecedor</label>
                            <input type="text" class="form-control" name="fornecedor_nome">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Email</label>
                            <input type="email" class="form-control" name="fornecedor_email">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Telefone</label>
                            <input type="tel" class="form-control" name="fornecedor_telefone" pattern="[0-9]{9}" maxlength="9" placeholder="Ex: 912345678">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Morada</label>
                            <input type="text" class="form-control" name="fornecedor_morada">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Observações</label>
                            <textarea class="form-control" rows="3" name="fornecedor_observacoes"></textarea>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="localizacao" role="tabpanel">
                    <div class="p-3 border rounded bg-light">
                        <h5 class="fw-bold mb-3">Localização do equipamento</h5>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Edifício</label>
                            <input type="text" class="form-control" name="local_edificio">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Piso</label>
                            <input type="text" class="form-control" name="local_piso">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Serviço / Departamento</label>
                            <input type="text" class="form-control" name="local_servico">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Sala / Gabinete</label>
                            <input type="text" class="form-control" name="local_sala">
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="garantia" role="tabpanel">
                    <div class="p-3 border rounded bg-light">
                        <h5 class="fw-bold mb-3">Garantia / Contrato associado</h5>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Data de início da garantia</label>
                            <input type="date" class="form-control" name="garantia_inicio">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Data de fim da garantia</label>
                            <input type="date" class="form-control" name="garantia_fim">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tipo de contrato</label>
                            <select class="form-select" name="garantia_tipo">
                                <option value="">Selecione...</option>
                                <option>Garantia</option>
                                <option>Contrato de Manutenção</option>
                                <option>Assistência Técnica</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Entidade responsável</label>
                            <input type="text" class="form-control" name="garantia_entidade">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Periodicidade</label>
                            <select class="form-select" name="garantia_periodicidade">
                                <option value="">Selecione...</option>
                                <option>Mensal</option>
                                <option>Trimestral</option>
                                <option>Semestral</option>
                                <option>Anual</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Observações</label>
                            <textarea class="form-control" rows="4" name="garantia_observacoes"></textarea>
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
                                <option>Manual</option>
                                <option>Certificado</option>
                                <option>Relatório Técnico</option>
                                <option>Outro</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Descrição</label>
                            <input type="text" class="form-control" name="doc_descricao">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Ficheiros</label>
                            <input type="file" class="form-control" name="documentos[]" multiple>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Observações</label>
                            <textarea class="form-control" rows="3" name="doc_observacoes"></textarea>
                        </div>
                    </div>
                </div>

            </div>

            <button type="submit" class="btn btn-success w-100 mt-4">
                <i class="fa-solid fa-check me-2"></i>Guardar Equipamento
            </button>

        </form>

    </main>
    <?php include __DIR__ . '/../includes/footer.php'; ?>