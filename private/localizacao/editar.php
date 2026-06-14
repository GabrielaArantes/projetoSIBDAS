<?php
require_once __DIR__ . '/../../private/includes/funcoes.php';
redirect_if_not_logged();
start_session();
?>
<?php include __DIR__ . '/../includes/header.php'; ?>

<body class="pagprivada">

    <?php include __DIR__ . '/../includes/nav.php'; ?>

    <main class="conteudo">

        <h1 class="mb-4">Editar Localização</h1>

        <form class="shadow p-4 rounded" style="max-width: 850px;">

            <div class="row mb-3">
                <div class="col">
                    <label class="form-label">Edifício</label>
                    <input type="text" class="form-control" name="edificio" value="">
                </div>

                <div class="col">
                    <label class="form-label">Piso</label>
                    <input type="text" class="form-control" name="piso" value="">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Serviço / Departamento</label>
                <input type="text" class="form-control" name="servico" value="">
            </div>

            <div class="mb-3">
                <label class="form-label">Sala / Gabinete</label>
                <input type="text" class="form-control" name="sala" value="">
            </div>

            <div class="mb-3">
                <label class="form-label">Observações</label>
                <textarea class="form-control" rows="3" name="observacoes"></textarea>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <a href="listar.php" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-left"></i> Voltar
                </a>

                <button type="submit" class="btn btn-success">
                    <i class="fa-solid fa-check"></i> Guardar Alterações
                </button>
            </div>

        </form>

    </main>
    <?php include __DIR__ . '/../includes/footer.php'; ?>