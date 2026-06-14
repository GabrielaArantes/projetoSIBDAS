<?php
require_once __DIR__ . '/../../private/includes/funcoes.php';
redirect_if_not_logged();
start_session();
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

        <form method="POST" action="#" class="shadow p-4 rounded bg-white" style="max-width: 900px; margin: auto;">

            <!-- NAV TABS -->
            <ul class="nav nav-tabs mb-4" id="equipTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#dados" role="tab" type="button">
                        Dados
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#fornecedor" role="tab" type="button">
                        Fornecedor
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#localizacao" role="tab" type="button">
                        Localização
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#garantia" role="tab" type="button">
                        Garantia / Contrato
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#docs" role="tab" type="button">
                        Documentação
                    </button>
                </li>
            </ul>

            <!-- TAB CONTENT -->
            <div class="tab-content">

                <!-- DADOS -->
                <div class="tab-pane fade show active" id="dados" role="tabpanel">
                    <div class="p-3 border rounded bg-light">

                        <h5 class="fw-bold mb-3">Dados do Equipamento</h5>

                        <label>Código Interno <span class="text-danger">*</span></label>
                        <input type="text" class="form-control mb-2" name="codigo_interno" id="codigo_interno" required>

                        <label>Designação <span class="text-danger">*</span></label>
                        <input type="text" class="form-control mb-2" name="designacao" id="designacao" required>

                        <label>Categoria / Grupo <span class="text-danger">*</span></label>
                        <input type="text" class="form-control mb-2" name="categoria" id="categoria" required>

                        <label>Marca <span class="text-danger">*</span></label>
                        <input type="text" class="form-control mb-2" name="marca" id="marca" required>

                        <label>Modelo <span class="text-danger">*</span></label>
                        <input type="text" class="form-control mb-2" name="modelo" id="modelo" required>

                        <label>Número de Série <span class="text-danger">*</span></label>
                        <input type="text" class="form-control mb-2" name="numero_serie" id="numero_serie" required>

                        <label>Fabricante <span class="text-danger">*</span></label>
                        <input type="text" class="form-control mb-2" name="fabricante" id="fabricante" required>

                        <label>Data de Aquisição <span class="text-danger">*</span></label>
                        <input type="date" class="form-control mb-2" name="data_aquisicao" id="data_aquisicao" required>

                        <label>Ano de Fabrico <span class="text-danger">*</span></label>
                        <input type="number" class="form-control mb-2" name="ano_fabrico" id="ano_fabrico" min="1900" max="2100" required>

                        <label>Custo de Aquisição (€)</label>
                        <input type="number" class="form-control mb-2" name="custo_aquisicao" id="custo_aquisicao" min="0">

                        <label>Tipo de Entrada <span class="text-danger">*</span></label>
                        <input type="text" class="form-control mb-2" name="tipo_entrada" id="tipo_entrada" required>

                        <label>Estado Atual <span class="text-danger">*</span></label>
                        <input type="text" class="form-control mb-2" name="estado" id="estado" required>

                        <label>Criticidade <span class="text-danger">*</span></label>
                        <input type="text" class="form-control mb-2" name="criticidade" id="criticidade" required>

                        <label>Observações</label>
                        <textarea class="form-control mb-2" name="observacoes" id="observacoes"></textarea>

                    </div>
                </div>

                <!-- FORNECEDOR -->
                <div class="tab-pane fade" id="fornecedor" role="tabpanel">
                    <div class="p-3 border rounded bg-light">

                        <h5 class="fw-bold mb-3">Fornecedor</h5>

                        <label>Nome</label>
                        <input type="text" class="form-control mb-2" name="fornecedor_nome" id="fornecedor_nome">

                        <label>Email</label>
                        <input type="email" class="form-control mb-2" name="fornecedor_email" id="fornecedor_email">

                        <label>Telefone</label>
                        <input type="text" class="form-control mb-2" name="fornecedor_telefone" id="fornecedor_telefone">

                        <label>Morada</label>
                        <input type="text" class="form-control mb-2" name="fornecedor_morada" id="fornecedor_morada">

                        <label>Observações</label>
                        <textarea class="form-control mb-2" name="fornecedor_observacoes" id="fornecedor_observacoes"></textarea>

                    </div>
                </div>

                <!-- LOCALIZAÇÃO -->
                <div class="tab-pane fade" id="localizacao" role="tabpanel">
                    <div class="p-3 border rounded bg-light">

                        <h5 class="fw-bold mb-3">Localização</h5>

                        <label>Edifício</label>
                        <input type="text" class="form-control mb-2" name="local_edificio" id="local_edificio">

                        <label>Piso</label>
                        <input type="text" class="form-control mb-2" name="local_piso" id="local_piso">

                        <label>Serviço / Departamento</label>
                        <input type="text" class="form-control mb-2" name="local_servico" id="local_servico">

                        <label>Sala / Gabinete</label>
                        <input type="text" class="form-control mb-2" name="local_sala" id="local_sala">

                        <label>Observações</label>
                        <textarea class="form-control mb-2" name="local_observacoes" id="local_observacoes"></textarea>

                    </div>
                </div>

                <!-- GARANTIA -->
                <div class="tab-pane fade" id="garantia" role="tabpanel">
                    <div class="p-3 border rounded bg-light">

                        <h5 class="fw-bold mb-3">Garantia / Contrato</h5>

                        <label>Data de início</label>
                        <input type="date" class="form-control mb-2" name="garantia_inicio" id="garantia_inicio">

                        <label>Data de fim</label>
                        <input type="date" class="form-control mb-2" name="garantia_fim" id="garantia_fim">

                        <label>Tipo de contrato</label>
                        <input type="text" class="form-control mb-2" name="garantia_tipo" id="garantia_tipo">

                        <label>Entidade responsável</label>
                        <input type="text" class="form-control mb-2" name="garantia_entidade" id="garantia_entidade">

                        <label>Periodicidade</label>
                        <input type="text" class="form-control mb-2" name="garantia_periodicidade" id="garantia_periodicidade">

                        <label>Observações</label>
                        <textarea class="form-control mb-2" name="garantia_observacoes" id="garantia_observacoes"></textarea>

                    </div>
                </div>

                <!-- DOCUMENTAÇÃO -->
                <div class="tab-pane fade" id="docs" role="tabpanel">
                    <div class="p-3 border rounded bg-light">

                        <h5 class="fw-bold mb-3">Documentação</h5>

                        <label>Tipo</label>
                        <input type="text" class="form-control mb-2" name="doc_tipo" id="doc_tipo">

                        <label>Descrição</label>
                        <input type="text" class="form-control mb-2" name="doc_descricao" id="doc_descricao">

                        <label>Observações</label>
                        <textarea class="form-control mb-2" name="doc_observacoes" id="doc_observacoes"></textarea>

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
