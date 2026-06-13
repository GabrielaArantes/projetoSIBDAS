<?php
require_once __DIR__ . '/../../config/config.php';
?>
<?php include __DIR__ . '/../includes/header.php'; ?>

<body class="pagprivada">

   <?php include __DIR__ . '/../includes/nav.php'; ?>

    <main class="conteudo">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Detalhes da Documentação</h1>

            <div class="d-flex gap-2">
                <a href="listar.php" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-left"></i> Voltar
                </a>

                <a href="editar.php" class="btn btn-warning">
                    <i class="fa-solid fa-pen"></i> Editar
                </a>
            </div>
        </div>

        <div class="shadow p-4 rounded bg-white" style="max-width: 700px; margin: auto;">

            <h5 class="fw-bold mb-3">Informação do Documento</h5>

            <p><strong>ID:</strong> <span id="doc_id"></span></p>
            <p><strong>Tipo:</strong> <span id="doc_tipo"></span></p>
            <p><strong>Nome:</strong> <span id="doc_nome"></span></p>
            <p><strong>Data do Documento:</strong> <span id="doc_data"></span></p>
            <p><strong>Data de Validade:</strong> <span id="doc_validade"></span></p>

            <p><strong>Equipamento Associado:</strong> <span id="doc_equipamento"></span></p>

            <p><strong>Ficheiro:</strong>
                <a id="doc_ficheiro" href="#" target="_blank" class="btn btn-outline-primary btn-sm ms-2">
                    <i class="fa-solid fa-file"></i> Abrir Ficheiro
                </a>
            </p>

        </div>

    </main>

    <?php include __DIR__ . '/../includes/footer.php'; ?>