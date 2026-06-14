<?php
require_once __DIR__ . '/../../private/includes/funcoes.php';
redirect_if_not_logged();
start_session();
?>
<?php include __DIR__ . '/../includes/header.php'; ?>

<body class="pagprivada">

    <?php include __DIR__ . '/../includes/nav.php'; ?>

    <main class="conteudo">

        <h1 class="mb-4">Editar Fornecedor</h1>

        <form class="shadow p-4 rounded" style="max-width: 800px;" method="POST" action="#">

            <div class="row mb-3">
                <div class="col">
                    <label class="form-label">Nome da Empresa <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="nome_empresa" value="" required>
                </div>

                <div class="col">
                    <label class="form-label">NIF <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" name="nif" value="" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col">
                    <label class="form-label">Telefone <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="telefone" value="" required>
                </div>

                <div class="col">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" name="email" value="" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Morada</label>
                <input type="text" class="form-control" name="morada" value="">
            </div>

            <div class="mb-3">
                <label class="form-label">Website</label>
                <input type="text" class="form-control" name="website" value="">
            </div>

            <hr>

            <div class="row mb-3">
                <div class="col">
                    <label class="form-label">Pessoa de Contacto</label>
                    <input type="text" class="form-control" name="pessoa_contacto" value="">
                </div>

                <div class="col">
                    <label class="form-label">Telefone da Pessoa de Contacto</label>
                    <input type="text" class="form-control" name="telefone_contacto" value="">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Tipo de Fornecedor <span class="text-danger">*</span></label>
                <select class="form-select" name="tipo_fornecedor" required>
                    <option value="">Selecione...</option>
                    <option>Fabricante</option>
                    <option>Distribuidor / Fornecedor Comercial</option>
                    <option>Assistência Técnica</option>
                    <option>Fornecedor de Consumíveis</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Observações</label>
                <textarea class="form-control" rows="4" name="observacoes"></textarea>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <a href="listar.php" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-left"></i> Voltar
                </a>

                <button type="submit" class="btn btn-warning">
                    <i class="fa-solid fa-pen"></i> Guardar Alterações
                </button>
            </div>

        </form>

    </main>

    <?php include __DIR__ . '/../includes/footer.php'; ?>