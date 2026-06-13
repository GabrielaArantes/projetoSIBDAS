<?php
require_once __DIR__ . '/../../config/config.php';
?>
<?php include __DIR__ . '/../includes/header.php'; ?>

<body class="pagprivada">

    <?php include __DIR__ . '/../includes/nav.php'; ?>



    <main class="conteudo">

        <h1 class="mb-4">Detalhes da Localização</h1>

        <div class="shadow p-4 rounded" style="max-width: 850px;">

            <h4 class="mb-3">
                <i class="fa-solid fa-location-dot me-2"></i>
                Localização
            </h4>

            <div class="mb-3">
                <strong>ID:</strong>
            </div>

            <div class="mb-3">
                <strong>Edifício:</strong>
            </div>

            <div class="mb-3">
                <strong>Piso:</strong>
            </div>

            <div class="mb-3">
                <strong>Serviço / Departamento:</strong>
            </div>

            <div class="mb-3">
                <strong>Sala / Gabinete:</strong>
            </div>

            <div class="mb-3">
                <strong>Observações:</strong><br>
                <span class="text-muted"></span>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <a href="listar.html" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-left"></i> Voltar
                </a>

                <a href="editar.html" class="btn btn-warning">
                    <i class="fa-solid fa-pen"></i> Editar
                </a>
            </div>

        </div>

    </main>
    <?php include __DIR__ . '/../includes/footer.php'; ?>