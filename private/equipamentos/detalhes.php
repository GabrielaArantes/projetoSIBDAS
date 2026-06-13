<?php
require_once __DIR__ . '/../../config/config.php';
?>
<?php include __DIR__ . '/../includes/header.php'; ?>

<body class="pagprivada">

    <?php include __DIR__ . '/../includes/nav.php'; ?>

    <main class="conteudo p-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Detalhes do Equipamento</h1>
            <a href="listar.php" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left me-2"></i>Voltar
            </a>
        </div>

        <div class="shadow p-4 rounded bg-white" style="max-width: 900px; margin: auto;">

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

            <div class="tab-content">

                <div class="tab-pane fade show active" id="dados" role="tabpanel">
                    <div class="p-3 border rounded bg-light">

                        <h5 class="fw-bold mb-3">Dados do Equipamento</h5>

                        <p><strong>Código Interno:</strong> <span id="codigo_interno"></span></p>
                        <p><strong>Designação:</strong> <span id="designacao"></span></p>
                        <p><strong>Categoria / Grupo:</strong> <span id="categoria"></span></p>
                        <p><strong>Marca:</strong> <span id="marca"></span></p>
                        <p><strong>Modelo:</strong> <span id="modelo"></span></p>
                        <p><strong>Número de Série:</strong> <span id="numero_serie"></span></p>
                        <p><strong>Fabricante:</strong> <span id="fabricante"></span></p>
                        <p><strong>Data de Aquisição:</strong> <span id="data_aquisicao"></span></p>
                        <p><strong>Ano de Fabrico:</strong> <span id="ano_fabrico"></span></p>
                        <p><strong>Custo de Aquisição:</strong> <span id="custo_aquisicao"></span></p>
                        <p><strong>Tipo de Entrada:</strong> <span id="tipo_entrada"></span></p>
                        <p><strong>Estado Atual:</strong> <span id="estado"></span></p>
                        <p><strong>Criticidade:</strong> <span id="criticidade"></span></p>
                        <p><strong>Observações:</strong> <span id="observacoes"></span></p>

                    </div>
                </div>

                <div class="tab-pane fade" id="fornecedor" role="tabpanel">
                    <div class="p-3 border rounded bg-light">

                        <h5 class="fw-bold mb-3">Fornecedor</h5>

                        <p><strong>Nome:</strong> <span id="fornecedor_nome"></span></p>
                        <p><strong>Email:</strong> <span id="fornecedor_email"></span></p>
                        <p><strong>Telefone:</strong> <span id="fornecedor_telefone"></span></p>
                        <p><strong>Morada:</strong> <span id="fornecedor_morada"></span></p>
                        <p><strong>Observações:</strong> <span id="fornecedor_observacoes"></span></p>

                    </div>
                </div>

                <div class="tab-pane fade" id="localizacao" role="tabpanel">
                    <div class="p-3 border rounded bg-light">

                        <h5 class="fw-bold mb-3">Localização</h5>

                        <p><strong>Edifício:</strong> <span id="local_edificio"></span></p>
                        <p><strong>Piso:</strong> <span id="local_piso"></span></p>
                        <p><strong>Serviço / Departamento:</strong> <span id="local_servico"></span></p>
                        <p><strong>Sala / Gabinete:</strong> <span id="local_sala"></span></p>
                        <p><strong>Observações:</strong> <span id="local_observacoes"></span></p>

                    </div>
                </div>

                <div class="tab-pane fade" id="garantia" role="tabpanel">
                    <div class="p-3 border rounded bg-light">

                        <h5 class="fw-bold mb-3">Garantia / Contrato</h5>

                        <p><strong>Data de início:</strong> <span id="garantia_inicio"></span></p>
                        <p><strong>Data de fim:</strong> <span id="garantia_fim"></span></p>
                        <p><strong>Tipo de contrato:</strong> <span id="garantia_tipo"></span></p>
                        <p><strong>Entidade responsável:</strong> <span id="garantia_entidade"></span></p>
                        <p><strong>Periodicidade:</strong> <span id="garantia_periodicidade"></span></p>
                        <p><strong>Observações:</strong> <span id="garantia_observacoes"></span></p>

                    </div>
                </div>

                <div class="tab-pane fade" id="docs" role="tabpanel">
                    <div class="p-3 border rounded bg-light">

                        <h5 class="fw-bold mb-3">Documentação</h5>

                        <p><strong>Tipo:</strong> <span id="doc_tipo"></span></p>
                        <p><strong>Descrição:</strong> <span id="doc_descricao"></span></p>
                        <p><strong>Observações:</strong> <span id="doc_observacoes"></span></p>

                        <p><strong>Ficheiros:</strong></p>
                        <ul id="doc_ficheiros"></ul>

                    </div>
                </div>

            </div>

        </div>

    </main>
    <?php include __DIR__ . '/../includes/footer.php'; ?>