<?php
require_once __DIR__ . '/../../config/config.php';
?>
<?php include __DIR__ . '/../includes/header.php'; ?>

<body class="pagprivada">

    <?php include __DIR__ . '/../includes/nav.php'; ?>

    <main class="conteudo">

        <h1 class="mb-4">Detalhes da Garantia e Contrato</h1>

        <div class="shadow p-4 rounded" style="max-width: 850px;">

            <div class="mb-3">
                <strong>ID:</strong>
            </div>

            <div class="mb-3">
                <strong>Tipo de contrato:</strong>
            </div>

            <div class="mb-3">
                <strong>Entidade responsável:</strong>
            </div>

            <div class="mb-3">
                <strong>Periodicidade:</strong>
            </div>

            <div class="mb-3">
                <strong>Data de início:</strong>
            </div>

            <div class="mb-3">
                <strong>Data de fim:</strong>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <a href="listar.html" class="btn btn-secondary">Voltar</a>
                <a href="editar.html" class="btn btn-warning">Editar</a>
            </div>

        </div>

    </main>
    <?php include __DIR__ . '/../includes/footer.php'; ?>
