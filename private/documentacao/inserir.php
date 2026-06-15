<?php
require_once __DIR__ . '/../includes/funcoes.php';
redirect_if_not_logged();
start_session();
?>
<?php include __DIR__ . '/../includes/header.php'; ?>

<body class="pagprivada">

    <?php include __DIR__ . '/../includes/nav.php'; ?>

    <main class="conteudo">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Inserir Documentação</h1>

            <a href="listar.php" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Voltar
            </a>
        </div>

        <div class="shadow p-4 rounded bg-white" style="max-width: 700px; margin: auto;">

            <form id="formDoc" enctype="multipart/form-data">

                <div class="mb-3">
                    <label class="form-label">Tipo de Documento *</label>
                    <input type="text" class="form-control" name="tipo" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nome do Documento *</label>
                    <input type="text" class="form-control" name="nome" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Data do Documento *</label>
                    <input type="date" class="form-control" name="data" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Data de Validade (opcional)</label>
                    <input type="date" class="form-control" name="validade">
                </div>

                <div class="mb-3">
                    <label class="form-label">Equipamento Associado *</label>
                    <select class="form-select" name="equipamento" required>
                        <option value="">Selecione...</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Ficheiro *</label>
                    <input type="file" class="form-control" name="ficheiro" accept=".pdf,.jpg,.png,.doc,.docx" required>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-success px-4">
                        <i class="fa-solid fa-floppy-disk me-2"></i>Guardar
                    </button>
                </div>

            </form>

        </div>

    </main>

    <?php include __DIR__ . '/../includes/footer.php'; ?>