<?php
require_once __DIR__ . '/../../private/includes/funcoes.php';
redirect_if_not_logged();
start_session();
?>
<?php include __DIR__ . '/../includes/header.php'; ?>

<body class="pagprivada">

    <?php include __DIR__ . '/../includes/nav.php'; ?>

    <main class="conteudo">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Editar Documentação</h1>

            <a href="listar.php" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Voltar
            </a>
        </div>

        <div class="shadow p-4 rounded bg-white" style="max-width: 700px; margin: auto;">

            <form id="formEditarDoc" enctype="multipart/form-data">

                <input type="hidden" name="id" id="doc_id">

                <div class="mb-3">
                    <label class="form-label">Tipo de Documento *</label>
                    <input type="text" class="form-control" name="tipo" id="tipo" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nome do Documento *</label>
                    <input type="text" class="form-control" name="nome" id="nome" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Data do Documento *</label>
                    <input type="date" class="form-control" name="data" id="data" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Data de Validade (opcional)</label>
                    <input type="date" class="form-control" name="validade" id="validade">
                </div>

                <div class="mb-3">
                    <label class="form-label">Equipamento Associado *</label>
                    <select class="form-select" name="equipamento" id="equipamento" required>
                        <option value="">Selecione...</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Ficheiro Atual</label>
                    <p id="ficheiroAtual" class="text-muted"></p>
                </div>

                <div class="mb-3">
                    <label class="form-label">Substituir Ficheiro (opcional)</label>
                    <input type="file" class="form-control" name="ficheiro" accept=".pdf,.jpg,.png,.doc,.docx">
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-success px-4">
                        <i class="fa-solid fa-floppy-disk me-2"></i>Guardar Alterações
                    </button>
                </div>

            </form>

        </div>

    </main>

    <?php include __DIR__ . '/../includes/footer.php'; ?>