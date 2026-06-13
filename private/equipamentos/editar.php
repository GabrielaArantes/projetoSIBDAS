<?php
require_once __DIR__ . '/../../config/config.php';
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

        <div class="shadow p-4 rounded bg-white" style="max-width: 900px; margin: auto;">

            <!-- NAV TABS -->
            <ul class="nav nav-tabs mb-4" id="equipTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#dados" role="tab">
                        Dados
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#fornecedor" role="tab">
                        Fornecedor
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#localizacao" role="tab">
                        Localização
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#garantia" role="tab">
                        Garantia / Contrato
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#docs" role="tab">
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

                        <label>Código Interno</label>
                        <input type="text" class="form-control mb-2" id="codigo_interno">

                        <label>Designação</label>
                        <input type="text" class="form-control mb-2" id="designacao">

                        <label>Categoria / Grupo</label>
                        <input type="text" class="form-control mb-2" id="categoria">

                        <label>Marca</label>
                        <input type="text" class="form-control mb-2" id="marca">

                        <label>Modelo</label>
                        <input type="text" class="form-control mb-2" id="modelo">

                        <label>Número de Série</label>
                        <input type="text" class="form-control mb-2" id="numero_serie">

                        <label>Fabricante</label>
                        <input type="text" class="form-control mb-2" id="fabricante">

                        <label>Data de Aquisição</label>
                        <input type="date" class="form-control mb-2" id="data_aquisicao">

                        <label>Ano de Fabrico</label>
                        <input type="number" class="form-control mb-2" id="ano_fabrico">

                        <label>Custo de Aquisição</label>
                        <input type="number" class="form-control mb-2" id="custo_aquisicao">

                        <label>Tipo de Entrada</label>
                        <input type="text" class="form-control mb-2" id="tipo_entrada">

                        <label>Estado Atual</label>
                        <input type="text" class="form-control mb-2" id="estado">

                        <label>Criticidade</label>
                        <input type="text" class="form-control mb-2" id="criticidade">

                        <label>Observações</label>
                        <textarea class="form-control mb-2" id="observacoes"></textarea>

                    </div>
                </div>

                <!-- FORNECEDOR -->
                <div class="tab-pane fade" id="fornecedor" role="tabpanel">
                    <div class="p-3 border rounded bg-light">

                        <h5 class="fw-bold mb-3">Fornecedor</h5>

                        <label>Nome</label>
                        <input type="text" class="form-control mb-2" id="fornecedor_nome">

                        <label>Email</label>
                        <input type="email" class="form-control mb-2" id="fornecedor_email">

                        <label>Telefone</label>
                        <input type="text" class="form-control mb-2" id="fornecedor_telefone">

                        <label>Morada</label>
                        <input type="text" class="form-control mb-2" id="fornecedor_morada">

                        <label>Observações</label>
                        <textarea class="form-control mb-2" id="fornecedor_observacoes"></textarea>

                    </div>
                </div>

                <!-- LOCALIZAÇÃO -->
                <div class="tab-pane fade" id="localizacao" role="tabpanel">
                    <div class="p-3 border rounded bg-light">

                        <h5 class="fw-bold mb-3">Localização</h5>

                        <label>Edifício</label>
                        <input type="text" class="form-control mb-2" id="local_edificio">

                        <label>Piso</label>
                        <input type="text" class="form-control mb-2" id="local_piso">

                        <label>Serviço / Departamento</label>
                        <input type="text" class="form-control mb-2" id="local_servico">

                        <label>Sala / Gabinete</label>
                        <input type="text" class="form-control mb-2" id="local_sala">

                        <label>Observações</label>
                        <textarea class="form-control mb-2" id="local_observacoes"></textarea>

                    </div>
                </div>

                <!-- GARANTIA -->
                <div class="tab-pane fade" id="garantia" role="tabpanel">
                    <div class="p-3 border rounded bg-light">

                        <h5 class="fw-bold mb-3">Garantia / Contrato</h5>

                        <label>Data de início</label>
                        <input type="date" class="form-control mb-2" id="garantia_inicio">

                        <label>Data de fim</label>
                        <input type="date" class="form-control mb-2" id="garantia_fim">

                        <label>Tipo de contrato</label>
                        <input type="text" class="form-control mb-2" id="garantia_tipo">

                        <label>Entidade responsável</label>
                        <input type="text" class="form-control mb-2" id="garantia_entidade">

                        <label>Periodicidade</label>
                        <input type="text" class="form-control mb-2" id="garantia_periodicidade">

                        <label>Observações</label>
                        <textarea class="form-control mb-2" id="garantia_observacoes"></textarea>

                    </div>
                </div>

                <!-- DOCUMENTAÇÃO -->
                <div class="tab-pane fade" id="docs" role="tabpanel">
                    <div class="p-3 border rounded bg-light">

                        <h5 class="fw-bold mb-3">Documentação</h5>

                        <label>Tipo</label>
                        <input type="text" class="form-control mb-2" id="doc_tipo">

                        <label>Descrição</label>
                        <input type="text" class="form-control mb-2" id="doc_descricao">

                        <label>Observações</label>
                        <textarea class="form-control mb-2" id="doc_observacoes"></textarea>

                        <label>Ficheiros</label>
                        <input type="file" class="form-control mb-2" multiple>

                    </div>
                </div>

            </div>

        </div>
        <div class="d-flex justify-content-end mt-4">
            <button class="btn btn-success px-4" id="guardarEquipamento">
                <i class="fa-solid fa-floppy-disk me-2"></i>Guardar Alterações
            </button>
        </div>

    </main>
    <?php include __DIR__ . '/../includes/footer.php'; ?>